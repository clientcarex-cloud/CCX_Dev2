<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facelink extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        load_admin_language();
        if (function_exists('facelogin_ensure_tables')) {
            facelogin_ensure_tables();
        }
        $this->load->model(['facelink_model', 'facedata_model']);
    }

    /**
     * Public link page for face punch in/out.
     */
    public function index($token = null)
    {
        if (!$token) {
            show_404();
        }

        $link = $this->facelink_model->by_token($token);
        if (!$link) {
            show_error('Link not found or inactive.', 404);
        }

        $staff = null;
        if (empty($link->is_global)) {
            $staff = $this->db->select('s.staffid, s.firstname, s.lastname, s.role, s.active, r.name as role_name')
                ->from(db_prefix() . 'staff as s')
                ->join(db_prefix() . 'roles as r', 'r.roleid = s.role', 'left')
                ->where('s.staffid', $link->staff_id)
                ->limit(1)
                ->get()
                ->row_array();
        }

        $data['link'] = $link;
        $data['staff'] = $staff;
        $data['title'] = 'Face Punch Link';
        $this->load->view('public_link', $data);
    }

    /**
     * AJAX: verify face against link and toggle attendance.
     */
    public function verify()
    {
        $token = $this->input->post('token', true);
        $descriptor = json_decode($this->input->post('user_json') ?? '', true);

        if (!$token || !is_array($descriptor)) {
            echo json_encode(['status' => 'failed', 'message' => 'Invalid data']);
            return;
        }

        $link = $this->facelink_model->by_token($token);
        if (!$link) {
            echo json_encode(['status' => 'failed', 'message' => 'Link inactive or missing']);
            return;
        }

        $match = $this->match_face($descriptor, $link);
        if (!$match) {
            echo json_encode(['status' => 'failed', 'message' => 'Face not matched']);
            return;
        }
        if (isset($match['error'])) {
            echo json_encode(['status' => 'failed', 'message' => $match['error']]);
            return;
        }

        $staff_id = $match['staff_id'];

        // Cooldown to avoid rapid re-punch
        if ($this->is_on_cooldown($staff_id)) {
            $this->log_attempt('failed', $staff_id, 'Cooldown');
            echo json_encode(['status' => 'failed', 'message' => 'Attendance already marked recently. Please try again in a few minutes.']);
            return;
        }

        $this->facelink_model->touch($link->id);

        $action = 'checked_in';
        if (function_exists('facelogin_attendance_available') && facelogin_attendance_available()) {
            $open = facelogin_find_open_attendance_task($staff_id);
            if ($open) {
                facelogin_close_attendance_entry($staff_id, 'complete');
                $action = 'checked_out';
            } else {
                facelogin_create_attendance_entry($staff_id, 'FaceLink');
                $action = 'checked_in';
            }
        }

        $this->log_attempt('success', $staff_id, $action);

        echo json_encode([
            'status'     => 'success',
            'action'     => $action,
            'staff_name' => $match['name'],
            'role'       => $match['role'],
            'time'       => date('Y-m-d H:i:s'),
        ]);
    }

    private function euclideanDistance($a, $b)
    {
        $sum = 0.0;
        $len = min(count($a), count($b));
        for ($i = 0; $i < $len; $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }

    private function match_face(array $incoming, $link)
    {
        $threshold = 0.6;

        if (empty($link->is_global)) {
            $face = $this->facedata_model->get_by_user_id($link->staff_id);
            if (!$face) {
                $this->log_attempt('failed', $link->staff_id, 'No face data');
                return null;
            }
            $stored = $this->load_descriptor($face->uniq_id);
            if (!$stored) {
                $this->log_attempt('failed', $link->staff_id, 'Descriptor missing/invalid');
                return null;
            }
            $distance = $this->euclideanDistance($incoming, $stored);
            if ($distance >= $threshold) {
                $this->log_attempt('failed', $link->staff_id, 'Face not matched');
                return null;
            }
            $staff = $this->fetch_staff($link->staff_id);
            if (!$staff['active']) {
                $this->log_attempt('failed', $link->staff_id, 'Staff inactive');
                return ['error' => 'Staff inactive'];
            }
            return [
                'staff_id' => $link->staff_id,
                'name'     => $staff['name'],
                'role'     => $staff['role'],
            ];
        }

        // Global link: find best match across all active faces
        $faces = $this->db->select('fd.user_id, fd.uniq_id, s.firstname, s.lastname, s.active as staff_active, r.name as role_name')
            ->from(db_prefix() . 'face_data as fd')
            ->join(db_prefix() . 'staff as s', 's.staffid = fd.user_id', 'left')
            ->join(db_prefix() . 'roles as r', 'r.roleid = s.role', 'left')
            ->where('fd.user_type', 'staff')
            ->where('fd.is_active', 1)
            ->where('s.active', 1)
            ->get()
            ->result_array();

        $best = null;
        foreach ($faces as $face) {
            $stored = $this->load_descriptor($face['uniq_id']);
            if (!$stored) {
                continue;
            }
            $distance = $this->euclideanDistance($incoming, $stored);
            if ($distance < $threshold && ($best === null || $distance < $best['distance'])) {
            $best = [
                'staff_id' => (int) $face['user_id'],
                'name'     => trim(($face['firstname'] ?? '') . ' ' . ($face['lastname'] ?? '')),
                'role'     => $face['role_name'] ?? '',
                'distance' => $distance,
            ];
            }
        }

        if ($best) {
            return $best;
        }

        $this->log_attempt('failed', null, 'Face not matched');
        return null;
    }

    private function load_descriptor($uniq_id)
    {
        $json_file = FACELOGIN_JSONSAVE_FOLDER . '/' . $uniq_id . '.json';
        if (!file_exists($json_file)) {
            return null;
        }
        $stored = json_decode(file_get_contents($json_file), true);
        return is_array($stored) ? $stored : null;
    }

    private function fetch_staff($staff_id)
    {
        $row = $this->db->select('s.firstname, s.lastname, s.active, r.name as role_name')
            ->from(db_prefix() . 'staff as s')
            ->join(db_prefix() . 'roles as r', 'r.roleid = s.role', 'left')
            ->where('s.staffid', $staff_id)
            ->limit(1)
            ->get()
            ->row_array();
        return [
            'name' => $row ? trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) : '',
            'role' => $row['role_name'] ?? '',
            'active' => isset($row['active']) ? (int) $row['active'] === 1 : false,
        ];
    }

    private function is_on_cooldown($staff_id)
    {
        $cooldown = (int) get_option('facelogin_cooldown_minutes');
        if ($cooldown <= 0) {
            return false;
        }
        $window = date('Y-m-d H:i:s', time() - ($cooldown * 60));
        $row = $this->db->select('id')
            ->from(db_prefix() . 'face_logs')
            ->where('staff_id', $staff_id)
            ->where('status', 'success')
            ->where('created_at >=', $window)
            ->order_by('id', 'desc')
            ->limit(1)
            ->get()
            ->row_array();
        return (bool) $row;
    }

    private $logs_table_ready = false;
    private function log_attempt($status, $user_id = null, $message = '')
    {
        $this->ensure_logs_table();
        $ua = $this->input->user_agent();
        $this->db->insert(db_prefix() . 'face_logs', [
            'staff_id'   => $user_id,
            'status'     => $status,
            'message'    => $message,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $ua ? substr($ua, 0, 500) : '',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

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
}
