<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mis_reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mis_reports_model');

        // Temporary Schema Fix
        if (!$this->db->field_exists('allowed_roles', db_prefix() . 'mis_reports')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'mis_reports` ADD `allowed_roles` TEXT NULL AFTER `description`;');
        }
        if (!$this->db->field_exists('allowed_staff', db_prefix() . 'mis_reports')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'mis_reports` ADD `allowed_staff` TEXT NULL AFTER `allowed_roles`;');
        }
    }

    public function index()
    {
        $this->load->model('staff_model');
        $data['title'] = 'MIS Reports';
        $data['staff_list'] = $this->staff_model->get('', ['active' => 1]);
        $all_reports = $this->mis_reports_model->get();
        $data['reports'] = [];
        foreach ($all_reports as $report) {
            if ($this->check_permission_for_report($report)) {
                $data['reports'][] = $report;
            }
        }
        $data['staff_id'] = $this->input->get('staff_id');
        $data['selected_report'] = $this->input->get('report_id');
        $data['from_date'] = $this->input->get('from_date') ? to_sql_date($this->input->get('from_date')) : date('Y-m-d');
        $data['to_date'] = $this->input->get('to_date') ? to_sql_date($this->input->get('to_date')) : date('Y-m-d');

        $report = null;
        if ($data['selected_report']) {
            $report = $this->mis_reports_model->get($data['selected_report']);
        }

        if ($data['selected_report'] == 1 || ($report && $report->name == 'Pharmacy')) {
            $raw_data = $this->mis_reports_model->get_patient_visit_overall_report($data['from_date'], $data['to_date'], $data['staff_id']);
            $data['new_patients'] = array_filter($raw_data, function ($r) {
                return $r['patient_status'] == 'New';
            });
            $data['old_patients'] = array_filter($raw_data, function ($r) {
                return $r['patient_status'] == 'Old';
            });
            $data['view_type'] = 'patient_visit_overall';
        } elseif ($data['selected_report'] == 2) {
            $data['report_data'] = $this->mis_reports_model->get_bill_wise_consultation_report($data['from_date'], $data['to_date'], $data['staff_id']);
            $data['view_type'] = 'bill_wise_consultation_report';
        } elseif ($data['selected_report'] == 3) {
            $data['report_data'] = $this->mis_reports_model->get_test_wise_collection_report($data['from_date'], $data['to_date']);
            $data['view_type'] = 'test_wise_collection_report';
        } elseif ($data['selected_report'] == 4) {
            $data['report_data'] = $this->mis_reports_model->get_doctor_wise_consultation_summary($data['from_date'], $data['to_date']);
            $data['view_type'] = 'doctor_wise_consultation_summary';
        } elseif ($data['selected_report'] == 5) {
            $data['report_data'] = $this->mis_reports_model->get_lab_departments_collection_overview($data['from_date'], $data['to_date']);
            $data['view_type'] = 'lab_departments_collection_overview';
        } elseif ($data['selected_report'] == 6) {
            $data['report_data'] = $this->mis_reports_model->get_departments_collection_lab_hospital_report($data['from_date'], $data['to_date']);
            $data['view_type'] = 'departments_collection_lab_hospital';
        } elseif ($data['selected_report'] == 7) {
            $data['report_data'] = $this->mis_reports_model->get_tests_collection_patient_wise_report($data['from_date'], $data['to_date']);
            $data['view_type'] = 'tests_collection_patient_wise';
        } elseif ($data['selected_report'] == 8) {
            $data['report_data'] = $this->mis_reports_model->get_services_collection_overview($data['from_date'], $data['to_date']);
            $data['view_type'] = 'services_collection_overview';
        } elseif ($data['selected_report'] == 9) {
            $data['report_data'] = $this->mis_reports_model->get_services_collection_patient_wise_report($data['from_date'], $data['to_date']);
            $data['view_type'] = 'services_collection_patient_wise';
        } elseif ($data['selected_report'] == 10) {
            $data['report_data'] = $this->mis_reports_model->get_refunds_overview_report($data['from_date'], $data['to_date']);
            $data['view_type'] = 'refunds_overview';
        } elseif ($data['selected_report'] == 11) {
            $data['report_data'] = $this->mis_reports_model->get_refunds_in_detail_report($data['from_date'], $data['to_date']);
            $data['view_type'] = 'refunds_in_detail';
        } elseif ($data['selected_report'] == 12) {
            $data['report_data'] = $this->mis_reports_model->get_general_userwise_report($data['from_date'], $data['to_date'], $data['staff_id']);
            $data['view_type'] = 'general_userwise_report';
        } elseif ($data['selected_report'] == 13) {
            $data['report_data'] = $this->mis_reports_model->get_business_userwise_report($data['from_date'], $data['to_date'], $data['staff_id']);
            $data['view_type'] = 'business_userwise_report';
        } elseif ($data['selected_report'] == 14) {
            $data['report_data'] = $this->mis_reports_model->get_transactions_userwise_report($data['from_date'], $data['to_date'], $data['staff_id']);
            $data['view_type'] = 'transactions_userwise_report';
        }

        $this->load->view('index', $data);
    }

    public function print_report()
    {
        $data['title'] = 'Print MIS Report';
        $data['staff_id'] = $this->input->get('staff_id');
        $data['selected_report'] = $this->input->get('report_id');
        $data['from_date'] = $this->input->get('from_date') ? to_sql_date($this->input->get('from_date')) : date('Y-m-d');
        $data['to_date'] = $this->input->get('to_date') ? to_sql_date($this->input->get('to_date')) : date('Y-m-d');

        $report = null;
        if ($data['selected_report']) {
            $report = $this->mis_reports_model->get($data['selected_report']);
        }

        if ($data['selected_report'] == 1 || ($report && $report->name == 'Pharmacy')) {
            $raw_data = $this->mis_reports_model->get_patient_visit_overall_report($data['from_date'], $data['to_date'], $data['staff_id']);
            $data['new_patients'] = array_filter($raw_data, function ($r) {
                return $r['patient_status'] == 'New';
            });
            $data['old_patients'] = array_filter($raw_data, function ($r) {
                return $r['patient_status'] == 'Old';
            });

            $this->load->view('patient_visit_overall_print', $data);
        } else {
            echo "Unknown report type for printing.";
        }
    }

    public function excel()
    {
        $data['staff_id'] = $this->input->get('staff_id');
        $data['selected_report'] = $this->input->get('report_id');
        $data['from_date'] = $this->input->get('from_date') ? to_sql_date($this->input->get('from_date')) : date('Y-m-d');
        $data['to_date'] = $this->input->get('to_date') ? to_sql_date($this->input->get('to_date')) : date('Y-m-d');

        $report = null;
        if ($data['selected_report']) {
            $report = $this->mis_reports_model->get($data['selected_report']);
        }

        if ($data['selected_report'] == 1 || ($report && $report->name == 'Pharmacy')) {
            include_once(module_dir_path('mis_reports', 'third_party/XLSXWriter/xlsxwriter.class.php'));

            $raw_data = $this->mis_reports_model->get_patient_visit_overall_report($data['from_date'], $data['to_date'], $data['staff_id']);
            $new_patients = array_filter($raw_data, function ($r) {
                return $r['patient_status'] == 'New';
            });
            $old_patients = array_filter($raw_data, function ($r) {
                return $r['patient_status'] == 'Old';
            });

            $filename = 'Patient_Visit_Overall_Report_' . date('Y-m-d_H-i-s') . '.xlsx';

            header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            $writer = new XLSXWriter();
            $writer->setAuthor('CCX');

            // Define Header Styles and Types
            $header_types = [
                'Sl.No' => 'integer',
                'Date' => 'date',
                'MR No.' => 'string',
                'VisitId' => 'string',
                'Patient Type' => 'string',
                'Name' => 'string',
                'Age' => 'string',
                'Gender' => 'string',
                'PhoneNO' => 'string',
                'ReferDoctor' => 'string',
                'Laboratory' => 'string',
                'Tests' => 'string',
                'CollectedBy' => 'string',
                'Amount' => 'price',
                'Discount' => 'price',
                'Paid' => 'price',
                'Balance' => 'price',
                'UserName' => 'string',
                'Refund' => 'price',
            ];

            // Helper to write a section
            $write_section = function ($title, $patients) use ($writer, $data, $header_types) {
                // Section Title Row
                $writer->writeSheetRow('Sheet1', [$title], ['font-style' => 'bold', 'font-size' => 14]);
                $writer->writeSheetRow('Sheet1', [$title . ' :: Report :: From :' . $data['from_date'] . ' To : ' . $data['to_date']], ['font-style' => 'italic']);

                // Column Headers
                $writer->writeSheetRow('Sheet1', array_keys($header_types), ['font-style' => 'bold', 'fill' => '#badcfb', 'border' => 'left,right,top,bottom']);

                $i = 1;
                $sum_amount = 0;
                $sum_discount = 0;
                $sum_paid = 0;
                $sum_balance = 0;
                $sum_refund = 0;

                foreach ($patients as $row) {
                    $balance = $row['amount'] - $row['paid'];
                    $sum_amount += $row['amount'];
                    $sum_discount += $row['discount'];
                    $sum_paid += $row['paid'];
                    $sum_balance += $balance;
                    $sum_refund += $row['refund'];

                    $writer->writeSheetRow('Sheet1', [
                        $i++,
                        $row['created_at'], // format YYYY-MM-DD usually works with 'date' type
                        $row['mr_number'],
                        $row['visit_code'],
                        $row['visit_type'],
                        $row['patient_name'],
                        $row['age'] . ' ' . $row['age_unit'],
                        $row['gender'],
                        $row['phonenumber'],
                        $row['refer_doctor'],
                        $row['laboratory'],
                        $row['tests'],
                        $row['collected_by'],
                        (float) $row['amount'],
                        (float) $row['discount'],
                        (float) $row['paid'],
                        (float) $balance,
                        $row['username'],
                        (float) $row['refund']
                    ]);
                }

                // Section Totals
                $writer->writeSheetRow('Sheet1', [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    'Total',
                    '',
                    (float) $sum_amount,
                    (float) $sum_discount,
                    (float) $sum_paid,
                    (float) $sum_balance,
                    '',
                    (float) $sum_refund
                ], ['font-style' => 'bold', 'fill' => '#f0f0f0']);

                $writer->writeSheetRow('Sheet1', []); // Empty line

                return [
                    'amount' => $sum_amount,
                    'discount' => $sum_discount,
                    'paid' => $sum_paid,
                    'balance' => $sum_balance,
                    'refund' => $sum_refund
                ];
            };

            $total_new = $write_section('New Patient', $new_patients);
            $total_old = $write_section('Old Patient', $old_patients);

            // Grand Totals
            $grand_amount = $total_new['amount'] + $total_old['amount'];
            $grand_discount = $total_new['discount'] + $total_old['discount'];
            $grand_paid = $total_new['paid'] + $total_old['paid'];
            $grand_balance = $total_new['balance'] + $total_old['balance'];
            $grand_refund = $total_new['refund'] + $total_old['refund'];

            $writer->writeSheetRow('Sheet1', [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Grand Total',
                '',
                (float) $grand_amount,
                (float) $grand_discount,
                (float) $grand_paid,
                (float) $grand_balance,
                '',
                (float) $grand_refund
            ], ['font-style' => 'bold', 'fill' => '#badcfb']);

            $writer->writeToStdOut();
            exit;
        } else {
            redirect(admin_url('mis_reports'));
        }
    }

    public function settings()
    {
        $this->load->model('roles_model');
        $this->load->model('staff_model');
        $data['title'] = 'Manage MIS Reports';
        $data['roles'] = $this->roles_model->get();
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['reports'] = $this->mis_reports_model->get();
        $this->load->view('manage_reports', $data);
    }

    public function report()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            if (isset($data['allowed_roles']) && is_array($data['allowed_roles'])) {
                $data['allowed_roles'] = implode(',', $data['allowed_roles']);
            } else {
                $data['allowed_roles'] = '';
            }

            if (isset($data['allowed_staff']) && is_array($data['allowed_staff'])) {
                $data['allowed_staff'] = implode(',', $data['allowed_staff']);
            } else {
                $data['allowed_staff'] = '';
            }


            if (!$data['id']) {
                unset($data['id']);
                $id = $this->mis_reports_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', 'Report'));
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->mis_reports_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', 'Report'));
                }
            }
            redirect(admin_url('mis_reports/settings'));
        }
    }

    public function delete_report($id)
    {
        if (!$id) {
            redirect(admin_url('mis_reports/settings'));
        }
        $response = $this->mis_reports_model->delete($id);
        if ($response) {
            set_alert('success', _l('deleted_successfully', 'Report'));
        }
        redirect(admin_url('mis_reports/settings'));
    }

    private function check_permission_for_report($report)
    {
        if (is_admin()) {
            return true;
        }

        $allowed_roles = !empty($report['allowed_roles']) ? explode(',', $report['allowed_roles']) : [];
        $allowed_staff = !empty($report['allowed_staff']) ? explode(',', $report['allowed_staff']) : [];

        // If no restrictions are set, who should see it? Assuming everyone if both empty, or maybe no one?
        // Let's assume if both are empty, it's visible to everyone (or admins only? usually public if settings empty)
        // Based on request "user want to give permissions... by either role wise and staff wise", implies restriction.
        // If nothing is selected, let's assume valid for all for now, or maybe restricted. 
        // Standard practice: if no roles/staff defined, maybe it's open? Or closed?
        // Let's go with: If no restrictions defined, it's visible. 
        if (empty($allowed_roles) && empty($allowed_staff)) {
            return true;
        }

        $current_user_role = $this->staff_model->get(get_staff_user_id())->role;

        if (!empty($allowed_roles) && in_array($current_user_role, $allowed_roles)) {
            return true;
        }

        if (!empty($allowed_staff) && in_array(get_staff_user_id(), $allowed_staff)) {
            return true;
        }

        return false;

    }


    public function fix_schema()
    {
        if (!$this->db->field_exists('allowed_roles', db_prefix() . 'mis_reports')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'mis_reports` ADD `allowed_roles` TEXT NULL AFTER `description`;');
            echo "Added allowed_roles column.<br>";
        } else {
            echo "allowed_roles column already exists.<br>";
        }

        if (!$this->db->field_exists('allowed_staff', db_prefix() . 'mis_reports')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'mis_reports` ADD `allowed_staff` TEXT NULL AFTER `allowed_roles`;');
            echo "Added allowed_staff column.<br>";
        } else {
            echo "allowed_staff column already exists.<br>";
        }
        die('Schema check complete.');
    }
}
