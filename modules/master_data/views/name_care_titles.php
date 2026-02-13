<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" onclick="new_name_care_title(); return false;"
                                class="btn btn-info pull-left display-block">
                                <?php echo _l('new_name_care_title'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('id'),
                            _l('name'),
                            _l('options'),
                        ], 'name-care-titles'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="name_care_title_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('master_data/save_name_care_title')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('add_new', _l('name_care_title')); ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id">
                <?php echo render_input('name', 'name'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-name-care-titles', window.location.href, [2], [2]);
        appValidateForm($('form'), {
            name: 'required'
        }, manage_name_care_titles);

        $('#name_care_title_modal').on('hidden.bs.modal', function (event) {
            $('#name_care_title_modal input[name="id"]').val('');
            $('#name_care_title_modal input[name="name"]').val('');
            $('#name_care_title_modal .modal-title').html("<?php echo _l('add_new', _l('name_care_title')); ?>");
            $('#name_care_title_modal button[type="submit"]').html("<?php echo _l('submit'); ?>");
        });
    });

    function manage_name_care_titles(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('.table-name-care-titles').DataTable().ajax.reload();
                $('#name_care_title_modal').modal('hide');
            } else {
                alert_float('warning', response.message);
            }
        });
        return false;
    }

    function new_name_care_title() {
        $('#name_care_title_modal').modal('show');
    }

    function edit_name_care_title(invoker, id) {
        var name = $(invoker).data('name');
        $('#name_care_title_modal input[name="id"]').val(id);
        $('#name_care_title_modal input[name="name"]').val(name);
        $('#name_care_title_modal .modal-title').html("<?php echo _l('edit', _l('name_care_title')); ?>");
        $('#name_care_title_modal button[type="submit"]').html("<?php echo _l('update'); ?>");
        $('#name_care_title_modal').modal('show');
    }
</script>
</body>

</html>