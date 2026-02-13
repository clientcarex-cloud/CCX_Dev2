<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string(), ['id' => 'staff_member_form']); ?>
                        <div class="clearfix">
                            <h4 class="no-margin pull-left"><?php echo $title; ?></h4>
                            <div class="pull-right" style="display:flex; align-items:center;">
                                <div class="onoffswitch mright10">
                                    <input type="checkbox" value="1" name="active" class="onoffswitch-checkbox"
                                        id="active" <?php if (!isset($member) || (isset($member) && $member->active == 1)) {
                                            echo 'checked';
                                        } ?>>
                                    <label class="onoffswitch-label" for="active"></label>
                                </div>
                                <span id="active_status_label" class="label <?php echo (!isset($member) || (isset($member) && $member->active == 1)) ? 'label-success' : 'label-danger'; ?>">
                                    <?php echo (!isset($member) || (isset($member) && $member->active == 1)) ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <?php if(isset($member)) { ?>
                        <!-- Doctor Profile Stats -->
                        <div class="row mbot20">
                            <style>
                                .doctor-stats-card {
                                    background: #fff;
                                    border-radius: 12px;
                                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                                    padding: 20px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    height: 100%;
                                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                                    border: 1px solid rgba(0,0,0,0.03);
                                }
                                .doctor-stats-card:hover {
                                    transform: translateY(-5px);
                                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                                }
                                .doctor-stats-icon {
                                    width: 50px;
                                    height: 50px;
                                    border-radius: 12px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 24px;
                                    margin-right: 15px;
                                }
                                .doctor-stats-info { flex-grow: 1; }
                                .doctor-stats-title {
                                    font-size: 13px;
                                    font-weight: 600;
                                    color: #64748b;
                                    margin-bottom: 5px;
                                    text-transform: uppercase;
                                    letter-spacing: 0.5px;
                                }
                                .doctor-stats-number {
                                    font-size: 24px;
                                    font-weight: 800;
                                    color: #1e293b;
                                    line-height: 1;
                                }
                                /* Stats Colors */
                                .stat-total-appt .doctor-stats-icon { background: #eff6ff; color: #2563eb; } /* Blue */
                                .stat-patients .doctor-stats-icon { background: #f0fdf4; color: #16a34a; } /* Green */
                                .stat-upcoming .doctor-stats-icon { background: #fefce8; color: #ca8a04; } /* Yellow */
                                .stat-pending .doctor-stats-icon { background: #fef2f2; color: #dc2626; } /* Red */
                                
                                .stats-col { margin-bottom: 20px; }
                            </style>

                            <!-- Total Appointments -->
                            <div class="col-md-3 stats-col">
                                <div class="doctor-stats-card stat-total-appt">
                                    <div class="doctor-stats-icon"><i class="fa fa-calendar-check-o"></i></div>
                                    <div class="doctor-stats-info">
                                        <div class="doctor-stats-title">Total Appointments</div>
                                        <div class="doctor-stats-number"><?php echo isset($total_appointments) ? $total_appointments : 0; ?></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Unique Patients -->
                            <div class="col-md-3 stats-col">
                                <div class="doctor-stats-card stat-patients">
                                    <div class="doctor-stats-icon"><i class="fa fa-users"></i></div>
                                    <div class="doctor-stats-info">
                                        <div class="doctor-stats-title">Unique Patients</div>
                                        <div class="doctor-stats-number"><?php echo isset($unique_patients) ? $unique_patients : 0; ?></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Upcoming Appointments -->
                            <div class="col-md-3 stats-col">
                                <div class="doctor-stats-card stat-upcoming">
                                    <div class="doctor-stats-icon"><i class="fa fa-clock-o"></i></div>
                                    <div class="doctor-stats-info">
                                        <div class="doctor-stats-title">Upcoming</div>
                                        <div class="doctor-stats-number"><?php echo isset($upcoming_appointments) ? $upcoming_appointments : 0; ?></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Pending Appointments -->
                            <div class="col-md-3 stats-col">
                                <div class="doctor-stats-card stat-pending">
                                    <div class="doctor-stats-icon"><i class="fa fa-exclamation-circle"></i></div>
                                    <div class="doctor-stats-info">
                                        <div class="doctor-stats-title">Pending</div>
                                        <div class="doctor-stats-number"><?php echo isset($pending_appointments) ? $pending_appointments : 0; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>


                        <!-- Single Page Layout -->
                        <div class="row">
                            <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname" class="control-label"><?php echo _l('Name'); ?> <span
                                                    class="text-danger">*</span></label>
                                            <div class="row">
                                                <div class="col-md-6" style="padding-right:5px;">
                                                    <input type="text" class="form-control" name="firstname"
                                                        placeholder="First Name"
                                                        value="<?php echo (isset($member) ? $member->firstname : ''); ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-6" style="padding-left:5px;">
                                                    <input type="text" class="form-control" name="lastname"
                                                        placeholder="Last Name"
                                                        value="<?php echo (isset($member) ? $member->lastname : ''); ?>"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sex" class="control-label"><?php echo _l('Gender'); ?> <span
                                                    class="text-danger">*</span></label>
                                            <select name="sex" class="form-control">
                                                <option value="Male" <?php if (isset($member) && $member->sex == 'Male')
                                                    echo 'selected'; ?>>Male</option>
                                                <option value="Female" <?php if (isset($member) && $member->sex == 'Female')
                                                    echo 'selected'; ?>>Female</option>
                                                <option value="Other" <?php if (isset($member) && $member->sex == 'Other')
                                                    echo 'selected'; ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email"
                                                class="control-label"><?php echo _l('Email ID'); ?></label>
                                            <input type="email" class="form-control" name="email"
                                                value="<?php echo (isset($member) ? $member->email : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phonenumber"
                                                class="control-label"><?php echo _l('Mobile No.'); ?>
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="phonenumber" maxlength="10"
                                                value="<?php echo (isset($member) ? $member->phonenumber : ''); ?>"
                                                onkeypress="return (event.charCode !=8 &&0 == event.charCode || (event.charCode >= 48 && event.charCode <= 57))"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_of_birth"
                                                class="control-label"><?php echo _l('Date of Birth'); ?></label>
                                            <input type="date" class="form-control" name="date_of_birth"
                                                value="<?php echo (isset($member) ? $member->date_of_birth : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="area" class="control-label"><?php echo _l('area'); ?></label>
                                            <input type="text" class="form-control" name="area"
                                                value="<?php echo (isset($member) ? $member->area : ''); ?>">
                                        </div>
                                    </div>
                                </div>

                            <!-- Appended Account & Permissions -->

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qualification"
                                            class="control-label"><?php echo _l('qualification'); ?></label>
                                        <input type="text" class="form-control" name="qualification"
                                            value="<?php echo (isset($member) ? $member->qualification : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="specialization"
                                            class="control-label"><?php echo _l('scholar_specialization'); ?></label>
                                        <input type="text" class="form-control" name="specialization"
                                            value="<?php echo (isset($member) ? $member->specialization : ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="designation"
                                            class="control-label"><?php echo _l('designation'); ?></label>
                                        <input type="text" class="form-control" name="designation"
                                            value="<?php echo (isset($member) ? $member->designation : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department"
                                            class="control-label">Department</label>
                                        <select name="department" class="form-control selectpicker"
                                            data-live-search="true">
                                            <option value=""></option>
                                            <option value="Cardiology" <?php if (isset($member) && $member->department == 'Cardiology')
                                                echo 'selected'; ?>>Cardiology</option>
                                            <option value="Neurology" <?php if (isset($member) && $member->department == 'Neurology')
                                                echo 'selected'; ?>>Neurology</option>
                                            <option value="Orthopedics" <?php if (isset($member) && $member->department == 'Orthopedics')
                                                echo 'selected'; ?>>Orthopedics</option>
                                            <option value="Dermatology" <?php if (isset($member) && $member->department == 'Dermatology')
                                                echo 'selected'; ?>>Dermatology</option>
                                            <option value="General" <?php if (isset($member) && $member->department == 'General')
                                                echo 'selected'; ?>>General</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doctor_profile_type"
                                            class="control-label"><?php echo _l('doctor_profile_type'); ?> <span class="text-danger">*</span></label>
                                        <select name="doctor_profile_type" class="form-control selectpicker" required>
                                            <option value=""></option>
                                            <option value="Consultant" <?php if (isset($member) && $member->doctor_profile_type == 'Consultant')
                                                echo 'selected'; ?>>Consultant</option>
                                            <option value="Referral" <?php if (isset($member) && $member->doctor_profile_type == 'Referral')
                                                echo 'selected'; ?>>Referral</option>
                                            <option value="On-Call" <?php if (isset($member) && $member->doctor_profile_type == 'On-Call')
                                                echo 'selected'; ?>>On-Call</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="experience"
                                            class="control-label"><?php echo _l('experience'); ?></label>
                                        <input type="text" class="form-control" name="experience" placeholder="in years"
                                            value="<?php echo (isset($member) ? $member->experience : ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="professional_id"
                                            class="control-label"><?php echo _l('professional_id'); ?></label>
                                        <input type="text" class="form-control" name="professional_id"
                                            value="<?php echo (isset($member) ? $member->professional_id : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="default_service_item"
                                        class="control-label"><?php echo _l('default_service_item'); ?></label>
                                    <select name="default_service_item" class="form-control selectpicker"
                                        data-live-search="true">
                                        <option value=""></option>
                                        <?php foreach ($items as $item) {
                                            $item_id = isset($item['id']) ? $item['id'] : (isset($item['itemid']) ? $item['itemid'] : '');
                                            if (empty($item_id))
                                                continue;
                                            ?>
                                            <option value="<?php echo $item_id; ?>" <?php if (isset($member) && $member->default_service_item == $item_id) {
                                                   echo 'selected';
                                               } ?>>
                                                <?php echo $item['description'] . ' - ' . number_format($item['rate'], 2); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="signature" class="control-label"><?php echo _l('signature'); ?></label>
                                    <input type="file" class="form-control" name="signature_image">
                                </div>
                            </div>
                            <!-- Role & Permissions Moved Here -->
                            <hr />
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="role"
                                        class="control-label"><?php echo _l('staff_add_edit_role'); ?> <span class="text-danger">*</span></label>
                                    <select name="role" class="form-control selectpicker" data-live-search="true"
                                        required>
                                        <option value=""></option>
                                        <?php foreach ($roles as $role) { ?>
                                            <option value="<?php echo $role['roleid']; ?>" <?php if (isset($member) && $member->role == $role['roleid']) {
                                                   echo 'selected';
                                               } ?>>
                                                <?php echo $role['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="password"
                                        class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control password" name="password" autocomplete="off" <?php if (isset($member)) {
                                            echo 'disabled';
                                        } ?>>
                                        <span class="input-group-addon">
                                            <a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
                                        </span>
                                        <span class="input-group-addon">
                                            <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <hr />
                            <h4 class="pointer" data-toggle="collapse" data-target="#permissions_wrapper" style="cursor:pointer;" aria-expanded="false" aria-controls="permissions_wrapper">
                                <?php echo _l('staff_add_edit_permissions'); ?> 
                                <i class="fa fa-chevron-down pull-right"></i>
                            </h4>
                            <div class="collapse" id="permissions_wrapper">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php $this->load->view('admin/staff/permissions', [
                                            'funcData' => ['staff_id' => isset($member) ? $member->staffid : null],
                                            'member' => isset($member) ? $member : null,
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                            <br>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="anytime_appointment" id="anytime_appointment"
                                            value="1" <?php if (isset($member) && $member->anytime_appointment == 1) {
                                                echo 'checked';
                                            } ?>>
                                        <label
                                            for="anytime_appointment"><?php echo _l('anytime_appointment'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('days'); ?></th>
                                            <th><?php echo _l('start_time'); ?></th>
                                            <th><?php echo _l('end_time'); ?></th>
                                            <th><?php echo _l('slot_duration'); ?></th>
                                            <th><?php echo _l('is_available'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        foreach ($days as $index => $day) {
                                            $day_schedule = null;
                                            if (isset($schedule)) {
                                                foreach ($schedule as $s) {
                                                    if ($s['day_of_week'] == $day) {
                                                        $day_schedule = $s;
                                                        break;
                                                    }
                                                }
                                            }

                                            $start_time = $day_schedule ? $day_schedule['start_time'] : '09:00:00';
                                            $end_time = $day_schedule ? $day_schedule['end_time'] : '17:00:00';
                                            $slot_duration = $day_schedule ? $day_schedule['slot_duration'] : 30;
                                            $is_available = $day_schedule ? $day_schedule['is_available'] : 1;
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $day; ?>
                                                    <input type="hidden" name="schedule[<?php echo $index; ?>][day_of_week]"
                                                        value="<?php echo $day; ?>">
                                                </td>
                                                <td>
                                                    <input type="time" name="schedule[<?php echo $index; ?>][start_time]"
                                                        class="form-control" value="<?php echo $start_time; ?>">
                                                </td>
                                                <td>
                                                    <input type="time" name="schedule[<?php echo $index; ?>][end_time]"
                                                        class="form-control" value="<?php echo $end_time; ?>">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        name="schedule[<?php echo $index; ?>][slot_duration]"
                                                        class="form-control" value="<?php echo $slot_duration; ?>">
                                                </td>
                                                <td>
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox"
                                                            name="schedule[<?php echo $index; ?>][is_available]" value="1" <?php if ($is_available)
                                                                   echo 'checked'; ?>>
                                                        <label></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
        $('#active').on('change', function() {
            var label = $('#active_status_label');
            if($(this).prop('checked')) {
                label.removeClass('label-danger').addClass('label-success').text('Active');
            } else {
                label.removeClass('label-success').addClass('label-danger').text('Inactive');
            }
        });

        $('select[name="role"]').on('change', function () {
            var roleid = $(this).val();
            init_roles_permissions(roleid, true);
        });

        // Initialize permissions if role is already selected (e.g. edit mode)
        var currentRole = $('select[name="role"]').val();
        if (currentRole) {
            // We don't want to reset permissions on page load if they are already saved, 
            // but init_roles_permissions without 'true' (reset) arg might simply init.
            // Actually, standard view calls init_roles_permissions(); at the end.
            init_roles_permissions();
        } else {
            init_roles_permissions();
        }
    });
</script>
</body>

</html>