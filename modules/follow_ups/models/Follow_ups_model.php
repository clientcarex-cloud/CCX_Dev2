<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Follow_ups_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();

        // Create Statuses Table
        if (!$this->db->table_exists(db_prefix() . 'follow_ups_statuses')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'follow_ups_statuses` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(150) NOT NULL,
              `color` varchar(20) DEFAULT "#757575",
              `status_order` int(11) DEFAULT 0,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');

            // Add filter_visible column
            if (!$this->db->field_exists('filter_visible', db_prefix() . 'follow_ups_statuses')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'follow_ups_statuses` ADD `filter_visible` INT(11) DEFAULT 1');
            }
        }

        // Sync Default Statuses (Run for both new and existing installs)
        $target_statuses = [
            // ['name' => 'New', 'color' => '#2196F3', 'status_order' => 1], // Removed generic 'New'
            ['name' => 'Call Back Request', 'color' => '#FF9800', 'status_order' => 2], // Orange
            ['name' => 'No Response', 'color' => '#757575', 'status_order' => 3], // Grey
            ['name' => 'On-Paid Appointment', 'color' => '#4CAF50', 'status_order' => 4], // Green
            ['name' => 'On-Unpaid Appointment', 'color' => '#8BC34A', 'status_order' => 5], // Light Green
            ['name' => 'Lost', 'color' => '#F44336', 'status_order' => 6], // Red
            ['name' => 'Dropped', 'color' => '#9E9E9E', 'status_order' => 7], // Dark Grey
        ];

        // Get existing status names to minimize queries
        $existing_statuses = $this->db->select('name')->get(db_prefix() . 'follow_ups_statuses')->result_array();
        $existing_names = array_column($existing_statuses, 'name');

        foreach ($target_statuses as $status) {
            if (!in_array($status['name'], $existing_names)) {
                $this->db->insert(db_prefix() . 'follow_ups_statuses', $status);
            }
        }

        // Add follow_up_status_id to prescriptions
        if (!$this->db->field_exists('follow_up_status_id', db_prefix() . 'prescriptions')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions` ADD `follow_up_status_id` INT(11) DEFAULT NULL AFTER `next_visit_date`');

            // Seed existing prescriptions with 'New' status
            $new_status = $this->db->where('name', 'New')->get(db_prefix() . 'follow_ups_statuses')->row();
            if ($new_status) {
                $this->db->query('UPDATE `' . db_prefix() . 'prescriptions` SET `follow_up_status_id` = ' . $new_status->id . ' WHERE `next_visit_date` IS NOT NULL AND `next_visit_date` > "1970-01-01"');
            }
        }
    }

    /**
     * Get all statuses
     */
    public function get_statuses($only_visible = false)
    {
        if ($only_visible) {
            $this->db->where('filter_visible', 1);
        }
        $this->db->order_by('status_order', 'asc');
        return $this->db->get(db_prefix() . 'follow_ups_statuses')->result_array();
    }

    /**
     * Get status by name
     */
    public function get_status_by_name($name)
    {
        $this->db->where('name', $name);
        return $this->db->get(db_prefix() . 'follow_ups_statuses')->row_array();
    }

    /**
     * Add new status
     */
    public function add_status($data)
    {
        $this->db->insert(db_prefix() . 'follow_ups_statuses', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Follow Up Status Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update status
     */
    public function update_status($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'follow_ups_statuses', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Follow Up Status Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete status
     */
    public function delete_status($id)
    {
        // Check if status is used
        if (total_rows(db_prefix() . 'prescriptions', ['follow_up_status_id' => $id]) > 0) {
            return ['status' => false, 'message' => 'This status is currently in use and cannot be deleted.'];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'follow_ups_statuses');
        if ($this->db->affected_rows() > 0) {
            log_activity('Follow Up Status Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    public function get_follow_ups($status_id = '', $from = '', $to = '')
    {
        $this->db->select(
            db_prefix() . 'prescriptions.id as prescription_id, ' .
            db_prefix() . 'prescriptions.datecreated as last_visit, ' .
            db_prefix() . 'prescriptions.next_visit_date, ' .
            db_prefix() . 'prescriptions.staff_id, ' .
            db_prefix() . 'clients.userid as patient_id, ' .
            db_prefix() . 'clients.company as patient_name, ' .
            db_prefix() . 'name_titles.name as patient_title, ' . // Fetch Title
            db_prefix() . 'patients_extra.mr_number, ' .
            'CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as doctor_name, ' .
            db_prefix() . 'visits.id as visit_id, ' .
            db_prefix() . 'follow_ups_statuses.name as status_name, ' .
            db_prefix() . 'follow_ups_statuses.color as status_color, ' .
            db_prefix() . 'patients_extra.gender, ' .
            '(SELECT description FROM ' . db_prefix() . 'notes WHERE rel_type="customer" AND rel_id=' . db_prefix() . 'clients.userid ORDER BY dateadded DESC LIMIT 1) as last_note_content, ' .
            '(SELECT dateadded FROM ' . db_prefix() . 'notes WHERE rel_type="customer" AND rel_id=' . db_prefix() . 'clients.userid ORDER BY dateadded DESC LIMIT 1) as last_note_date'
        );

        $this->db->from(db_prefix() . 'prescriptions');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'prescriptions.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'prescriptions.patient_id', 'left');
        $this->db->join(db_prefix() . 'name_titles', db_prefix() . 'name_titles.id = ' . db_prefix() . 'patients_extra.title_id', 'left'); // Join Titles
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'prescriptions.staff_id', 'left');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.id = ' . db_prefix() . 'prescriptions.visit_id', 'left');
        $this->db->join(db_prefix() . 'follow_ups_statuses', db_prefix() . 'follow_ups_statuses.id = ' . db_prefix() . 'prescriptions.follow_up_status_id', 'left');

        // Join to filter by 'Completed' Consultation Status
        $this->db->join(db_prefix() . 'patient_tests', db_prefix() . 'patient_tests.invoice_id = ' . db_prefix() . 'visits.invoice_id', 'left');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');

        $this->db->where(db_prefix() . 'prescriptions.next_visit_date IS NOT NULL');
        // Avoid using 0000-00-00 directly due to NO_ZERO_DATE usage in strict SQL modes
        $this->db->where(db_prefix() . 'prescriptions.next_visit_date >', '1970-01-01');

        // Filter by Completed Status
        $this->db->where(db_prefix() . 'patient_tests.status', 'Completed');

        // Filter by Status
        if ($status_id != '') {
            $this->db->where(db_prefix() . 'prescriptions.follow_up_status_id', $status_id);
        }

        // Filter by Date
        if ($from != '' && $to != '') {
            $from = to_sql_date($from);
            $to = to_sql_date($to);
            $this->db->where(db_prefix() . 'prescriptions.next_visit_date >=', $from);
            $this->db->where(db_prefix() . 'prescriptions.next_visit_date <=', $to);
        }

        // Ensure we only look at 'Fee' items for status to avoid duplication if multiple tests exist
        $this->db->where(db_prefix() . 'items_groups.name', 'Fee');

        // Group by prescription to ensure uniqueness if multiple fee items exist (unlikely)
        $this->db->group_by(db_prefix() . 'prescriptions.id');

        $this->db->order_by(db_prefix() . 'prescriptions.next_visit_date', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_visits_count($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        return $this->db->count_all_results(db_prefix() . 'visits');
    }

    public function get_status_counts()
    {
        // Counts per status
        $this->db->select('follow_up_status_id, COUNT(*) as count');
        $this->db->from(db_prefix() . 'prescriptions');

        // Same core joins/filters to match main query logic
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.id = ' . db_prefix() . 'prescriptions.visit_id', 'left');
        $this->db->join(db_prefix() . 'patient_tests', db_prefix() . 'patient_tests.invoice_id = ' . db_prefix() . 'visits.invoice_id', 'left');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');

        $this->db->where(db_prefix() . 'prescriptions.next_visit_date IS NOT NULL');
        $this->db->where(db_prefix() . 'prescriptions.next_visit_date >', '1970-01-01');
        $this->db->where(db_prefix() . 'patient_tests.status', 'Completed');
        $this->db->where(db_prefix() . 'items_groups.name', 'Fee');

        $this->db->group_by('follow_up_status_id');
        $query = $this->db->get()->result_array();

        $counts = [];
        foreach ($query as $row) {
            $counts[$row['follow_up_status_id']] = $row['count'];
        }
        return $counts;
    }


    public function update_prescription_status($prescription_id, $status_id)
    {
        $this->db->where('id', $prescription_id);
        $this->db->update(db_prefix() . 'prescriptions', ['follow_up_status_id' => $status_id]);
        return $this->db->affected_rows() > 0;
    }
}
