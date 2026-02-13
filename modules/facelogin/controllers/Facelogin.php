<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facelogin extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['facedata_model', 'staff_model', 'settings_model', 'facelink_model']);
    }

    /**
     * Personal face setup screen.
     */
    public function setup()
    {
        $data['title'] = _l('facelogin_setup');
        $existing = $this->facedata_model->get_by_user(get_staff_user_id(), 'staff');
        $data['is_active'] = isset($existing->is_active) ? 1 : 0;
        $this->load->view('setup', $data);
    }

    /**
     * Enrollment screen for admins to manage staff faces.
     */
    public function enrollment()
    {
        $data['title'] = _l('facelogin_enrollment');
        $faces = $this->facedata_model->get();
        $facesByUser = [];
        foreach ($faces as $face) {
            $facesByUser[$face['user_id']] = $face;
        }

        $staff = $this->db->select('s.staffid, s.firstname, s.lastname, s.email, s.active as staff_active, r.name as role_name, GROUP_CONCAT(DISTINCT d.name SEPARATOR ", ") as departments')
            ->from(db_prefix() . 'staff as s')
            ->join(db_prefix() . 'roles as r', 'r.roleid = s.role', 'left')
            ->join(db_prefix() . 'staff_departments as sd', 'sd.staffid = s.staffid', 'left')
            ->join(db_prefix() . 'departments as d', 'd.departmentid = sd.departmentid', 'left')
            ->group_by('s.staffid')
            ->order_by('s.staffid', 'asc')
            ->get()
            ->result_array();

        $roles = [];
        $departments = [];
        foreach ($staff as &$member) {
            $member['full_name'] = trim($member['firstname'] . ' ' . $member['lastname']);
            $member['has_face'] = isset($facesByUser[$member['staffid']]);
            if (!empty($member['role_name'])) {
                $roles[] = $member['role_name'];
            }
            if (!empty($member['departments'])) {
                foreach (explode(',', $member['departments']) as $dept) {
                    $departments[] = trim($dept);
                }
            }
        }
        unset($member);

        $data['staff_list'] = $staff;
        $data['roles_list'] = array_values(array_unique(array_filter($roles)));
        $data['dept_list'] = array_values(array_unique(array_filter($departments)));

        $this->load->view('enrollment', $data);
    }

    public function face_logs()
    {
        $this->ensure_logs_table();
        $data['title'] = _l('facelogin_face_logs');
        $data['logs'] = $this->db->select('l.*, CONCAT(s.firstname, " ", s.lastname) AS staff_name, s.email')
            ->from(db_prefix() . 'face_logs as l')
            ->join(db_prefix() . 'staff as s', 's.staffid = l.staff_id', 'left')
            ->order_by('l.id', 'desc')
            ->limit(300)
            ->get()
            ->result_array();
        $this->load->view('face_logs', $data);
    }

    public function clear_logs()
    {
        if (!is_admin()) {
            show_error('Not authorized', 403);
        }
        $this->ensure_logs_table();
        $this->db->truncate(db_prefix() . 'face_logs');
        set_alert('success', 'Face logs cleared');
        redirect(admin_url('facelogin/face_logs'));
    }

    /**
     * Manage public FaceLogin links for staff.
     */
    public function links()
    {
        if (!is_admin()) {
            show_error('Not authorized', 403);
        }

        if ($this->input->post('mode') === 'global') {
            $name = trim($this->input->post('global_link_name'));
            if ($name === '') {
                $name = 'All Staff';
            }
            $link = $this->facelink_model->upsert_global($name, get_staff_user_id());
            set_alert('success', 'Global link ready');
            redirect(admin_url('facelogin/links'));
        } elseif ($this->input->post()) {
            $staff_id = (int) $this->input->post('staff_id');
            $name = trim($this->input->post('link_name'));
            if ($staff_id && $name !== '') {
                $link = $this->facelink_model->create($staff_id, $name, get_staff_user_id());
                set_alert('success', 'Link created');
                redirect(admin_url('facelogin/links'));
            } else {
                set_alert('warning', 'Please choose staff and name the link.');
            }
        }

        $data['title'] = _l('facelogin_links');
        $data['links'] = $this->facelink_model->all();
        $data['global_link'] = $this->facelink_model->global_link();
        $data['staff'] = $this->db->select('staffid, firstname, lastname, email, active, role')
            ->from(db_prefix() . 'staff')
            ->order_by('firstname', 'asc')
            ->get()
            ->result_array();
        $this->load->view('links', $data);
    }

    public function toggle_link($id)
    {
        if (!is_admin()) {
            show_error('Not authorized', 403);
        }
        $active = $this->input->get('active') === '1';
        $this->facelink_model->toggle($id, $active);
        set_alert('success', 'Link updated');
        redirect(admin_url('facelogin/links'));
    }

    public function delete_link($id)
    {
        if (!is_admin()) {
            show_error('Not authorized', 403);
        }
        $this->facelink_model->delete($id);
        set_alert('success', 'Link removed');
        redirect(admin_url('facelogin/links'));
    }

    /**
     * Return stored face data for a staff member (admin only).
     */
    public function view_face($staff_id)
    {
        if (!is_admin()) {
            show_error('Not authorized', 403);
        }

        $record = $this->facedata_model->get_by_user($staff_id, 'staff');
        if (!$record) {
            echo json_encode(['status' => false, 'message' => 'No face data found.']);
            return;
        }

        $json_file = FACELOGIN_JSONSAVE_FOLDER . '/' . $record->uniq_id . '.json';
        if (!file_exists($json_file)) {
            echo json_encode(['status' => false, 'message' => 'Face data file missing.']);
            return;
        }

        $descriptor = json_decode(file_get_contents($json_file));
        $image_path = FACELOGIN_JSONSAVE_FOLDER . '/' . $record->uniq_id . '.png';
        $image_url = file_exists($image_path) ? module_dir_url('facelogin', 'faces/' . $record->uniq_id . '.png') : null;
        $image_mtime = file_exists($image_path) ? filemtime($image_path) : null;
        $image_size = file_exists($image_path) ? filesize($image_path) : null;
        echo json_encode([
            'status' => true,
            'uniq_id' => $record->uniq_id,
            'created_at' => $record->created_at,
            'updated_at' => $record->updated_at,
            'descriptor' => $descriptor,
            'image_url' => $image_url,
            'image_version' => $image_mtime,
            'image_size' => $image_size,
            'descriptor_len' => is_array($descriptor) ? count($descriptor) : 0,
        ]);
    }

    public function settings()
    {
        $data['title'] = _l('facelogin_settings');
        $data['login_btn_enabled'] = get_option('facelogin_login_btn_enabled');
        $data['checkin_on_login'] = get_option('facelogin_checkin_on_login');
        $data['checkin_on_face'] = get_option('facelogin_checkin_on_facelogin');
        $data['checkout_on_logout'] = get_option('facelogin_checkout_on_logout');
        $data['att_title_pattern'] = get_option('facelogin_att_title_pattern');
        $data['att_description'] = get_option('facelogin_att_description');
        $data['reuse_daily_task'] = get_option('facelogin_reuse_daily_task');
        $data['logout_mode'] = get_option('facelogin_logout_mode');
        $data['logout_label'] = get_option('facelogin_logout_label');
        $data['logout_statuses'] = get_option('facelogin_logout_statuses');
        $data['logout_modal_enabled'] = get_option('facelogin_logout_modal_enabled');
        $data['cooldown_minutes'] = get_option('facelogin_cooldown_minutes');
        if ($data['login_btn_enabled'] === '' || $data['login_btn_enabled'] === null) {
            add_option('facelogin_login_btn_enabled', '1');
            $data['login_btn_enabled'] = '1';
        }
        if ($data['checkin_on_login'] === '' || $data['checkin_on_login'] === null) {
            add_option('facelogin_checkin_on_login', '0');
            $data['checkin_on_login'] = '0';
        }
        if ($data['checkin_on_face'] === '' || $data['checkin_on_face'] === null) {
            add_option('facelogin_checkin_on_facelogin', '0');
            $data['checkin_on_face'] = '0';
        }
        if ($data['checkout_on_logout'] === '' || $data['checkout_on_logout'] === null) {
            add_option('facelogin_checkout_on_logout', '0');
            $data['checkout_on_logout'] = '0';
        }
        if ($data['att_title_pattern'] === '' || $data['att_title_pattern'] === null) {
            add_option('facelogin_att_title_pattern', 'Attendance|{staff_name}|{datetime}');
            $data['att_title_pattern'] = 'Attendance|{staff_name}|{datetime}';
        }
        if ($data['att_description'] === '' || $data['att_description'] === null) {
            add_option('facelogin_att_description', 'Auto punch-in via {method}. [FaceLogin Attendance]');
            $data['att_description'] = 'Auto punch-in via {method}. [FaceLogin Attendance]';
        }
        if ($data['reuse_daily_task'] === '' || $data['reuse_daily_task'] === null) {
            add_option('facelogin_reuse_daily_task', '1');
            $data['reuse_daily_task'] = '1';
        }
        if ($data['logout_mode'] === '' || $data['logout_mode'] === null) {
            add_option('facelogin_logout_mode', 'complete'); // complete|pause
            $data['logout_mode'] = 'complete';
        }
        if ($data['logout_label'] === '' || $data['logout_label'] === null) {
            add_option('facelogin_logout_label', 'On Break');
            $data['logout_label'] = 'On Break';
        }
        if ($data['logout_statuses'] === '' || $data['logout_statuses'] === null) {
            add_option('facelogin_logout_statuses', json_encode(['end_shift', 'on_break', 'on_lunch']));
            $data['logout_statuses'] = json_encode(['end_shift', 'on_break', 'on_lunch']);
        }
        if ($data['logout_modal_enabled'] === '' || $data['logout_modal_enabled'] === null) {
            add_option('facelogin_logout_modal_enabled', '1');
            $data['logout_modal_enabled'] = '1';
        }
        if ($data['cooldown_minutes'] === '' || $data['cooldown_minutes'] === null) {
            add_option('facelogin_cooldown_minutes', '5');
            $data['cooldown_minutes'] = '5';
        }
        $this->load->view('settings', $data);
    }

    public function save_face()
    {
        $data = $this->input->post(null, false); // do not XSS-filter to preserve base64

        $user_id = $data['user_id'];
        $face_json = $data['face_json'];
        $face_image = $data['face_image'] ?? null; // base64 data URL
        $user_type = 'staff';

        if (!is_admin() && (int) $user_id !== (int) get_staff_user_id()) {
            echo json_encode(['status' => false, 'message' => 'Not authorized']);
            return;
        }

        if (!is_dir(FACELOGIN_JSONSAVE_FOLDER)) {
            mkdir(FACELOGIN_JSONSAVE_FOLDER, 0755, true);
        }

        $existing = $this->facedata_model->get_by_user($user_id, $user_type);

        if ($existing) {
            $json_file = FACELOGIN_JSONSAVE_FOLDER . '/' . $existing->uniq_id . '.json';
            file_put_contents($json_file, $face_json);
            $this->save_face_image($existing->uniq_id, $face_image);

            // keep updated_at in sync for display
            $this->db->where('id', $existing->id);
            $this->db->update(db_prefix() . 'face_data', ['updated_at' => date('Y-m-d H:i:s')]);

            echo json_encode(['status' => 'updated', 'message' => 'Face data updated.']);
        } else {
            $uniq_id = time();
            $insert_data = [
                'user_id' => $user_id,
                'user_type' => $user_type,
                'uniq_id' => $uniq_id,
                'is_active' => 1,
                'created_by' => get_staff_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $insert_id = $this->facedata_model->add($insert_data);
            if ($insert_id) {
                file_put_contents(FACELOGIN_JSONSAVE_FOLDER . '/' . "$uniq_id.json", $face_json);
                $this->save_face_image($uniq_id, $face_image);
                echo json_encode(['status' => true, 'message' => 'Face data saved.']);
            } else {
                echo json_encode(['status' => false, 'message' => 'DB insert failed.']);
            }
        }
    }

    public function verify_face()
    {
        $data = $this->input->post();

        if (!isset($data['user_json']) || !isset($data['user_id'])) {
            echo json_encode(['status' => 'failed', 'message' => 'Invalid input']);
            return;
        }

        $user_id = $data['user_id'];
        $incomingDescriptor = json_decode($data['user_json']);

        $record = $this->facedata_model->get_by_user_id($user_id);

        if (!$record) {
            echo json_encode(['status' => 'failed', 'message' => 'No face data found for this user']);
            return;
        }

        $json_file = FACELOGIN_JSONSAVE_FOLDER . '/' . $record->uniq_id . ".json";

        if (!file_exists($json_file)) {
            echo json_encode(['status' => 'failed', 'message' => 'Face data file missing']);
            return;
        }

        $storedDescriptor = json_decode(file_get_contents($json_file));

        $distance = $this->calculateEuclideanDistance($incomingDescriptor, $storedDescriptor);
        $threshold = 0.5;

        if ($distance < $threshold) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Face not matched']);
        }
    }

    private function calculateEuclideanDistance($a, $b)
    {
        $sum = 0.0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }

    public function remove_face($staff_id = null)
    {
        $requestId = $this->input->post('staff_id');
        $targetId = $staff_id ?? $requestId ?? get_staff_user_id();
        $user_type = 'staff';

        if ($targetId !== get_staff_user_id() && !is_admin()) {
            show_error('Not authorized', 403);
        }

        $existing = $this->facedata_model->get_by_user($targetId, $user_type);

        if ($existing) {
            $json_file = FACELOGIN_JSONSAVE_FOLDER . '/' . $existing->uniq_id . '.json';
            $img_file = FACELOGIN_JSONSAVE_FOLDER . '/' . $existing->uniq_id . '.png';

            if (file_exists($json_file)) {
                unlink($json_file);
            }
            if (file_exists($img_file)) {
                unlink($img_file);
            }

            $this->db->where('id', $existing->id);
            $this->db->delete(db_prefix() . 'face_data');

            if ($staff_id || $requestId) {
                echo json_encode(['status' => 'success']);
                return;
            }

            set_alert('success', _l('Face data removed'));
        } else {
            if ($staff_id || $requestId) {
                echo json_encode(['status' => 'failed', 'message' => 'No face data found.']);
                return;
            }
            set_alert('danger', _l('No face data found.'));
        }

        if (!$staff_id) {
            redirect(admin_url('facelogin/setup'));
        }
    }

    public function save_settings()
    {
        if (!is_admin()) {
            show_error('Not authorized', 403);
        }

        $enabled = $this->input->post('login_btn_enabled') ? '1' : '0';
        $checkin_login = $this->input->post('checkin_on_login') ? '1' : '0';
        $checkin_face = $this->input->post('checkin_on_face') ? '1' : '0';
        $checkout_logout = $this->input->post('checkout_on_logout') ? '1' : '0';
        $att_title_pattern = trim($this->input->post('att_title_pattern'));
        $att_description = trim($this->input->post('att_description'));
        $reuse_daily_task = $this->input->post('reuse_daily_task') ? '1' : '0';
        $logout_mode = $this->input->post('logout_mode') ?: 'complete';
        $logout_label = trim($this->input->post('logout_label'));
        $logout_statuses = $this->input->post('logout_statuses') ?: [];
        $logout_modal_enabled = $this->input->post('logout_modal_enabled') ? '1' : '0';
        $cooldown_minutes = (int) ($this->input->post('cooldown_minutes') ?: 5);
        if ($cooldown_minutes < 0) {
            $cooldown_minutes = 0;
        }
        if (get_option('facelogin_login_btn_enabled') === '' || get_option('facelogin_login_btn_enabled') === null) {
            add_option('facelogin_login_btn_enabled', $enabled);
        } else {
            update_option('facelogin_login_btn_enabled', $enabled);
        }
        if (get_option('facelogin_checkin_on_login') === '' || get_option('facelogin_checkin_on_login') === null) {
            add_option('facelogin_checkin_on_login', $checkin_login);
        } else {
            update_option('facelogin_checkin_on_login', $checkin_login);
        }
        if (get_option('facelogin_checkin_on_facelogin') === '' || get_option('facelogin_checkin_on_facelogin') === null) {
            add_option('facelogin_checkin_on_facelogin', $checkin_face);
        } else {
            update_option('facelogin_checkin_on_facelogin', $checkin_face);
        }
        if (get_option('facelogin_checkout_on_logout') === '' || get_option('facelogin_checkout_on_logout') === null) {
            add_option('facelogin_checkout_on_logout', $checkout_logout);
        } else {
            update_option('facelogin_checkout_on_logout', $checkout_logout);
        }
        if (get_option('facelogin_att_title_pattern') === '' || get_option('facelogin_att_title_pattern') === null) {
            add_option('facelogin_att_title_pattern', $att_title_pattern);
        } else {
            update_option('facelogin_att_title_pattern', $att_title_pattern);
        }
        if (get_option('facelogin_att_description') === '' || get_option('facelogin_att_description') === null) {
            add_option('facelogin_att_description', $att_description);
        } else {
            update_option('facelogin_att_description', $att_description);
        }
        if (get_option('facelogin_reuse_daily_task') === '' || get_option('facelogin_reuse_daily_task') === null) {
            add_option('facelogin_reuse_daily_task', $reuse_daily_task);
        } else {
            update_option('facelogin_reuse_daily_task', $reuse_daily_task);
        }
        if (get_option('facelogin_logout_mode') === '' || get_option('facelogin_logout_mode') === null) {
            add_option('facelogin_logout_mode', $logout_mode);
        } else {
            update_option('facelogin_logout_mode', $logout_mode);
        }
        if (get_option('facelogin_logout_label') === '' || get_option('facelogin_logout_label') === null) {
            add_option('facelogin_logout_label', $logout_label);
        } else {
            update_option('facelogin_logout_label', $logout_label);
        }
        $statuses_clean = [];
        foreach ((array) $logout_statuses as $st) {
            $st = trim($st);
            if ($st !== '') {
                $statuses_clean[] = $st;
            }
        }
        if (empty($statuses_clean)) {
            $statuses_clean = ['end_shift', 'on_break', 'on_lunch'];
        }
        if (get_option('facelogin_logout_statuses') === '' || get_option('facelogin_logout_statuses') === null) {
            add_option('facelogin_logout_statuses', json_encode($statuses_clean));
        } else {
            update_option('facelogin_logout_statuses', json_encode($statuses_clean));
        }
        if (get_option('facelogin_logout_modal_enabled') === '' || get_option('facelogin_logout_modal_enabled') === null) {
            add_option('facelogin_logout_modal_enabled', $logout_modal_enabled);
        } else {
            update_option('facelogin_logout_modal_enabled', $logout_modal_enabled);
        }
        if (get_option('facelogin_cooldown_minutes') === '' || get_option('facelogin_cooldown_minutes') === null) {
            add_option('facelogin_cooldown_minutes', (string) $cooldown_minutes);
        } else {
            update_option('facelogin_cooldown_minutes', (string) $cooldown_minutes);
        }

        set_alert('success', _l('settings_updated'));
        redirect(admin_url('facelogin/settings'));
    }

    /**
     * AJAX: apply logout status and stop/pause attendance.
     */
    public function apply_logout_status()
    {
        if (!is_staff_logged_in()) {
            show_error('Not authorized', 403);
        }
        $status = $this->input->post('status', true);
        $allowed = facelogin_get_selected_logout_statuses();
        if (!$status || !in_array($status, $allowed)) {
            echo json_encode(['status' => false, 'message' => 'Invalid status']);
            return;
        }

        $label_map = [
            'on_break' => get_option('facelogin_logout_label') ?: 'On Break',
            'on_lunch' => 'On Lunch',
            'in_meeting' => 'In Meeting',
        ];

        switch ($status) {
            case 'end_shift':
                facelogin_close_attendance_entry(get_staff_user_id(), 'complete');
                break;
            case 'on_break':
            case 'on_lunch':
            case 'in_meeting':
                $lbl = $label_map[$status] ?? ucfirst(str_replace('_', ' ', $status));
                facelogin_close_attendance_entry(get_staff_user_id(), 'pause', $lbl);
                break;
            default:
                facelogin_close_attendance_entry(get_staff_user_id());
        }

        echo json_encode(['status' => true]);
    }

    /**
     * Save the face image (base64) alongside the descriptor.
     */
    private function save_face_image($uniq_id, $face_image_data)
    {
        if (!$face_image_data) {
            return;
        }

        if (!is_dir(FACELOGIN_JSONSAVE_FOLDER)) {
            mkdir(FACELOGIN_JSONSAVE_FOLDER, 0755, true);
        }

        // Expect data URL: data:image/png;base64,xxxx
        if (strpos($face_image_data, 'base64,') === false) {
            return;
        }

        [$meta, $content] = explode('base64,', $face_image_data, 2);
        $binary = base64_decode($content);
        if ($binary === false) {
            return;
        }

        $img_path = FACELOGIN_JSONSAVE_FOLDER . '/' . $uniq_id . '.png';
        file_put_contents($img_path, $binary);
    }

    private $logs_table_ready = false;
    private function ensure_logs_table()
    {
        if ($this->logs_table_ready) {
            return;
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "face_logs` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `staff_id` INT(11) DEFAULT NULL,
                `status` ENUM('success','failed') NOT NULL DEFAULT 'failed',
                `message` VARCHAR(255) DEFAULT NULL,
                `ip_address` VARCHAR(45) DEFAULT NULL,
                `user_agent` TEXT DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX (`staff_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->logs_table_ready = true;
    }

    /**
     * Backward compatibility for legacy route.
     */
    public function setting()
    {
        redirect(admin_url('facelogin/settings'));
    }
    public function heartbeat()
    {
        if (!is_staff_logged_in()) {
            return;
        }

        $active = (int) $this->input->post('active_seconds');
        $idle = (int) $this->input->post('idle_seconds');
        $staff_id = get_staff_user_id();
        $date = date('Y-m-d');

        if ($active === 0 && $idle === 0) {
            return;
        }

        // Upsert logic
        $sql = "INSERT INTO " . db_prefix() . "face_activity_log (staff_id, date, active_seconds, idle_seconds, last_heartbeat)
                VALUES (?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                active_seconds = active_seconds + VALUES(active_seconds),
                idle_seconds = idle_seconds + VALUES(idle_seconds),
                last_heartbeat = NOW()";

        $this->db->query($sql, [$staff_id, $date, $active, $idle]);
    }

    public function activity_log()
    {
        if (!is_admin()) {
            show_error('Not authorized', 403);
        }

        $data['title'] = 'Activity Log';

        $this->load->model('staff_model');
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);

        $where = [];
        if ($this->input->get('staff_id')) {
            $where['staff_id'] = $this->input->get('staff_id');
        }
        if ($this->input->get('from')) {
            $where['date >='] = to_sql_date($this->input->get('from'));
        }
        if ($this->input->get('to')) {
            $where['date <='] = to_sql_date($this->input->get('to'));
        }

        $this->db->select('l.*, CONCAT(s.firstname, " ", s.lastname) as staff_name')
            ->from(db_prefix() . 'face_activity_log l')
            ->join(db_prefix() . 'staff s', 's.staffid = l.staff_id', 'left')
            ->order_by('l.date', 'DESC')
            ->order_by('l.last_heartbeat', 'DESC');

        if (!empty($where)) {
            $this->db->where($where);
        }

        $data['logs'] = $this->db->get()->result_array();

        $this->load->view('activity_log', $data);
    }
}
