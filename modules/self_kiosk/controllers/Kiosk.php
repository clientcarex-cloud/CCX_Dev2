<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kiosk extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('patients/patients_model');
        $this->load->model('self_kiosk_model');
        $this->lang->load(SELF_KIOSK_MODULE_NAME . '/self_kiosk');
    }

    public function go($slug)
    {
        $qr_code = $this->self_kiosk_model->get_by_slug($slug);

        if (!$qr_code) {
            show_404();
        }

        // Store source/context if needed
        $this->session->set_userdata('kiosk_qr_source', $qr_code->id);

        redirect(site_url('self_kiosk/kiosk'));
    }

    public function index()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->has_userdata('kiosk_patient_id')) {
            redirect(site_url('self_kiosk/kiosk/dashboard'));
        }

        $this->load->view('self_kiosk/kiosk/login');
    }

    public function login()
    {
        $mobile = $this->input->post('mobile');
        if (!$mobile) {
            set_alert('danger', _l('please_enter_mobile'));
            redirect(site_url('self_kiosk/kiosk'));
        }

        // Search for patient by mobile number
        $patients = $this->patients_model->get_patient_by_mobile($mobile);

        if (count($patients) === 1) {
            // Single patient found
            $this->session->set_userdata('kiosk_patient_id', $patients[0]['userid']);
            redirect(site_url('self_kiosk/kiosk/dashboard'));
        } elseif (count($patients) > 1) {
            // Multiple patients found
            $this->session->set_userdata('kiosk_found_patients', $patients);
            redirect(site_url('self_kiosk/kiosk/select_patient'));
        } else {
            set_alert('danger', _l('patient_not_found_mobile'));
            redirect(site_url('self_kiosk/kiosk'));
        }
    }

    public function select_patient()
    {
        if (!$this->session->has_userdata('kiosk_found_patients')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $data['patients'] = $this->session->userdata('kiosk_found_patients');
        $data['title'] = _l('select_family_member');

        $this->load->view('self_kiosk/kiosk/select_patient', $data);
    }

    public function do_select_patient($patient_id)
    {
        if (!$this->session->has_userdata('kiosk_found_patients')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $found_patients = $this->session->userdata('kiosk_found_patients');
        $valid = false;

        foreach ($found_patients as $p) {
            if ($p['userid'] == $patient_id) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            set_alert('danger', 'Invalid patient selection.');
            redirect(site_url('self_kiosk/kiosk'));
        }

        $this->session->set_userdata('kiosk_patient_id', $patient_id);
        $this->session->unset_userdata('kiosk_found_patients');

        redirect(site_url('self_kiosk/kiosk/dashboard'));
    }

    public function dashboard()
    {
        if (!$this->session->has_userdata('kiosk_patient_id')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $patient_id = $this->session->userdata('kiosk_patient_id');
        $patient = $this->patients_model->get($patient_id);

        if (!$patient) {
            $this->session->unset_userdata('kiosk_patient_id');
            redirect(site_url('self_kiosk/kiosk'));
        }

        $data['patient'] = $patient;
        $data['title'] = _l('kiosk_dashboard');

        // Check for other family members sharing the same mobile
        $mobile = $patient->phonenumber;
        $data['family_members'] = [];
        if (!empty($mobile)) {
            $family = $this->patients_model->get_patient_by_mobile($mobile);
            if (count($family) > 1) {
                $data['family_members'] = $family;
            }
        }

        // Load QR settings
        $data['settings'] = [];
        $source_id = $this->session->userdata('kiosk_qr_source');
        if ($source_id) {
            $qr_code = $this->self_kiosk_model->get($source_id);
            if ($qr_code && !empty($qr_code->settings)) {
                $data['settings'] = json_decode($qr_code->settings, true);
            }
        }

        $this->load->view('self_kiosk/kiosk/dashboard', $data);
    }

    public function profile()
    {
        if (!$this->session->has_userdata('kiosk_patient_id')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $patient_id = $this->session->userdata('kiosk_patient_id');
        $patient = $this->patients_model->get($patient_id);

        if (!$patient) {
            $this->session->unset_userdata('kiosk_patient_id');
            redirect(site_url('self_kiosk/kiosk'));
        }

        $settings = $this->get_kiosk_settings();
        $can_edit = !isset($settings['update_info']) || (isset($settings['update_info']['edit']) && $settings['update_info']['edit'] == "1");

        $data['patient'] = $patient;
        $data['title'] = $can_edit ? _l('update_pt_info') : _l('view_pt_info');
        $data['attender_titles'] = $this->patients_model->get_name_care_titles();
        $data['settings'] = $settings;

        $this->load->view('self_kiosk/kiosk/services/update_info', $data);
    }

    public function update_profile()
    {
        if (!$this->session->has_userdata('kiosk_patient_id')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $settings = $this->get_kiosk_settings();
        $can_edit = !isset($settings['update_info']) || (isset($settings['update_info']['edit']) && $settings['update_info']['edit'] == "1");

        if (!$can_edit) {
            set_alert('danger', 'You do not have permission to edit your profile.');
            redirect(site_url('self_kiosk/kiosk/profile'));
        }

        $patient_id = $this->session->userdata('kiosk_patient_id');
        $patient = $this->patients_model->get($patient_id);

        $post_data = $this->input->post();

        // Security: Prevent updating Name and Mobile via POST manually
        $data = [
            'full_name' => $patient->full_name,
            'mobile_number' => $patient->phonenumber,
            'age' => $post_data['age'],
            'age_unit' => $post_data['age_unit'],
            'email' => $post_data['email'],
            'uid_no' => $post_data['uid_no'],
            'attender_title_id' => $post_data['attender_title_id'],
            'attender_name' => $post_data['attender_name'],
            'address' => $post_data['address'],
        ];

        if ($this->patients_model->update($data, $patient_id)) {
            set_alert('success', _l('update_success'));
        } else {
            set_alert('danger', _l('update_fail'));
        }

        redirect(site_url('self_kiosk/kiosk/profile'));
    }

    public function feedback()
    {
        if (!$this->session->has_userdata('kiosk_patient_id')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $patient_id = $this->session->userdata('kiosk_patient_id');
        $patient = $this->patients_model->get($patient_id);

        if (!$patient) {
            $this->session->unset_userdata('kiosk_patient_id');
            redirect(site_url('self_kiosk/kiosk'));
        }

        $settings = $this->get_kiosk_settings();

        // Check daily limit
        $this->db->where('patient_id', $patient_id);
        $this->db->where('DATE(date_created)', date('Y-m-d'));
        $data['submission_count'] = $this->db->count_all_results(db_prefix() . 'self_kiosk_feedback');
        $data['can_submit'] = $data['submission_count'] < 2;

        $this->load->view('self_kiosk/kiosk/services/feedback', $data);
    }

    public function submit_feedback()
    {
        if (!$this->session->has_userdata('kiosk_patient_id')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $patient_id = $this->session->userdata('kiosk_patient_id');

        // Enforcement: Check daily limit on backend
        $this->db->where('patient_id', $patient_id);
        $this->db->where('DATE(date_created)', date('Y-m-d'));
        $count = $this->db->count_all_results(db_prefix() . 'self_kiosk_feedback');

        if ($count >= 2) {
            set_alert('danger', 'You have reached the daily limit for feedback submissions.');
            redirect(site_url('self_kiosk/kiosk/dashboard'));
        }

        $patient = $this->patients_model->get($patient_id);
        $qr_source = $this->session->userdata('kiosk_qr_source');

        $rating = $this->input->post('rating');
        $message = $this->input->post('message');

        if (empty($rating)) {
            set_alert('danger', 'Please provide a star rating.');
            redirect(site_url('self_kiosk/kiosk/feedback'));
        }

        $feedback_data = [
            'patient_id' => $patient_id,
            'patient_name' => $patient->full_name,
            'mobile_number' => $patient->phonenumber,
            'rating' => $rating,
            'message' => $message,
            'qr_code_id' => $qr_source,
            'date_created' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . 'self_kiosk_feedback', $feedback_data);

        set_alert('success', _l('feedback_submit_success'));
        redirect(site_url('self_kiosk/kiosk/dashboard'));
    }

    public function appointments()
    {
        if (!$this->session->has_userdata('kiosk_patient_id')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $this->load->model('appointments/appointments_model');
        $patient_id = $this->session->userdata('kiosk_patient_id');
        $patient = $this->patients_model->get($patient_id);

        if (!$patient) {
            $this->session->unset_userdata('kiosk_patient_id');
            redirect(site_url('self_kiosk/kiosk'));
        }

        // Fetch all appointments for this patient
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('appointment_date', 'desc');
        $this->db->order_by('start_time', 'desc');
        $all_appointments = $this->db->get(db_prefix() . 'appointments')->result_array();

        $data['active_appointments'] = [];
        $data['past_appointments'] = [];

        $today = date('Y-m-d');
        foreach ($all_appointments as $appt) {
            if (($appt['status'] == 'pending' || $appt['status'] == 'confirmed') && $appt['appointment_date'] >= $today) {
                $data['active_appointments'][] = $appt;
            } else {
                $data['past_appointments'][] = $appt;
            }
        }

        $data['doctors'] = $this->appointments_model->get_doctors();
        $data['patient'] = $patient;
        $data['title'] = _l('your_appointments');
        $data['settings'] = $this->get_kiosk_settings();

        $this->load->view('self_kiosk/kiosk/services/appointments', $data);
    }

    public function get_slots()
    {
        if (!$this->session->has_userdata('kiosk_patient_id')) {
            echo json_encode([]);
            return;
        }

        $this->load->model('appointments/appointments_model');
        $doctor_id = $this->input->get('doctor_id');
        $date = $this->input->get('date');

        if (!$doctor_id || !$date) {
            echo json_encode([]);
            return;
        }

        $slots = $this->appointments_model->get_doctor_slots($doctor_id, $date);
        echo json_encode($slots);
    }

    public function book_appointment()
    {
        if (!$this->session->has_userdata('kiosk_patient_id')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $this->load->model('appointments/appointments_model');
        $patient_id = $this->session->userdata('kiosk_patient_id');

        $data = $this->input->post();
        $data['patient_id'] = $patient_id;
        $data['notes'] = 'Booked via Self Kiosk';
        $data['status'] = 'pending'; // Default status for kiosk bookings
        $data['appointment_type'] = 'Unpaid'; // Default to Unpaid for kiosk

        // Security: unset any sensitive fields if they were spoofed
        unset($data['id']);

        $result = $this->appointments_model->add($data);

        if (is_numeric($result)) {
            set_alert('success', _l('appointment_booked_success'));
        } else {
            set_alert('danger', _l($result));
        }

        redirect(site_url('self_kiosk/kiosk/appointments'));
    }

    public function visits()
    {
        if (!$this->session->has_userdata('kiosk_patient_id')) {
            redirect(site_url('self_kiosk/kiosk'));
        }

        $patient_id = $this->session->userdata('kiosk_patient_id');
        $patient = $this->patients_model->get($patient_id);

        if (!$patient) {
            $this->session->unset_userdata('kiosk_patient_id');
            redirect(site_url('self_kiosk/kiosk'));
        }

        $filters = ['patient_id' => $patient_id];
        $data['visits'] = $this->patients_model->get_all_visits('', '', $filters);
        $data['patient'] = $patient;
        $data['title'] = _l('your_visits');
        $data['settings'] = $this->get_kiosk_settings();

        $this->load->view('self_kiosk/kiosk/services/visits', $data);
    }

    public function logout()
    {
        $this->session->unset_userdata('kiosk_patient_id');
        redirect(site_url('self_kiosk/kiosk'));
    }

    private function get_kiosk_settings()
    {
        $settings = [];
        $source_id = $this->session->userdata('kiosk_qr_source');
        if ($source_id) {
            $qr_code = $this->self_kiosk_model->get($source_id);
            if ($qr_code && !empty($qr_code->settings)) {
                $settings = json_decode($qr_code->settings, true);
            }
        }
        return $settings;
    }
}
