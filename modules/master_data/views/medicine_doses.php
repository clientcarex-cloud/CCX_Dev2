<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" onclick="new_medicine_dose(); return false;"
                                class="btn btn-info pull-left display-block">
                                <?php echo _l('new_medicine_dose'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('id'),
                            _l('name'),
                            _l('options'),
                        ], 'medicine-doses'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="medicine_dose_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('master_data/save_medicine_dose')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('add_new', _l('medicine_dose')); ?>
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
        initDataTable('.table-medicine-doses', window.location.href, [2], [2]);
        appValidateForm($('form'), {
            name: 'required'
        }, manage_medicine_doses);

        $('#medicine_dose_modal').on('hidden.bs.modal', function (event) {
            $('#medicine_dose_modal input[name="id"]').val('');
            $('#medicine_dose_modal input[name="name"]').val('');
            $('#medicine_dose_modal .modal-title').html("<?php echo _l('add_new', _l('medicine_dose')); ?>");
            $('#medicine_dose_modal button[type="submit"]').html("<?php echo _l('submit'); ?>");
        });
    });

    function manage_medicine_doses(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('.table-medicine-doses').DataTable().ajax.reload();
                $('#medicine_dose_modal').modal('hide');
            } else {
                alert_float('warning', response.message);
            }
        });
        return false;
    }

    function new_medicine_dose() {
        $('#medicine_dose_modal').modal('show');
    }

    function edit_medicine_dose(invoker, id) {
        var name = $(invoker).data('name');
        $('#medicine_dose_modal input[name="id"]').val(id);
        $('#medicine_dose_modal input[name="name"]').val(name);
        $('#medicine_dose_modal .modal-title').html("<?php echo _l('edit', _l('medicine_dose')); ?>");
        $('#medicine_dose_modal button[type="submit"]').html("<?php echo _l('update'); ?>");
        $('#medicine_dose_modal').modal('show');
    }
</script>
</body>

</html>