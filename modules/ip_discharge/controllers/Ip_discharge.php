<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ip_discharge extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Ensure table exists (Temporary dev check, ideally handled by migration)
        if (!$this->db->table_exists(db_prefix() . 'discharge_summaries')) {
            require_once(module_dir_path('ip_discharge', 'install.php'));
        }
    }

    public function index()
    {
        if (!has_permission('ip_discharge', '', 'view')) {
            access_denied('ip_discharge');
        }

        $data['title'] = 'IP Discharge';
        // Load initial visits for the list (e.g., last 50)
        $this->db->select(db_prefix() . 'visits.*, ' . db_prefix() . 'clients.userid as patient_user_id, ' . db_prefix() . 'clients.company as patient_name, ' . db_prefix() . 'patients_extra.mr_number, ' . db_prefix() . 'patients_extra.age, ' . db_prefix() . 'patients_extra.gender, ' . db_prefix() . 'patients_extra.mobile_number, (SELECT CONCAT(firstname, " ", lastname) FROM ' . db_prefix() . 'staff WHERE staffid = ' . db_prefix() . 'visits.primary_doctor_id) as doctor_name');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'clients.userid', 'left');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(50);
        $data['visits'] = $this->db->get(db_prefix() . 'visits')->result_array();

        $this->load->view('manage', $data);
    }

    public function get_visit_details($visit_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->db->select(db_prefix() . 'visits.*, ' . db_prefix() . 'clients.userid as patient_user_id, ' . db_prefix() . 'clients.company as patient_name, ' . db_prefix() . 'patients_extra.mr_number, ' . db_prefix() . 'patients_extra.age, ' . db_prefix() . 'patients_extra.gender, ' . db_prefix() . 'patients_extra.mobile_number, ' . db_prefix() . 'patients_extra.attender_name as family_head_name, (SELECT CONCAT(firstname, " ", lastname) FROM ' . db_prefix() . 'staff WHERE staffid = ' . db_prefix() . 'visits.primary_doctor_id) as doctor_name');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'clients.userid', 'left');
        $this->db->where(db_prefix() . 'visits.id', $visit_id);
        $visit = $this->db->get(db_prefix() . 'visits')->row_array();

        if (!$visit) {
            echo json_encode(['success' => false, 'message' => 'Visit not found']);
            return;
        }

        // Fetch existing summary if any
        $this->db->where('visit_id', $visit_id);
        $summary = $this->db->get(db_prefix() . 'discharge_summaries')->row_array();
        $visit['summary'] = $summary ? $summary['summary_content'] : '';

        // Render the details view
        $html = $this->load->view('visit_details', ['visit' => $visit], true);

        echo json_encode(['success' => true, 'html' => $html]);
    }

    public function save_discharge_summary()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $visit_id = $this->input->post('visit_id');
        $patient_id = $this->input->post('patient_id');
        $content = $this->input->post('content');

        if (!$visit_id || !$patient_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }

        $data = [
            'visit_id' => $visit_id,
            'patient_id' => $patient_id,
            'summary_content' => $content,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Check if exists
        $this->db->where('visit_id', $visit_id);
        $exists = $this->db->get(db_prefix() . 'discharge_summaries')->row();

        if ($exists) {
            $this->db->where('id', $exists->id);
            $this->db->update(db_prefix() . 'discharge_summaries', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert(db_prefix() . 'discharge_summaries', $data);
        }

        echo json_encode(['success' => true, 'message' => 'Discharge summary saved successfully.']);
    }
}
