<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dr_authorization extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('dr_authorization_model');
        $this->load->model('staff_model');

        $filters = [
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date'),
            'user_id' => $this->input->get('user_id'),
            'department_id' => $this->input->get('department_id'),
            'ref_doctor_id' => $this->input->get('ref_doctor_id'),
            'status' => $this->input->get('status') ? $this->input->get('status') : 'All'
        ];

        // Apply Default Date if NO date filter is present
        if (empty($filters['from_date']) && empty($filters['to_date'])) {
            $default_date = get_option('dr_authorization_default_date_filter');
            if ($default_date == 'today') {
                $filters['from_date'] = date('Y-m-d');
                $filters['to_date'] = date('Y-m-d');
            } elseif ($default_date == 'yesterday') {
                $filters['from_date'] = date('Y-m-d', strtotime('-1 day'));
                $filters['to_date'] = date('Y-m-d', strtotime('-1 day'));
            } elseif ($default_date == 'yesterday_today') {
                $filters['from_date'] = date('Y-m-d', strtotime('-1 day'));
                $filters['to_date'] = date('Y-m-d');
            }
        }

        // Fetch Data
        $data['requests'] = $this->dr_authorization_model->get_transcription_requests($filters);

        // Filter Data Lists
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['doctors'] = $this->dr_authorization_model->get_doctors_by_roles();

        // Fetch Departments (tblitems.department_id linked to tbldepartments)
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();

        $data['filters'] = $filters; // Pass current selection to view
        $data['selected_status'] = $filters['status'];
        $data['title'] = 'Dr Authorization';
        $this->load->view('dr_authorization/manage', $data);
    }

    public function settings()
    {
        if ($this->input->post()) {
            $group = $this->input->post('settings_group');

            if ($group == 'filters') {
                update_option('dr_authorization_default_date_filter', $this->input->post('dr_authorization_default_date_filter'));
            } elseif ($group == 'hide_show') {
                $opts = [
                    'dr_authorization_show_date_filter',
                    'dr_authorization_show_user_filter',
                    'dr_authorization_show_ref_doctor_filter',
                    'dr_authorization_show_department_filter',
                    'dr_authorization_show_payment_info'
                ];
                foreach ($opts as $opt) {
                    $val = $this->input->post($opt) ? '1' : '0';
                    update_option($opt, $val);
                }
            }

            set_alert('success', 'Settings Saved Successfully');
            redirect(admin_url('dr_authorization/settings'));
        }

        $data['title'] = 'Dr Authorization Settings';
        $this->load->view('dr_authorization/settings', $data);
    }

    public function create_report($test_id)
    {
        $this->load->model('dr_authorization_model');
        $id = $this->dr_authorization_model->create_transcription($test_id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => (bool) $id, 'id' => $id]);
            die;
        }

        if ($id) {
            redirect(admin_url('dr_authorization/edit_report/' . $id));
        } else {
            set_alert('warning', 'Could not create report');
            redirect(admin_url('dr_authorization'));
        }
    }

    public function edit_report($id)
    {
        $this->load->model('dr_authorization_model');
        $data['report'] = $this->dr_authorization_model->get_transcription($id);

        if (!$data['report']) {
            show_404();
        }

        if ($this->input->is_ajax_request()) {
            if (isset($data['report']->template_type) && $data['report']->template_type == 'fixed') {
                $data['params'] = $this->dr_authorization_model->get_transcription_params($id);
                $this->load->view('dr_authorization/edit_fixed_modal', $data);
            } else {
                $this->load->view('dr_authorization/edit_modal', $data);
            }
        } else {
            $data['title'] = 'Edit Report';
            $this->load->view('dr_authorization/edit', $data);
        }
    }

    public function save_report()
    {
        $this->load->model('dr_authorization_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $data['id'];
            $action = isset($data['action']) ? $data['action'] : 'save';

            unset($data['id']);
            unset($data['action']); // Remove action from db data

            // Handle Fixed Params
            if (isset($data['params'])) {
                $this->dr_authorization_model->update_transcription_params(['params' => $data['params']]);
                unset($data['params']);

                if (isset($data['content'])) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'transcriptor', ['content' => $data['content']]);
                }
            } else {
                // Word Mode
                if (isset($data['content'])) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'transcriptor', ['content' => $data['content']]);
                }
            }

            if ($action == 'complete') {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'transcriptor', ['status' => 'Completed']);

                // Also update patient_tests status
                $report = $this->dr_authorization_model->get_transcription($id);
                if ($report) {
                    $this->db->where('id', $report->patient_test_id);
                    $this->db->update(db_prefix() . 'patient_tests', ['status' => 'Completed']);
                }
                $msg = 'Report marked as completed';
            } elseif ($action == 'send_for_authorization') {
                $report = $this->dr_authorization_model->get_transcription($id);
                if ($report) {
                    $this->db->where('id', $report->patient_test_id);
                    $this->db->update(db_prefix() . 'patient_tests', ['status' => 8]); // 8 = Authorization Required
                }
                $msg = 'Report sent for authorization';
            } else {
                $msg = 'Report saved successfully';
            }
            set_alert('success', $msg);

            // If saved from modal, redirect to list
            redirect(admin_url('dr_authorization'));
        }
    }

    public function view_report($id)
    {
        $this->load->model('dr_authorization_model');
        // $id passed here is test_id
        $data['report'] = $this->dr_authorization_model->get_transcription_by_test($id);

        if (!$data['report']) {
            show_404();
        }

        if ($this->input->is_ajax_request()) {
            $this->load->view('dr_authorization/view_report_modal', $data);
        } else {
            die('Request must be AJAX');
        }
    }

    public function preview($id)
    {
        $this->load->model('dr_authorization_model');
        $report = $this->dr_authorization_model->get_transcription($id);

        if (!$report) {
            show_404();
        }

        // Output raw HTML with some basic styling for the iframe
        echo '<!DOCTYPE html><html><head><style>
            body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; padding: 20px; line-height: 1.42857143; color: #333; background-color: #fff;}
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 4px; }
            .a4-page { width: 210mm; min-height: 297mm; margin: 0 auto; background: white;  }
            @media print { body { padding: 0; } .a4-page { width: 100%; } }
        </style></head><body>';
        echo '<div class="a4-page">';
        echo $report->content;
        echo '</div>';
        echo '</body></html>';
    }
}
