<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('settings'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('token_system/token_settings')); ?>

                        <div class="form-group">
                            <label for="token_system_workflow"
                                class="control-label"><?php echo _l('token_system_workflow'); ?></label>
                            <select name="token_system_workflow" id="token_system_workflow" class="form-control">
                                <option value="manual" <?php if (get_option('token_system_workflow') == 'manual') {
                                    echo 'selected';
                                } ?>><?php echo _l('workflow_manual'); ?></option>
                                <option value="select_patient" <?php if (get_option('token_system_workflow') == 'select_patient') {
                                    echo 'selected';
                                } ?>>
                                    <?php echo _l('workflow_select_patient'); ?></option>
                                <option value="smart" <?php if (get_option('token_system_workflow') == 'smart') {
                                    echo 'selected';
                                } ?>><?php echo _l('workflow_smart'); ?></option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>

</html>