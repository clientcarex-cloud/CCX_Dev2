<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Forms extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('web_integration_model');
    }

    public function ping()
    {
        echo 'pong';
    }

    public function get_doctor_slots()
    {
        if ($this->input->is_ajax_request()) {
            $doctor_id = $this->input->post('doctor_id');
            $date = $this->input->post('date');

            if (!$doctor_id || !$date) {
                echo json_encode([]);
                return;
            }

            $this->load->model('appointments/appointments_model');
            $slots = $this->appointments_model->get_doctor_slots($doctor_id, $date);
            echo json_encode($slots);
        }
    }

    public function index($key)
    {
        $form = $this->web_integration_model->get_form_by_key($key);

        if (!$form) {
            show_404();
        }

        $data['form'] = $form;
        $data['fields'] = $this->web_integration_model->get_form_fields($form->id);

        // Fetch Client Groups for Branch field
        $this->load->model('clients_model');
        $data['branches'] = $this->clients_model->get_groups();

        // Fetch Treatments
        $data['treatments'] = $this->db->get(db_prefix() . 'treatments')->result_array();

        // Fetch Doctors (Active, Consultant, Role: Doctor/Jr. Doctor/Sr. Doctor)
        $roles = $this->db->where_in('name', ['Doctor', 'Jr. Doctor', 'Sr. Doctor'])->get(db_prefix() . 'roles')->result_array();
        $role_ids = array_column($roles, 'roleid');

        if (!empty($role_ids)) {
            $this->db->where('active', 1);
            $this->db->group_start();
            $this->db->where('doctor_profile_type', 'Consultant');
            $this->db->or_where('doctor_profile_type', 'consultant');
            $this->db->group_end();
            $this->db->where_in('role', $role_ids);
            $data['doctors'] = $this->db->get(db_prefix() . 'staff')->result_array();
        } else {
            $data['doctors'] = [];
        }

        // Remove layout to show only the form in iframe
        $this->load->view('public_form', $data);
    }

    public function submit($key)
    {
        $form = $this->web_integration_model->get_form_by_key($key);

        if (!$form) {
            show_404();
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Check Secret Key
            if (!$this->check_secret_key($data, $form)) {
                return;
            }

            // Server-side Validations
            $errors = $this->validate_submission($data);

            if (!empty($errors)) {
                if ($this->input->is_ajax_request() || stripos($this->input->get_request_header('Accept'), 'json') !== false) {
                    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
                    return;
                }
                set_alert('danger', implode('<br>', $errors));
                redirect(site_url('web_integration/forms/index/' . $key));
                return;
            }

            // Handle file upload
            $data['attachment'] = $this->handle_attachment($form->id);

            $data['form_id'] = $form->id;

            $id = $this->web_integration_model->add_entry($data);

            if ($id) {
                $success_message = !empty($form->success_message) ? $form->success_message : 'Form submitted successfully.';
                if ($this->input->is_ajax_request() || stripos($this->input->get_request_header('Accept'), 'json') !== false) {
                    echo json_encode(['success' => true, 'message' => $success_message, 'entry_id' => $id]);
                    return;
                }
                set_alert('success', $success_message);
                $this->session->set_flashdata('success_message', $success_message);
            } else {
                if ($this->input->is_ajax_request() || stripos($this->input->get_request_header('Accept'), 'json') !== false) {
                    echo json_encode(['success' => false, 'message' => 'Problem submitting form']);
                    return;
                }
                set_alert('danger', 'Problem submitting form');
            }
            redirect(site_url('web_integration/forms/index/' . $key . '?success=true'));
        }
    }

    private function check_secret_key($data, $form)
    {
        if (!empty($form->secret_key)) {
            $submitted_key = isset($data['secret_key']) ? $data['secret_key'] : '';
            if ($submitted_key !== $form->secret_key) {
                if ($this->input->is_ajax_request() || stripos($this->input->get_request_header('Accept'), 'json') !== false) {
                    echo json_encode(['success' => false, 'message' => 'Unauthorized Access: Invalid Secret Key']);
                    return false;
                }
                show_error('Unauthorized Access: Invalid Secret Key', 403);
                return false;
            }
        }
        return true;
    }

    private function validate_submission($data)
    {
        $errors = [];

        // Name limit 50
        if (isset($data['name']) && strlen($data['name']) > 50) {
            $errors[] = 'Name exceeds 50 characters limit.';
        }

        // Age number only and limit 3
        if (isset($data['age'])) {
            if (!is_numeric($data['age'])) {
                $errors[] = 'Age must be a number.';
            } elseif (strlen((string) $data['age']) > 3) {
                $errors[] = 'Age cannot exceed 3 digits.';
            }
        }

        // Mobile limit 10 digits
        if (isset($data['mobile_number'])) {
            if (!preg_match('/^\d{10}$/', $data['mobile_number'])) {
                $errors[] = 'Mobile number must be exactly 10 digits.';
            }
        }

        // WhatsApp limit 10 digits
        if (isset($data['whatsapp_number'])) {
            if (!preg_match('/^\d{10}$/', $data['whatsapp_number'])) {
                $errors[] = 'WhatsApp number must be exactly 10 digits.';
            }
        }

        // Consultation Date future only
        if (isset($data['consultation_datetime']) && !empty($data['consultation_datetime'])) {
            if (strtotime($data['consultation_datetime']) <= time()) {
                $errors[] = 'Consultation date and time must be in the future.';
            }
        }

        // Duplicate Appointment Check
        if (isset($data['consultation_date']) && !empty($data['consultation_date'])) {
            $this->load->model('appointments/appointments_model');
            if ($this->appointments_model->check_duplicate_appointment($data['name'], $data['mobile_number'] ?? '', $data['consultation_date'])) {
                $errors[] = 'An appointment already exists for this patient on this date.';
            }
        }

        // Rating validation
        if (isset($data['rating']) && !empty($data['rating'])) {
            if (!is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
                $errors[] = 'Rating must be between 1 and 5.';
            }
        }

        return $errors;
    }

    private function handle_attachment($form_id)
    {
        if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
            $path = FCPATH . 'uploads/web_integration/' . $form_id . '/';

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
                $index_file = $path . 'index.html';
                if (!file_exists($index_file)) {
                    $fp = fopen($index_file, 'w');
                    fwrite($fp, '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
                    fclose($fp);
                }
            }

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|image/jpg|image/jpeg|image/png|image/gif|application/pdf|application/msword|application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('attachment')) {
                $upload_data = $this->upload->data();
                return $upload_data['file_name'];
            }
        }
        return '';
    }
}
