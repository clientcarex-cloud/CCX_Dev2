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
                        </h4>
                        <hr class="hr-panel-heading" />
                        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#fields" aria-controls="fields" role="tab" data-toggle="tab">
                                            Fields
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#ordering" aria-controls="ordering" role="tab" data-toggle="tab">
                                            Ordering
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#roller_coaster" aria-controls="roller_coaster" role="tab"
                                            data-toggle="tab">
                                            Roller Coaster
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#reports" aria-controls="reports" role="tab" data-toggle="tab">
                                            Reports
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#call_mgmt" aria-controls="call_mgmt" role="tab" data-toggle="tab">
                                            Call Mgmt
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#wa_web" aria-controls="wa_web" role="tab" data-toggle="tab">
                                            WA Web
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="fields">
                                <?php echo form_open(admin_url('ccx_leads/settings')); ?>
                                <div class="table-responsive">
                                    <table class="table dt-table" data-order-col="0" data-order-type="asc">
                                        <thead>
                                            <tr>
                                                <th>Field Name</th>
                                                <th>Slug</th>
                                                <th>Mandatory</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ccx_leads_fields as $key => $field) { ?>
                                                <tr>
                                                    <td>
                                                        <input type="hidden"
                                                            name="ccx_leads_fields[<?php echo $key; ?>][name]"
                                                            value="<?php echo $field['name']; ?>">
                                                        <?php echo $field['name']; ?>
                                                    </td>
                                                    <td>
                                                        <input type="hidden"
                                                            name="ccx_leads_fields[<?php echo $key; ?>][slug]"
                                                            value="<?php echo $field['slug']; ?>">
                                                        <?php echo $field['slug']; ?>
                                                    </td>
                                                    <td>
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="hidden"
                                                                name="ccx_leads_fields[<?php echo $key; ?>][mandatory]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="ccx_leads_fields[<?php echo $key; ?>][mandatory]"
                                                                value="1" <?php if (isset($field['mandatory']) && $field['mandatory'] == 1) {
                                                                    echo 'checked';
                                                                } ?>>
                                                            <label></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="onoffswitch">
                                                            <input type="hidden"
                                                                name="ccx_leads_fields[<?php echo $key; ?>][status]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="ccx_leads_fields[<?php echo $key; ?>][status]"
                                                                class="onoffswitch-checkbox" id="status_<?php echo $key; ?>"
                                                                value="1" <?php if (isset($field['status']) && $field['status'] == 1) {
                                                                    echo 'checked';
                                                                } ?>>
                                                            <label class="onoffswitch-label"
                                                                for="status_<?php echo $key; ?>"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
                                <?php echo form_close(); ?>

                                <hr />
                                <h4 class="font-bold">Custom Fields</h4>
                                <a href="#" onclick="new_custom_field(); return false;" class="btn btn-info mbot20">New
                                    Custom Field</a>
                                <div class="table-responsive">
                                    <table class="table dt-table" data-order-col="0" data-order-type="asc">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($custom_fields)) {
                                                foreach ($custom_fields as $field) { ?>
                                                    <tr>
                                                        <td><?php echo $field['name']; ?></td>
                                                        <td><?php echo ucfirst($field['type']); ?></td>
                                                        <td>
                                                            <div class="onoffswitch">
                                                                <input type="checkbox"
                                                                    data-switch-url="<?php echo admin_url('ccx_leads/change_custom_field_status'); ?>"
                                                                    name="onoffswitch" class="onoffswitch-checkbox"
                                                                    id="c_field_<?php echo $field['id']; ?>"
                                                                    data-id="<?php echo $field['id']; ?>" <?php echo ($field['status'] == 1 ? 'checked' : ''); ?>>
                                                                <label class="onoffswitch-label"
                                                                    for="c_field_<?php echo $field['id']; ?>"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a href="#"
                                                                onclick="edit_custom_field(<?php echo $field['id']; ?>); return false;"
                                                                class="btn btn-default btn-icon"><i
                                                                    class="fa fa-pencil-square-o"></i></a>
                                                            <a href="<?php echo admin_url('ccx_leads/delete_custom_field/' . $field['id']); ?>"
                                                                class="btn btn-danger btn-icon _delete"><i
                                                                    class="fa fa-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Custom Field Modal -->
                                <div class="modal fade" id="custom_field_modal" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title">Custom Field</h4>
                                            </div>
                                            <?php echo form_open(admin_url('ccx_leads/save_custom_field'), ['id' => 'custom-field-form']); ?>
                                            <div class="modal-body">
                                                <input type="hidden" name="id">
                                                <?php echo render_input('name', 'ccx_leads_name'); ?>
                                                <?php echo render_select('type', [
                                                    ['id' => 'text', 'name' => 'Text'],
                                                    ['id' => 'number', 'name' => 'Number'],
                                                    ['id' => 'textarea', 'name' => 'Textarea'],
                                                    ['id' => 'select', 'name' => 'Select'],
                                                    ['id' => 'date', 'name' => 'Date'],
                                                    ['id' => 'email', 'name' => 'Email'],
                                                ], ['id', 'name'], 'Type'); ?>

                                                <div id="options_wrapper" class="hide">
                                                    <?php echo render_textarea('options', 'Options (one per line or comma separated)', '', ['rows' => 5]); ?>
                                                </div>

                                                <div class="checkbox checkbox-primary">
                                                    <input type="checkbox" name="mandatory" id="cf_mandatory">
                                                    <label for="cf_mandatory">Mandatory</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-info">Save</button>
                                            </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function new_custom_field() {
                                        $('#custom_field_modal input[name="id"]').val('');
                                        $('#custom_field_modal input[name="name"]').val('');
                                        $('#custom_field_modal select[name="type"]').val('text').change();
                                        $('#custom_field_modal textarea[name="options"]').val('');
                                        $('#custom_field_modal input[name="mandatory"]').prop('checked', false);
                                        $('#custom_field_modal').modal('show');
                                    }
                                    function edit_custom_field(id) {
                                        $.get(admin_url + 'ccx_leads/get_custom_field/' + id, function (response) {
                                            response = JSON.parse(response);
                                            $('#custom_field_modal input[name="id"]').val(response.id);
                                            $('#custom_field_modal input[name="name"]').val(response.name);
                                            $('#custom_field_modal select[name="type"]').val(response.type).change();
                                            $('#custom_field_modal textarea[name="options"]').val(response.options);
                                            $('#custom_field_modal input[name="mandatory"]').prop('checked', response.mandatory == 1);
                                            $('#custom_field_modal').modal('show');
                                        });
                                    }
                                    $(function () {
                                        $('select[name="type"]').on('change', function () {
                                            if ($(this).val() == 'select') {
                                                $('#options_wrapper').removeClass('hide');
                                            } else {
                                                $('#options_wrapper').addClass('hide');
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="ordering">
                                <p>Ordering settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="roller_coaster">
                                <p>Roller Coaster settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="reports">
                                <p>Reports settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="call_mgmt">
                                <p>Call Mgmt settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="wa_web">
                                <p>WA Web settings coming soon...</p>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <a href="<?php echo admin_url('ccx_leads'); ?>" class="btn btn-default">
                            <?php echo _l('go_back'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>