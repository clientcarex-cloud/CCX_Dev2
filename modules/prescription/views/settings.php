<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Prescription Settings</h4>
                        <hr class="hr-panel-heading" />
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#filters" aria-controls="filters" role="tab"
                                            data-toggle="tab">Filters</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#hide_show" aria-controls="hide_show" role="tab"
                                            data-toggle="tab">Hide/Show</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#digital_prescription" aria-controls="digital_prescription" role="tab"
                                            data-toggle="tab">Digital Prescription</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#prescriptions_table" aria-controls="prescriptions_table" role="tab"
                                            data-toggle="tab">Prescriptions Table</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#prescription_qas" aria-controls="prescription_qas" role="tab"
                                            data-toggle="tab">Prescription QAs</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="filters">
                                <?php echo form_open(admin_url('prescription/settings')); ?>
                                <input type="hidden" name="settings_group" value="filters">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="prescription_default_date_filter">Default Date Filter</label>
                                        <select name="prescription_default_date_filter"
                                            id="prescription_default_date_filter" class="form-control selectpicker">
                                            <option value="" <?php echo get_option('prescription_default_date_filter') == '' ? 'selected' : ''; ?>>None</option>
                                            <option value="today" <?php echo get_option('prescription_default_date_filter') == 'today' ? 'selected' : ''; ?>>Today</option>
                                            <option value="yesterday" <?php echo get_option('prescription_default_date_filter') == 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                                            <option value="yesterday_today" <?php echo get_option('prescription_default_date_filter') == 'yesterday_today' ? 'selected' : ''; ?>>Yesterday & Today</option>
                                        </select>
                                    </div>
                                </div>
                                <hr />
                                <button type="submit" class="btn btn-info">Save Settings</button>
                                <?php echo form_close(); ?>
                            </div>

                            <!-- Hide/Show Tab -->
                            <div role="tabpanel" class="tab-pane" id="hide_show">
                                <?php echo form_open(admin_url('prescription/settings')); ?>
                                <input type="hidden" name="settings_group" value="hide_show">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="text-info bold">Toggle visibility of filters on the Prescription list
                                            page.</p>
                                    </div>
                                    <div class="col-md-12">
                                        <!-- Date Range Filter -->
                                        <div class="form-group">
                                            <label for="prescription_show_date_filter">Show Date Filter</label>
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="prescription_show_date_filter"
                                                    class="onoffswitch-checkbox" id="prescription_show_date_filter"
                                                    value="1" <?php echo get_option('prescription_show_date_filter') == '1' || get_option('prescription_show_date_filter') === '' ? 'checked' : ''; ?>>
                                                <label class="onoffswitch-label" for="prescription_show_date_filter">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <button type="submit" class="btn btn-info">Save Settings</button>
                                <?php echo form_close(); ?>
                            </div>
                            <!-- Digital Prescription Tab -->
                            <div role="tabpanel" class="tab-pane" id="digital_prescription">
                                <?php echo form_open(admin_url('prescription/settings')); ?>
                                <input type="hidden" name="settings_group" value="digital_prescription">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="text-info bold">Configure Input Controls</p>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="prescription_mode_type" class="control-label">Prescription Mode Type</label>
                                                    <select name="prescription_mode_type" id="prescription_mode_type" class="form-control selectpicker">
                                                        <option value="digital" <?php echo (get_option('prescription_mode_type') == 'digital' || get_option('prescription_mode_type') == '') ? 'selected' : ''; ?>>Digital</option>
                                                        <option value="manual" <?php echo get_option('prescription_mode_type') == 'manual' ? 'selected' : ''; ?>>Manual</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Enable Autosave (10s)</label>
                                                    <div class="onoffswitch">
                                                        <input type="checkbox" name="prescription_enable_autosave"
                                                            class="onoffswitch-checkbox" id="prescription_enable_autosave"
                                                            value="1" <?php echo get_option('prescription_enable_autosave') == '1' ? 'checked' : ''; ?>>
                                                        <label class="onoffswitch-label" for="prescription_enable_autosave">
                                                            <span class="onoffswitch-inner"></span>
                                                            <span class="onoffswitch-switch"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="prescription_edit_timeout" class="control-label">Disable Edit Button After (Completed Prescriptions)</label>
                                                    <select name="prescription_edit_timeout" id="prescription_edit_timeout" class="form-control selectpicker">
                                                        <option value="5" <?php echo get_option('prescription_edit_timeout') == '5' ? 'selected' : ''; ?>>5 Minutes</option>
                                                        <option value="10" <?php echo get_option('prescription_edit_timeout') == '10' ? 'selected' : ''; ?>>10 Minutes</option>
                                                        <option value="15" <?php echo get_option('prescription_edit_timeout') == '15' ? 'selected' : ''; ?>>15 Minutes</option>
                                                        <option value="30" <?php echo get_option('prescription_edit_timeout') == '30' ? 'selected' : ''; ?>>30 Minutes</option>
                                                        <option value="60" <?php echo get_option('prescription_edit_timeout') == '60' ? 'selected' : ''; ?>>1 Hour</option>
                                                        <option value="360" <?php echo get_option('prescription_edit_timeout') == '360' ? 'selected' : ''; ?>>6 Hours</option>
                                                        <option value="720" <?php echo get_option('prescription_edit_timeout') == '720' ? 'selected' : ''; ?>>12 Hours</option>
                                                        <option value="1440" <?php echo get_option('prescription_edit_timeout') == '1440' ? 'selected' : ''; ?>>24 Hours</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <hr />

                                            <div class="clearfix mbottom15">
                                                <button type="button" class="btn btn-info pull-left" onclick="add_custom_field()">Add New Input</button>
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Field Order</th>
                                                        <th>Input Name</th>
                                                        <th>Usage</th>
                                                        <th>Input Type</th>
                                                        <th>Master Data</th>
                                                        <th>Mandatory</th>
                                                        <th>Hide/Show</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // $custom_fields passed from controller
                                                    foreach ($custom_fields as $field) {
                                                        $key = $field['slug'];
                                                        $label = $field['label'];
                                                        $type_val = $field['type'];
                                                        $is_system = $field['is_system'];
                                                        
                                                        // Template Key for QAs
                                                        $template_key = 'prescription_qa_template_' . $key;
                                                        // For system fields, we still use get_option for backward compatibility if needed, 
                                                        // OR we rely on the `prescription_custom_fields` table data. 
                                                        // Controller settings save logic updates BOTH options and table.
                                                        // But here we should display from the TABLE data ($field) as source of truth.
                                                        
                                                        // However, QA Template ID for system fields is still stored in options.
                                                        // For custom fields... we need to decide where to store QA Template ID.
                                                        // Logic: Controller `settings` method saves `prescription_qa_template_$slug` option for ALL fields.
                                                        $template_val = get_option($template_key);
                                                        ?>
                                                        <tr>
                                                            <td width="5%">
                                                                <input type="number" name="field_order[<?php echo $field['id']; ?>]" class="form-control" value="<?php echo $field['field_order']; ?>">
                                                            </td>
                                                            <td>
                                                                <?php echo $label; ?>
                                                                <?php if($is_system == 0) echo ' <span class="label label-info">Custom</span>'; ?>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                    $count = isset($usage_counts[$field['id']]) ? $usage_counts[$field['id']] : 0;
                                                                    echo '<span class="label label-default">' . $count . '</span>';
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <select name="type[<?php echo $field['id']; ?>]" class="form-control selectpicker" onchange="check_qa_input(this, 'wrapper_<?php echo $key; ?>')">
                                                                            <option value="smart_box" <?php echo ($type_val == 'smart_box' ? 'selected' : ''); ?>>Smart Box</option>
                                                                            <option value="smart_editor" <?php echo ($type_val == 'smart_editor' ? 'selected' : ''); ?>>Smart Editor</option>
                                                                            <option value="qas" <?php echo ($type_val == 'qas' ? 'selected' : ''); ?>>QAs (Questions)</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6" id="wrapper_<?php echo $key; ?>" style="<?php echo ($type_val == 'qas' ? '' : 'display:none;'); ?>">
                                                                        <select name="<?php echo $template_key; ?>" class="form-control selectpicker" data-live-search="true" title="Select QA Template">
                                                                            <option value=""></option>
                                                                            <?php foreach ($qa_templates as $t) { ?>
                                                                                <option value="<?php echo $t['id']; ?>" <?php echo ($template_val == $t['id'] ? 'selected' : ''); ?>><?php echo $t['name']; ?></option>
                                                                            <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                    <a href="<?php echo admin_url('prescription/master_data/'.$key); ?>" class="btn btn-default btn-xs" target="_blank">Master Data</a>
                                                            </td>
                                                            <td>
                                                                <div class="onoffswitch">
                                                                    <input type="checkbox" name="mandatory[<?php echo $field['id']; ?>]" class="onoffswitch-checkbox" id="mandatory_<?php echo $key; ?>" value="1" <?php echo ($field['mandatory'] == 1 ? 'checked' : ''); ?>>
                                                                    <label class="onoffswitch-label" for="mandatory_<?php echo $key; ?>">
                                                                        <span class="onoffswitch-inner"></span>
                                                                        <span class="onoffswitch-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="onoffswitch">
                                                                    <input type="checkbox" name="is_active[<?php echo $field['id']; ?>]" class="onoffswitch-checkbox" id="show_<?php echo $key; ?>" value="1" <?php echo ($field['is_active'] == 1 ? 'checked' : ''); ?>>
                                                                    <label class="onoffswitch-label" for="show_<?php echo $key; ?>">
                                                                        <span class="onoffswitch-inner"></span>
                                                                        <span class="onoffswitch-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <?php if($is_system == 0) { 
                                                                    if ($count == 0) { ?>
                                                                    <button type="button" class="btn btn-danger btn-icon" onclick="delete_custom_field(<?php echo $field['id']; ?>)">
                                                                        <i class="fa fa-remove"></i>
                                                                    </button>
                                                                    <?php } else { ?>
                                                                        <button type="button" class="btn btn-danger btn-icon" disabled data-toggle="tooltip" title="Cannot delete: Field is in use">
                                                                            <i class="fa fa-remove"></i>
                                                                        </button>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <span class="text-muted">System Field</span>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>

                                        <hr />
                                        <h4>Other Settings</h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Settings Name</th>
                                                        <th>Mandatory</th>
                                                        <th>Hide/Show</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $other_fields = [
                                                        'prescription_show_vitals' => 'Vitals',
                                                        'prescription_show_tests_requested' => 'Tests Requested',
                                                        'prescription_show_next_visit' => 'Next Visit',
                                                        'prescription_show_medicine' => 'Medicine'
                                                    ];
                                                    foreach ($other_fields as $key => $label) {
                                                        $val = get_option($key);
                                                        $checked = ($val == '1' || $val === '') ? 'checked' : '';
                                                        
                                                        // Resolve Mandatory Key
                                                        $mandatory_key = str_replace('_show_', '_mandatory_', $key);
                                                        $mandatory_val = get_option($mandatory_key);
                                                        $mandatory_checked = ($mandatory_val == '1') ? 'checked' : '';
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $label; ?></td>
                                                            <td>
                                                                <div class="onoffswitch">
                                                                    <input type="checkbox" name="<?php echo $mandatory_key; ?>"
                                                                        class="onoffswitch-checkbox" id="<?php echo $mandatory_key; ?>"
                                                                        value="1" <?php echo $mandatory_checked; ?>>
                                                                    <label class="onoffswitch-label" for="<?php echo $mandatory_key; ?>">
                                                                        <span class="onoffswitch-inner"></span>
                                                                        <span class="onoffswitch-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="onoffswitch">
                                                                    <input type="checkbox" name="<?php echo $key; ?>"
                                                                        class="onoffswitch-checkbox" id="<?php echo $key; ?>"
                                                                        value="1" <?php echo $checked; ?>>
                                                                    <label class="onoffswitch-label" for="<?php echo $key; ?>">
                                                                        <span class="onoffswitch-inner"></span>
                                                                        <span class="onoffswitch-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <hr />
                                    <button type="submit" class="btn btn-info">Save Settings</button>
                                    <?php echo form_close(); ?>
                            </div>
                            </div>

                            <!-- Prescriptions Table Tab -->
                            <div role="tabpanel" class="tab-pane" id="prescriptions_table">
                                <?php echo form_open(admin_url('prescription/settings')); ?>
                                <input type="hidden" name="settings_group" value="prescriptions_table">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="text-info bold">Configure Prescriptions Table Visibility</p>
                                    </div>
                                    <div class="col-md-12">
                                        <!-- Options Column Visibility -->
                                        <div class="form-group">
                                            <label for="prescription_show_options_column">Show "Options" Column</label>
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="prescription_show_options_column"
                                                    class="onoffswitch-checkbox" id="prescription_show_options_column"
                                                    value="1" <?php echo get_option('prescription_show_options_column') == '1' || get_option('prescription_show_options_column') === '' ? 'checked' : ''; ?>>
                                                <label class="onoffswitch-label" for="prescription_show_options_column">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <button type="submit" class="btn btn-info">Save Settings</button>
                                <?php echo form_close(); ?>
                            </div>

                            <!-- Prescription QAs Tab -->
                            <div role="tabpanel" class="tab-pane" id="prescription_qas">
                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="#" class="btn btn-info mbottom15" onclick="new_qa_template(); return false;">New QA Template</a>
                                        
                                        <div class="table-responsive">
                                            <table class="table dt-table" data-order-col="0" data-order-type="asc">
                                                <thead>
                                                    <tr>
                                                        <th>Template Name</th>
                                                        <th>Usage Count</th>
                                                        <th>Questions Count</th>
                                                        <th>Options</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($qa_templates as $template) { 
                                                        $questions = json_decode($template['questions'], true);
                                                        $count = is_array($questions) ? count($questions) : 0;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $template['name']; ?></td>
                                                        <td>
                                                            <?php 
                                                            if (isset($template['usage_count']) && $template['usage_count'] > 0) {
                                                                echo '<span class="label label-info">' . $template['usage_count'] . ' Prescriptions</span>';
                                                            } else {
                                                                echo '<span class="text-muted">Not Used</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $count; ?></td>
                                                        <td>
                                                            <a href="#" onclick="edit_qa_template(<?php echo $template['id']; ?>, '<?php echo addslashes($template['name']); ?>'); return false;" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                                                            <a href="<?php echo admin_url('prescription/delete_qa_template/' . $template['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                                            <div class="hidden" id="questions_<?php echo $template['id']; ?>"><?php echo $template['questions']; ?></div>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QA Template Modal -->
<div class="modal fade" id="qa_template_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('prescription/settings')); ?>
        <input type="hidden" name="settings_group" value="prescription_qas_save">
        <input type="hidden" name="id" id="qa_template_id">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">QA Template</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="control-label">Template Name</label>
                    <input type="text" class="form-control" name="name" id="qa_template_name" required>
                </div>
                <hr />
                <label>Questions</label>
                <div id="questions_wrapper">
                    <!-- Dynamic Questions -->
                </div>
                <button type="button" class="btn btn-success btn-xs" onclick="add_question_row()"><i class="fa fa-plus"></i> Add Question</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info">Save</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php init_tail(); ?>
<script>
    // Check for tab group in URL
    $(function(){
        var group = '<?php echo $this->input->get('group'); ?>';
        if(group == 'prescription_qas'){
            $('a[href="#prescription_qas"]').click();
        }
    });

    function add_question_row(value = '', required = false, type = 'text') {
        var index = $('#questions_wrapper .question-row').length;
        var html = '<div class="row mbottom10 question-row">';
        html += '<div class="col-md-7">';
        html += '<input type="text" name="questions['+index+'][text]" class="form-control" placeholder="Question" value="'+value+'" required>';
        html += '</div>';
        
        // Add Type Selector
        html += '<div class="col-md-2">';
        html += '<select name="questions['+index+'][type]" class="form-control" title="Answer Type">';
        html += '<option value="text" '+(type == 'text' ? 'selected' : '')+'>Text</option>';
        html += '<option value="checkbox" '+(type == 'checkbox' ? 'selected' : '')+'>True/False</option>';
        html += '</select>';
        html += '</div>';

        html += '<div class="col-md-2">';
        html += '<div class="checkbox checkbox-primary" style="margin-top: 8px;">';
        html += '<input type="checkbox" name="questions['+index+'][required]" id="req_'+index+'" value="1" '+(required ? 'checked' : '')+'>';
        html += '<label for="req_'+index+'">Mandatory</label>';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-1">';
        html += '<button type="button" class="btn btn-danger btn-icon" onclick="$(this).closest(\'.row\').remove();"><i class="fa fa-remove"></i></button>';
        html += '</div>';
        html += '</div>';
        $('#questions_wrapper').append(html);
    }

    function new_qa_template() {
        $('#qa_template_id').val('');
        $('#qa_template_name').val('');
        $('#questions_wrapper').html('');
        add_question_row(); // Add one empty row
        $('#qa_template_modal').modal('show');
    }

    function edit_qa_template(id, name) {
        $('#qa_template_id').val(id);
        $('#qa_template_name').val(name);
        $('#questions_wrapper').html('');
        
        var questions = JSON.parse($('#questions_' + id).text());
        if(questions && questions.length > 0) {
            $.each(questions, function(i, val){
                // Handle both old format (string) and new format (object)
                if (typeof val === 'string') {
                    add_question_row(val, false, 'text');
                } else {
                    add_question_row(val.text, val.required == '1', val.type || 'text');
                }
            });
        } else {
            add_question_row();
        }
        
        $('#qa_template_modal').modal('show');
    }

    function check_qa_input(select, wrapper_id) {
        if($(select).val() == 'qas') {
            $('#' + wrapper_id).show();
            // Try to refresh selectpicker if initialized
            try {
               $('#' + wrapper_id).find('.selectpicker').selectpicker('refresh');
            } catch(e) {}
        } else {
            $('#' + wrapper_id).hide();
        }
    }

    function add_custom_field() {
        $('#add_custom_field_modal').modal('show');
    }

    function delete_custom_field(id) {
        if(confirm('Are you sure you want to delete this field? Data associated with it will be lost.')) {
            $.post(admin_url + 'prescription/delete_custom_field/' + id, function(response) {
                var res = JSON.parse(response);
                if(res.success) {
                    alert_float('success', res.message);
                    window.location.reload();
                } else {
                    alert_float('danger', res.message);
                }
            });
        }
    }

    // Save Custom Field Logic
    $(function(){
        $('#add_custom_field_form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            
            $btn.prop('disabled', true);
            
            var data = $form.serialize();
            $.post(admin_url + 'prescription/save_custom_field', data, function(response) {
                var res = JSON.parse(response);
                if(res.success) {
                    $('#add_custom_field_modal').modal('hide');
                    alert_float('success', res.message);
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                } else {
                    alert_float('danger', res.message);
                    $btn.prop('disabled', false);
                }
            }).fail(function() {
                alert_float('danger', 'Error connecting to server');
                $btn.prop('disabled', false);
            });
        });
    });
</script>

<!-- Add Custom Field Modal -->
<div class="modal fade" id="add_custom_field_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('prescription/save_custom_field'), array('id'=>'add_custom_field_form')); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Input Field</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="field_label" class="control-label">Label Name</label>
                        <input type="text" class="form-control" name="label" id="field_label" required placeholder="e.g. Observation, History of Present Illness">
                    </div>
                    <div class="form-group">
                        <label for="field_type" class="control-label">Input Type</label>
                        <select name="type" class="form-control selectpicker" id="field_type">
                            <option value="smart_box">Smart Box</option>
                            <option value="smart_editor">Smart Editor</option>
                            <option value="qas">QAs (Questions)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Add Field</button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>
</body>

</html>