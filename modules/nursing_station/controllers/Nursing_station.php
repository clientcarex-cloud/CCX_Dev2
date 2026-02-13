<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Nursing_station extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Ensure table exists or standard module requires
    }

    public function index()
    {
        if (!has_permission('nursing_station', '', 'view')) {
            access_denied('nursing_station');
        }

        $data['title'] = 'Nursing Station';

        // Load initial visits for the list (e.g., last 50) - Same logic as IP Discharge
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

        // Render the details view
        $html = $this->load->view('visit_details', ['visit' => $visit], true);

        echo json_encode(['success' => true, 'html' => $html]);
    }
}
