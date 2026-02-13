<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Manage_payments_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_payments($filters = [])
    {
        $this->db->select(db_prefix() . 'visits.id as visit_id, ' . db_prefix() . 'visits.created_at as visit_date, ' . db_prefix() . 'visits.visit_code');
        $this->db->select(db_prefix() . 'clients.company as patient_name, ' . db_prefix() . 'clients.userid as client_id');
        $this->db->select('CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as doctor_name');

        // Determine invoice table name for subqueries
        $invoice_table = (isset($filters['status']) && $filters['status'] == 'archived') ? db_prefix() . 'invoices_archive' : db_prefix() . 'invoices';
        $payment_table = (isset($filters['status']) && $filters['status'] == 'archived') ? db_prefix() . 'invoicepaymentrecords_archive' : db_prefix() . 'invoicepaymentrecords';

        // Invoice Details - Use 'invoices' alias which we set in the JOINs
        $this->db->select('invoices.id as invoice_id, invoices.number as invoice_number, invoices.prefix as invoice_prefix, invoices.date as invoice_date, invoices.total, invoices.subtotal, invoices.discount_total as discount, invoices.status as invoice_status, invoices.sale_agent');

        // Calculate Paid (Keep aggregate for compatibility)
        $this->db->select('(SELECT SUM(amount) FROM ' . $payment_table . ' WHERE invoiceid = invoices.id) as paid_amount');
        $this->db->select('(SELECT COUNT(id) FROM ' . $payment_table . ' WHERE invoiceid = invoices.id) as receipt_count');

        // Refund? Placeholder 
        $this->db->select('0 as refund_amount');

        $this->db->from(db_prefix() . 'visits');

        // ... (Rest of existing joins and filters - skipping for brevity in replacement search but ensuring context) ...

        if (isset($filters['status']) && $filters['status'] == 'archived') {
            // Join Archive Tables
            $this->db->join(db_prefix() . 'invoices_archive as invoices', 'invoices.id = ' . db_prefix() . 'visits.invoice_id', 'left');
            $this->db->join(db_prefix() . 'clients', 'clients.userid = ' . db_prefix() . 'visits.patient_id', 'left');
            $this->db->join(db_prefix() . 'staff', 'staff.staffid = ' . db_prefix() . 'visits.referral_doctor_id', 'left');
        } else {
            // Join Normal Tables
            $this->db->join(db_prefix() . 'invoices as invoices', 'invoices.id = ' . db_prefix() . 'visits.invoice_id', 'left');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'visits.patient_id', 'left');
            $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'visits.referral_doctor_id', 'left');
        }

        // ... (Filters logic - ensuring these aren't broken by this replace, wait, replace block is better if smaller)


        // ... (This replace is too big effectively. Let's make smaller chunks)

        // Filters
        if (!empty($filters['report_from'])) {
            $this->db->where('DATE(' . db_prefix() . 'visits.created_at) >=', to_sql_date($filters['report_from']));
        }
        if (!empty($filters['report_to'])) {
            $this->db->where('DATE(' . db_prefix() . 'visits.created_at) <=', to_sql_date($filters['report_to']));
        }
        if (!empty($filters['user_id'])) {
            // Assuming User refers to the one who created the visit or invoice sale agent.
            $this->db->where('invoices.sale_agent', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            // Status: 'all', 'dues' (Overdue/Unpaid/Partial), 'partial', 'refund', 'paid'
            // Invoice Statuses: 1=Unpaid, 2=Paid, 3=Partial, 4=Overdue, 5=Cancelled, 6=Draft

            if ($filters['status'] == 'dues') {
                $this->db->where_in('invoices.status', [1, 3, 4]); // Unpaid, Partial, Overdue
            } elseif ($filters['status'] == 'partial') {
                $this->db->where('invoices.status', 3);
            } elseif ($filters['status'] == 'paid') {
                $this->db->where('invoices.status', 2);
            } elseif ($filters['status'] == 'refund') {
                // Refund logic? Maybe status 5 (Cancelled) or checking credit notes?
                // For now, let's assume no refund filter or just placeholder
            } elseif ($filters['status'] == 'archived') {
                $this->db->where(db_prefix() . 'visits.is_archived', 1);
            }
        }

        // Exclude archived from other views
        if (isset($filters['status']) && $filters['status'] != 'archived') {
            $this->db->group_start();
            $this->db->where(db_prefix() . 'visits.is_archived', 0);
            $this->db->or_where(db_prefix() . 'visits.is_archived IS NULL');
            $this->db->group_end();
        }

        $this->db->order_by(db_prefix() . 'visits.created_at', 'DESC');

        $visits = $this->db->get()->result_array();

        // Fetch Tests for each visit
        foreach ($visits as &$visit) {
            $this->db->select(db_prefix() . 'items.description');
            $this->db->from(db_prefix() . 'patient_tests');
            $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id');
            // Check by invoice_id
            $this->db->where(db_prefix() . 'patient_tests.invoice_id', $visit['invoice_id']);

            $item_groups = get_option('patients_review_item_groups');
            if (!empty($item_groups)) {
                $this->db->where_in(db_prefix() . 'items.group_id', explode(',', $item_groups));
            }
            $tests = $this->db->get()->result_array();

            $visit['tests'] = $tests;
            $visit['test_count'] = count($tests);

            $visit['receipts'] = [];
            if ($visit['invoice_id']) {
                $this->db->where('invoiceid', $visit['invoice_id']);
                $visit['receipts'] = $this->db->get($payment_table)->result_array();
            }

            // Calculate Due
            $total = isset($visit['total']) ? $visit['total'] : 0;
            $paid = isset($visit['paid_amount']) ? $visit['paid_amount'] : 0;
            $visit['due_amount'] = $total - $paid;

            // Refund logic if needed
            $visit['refund_amount'] = 0; // Placeholder
        }

        return $visits;
    }
}
