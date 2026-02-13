<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pndt extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pndt_model');
        $this->load->helper('form');
    }

    public function index()
    {
        $filters = [
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date'),
            'status' => $this->input->get('status') ? $this->input->get('status') : 'All'
        ];

        // Fetch Data
        $data['requests'] = $this->pndt_model->get_pndt_visits($filters);

        // Filter Data Lists
        // Filter Data Lists
        $data['filters'] = $filters;
        $data['selected_status'] = $filters['status'];
        $data['title'] = 'PNDT';
        $this->load->view('manage', $data);
    }

    public function form($id = '')
    {
        $data['title'] = 'Pre-Natal Diagnostic Techniques Form';

        if ($id) {
            $data['request'] = $this->pndt_model->get_request($id);
            if (!$data['request']) {
                show_404();
            }
        }

        $this->load->view('pndt_form', $data);
    }

    public function print_form($id = '')
    {
        if (!$id) {
            show_404();
        }
        $data['title'] = 'Print PNDT Form';
        $data['request'] = $this->pndt_model->get_request($id);

        if (!$data['request']) {
            show_404();
        }

        $this->load->view('print_form', $data);
    }

    public function settings()
    {
        $data['title'] = 'PNDT Settings';
        $this->load->model('staff_model');
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['pndt_doctors'] = get_option('pndt_selected_doctors');
        $this->load->view('settings', $data);
    }

    public function save_settings()
    {
        if ($this->input->post()) {
            $doctors = $this->input->post('pndt_doctors');
            // Check if it's already a JSON string or array. If array, encode it.
            // Actually input->post usually returns array if name="pndt_doctors[]" but we might send a json string.
            // Let's assume we send a JSON string from the frontend for simplicity of handling nested data (license/phone).

            update_option('pndt_selected_doctors', $doctors);
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
        }
    }

    public function save()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            // Check if csrf token is in data causing issues with model? usually not, but good to clean
            if (isset($data['csrf_token_name'])) {
                unset($data['csrf_token_name']);
            }

            try {
                $id = $this->pndt_model->save_pndt_form($data);
                if ($id) {
                    echo json_encode(['success' => true, 'message' => 'Form saved successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to save form']);
                }
            } catch (Exception $e) {
                log_message('error', 'PNDT Save Error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
            }
        }
    }

    public function fix_db()
    {
        $CI = &get_instance();
        $created = false;

        if (!$CI->db->table_exists(db_prefix() . 'pndt')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'pndt` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `patient_test_id` int(11) NOT NULL,
              `staff_id` int(11) NOT NULL,
              `content` longtext DEFAULT NULL,
              `status` varchar(50) DEFAULT "Draft",
              `created_at` datetime DEFAULT current_timestamp(),
              `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            echo "Table tblpndt created.<br>";
            $created = true;
        } else {
            echo "Table tblpndt already exists checking for columns...<br>";
            if (!$CI->db->field_exists('updated_at', db_prefix() . 'pndt')) {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'pndt` ADD `updated_at` datetime DEFAULT NULL');
                echo "Column updated_at added.<br>";
                $created = true;
            }
            if (!$CI->db->field_exists('created_at', db_prefix() . 'pndt')) {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'pndt` ADD `created_at` datetime DEFAULT NULL');
                echo "Column created_at added.<br>";
                $created = true;
            }
            if (!$CI->db->field_exists('status', db_prefix() . 'pndt')) {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'pndt` ADD `status` varchar(50) DEFAULT "Draft"');
                echo "Column status added.<br>";
                $created = true;
            }
        }

        if ($CI->db->table_exists(db_prefix() . 'patients_extra')) {
            if (!$CI->db->field_exists('father_husband_name', db_prefix() . 'patients_extra')) {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `father_husband_name` VARCHAR(150) DEFAULT NULL AFTER `mr_number`');
                echo "Column father_husband_name added to tblpatients_extra.<br>";
                $created = true;
            } else {
                echo "Column father_husband_name already exists.<br>";
            }
        }

        echo $created ? "<b>Database fixed! You can now use the form.</b>" : "Database is already correct.";
    }

    // Unused methods removed

}
