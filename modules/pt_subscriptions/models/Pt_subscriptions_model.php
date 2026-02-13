<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pt_subscriptions_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        // Self-heal: Ensure columns exist
        if (!$this->db->field_exists('is_pt_subscription', db_prefix() . 'invoices')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "invoices` ADD `is_pt_subscription` INT(1) DEFAULT 0;");
        }
        if (!$this->db->field_exists('pt_subscription_status', db_prefix() . 'invoices')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "invoices` ADD `pt_subscription_status` VARCHAR(50) DEFAULT 'active';");
        }
    }

    /**
     * Get data for the main subscriptions table with filters
     */
    public function get_subscriptions_table_data($status = '', $from = '', $to = '')
    {
        $this->db->select(
            db_prefix() . 'clients.userid as patient_id, ' .
            db_prefix() . 'clients.company as patient_name, ' .
            db_prefix() . 'name_titles.name as patient_title, ' .
            db_prefix() . 'patients_extra.mr_number, ' .
            db_prefix() . 'patients_extra.gender, ' .
            '(SELECT description FROM ' . db_prefix() . 'notes WHERE rel_type="customer" AND rel_id=' . db_prefix() . 'clients.userid ORDER BY dateadded DESC LIMIT 1) as last_note_content, ' .
            '(SELECT dateadded FROM ' . db_prefix() . 'notes WHERE rel_type="customer" AND rel_id=' . db_prefix() . 'clients.userid ORDER BY dateadded DESC LIMIT 1) as last_note_date, ' .
            '(SELECT COUNT(*) FROM ' . db_prefix() . 'visits WHERE patient_id=' . db_prefix() . 'clients.userid) as visits_count, ' .
            '(SELECT datecreated FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid ORDER BY datecreated DESC LIMIT 1) as last_visit_date, ' .
            '(SELECT id FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid ORDER BY datecreated DESC LIMIT 1) as prescription_id, ' .
            '(SELECT next_visit_date FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid AND next_visit_date >= CURDATE() ORDER BY next_visit_date ASC LIMIT 1) as next_visit_date, ' .
            '(SELECT staff_id FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid AND next_visit_date >= CURDATE() ORDER BY next_visit_date ASC LIMIT 1) as next_doctor_id, ' .
            // Latest Subscription Status for Filtering
            '(SELECT status FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1 ORDER BY date DESC LIMIT 1) as latest_sub_status, ' .
            '(SELECT pt_subscription_status FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1 ORDER BY date DESC LIMIT 1) as subscription_state, ' .
            '(SELECT date FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1 ORDER BY date DESC LIMIT 1) as latest_sub_date, ' .
            '(SELECT COUNT(*) FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1) as total_subs'
        );

        $this->db->from(db_prefix() . 'clients');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'clients.userid', 'left');
        $this->db->join(db_prefix() . 'name_titles', db_prefix() . 'name_titles.id = ' . db_prefix() . 'patients_extra.title_id', 'left');

        // Always filter to show only patients with at least 1 subscription
        $this->db->having('total_subs >', 0);

        // Filter: Status
        if ($status != '' && $status != 'all') {
            if ($status == 'active' || $status == 'inactive' || $status == 'cancelled') {
                $this->db->having('subscription_state', $status);
            } else {
                $this->db->having('latest_sub_status', $status);
            }
        }

        // Filter: Date Range (Applied to latest_sub_date)
        if ($from != '') {
            $this->db->having('latest_sub_date >=', to_sql_date($from));
        }
        if ($to != '') {
            $this->db->having('latest_sub_date <=', to_sql_date($to));
        }

        $this->db->order_by(db_prefix() . 'clients.userid', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Get counts for tabs
     */
    public function get_subscription_counts($from = '', $to = '')
    {
        $statuses = [
            'all' => 'Subscriptions',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'cancelled' => 'Cancelled',
            '2' => 'Paid',
            '1' => 'Due',
            '3' => 'Partial',
            '4' => 'Overdue'
        ];

        $counts = [];
        foreach ($statuses as $id => $label) {
            $this->db->select('COUNT(*) as count');
            $this->db->from(db_prefix() . 'clients');

            // Subquery Expressions for WHERE clause in Count
            $sub_status_sql = '(SELECT status FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1 ORDER BY date DESC LIMIT 1)';
            $sub_state_sql = '(SELECT pt_subscription_status FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1 ORDER BY date DESC LIMIT 1)';
            $sub_date_sql = '(SELECT date FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1 ORDER BY date DESC LIMIT 1)';

            // Always ensure the patient has at least one recurring invoice
            $this->db->where("$sub_status_sql IS NOT NULL", NULL, FALSE);

            if ($id == 'active' || $id == 'inactive' || $id == 'cancelled') {
                $this->db->where("$sub_state_sql = '$id'", NULL, FALSE);
            } elseif ($id != 'all') {
                $this->db->where("$sub_status_sql = $id", NULL, FALSE);
            }

            if ($from != '') {
                $this->db->where("$sub_date_sql >=", to_sql_date($from), FALSE);
            }
            if ($to != '') {
                $this->db->where("$sub_date_sql <=", to_sql_date($to), FALSE);
            }

            $result = $this->db->get()->row();
            $counts[$id] = $result ? $result->count : 0;
        }
        return $counts;
    }

    /**
     * Get prescription/visit data helper to find "Next Visit"
     */
    public function get_next_visit_data($patient_id)
    {
        $this->db->select('next_visit_date, staff_id');
        $this->db->from(db_prefix() . 'prescriptions');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('next_visit_date >=', date('Y-m-d'));
        $this->db->order_by('next_visit_date', 'ASC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    /**
     * Get subscription invoice data
     */
    public function get_subscription_data($patient_id)
    {
        // Find the latest actively recurring invoice
        $this->db->select('*');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where('clientid', $patient_id);
        $this->db->where('is_pt_subscription', 1); // Use custom flag
        $this->db->order_by('date', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function count_subscriptions($patient_id)
    {
        $this->db->where('clientid', $patient_id);
        $this->db->where('is_pt_subscription', 1);
        return $this->db->count_all_results(db_prefix() . 'invoices');
    }

    /**
     * Get list of doctors
     */
    public function get_doctors()
    {
        // Get Role IDs for Doctor, Jr. Doctor, Sr. Doctor
        $this->db->where_in('name', ['Doctor', 'Jr. Doctor', 'Sr. Doctor']);
        $roles = $this->db->get(db_prefix() . 'roles')->result_array();

        if (empty($roles)) {
            return [];
        }

        $role_ids = array_column($roles, 'roleid');

        $this->db->select('staffid, firstname, lastname');
        $this->db->from(db_prefix() . 'staff');
        $this->db->where_in('role', $role_ids);
        $this->db->where('active', 1);
        return $this->db->get()->result_array();
    }

    /**
     * Get single subscription (invoice) by ID
     */
    public function get_subscription($id)
    {
        $this->db->where('id', $id);
        $this->db->where('is_pt_subscription', 1);
        return $this->db->get(db_prefix() . 'invoices')->row();
    }

    /**
     * Update subscription details
     */
    public function update_subscription($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'invoices', $data);
    }

    /**
     * Change subscription status (Cancel/Activate)
     */
    public function change_subscription_status($id, $status)
    {
        $this->db->where('id', $id);

        $data = ['pt_subscription_status' => $status];
        if ($status == 'cancelled') {
            // Stop recurrence
            $data['recurring'] = 0;
            $data['cycles'] = 0;
        } elseif ($status == 'active') {
            // If reactivating, we assume user keeps existing recurring settings in DB 
            // IF they were not cleared. But we cleared them on cancel.
            // User should EDIT to fix recurring.
            // We won't set recurring here as we don't know the values.
            // Just updating status.
        } elseif ($status == 'inactive') {
            // Support legacy thought process just in case
            $data['recurring'] = 0;
            $data['cycles'] = 0;
        }

        return $this->db->update(db_prefix() . 'invoices', $data);
    }

    public function log_activity($description)
    {
        $data = [
            'description' => $description,
            'date' => date('Y-m-d H:i:s'),
            'staffid' => get_staff_user_id()
        ];
        $this->db->insert(db_prefix() . 'activity_log', $data);
    }

}
