<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shift_report extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shift_report_model');
        $this->load->model('staff_model');
    }

    /* List all shift reports */
    public function index()
    {
        if (!has_permission('shift_report', '', 'view')) {
            access_denied('shift_report');
        }

        $this->load->view('report');
    }

    public function report()
    {
        if (!has_permission('shift_report', '', 'view')) {
            access_denied('shift_report');
        }

        $staff_id = $this->input->get('staff_id');
        $date = $this->input->get('date');
        $type = $this->input->get('type');

        // Default to current user and today if not provided
        if (!$staff_id) {
            $staff_id = get_staff_user_id();
        }
        if (!$date) {
            $date = date('Y-m-d');
        } else {
            $date = to_sql_date($date);
        }

        $data['title'] = _l('shift_report');
        $data['staff_id'] = $staff_id;
        $data['date'] = $date;
        $data['type'] = $type;
        $data['staff_list'] = $this->staff_model->get('', ['active' => 1]);

        // Fetch report data
        $data['report_data'] = $this->shift_report_model->get_report_data($staff_id, $date, $type);
        $data['staff_details'] = $this->staff_model->get($staff_id);

        if ($type == 'shift_report_hospital') {
            $this->load->view('types/report_hospital', $data);
        } elseif ($type == 'shift_report_lab') {
            $this->load->view('types/report_lab', $data);
        } elseif ($type == 'overall_collection_hospital') {
            $data['report_data'] = $this->shift_report_model->get_overall_collection_hospital($date); // Override report data
            $this->load->view('types/report_hospital_overall', $data);
        } elseif ($type == 'overall_collection_lab') {
            $data['report_data'] = $this->shift_report_model->get_overall_collection_lab($date); // Override report data
            $this->load->view('types/report_lab_overall', $data);
        } elseif ($type == 'consolidated_shift_hospital_lab') {
            $data['report_data'] = $this->shift_report_model->get_consolidated_shift_hospital_lab($date); // Override report data
            $this->load->view('types/report_consolidated_shift_hospital_lab', $data);
        } elseif ($type == 'consolidated_overall_hospital_lab') {
            $data['report_data'] = $this->shift_report_model->get_consolidated_overall_hospital_lab($date); // Override report data
            $this->load->view('types/report_consolidated_overall_hospital_lab', $data);
        } elseif ($type == 'consolidated_all_groups_shift') {
            $data['report_data'] = $this->shift_report_model->get_consolidated_all_groups_shift($date, $staff_id); // Override report data
            $this->load->view('types/report_consolidated_all_groups_shift', $data);
        } elseif ($type == 'consolidated_overall_all_groups') {
            $data['report_data'] = $this->shift_report_model->get_consolidated_overall_all_groups($date); // Override report data
            $this->load->view('types/report_consolidated_overall_all_groups', $data);
        } elseif ($type == 'general_userwise_shift') {
            $data['report_data'] = $this->shift_report_model->get_general_userwise_shift_data($staff_id, $date);
            $this->load->view('types/report_general_userwise_shift', $data);
        } elseif ($type == 'business_userwise_shift') {
            $data['report_data'] = $this->shift_report_model->get_business_userwise_shift_data($staff_id, $date);
            $this->load->view('types/report_business_userwise_shift', $data);
        } elseif ($type == 'transactions_userwise_shift') {
            $data['report_data'] = $this->shift_report_model->get_transactions_userwise_shift_data($staff_id, $date);
            $this->load->view('types/report_transactions_userwise_shift', $data);
        } else {
            $this->load->view('report', $data);
        }
    }

    public function pdf()
    {
        if (!has_permission('shift_report', '', 'view')) {
            access_denied('shift_report');
        }

        $staff_id = $this->input->get('staff_id');
        $date = to_sql_date($this->input->get('date'));

        $type = $this->input->get('type');

        if (!$staff_id || !$date) {
            redirect(admin_url('shift_report'));
        }

        $this->load->library('shift_report/shift_report_pdf', [
            'staff_id' => $staff_id,
            'date' => $date,
            'type' => $type,
            'report_data' => $this->shift_report_model->get_report_data($staff_id, $date, $type),
            'staff_details' => $this->staff_model->get($staff_id)
        ]);

        $this->shift_report_pdf->prepare();
        $this->shift_report_pdf->Output('Shift_Report_' . $date . '.pdf', 'I');
    }
}
