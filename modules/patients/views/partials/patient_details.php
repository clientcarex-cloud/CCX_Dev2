<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Top Row: Mobile, Name, Age -->
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label for="mobile_number" class="control-label">
                <small class="req text-danger">*</small>
                Mobile Number
            </label>
            <div class="input-group">
                <span class="input-group-addon">+91</span>
                <input type="tel" class="form-control" name="mobile_number"
                    id="phonenumber_input"
                    value="<?php echo (isset($patient) ? $patient->phonenumber : ''); ?>"
                    maxlength="10" minlength="10" pattern="[0-9]{10}" inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                    autocomplete="nop" <?php echo (isset($locked_fields) && in_array('mobile_number', $locked_fields)) ? 'readonly style="background-color:#eee;"' : ''; ?> required autofocus>
            </div>
            <div id="patient_suggestions" class="list-group"
                style="display:none; position: absolute; z-index: 1000; width: 90%; max-height: 200px; overflow-y: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            </div>
            <small class="text-muted help-block"
                style="font-size: 11px; margin-bottom: 0px; margin-top: 2px;"
                id="mobile_counter">0/10</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">
                <small class="req text-danger">*</small>
                Full Name & Gender
            </label>
            <div class="input-group">
                <span class="input-group-addon" style="padding:0px; border:none; width: 70px;">
                    <select name="title_id" class="selectpicker" data-width="100%">
                        <?php foreach ($name_titles as $t) { ?>
                            <option value="<?php echo $t['id']; ?>" <?php echo (isset($t['is_default']) && $t['is_default'] == 1) ? 'selected' : ''; ?>>
                                <?php echo $t['name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </span>
                <input type="text" class="form-control" name="full_name"
                    value="<?php echo (isset($patient) ? $patient->full_name : ''); ?>"
                    placeholder="Full Name" maxlength="50" required <?php echo (isset($locked_fields) && in_array('patient_name', $locked_fields)) ? 'readonly style="background-color:#eee;"' : ''; ?>>
                <span class="input-group-addon" style="padding:0px; border:none; width: 90px;">
                    <select name="gender" class="selectpicker" data-width="100%">
                        <option value="Male" <?php echo (isset($patient) && $patient->gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (isset($patient) && $patient->gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo (isset($patient) && $patient->gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <?php $req_age = (get_option('patients_req_age') == '1'); ?>
            <label for="age" class="control-label">
                <?php if ($req_age) { ?><small class="req text-danger">*</small><?php } ?>
                <?php echo _l('patient_age'); ?> / DOB
            </label>
            <div class="input-group">
                <input type="text" class="form-control" name="age" id="age_input" value="<?php
                if (isset($patient)) {
                    if (isset($patient->age_unit) && $patient->age_unit == 'DOB' && !empty($patient->dob) && $patient->dob != '0000-00-00') {
                        echo date('d-m-Y', strtotime($patient->dob));
                    } else {
                        // Fix: If age is valid, show it.
                        if (isset($patient->age) && $patient->age !== '' && $patient->age != 0) {
                            echo $patient->age;
                        } elseif (!empty($patient->dob) && $patient->dob != '0000-00-00') {
                            // Fallback: Calculate Age from DOB
                            try {
                                $dob_date = new DateTime($patient->dob);
                                $now = new DateTime();
                                $diff = $now->diff($dob_date);
                                echo $diff->y;
                            } catch (Exception $e) {
                                echo '';
                            }
                        } else {
                            echo isset($patient->age) ? $patient->age : '';
                        }
                    }
                }
                ?>" placeholder="Age" <?php echo $req_age ? 'required' : ''; ?>
                    autocomplete="off" <?php echo (isset($locked_fields) && in_array('age', $locked_fields)) ? 'readonly style="background-color:#eee;"' : ''; ?>>
                <span class="input-group-addon" style="padding:0px; border:none; width: 70px;">
                    <select name="age_unit" id="age_unit" class="selectpicker"
                        data-width="100%">
                        <option value="Years" <?php echo (isset($patient) && (empty($patient->age_unit) || $patient->age_unit == 'Years')) || (!isset($patient)) ? 'selected' : ''; ?>>Years</option>
                        <option value="Months" <?php echo (isset($patient) && isset($patient->age_unit) && $patient->age_unit == 'Months') ? 'selected' : ''; ?>>Months</option>
                        <option value="Days" <?php echo (isset($patient) && isset($patient->age_unit) && $patient->age_unit == 'Days') ? 'selected' : ''; ?>>Days</option>
                        <option value="DOB" <?php echo (isset($patient) && isset($patient->age_unit) && $patient->age_unit == 'DOB') ? 'selected' : ''; ?>>DOB</option>
                    </select>
                    <?php if (isset($locked_fields) && in_array('age', $locked_fields)) { ?>
                        <input type="hidden" name="age_unit"
                            value="<?php echo (isset($patient) && isset($patient->age_unit)) ? $patient->age_unit : 'Years'; ?>">
                        <script>
                            $(function () {
                                $('select[name="age_unit"]').prop('disabled', true).selectpicker('refresh');
                            });
                        </script>
                    <?php } ?>
                </span>
            </div>
            <div id="age_calc_text" class="text-muted"
                style="font-size:11px; margin-top:2px; min-height:15px;"></div>
        </div>
    </div>

    <!-- Rows merged for better layout -->

    <?php
    // BUFFER FIELDS
    $rendered_fields = [];
    $required_status = [];

    // -2. UID No
    ob_start();
    ?>
    <div class="col-md-2">
        <div class="form-group">
            <?php $req_uid = (get_option('patients_req_uid') == '1'); ?>
            <label for="uid_no" class="control-label">
                <?php if ($req_uid) { ?><small class="req text-danger">*</small><?php } ?>
                UID No
            </label>
            <input type="text" class="form-control" name="uid_no" id="uid_no"
                value="<?php echo (isset($patient) && isset($patient->uid_no) ? $patient->uid_no : ''); ?>"
                maxlength="12" minlength="12" pattern="\d{12}"
                title="Please enter exactly 12 digits" inputmode="numeric" <?php echo $req_uid ? 'required' : ''; ?> <?php echo (isset($locked_fields) && in_array('uid_no', $locked_fields)) ? 'readonly style="background-color:#eee;"' : ''; ?>>
            <small class="text-muted help-block"
                style="font-size: 11px; margin-bottom: 0px; margin-top: 2px;"
                id="uid_no_counter">0/12</small>
        </div>
    </div>
    <?php
    $rendered_fields['uid_no'] = ob_get_clean();
    $required_status['uid_no'] = $req_uid;

    // -1. Primary Doctor
    ob_start();
    ?>
    <div class="col-md-2">
        <div class="form-group">
            <?php $req_primary = (get_option('patients_req_primary_doctor') == '1'); ?>
            <label for="primary_doctor_id" class="control-label">
                <?php if ($req_primary) { ?><small class="req text-danger">*</small><?php } ?>
                Primary Doctor
            </label>
            <select name="primary_doctor_id" class="selectpicker" data-width="100%"
                data-live-search="true" data-none-selected-text="Doctor" <?php echo $req_primary ? 'required' : ''; ?> <?php echo (isset($locked_fields) && in_array('primary_doctor', $locked_fields)) ? 'disabled' : ''; ?>>
                <option value=""></option>
                <?php foreach ($doctors as $d) {
                    $selected = '';
                    if (isset($visit_data) && isset($visit_data->primary_doctor_id)) {
                        if ($visit_data->primary_doctor_id == $d['staffid'])
                            $selected = 'selected';
                    } elseif (isset($patient) && isset($patient->last_primary_doctor_id)) {
                        if ($patient->last_primary_doctor_id == $d['staffid'])
                            $selected = 'selected';
                    }
                    ?>
                    <option value="<?php echo $d['staffid']; ?>" <?php echo $selected; ?>>
                        <?php echo $d['firstname'] . ' ' . $d['lastname']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php
    $rendered_fields['primary_doctor'] = ob_get_clean();
    $required_status['primary_doctor'] = $req_primary;

    // 0. Referral Doctor
    ob_start();
    ?>
    <div class="col-md-2">
        <div class="form-group">
            <?php $req_doctor = (get_option('patients_req_referral_doctor') == '1'); ?>
            <label for="referral_doctor_id" class="control-label">
                <?php if ($req_doctor) { ?><small class="req text-danger">*</small><?php } ?>
                Referral Doctor
            </label>
            <div class="input-group">
                <select name="referral_doctor_id" class="selectpicker" data-width="100%"
                    data-live-search="true" data-none-selected-text="Doctor" <?php echo $req_doctor ? 'required' : ''; ?> <?php echo (isset($locked_fields) && in_array('referral_doctor', $locked_fields)) ? 'disabled' : ''; ?>>
                    <option value=""></option>
                    <?php foreach ($doctors as $d) { ?>
                        <option value="<?php echo $d['staffid']; ?>" data-subtext="<?php echo isset($d['phonenumber']) ? $d['phonenumber'] : ''; ?>" <?php echo (isset($visit_data) && $visit_data->referral_doctor_id == $d['staffid']) ? 'selected' : ''; ?>>
                            <?php echo $d['firstname'] . ' ' . $d['lastname']; ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" data-toggle="modal"
                        data-target="#referral_doctor_modal"><i class="fa fa-plus"></i></button>
                </span>
            </div>
        </div>
    </div>
    <?php
    $rendered_fields['doctor'] = ob_get_clean();
    $required_status['doctor'] = $req_doctor;

    // 1. Attender
    ob_start();
    ?>
    <div class="col-md-3">
        <div class="form-group">
            <?php $req_attender = (get_option('patients_req_attender_name') == '1'); ?>
            <label class="control-label">
                <?php if ($req_attender) { ?><small class="req text-danger">*</small><?php } ?>
                Attender Title & Name
            </label>
            <div class="input-group">
                <span class="input-group-addon" style="padding:0px; border:none; width: 80px;">
                    <select name="attender_title_id" class="selectpicker" data-width="100%"
                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                        <?php echo (isset($locked_fields) && in_array('attender', $locked_fields)) ? 'disabled' : ''; ?>>
                        <option value=""></option>
                        <?php foreach ($care_titles as $t) { ?>
                            <option value="<?php echo $t['id']; ?>" <?php echo (isset($patient) && $patient->attender_title_id == $t['id']) || (!isset($patient) && $t['name'] == 'W/O') ? 'selected' : ''; ?>><?php echo $t['name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </span>
                <input type="text" class="form-control" name="attender_name"
                    value="<?php echo (isset($patient) ? $patient->attender_name : ''); ?>"
                    placeholder="Attender Name" <?php echo $req_attender ? 'required' : ''; ?>
                    <?php echo (isset($locked_fields) && in_array('attender', $locked_fields)) ? 'readonly style="background-color:#eee;"' : ''; ?>>
            </div>
        </div>
    </div>
    <?php
    $rendered_fields['attender'] = ob_get_clean();
    $required_status['attender'] = $req_attender;

    // 2. Email
    ob_start();
    ?>
    <div class="col-md-2">
        <div class="form-group">
            <?php $req_email = (get_option('patients_req_email') == '1'); ?>
            <label for="email" class="control-label">
                <?php if ($req_email) { ?><small class="req text-danger">*</small><?php } ?>
                <?php echo _l('client_email'); ?>
            </label>
            <input type="email" class="form-control" name="email"
                value="<?php echo (isset($patient) ? $patient->email : ''); ?>" <?php echo $req_email ? 'required' : ''; ?> <?php echo (isset($locked_fields) && in_array('email', $locked_fields)) ? 'readonly style="background-color:#eee;"' : ''; ?>>
        </div>
    </div>
    <?php
    $rendered_fields['email'] = ob_get_clean();
    $required_status['email'] = $req_email;

    // 3. Address
    ob_start();
    ?>
    <div class="col-md-3">
        <div class="form-group">
            <?php $req_address = (get_option('patients_req_address') == '1'); ?>
            <label for="address" class="control-label">
                <?php if ($req_address) { ?><small class="req text-danger">*</small><?php } ?>
                <?php echo _l('client_address'); ?>
            </label>
            <textarea name="address" class="form-control" rows="1" <?php echo $req_address ? 'required' : ''; ?> <?php echo (isset($locked_fields) && in_array('address', $locked_fields)) ? 'readonly style="background-color:#eee;"' : ''; ?>><?php echo (isset($patient) ? $patient->address : ''); ?></textarea>
        </div>
    </div>
    <?php
    $rendered_fields['address'] = ob_get_clean();
    $required_status['address'] = $req_address;

    // 4. Lab
    ob_start();
    ?>
    <div class="col-md-2">
        <div class="form-group">
            <?php $req_lab = (get_option('patients_req_referral_lab') == '1'); ?>
            <label for="referral_lab_id" class="control-label">
                <?php if ($req_lab) { ?><small class="req text-danger">*</small><?php } ?>
                Referral Lab
            </label>
            <select name="referral_lab_id" class="selectpicker" data-width="100%"
                data-live-search="true" data-none-selected-text="Select Lab" <?php echo $req_lab ? 'required' : ''; ?> <?php echo (isset($locked_fields) && in_array('referral_lab', $locked_fields)) ? 'disabled' : ''; ?>>
                <option value=""></option>
                <?php foreach ($referral_labs as $lab) { ?>
                    <option value="<?php echo $lab['staffid']; ?>" <?php echo (isset($visit_data) && $visit_data->referral_lab_id == $lab['staffid']) ? 'selected' : ''; ?>>
                        <?php echo $lab['firstname'] . ' ' . $lab['lastname']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php
    $rendered_fields['lab'] = ob_get_clean();
    $required_status['lab'] = $req_lab;

    // 5. Company
    ob_start();
    ?>
    <div class="col-md-2">
        <div class="form-group">
            <?php $req_company = (get_option('patients_req_company') == '1'); ?>
            <label for="company_id" class="control-label">
                <?php if ($req_company) { ?><small class="req text-danger">*</small><?php } ?>
                Company
            </label>
            <select name="company_id" class="selectpicker" data-width="100%"
                data-live-search="true" data-none-selected-text="Select Company" <?php echo $req_company ? 'required' : ''; ?> <?php echo (isset($locked_fields) && in_array('company', $locked_fields)) ? 'disabled' : ''; ?>>
                <option value=""></option>
                <?php foreach ($companies as $comp) { ?>
                    <option value="<?php echo $comp['staffid']; ?>" <?php echo (isset($visit_data) && $visit_data->company_id == $comp['staffid']) ? 'selected' : ''; ?>>
                        <?php echo $comp['firstname'] . ' ' . $comp['lastname']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php
    $rendered_fields['company'] = ob_get_clean();
    $required_status['company'] = $req_company;

    // 6. Prescription
    ob_start();
    ?>
    <div class="col-md-2">
        <div class="form-group">
            <?php $req_rx = (get_option('patients_req_prescription') == '1'); ?>
            <label for="prescription" class="control-label">
                <?php if ($req_rx) { ?><small class="req text-danger">*</small><?php } ?>
                Attach Prescription
            </label>
            <input type="file" name="prescription" class="form-control" <?php echo ($req_rx && (!isset($patient) || !$patient->prescription_file)) ? 'required' : ''; ?> <?php echo (isset($locked_fields) && in_array('prescription', $locked_fields)) ? 'disabled' : ''; ?>>
            <?php if (isset($patient) && $patient->prescription_file) { ?>
                <a href="<?php echo base_url('uploads/patients_uploads/' . $patient->prescription_file); ?>"
                    target="_blank">View File</a>
            <?php } ?>
        </div>
    </div>
    <?php
    $rendered_fields['prescription'] = ob_get_clean();
    $required_status['prescription'] = $req_rx;
    ?>

    <!-- Mandatory Dynamic Fields Rendered Here -->
    <?php
    foreach ($rendered_fields as $key => $html) {
        if ($required_status[$key]) {
            echo $html;
        }
    }
    ?>

    <div class="row">
        <div class="col-md-12">
            <a href="#" id="optional_fields_toggle"><?php echo $toggle_text; ?></a>
            <div id="optional_fields"
                style="<?php echo $show_optional ? 'display:block;' : 'display:none;'; ?> margin-top:15px;">
                <div class="row">
                    <?php
                    // RENDER OPTIONAL FIELDS IN HIDDEN DIV
                    foreach ($rendered_fields as $key => $html) {
                        if (!$required_status[$key]) {
                            echo $html;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <hr />

<script>
    if(typeof doctor_service_items === 'undefined') {
        var doctor_service_items = <?php echo isset($doctor_service_items) ? json_encode($doctor_service_items) : '{}'; ?>;
    }
</script>
