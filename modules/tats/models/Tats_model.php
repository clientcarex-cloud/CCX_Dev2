<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tats_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_tat_report()
    {
        $this->db->select(db_prefix() . 'patient_test_history.*');
        $this->db->select(db_prefix() . 'items.description as item_name');
        $this->db->select(db_prefix() . 'items_groups.name as group_name');
        $this->db->select('CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as staff_name');

        $this->db->from(db_prefix() . 'patient_test_history');
        $this->db->join(db_prefix() . 'patient_tests', db_prefix() . 'patient_tests.id = ' . db_prefix() . 'patient_test_history.test_id', 'left');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'patient_test_history.staff_id', 'left');

        $this->db->order_by(db_prefix() . 'items_groups.name', 'ASC');
        $this->db->order_by(db_prefix() . 'patient_test_history.test_id', 'ASC');
        $this->db->order_by(db_prefix() . 'patient_test_history.created_at', 'ASC');

        $history = $this->db->get()->result_array();

        $report = [];

        foreach ($history as $row) {
            $group = $row['group_name'] ? $row['group_name'] : 'Uncategorized';
            $test_id = $row['test_id'];

            if (!isset($report[$group])) {
                $report[$group] = [];
            }
            if (!isset($report[$group][$test_id])) {
                $report[$group][$test_id] = [
                    'item_name' => $row['item_name'],
                    'history' => []
                ];
            }

            $report[$group][$test_id]['history'][] = $row;
        }

        return $report;
    }
}
