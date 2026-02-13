<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php echo form_open_multipart($this->uri->uri_string(), ['class' => 'staff-form', 'autocomplete' => 'off']); ?>
                <?php if (isset($member)) { ?>
                    <input type="hidden" name="isedit" value="yes">
                    <input type="hidden" name="memberid" value="<?php echo $member->staffid; ?>">
                <?php } ?>

                <div class="team-header">
                    <h4><?php echo $title; ?></h4>
                    <a href="<?php echo admin_url('team'); ?>" class="btn-cancel">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="team-form-layout">
                    <!-- Left Column: Personal Info -->
                    <div class="form-section">
                        <h5 class="form-title">Personal Information</h5>

                        <div class="profile-upload-container">
                            <div class="profile-preview">
                                <?php if (isset($member) && $member->profile_image) { ?>
                                    <?php echo staff_profile_image($member->staffid, ['img', 'img-responsive']); ?>
                                <?php } else { ?>
                                    <div
                                        style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#9ca3af;">
                                        <i class="fa fa-user fa-3x"></i>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <label for="profile_image"
                                    class="profile-image"><?= _l('staff_edit_profile_image'); ?></label>
                                <input type="file" name="profile_image" class="form-control" id="profile_image">
                            </div>
                        </div>

                        <?php $value = (isset($member) ? $member->firstname : ''); ?>
                        <?php $attrs = (isset($member) ? [] : ['autofocus' => true]); ?>
                        <?php echo render_input('firstname', 'staff_add_edit_firstname', $value, 'text', $attrs); ?>

                        <?php $value = (isset($member) ? $member->lastname : ''); ?>
                        <?php echo render_input('lastname', 'staff_add_edit_lastname', $value); ?>

                        <?php $value = (isset($member) ? $member->email : ''); ?>
                        <?php echo render_input('email', 'staff_add_edit_email', $value, 'email', ['autocomplete' => 'off']); ?>

                        <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
                        <?php echo render_input('phonenumber', 'staff_add_edit_phonenumber', $value); ?>

                        <div class="form-group">
                            <label for="password"
                                class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
                            <div class="input-group">
                                <input type="password" class="form-control password" name="password" autocomplete="off">
                                <span class="input-group-addon">
                                    <a href="#password" class="show_password"
                                        onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
                                </span>
                                <span class="input-group-addon">
                                    <a href="#" class="generate_password"
                                        onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
                                </span>
                            </div>
                            <?php if (isset($member)) { ?>
                                <p class="text-muted"><?php echo _l('staff_add_edit_password_note'); ?></p>
                            <?php } ?>
                        </div>

                        <?php if (is_admin()) { ?>
                            <hr />
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="administrator" id="administrator" <?php echo (isset($member) && ($member->admin == 1)) ? 'checked' : ''; ?>>
                                <label for="administrator"><?php echo _l('staff_add_edit_administrator'); ?></label>
                            </div>
                        <?php } ?>

                        <div class="form-group">
                            <?php if (count($departments) > 0) { ?>
                                <label for="departments"><?= _l('staff_add_edit_departments'); ?></label>
                            <?php } ?>
                            <div
                                style="max-height: 150px; overflow-y: auto; border: 1px solid #e5e7eb; padding: 10px; border-radius: 4px;">
                                <?php foreach ($departments as $department) { ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="dep_<?= $department['departmentid']; ?>"
                                            name="departments[]" value="<?= $department['departmentid']; ?>"
                                            <?= (isset($member) && in_array($department['departmentid'], array_column($staff_departments, 'departmentid'))) ? 'checked' : ''; ?>>
                                        <label
                                            for="dep_<?= $department['departmentid']; ?>"><?= $department['name']; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Roles & Permissions -->
                    <div class="form-section">
                        <h5 class="form-title">Roles & Permissions</h5>

                        <?php
                        $selected = '';
                        foreach ($roles as $role) {
                            if (isset($member) && $member->role == $role['roleid']) {
                                $selected = $role['roleid'];
                                break;
                            }
                        }
                        ?>
                        <?php echo render_select('role', $roles, ['roleid', 'name'], 'staff_add_edit_role', $selected); ?>

                        <div class="clearfix mtop15"></div>
                        <h5 class="mbot15"><?php echo _l('staff_add_edit_permissions'); ?></h5>

                        <?php
                        // Reuse the core permissions view but wrap it nicely if needed
                        $this->load->view('admin/staff/permissions', [
                            'funcData' => ['staff_id' => isset($member) ? $member->staffid : null],
                            'member' => isset($member) ? $member : null,
                        ]);
                        ?>
                    </div>
                </div>

                <div class="team-actions">
                    <button type="submit" class="btn-save"><?php echo _l('submit'); ?></button>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        // Init roles/permissions logic from core
        $('select[name="role"]').on('change', function () {
            var roleid = $(this).val();
            init_roles_permissions(roleid, true);
        });

        $('input[name="administrator"]').on('change', function () {
            var checked = $(this).prop('checked');
            var isNotStaffMember = $('.is-not-staff'); // This class might not be used here but keeping logic consistent
            if (checked == true) {
                $('.roles').find('input').prop('disabled', true).prop('checked', false);
            } else {
                $('.roles').find('.capability').not('[data-not-applicable="true"]').prop('disabled', false)
            }
        });

        // Initialize permissions
        init_roles_permissions();

        // Validation
        appValidateForm($('.staff-form'), {
            firstname: 'required',
            lastname: 'required',
            email: {
                required: true,
                email: true,
                remote: {
                    url: admin_url + "misc/staff_email_exists",
                    type: 'post',
                    data: {
                        email: function () {
                            return $('input[name="email"]').val();
                        },
                        memberid: function () {
                            return $('input[name="memberid"]').val(); // Need to ensure memberid is in form if edit
                        }
                    }
                }
            },
            password: {
                required: {
                    depends: function (element) {
                        return ($('input[name="isedit"]').length == 0) ? true : false
                    }
                }
            }
        });
    });
</script>