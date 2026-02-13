<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('appointments_model');
        // We might need patients model for some lookups, but let's try to keep it independent or use clients_model
        $this->load->model('clients_model');
    }

    public function index()
    {
        if (!has_permission('appointments', '', 'view')) {
            access_denied('Appointments');
        }

        $data['title'] = _l('appointments');

        $filters = [
            'doctor_id' => $this->input->get('doctor_id'),
            'date' => $this->input->get('date'),
            'status' => $this->input->get('status'),
            'search' => $this->input->get('search'),
        ];

        $data['filters'] = $filters; // Pass back to view to keep inputs populated
        $data['appointments'] = $this->appointments_model->get('', $filters);

        // Enhance appointment data with names
        foreach ($data['appointments'] as &$appointment) {
            $patient = null;
            if (isset($appointment['patient_id']) && is_numeric($appointment['patient_id']) && $appointment['patient_id'] > 0) {
                $patient = $this->clients_model->get($appointment['patient_id']);
            }

            $doctor = $this->staff_model->get($appointment['doctor_id']);
            $db_patient_name = isset($appointment['patient_name']) ? $appointment['patient_name'] : '';

            if ($patient) {
                $appointment['type'] = 'client';
                if (is_array($patient)) {
                    $appointment['patient_name'] = isset($patient['company']) ? $patient['company'] : 'Unknown';
                    $appointment['mr_no'] = isset($patient['mr_no']) ? $patient['mr_no'] : '';
                } else {
                    $appointment['patient_name'] = isset($patient->company) ? $patient->company : 'Unknown';
                    $appointment['mr_no'] = isset($patient->mr_no) ? $patient->mr_no : '';
                }
            } elseif (!empty($appointment['guest_id'])) {
                $appointment['type'] = 'guest';
                $guest = $this->appointments_model->get_guest($appointment['guest_id']);
                $appointment['patient_name'] = $guest ? $guest->name : 'Unknown Guest';
            } elseif (!empty($db_patient_name)) {
                $appointment['type'] = 'guest';
                $appointment['patient_name'] = $db_patient_name;
            } else {
                $appointment['type'] = 'unknown';
                $appointment['patient_name'] = 'Unknown';
            }
            $appointment['doctor_name'] = $doctor ? $doctor->firstname . ' ' . $doctor->lastname : 'Unknown';
            $appointment['has_history'] = $this->appointments_model->has_reschedule_history($appointment['id']);
        }

        // Calculate Stats based on filtered results
        $stats = [
            'total' => count($data['appointments']),
            'confirmed' => 0,
            'visited' => 0,
            'pending' => 0,
            'cancelled' => 0,
            'missed' => 0,
            'completed' => 0,
        ];

        foreach ($data['appointments'] as $appt) {
            $status = $appt['status'];
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }
        $data['stats'] = $stats;

        $this->load->view('appointments', $data);
    }

    public function appointment($id = '')
    {
        if (!has_permission('appointments', '', 'create') && !has_permission('appointments', '', 'edit')) {
            access_denied('Appointments');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Check for duplicate appointment
            $name_to_check = '';
            $mobile_to_check = '';

            if (!empty($data['patient_id'])) {
                $patient = $this->clients_model->get($data['patient_id']);
                if ($patient) {
                    $name_to_check = $patient->company;
                    $mobile_to_check = $patient->phonenumber;
                }
            } elseif (!empty($data['guest_id'])) {
                $guest = $this->appointments_model->get_guest($data['guest_id']);
                if ($guest) {
                    $name_to_check = $guest->name;
                    $mobile_to_check = $guest->phone;
                }
            } elseif (!empty($data['new_patient_name']) && !empty($data['mobile_number'])) {
                $name_to_check = $data['new_patient_name'];
                $mobile_to_check = $data['mobile_number'];
            }

            if (!empty($name_to_check) && !empty($mobile_to_check)) {
                $appointment_date = isset($data['appointment_date']) ? $data['appointment_date'] : date('Y-m-d');
                // Ensure ID is passed correctly. If ID is empty string, it should be null/false for the model check if it's a new appointment, 
                // but here we are in editing context or new, so $id is reliable from function arg.
                if ($this->appointments_model->check_duplicate_appointment($name_to_check, $mobile_to_check, $appointment_date, $id)) {
                    set_alert('warning', _l('appointment_already_exists'));
                    redirect(admin_url('appointments/appointment' . ($id ? '/' . $id : '')));
                }
            }

            // Handle New Patient (Guest) Data
            if ($id == '' && empty($data['patient_id']) && empty($data['guest_id'])) {
                // Validate required fields for new patient
                if (empty($data['new_patient_name']) || empty($data['new_patient_age']) || empty($data['mobile_number'])) {
                    set_alert('warning', _l('new_patient_required_fields'));
                    redirect(admin_url('appointments/appointment'));
                }

                // Calculate DOB from Age
                $age = intval($data['new_patient_age']);
                $dob = date('Y-m-d', strtotime("-$age years"));

                // Create Guest Record
                $guest_data = [
                    'name' => $data['new_patient_name'],
                    'phone' => $data['mobile_number'],
                    'dob' => $dob,
                    'gender' => isset($data['new_patient_gender']) ? $data['new_patient_gender'] : '',
                ];

                if ($this->db->field_exists('title', db_prefix() . 'patient_guests')) {
                    $guest_data['title'] = isset($data['new_patient_title']) ? $data['new_patient_title'] : '';
                }

                $guest_id = $this->appointments_model->add_guest($guest_data);
                if ($guest_id) {
                    $data['guest_id'] = $guest_id;
                    $data['patient_id'] = null;
                } else {
                    set_alert('danger', _l('failed_to_create_guest'));
                    redirect(admin_url('appointments/appointment'));
                }
            }

            // Cleanup non-appointment fields
            unset($data['new_patient_title']);
            unset($data['new_patient_name']);
            unset($data['new_patient_age']);
            unset($data['new_patient_gender']);
            unset($data['mobile_number']);

            // Ensure proper IDs
            if (empty($data['patient_id']))
                $data['patient_id'] = null;
            if (empty($data['guest_id']))
                $data['guest_id'] = null;

            if ($id == '') {
                if (!has_permission('appointments', '', 'create')) {
                    access_denied('Appointments');
                }
                $id = $this->appointments_model->add($data);
                if ($id === 'slot_required') {
                    set_alert('warning', _l('select_time_slot'));
                    redirect(admin_url('appointments/appointment'));
                } elseif ($id === 'slot_unavailable') { // Handle slot_unavailable for add as well
                    set_alert('warning', _l('slot_booked'));
                    redirect(admin_url('appointments/appointment'));
                } elseif ($id) {
                    set_alert('success', _l('added_successfully', _l('new_appointment')));
                    redirect(admin_url('appointments'));
                }
            } else {
                if (!has_permission('appointments', '', 'edit')) {
                    access_denied('Appointments');
                }
                $success = $this->appointments_model->update($id, $data);
                if ($success === 'slot_unavailable') {
                    set_alert('warning', _l('slot_booked'));
                    redirect(admin_url('appointments/appointment/' . $id));
                } elseif ($success) {
                    set_alert('success', _l('updated_successfully', _l('edit_appointment')));
                }
                redirect(admin_url('appointments'));
            }
        }

        if ($id == '') {
            $data['title'] = _l('new_appointment');
        } else {
            $data['appointment'] = $this->appointments_model->get($id);
            $data['title'] = _l('edit_appointment');

            if ($data['appointment']) {
                if ($data['appointment']->patient_id) {
                    $this->load->model('clients_model');
                    $patient = $this->clients_model->get($data['appointment']->patient_id);
                    if ($patient) {
                        if (is_array($patient)) {
                            $data['patient_mobile'] = isset($patient['phonenumber']) ? $patient['phonenumber'] : '';
                            $data['patient_name'] = isset($patient['company']) ? $patient['company'] : '';
                            $data['patient_mr_no'] = isset($patient['mr_no']) ? $patient['mr_no'] : '';
                            $data['patient_reg_date'] = isset($patient['datecreated']) ? _dt($patient['datecreated']) : '';
                            $dob = isset($patient['dob']) ? $patient['dob'] : '';
                        } else {
                            $data['patient_mobile'] = isset($patient->phonenumber) ? $patient->phonenumber : '';
                            $data['patient_name'] = isset($patient->company) ? $patient->company : '';
                            $data['patient_mr_no'] = isset($patient->mr_no) ? $patient->mr_no : '';
                            $data['patient_reg_date'] = isset($patient->datecreated) ? _dt($patient->datecreated) : '';
                            $dob = isset($patient->dob) ? $patient->dob : '';
                        }

                        if (!empty($dob)) {
                            $data['patient_age'] = date_diff(date_create($dob), date_create('today'))->y;
                        } else {
                            $data['patient_age'] = 'N/A';
                        }
                    }
                } elseif ($data['appointment']->guest_id) {
                    // Guest Patient
                    $guest = $this->appointments_model->get_guest($data['appointment']->guest_id);
                    if ($guest) {
                        $data['patient_mobile'] = $guest->phone;
                        $data['patient_name'] = $guest->name;
                        $data['patient_mr_no'] = 'Guest';
                        $data['patient_reg_date'] = isset($guest->created_at) ? _dt($guest->created_at) : '';

                        if (!empty($guest->dob)) {
                            $data['patient_age'] = date_diff(date_create($guest->dob), date_create('today'))->y;
                        } else {
                            $data['patient_age'] = 'N/A';
                        }
                    }
                }
            }
        }

        $data['doctors'] = $this->appointments_model->get_doctors();

        $this->load->model('invoice_items_model');
        $data['items'] = $this->invoice_items_model->get();

        $this->load->view('appointment_form', $data);
    }

    public function search_patient()
    {
        if (!has_permission('appointments', '', 'view')) {
            ajax_access_denied();
        }
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $mobile = $this->input->post('mobile');
        $patients = $this->appointments_model->get_patient_by_mobile($mobile);

        echo json_encode($patients);
    }

    public function confirm_visit($id)
    {
        if (!has_permission('appointments', '', 'edit')) {
            access_denied('Appointments');
        }

        if (!$id) {
            redirect(admin_url('appointments'));
        }

        // Optimistically update status first? Or check logic first?
        // Let's retrieve appointment first to know what to do.
        $appointment = $this->appointments_model->get($id);
        if (!$appointment) {
            redirect(admin_url('appointments'));
        }

        $data = [
            'status' => 'visited',
            'visit_confirmed_at' => date('Y-m-d H:i:s')
        ];

        $success = $this->appointments_model->update($id, $data);

        if ($success) {
            set_alert('success', _l('visit_confirmed'));

            $CI = &get_instance();
            $redirect_url = admin_url('appointments'); // Default fallback

            // Check if Patients module is active
            if ($CI->app_modules->is_active('patients')) {
                $this->load->model('patients/patients_model');

                $final_patient_id = null;

                // Scenario 1: Existing Patient
                if ($appointment->patient_id) {
                    $final_patient_id = $appointment->patient_id;
                }
                // Scenario 2: Guest User - Create Patient
                elseif ($appointment->guest_id) {
                    $guest = $this->appointments_model->get_guest($appointment->guest_id);
                    if ($guest) {
                        $patient_data = [
                            'firstname' => $guest->name, // Assuming name is full name
                            'lastname' => '',
                            'phonenumber' => $guest->phone,
                            'dob' => $guest->dob,
                            'gender' => $guest->gender,
                            'patient_title' => isset($guest->title) ? $guest->title : '',
                            'email' => '', // Prevent undefined index error
                            'password' => '', // Auto-generate or empty
                        ];

                        $final_patient_id = $this->patients_model->add_patient($patient_data);

                        if ($final_patient_id) {
                            // Update appointment to link to new patient and remove guest link
                            $this->appointments_model->update($id, [
                                'patient_id' => $final_patient_id,
                                'guest_id' => null
                            ]);
                            log_activity('Guest Converted to Patient on Visit Confirmation [ApptID: ' . $id . ', NewPatientID: ' . $final_patient_id . ']');

                            // Reload appointment data to ensure correct IDs if needed downstream
                            $appointment->patient_id = $final_patient_id;
                        }
                    }
                }

                // If we have a valid patient ID (either existing or newly created), add visit and redirect
                if ($final_patient_id) {
                    // Prepare visit data
                    $visit_data = [
                        'patient_id' => $final_patient_id,
                        'doctor_id' => $appointment->doctor_id,
                        'notes' => isset($appointment->notes) ? $appointment->notes : 'Visit created from appointment confirmation',
                    ];

                    $visit_id = $this->patients_model->add_visit($visit_data);
                    if ($visit_id) {
                        log_activity('Patient Visit Record Created [VisitID: ' . $visit_id . ', ApptID: ' . $id . ']');
                    }

                    // Set Redirect to Split View with ID
                    $redirect_url = admin_url('patients?view=split&id=' . $final_patient_id);

                    // Auto-Add Default Service Item Logic
                    // Check if invoice already exists or is paid
                    if (empty($appointment->invoice_id)) {
                        $this->load->model('staff_model');
                        $doctor = $this->staff_model->get($appointment->doctor_id);
                        if ($doctor && !empty($doctor->default_service_item)) {
                            $redirect_url .= '&auto_item=' . $doctor->default_service_item;
                        }
                    }
                }
            }

            // Token System Integration (Preserved)
            if ($CI->app_modules->is_active('token_system')) {
                if (get_option('token_system_workflow') == 'smart') {
                    $this->load->model('token_system/token_system_model');

                    // Determine patient name using exact logic as index() or re-use logic
                    // If we converted guest, we now have a patient_id, so logic should hold

                    $patient_name = 'Unknown';
                    $patient_id_for_token = null;

                    if (isset($appointment->patient_id) && $appointment->patient_id) {
                        // Reload if we just updated it? $appointment object might be stale if we updated DB but not object
                        // But we set $appointment->patient_id = $final_patient_id manually above if converted.
                        $patient = $this->clients_model->get($appointment->patient_id);
                        if ($patient) {
                            $patient_name = is_array($patient) ? $patient['company'] : $patient->company;
                            $patient_id_for_token = $appointment->patient_id;
                        }
                    } elseif ($appointment->guest_id && !$final_patient_id) {
                        // Fallback if conversion failed or not attempted (e.g. module inactive)
                        $guest = $this->appointments_model->get_guest($appointment->guest_id);
                        if ($guest) {
                            $patient_name = $guest->name;
                        }
                    }

                    $token_data = [
                        'patient_name' => $patient_name,
                        'patient_id' => $patient_id_for_token,
                        'doctor_id' => isset($appointment->doctor_id) ? $appointment->doctor_id : 0
                    ];

                    $token_id = $this->token_system_model->add($token_data);
                    if ($token_id) {
                        set_alert('success', _l('visit_confirmed_token_generated'));
                    }
                }
            }

            redirect($redirect_url);

        } else {
            set_alert('warning', _l('visit_confirm_failed'));
        }
        redirect(admin_url('appointments'));
    }

    public function get_slots()
    {
        if (!has_permission('appointments', '', 'view')) {
            ajax_access_denied();
        }
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $doctor_id = $this->input->post('doctor_id');
        $date = $this->input->post('date');
        $appointment_id = $this->input->post('appointment_id'); // Get appointment ID for exclusion

        $slots = $this->appointments_model->get_doctor_slots($doctor_id, $date, $appointment_id);
        echo json_encode($slots);
    }

    public function delete($id)
    {
        if (!has_permission('appointments', '', 'delete')) {
            access_denied('Appointments');
        }

        if (!$id) {
            redirect(admin_url('appointments'));
        }

        $response = $this->appointments_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Appointment'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Appointment'));
        }
        redirect(admin_url('appointments'));
    }

    public function get_patient_details_ajax()
    {
        if (!has_permission('appointments', '', 'view')) {
            ajax_access_denied();
        }
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        $type = $this->input->post('type');

        if ($type == 'client') {
            $this->load->model('clients_model');
            $patient = $this->clients_model->get($id);
            if ($patient) {
                $data['patient'] = $patient;
                $data['type'] = 'client';
                $this->load->view('patient_details_modal', $data);
            }
        } elseif ($type == 'guest') {
            $guest = $this->appointments_model->get_guest($id);
            if ($guest) {
                $data['patient'] = $guest;
                $data['type'] = 'guest';
                $this->load->view('patient_details_modal', $data);
            }
        }
    }

    public function get_reschedule_history_ajax()
    {
        if (!has_permission('appointments', '', 'view')) {
            ajax_access_denied();
        }
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        $data['history'] = $this->appointments_model->get_reschedule_history($id);
        $this->load->view('reschedule_history_modal', $data);
    }

    public function check_duplicate_ajax()
    {
        if (!has_permission('appointments', '', 'view') && !has_permission('appointments', '', 'create') && !has_permission('appointments', '', 'edit')) {
            ajax_access_denied();
        }
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data = $this->input->post();
        $id = isset($data['appointment_id']) ? $data['appointment_id'] : null;
        $date = isset($data['appointment_date']) ? $data['appointment_date'] : date('Y-m-d');

        $name_to_check = '';
        $mobile_to_check = '';

        if (!empty($data['patient_id'])) {
            $patient = $this->clients_model->get($data['patient_id']);
            if ($patient) {
                $name_to_check = $patient->company;
                $mobile_to_check = $patient->phonenumber;
            }
        } elseif (!empty($data['guest_id'])) {
            $guest = $this->appointments_model->get_guest($data['guest_id']);
            if ($guest) {
                $name_to_check = $guest->name;
                $mobile_to_check = $guest->phone;
            }
        } elseif (!empty($data['new_patient_name']) && !empty($data['mobile_number'])) {
            $name_to_check = $data['new_patient_name'];
            $mobile_to_check = $data['mobile_number'];
        }

        $exists = false;
        if (!empty($name_to_check) && !empty($mobile_to_check)) {
            $exists = $this->appointments_model->check_duplicate_appointment($name_to_check, $mobile_to_check, $date, $id);
        }

        echo json_encode([
            'exists' => $exists,
            'message' => $exists ? _l('appointment_already_exists') : ''
        ]);
    }

    public function settings()
    {
        if (!has_permission('appointments', '', 'edit')) {
            access_denied('Appointments');
        }

        if ($this->input->post()) {
            $buffer_time = $this->input->post('appointments_buffer_time');
            update_option('appointments_buffer_time', $buffer_time);
            set_alert('success', _l('updated_successfully', _l('appointments_settings')));
            redirect(admin_url('appointments/settings'));
        }

        $data['title'] = _l('appointments_settings_title');
        $this->load->view('settings', $data);
    }
}
