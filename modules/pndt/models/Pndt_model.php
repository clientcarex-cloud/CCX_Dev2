<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pndt_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_pndt_visits($filters = [])
    {
        $this->db->select(db_prefix() . 'patient_tests.id, ' . db_prefix() . 'patient_tests.created_at, ' . db_prefix() . 'patient_tests.status');
        $this->db->select(db_prefix() . 'patient_tests.is_emergency');
        $this->db->select(db_prefix() . 'items.description as test_name');
        $this->db->select(db_prefix() . 'clients.company as patient_name, ' . db_prefix() . 'clients.userid as client_id');
        $this->db->select(db_prefix() . 'visits.visit_code, ' . db_prefix() . 'visits.id as visit_id');

        // Ref Doc
        $this->db->select('CONCAT(staff_ref.firstname, " ", staff_ref.lastname) as ref_doc_name');

        // MR Number, Age, Gender
        $this->db->select(db_prefix() . 'patients_extra.mr_number, ' . db_prefix() . 'patients_extra.gender, ' . db_prefix() . 'patients_extra.age');

        // Payment Info
        $this->db->select(db_prefix() . 'invoices.status as invoice_status, ' . db_prefix() . 'invoices.total, ' . db_prefix() . 'invoices.id as invoice_id');

        $this->db->from(db_prefix() . 'patient_tests');

        // Joins
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'staff as staff_ref', 'staff_ref.staffid = ' . db_prefix() . 'visits.referral_doctor_id', 'left');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'departments', db_prefix() . 'departments.departmentid = ' . db_prefix() . 'items.department_id', 'left');

        // FIXED FILTERS
        // 1. Item Group == Tests
        $this->db->where(db_prefix() . 'items_groups.name', 'Tests');
        // 2. Department == Ultrasound
        $this->db->where(db_prefix() . 'departments.name', 'Ultrasound');

        // Apply Status Filter (Tabs)
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
    }

    public function get_request($id)
    {
        $this->db->select(db_prefix() . 'patient_tests.id, ' . db_prefix() . 'patient_tests.created_at, ' . db_prefix() . 'patient_tests.status');
        $this->db->select(db_prefix() . 'patient_tests.is_emergency');
        $this->db->select(db_prefix() . 'items.description as test_name');
        $this->db->select(db_prefix() . 'clients.company as patient_name, ' . db_prefix() . 'clients.userid as client_id, ' . db_prefix() . 'clients.phonenumber as mobile_no, ' . db_prefix() . 'clients.address, ' . db_prefix() . 'clients.city, ' . db_prefix() . 'clients.state, ' . db_prefix() . 'clients.zip');
        $this->db->select(db_prefix() . 'visits.visit_code, ' . db_prefix() . 'visits.id as visit_id, ' . db_prefix() . 'visits.referral_doctor_id');

        // Ref Doc
        $this->db->select('CONCAT(staff_ref.firstname, " ", staff_ref.lastname) as ref_doc_name');

        // MR Number, Age, Gender
        $this->db->select(db_prefix() . 'patients_extra.mr_number, ' . db_prefix() . 'patients_extra.gender, ' . db_prefix() . 'patients_extra.age');

        // Safely check for dob and father_husband_name
        $extra_fields = $this->db->list_fields(db_prefix() . 'patients_extra');
        if (in_array('dob', $extra_fields)) {
            $this->db->select(db_prefix() . 'patients_extra.dob');
        }
        if (in_array('father_husband_name', $extra_fields)) {
            $this->db->select(db_prefix() . 'patients_extra.father_husband_name');
        }
        if (in_array('attender_name', $extra_fields)) {
            $this->db->select(db_prefix() . 'patients_extra.attender_name');
        }

        $this->db->from(db_prefix() . 'patient_tests');

        // Joins
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'staff as staff_ref', 'staff_ref.staffid = ' . db_prefix() . 'visits.referral_doctor_id', 'left');

        $this->db->where(db_prefix() . 'patient_tests.id', $id);

        $request = $this->db->get()->row_array();

        if ($request) {
            // Fetch PNDT Data if exists
            $this->db->where('patient_test_id', $id);
            $pndt = $this->db->get(db_prefix() . 'pndt')->row_array();
            if ($pndt) {
                $request['pndt_form_data'] = json_decode($pndt['content'], true);
                $request['pndt_id'] = $pndt['id'];
            }
        }

        return $request;
    }

    public function save_pndt_form($data)
    {
        $patient_test_id = $data['patient_test_id'];
        unset($data['patient_test_id']);

        $insert_data = [
            'patient_test_id' => $patient_test_id,
            'staff_id' => get_staff_user_id(),
            'content' => json_encode($data),
            'status' => 'Draft', // You might want to pass status from form if "Finalize" button exists
        ];

        // Check for updated_at column
        if ($this->db->field_exists('updated_at', db_prefix() . 'pndt')) {
            $insert_data['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('patient_test_id', $patient_test_id);
        $exists = $this->db->get(db_prefix() . 'pndt')->row();

        if ($exists) {
            $this->db->where('id', $exists->id);
            $this->db->update(db_prefix() . 'pndt', $insert_data);
            return $exists->id;
        } else {
            if ($this->db->field_exists('created_at', db_prefix() . 'pndt')) {
                $insert_data['created_at'] = date('Y-m-d H:i:s');
            }
            $this->db->insert(db_prefix() . 'pndt', $insert_data);
            return $this->db->insert_id();
        }
    }
}
