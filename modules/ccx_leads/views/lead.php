<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="customer-profile-group-heading">
                    <?php echo $title; ?>
                </h4>
            </div>

            <!-- Lead Information Form -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string(), ['id' => 'ccx-lead-form']); ?>

                        <div class="form-group">
                            <label for="name" class="control-label"><?php echo _l('ccx_leads_name'); ?></label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="<?php echo (isset($lead) ? $lead->name : ''); ?>" required>
                        </div>

                        <div class="form-group" id="phone_group">
                            <label for="phonenumber"
                                class="control-label"><?php echo _l('ccx_leads_phonenumber'); ?></label>
                            <input type="text" id="phonenumber" name="phonenumber" class="form-control"
                                value="<?php echo (isset($lead) ? $lead->phonenumber : ''); ?>">
                            <span id="phone_counter" class="text-muted small pull-right" style="display:none;"></span>
                            <span id="phone_duplicate_error" class="text-danger small" style="display:none;"></span>
                        </div>

                        <div class="form-group">
                            <label for="email" class="control-label"><?php echo _l('ccx_leads_email'); ?></label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="<?php echo (isset($lead) ? $lead->email : ''); ?>">
                        </div>

                        <!-- Add other fields as needed (source, status, assigned) -->
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_select('status', $statuses ?? [], ['id', 'name'], 'status', (isset($lead) ? $lead->status : '')); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_select('assigned', $staff ?? [], ['staffid', ['firstname', 'lastname']], 'assigned', (isset($lead) ? $lead->assigned : '')); ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info pull-right">
                            <?php echo _l('ccx_leads_submit'); ?>
                        </button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>

            <!-- Call Logs Section -->
            <?php if (isset($lead)) { ?>
                <div class="col-md-6">
                    <div class="panel_s">
                        <div class="panel-heading">
                            <span class="font-bold"><?php echo _l('ccx_leads_call_logs'); ?></span>
                            <a href="#" onclick="new_call_log(); return false;"
                                class="btn btn-info btn-xs pull-right"><?php echo _l('ccx_leads_new_call_log'); ?></a>
                        </div>
                        <div class="panel-body">
                            <div id="call_logs_container">
                                <?php if (empty($call_logs)) { ?>
                                    <p class="text-muted"><?php echo _l('ccx_leads_no_call_logs_found'); ?></p>
                                <?php } else { ?>
                                    <ul class="list-group">
                                        <?php foreach ($call_logs as $log) { ?>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span class="pull-left font-bold">
                                                            <?php echo $log['firstname'] . ' ' . $log['lastname']; ?>
                                                        </span>
                                                        <span class="pull-right text-muted small">
                                                            <?php echo _dt($log['date']); ?>
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mtop10">
                                                        <p>
                                                            <?php echo $log['content']; ?>
                                                        </p>
                                                        <hr class="hr-10" />
                                                        <span class="label label-default">
                                                            <?php echo _l('ccx_leads_duration'); ?>:
                                                            <?php echo $log['duration']; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>

<!-- Call Log Modal -->
<div class="modal fade" id="call_log_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('ccx_leads_new_call_log'); ?></h4>
            </div>
            <?php echo form_open(admin_url('ccx_leads/save_call_log'), ['id' => 'call-log-form']); ?>
            <div class="modal-body">
                <input type="hidden" name="lead_id" value="<?php echo (isset($lead) ? $lead->id : ''); ?>">
                <div class="form-group">
                    <label for="content" class="control-label"><?php echo _l('ccx_leads_call_summary'); ?></label>
                    <textarea id="content" name="content" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="duration" class="control-label"><?php echo _l('ccx_leads_duration'); ?></label>
                    <input type="text" id="duration" name="duration" class="form-control" placeholder="e.g. 5 mins">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('ccx_leads_close'); ?>
                </button>
                <button type="submit" class="btn btn-info">
                    <?php echo _l('ccx_leads_save'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<style>
    .iti {
        width: 100%;
    }
</style>
<script>
    function new_call_log() {
        $('#call_log_modal').modal('show');
    }

    $(function () {
        var input = document.querySelector("#phonenumber");
        var lead_id = '<?php echo isset($lead) ? $lead->id : ""; ?>';

        if (input) {
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
                    $.post(admin_url + 'ccx_leads/check_duplicate_phone', { phone: val, id: lead_id }, function (response) {
                        response = JSON.parse(response);
                        if (response.exists) {
                            $('#phone_group').addClass('has-error');
                            $('#phone_duplicate_error').text(response.message).show();
                            $('button[type="submit"]').prop('disabled', true);
                        }
                    });
                }
            });

            // Trigger initial counter set
            setTimeout(function () { input.dispatchEvent(new Event("countrychange")); }, 1000);
        }
        appValidateForm($('#ccx-lead-form'), {
            name: 'required'
        });

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

        appValidateForm($('#call-log-form'), {
            content: 'required'
        }, function (form) {
            $.post(form.action, $(form).serialize(), function (response) {
                response = JSON.parse(response);
                if (response.success) {
                    $('#call_log_modal').modal('hide');
                    alert_float('success', response.message);
                    location.reload(); // Simple reload to show new log
                }
            });
            return false;
        });
    });
</script>
</body>

</html>