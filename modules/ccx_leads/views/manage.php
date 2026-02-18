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
                            _l('ccx_leads_id'),
                            _l('ccx_leads_name'),
                            _l('ccx_leads_phonenumber'),
                            _l('ccx_leads_email'),
                            _l('ccx_leads_status'),
                            _l('ccx_leads_assigned'),
                            _l('ccx_leads_dateadded'),
                            _l('ccx_leads_options'),
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
                <?php
                $fields_settings = get_option('ccx_leads_field_settings');
                $fields_map = [];
                if ($fields_settings) {
                    $decoded = json_decode($fields_settings, true);
                    foreach ($decoded as $f) {
                        $fields_map[$f['slug']] = $f;
                    }
                }
                $get_field = function($slug) use ($fields_map) {
                    return isset($fields_map[$slug]) ? $fields_map[$slug] : ['status' => 1, 'mandatory' => 0];
                };
                ?>

                <?php $f = $get_field('name'); if ($f['status'] == 1) { ?>
                <div class="form-group">
                    <label for="name" class="control-label">
                        <?php echo _l('ccx_leads_name'); ?>
                        <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                    </label>
                    <input type="text" id="name" name="name" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>>
                </div>
                <?php } ?>

                <?php $f = $get_field('phonenumber'); if ($f['status'] == 1) { ?>
                <div class="form-group" id="phone_group">
                    <label for="phonenumber" class="control-label">
                        <?php echo _l('ccx_leads_phonenumber'); ?>
                        <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                    </label>
                    <input type="text" id="phonenumber" name="phonenumber" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>>
                    <span id="phone_counter" class="text-muted small pull-right" style="display:none;"></span>
                    <span id="phone_duplicate_error" class="text-danger small" style="display:none;"></span>
                </div>
                <?php } ?>

                <?php $f = $get_field('email'); if ($f['status'] == 1) { ?>
                <div class="form-group">
                    <label for="email" class="control-label">
                        <?php echo _l('ccx_leads_email'); ?>
                        <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                    </label>
                    <input type="email" id="email" name="email" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>>
                </div>
                <?php } ?>

                <?php $f = $get_field('title'); if ($f['status'] == 1) { ?>
                <div class="form-group">
                    <label for="title" class="control-label">
                        <?php echo _l('ccx_leads_title'); ?>
                        <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                    </label>
                    <input type="text" id="title" name="title" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>>
                </div>
                <?php } ?>

                <?php $f = $get_field('website'); if ($f['status'] == 1) { ?>
                <div class="form-group">
                    <label for="website" class="control-label">
                        <?php echo _l('ccx_leads_website'); ?>
                        <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                    </label>
                    <input type="text" id="website" name="website" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>>
                </div>
                <?php } ?>

                <?php $f = $get_field('description'); if ($f['status'] == 1) { ?>
                <div class="form-group">
                    <label for="description" class="control-label">
                        <?php echo _l('ccx_leads_description'); ?>
                        <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                    </label>
                    <textarea id="description" name="description" class="form-control" rows="4" <?php if ($f['mandatory'] == 1) echo 'required'; ?>></textarea>
                </div>
                <?php } ?>

                <div class="row">
                    <?php $f = $get_field('address'); if ($f['status'] == 1) { ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address" class="control-label">
                                <?php echo _l('ccx_leads_address'); ?>
                                <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                            </label>
                            <input type="text" id="address" name="address" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>>
                        </div>
                    </div>
                    <?php } ?>
                    <?php $f = $get_field('city'); if ($f['status'] == 1) { ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city" class="control-label">
                                <?php echo _l('ccx_leads_city'); ?>
                                <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                            </label>
                            <input type="text" id="city" name="city" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <div class="row">
                    <?php $f = $get_field('state'); if ($f['status'] == 1) { ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="state" class="control-label">
                                <?php echo _l('ccx_leads_state'); ?>
                                <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                            </label>
                            <input type="text" id="state" name="state" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>>
                        </div>
                    </div>
                    <?php } ?>
                    <?php $f = $get_field('country'); if ($f['status'] == 1) { ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="country" class="control-label">
                                <?php echo _l('ccx_leads_country'); ?>
                                <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                            </label>
                            <select id="country" name="country" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <?php foreach (get_all_countries() as $country) { ?>
                                    <option value="<?php echo $country['country_id']; ?>" <?php if (get_option('customer_default_country') == $country['country_id']) {
                                           echo 'selected';
                                       } ?>><?php echo $country['short_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php } ?>
                    <?php $f = $get_field('zip'); if ($f['status'] == 1) { ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="zip" class="control-label">
                                <?php echo _l('ccx_leads_zip'); ?>
                                <?php if ($f['mandatory'] == 1) echo '<span class="text-danger">*</span>'; ?>
                            </label>
                            <input type="text" id="zip" name="zip" class="form-control" <?php if ($f['mandatory'] == 1) echo 'required'; ?>>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <!-- Add other fields as needed (source, status, assigned) -->
                <div class="row">
                    <?php $f = $get_field('status'); if ($f['status'] == 1) { ?>
                    <div class="col-md-6">
                        <?php 
                            $attrs = [];
                            if ($f['mandatory'] == 1) $attrs['required'] = true;
                            echo render_select('status', $statuses, array('id', 'name'), 'ccx_leads_status', (isset($lead) ? $lead->status : ''), $attrs); 
                        ?>
                    </div>
                    <?php } ?>
                    <?php $f = $get_field('source'); if ($f['status'] == 1) { // Assuming 'source' is mapped or we add it to settings later. Defaulting to show if not in map or treating as 'source' slug. ?>
                    <div class="col-md-6">
                        <?php echo render_select('source', $sources, array('id', 'name'), 'ccx_leads_source', (isset($lead) ? $lead->source : '')); ?>
                    </div>
                    <?php } ?>
                </div>
                 <div class="row">
                    <?php $f = $get_field('assigned'); if ($f['status'] == 1) { ?>
                    <div class="col-md-6">
                        <?php 
                            $attrs = [];
                            if ($f['mandatory'] == 1) $attrs['required'] = true;
                            echo render_select('assigned', $staff_members, array('staffid', array('firstname', 'lastname')), 'ccx_leads_assigned', (isset($lead) ? $lead->assigned : get_staff_user_id()), $attrs); 
                        ?>
                    </div>
                    <?php } ?>
                    <?php $f = $get_field('lead_value'); if ($f['status'] == 1) { ?>
                     <div class="col-md-6">
                         <?php 
                            $attrs = ['step' => '0.01'];
                            if ($f['mandatory'] == 1) $attrs['required'] = true;
                            echo render_input('lead_value', 'ccx_leads_value', (isset($lead) ? $lead->lead_value : ''), 'number', $attrs); 
                         ?>
                    </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-12">
                         <?php 
                            $attrs = [];
                            if ($f['mandatory'] == 1) $attrs['required'] = true;
                            echo render_select('priority', $priorities, array('priorityid', 'name'), 'ccx_leads_priority', (isset($lead) ? $lead->priority : ''), $attrs); 
                         ?>
                    </div>
                </div>

                <?php
                // Fetch Custom Fields
                $custom_fields = [];
                if($this->db->table_exists(db_prefix() . 'ccx_leads_custom_fields')){
                    $custom_fields = $this->db->where('status', 1)->order_by('field_order', 'asc')->get(db_prefix() . 'ccx_leads_custom_fields')->result_array();
                }
                
                if(!empty($custom_fields)) {
                    echo '<div class="row">';
                    foreach($custom_fields as $field) {
                        $col = ($field['type'] == 'textarea' || $field['type'] == 'select' && count($custom_fields) % 2 != 0) ? 12 : 6;
                        echo '<div class="col-md-'.$col.'">';
                        $attrs = [];
                        if($field['mandatory'] == 1) $attrs['required'] = true;
                        
                        $input_name = 'custom_fields['.$field['id'].']';
                        $label = $field['name'];

                        if($field['type'] == 'text' || $field['type'] == 'number' || $field['type'] == 'email') {
                            echo render_input($input_name, $label, '', $field['type'], $attrs);
                        } elseif($field['type'] == 'date') {
                            echo render_date_input($input_name, $label, '', $attrs);
                        } elseif($field['type'] == 'textarea') {
                            echo render_textarea($input_name, $label, '', $attrs);
                        } elseif($field['type'] == 'select') {
                            $options = explode(',', $field['options']);
                            $select_options = [];
                            foreach($options as $opt) {
                                $opt = trim($opt);
                                $select_options[] = ['id' => $opt, 'name' => $opt];
                            }
                            echo render_select($input_name, $select_options, ['id', 'name'], $label, '', $attrs);
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?php echo _l('ccx_leads_close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('ccx_leads_save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php if (is_admin()) { ?>
    <a href="<?php echo admin_url('ccx_leads/settings'); ?>" class="floating-settings-btn" data-toggle="tooltip"
        title="<?php echo _l('settings'); ?>">
        <i class="fa fa-cogs"></i>
    </a>
    <style>
        .floating-settings-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            text-align: center;
            line-height: 50px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            font-size: 20px;
            color: #555;
            transition: all 0.3s ease;
            display: block;
        }

        .floating-settings-btn:hover {
            transform: scale(1.1);
            color: #333;
        }
    </style>
<?php } ?>
<?php init_tail(); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<style>
    .iti {
        width: 100%;
    }
</style>
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
        // Re-initialize to ensure it renders correctly if modal was hidden
        setTimeout(function () {
            var input = document.querySelector("#phonenumber");
            if (!input.classList.contains("iti-enabled")) {
                var iti = window.intlTelInput(input, {
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    separateDialCode: true,
                    autoPlaceholder: "aggressive",
                    initialCountry: "auto",
                    geoIpLookup: function (callback) {
                        $.get('https://ipapi.co/json', function () { }, "jsonp").always(function (resp) {
                            var countryCode = (resp && resp.country_code) ? resp.country_code : "in";
                            callback(countryCode);
                        });
                    }
                });
                input.classList.add("iti-enabled");

                input.addEventListener("countrychange", function () {
                    var placeholder = iti.promise.then(function () {
                        var mask = input.getAttribute("placeholder");
                        if (mask) {
                            // replace all non-digits with nothing to count length
                            var len = mask.replace(/\D/g, '').length;
                            input.setAttribute("maxLength", len + 5); // Add buffer for spaces/dashes
                            input.setAttribute("data-max-digits", len);
                            updatePhoneCounter(input, len);
                        }
                    });
                });

                input.addEventListener("input", function () {
                    var max = input.getAttribute("data-max-digits");
                    if (max) updatePhoneCounter(input, max);
                    $('#phone_duplicate_error').hide();
                    $('#phone_group').removeClass('has-error');
                    $('button[type="submit"]').prop('disabled', false);
                });

                input.addEventListener("blur", function () {
                    var val = input.value.trim();
                    if (val) {
                        $.post(admin_url + 'ccx_leads/check_duplicate_phone', {
                            phone: val
                        }, function (response) {
                            response = JSON.parse(response);
                            if (response.exists) {
                                $('#phone_group').addClass('has-error');
                                $('#phone_duplicate_error').text(response.message).show();
                                $('button[type="submit"]').prop('disabled', true);
                            }
                        });
                    }
                });
            }
        }, 500);
    }

    function updatePhoneCounter(input, max) {
        var val = input.value.replace(/\D/g, '');
        var len = val.length;
        $('#phone_counter').text(len + ' / ' + max).show();
        if (len > max) {
            $('#phone_counter').addClass('text-danger');
        } else {
            $('#phone_counter').removeClass('text-danger');
        }
    }
</script>
</body>

</html>