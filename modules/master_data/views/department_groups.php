<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" onclick="new_department_group(); return false;"
                                class="btn btn-info pull-left display-block">
                                <?php echo _l('new_department_group', 'New Department Group'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('id'),
                            _l('name'),
                            _l('departments'),
                            _l('options'),
                        ], 'department-groups'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="department_group_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('master_data/save_department_group')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('add_new', 'Department Group'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id">
                <?php echo render_input('name', 'name'); ?>
                <?php echo render_select('departments[]', $departments, ['departmentid', 'name'], 'departments', [], ['multiple' => true]); ?>
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
        initDataTable('.table-department-groups', window.location.href, [3], [3]);
        appValidateForm($('form'), {
            name: 'required'
        }, manage_department_groups);

        $('#department_group_modal').on('hidden.bs.modal', function (event) {
            $('#department_group_modal input[name="id"]').val('');
            $('#department_group_modal input[name="name"]').val('');
            $('#department_group_modal select[name="departments[]"]').selectpicker('val', []);
            $('#department_group_modal .modal-title').html("<?php echo _l('add_new', 'Department Group'); ?>");
            $('#department_group_modal button[type="submit"]').html("<?php echo _l('submit'); ?>");
        });
    });

    function manage_department_groups(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('.table-department-groups').DataTable().ajax.reload();
                $('#department_group_modal').modal('hide');
            } else {
                alert_float('warning', response.message);
            }
        });
        return false;
    }

    function new_department_group() {
        $('#department_group_modal').modal('show');
    }

    function edit_department_group(invoker, id) {
        var name = $(invoker).data('name');
        var departments = $(invoker).data('departments');

        $('#department_group_modal input[name="id"]').val(id);
        $('#department_group_modal input[name="name"]').val(name);

        if (departments) {
            $('#department_group_modal select[name="departments[]"]').selectpicker('val', departments);
        }

        $('#department_group_modal .modal-title').html("<?php echo _l('edit', 'Department Group'); ?>");
        $('#department_group_modal button[type="submit"]').html("<?php echo _l('update'); ?>");
        $('#department_group_modal').modal('show');
    }
</script>
</body>

</html>