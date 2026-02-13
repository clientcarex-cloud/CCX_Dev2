<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" class="btn btn-info pull-left" onclick="new_status(); return false;">
                                <?php echo _l('new_status'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('id'),
                            _l('name'),
                            _l('options') // Ensure options column header usually generic
                        ], 'item-statuses'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="item_status_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('master_data/save_item_status')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('add_new', _l('status')); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="id">
                        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                        <?php echo render_input('name', 'name'); ?>
                    </div>
                </div>
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
        initDataTable('.table-item-statuses', window.location.href, [2], [2], undefined, [0, 'asc']);
        appValidateForm($('form'), {
            name: 'required'
        }, manage_item_statuses);
    });

    function manage_item_statuses(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);
                $('.table-item-statuses').DataTable().ajax.reload();
                $('#item_status_modal').modal('hide');
            }
        });
        return false;
    }

    function new_status() {
        $('#item_status_modal').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#item_status_modal input[name="name"]').val('');
        $('#item_status_modal input[name="id"]').val('');
    }

    function edit_status(invoker, id) {
        var name = $(invoker).data('name');
        $('#item_status_modal input[name="name"]').val(name);
        $('#item_status_modal input[name="id"]').val(id);
        $('#item_status_modal').modal('show');
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
    }
</script>
</body>

</html>