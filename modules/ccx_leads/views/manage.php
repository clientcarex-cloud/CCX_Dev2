<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('ccx_leads', '', 'create')) { ?>
                                <a href="#" onclick="new_ccx_lead(); return false;"
                                    class="btn btn-info pull-left display-block"><?php echo _l('new_ccx_lead'); ?></a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('id'),
                            _l('name'),
                            _l('phonenumber'),
                            _l('email'),
                            _l('status'),
                            _l('assigned'),
                            _l('dateadded'),
                            _l('options'),
                        ], 'ccx-leads'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ccx_lead_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('new_ccx_lead'); ?></h4>
            </div>
            <?php echo form_open(admin_url('ccx_leads/lead'), ['id' => 'ccx-lead-modal-form']); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="control-label"><?php echo _l('name'); ?></label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="phonenumber" class="control-label"><?php echo _l('phonenumber'); ?></label>
                    <input type="text" id="phonenumber" name="phonenumber" class="form-control">
                </div>
                <div class="form-group">
                    <label for="email" class="control-label"><?php echo _l('email'); ?></label>
                    <input type="email" id="email" name="email" class="form-control">
                </div>
                <!-- Add other fields as needed (source, status, assigned) -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-ccx-leads', window.location.href, [7], [7]);

        appValidateForm($('#ccx-lead-modal-form'), {
            name: 'required'
        }, function (form) {
            $.post(form.action, $(form).serialize(), function (response) {
                response = JSON.parse(response);
                if (response.success) {
                    $('.table-ccx-leads').DataTable().ajax.reload();
                    $('#ccx_lead_modal').modal('hide');
                    alert_float('success', response.message);
                }
            });
            return false;
        });
    });

    function new_ccx_lead() {
        $('#ccx_lead_modal').modal('show');
    }
</script>
</body>

</html>