<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string(), ['id' => 'appointment-form']); ?>

                        <div class="form-group relative">
                            <label for="mobile_number" class="control-label"><?php echo _l('mobile_number'); ?> <small
                                    class="text-danger">*</small> <span id="mobile_count" class="pull-right text-muted"
                                    style="font-size: 85%;">0/10</span></label>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control"
                                value="<?php echo isset($patient_mobile) ? $patient_mobile : ''; ?>" required
                                autocomplete="off" maxlength="10">
                            <div id="patient_suggestions" class="list-group"
                                style="display:none; position: absolute; z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            </div>
                        </div>

                        <div id="patient_info_display" style="display:none; margin-bottom: 15px;"
                            class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Name:</strong> <span id="found_patient_name"></span> <br>
                                    <strong>MR No:</strong> <span id="found_patient_mr_no"></span> <br>
                                    <strong>Mobile:</strong> <span id="found_patient_mobile"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Age:</strong> <span id="found_patient_age"></span> <br>
                                    <strong>Registered:</strong> <span id="found_patient_reg_date"></span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="patient_id" id="patient_id"
                            value="<?php echo isset($appointment) ? $appointment->patient_id : ''; ?>">

                        <input type="hidden" name="appointment_id" id="appointment_id"
                            value="<?php echo isset($appointment) ? $appointment->id : ''; ?>">

                        <div id="new_patient_fields" style="display:none;">
                            <div class="form-group">
                                <label for="new_patient_name" class="control-label"><?php echo _l('patient_name'); ?>
                                    <small class="text-danger">*</small></label>
                                <div class="input-group merged-input-group">
                                    <div class="input-group-btn">
                                        <select name="new_patient_title" id="new_patient_title" class="selectpicker"
                                            data-width="80px">
                                            <option value=""></option>
                                            <?php foreach (['Mr.', 'Mrs.', 'Ms.', 'Miss.', 'Master', 'Baby', 'B/O.', 'Dr.'] as $t) {
                                                $selected = ($t == 'Mr.') ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $t; ?>" <?php echo $selected; ?>><?php echo $t; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <input type="text" name="new_patient_name" id="new_patient_name"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="new_patient_age" class="control-label"><?php echo _l('age'); ?>
                                            <small class="text-danger">*</small></label>
                                        <input type="number" name="new_patient_age" id="new_patient_age"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="new_patient_gender"
                                            class="control-label"><?php echo _l('gender'); ?></label>
                                        <select name="new_patient_gender" id="new_patient_gender" class="selectpicker"
                                            data-width="100%">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="doctor_id" class="control-label"><?php echo _l('doctor'); ?></label>
                            <select name="doctor_id" id="doctor_id" class="selectpicker" data-width="100%"
                                data-live-search="true" required>
                                <option value=""></option>
                                <?php foreach ($doctors as $doctor) { ?>
                                    <option value="<?php echo $doctor['staffid']; ?>" <?php if (isset($appointment) && $appointment->doctor_id == $doctor['staffid'])
                                           echo 'selected'; ?> data-default-item="<?php echo $doctor['default_service_item']; ?>"
                                        data-anytime="<?php echo $doctor['anytime_appointment']; ?>">
                                        <?php echo $doctor['firstname'] . ' ' . $doctor['lastname']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="appointment_date" class="control-label"><?php echo _l('date'); ?></label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control"
                                value="<?php echo isset($appointment) ? $appointment->appointment_date : date('Y-m-d'); ?>"
                                min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group" id="slots_wrapper" style="display:none;">
                            <label class="control-label"><?php echo _l('available_slots'); ?></label>
                            <div id="slots_container" class="row">
                                <!-- Slots will be loaded here -->
                            </div>
                            <input type="hidden" name="start_time" id="start_time" required>
                            <input type="hidden" name="end_time" id="end_time" required>
                        </div>

                        <!-- Fallback for edit mode or if JS fails, though we rely on JS for slots -->
                        <?php if (isset($appointment)) { ?>
                            <div class="form-group">
                                <label class="control-label"><?php echo _l('current_slot'); ?></label>
                                <p><?php echo date('h:i A', strtotime($appointment->start_time)) . ' - ' . date('h:i A', strtotime($appointment->end_time)); ?>
                                </p>
                            </div>
                        <?php } ?>

                        <div class="form-group">
                            <label for="status" class="control-label"><?php echo _l('status'); ?></label>
                            <select name="status" id="status" class="selectpicker" data-width="100%">
                                <option value="pending" <?php if (isset($appointment) && $appointment->status == 'pending')
                                    echo 'selected'; ?>>Pending</option>
                                <option value="confirmed" <?php if (isset($appointment) && $appointment->status == 'confirmed')
                                    echo 'selected'; ?>>Confirmed</option>
                                <option value="cancelled" <?php if (isset($appointment) && $appointment->status == 'cancelled')
                                    echo 'selected'; ?>>Cancelled</option>
                                <option value="completed" <?php if (isset($appointment) && $appointment->status == 'completed')
                                    echo 'selected'; ?>>Completed</option>
                            </select>
                        </div>

                        <input type="hidden" name="guest_id" id="guest_id"
                            value="<?php echo isset($appointment) ? $appointment->guest_id : ''; ?>">

                        <div class="form-group">
                            <label for="appointment_type"
                                class="control-label"><?php echo _l('appointment_type'); ?></label>
                            <select name="appointment_type" id="appointment_type" class="selectpicker" data-width="100%"
                                <?php if (isset($appointment))
                                    echo 'disabled'; ?>>
                                <option value="Unpaid" <?php if (isset($appointment) && $appointment->appointment_type == 'Unpaid')
                                    echo 'selected'; ?>><?php echo _l('appointment_type_Unpaid'); ?></option>
                                <option value="Paid" <?php if (isset($appointment) && $appointment->appointment_type == 'Paid')
                                    echo 'selected'; ?>><?php echo _l('appointment_type_Paid'); ?></option>
                                <option value="Free Camp" <?php if (isset($appointment) && $appointment->appointment_type == 'Free Camp')
                                    echo 'selected'; ?>><?php echo _l('appointment_type_Free Camp'); ?></option>
                            </select>
                            <?php if (isset($appointment)) { ?>
                                <input type="hidden" name="appointment_type"
                                    value="<?php echo $appointment->appointment_type; ?>">
                            <?php } ?>
                        </div>

                        <div id="invoice_fields" style="display:none;">
                            <input type="hidden" name="create_invoice" value="1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="service_name"
                                            class="control-label"><?php echo _l('service_name'); ?> <small
                                                class="text-danger">*</small></label>
                                        <select name="service_name" id="service_name" class="selectpicker"
                                            data-width="100%" data-live-search="true">
                                            <option value=""><?php echo _l('select_item'); ?></option>
                                            <?php foreach ($items as $item) {
                                                $item_id = isset($item['id']) ? $item['id'] : (isset($item['itemid']) ? $item['itemid'] : '');
                                                if (empty($item_id))
                                                    continue;
                                                ?>
                                                <option value="<?php echo $item['description']; ?>"
                                                    data-rate="<?php echo $item['rate']; ?>"
                                                    data-id="<?php echo $item_id; ?>">
                                                    <?php echo $item['description']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <!-- Hidden input to submit value when disabled -->
                                        <input type="hidden" name="service_name" id="hidden_service_name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount" class="control-label"><?php echo _l('total_amount'); ?>
                                            <small class="text-danger">*</small></label>
                                        <input type="number" name="amount" id="amount" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="received_amount"
                                            class="control-label"><?php echo _l('received_amount'); ?> <small
                                                class="text-danger">*</small></label>
                                        <input type="number" name="received_amount" id="received_amount"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_mode"
                                            class="control-label"><?php echo _l('payment_mode'); ?> <small
                                                class="text-danger">*</small></label>
                                        <select name="payment_mode" id="payment_mode" class="selectpicker"
                                            data-width="100%">
                                            <option value=""><?php echo _l('select_payment_mode'); ?></option>
                                            <?php
                                            $this->load->model('payment_modes_model');
                                            $modes = $this->payment_modes_model->get('', [], false);
                                            foreach ($modes as $mode) {
                                                echo '<option value="' . $mode['id'] . '">' . $mode['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes" class="control-label"><?php echo _l('notes'); ?></label>
                            <textarea name="notes" id="notes" class="form-control"
                                rows="4"><?php echo isset($appointment) ? $appointment->notes : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        var doctorSelect = $('#doctor_id');
        var dateInput = $('#appointment_date');
        var slotsWrapper = $('#slots_wrapper');
        var slotsContainer = $('#slots_container');
        var startTimeInput = $('#start_time');
        var endTimeInput = $('#end_time');

        var mobileInput = $('#mobile_number');
        // Init count
        if (mobileInput.val()) {
            $('#mobile_count').text(mobileInput.val().length + '/10');
        }
        var patientSuggestions = $('#patient_suggestions');
        var patientIdInput = $('#patient_id');
        var guestIdInput = $('#guest_id');
        var patientInfoDisplay = $('#patient_info_display');

        var foundPatientName = $('#found_patient_name');
        var foundPatientMrNo = $('#found_patient_mr_no');
        var foundPatientMobile = $('#found_patient_mobile');
        var foundPatientAge = $('#found_patient_age');
        var foundPatientRegDate = $('#found_patient_reg_date');

        var newPatientFields = $('#new_patient_fields');
        var newPatientName = $('#new_patient_name');
        var newPatientAge = $('#new_patient_age');
        var newPatientTitle = $('#new_patient_title');
        var newPatientGender = $('#new_patient_gender');

        var serviceNameSelect = $('#service_name');
        var hiddenServiceName = $('#hidden_service_name');

        // Search Patient Logic
        var searchTimeout;
        mobileInput.on('input', function () {
            // Enforce numeric only and 10 digit limit
            var val = $(this).val().replace(/[^0-9]/g, '');
            if (val.length > 10) val = val.substring(0, 10);
            $(this).val(val);

            // Update Count
            $('#mobile_count').text(val.length + '/10');

            var mobile = val;



            // Logic: 
            // 1. If length < 10, hide new patient fields (strict 10-digit requirement for new patient creation).
            // 2. If length >= 5, SEARCH.
            // 3. Search Results:
            //    - Found: Show suggestions.
            //    - Not Found:
            //         - If length == 10: AUTO SHOW new patient fields.
            //         - If length < 10: Show "No patient found" in dropdown (or keep searching).

            if (mobile.length < 10) {
                newPatientFields.hide();
                // Also clear selected patient info if user is typing?
                // If the user modifies the number, the previously selected patient is no longer valid for this number.
                patientIdInput.val('');
                guestIdInput.val('');
                patientInfoDisplay.hide();
            }

            if (mobile.length >= 5) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    $.post(admin_url + 'appointments/search_patient', { mobile: mobile }, function (response) {
                        var patients = JSON.parse(response);

                        if (patients.length > 0) {
                            // Patients Found
                            patientSuggestions.empty().show();
                            // Ensure new patient fields are hidden if we found someone (and user hasn't clicked New yet)
                            newPatientFields.hide();

                            $.each(patients, function (index, patient) {
                                var item = $('<a href="#" class="list-group-item list-group-item-action"></a>');
                                item.html('<strong>' + patient.name + '</strong> (' + patient.phone + ')<br><small>MR: ' + (patient.mr_no || 'N/A') + '</small>');
                                item.on('click', function (e) {
                                    e.preventDefault();
                                    selectPatient(patient);
                                });
                                patientSuggestions.append(item);
                            });

                            // Add "New Patient" option manually
                            var newItem = $('<a href="#" class="list-group-item list-group-item-action list-group-item-warning"></a>');
                            newItem.html('<strong>New Patient</strong><br><small>Click to create new</small>');
                            newItem.on('click', function (e) {
                                e.preventDefault();
                                showNewPatientFields();
                            });
                            patientSuggestions.append(newItem);

                        } else {
                            // No results found
                            if (mobile.length == 10) {
                                // AUTO SHOW New Patient Fields
                                patientSuggestions.hide();
                                showNewPatientFields();
                            } else {
                                // Show "No patient found" option in dropdown
                                patientSuggestions.empty().show();
                                var item = $('<a href="#" class="list-group-item list-group-item-action list-group-item-warning"></a>');
                                item.html('<strong>No patient found</strong><br><small>Keep typing...</small>');
                                // item.on('click', ...); // Optional: Allow click to force new patient? 
                                // User said "not let user to click" for 10 digits. For < 10, strictly speaking, we don't want to create new patient with invalid mobile.
                                // So just informational.
                                patientSuggestions.append(item);
                            }
                        }
                    });
                }, 300);
            } else {
                patientSuggestions.hide();
            }
        });

        // Hide suggestions when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#mobile_number').length && !$(e.target).closest('#patient_suggestions').length) {
                patientSuggestions.hide();
            }
        });

        function selectPatient(patient) {
            mobileInput.val(patient.phone);
            patientSuggestions.hide();

            if (patient.type == 'client') {
                patientIdInput.val(patient.userid);
                guestIdInput.val('');
                foundPatientMrNo.text(patient.mr_no);
            } else {
                patientIdInput.val('');
                guestIdInput.val(patient.userid); // userid mapped to id for guest
                foundPatientMrNo.text('Guest');
            }

            foundPatientName.text(patient.name);
            foundPatientMobile.text(patient.phone);
            foundPatientAge.text(patient.age || 'N/A');
            foundPatientRegDate.text(patient.date_registered || 'N/A');

            patientInfoDisplay.show();
            newPatientFields.hide();

            // Clear new patient fields
            newPatientName.val('');
            newPatientAge.val('');
            newPatientName.prop('required', false);
            newPatientAge.prop('required', false);
        }

        function showNewPatientFields() {
            patientSuggestions.hide();
            patientIdInput.val('');
            guestIdInput.val('');
            patientInfoDisplay.hide();
            newPatientFields.show();

            // Require new patient fields
            newPatientName.prop('required', true);
            newPatientAge.prop('required', true);
        }

        // Auto-Gender based on Title
        newPatientTitle.on('change', function () {
            var title = $(this).val();
            var gender = '';
            if (['Mr.', 'Master'].includes(title)) {
                gender = 'male';
            } else if (['Mrs.', 'Ms.', 'Miss.'].includes(title)) {
                gender = 'female';
            }

            if (gender) {
                newPatientGender.selectpicker('val', gender);
            }
        });

        // Auto-Title based on Gender
        newPatientGender.on('change', function () {
            var gender = $(this).val();
            var title = newPatientTitle.val(); // Current title

            // Only change title if it doesn't match the gender or if it's empty
            // But user might want to switch from Mr to Mrs manually.
            // If user selects Female, we should probably switch to Ms. if currently Mr.

            if (gender == 'female') {
                if (!['Mrs.', 'Ms.', 'Miss.'].includes(title)) {
                    newPatientTitle.selectpicker('val', 'Ms.');
                }
            } else if (gender == 'male') {
                if (!['Mr.', 'Master'].includes(title)) {
                    newPatientTitle.selectpicker('val', 'Mr.');
                }
            }
        });

        function checkDefaultServiceItem() {
            var type = $('#appointment_type').val();
            var doctorOption = doctorSelect.find('option:selected');
            var defaultItemId = doctorOption.data('default-item');

            if (type == 'Paid' && defaultItemId) {
                // Find option with this ID (we need to match by ID now, but value is description)
                // Wait, the value of service_name is description. We need to find the option with data-id matching defaultItemId
                var option = serviceNameSelect.find('option[data-id="' + defaultItemId + '"]');
                if (option.length) {
                    serviceNameSelect.selectpicker('val', option.val());
                    serviceNameSelect.prop('disabled', true);
                    serviceNameSelect.selectpicker('refresh');
                    hiddenServiceName.val(option.val());

                    // Trigger change to update amount
                    serviceNameSelect.trigger('change');
                }
            } else {
                serviceNameSelect.prop('disabled', false);
                serviceNameSelect.selectpicker('refresh');
                hiddenServiceName.val('');
            }
        }

        // Appointment Type Logic
        $('#appointment_type').on('change', function () {
            if ($(this).val() == 'Paid') {
                $('#invoice_fields').show();
                $('#service_name').prop('required', true);
                $('#amount').prop('required', true);
                // received_amount is required if payment mode is selected, but let's make it required for Paid generally to avoid confusion, or handle validation on submit.
                // Better: if Paid, amount is required. received_amount defaults to amount.
                $('#received_amount').prop('required', true);
                $('#payment_mode').prop('required', true);
                checkDefaultServiceItem();
            } else {
                $('#invoice_fields').hide();
                $('#service_name').prop('required', false);
                $('#amount').prop('required', false);
                $('#received_amount').prop('required', false);
                $('#payment_mode').prop('required', false);
            }
        });

        // Doctor Change Logic for Default Item
        doctorSelect.on('change', function () {
            fetchSlots();
            if ($('#appointment_type').val() == 'Paid') {
                checkDefaultServiceItem();
            }
            checkAnytimeAppointment();
        });

        function checkAnytimeAppointment() {
            var doctorOption = doctorSelect.find('option:selected');
            var isAnytime = doctorOption.data('anytime') == 1;

            if (isAnytime) {
                // Optional: Show a message or indicator
                if ($('#anytime_msg').length == 0) {
                    $('<div id="anytime_msg" class="alert alert-info" style="margin-top:10px;">' + "<?php echo _l('anytime_appointment_msg'); ?>" + '</div>').insertAfter(doctorSelect.closest('.form-group'));
                }
                $('#start_time').prop('required', false);
                $('#end_time').prop('required', false);
            } else {
                $('#anytime_msg').remove();
                $('#start_time').prop('required', true);
                $('#end_time').prop('required', true);
            }
        }

        // Service Item Change Logic
        $('#service_name').on('change', function () {
            var selectedOption = $(this).find('option:selected');
            var rate = selectedOption.data('rate');
            if (rate) {
                $('#amount').val(rate);
                $('#received_amount').val(rate); // Auto-fill received amount
            }
            // Update hidden input if not disabled (if disabled, it's handled in checkDefaultServiceItem)
            if (!$(this).prop('disabled')) {
                hiddenServiceName.val($(this).val());
            }
        });

        // Amount Change Logic - Sync received amount if it wasn't manually changed? 
        // Simplest: If amount changes, just update received amount if it equals the old amount? 
        // Or just let user handle it. But requirement says "user should not take extra amount".
        $('#amount').on('input', function () {
            var total = parseFloat($(this).val()) || 0;
            var received = parseFloat($('#received_amount').val()) || 0;
            if (received > total) {
                $('#received_amount').val(total);
            }
        });

        $('#received_amount').on('input', function () {
            var total = parseFloat($('#amount').val()) || 0;
            var received = parseFloat($(this).val()) || 0;
            if (received > total) {
                alert_float('warning', 'Received amount cannot be greater than Total Amount');
                $(this).val(total);
            }
        });

        // If editing, show patient info if available
        <?php if (isset($appointment) && isset($patient_name)) { ?>
            patientInfoDisplay.show();
            foundPatientName.text("<?php echo $patient_name; ?>");
            foundPatientMobile.text("<?php echo isset($patient_mobile) ? $patient_mobile : ''; ?>");
            foundPatientMrNo.text("<?php echo isset($patient_mr_no) ? $patient_mr_no : ''; ?>");
            foundPatientAge.text("<?php echo isset($patient_age) ? $patient_age : ''; ?>");
            foundPatientRegDate.text("<?php echo isset($patient_reg_date) ? $patient_reg_date : ''; ?>");
        <?php } ?>

        // Initial check for anytime appointment
        checkAnytimeAppointment();

        // Store current appointment ID if editing
        var currentAppointmentId = '<?php echo isset($appointment) ? $appointment->id : ''; ?>';
        var currentStartTime = '<?php echo isset($appointment) ? $appointment->start_time : ''; ?>';
        var currentEndTime = '<?php echo isset($appointment) ? $appointment->end_time : ''; ?>';

        if (currentAppointmentId) {
            fetchSlots(currentAppointmentId);
        }

        // Form Validation & Duplicate Check
        $('#appointment-form').on('submit', function (e) {
            e.preventDefault(); // Always prevent default first

            var doctorOption = doctorSelect.find('option:selected');
            var isAnytime = doctorOption.data('anytime') == 1;
            var startTime = $('#start_time').val();

            if (!isAnytime && !startTime) {
                alert("<?php echo _l('select_time_slot'); ?>");
                $('html, body').animate({
                    scrollTop: $("#slots_wrapper").offset().top - 100
                }, 500);
                return false;
            }

            // AJAX Duplicate Check
            var formData = $(this).serialize();
            var submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Checking...');

            $.post(admin_url + 'appointments/check_duplicate_ajax', formData, function (response) {
                var res = JSON.parse(response);
                if (res.exists) {
                    alert_float('warning', res.message);
                    submitBtn.prop('disabled', false).text('<?php echo _l('submit'); ?>');
                } else {
                    // Proceed with submission
                    // We need to unbind this handler or submit the form programmatically bypassing the handler
                    // But since we used preventDefault(), we can just use the native submit() which doesn't trigger jQuery submit handler?
                    // Actually, native submit() does NOT trigger jQuery submit handler.
                    document.getElementById('appointment-form').submit();
                }
            }).fail(function () {
                alert('Error checking for duplicates. Please try again.');
                submitBtn.prop('disabled', false).text('<?php echo _l('submit'); ?>');
            });
        });

        function fetchSlots(appointment_id = null) {
            var doctorId = doctorSelect.val();
            var date = dateInput.val();

            if (doctorId && date) {
                $.post(admin_url + 'appointments/get_slots', {
                    doctor_id: doctorId,
                    date: date,
                    appointment_id: currentAppointmentId
                }, function (response) {
                    var slots = JSON.parse(response);
                    slotsContainer.html('');
                    if (slots.length > 0) {
                        slotsWrapper.show();
                        $.each(slots, function (index, slot) {
                            var btnClass = 'btn-default';
                            var disabledAttr = '';
                            var style = '';

                            if (!slot.available) {
                                if (slot.past) {
                                    btnClass = 'btn-default'; // Keep default class but add style
                                    style = 'style="background-color: #eee; color: #999; border-color: #ccc;"';
                                } else {
                                    btnClass = 'btn-danger disabled';
                                    disabledAttr = 'disabled';
                                }
                            }

                            // Check if this is the currently selected slot (for edit)
                            if (currentAppointmentId && slot.start_time == currentStartTime && slot.end_time == currentEndTime) {
                                btnClass = 'btn-info'; // Highlight current slot
                                startTimeInput.val(slot.start_time);
                                endTimeInput.val(slot.end_time);
                            }

                            var html = '<div class="col-md-3" style="margin-bottom:10px;">';
                            html += '<button type="button" class="btn ' + btnClass + ' btn-block slot-btn" data-start="' + slot.start_time + '" data-end="' + slot.end_time + '" data-past="' + (slot.past ? 'true' : 'false') + '" ' + disabledAttr + ' ' + style + '>' + slot.time + '</button>';
                            html += '</div>';
                            slotsContainer.append(html);
                        });
                    } else {
                        slotsWrapper.hide();
                        // Only alert if user manually changed date/doctor, not on initial load if empty
                        // But here we want to know if no slots.
                        // alert('No slots available for this day.');
                        slotsContainer.html('<div class="col-md-12"><div class="alert alert-warning">' + "<?php echo _l('no_slots_available'); ?>" + '</div></div>');
                        slotsWrapper.show();
                    }
                });
            }
        }

        doctorSelect.on('change', fetchSlots);
        dateInput.on('change', fetchSlots);

        $(document).on('click', '.slot-btn', function () {
            if ($(this).data('past') == true) {
                alert_float('warning', 'This time slot has passed.');
                return;
            }
            if (!$(this).hasClass('disabled')) {
                $('.slot-btn').removeClass('btn-info').addClass('btn-default');
                // Re-apply past style if removing info class? No, if it was past it wouldn't have info class normally, unless we are editing a past appt?
                // If we are editing, current slot is selected. If it was past, it would be marked past?
                // Logic above: if current, set btn-info.
                // Resetting all to default might lose custom styles. 
                // Better: Remove btn-info, add btn-default.
                // But we need to preserve past styles for OTHER buttons? 
                // Using .css() call or just class? We used inline style.

                $(this).removeClass('btn-default').addClass('btn-info');
                startTimeInput.val($(this).data('start'));
                endTimeInput.val($(this).data('end'));
            }
        });

        // Initial fetch if editing
        <?php if (isset($appointment)) { ?>
            fetchSlots();
            // Trigger change to set up UI correctly based on initial type
            $('#appointment_type').trigger('change');
        <?php } ?>
    });
</script>
</body>

</html>