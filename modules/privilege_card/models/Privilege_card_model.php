<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Privilege_card_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get privilege card types (plans)
     * @param  mixed $id Optional type ID
     * @return mixed     Object or Array
     */
    public function get_types($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'privilege_card_types')->row();
        }

        return $this->db->get(db_prefix() . 'privilege_card_types')->result_array();
    }

    /**
     * Add new privilege card type
     * @param array $data
     */
    public function add_type($data)
    {
        $this->db->insert(db_prefix() . 'privilege_card_types', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Privilege Card Type Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update privilege card type
     * @param  array $data
     * @param  mixed $id
     * @return boolean
     */
    public function update_type($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'privilege_card_types', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Privilege Card Type Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete privilege card type
     * @param  mixed $id
     * @return boolean|array
     */
    public function delete_type($id)
    {
        // Check if used in members
        $this->db->where('card_type_id', $id);
        $member = $this->db->get(db_prefix() . 'privilege_card_members')->row();
        if ($member) {
            return ['referenced' => true];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'privilege_card_types');
        if ($this->db->affected_rows() > 0) {
            log_activity('Privilege Card Type Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get privilege card members (issued cards)
     * @param  mixed $id Optional member ID
     * @return mixed     Object or Array
     */
    public function get_members($id = '')
    {
        $this->db->select(db_prefix() . 'privilege_card_members.*, ' . db_prefix() . 'clients.company as patient_name, ' . db_prefix() . 'privilege_card_types.name as plan_name');
        $this->db->from(db_prefix() . 'privilege_card_members');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'privilege_card_members.patient_id', 'left');
        $this->db->join(db_prefix() . 'privilege_card_types', db_prefix() . 'privilege_card_types.id = ' . db_prefix() . 'privilege_card_members.card_type_id', 'left');

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'privilege_card_members.id', $id);
            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    /**
     * Add new privilege card member (Issue Card)
     * @param array $data
     */
    public function add_member($data)
    {
        if (isset($data['issue_date']) && !empty($data['issue_date'])) {
            $data['issue_date'] = to_sql_date($data['issue_date']);
        }
        if (isset($data['expiry_date']) && !empty($data['expiry_date'])) {
            $data['expiry_date'] = to_sql_date($data['expiry_date']);
        }

        // Generate Card Number if not provided (Simplistic approach)
        if (!isset($data['card_number']) || empty($data['card_number'])) {
            $data['card_number'] = 'PC-' . date('Ymd') . '-' . rand(1000, 9999);
        }

        $this->db->insert(db_prefix() . 'privilege_card_members', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('Privilege Card Issued [ID: ' . $insert_id . ', Card Number: ' . $data['card_number'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update privilege card member
     * @param  array $data
     * @param  mixed $id
     * @return boolean
     */
    public function update_member($data, $id)
    {
        if (isset($data['issue_date']) && !empty($data['issue_date'])) {
            $data['issue_date'] = to_sql_date($data['issue_date']);
        }
        if (isset($data['expiry_date']) && !empty($data['expiry_date'])) {
            $data['expiry_date'] = to_sql_date($data['expiry_date']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'privilege_card_members', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Privilege Card Member Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete privilege card member
     * @param  mixed $id
     * @return boolean
     */
    public function delete_member($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'privilege_card_members');
        if ($this->db->affected_rows() > 0) {
            log_activity('Privilege Card Member Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
}
