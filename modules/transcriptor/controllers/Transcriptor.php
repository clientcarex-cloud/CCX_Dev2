<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Transcriptor extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('transcriptor_model');
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
            $default_date = get_option('transcriptor_default_date_filter');
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

        // Load invoice model or use helper? Invoice model is extensive.
        // Let's use internal calculation for now or just status.
        // Actually, we can just fetch invoice balance via model if we select invoice ID.
        // Perfex helper `get_invoice_total_left_to_pay($invoice_id, $invoice_total)`

        // Fetch Data
        $data['requests'] = $this->transcriptor_model->get_transcription_requests($filters);

        // Filter Data Lists
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['doctors'] = $this->transcriptor_model->get_doctors_by_roles();

        // Fetch Departments (tblitems.department_id linked to tbldepartments)
        // User clarified filtering by "Lab Tests Department"
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();

        $data['filters'] = $filters; // Pass current selection to view
        $data['selected_status'] = $filters['status'];
        $data['title'] = 'Transcriptor';
        $this->load->view('transcriptor/manage', $data);
    }

    public function settings()
    {
        if ($this->input->post()) {
            $group = $this->input->post('settings_group');

            if ($group == 'filters') {
                update_option('transcriptor_default_date_filter', $this->input->post('transcriptor_default_date_filter'));
            } elseif ($group == 'hide_show') {
                $opts = [
                    'transcriptor_show_date_filter',
                    'transcriptor_show_user_filter',
                    'transcriptor_show_ref_doctor_filter',
                    'transcriptor_show_department_filter',
                    'transcriptor_show_payment_info'
                ];
                foreach ($opts as $opt) {
                    $val = $this->input->post($opt) ? '1' : '0';
                    update_option($opt, $val);
                }
            }

            set_alert('success', 'Settings Saved Successfully');
            redirect(admin_url('transcriptor/settings'));
        }

        $data['title'] = 'Transcriptor Settings';
        $this->load->view('transcriptor/settings', $data);
    }

    public function create_report($test_id)
    {
        $this->load->model('transcriptor_model');
        $id = $this->transcriptor_model->create_transcription($test_id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => (bool) $id, 'id' => $id]);
            die;
        }

        if ($id) {
            redirect(admin_url('transcriptor/edit_report/' . $id));
        } else {
            set_alert('warning', 'Could not create report');
            redirect(admin_url('transcriptor'));
        }
    }

    public function edit_report($id)
    {
        $this->load->model('transcriptor_model');
        $data['report'] = $this->transcriptor_model->get_transcription($id);

        if (!$data['report']) {
            show_404();
        }

        if ($this->input->is_ajax_request()) {
            if (isset($data['report']->template_type) && $data['report']->template_type == 'fixed') {
                $data['params'] = $this->transcriptor_model->get_transcription_params($id);
                $this->load->view('transcriptor/edit_fixed_modal', $data);
            } else {
                $this->load->view('transcriptor/edit_modal', $data);
            }
        } else {
            $data['title'] = 'Edit Report';
            $this->load->view('transcriptor/edit', $data);
        }
    }

    public function save_report()
    {
        $this->load->model('transcriptor_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $data['id'];
            $action = isset($data['action']) ? $data['action'] : 'save';

            unset($data['id']);
            unset($data['action']); // Remove action from db data

            // Handle Fixed Params
            if (isset($data['params'])) {
                $this->transcriptor_model->update_transcription_params(['params' => $data['params']]);
                unset($data['params']);

                // For now, we might not update 'content' field for fixed reports 
                // unless we generate HTML from params. 
                // To avoid overwriting content with empty if it fails validation or something:
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
                $this->load->model('transcriptor_model'); // Ensure model is loaded for get_transcription
                $report = $this->transcriptor_model->get_transcription($id);
                if ($report) {
                    $this->db->where('id', $report->patient_test_id);
                    $this->db->update(db_prefix() . 'patient_tests', ['status' => 'Completed']);
                }
                $msg = 'Report marked as completed';
            } elseif ($action == 'send_for_authorization') {
                $this->load->model('transcriptor_model');
                $report = $this->transcriptor_model->get_transcription($id);
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
            redirect(admin_url('transcriptor'));
        }
    }

    public function view_report($id)
    {
        $this->load->model('transcriptor_model');
        // $id passed here is test_id
        $data['report'] = $this->transcriptor_model->get_transcription_by_test($id);

        if (!$data['report']) {
            show_404();
        }

        if ($this->input->is_ajax_request()) {
            $this->load->view('transcriptor/view_report_modal', $data);
        } else {
            die('Request must be AJAX');
        }
    }

    public function preview($id)
    {
        $this->load->model('transcriptor_model');
        $report = $this->transcriptor_model->get_transcription($id);

        if (!$report) {
            show_404();
        }

        $print = $this->input->get('print');
        $with_letterhead = $this->input->get('letterhead');

        // Generate Report Content
        $report_content = '';
        if ($report->template_type == 'fixed') {
            // Fetch Params
            $params = $this->transcriptor_model->get_transcription_params($id);

            // Generate Table
            $report_content = '<table class="table" border="1" style="width: 100%; border-collapse: collapse; border: 1px solid #ccc;">';
            $report_content .= '<thead><tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px; text-align: left;">Parameter</th>
                        <th style="padding: 8px; text-align: left;">Value</th>
                        <th style="padding: 8px; text-align: left;">Unit</th>
                        <th style="padding: 8px; text-align: left;">Reference Range</th>
                      </tr></thead><tbody>';

            foreach ($params as $p) {
                $bold_style = $p['is_bold'] == 1 ? 'font-weight: bold;' : '';
                $report_content .= '<tr>';
                $report_content .= '<td style="padding: 8px; border: 1px solid #ccc; ' . $bold_style . '">' . $p['parameter_name'] . '</td>';
                $report_content .= '<td style="padding: 8px; border: 1px solid #ccc; ' . $bold_style . '">' . $p['result_value'] . '</td>';
                $report_content .= '<td style="padding: 8px; border: 1px solid #ccc;">' . $p['unit'] . '</td>';
                $report_content .= '<td style="padding: 8px; border: 1px solid #ccc;">' . $p['referral_range'] . '</td>';
                $report_content .= '</tr>';
            }
            $report_content .= '</tbody></table>';
        } else {
            $report_content = $report->content;
        }

        // Check for Print Template (Type = 'Report')
        $this->db->where('type', 'Report');
        $this->db->where('is_default', 1);
        $template = $this->db->get(db_prefix() . 'print_templates')->row();

        if (!$template) {
            // Try getting any template if no default is found
            $this->db->where('type', 'Report');
            $template = $this->db->get(db_prefix() . 'print_templates')->row();
        }

        // HEAD and Helper Styles
        echo '<!DOCTYPE html><html><head><style>
            body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; padding: 20px; line-height: 1.42857143; color: #333; background-color: #fff;}
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 4px; }
            .a4-page { width: 210mm; min-height: 297mm; margin: 0 auto; background: white;  }
            @media print { body { padding: 0; } .a4-page { width: 100%; box-shadow: none; } }
            @page { margin: 0; size: auto; }
        </style>';

        if ($print) {
            echo '<script>window.onload = function() { window.print(); }</script>';
        }
        echo '</head><body>';

        if ($template) {
            $content = $template->content;

            // Expand Recursive Tags (Nested Print Templates)
            $this->load->model('print_templates/print_templates_model');
            $content = $this->print_templates_model->expand_template_tags($content);

            // Replacements
            $content = str_replace('{report_content}', $report_content, $content);
            $content = str_replace('{patient_name}', $report->patient_name, $content);
            $content = str_replace('{age}', $report->age, $content);
            $content = str_replace('{gender}', $report->gender, $content);
            $content = str_replace('{test_name}', $report->test_name, $content);
            $content = str_replace('{ref_doc_name}', $report->ref_doc_name ? $report->ref_doc_name : '-', $content);
            $content = str_replace('{test_date}', _d($report->test_date), $content);
            $content = str_replace('{generated_at}', _dt(date('Y-m-d H:i:s')), $content);

            // Output Content
            echo '<div class="a4-page">';
            echo $content;
            echo '</div>';

        } else {
            // FALLBACK LEGACY OUTPUT
            echo '<div class="a4-page">';
            if ($with_letterhead == 'true') {
                echo '<div class="letterhead-header" style="height: 100px;"></div>';
            }
            echo $report_content;
            echo '</div>';
        }

        echo '</body></html>';
    }
}
