<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Module Name: FaceLogin
 * Description: Login with Face Recognition
 * Version: 1.0.0
 * Requires at least: 3.0.*
 */

define('FACELOGIN_MODULE_NAME', 'facelogin');
define('VERSION_FACELOGIN', 100);
define('FACELOGIN_MODULE_ITEM_ID', 'FaceLogin');
define('FACELOGIN_JSONSAVE_FOLDER', module_dir_path(FACELOGIN_MODULE_NAME, 'faces'));

hooks()->add_action('admin_init', 'facelogin_init_menu_items');
hooks()->add_action('after_staff_login', 'facelogin_handle_auto_checkin');
hooks()->add_action('before_staff_logout', 'facelogin_handle_auto_checkout');
hooks()->add_action('admin_init', 'facelogin_guard_attendance_session');
hooks()->add_action('app_admin_footer', 'facelogin_render_logout_modal');
hooks()->add_action('admin_init', 'facelogin_ensure_tables');
hooks()->add_action('admin_footer', 'facelogin_inject_activity_tracker');

// Register language files
register_language_files(FACELOGIN_MODULE_NAME, [FACELOGIN_MODULE_NAME]);

/**
 * Module activation hook.
 */
register_activation_hook(FACELOGIN_MODULE_NAME, 'facelogin_activate_module');

function facelogin_activate_module()
{
    require_once __DIR__ . '/install.php';
}


$CI = &get_instance();
// Load helper functions
$CI->load->helper(FACELOGIN_MODULE_NAME . '/facelogin');

/**
 * Ensure new tables exist for upgraded installs.
 */
