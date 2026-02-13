<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('items', '', 'create')) { ?>
                                <a href="#" class="btn btn-info pull-left"
                                    onclick="new_test(); return false;"><?php echo _l('new_test'); ?></a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('test_name'),
                            _l('code'),
                            _l('lab_department'),
                            _l('price'),
                            'B2B Price',
                            'Price Change',
                            'Auth Req',
                            'Report Mode',
                            _l('status'),
                            _l('Templates'),
                            _l('options'),
                        ], 'tests-master'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('tests_master/test_modal'); ?>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-tests-master', admin_url + 'tests_master/table', undefined, undefined, 'undefined', [0, 'asc']);
        appValidateForm($('#test_form'), {
            description: 'required',
            rate: 'required',
            department: 'required',
            department_id: 'required'
        }, manage_test);

        $('#rate, #b2b_price').on('keyup change', function () {
            var rate = parseFloat($('#rate').val());
            var b2b_price = parseFloat($('#b2b_price').val());

            // Remove existing error
            $('#b2b_price_error').remove();

            if (!isNaN(rate) && !isNaN(b2b_price) && b2b_price >= rate) {
                $('#b2b_price').after('<div id="b2b_price_error" class="text-danger">B2B Price must be lower than Price</div>');
                $('button[type="submit"]').prop('disabled', true);
            } else {
                $('button[type="submit"]').prop('disabled', false);
            }
        });

        // Real-time code validation
        var codeCheckTimeout;
        $('input[name="code"]').on('keyup blur', function () {
            var code = $(this).val();
            var id = $('input[name="id"]').val();
            var $submitBtn = $('button[type="submit"]');
            var $input = $(this);

            // Clear existing timeout
            if (codeCheckTimeout) {
                clearTimeout(codeCheckTimeout);
            }

            // Create message container if not exists
            if ($input.next('#code_message').length == 0) {
                $input.after('<div id="code_message"></div>');
            }
            var $message = $input.next('#code_message');
            $message.html('').removeClass('text-danger text-success');

            if (code.trim() === '') {
                return;
            }

            // Debounce
            codeCheckTimeout = setTimeout(function () {
                $.post(admin_url + 'tests_master/check_code', { code: code, id: id }, function (response) {
                    response = JSON.parse(response);
                    // Ensure we are still referring to the correct element and it hasn't been removed/changed in the meantime
                    if (response.exists) {
                        $message.html(response.message).addClass('text-danger').removeClass('text-success');
                        $submitBtn.prop('disabled', true);
                    } else {
                        $message.html(response.message).addClass('text-success').removeClass('text-danger');
                        // Only enable if other validation passes
                        if ($('#b2b_price_error').length === 0) {
                            $submitBtn.prop('disabled', false);
                        }
                    }
                });
            }, 500); // 500ms delay
        });
    });

    function manage_test(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('#test_modal').modal('hide');
                $('.table-tests-master').DataTable().ajax.reload();
            }
        });
        return false;
    }

    function new_test() {
        $('#test_modal').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#test_form')[0].reset();
        $('#b2b_price_error').remove();
        $('#code_message').remove(); // Clear message
        $('button[type="submit"]').prop('disabled', false);
        $('#test_form').find('input[name="id"]').remove();
        $('#is_blood_sample_required').prop('checked', false);
        $('#is_price_changable').prop('checked', false);
        $('#is_authorization_required').prop('checked', false);
        $('#is_active').prop('checked', true);

        // Set default group
        var testsGroupId = '<?php echo $tests_group_id; ?>';
        if (testsGroupId) {
            $('#test_modal').find('select[name="department"]').selectpicker('val', testsGroupId);
        } else {
            $('#test_modal').find('select[name="department"]').selectpicker('val', '');
        }
        $('#test_modal').find('select[name="department"]').prop('disabled', true);
        $('#test_modal').find('select[name="department_id"]').selectpicker('val', '');
        $('#test_modal').find('select[name="test_method_id"]').selectpicker('val', '');
        $('#test_modal').find('select').selectpicker('refresh');
    }

    function edit_test(invoker, id) {
        var name = $(invoker).data('name');
        var code = $(invoker).data('code');
        var group_id = $(invoker).data('group_id');
        var department_id = $(invoker).data('department_id');
        var rate = $(invoker).data('rate');
        var b2b_price = $(invoker).data('b2b_price');
        var description = $(invoker).data('description');
        var is_blood_sample_required = $(invoker).data('is_blood_sample_required');
        var test_method_id = $(invoker).data('test_method_id');
        var is_active = $(invoker).data('is_active');

        $('#test_modal').find('input[name="description"]').val(name);
        $('#test_modal').find('input[name="code"]').val(code);
        $('#test_modal').find('input[name="rate"]').val(rate);
        $('#test_modal').find('input[name="b2b_price"]').val(b2b_price);
        $('#test_modal').find('textarea[name="long_description"]').val(description);
        $('#test_modal').find('select[name="department"]').selectpicker('val', group_id);
        $('#test_modal').find('select[name="department"]').prop('disabled', true);
        $('#test_modal').find('select[name="department_id"]').selectpicker('val', department_id);
        $('#test_modal').find('select[name="test_method_id"]').selectpicker('val', test_method_id);
        $('#test_modal').find('select').selectpicker('refresh');

        if (is_blood_sample_required == 1) {
            $('#is_blood_sample_required').prop('checked', true);
        } else {
            $('#is_blood_sample_required').prop('checked', false);
        }

        if ($(invoker).data('is_price_changable') == 1) {
            $('#is_price_changable').prop('checked', true);
        } else {
            $('#is_price_changable').prop('checked', false);
        }

        if ($(invoker).data('is_authorization_required') == 1) {
            $('#is_authorization_required').prop('checked', true);
        } else {
            $('#is_authorization_required').prop('checked', false);
        }

        if (is_active == 1) {
            $('#is_active').prop('checked', true);
        } else {
            $('#is_active').prop('checked', false);
        }

        $('#test_modal').modal('show');
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
        $('#test_form').append('<input type="hidden" name="id" value="' + id + '">');
        $('#code_message').remove();
        $('button[type="submit"]').prop('disabled', false);
    }
</script>
</body>

</html>