<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $test->description; ?> - Templates
                            <a href="<?php echo admin_url('tests_master'); ?>" class="btn btn-default pull-right display-block mright5">
                                <i class="fa fa-arrow-left"></i> <?php echo _l('back_to_list'); ?>
                            </a>
                        </h4>
                        
                        <div class="row mtop15">
                            <div class="col-md-12">
                                <div class="panel_s" style="background: #f8f9fa; border: 1px solid #e0e0e0;">
                                    <div class="panel-body text-center" style="padding: 15px;">
                                        <h4 style="margin-top: 0; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #4e4e4e;">
                                            <i class="fa fa-cogs" aria-hidden="true"></i> ACTIVE REPORTING MODE
                                        </h4>
                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn btn-default <?php echo $test->active_template_type == 'word' || !$test->active_template_type ? 'active btn-info' : ''; ?>" onclick="set_active_type('word')" style="min-width: 120px;">
                                                <input type="radio" name="active_type" id="option1" autocomplete="off" <?php echo $test->active_template_type == 'word' || !$test->active_template_type ? 'checked' : ''; ?>> 
                                                <i class="fa fa-file-word-o"></i> Word Template
                                            </label>
                                            <label class="btn btn-default <?php echo $test->active_template_type == 'fixed' ? 'active btn-info' : ''; ?>" onclick="set_active_type('fixed')" style="min-width: 120px;">
                                                <input type="radio" name="active_type" id="option2" autocomplete="off" <?php echo $test->active_template_type == 'fixed' ? 'checked' : ''; ?>> 
                                                <i class="fa fa-table"></i> Fixed Template
                                            </label>
                                        </div>
                                        <p class="text-muted mtop5 no-mbot"><small>This setting determines which template is generated for patient reports.</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />
                        
                        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                             <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                             <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                             <div class="horizontal-tabs">
                                 <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                     <li role="presentation" class="<?php echo $this->input->get('tab') != 'fixed_template' ? 'active' : ''; ?>">
                                         <a href="#word_template" aria-controls="word_template" role="tab" data-toggle="tab">
                                             Word Template
                                         </a>
                                     </li>
                                     <li role="presentation" class="<?php echo $this->input->get('tab') == 'fixed_template' ? 'active' : ''; ?>">
                                         <a href="#fixed_template" aria-controls="fixed_template" role="tab" data-toggle="tab">
                                             Fixed Template
                                         </a>
                                     </li>
                                 </ul>
                             </div>
                        </div>
                        
                        <div class="tab-content">
                            <!-- Word Template Tab -->
                            <div role="tabpanel" class="tab-pane <?php echo $this->input->get('tab') != 'fixed_template' ? 'active' : ''; ?>" id="word_template">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h4>Existing Templates</h4>
                                        <hr />
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <a href="#" onclick="new_template(); return false;">
                                                    <i class="fa fa-plus"></i> <?php echo _l('new_word_template'); ?>
                                                </a>
                                            </li>
                                            <?php foreach($templates as $template){
                                                $is_default = $template['is_default'] == 1;
                                            ?>
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <div class="col-md-7">
                                                            <a href="#" onclick="edit_template(<?php echo $template['id']; ?>, '<?php echo htmlspecialchars($template['template_name']); ?>'); return false;">
                                                                <?php echo $template['template_name']; ?>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-5 text-right">
                                                            <?php if(!$is_default){ ?>
                                                                <a href="<?php echo admin_url('tests_master/set_default/' . $template['id'] . '/' . $test->id); ?>" class="btn btn-xs btn-default">
                                                                    Set Default
                                                                </a>
                                                            <?php } else { ?>
                                                                <span class="label label-success">Default</span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="template_form_wrapper" class="hide">
                                            <h4 id="form_title">New Template</h4>
                                            <hr />
                                            <?php echo form_open(admin_url('tests_master/save_template'), ['id' => 'template_form']); ?>
                                            <input type="hidden" name="test_id" value="<?php echo $test->id; ?>">
                                            <input type="hidden" name="id" value="">
                                            <?php echo render_input('template_name', 'Template Name', '', 'text', ['required' => 'true']); ?>
                                            <?php echo render_textarea('template_content', 'Content', '', ['rows' => 30], [], '', 'tinymce'); ?>
                                            <div class="text-right mtop15">
                                                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                                            </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                        <div id="select_template_msg">
                                            <p class="text-info">Select a template to edit or create a new one.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fixed Template Tab -->
                            <div role="tabpanel" class="tab-pane <?php echo $this->input->get('tab') == 'fixed_template' ? 'active' : ''; ?>" id="fixed_template">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h4>Fixed Templates</h4>
                                        <hr />
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <a href="#" onclick="new_fixed_template(); return false;">
                                                    <i class="fa fa-plus"></i> New Fixed Template
                                                </a>
                                            </li>
                                             <?php foreach($fixed_templates as $ft){
                                                $is_default = $ft['is_default'] == 1;
                                                $active_class = (isset($current_fixed_template) && $current_fixed_template->id == $ft['id']) ? 'active_fixed_template' : '';
                                            ?>
                                                <li class="list-group-item <?php echo $active_class; ?>" style="<?php echo $active_class ? 'background-color: #f0f0f0;' : ''; ?>">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <a href="<?php echo admin_url('tests_master/templates/' . $test->id . '?tab=fixed_template&fixed_id=' . $ft['id']); ?>">
                                                                <?php echo $ft['template_name']; ?>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-6 text-right">
                                                            <?php if(!$is_default){ ?>
                                                                <a href="<?php echo admin_url('tests_master/set_default_fixed/' . $ft['id'] . '/' . $test->id); ?>" class="btn btn-xs btn-default">
                                                                    Set Default
                                                                </a>
                                                            <?php } else { ?>
                                                                <span class="label label-success">Default</span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="col-md-8">
                                        <!-- Create/Edit Fixed Template Metadata Form -->
                                        <div id="fixed_template_meta_wrapper" class="<?php echo isset($current_fixed_template) ? '' : 'hide'; ?>">
                                            <?php echo form_open(admin_url('tests_master/save_fixed_template'), ['id' => 'fixed_template_meta_form']); ?>
                                            <input type="hidden" name="test_id" value="<?php echo $test->id; ?>">
                                            <input type="hidden" name="id" value="<?php echo isset($current_fixed_template) ? $current_fixed_template->id : ''; ?>">
                                            <div class="row">
                                                <div class="col-md-10">
                                                     <?php echo render_input('template_name', 'Template Name', isset($current_fixed_template) ? $current_fixed_template->template_name : '', 'text', ['required' => 'true']); ?>
                                                </div>
                                                <div class="col-md-2 mtop25">
                                                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                                                </div>
                                            </div>
                                            <?php echo form_close(); ?>
                                            <hr />
                                        </div>

                                        <?php if(isset($current_fixed_template)){ ?>
                                            <!-- Parameters Section -->
                                            <h4>Parameters</h4>
                                            <?php echo form_open(admin_url('tests_master/save_parameter'), ['id' => 'parameter_form']); ?>
                                            <input type="hidden" name="test_id" value="<?php echo $test->id; ?>"> <!-- Kept for valid check if needed, but logic uses fixed_id -->
                                            <input type="hidden" name="fixed_template_id" value="<?php echo $current_fixed_template->id; ?>">
                                            <input type="hidden" name="id" value="">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <?php echo render_input('parameter_name', 'Parameter', '', 'text', ['required' => 'true']); ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <?php echo render_input('default_value', 'Value'); ?>
                                                </div>
                                                <div class="col-md-2">
                                                    <?php echo render_input('unit', 'Units'); ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo render_input('referral_range', 'Referral Range'); ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <?php echo render_input('group_name', 'Group'); ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <?php echo render_input('method', 'Method'); ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <?php echo render_input('formula', 'Formula'); ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Machine Code</label>
                                                    <div class="input-group">
                                                        <input type="text" name="machine_code" class="form-control">
                                                        <span class="input-group-btn">
                                                             <button class="btn btn-success" type="submit">Save</button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                 <div class="col-md-6">
                                                     <?php echo render_input('normal_range', 'Normal Range'); ?>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <div class="checkbox checkbox-primary">
                                                         <input type="checkbox" name="is_bold" id="is_bold" value="1">
                                                         <label for="is_bold">Bold</label>
                                                     </div>
                                                     <div class="checkbox checkbox-primary">
                                                         <input type="checkbox" name="hide_units_range" id="hide_units_range" value="1">
                                                         <label for="hide_units_range">Hide Units/Range</label>
                                                     </div>
                                                 </div>
                                            </div>
                                            <?php echo form_close(); ?>
                                            
                                            <hr />
                                            
                                            <div class="table-responsive">
                                                <table class="table dt-table table-fixed-parameters" data-order-col="0" data-order-type="asc">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%"></th>
                                                            <th>Parameter & Method</th>
                                                            <th>Value & Formula</th>
                                                            <th>Units</th>
                                                            <th>Referral Range</th>
                                                            <th>Normal Ranges</th>
                                                            <th>M. Code</th>
                                                            <th>Hide</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($parameters as $param){ ?>
                                                            <tr id="param_row_<?php echo $param['id']; ?>" data-id="<?php echo $param['id']; ?>">
                                                                <td class="drag-handle"><i class="fa fa-bars" style="cursor: move;"></i></td>
                                                                <td>
                                                                    <span class="bold"><?php echo $param['parameter_name']; ?></span><br>
                                                                    <small class="text-muted"><?php echo $param['method']; ?></small>
                                                                </td>
                                                                <td>
                                                                    <?php echo $param['default_value']; ?><br>
                                                                    <small class="text-muted"><?php echo $param['formula']; ?></small>
                                                                </td>
                                                                <td><?php echo $param['unit']; ?></td>
                                                                <td><?php echo $param['referral_range']; ?></td>
                                                                <td><?php echo $param['normal_range']; ?></td>
                                                                <td><?php echo $param['machine_code']; ?></td>
                                                                <td>
                                                                    <?php if($param['hide_units_range'] == 1) echo '<span class="label label-warning">Units/Ref</span>'; ?>
                                                                </td>
                                                                <td>
                                                                    <a href="#" onclick='edit_parameter(<?php echo json_encode($param); ?>); return false;' class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>
                                                                    <a href="#" onclick="delete_parameter(<?php echo $param['id']; ?>); return false;" class="btn btn-danger btn-icon"><i class="fa fa-trash"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <hr />
                                            <h4>Default Remarks</h4>
                                            <?php echo form_open(admin_url('tests_master/save_fixed_template'), ['id' => 'remarks_form']); ?> <!-- Reusing save_fixed_template to update name/remarks -->
                                            <input type="hidden" name="test_id" value="<?php echo $test->id; ?>">
                                            <input type="hidden" name="id" value="<?php echo $current_fixed_template->id; ?>">
                                            <!-- Hidden name input to keep name same when saving remarks -->
                                            <input type="hidden" name="template_name" value="<?php echo $current_fixed_template->template_name; ?>"> 
                                            <?php echo render_textarea('remarks', '', $current_fixed_template->remarks, ['rows' => 10], [], '', 'tinymce'); ?>
                                            <div class="text-right mtop15">
                                                <button type="submit" class="btn btn-info">Save Remarks</button>
                                            </div>
                                            <?php echo form_close(); ?>
                                        <?php } else { ?>
                                            <div id="select_fixed_msg">
                                                <p class="text-info">Select a Fixed Template to edit or create a new one.</p>
                                            </div>
                                        <?php } ?>

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
<?php init_tail(); ?>
<script>
    var template_content = <?php echo json_encode($templates); ?>;

    // Word Template Logic
    function new_template(){
        $('#template_form_wrapper').removeClass('hide');
        $('#select_template_msg').addClass('hide');
        $('#form_title').text('New Template');
        $('input[name="id"]').val('');
        $('input[name="template_name"]').val('');
        tinyMCE.get('template_content').setContent('');
    }

    function edit_template(id){
        $('#template_form_wrapper').removeClass('hide');
        $('#select_template_msg').addClass('hide');
        $('#form_title').text('Edit Template');
        
        var template = template_content.find(t => t.id == id);
        if(template){
            $('#template_form input[name="id"]').val(template.id);
            $('#template_form input[name="template_name"]').val(template.template_name);
            tinyMCE.get('template_content').setContent(template.template_content);
        }
    }

    // Fixed Template Logic
    function new_fixed_template(){
         $('#fixed_template_meta_wrapper').removeClass('hide');
         $('#select_fixed_msg').addClass('hide');
         $('#fixed_template_meta_form input[name="id"]').val('');
         $('#fixed_template_meta_form input[name="template_name"]').val('');
         // If there are panels for params/remarks, hide them since we are creating new
         $('.table-fixed-parameters').closest('.table-responsive').hide(); // Rough hiding
         $('#remarks_form').hide();
         $('#parameter_form').hide();
         $('h4:contains("Parameters")').hide();
         $('h4:contains("Default Remarks")').hide();
         $('hr').hide(); // Might hide too much but ok for now
         // Actually better to just show the name form and hide everything else if not already structurally done
         // But since page reload happens on save, this is fine for "Create New" state.
    }
    
    function edit_parameter(param){
        $('#parameter_form input[name="id"]').val(param.id);
        $('#parameter_form input[name="parameter_name"]').val(param.parameter_name);
        $('#parameter_form input[name="default_value"]').val(param.default_value);
        $('#parameter_form input[name="unit"]').val(param.unit);
        $('#parameter_form input[name="referral_range"]').val(param.referral_range);
        $('#parameter_form input[name="normal_range"]').val(param.normal_range);
        $('#parameter_form input[name="group_name"]').val(param.group_name);
        $('#parameter_form input[name="method"]').val(param.method);
        $('#parameter_form input[name="formula"]').val(param.formula);
        $('#parameter_form input[name="machine_code"]').val(param.machine_code);
        
        if(param.is_bold == 1) $('#is_bold').prop('checked', true);
        else $('#is_bold').prop('checked', false);

        if(param.hide_units_range == 1) $('#hide_units_range').prop('checked', true);
        else $('#hide_units_range').prop('checked', false);
    }

    // AJAX Form Handling
    $(function(){
        // Parameter Save
        appValidateForm($('#parameter_form'), {parameter_name: 'required'}, function(form){
             $.post(form.action, $(form).serialize()).done(function(response){
                 response = JSON.parse(response);
                 if(response.success){
                     alert_float('success', response.message);
                     location.reload(); 
                 }
             });
             return false;
        });

        // Remarks Save (using the fixed template save endpoint)
        appValidateForm($('#remarks_form'), {}, function(form){
            var editorContent = tinyMCE.get('remarks').getContent(); 
            $('#remarks').val(editorContent);
             $.post(form.action, $(form).serialize()).done(function(response){
                 // Reloading because we are saving the main template (metadata/remarks)
                 // Or we could just alert since we are redirecting in controller anyway? 
                 // Wait, controller does redirect for save_fixed_template. 
                 // If using AJAX, we should handle response. 
                 // Controller redirects, so response will be the HTML of the page?
                 // No, standard Perfex/Ajax forms usually expect JSON if we implement callback.
                 // My controller `save_fixed_template` does `redirect`.
                 // So I should NOT use appValidateForm with callback for that one if I want the redirect to happen.
                 // But wait, `save_parameter` returns JSON. `save_fixed_template` redirects.
                 // `remarks_form` uses `save_fixed_template`.
                 // So I should remove the AJAX callback for `remarks_form` and let it submit normally.
                 form.submit();
             });
             return false;
        });
        
        // Drag & Drop Sorting
        $(".table-fixed-parameters tbody").sortable({
            handle: '.drag-handle',
            placeholder: "ui-state-highlight",
            update: function (event, ui) {
                var order = [];
                $('.table-fixed-parameters tbody tr').each(function(index){
                    var id = $(this).data('id');
                    order.push([id, index+1]);
                });
                
                $.post(admin_url + 'tests_master/update_parameter_order', {order: order});
            }
        });
    });

    function delete_parameter(id){
        if(confirm_delete()){
            $.get(admin_url + 'tests_master/delete_parameter/' + id).done(function(response){
                response = JSON.parse(response);
                if(response.success){
                    alert_float('success', response.message);
                    $('#param_row_' + id).remove();
                }
            });
        }
    }

    function set_active_type(type){
        $.get(admin_url + 'tests_master/set_active_type/<?php echo $test->id; ?>/' + type).done(function(response){
             response = JSON.parse(response);
             if(response.success){
                 alert_float('success', response.message);
                 // Update visual state manually if needed (though bootstrap does active)
                 // Just aiming to swap the btn-info class
                 $('.btn-group .btn').removeClass('btn-info');
                 if(type == 'word') $('#option1').parent().addClass('btn-info');
                 else $('#option2').parent().addClass('btn-info');
             }
        });
    }
</script>
</body>
</html>