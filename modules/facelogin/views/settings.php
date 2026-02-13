<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row m-b-25">
                            <div class="col-md-12">
                                <div class="pull-left">
                                    <h4 class="tw-mt-0 tw-mb-1"><?php echo _l('facelogin_settings'); ?></h4>
                                    <p class="text-muted m-b-0"><?php echo _l('facelogin_settings_subtitle'); ?></p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <?php echo form_open(admin_url('facelogin/save_settings')); ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-toggle-on m-r-5"></i> <?php echo _l('facelogin_login_button_heading'); ?>
                            </div>
                            <div class="panel-body">
                                <p class="text-muted m-b-15"><?php echo _l('facelogin_login_button_desc'); ?></p>
                                <div class="form-group m-b-0">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="login_btn_enabled" name="login_btn_enabled" value="1" <?php echo ($login_btn_enabled === '1' ? 'checked' : ''); ?>>
                                        <label for="login_btn_enabled"><?php echo _l('facelogin_login_button_label'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-user-clock m-r-5"></i> Attendance Automation
                            </div>
                            <div class="panel-body">
                                <p class="text-muted m-b-15"><?php echo _l('facelogin_attendance_note'); ?></p>
                                <div class="form-group m-b-10">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="checkin_on_login" name="checkin_on_login" value="1" <?php echo ($checkin_on_login === '1' ? 'checked' : ''); ?>>
                                        <label for="checkin_on_login"><?php echo _l('facelogin_checkin_on_login'); ?></label>
                                    </div>
                                </div>
                                <div class="form-group m-b-10">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="checkin_on_face" name="checkin_on_face" value="1" <?php echo ($checkin_on_face === '1' ? 'checked' : ''); ?>>
                                        <label for="checkin_on_face"><?php echo _l('facelogin_checkin_on_face'); ?></label>
                                    </div>
                                </div>
                                <div class="form-group m-b-0">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="checkout_on_logout" name="checkout_on_logout" value="1" <?php echo ($checkout_on_logout === '1' ? 'checked' : ''); ?>>
                                        <label for="checkout_on_logout"><?php echo _l('facelogin_checkout_on_logout'); ?></label>
                                    </div>
                                </div>
                                <hr class="m-t-15 m-b-15">
                                <div class="form-group">
                                    <label for="att_title_pattern"><?php echo _l('facelogin_att_title_pattern'); ?></label>
                                    <input type="text" id="att_title_pattern" name="att_title_pattern" class="form-control" value="<?php echo html_escape($att_title_pattern); ?>">
                                </div>
                                <div class="form-group m-b-5">
                                    <label for="att_description"><?php echo _l('facelogin_att_description'); ?></label>
                                    <textarea id="att_description" name="att_description" rows="2" class="form-control"><?php echo html_escape($att_description); ?></textarea>
                                </div>
                                <p class="text-muted m-b-0 small"><?php echo _l('facelogin_att_placeholders'); ?></p>
                                <hr class="m-t-15 m-b-15">
                                <div class="form-group m-b-10">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="reuse_daily_task" name="reuse_daily_task" value="1" <?php echo ($reuse_daily_task === '1' ? 'checked' : ''); ?>>
                                        <label for="reuse_daily_task"><?php echo _l('facelogin_reuse_daily_task'); ?></label>
                                    </div>
                                </div>
                                <div class="form-group m-b-10">
                                    <div class="checkbox checkbox-primary">
        <input type="checkbox" id="logout_modal_enabled" name="logout_modal_enabled" value="1" <?php echo ($logout_modal_enabled === '1' ? 'checked' : ''); ?>>
        <label for="logout_modal_enabled"><?php echo _l('facelogin_logout_modal_enabled'); ?></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="logout_mode"><?php echo _l('facelogin_logout_mode'); ?></label>
                                    <select id="logout_mode" name="logout_mode" class="form-control">
                                        <option value="complete" <?php echo ($logout_mode === 'complete' ? 'selected' : ''); ?>><?php echo _l('facelogin_logout_mode_complete'); ?></option>
                                        <option value="pause" <?php echo ($logout_mode === 'pause' ? 'selected' : ''); ?>><?php echo _l('facelogin_logout_mode_pause'); ?></option>
                                    </select>
                                </div>
                                <div class="form-group m-b-0">
                                    <label for="logout_label"><?php echo _l('facelogin_logout_label'); ?></label>
                                    <input type="text" id="logout_label" name="logout_label" class="form-control" value="<?php echo html_escape($logout_label); ?>">
                                    <p class="text-muted m-b-0 small">Only used for pause mode to tag break notes.</p>
                                </div>
                                <hr class="m-t-15 m-b-15">
                                <div class="form-group">
                                    <label for="cooldown_minutes"><?php echo _l('facelogin_cooldown_minutes'); ?></label>
                                    <input type="number" min="0" id="cooldown_minutes" name="cooldown_minutes" class="form-control" value="<?php echo html_escape($cooldown_minutes); ?>">
                                    <p class="text-muted small m-b-0">Minimum minutes between punches for the same staff when using public links (0 disables).</p>
                                </div>
                                <hr class="m-t-15 m-b-15">
                                <div class="form-group">
                                    <label>Logout status choices</label>
                                    <?php
                                        $available = [
                                            'end_shift'  => _l('facelogin_logout_mode_complete'),
                                            'on_break'   => _l('facelogin_logout_label') . ' (' . _l('facelogin_logout_mode_pause') . ')',
                                            'on_lunch'   => 'On Lunch',
                                            'in_meeting' => 'In Meeting',
                                        ];
                                        $selected_statuses = json_decode($logout_statuses ?: '[]', true);
                                        if (!is_array($selected_statuses)) $selected_statuses = [];
                                    ?>
                                    <?php foreach ($available as $key => $label): ?>
                                        <div class="checkbox checkbox-primary m-b-5">
                                            <input type="checkbox" name="logout_statuses[]" id="logout_status_<?php echo $key; ?>" value="<?php echo $key; ?>" <?php echo in_array($key, $selected_statuses) ? 'checked' : ''; ?>>
                                            <label for="logout_status_<?php echo $key; ?>"><?php echo html_escape($label); ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                    <p class="text-muted small m-b-0">Selected options will be shown to staff when logging out to stop/pause attendance timers.</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo _l('settings_save'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</html>
