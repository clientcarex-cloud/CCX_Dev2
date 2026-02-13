<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="test_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('edit_test'); ?></span>
                    <span class="add-title"><?php echo _l('new_test'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/tests_master/test', ['id' => 'test_form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('description', 'test_name'); ?>
                        <?php echo render_input('code', 'code'); ?>
                        <div class="form-group">
                            <label for="department" class="control-label"><?php echo _l('department'); ?></label>
                            <select name="department" id="department" class="form-control selectpicker"
                                data-width="100%"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <?php foreach ($groups as $group) { ?>
                                    <option value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="department_id" class="control-label"><?php echo _l('lab_department'); ?></label>
                            <select name="department_id" id="department_id" class="form-control selectpicker"
                                data-width="100%"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <?php foreach ($departments as $department) { ?>
                                    <option value="<?php echo $department['departmentid']; ?>">
                                        <?php echo $department['name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="test_method_id" class="control-label"><?php echo _l('test_method'); ?></label>
                            <select name="test_method_id" id="test_method_id" class="form-control selectpicker"
                                data-width="100%"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <?php foreach ($tests_methods as $method) { ?>
                                    <option value="<?php echo $method['id']; ?>"><?php echo $method['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php echo render_input('rate', 'price', '', 'number'); ?>
                        <?php echo render_input('b2b_price', 'B2B Price', '', 'number'); ?>
                        <?php echo render_textarea('long_description', 'description'); ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="is_blood_sample_required" id="is_blood_sample_required"
                                value="1">
                            <label for="is_blood_sample_required"><?php echo _l('Is Blood Sample Required'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="is_price_changable" id="is_price_changable" value="1">
                            <label for="is_price_changable"><?php echo _l('Is Price Changable'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="is_authorization_required" id="is_authorization_required"
                                value="1">
                            <label
                                for="is_authorization_required"><?php echo _l('Is Authorization Required'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label for="is_active"><?php echo _l('is_active'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>