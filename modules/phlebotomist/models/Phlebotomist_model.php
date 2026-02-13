<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Phlebotomist_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_blood_sample_requests($status = '', $date = '')
    {
        $this->db->select(db_prefix() . 'patient_tests.id, ' . db_prefix() . 'patient_tests.created_at, ' . db_prefix() . 'patient_tests.status');
        $this->db->select(db_prefix() . 'patient_tests.patient_id'); // Added this
        $this->db->select(db_prefix() . 'patient_tests.is_emergency'); // Added this for emoji display
        $this->db->select(db_prefix() . 'items.description as test_name');
        $this->db->select(db_prefix() . 'clients.company as patient_name');
        $this->db->select(db_prefix() . 'visits.visit_code');
        $this->db->select(db_prefix() . 'patients_extra.mr_number');
        // Select collection info
        $this->db->select(db_prefix() . 'phlebotomist.id as collection_id, ' . db_prefix() . 'phlebotomist.created_at as collected_at');

        $this->db->from(db_prefix() . 'patient_tests');

        // Join with Items to filter by blood sample required
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');

        // Join with Clients/Patients
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left');

        // Join with Visits
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');

        // Join Invoice to get Sale Agent (User)
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'invoices.sale_agent', 'left');

        // Join Phlebotomist Log
        $this->db->join(db_prefix() . 'phlebotomist', db_prefix() . 'phlebotomist.patient_test_id = ' . db_prefix() . 'patient_tests.id', 'left');

        $this->db->select('CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as user_name');

        // Filter
        $this->db->where(db_prefix() . 'items.is_blood_sample_required', 1);

        // Date Filter
        if ($date) {
            $this->db->where('DATE(' . db_prefix() . 'patient_tests.created_at)', $date);
        }

        if ($status == 'Sample Collected') {
            $this->db->where(db_prefix() . 'phlebotomist.id IS NOT NULL');
        } elseif ($status == 'Emergency') {
            $this->db->where(db_prefix() . 'patient_tests.is_emergency', 1);
            // And show only pending? Usually yes.
            $this->db->where(db_prefix() . 'phlebotomist.id IS NULL');
        } elseif ($status != '' && $status != 'All') {
            $this->db->where(db_prefix() . 'patient_tests.status', $status);
        } elseif ($status == 'All' || $status == '') {
            // "All" tab should show Regular, Emergency, Pending, Sample Lost, Sample Insufficient
            // But we do not want to show collected ones?
            // Existing logic: All shows everything EXCEPT Sample Collected (since Sample Collected is a separate tab/state? 
            // Wait, "Sample Collected" status updates the row status to 'Sample Collected'? 
            // If so, "All" should include them if we want truly ALL. 
            // BUT, the existing logic (before my edit) has a group_start/end block for specific statuses.
            // Start: Regular OR Emergency OR Pending OR Sample Lost OR Sample Insufficient.
            // It excluded 'Sample Collected' status from 'All'.
            // I will preserve this logic.
            $this->db->group_start();
            $this->db->where(db_prefix() . 'patient_tests.status', 'Regular');
            // $this->db->or_where(db_prefix() . 'patient_tests.status', 'Emergency'); // Removed status check
            $this->db->or_where(db_prefix() . 'patient_tests.is_emergency', 1); // Added flag check for All tab too
            $this->db->or_where(db_prefix() . 'patient_tests.status', 'Pending');
            $this->db->or_where(db_prefix() . 'patient_tests.status', 'Sample Lost');
            $this->db->or_where(db_prefix() . 'patient_tests.status', 'Sample Insufficient');
            $this->db->group_end();
            $this->db->where(db_prefix() . 'phlebotomist.id IS NULL'); // Ensure not collected
        }

        $this->db->order_by(db_prefix() . 'patient_tests.created_at', 'DESC');

        return $this->db->get()->result_array();
    }

    public function count_by_status($status, $date = '')
    {
        $this->db->from(db_prefix() . 'patient_tests');

        // Join Items for blood sample check
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->where(db_prefix() . 'items.is_blood_sample_required', 1);

        if ($status == 'Emergency') {
            $this->db->where(db_prefix() . 'patient_tests.is_emergency', 1);
            // Count pending emergencies
            // We need to join phlebotomist table to check if collected?
            // count_by_status didn't join phlebotomist table before.
            // But the 'All' list implies 'Pending'.
            // Let's check if collected logic should be here.
            // If I want to count strictly pending emergencies, I should join phlebo table.

            // Re-adding join for count consistency
            $this->db->join(db_prefix() . 'phlebotomist', db_prefix() . 'phlebotomist.patient_test_id = ' . db_prefix() . 'patient_tests.id', 'left');
            $this->db->where(db_prefix() . 'phlebotomist.id IS NULL');

        } elseif ($status) {
            $this->db->where(db_prefix() . 'patient_tests.status', $status);
        }

        if ($date) {
            $this->db->where('DATE(' . db_prefix() . 'patient_tests.created_at)', $date);
        }

        return $this->db->count_all_results();
    }

    public function add_collection_log($data)
    {
        $this->db->insert(db_prefix() . 'phlebotomist', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $sample_id = 'S-' . $insert_id;
            $this->db->where('id', $insert_id);
            $this->db->update(db_prefix() . 'phlebotomist', ['sample_id' => $sample_id]);
        }

        return $insert_id;
    }

    public function update_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'patient_tests', ['status' => $status]);
    }

    public function get_barcode_template()
    {
        $this->db->where('type', 'barcode');
        $this->db->where('is_default', 1);
        return $this->db->get(db_prefix() . 'print_templates')->row();
    }

    public function get_test_details($id)
    {
        // Similar to get_blood_sample_requests but for single ID and specific fields for label
        // We can reuse the query logic or just fetch what is needed.
        // Let's reuse the main query but filter by ID to ensure we get all the joins (mr_number, etc.)
        $this->db->select(db_prefix() . 'patient_tests.id, ' . db_prefix() . 'patient_tests.created_at');
        $this->db->select(db_prefix() . 'items.description as test_name');
        $this->db->select(db_prefix() . 'clients.company as patient_name, ' . db_prefix() . 'clients.userid as client_id');
        $this->db->select(db_prefix() . 'visits.visit_code');
        $this->db->select(db_prefix() . 'patients_extra.mr_number, ' . db_prefix() . 'patients_extra.gender, ' . db_prefix() . 'patients_extra.age');
        $this->db->select(db_prefix() . 'phlebotomist.created_at as collected_at, ' . db_prefix() . 'phlebotomist.sample_id'); // Added sample_id

        $this->db->from(db_prefix() . 'patient_tests');

        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'phlebotomist', db_prefix() . 'phlebotomist.patient_test_id = ' . db_prefix() . 'patient_tests.id', 'left');

        $this->db->where(db_prefix() . 'patient_tests.id', $id);

        return $this->db->get()->row_array();
    }
}
