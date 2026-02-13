<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                            <a href="<?php echo admin_url('web_integration'); ?>"
                                class="btn btn-default pull-right display-block mleft5"><?php echo _l('go_back'); ?></a>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string()); ?>

                        <!-- Form Settings -->
                        <div class="row">
                            <div class="col-md-6">
                                <?php $value = (isset($form) ? $form->name : ''); ?>
                                <?php echo render_input('name', 'Form Name', $value); ?>

                                <?php $value = (isset($form) ? $form->form_category : 'Lead'); ?>
                                <?php echo render_select('form_category', [
                                    ['id' => 'Lead', 'name' => 'Lead'],
                                    ['id' => 'Appointment', 'name' => 'Appointment'],
                                    ['id' => 'Lead + Appointment', 'name' => 'Lead + Appointment'],
                                ], ['id', 'name'], 'Form Category', $value); ?>

                                <?php $value = (isset($form) ? $form->description : ''); ?>
                                <?php echo render_textarea('description', 'Short Description', $value); ?>

                                <?php $value = (isset($form) ? $form->success_message : 'Form submitted successfully.'); ?>
                                <?php echo render_textarea('success_message', 'Success Message', $value); ?>

                                <div class="form-group">
                                    <label for="link_with_leads" class="control-label clearfix">Link with Leads</label>
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="link_with_leads_yes" name="link_with_leads" value="1"
                                            <?php if (isset($form) && $form->link_with_leads == 1) {
                                                echo 'checked';
                                            } ?>>
                                        <label for="link_with_leads_yes"><?php echo _l('yes'); ?></label>
                                    </div>
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="link_with_leads_no" name="link_with_leads" value="0"
                                            <?php if (isset($form) && $form->link_with_leads == 0) {
                                                echo 'checked';
                                            } else if (!isset($form)) {
                                                echo 'checked';
                                            } ?>>
                                        <label for="link_with_leads_no"><?php echo _l('no'); ?></label>
                                    </div>
                                </div>

                                <div id="lead_settings_wrapper"
                                    class="<?php echo (isset($form) && $form->link_with_leads == 1) ? '' : 'hide'; ?>">
                                    <?php $value = (isset($form) ? $form->lead_source : ''); ?>
                                    <?php echo render_select('lead_source', $sources, ['id', 'name'], 'Lead Source', $value); ?>

                                    <?php $value = (isset($form) ? $form->lead_assigned : ''); ?>
                                    <?php echo render_select('lead_assigned', $members, ['staffid', ['firstname', 'lastname']], 'Lead Assignee', $value); ?>
                                </div>

                                <div class="form-group">
                                    <label for="link_with_appointments" class="control-label clearfix">Link with
                                        Appointment</label>
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="link_with_appointments_yes"
                                            name="link_with_appointments" value="1" <?php if (isset($form) && $form->link_with_appointments == 1) {
                                                echo 'checked';
                                            } ?>>
                                        <label for="link_with_appointments_yes"><?php echo _l('yes'); ?></label>
                                    </div>
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="link_with_appointments_no" name="link_with_appointments"
                                            value="0" <?php if (isset($form) && $form->link_with_appointments == 0) {
                                                echo 'checked';
                                            } else if (!isset($form)) {
                                                echo 'checked';
                                            } ?>>
                                        <label for="link_with_appointments_no"><?php echo _l('no'); ?></label>
                                    </div>
                                </div>

                                <?php $value = (isset($form) ? $form->secret_key : ''); ?>
                                <div class="form-group">
                                    <label for="secret_key" class="control-label">Secret Key</label>
                                    <div class="input-group">
                                        <input type="text" id="secret_key" name="secret_key" class="form-control"
                                            value="<?php echo $value; ?>">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"
                                                onclick="generate_secret_key();">Generate</button>
                                        </span>
                                    </div>
                                    <p class="help-block">Protect your API with a secret key. Leave blank for no
                                        protection.</p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-info pull-right">
                                    <?php echo _l('save'); ?>
                                </button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>

                        <?php if (isset($form)) { ?>
                            <hr />
                            <h4 class="bold">Form Fields</h4>
                            <?php echo form_open($this->uri->uri_string()); ?>
                            <div class="table-responsive">
                                <table class="table dt-table" data-order-col="4" data-order-type="asc">
                                    <thead>
                                        <th>Field</th>
                                        <th>Visible</th>
                                        <th>Required</th>
                                        <th>Custom Label</th>
                                        <th>Order</th>
                                        <th>Custom_fields</th>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($fields as $field) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo ucfirst(str_replace('_', ' ', $field['field_id'])); ?>
                                                </td>
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox"
                                                            name="fields[<?php echo $field['id']; ?>][is_visible]" <?php if ($field['is_visible'] == 1) {
                                                                   echo 'checked';
                                                               } ?>>
                                                        <label></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox"
                                                            name="fields[<?php echo $field['id']; ?>][is_required]" <?php if ($field['is_required'] == 1) {
                                                                   echo 'checked';
                                                               } ?>>
                                                        <label></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="fields[<?php echo $field['id']; ?>][custom_label]"
                                                        class="form-control" value="<?php echo $field['custom_label']; ?>">
                                                </td>
                                                <td>
                                                    <input type="number" name="fields[<?php echo $field['id']; ?>][field_order]"
                                                        class="form-control" value="<?php echo $field['field_order']; ?>">
                                                </td>
                                                <td>
                                                    <?php echo $this->web_integration_model->get_field_slug($field['field_id']); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-info pull-right">Save Fields</button>
                            <?php echo form_close(); ?>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url('modules/web_integration/assets/js/form_builder.js'); ?>"></script>