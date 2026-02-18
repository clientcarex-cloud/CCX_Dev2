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
                <div class="form-group">
                    <label for="name" class="control-label"><?php echo _l('ccx_leads_name'); ?></label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group" id="phone_group">
                    <label for="phonenumber" class="control-label"><?php echo _l('ccx_leads_phonenumber'); ?></label>
                    <input type="text" id="phonenumber" name="phonenumber" class="form-control">
                    <span id="phone_counter" class="text-muted small pull-right" style="display:none;"></span>
                    <span id="phone_duplicate_error" class="text-danger small" style="display:none;"></span>
                </div>
                <div class="form-group">
                    <label for="email" class="control-label"><?php echo _l('ccx_leads_email'); ?></label>
                    <input type="email" id="email" name="email" class="form-control">
                </div>

                <div class="form-group">
                    <label for="title" class="control-label"><?php echo _l('ccx_leads_title'); ?></label>
                    <input type="text" id="title" name="title" class="form-control">
                </div>

                <div class="form-group">
                    <label for="website" class="control-label"><?php echo _l('ccx_leads_website'); ?></label>
                    <input type="text" id="website" name="website" class="form-control">
                </div>

                <div class="form-group">
                    <label for="description" class="control-label"><?php echo _l('ccx_leads_description'); ?></label>
                    <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address" class="control-label"><?php echo _l('ccx_leads_address'); ?></label>
                            <input type="text" id="address" name="address" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city" class="control-label"><?php echo _l('ccx_leads_city'); ?></label>
                            <input type="text" id="city" name="city" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="state" class="control-label"><?php echo _l('ccx_leads_state'); ?></label>
                            <input type="text" id="state" name="state" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="country" class="control-label"><?php echo _l('ccx_leads_country'); ?></label>
                            <select id="country" name="country" class="form-control"
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="zip" class="control-label"><?php echo _l('ccx_leads_zip'); ?></label>
                            <input type="text" id="zip" name="zip" class="form-control">
                        </div>
                    </div>
                </div>
                <!-- Add other fields as needed (source, status, assigned) -->
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