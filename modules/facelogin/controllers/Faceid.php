<?php

defined('BASEPATH') or exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');

class Faceid extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        load_admin_language();
        $this->load->model('Authentication_model');
    }


    public function index()
    {
        $post = $this->input->post();
        $descriptor = json_decode($post['user_json'] ?? '', true);

        if (!$descriptor) {
            $this->log_attempt('failed', null, 'Invalid data');
            echo json_encode(['status' => 'failed', 'msg' => 'Invalid data']);
            return;
        }

        $this->load->model('facedata_model');
        $faceDataList = $this->facedata_model->get();
        
        

        foreach ($faceDataList as $data) {
            $jsonPath = FACELOGIN_JSONSAVE_FOLDER .'/'. $data['uniq_id'] . '.json';
            if (!file_exists($jsonPath)) {
                continue;
            }
            $dbDescriptor = json_decode(file_get_contents($jsonPath), true);
            $similarity = $this->euclideanDistance($descriptor, $dbDescriptor);
            if ($similarity < 0.6) {
                $this->process_login($data['user_id']);
                $this->log_attempt('success', $data['user_id'], 'Matched');
                echo json_encode(['status' => 'success']);
                return;
            }
        }

        $this->log_attempt('failed', null, 'Face not matched');
        echo json_encode(['status' => 'failed']);
    }

    private function process_login($staff_id)
    {
        // Flag to differentiate face login for auto check-in logic
        $this->session->set_userdata('facelogin_via_face', true);

        $this->session->set_userdata([
            'staff_user_id'   => $staff_id,
            'staff_logged_in' => true,
        ]);

        $this->load->model('announcements_model');
        $this->announcements_model->set_announcements_as_read_except_last_one($staff_id, true);

        $this->update_login_info($staff_id);
        $this->create_autologin($staff_id, true);

        hooks()->do_action('after_staff_login');
    }

    private function update_login_info($user_id)
    {
        $this->db->set('last_ip', $this->input->ip_address());
        $this->db->set('last_login', date('Y-m-d H:i:s'));
        $this->db->where('staffid', $user_id);
        $this->db->update(db_prefix() . 'staff');
    }

    private function create_autologin($user_id, $staff)
    {
        $this->load->helper('cookie');
        $key = substr(md5(uniqid(rand())), 0, 16);
        $this->user_autologin->delete($user_id, $key, $staff);

        if ($this->user_autologin->set($user_id, md5($key), $staff)) {
            set_cookie([
                'name'   => 'autologin',
                'value'  => serialize(['user_id' => $user_id, 'key' => $key]),
                'expire' => 60 * 60 * 24 * 31 * 2, // 2 months
            ]);
        }
    }
    private function euclideanDistance($d1, $d2) {
        $sum = 0;
        for ($i = 0; $i < count($d1); $i++) {
          $sum += pow($d1[$i] - $d2[$i], 2);
        }
        return sqrt($sum);
      }

    private $logs_table_ready = false;

    private function log_attempt($status, $user_id = null, $message = '')
    {
        $this->ensure_logs_table();

        $ua = $this->input->user_agent();
        $data = [
            'staff_id'   => $user_id,
            'status'     => $status,
            'message'    => $message,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $ua ? substr($ua, 0, 500) : '',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'face_logs', $data);
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
