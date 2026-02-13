<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" onclick="new_medicine_when(); return false;"
                                class="btn btn-info pull-left display-block">
                                <?php echo _l('new_medicine_when'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('id'),
                            _l('name'),
                            _l('options'),
                        ], 'medicine-whens'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="medicine_when_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('master_data/save_medicine_when')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('add_new', _l('medicine_when')); ?>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id">
                <?php echo render_input('name', 'name'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-info">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-medicine-whens', window.location.href, [2], [2]);
        appValidateForm($('form'), {
            name: 'required'
        }, manage_medicine_whens);

        $('#medicine_when_modal').on('hidden.bs.modal', function (event) {
            $('#medicine_when_modal input[name="id"]').val('');
            $('#medicine_when_modal input[name="name"]').val('');
            $('#medicine_when_modal .modal-title').html("<?php echo _l('add_new', _l('medicine_when')); ?>");
            $('#medicine_when_modal button[type="submit"]').html("<?php echo _l('submit'); ?>");
        });
    });

    function manage_medicine_whens(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('.table-medicine-whens').DataTable().ajax.reload();
                $('#medicine_when_modal').modal('hide');
            } else {
                alert_float('warning', response.message);
            }
        });
        return false;
    }

    function new_medicine_when() {
        $('#medicine_when_modal').modal('show');
    }

    function edit_medicine_when(invoker, id) {
        var name = $(invoker).data('name');
        $('#medicine_when_modal input[name="id"]').val(id);
        $('#medicine_when_modal input[name="name"]').val(name);
        $('#medicine_when_modal .modal-title').html("<?php echo _l('edit', _l('medicine_when')); ?>");
        $('#medicine_when_modal button[type="submit"]').html("<?php echo _l('update'); ?>");
        $('#medicine_when_modal').modal('show');
    }
</script>
</body>

</html>