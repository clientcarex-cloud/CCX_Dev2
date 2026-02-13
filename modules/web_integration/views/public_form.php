<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $form->name; ?>
    </title>
    <!-- Use Perfex default fonts/styles equivalent or just simple bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo base_url('modules/web_integration/assets/css/public_form.css'); ?>" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <?php if ($this->input->get('success')) {
                    $msg = $this->session->flashdata('success_message') ? $this->session->flashdata('success_message') : (!empty($form->success_message) ? $form->success_message : 'Form submitted successfully!');
                    ?>
                    <div class="alert alert-success"><?php echo $msg; ?></div>
                <?php } ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $form->name; ?>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open_multipart('web_integration/forms/submit/' . $form->form_key); ?>
                        <?php if (!empty($form->secret_key)) { ?>
                            <input type="hidden" name="secret_key" value="<?php echo $form->secret_key; ?>">
                        <?php } ?>

                        <?php foreach ($fields as $field) {
                            if ($field['is_visible'] == 0)
                                continue;
                            $label = $field['custom_label'] ? $field['custom_label'] : ucfirst(str_replace('_', ' ', $field['field_id']));
                            if ($field['field_id'] == 'consultation_datetime' && empty($field['custom_label'])) {
                                $label = 'Appointment Date & Time';
                            }
                            $required = $field['is_required'] == 1 ? 'required' : '';
                            $req_star = $field['is_required'] == 1 ? '<span class="required">*</span>' : '';
                            $name = $field['field_id']; // This matches the database column name in local table logic
                            ?>
                            <div class="form-group">
                                <label for="<?php echo $name; ?>">
                                    <?php echo $label; ?>
                                    <?php echo $req_star; ?>
                                </label>

                                <?php if ($name == 'gender') { ?>
                                    <select name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control" <?php echo $required; ?>>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Transgender">Transgender</option>
                                        <option value="Other">Other</option>
                                    </select>

                                <?php } elseif ($name == 'treatment') { ?>
                                    <select name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control" <?php echo $required; ?>>
                                        <option value="">Select Treatment</option>
                                        <?php if (isset($treatments) && count($treatments) > 0) {
                                            foreach ($treatments as $treatment) { ?>
                                                <option value="<?php echo $treatment['name']; ?>"><?php echo $treatment['name']; ?>
                                                </option>
                                            <?php }
                                        } ?>
                                    </select>

                                <?php } elseif ($name == 'address') { ?>
                                    <textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control"
                                        rows="3" <?php echo $required; ?>></textarea>

                                <?php } elseif ($name == 'doctor') { ?>
                                    <select name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control" <?php echo $required; ?>>
                                        <option value="">Select Doctor</option>
                                        <?php if (isset($doctors) && count($doctors) > 0) {
                                            foreach ($doctors as $doctor) { ?>
                                                <option value="<?php echo $doctor['staffid']; ?>">
                                                    <?php echo $doctor['firstname'] . ' ' . $doctor['lastname']; ?>
                                                </option>
                                            <?php }
                                        } ?>
                                    </select>
                                <?php } elseif ($name == 'consultation_datetime') { ?>
                                    <?php if (isset($form->link_with_appointments) && $form->link_with_appointments == 1) { ?>
                                        <input type="date" name="consultation_date" id="consultation_date" class="form-control"
                                            <?php echo $required; ?> min="<?php echo date('Y-m-d'); ?>">
                                        <input type="hidden" name="start_time" id="start_time">
                                        <input type="hidden" name="end_time" id="end_time">
                                        <div id="slots_wrapper" style="margin-top: 10px;"></div>
                                        <p id="slot_error" class="text-danger hide">Please select a time slot.</p>
                                    <?php } else { ?>
                                        <input type="datetime-local" name="<?php echo $name; ?>" id="<?php echo $name; ?>"
                                            class="form-control" <?php echo $required; ?> min="<?php echo date('Y-m-d\TH:i'); ?>">
                                    <?php } ?>

                                <?php } elseif ($name == 'age') { ?>
                                    <input type="number" name="<?php echo $name; ?>" id="<?php echo $name; ?>"
                                        class="form-control" max="999"
                                        oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" <?php echo $required; ?>>

                                <?php } elseif ($name == 'mobile_number' || $name == 'whatsapp_number') { ?>
                                    <input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>"
                                        class="form-control" pattern="\d{10}" maxlength="10"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');" <?php echo $required; ?>
                                        title="Must be exactly 10 digits">

                                <?php } elseif ($name == 'email') { ?>
                                    <input type="email" name="<?php echo $name; ?>" id="<?php echo $name; ?>"
                                        class="form-control" <?php echo $required; ?>>

                                <?php } elseif ($name == 'attachment') { ?>
                                    <input type="file" name="<?php echo $name; ?>" id="<?php echo $name; ?>"
                                        class="form-control" <?php echo $required; ?>>

                                <?php } elseif ($name == 'branch') { ?>
                                    <select name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control" <?php echo $required; ?>>
                                        <option value="">Select Branch</option>
                                        <?php if (isset($branches) && count($branches) > 0) {
                                            foreach ($branches as $branch) { ?>
                                                <option value="<?php echo $branch['name']; ?>"><?php echo $branch['name']; ?></option>
                                            <?php }
                                        } ?>
                                    </select>

                                <?php } elseif ($name == 'name') { ?>
                                    <input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>"
                                        class="form-control" maxlength="50" <?php echo $required; ?>>

                                <?php } elseif ($name == 'message') { ?>
                                    <textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control"
                                        rows="4" <?php echo $required; ?>></textarea>

                                <?php } elseif ($name == 'rating') { ?>
                                    <div class="star-rating">
                                        <input type="radio" id="star5" name="rating" value="5" /><label for="star5"
                                            title="5 stars">★</label>
                                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4"
                                            title="4 stars">★</label>
                                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3"
                                            title="3 stars">★</label>
                                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2"
                                            title="2 stars">★</label>
                                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1"
                                            title="1 star">★</label>
                                    </div>

                                <?php } else { ?>
                                    <input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>"
                                        class="form-control" <?php echo $required; ?>>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <div
                            style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px;">
                            <button type="submit" class="btn btn-primary"
                                style="padding: 8px 25px; font-weight: 600;">Submit</button>

                            <div class="branding-container" id="brandingTrigger">
                                <div
                                    style="display: flex; align-items: center; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                                    <span
                                        style="font-size: 11px; color: #7e8c9d; font-weight: 500; letter-spacing: 0.3px;">Powered
                                        by</span>
                                    <img src="<?php echo base_url('modules/web_integration/assets/images/healtho_logo.png'); ?>"
                                        alt="HealthO" style="height: 20px; margin-left: 6px; width: auto;">
                                </div>

                                <div class="healtho-card" id="healthoCard">
                                    <h5>Get our service for your Healthcare Brand</h5>
                                    <a href="tel:+919700730044" class="healtho-card-item">
                                        <i class="fas fa-phone-alt"></i> +91 9700730044
                                    </a>
                                    <a href="mailto:Sales@healtho.in" class="healtho-card-item">
                                        <i class="fas fa-envelope"></i> Sales@healtho.in
                                    </a>
                                    <a href="https://healtho.in" target="_blank" class="healtho-card-item">
                                        <i class="fas fa-globe"></i> healtho.in
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    var web_integration_config = {
        linkWithAppointments: <?php echo (isset($form->link_with_appointments) && $form->link_with_appointments == 1) ? 'true' : 'false'; ?>,
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        slotsUrl: '<?php echo site_url("web_integration/forms/get_doctor_slots"); ?>'
    };
</script>
<script src="<?php echo base_url('modules/web_integration/assets/js/public_form.js'); ?>"></script>

</html>