function facelogin_ensure_tables()
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $CI = &get_instance();
    $CI->db->query("CREATE TABLE IF NOT EXISTS `" . db_prefix() . "face_links` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `staff_id` INT(11) NOT NULL,
        `name` VARCHAR(191) NOT NULL,
        `token` VARCHAR(64) NOT NULL,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `is_global` TINYINT(1) NOT NULL DEFAULT 0,
        `created_by` INT(11) DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `last_used_at` DATETIME DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `token` (`token`),
        INDEX `staff_idx` (`staff_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    if (!$CI->db->field_exists('is_global', db_prefix() . 'face_links')) {
        $CI->db->query("ALTER TABLE `" . db_prefix() . "face_links` ADD `is_global` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`");
    }

    $CI->db->query("CREATE TABLE IF NOT EXISTS `" . db_prefix() . "face_activity_log` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `staff_id` INT(11) NOT NULL,
        `date` DATE NOT NULL,
        `active_seconds` INT(11) NOT NULL DEFAULT 0,
        `idle_seconds` INT(11) NOT NULL DEFAULT 0,
        `last_heartbeat` DATETIME DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `staff_date` (`staff_id`, `date`),
        INDEX `date_idx` (`date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}


function facelogin_init_menu_items()
{
    $CI = &get_instance();

    if (is_admin()) {
        $CI->app_menu->add_sidebar_menu_item(FACELOGIN_MODULE_NAME, [
            'name' => _l('facelogin'),
            'icon' => 'fa fa-right-to-bracket',
            'href' => '#',
            'position' => 30,
        ]);

        $CI->app_menu->add_sidebar_children_item(FACELOGIN_MODULE_NAME, [
            'slug' => 'facelogin_enrollment',
            'name' => _l('facelogin_enrollment'),
            'href' => admin_url('facelogin/enrollment'),
            'icon' => 'fa fa-user-plus',
            'position' => 1,
        ]);

        $CI->app_menu->add_sidebar_children_item(FACELOGIN_MODULE_NAME, [
            'slug' => 'facelogin_face_logs',
            'name' => _l('facelogin_face_logs'),
            'href' => admin_url('facelogin/face_logs'),
            'icon' => 'fa fa-list-alt',
            'position' => 2,
        ]);

        $CI->app_menu->add_sidebar_children_item(FACELOGIN_MODULE_NAME, [
            'slug' => 'facelogin_activity_log',
            'name' => 'Activity Log',
            'href' => admin_url('facelogin/activity_log'),
            'icon' => 'fa fa-chart-line',
            'position' => 2.2,
        ]);

        $CI->app_menu->add_sidebar_children_item(FACELOGIN_MODULE_NAME, [
            'slug' => 'facelogin_links',
            'name' => _l('facelogin_links'),
            'href' => admin_url('facelogin/links'),
            'icon' => 'fa fa-link',
            'position' => 2.5,
        ]);

        $CI->app_menu->add_sidebar_children_item(FACELOGIN_MODULE_NAME, [
            'slug' => 'facelogin_settings',
            'name' => _l('facelogin_settings'),
            'href' => admin_url('facelogin/settings'),
            'icon' => 'fa fa-gear',
            'position' => 3,
        ]);
    }
}


hooks()->add_action('before_admin_login_form_close', 'facelogin_init_login_btn');
function facelogin_init_login_btn()
{
    $enabled = get_option('facelogin_login_btn_enabled');

    if ($enabled === '' || $enabled === null) {
        add_option('facelogin_login_btn_enabled', '1');
        $enabled = '1';
    }

    if ($enabled === '1') {
        include __DIR__ . '/views/login_btn.php';
    }
}

/**
 * Auto check-in when enabled.
 */
function facelogin_handle_auto_checkin($staff = null)
{
    $CI = &get_instance();
    log_message('error', 'FaceLogin: Auto checkin triggered for staff: ' . json_encode($staff));
    $staff_id = facelogin_resolve_staff_id($staff);
    if (!$staff_id) {
        return;
    }

    $checkin_login = get_option('facelogin_checkin_on_login');
    $checkin_facelogin = get_option('facelogin_checkin_on_facelogin');
    $from_face = (bool) $CI->session->userdata('facelogin_via_face');

    // Clear flag after use
    $CI->session->unset_userdata('facelogin_via_face');

    if ($from_face && $checkin_facelogin !== '1') {
        return;
    }
    if (!$from_face && $checkin_login !== '1') {
        return;
    }

    facelogin_create_attendance_entry($staff_id, $from_face ? 'FaceLogin' : 'Password Login');
}

/**
 * Auto check-out when enabled.
 */
function facelogin_handle_auto_checkout($staff = null)
{
    if (get_option('facelogin_checkout_on_logout') !== '1') {
        return;
    }

    $CI = &get_instance();
    $staff_id = facelogin_resolve_staff_id($staff);
    if (!$staff_id) {
        return;
    }

    facelogin_close_attendance_entry($staff_id);
}

/**
 * Resolve staff ID from hook payloads (array/object/int), fallback to current user.
 */
function facelogin_resolve_staff_id($staff)
{
    if (is_numeric($staff)) {
        return (int) $staff;
    }
    if (is_array($staff) && isset($staff['staffid'])) {
        return (int) $staff['staffid'];
    }
    if (is_object($staff) && isset($staff->staffid)) {
        return (int) $staff->staffid;
    }
    if (function_exists('get_staff_user_id')) {
        return (int) get_staff_user_id();
    }
    return null;
}

/**
 * Create attendance task/timer if none is open for this staff.
 */
function facelogin_create_attendance_entry($staff_id, $method_label = 'Login')
{
    $CI = &get_instance();
    if (!facelogin_attendance_available()) {
        return;
    }

    $reuse_daily = get_option('facelogin_reuse_daily_task') === '1';
    $today_task = facelogin_find_today_attendance_task($staff_id);
    $open = facelogin_find_open_attendance_task($staff_id);

    // Reuse today's task if allowed
    if ($reuse_daily && $today_task) {
        $task_id = $today_task['id'];
        // reopen if it was finished
        $CI->db->where('id', $task_id)->update(db_prefix() . 'tasks', [
            'status' => 4,
            'datefinished' => null,
        ]);
        facelogin_start_timer($staff_id, $task_id);
        return;
    }

    // avoid duplicate open entry
    if ($open) {
        return;
    }

    $staff = facelogin_get_staff_info($staff_id);
    $title = facelogin_format_attendance_title($staff, $method_label);
    $desc = facelogin_format_attendance_description($staff, $method_label);

    $CI->db->insert(db_prefix() . 'tasks', [
        'name' => $title,
        'description' => $desc,
        'dateadded' => date('Y-m-d H:i:s'),
        'startdate' => date('Y-m-d'),
        'addedfrom' => $staff_id,
        'priority' => 2,
        'status' => 4, // in progress
    ]);

    $task_id = $CI->db->insert_id();
    if ($task_id) {
        $CI->db->insert(db_prefix() . 'taskstimers', [
            'task_id' => $task_id,
            'start_time' => time(),
            'staff_id' => $staff_id,
        ]);
    }
}

/**
 * Close the latest open attendance task for this staff.
 */
function facelogin_close_attendance_entry($staff_id, $forced_mode = null, $label = null)
{
    $CI = &get_instance();
    if (!facelogin_attendance_available()) {
        return;
    }

    $task = facelogin_find_open_attendance_task($staff_id);
    if (!$task) {
        return;
    }

    $CI->db->where('id', $task['id']);

    $logout_mode = $forced_mode ?: (get_option('facelogin_logout_mode') ?: 'complete');
    $break_label = $label ?: (get_option('facelogin_logout_label') ?: 'On Break');

    if ($logout_mode === 'complete') {
        $CI->db->update(db_prefix() . 'tasks', [
            'datefinished' => date('Y-m-d H:i:s'),
            'status' => 5, // complete
        ]);
    } else {
        // Pause: keep task open but mark status textually
        $desc = $task['description'] . ' | ' . $break_label . ' ' . date('H:i');
        $CI->db->update(db_prefix() . 'tasks', [
            'status' => 4,
            'description' => $desc,
        ]);
    }

    facelogin_stop_timer($staff_id, $task['id']);
}

/**
 * Find latest open attendance task created by FaceLogin.
 */
function facelogin_find_open_attendance_task($staff_id)
{
    $CI = &get_instance();
    $marker = facelogin_attendance_marker();
    return $CI->db->select('*')
        ->from(db_prefix() . 'tasks')
        ->where('addedfrom', $staff_id)
        ->where('status !=', 5)
        ->where('datefinished IS NULL', null, false)
        ->like('description', $marker)
        ->order_by('id', 'desc')
        ->limit(1)
        ->get()
        ->row_array();
}

/**
 * Find today's FaceLogin attendance task (any status).
 */
function facelogin_find_today_attendance_task($staff_id)
{
    $CI = &get_instance();
    $marker = facelogin_attendance_marker();
    $today = date('Y-m-d');
    return $CI->db->select('*')
        ->from(db_prefix() . 'tasks')
        ->where('addedfrom', $staff_id)
        ->where('DATE(startdate)=', $today)
        ->like('description', $marker)
        ->order_by('id', 'desc')
        ->limit(1)
        ->get()
        ->row_array();
}

function facelogin_attendance_marker()
{
    return '[FaceLogin Attendance]';
}

function facelogin_attendance_available()
{
    // The module uses core 'tasks' and 'taskstimers' tables, so it doesn't strictly depend on an 'attendance' module.
    // We return true to allow the built-in attendance logic to run.
    return true;
    // return file_exists(module_dir_path('attendance', 'models/Attendance_model.php'));
}

function facelogin_get_staff_info($staff_id)
{
    $CI = &get_instance();
    $row = $CI->db->select('firstname, lastname, email')->from(db_prefix() . 'staff')->where('staffid', $staff_id)->get()->row_array();
    return [
        'name' => isset($row['firstname']) ? trim($row['firstname'] . ' ' . $row['lastname']) : '',
        'email' => $row['email'] ?? '',
        'id' => $staff_id,
    ];
}

function facelogin_format_attendance_title(array $staff, $method_label)
{
    $pattern = get_option('facelogin_att_title_pattern');
    if ($pattern === '' || $pattern === null) {
        $pattern = 'Attendance|{staff_name}|{datetime}';
    }
    $replacements = [
        '{staff_name}' => $staff['name'] ?? '',
        '{staff_email}' => $staff['email'] ?? '',
        '{staff_id}' => $staff['id'] ?? '',
        '{datetime}' => date('d-m-Y H:i:s'),
        '{method}' => $method_label,
    ];
    return strtr($pattern, $replacements);
}

function facelogin_start_timer($staff_id, $task_id)
{
    $CI = &get_instance();
    // start new timer if none open
    $open_timer = $CI->db->select('id')
        ->from(db_prefix() . 'taskstimers')
        ->where('task_id', $task_id)
        ->where('staff_id', $staff_id)
        ->where('(end_time IS NULL)', null, false)
        ->order_by('id', 'desc')
        ->get()->row_array();
    if ($open_timer) {
        return;
    }
    $CI->db->insert(db_prefix() . 'taskstimers', [
        'task_id' => $task_id,
        'start_time' => time(),
        'staff_id' => $staff_id,
    ]);
}

function facelogin_stop_timer($staff_id, $task_id)
{
    $CI = &get_instance();
    $timer = $CI->db->select('id')
        ->from(db_prefix() . 'taskstimers')
        ->where('task_id', $task_id)
        ->where('staff_id', $staff_id)
        ->where('(end_time IS NULL)', null, false)
        ->order_by('id', 'desc')
        ->get()->row_array();

    if ($timer) {
        $CI->db->where('id', $timer['id']);
        $CI->db->update(db_prefix() . 'taskstimers', [
            'end_time' => time(),
        ]);
    }
}

/**
 * Render logout modal and JS to control attendance-aware logout flow.
 */
function facelogin_render_logout_modal()
{
    if (!is_staff_logged_in()) {
        return;
    }
    if (get_option('facelogin_logout_modal_enabled') !== '1') {
        return;
    }
    $enabled = get_option('facelogin_checkin_on_login') === '1' || get_option('facelogin_checkin_on_facelogin') === '1';
    $statuses = facelogin_get_logout_status_options();
    $selected = facelogin_get_selected_logout_statuses();
    if (!$enabled || empty($statuses) || empty($selected)) {
        return;
    }

    $logout_url = admin_url('authentication/logout');
    $csrf_name = get_instance()->security->get_csrf_token_name();
    $csrf_hash = get_instance()->security->get_csrf_hash();
    ?>
    <div class="modal fade" id="faceloginLogoutModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Choose status before logout</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted m-b-10">Select how to mark your attendance session before logging out.</p>
                    <?php foreach ($selected as $idx => $key):
                        if (!isset($statuses[$key]))
                            continue; ?>
                        <div class="radio radio-primary m-b-5">
                            <input type="radio" name="facelogout_status" id="facelogout_<?php echo html_escape($key); ?>"
                                value="<?php echo html_escape($key); ?>" <?php echo $idx === 0 ? 'checked' : ''; ?>>
                            <label
                                for="facelogout_<?php echo html_escape($key); ?>"><?php echo html_escape($statuses[$key]); ?></label>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-danger m-t-10" id="facelogout_error" style="display:none;">Please choose a status.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="button" class="btn btn-primary" id="facelogout_confirm"><i class="fa fa-sign-out"></i>
                        Logout</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function ($) {
            var logoutUrl = '<?php echo $logout_url; ?>';
            function openLogoutModal(href) {
                $('#facelogout_error').hide();
                $('input[name="facelogout_status"]').prop('checked', false);
                $('input[name="facelogout_status"]').first().prop('checked', true);
                $('#faceloginLogoutModal').data('logout-url', href || logoutUrl).modal('show');
            }
            function interceptLogout(e) {
                var href = $(this).attr('href');
                if (!href || href.indexOf('authentication/logout') === -1) return;
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                openLogoutModal(href);
            }
            $(function () {
                $('body').on('click', 'a[href*="authentication/logout"]', interceptLogout);
                // override possible core init_logout hook
                window.init_logout = function () { openLogoutModal(logoutUrl); return false; };
                $('#facelogout_confirm').on('click', function () {
                    var val = $('input[name="facelogout_status"]:checked').val();
                    if (!val) {
                        $('#facelogout_error').show();
                        return;
                    }
                    var fd = new FormData();
                    fd.append('status', val);
                    fd.append('<?php echo $csrf_name; ?>', '<?php echo $csrf_hash; ?>');
                    $.ajax({
                        url: '<?php echo admin_url('facelogin/apply_logout_status'); ?>',
                        method: 'POST',
                        data: fd,
                        contentType: false,
                        processData: false,
                        dataType: 'json'
                    }).always(function () {
                        window.location.href = $('#faceloginLogoutModal').data('logout-url') || logoutUrl;
                    });
                });
            });
        })(jQuery);
    </script>
    <?php
}

function facelogin_get_logout_status_options()
{
    return [
        'end_shift' => 'Complete attendance (end shift)',
        'on_break' => 'On Break',
        'on_lunch' => 'On Lunch',
        'in_meeting' => 'In Meeting',
    ];
}

function facelogin_get_selected_logout_statuses()
{
    $json = get_option('facelogin_logout_statuses');
    if (!$json) {
        return ['end_shift', 'on_break', 'on_lunch'];
    }
    $arr = @json_decode($json, true);
    if (!is_array($arr)) {
        return ['end_shift', 'on_break', 'on_lunch'];
    }
    return array_values(array_filter($arr));
}

function facelogin_format_attendance_description(array $staff, $method_label)
{
    $pattern = get_option('facelogin_att_description');
    $marker = facelogin_attendance_marker();
    if ($pattern === '' || $pattern === null) {
        $pattern = 'Auto punch-in via {method}. ' . $marker;
    }
    if (strpos($pattern, $marker) === false) {
        $pattern .= ' ' . $marker;
    }
    $replacements = [
        '{staff_name}' => $staff['name'] ?? '',
        '{staff_email}' => $staff['email'] ?? '',
        '{staff_id}' => $staff['id'] ?? '',
        '{datetime}' => date('d-m-Y H:i:s'),
        '{method}' => $method_label,
    ];
    return strtr($pattern, $replacements);
}

/**
 * Guard: ensure an attendance entry exists whenever auto check-in is required.
 */
function facelogin_guard_attendance_session()
{
    if (!is_staff_logged_in()) {
        return;
    }

    $require_login = get_option('facelogin_checkin_on_login') === '1';
    $require_face = get_option('facelogin_checkin_on_facelogin') === '1';
    if (!$require_login && !$require_face) {
        return;
    }

    $staff_id = get_staff_user_id();
    if (!$staff_id || !facelogin_attendance_available()) {
        return;
    }

    $open = facelogin_find_open_attendance_task($staff_id);
    if (!$open) {
        facelogin_create_attendance_entry($staff_id, 'Session Guard');
    }
}

function facelogin_inject_activity_tracker()
{
    if (!is_staff_logged_in()) {
        return;
    }
    // Only track if we have an open attendance task? 
    // The user said "after their punch-in and till punch-out".
    // So we should check if they are checked in.
    $staff_id = get_staff_user_id();
    $open_task = facelogin_find_open_attendance_task($staff_id);

    if ($open_task) {
        echo '<script src="' . module_dir_url(FACELOGIN_MODULE_NAME, 'assets/js/activity_tracker.js') . '?v=' . VERSION_FACELOGIN . '"></script>';
    }
}
