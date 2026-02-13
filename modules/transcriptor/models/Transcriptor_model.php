<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Transcriptor_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_transcription_requests($filters = [])
    {
        $this->db->select(db_prefix() . 'patient_tests.id, ' . db_prefix() . 'patient_tests.created_at, ' . db_prefix() . 'patient_tests.status');
        $this->db->select(db_prefix() . 'patient_tests.is_emergency'); // Added for emoji prefix
        $this->db->select(db_prefix() . 'items.description as test_name');
        $this->db->select(db_prefix() . 'clients.company as patient_name, ' . db_prefix() . 'clients.userid as client_id');
        $this->db->select(db_prefix() . 'visits.visit_code, ' . db_prefix() . 'visits.id as visit_id');

        // Ref Doc (Assumption: Linked to Staff)
        $this->db->select('CONCAT(staff_ref.firstname, " ", staff_ref.lastname) as ref_doc_name');

        // MR Number, Age, Gender
        $this->db->select(db_prefix() . 'patients_extra.mr_number, ' . db_prefix() . 'patients_extra.gender, ' . db_prefix() . 'patients_extra.age');

        // Payment Info
        // Invoice status: 1=Unpaid, 2=Paid, 3=Partial, 4=Overdue, 5=Cancelled, 6=Draft
        $this->db->select(db_prefix() . 'invoices.status as invoice_status, ' . db_prefix() . 'invoices.total, ' . db_prefix() . 'invoices.id as invoice_id');

        $this->db->from(db_prefix() . 'patient_tests');

        // Joins
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');

        // Join for Ref Doc
        $this->db->join(db_prefix() . 'staff as staff_ref', 'staff_ref.staffid = ' . db_prefix() . 'visits.referral_doctor_id', 'left');

        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');

        // Filter by Item Group "Tests"
        $this->db->where(db_prefix() . 'items_groups.name', 'Tests');

        // Filter: If Sample Required, Status must NOT be 'Regular'
        // User Request: "show all status except 'regular' only for item group = Tests and is_sample_collected = true"
        $this->db->group_start();
        $this->db->group_start();
        $this->db->where(db_prefix() . 'items.is_blood_sample_required', 1);
        $this->db->where(db_prefix() . 'patient_tests.status !=', 'Regular');
        $this->db->group_end();

        $this->db->or_group_start();
        $this->db->where(db_prefix() . 'items.is_blood_sample_required !=', 1);
        $this->db->or_where(db_prefix() . 'items.is_blood_sample_required IS NULL');
        $this->db->group_end();
        $this->db->group_end();

        // Apply Status Filter (Tabs)
        // Regular: Not Emergency AND Not Completed
        if (isset($filters['status'])) {
            if ($filters['status'] == 'Regular') {
                $this->db->group_start();
                $this->db->where(db_prefix() . 'patient_tests.is_emergency !=', 1);
                $this->db->or_where(db_prefix() . 'patient_tests.is_emergency IS NULL');
                $this->db->group_end();
                $this->db->where(db_prefix() . 'patient_tests.status !=', 'Completed');
                $this->db->where(db_prefix() . 'patient_tests.status !=', 'Processing');
            } elseif ($filters['status'] == 'Emergency') {
                $this->db->where(db_prefix() . 'patient_tests.is_emergency', 1);
                $this->db->where(db_prefix() . 'patient_tests.status !=', 'Completed');
            } elseif ($filters['status'] == 'Processing') {
                $this->db->where(db_prefix() . 'patient_tests.status', 'Processing');
            } elseif ($filters['status'] == 'Completed') {
                $this->db->where(db_prefix() . 'patient_tests.status', 'Completed');
            }
        }

        // Apply Filters
        if (!empty($filters['from_date'])) {
            $this->db->where('DATE(' . db_prefix() . 'patient_tests.created_at) >=', to_sql_date($filters['from_date']));
        }
        if (!empty($filters['to_date'])) {
            $this->db->where('DATE(' . db_prefix() . 'patient_tests.created_at) <=', to_sql_date($filters['to_date']));
        }
        if (!empty($filters['user_id'])) {
            $this->db->where(db_prefix() . 'invoices.sale_agent', $filters['user_id']);
        }
        if (!empty($filters['department_id'])) {
            $this->db->where(db_prefix() . 'items.department_id', $filters['department_id']);
        }
        if (!empty($filters['ref_doctor_id'])) {
            $this->db->where(db_prefix() . 'visits.referral_doctor_id', $filters['ref_doctor_id']);
        }

        // Order
        $this->db->order_by(db_prefix() . 'patient_tests.created_at', 'DESC');

        return $this->db->get()->result_array();
    }

    public function get_doctors_by_roles()
    {
        $roles = ['Doctor', 'Jr. Doctor', 'Sr. Doctor'];

        $this->db->select(db_prefix() . 'staff.staffid, ' . db_prefix() . 'staff.firstname, ' . db_prefix() . 'staff.lastname');
        $this->db->from(db_prefix() . 'staff');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role', 'left');
        $this->db->where_in(db_prefix() . 'roles.name', $roles);
        $this->db->where(db_prefix() . 'staff.active', 1);

        return $this->db->get()->result_array();
        return $this->db->get()->result_array();
    }

    public function create_transcription($test_id)
    {
        // Check if exists
        $this->db->where('patient_test_id', $test_id);
        $exists = $this->db->get(db_prefix() . 'transcriptor')->row();

        if ($exists) {
            return $exists->id;
        }

        // Get Test Details for Item ID
        $this->db->where('id', $test_id);
        $test = $this->db->get(db_prefix() . 'patient_tests')->row();

        if (!$test) {
            return false;
        }

        // Get Item Details (Check for Fixed Mode)
        $this->db->where('id', $test->item_id);
        $item = $this->db->get(db_prefix() . 'items')->row();

        $template_type = 'word';
        if ($item && isset($item->active_template_type) && $item->active_template_type == 'fixed') {
            $template_type = 'fixed';
        }

        $content = '';
        if ($template_type == 'word') {
            // Get Default Word Template
            $this->db->where('test_id', $test->item_id);
            $this->db->where('is_default', 1);
            $template = $this->db->get(db_prefix() . 'tests_word_templates')->row();

            // Fallback to first if no default? Or empty.
            if ($template) {
                $content = $template->template_content;
            } else {
                // Try fetching any template
                $this->db->where('test_id', $test->item_id);
                $template = $this->db->get(db_prefix() . 'tests_word_templates')->row();
                if ($template) {
                    $content = $template->template_content;
                }
            }
        }

        $data = [
            'patient_test_id' => $test_id,
            'staff_id' => get_staff_user_id(),
            'content' => $content,
            'status' => 'Draft',
            'template_type' => $template_type,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . 'transcriptor', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            if ($template_type == 'fixed') {
                // Check if tables exist (Safety)
                if ($this->db->table_exists(db_prefix() . 'tests_fixed_templates') && $this->db->table_exists(db_prefix() . 'tests_params')) {
                    // Populate Fixed Params
                    // 1. Get Default Fixed Template ID
                    $this->db->where('test_id', $test->item_id);
                    $this->db->where('is_default', 1);
                    $fixed_template = $this->db->get(db_prefix() . 'tests_fixed_templates')->row();

                    $fixed_template_id = 0;
                    if (!$fixed_template) {
                        // Fallback
                        $this->db->where('test_id', $test->item_id);
                        $fixed_template = $this->db->get(db_prefix() . 'tests_fixed_templates')->row();
                        if ($fixed_template)
                            $fixed_template_id = $fixed_template->id;
                    } else {
                        $fixed_template_id = $fixed_template->id;
                    }

                    if ($fixed_template_id) {
                        // Get Params
                        $this->db->where('fixed_template_id', $fixed_template_id);
                        $this->db->order_by('sort_order', 'ASC');
                        $params = $this->db->get(db_prefix() . 'tests_params')->result_array();

                        foreach ($params as $param) {
                            $p_data = [
                                'transcription_id' => $insert_id,
                                'parameter_id' => $param['id'],
                                'parameter_name' => $param['parameter_name'],
                                'result_value' => $param['default_value'],
                                'unit' => $param['unit'],
                                'referral_range' => $param['normal_range'],
                                'is_bold' => $param['is_bold'],
                                'sort_order' => $param['sort_order']
                            ];
                            $this->db->insert(db_prefix() . 'transcriptor_params', $p_data);
                        }
                    }
                }
            }

            // Update Status to Processing
            $this->db->where('id', $test_id);
            $this->db->update(db_prefix() . 'patient_tests', ['status' => 'Processing']);
        }

        return $insert_id;
    }

    public function get_transcription_params($transcription_id)
    {
        $this->db->where('transcription_id', $transcription_id);
        $this->db->order_by('sort_order', 'ASC');
        return $this->db->get(db_prefix() . 'transcriptor_params')->result_array();
    }

    public function update_transcription_params($data)
    {
        if (isset($data['params']) && is_array($data['params'])) {
            foreach ($data['params'] as $id => $val) {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'transcriptor_params', ['result_value' => $val]);
            }
        }
        return true;
    }

    public function get_transcription($id)
    {
        $this->db->select(db_prefix() . 'transcriptor.*');

        // Join for Details
        $this->db->select(db_prefix() . 'items.description as test_name');
        $this->db->select(db_prefix() . 'items.is_authorization_required');
        $this->db->select(db_prefix() . 'clients.company as patient_name');
        $this->db->select(db_prefix() . 'patients_extra.age, ' . db_prefix() . 'patients_extra.gender');
        $this->db->select('CONCAT(staff_ref.firstname, " ", staff_ref.lastname) as ref_doc_name');
        $this->db->select(db_prefix() . 'patient_tests.created_at as test_date');
        $this->db->select(db_prefix() . 'invoices.status as invoice_status, ' . db_prefix() . 'invoices.total as invoice_amount');

        $this->db->from(db_prefix() . 'transcriptor');
        $this->db->join(db_prefix() . 'patient_tests', db_prefix() . 'patient_tests.id = ' . db_prefix() . 'transcriptor.patient_test_id', 'left');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'staff as staff_ref', 'staff_ref.staffid = ' . db_prefix() . 'visits.referral_doctor_id', 'left');

        $this->db->where(db_prefix() . 'transcriptor.id', $id);
        return $this->db->get()->row();
    }

    public function get_transcription_by_test($test_id)
    {
        $this->db->select(db_prefix() . 'transcriptor.*');

        // Join for Details
        $this->db->select(db_prefix() . 'items.description as test_name');
        $this->db->select(db_prefix() . 'items.is_authorization_required');
        $this->db->select(db_prefix() . 'clients.company as patient_name');
        $this->db->select(db_prefix() . 'patients_extra.age, ' . db_prefix() . 'patients_extra.gender');
        $this->db->select('CONCAT(staff_ref.firstname, " ", staff_ref.lastname) as ref_doc_name');
        $this->db->select(db_prefix() . 'patient_tests.created_at as test_date');
        $this->db->select(db_prefix() . 'invoices.status as invoice_status, ' . db_prefix() . 'invoices.total as invoice_amount');

        $this->db->from(db_prefix() . 'transcriptor');
        $this->db->join(db_prefix() . 'patient_tests', db_prefix() . 'patient_tests.id = ' . db_prefix() . 'transcriptor.patient_test_id', 'left');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'staff as staff_ref', 'staff_ref.staffid = ' . db_prefix() . 'visits.referral_doctor_id', 'left');

        $this->db->where(db_prefix() . 'transcriptor.patient_test_id', $test_id);
        return $this->db->get()->row();
    }
}
