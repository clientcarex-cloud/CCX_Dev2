<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Token_system_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get tokens by date
     * @param  string $date Y-m-d
     * @param  int $doctor_id Optional filter
     * @return array
     */
    public function get_todays_tokens($date = '', $doctor_id = 0)
    {
        if ($date == '')
            $date = date('Y-m-d');

        $this->db->select(db_prefix() . 'tokens.*, CONCAT(firstname, " ", lastname) as doctor_name');
        $this->db->from(db_prefix() . 'tokens');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'tokens.doctor_id', 'left');

        $this->db->where('DATE(' . db_prefix() . 'tokens.created_at)', $date);

        if ($doctor_id > 0) {
            $this->db->where('doctor_id', $doctor_id);
        }

        // Order by status (Pending 0, Calling 1) -> 1, 0, 3, 2
        $this->db->order_by('FIELD(status, 1, 0, 3, 2)');
        $this->db->order_by('id', 'asc'); // FIFO
        return $this->db->get()->result_array();
    }

    /**
     * Add new token
     * @param array $data
     */
    public function add($data)
    {
        // Validation: Doctor ID is mandatory
        if (!isset($data['doctor_id']) || empty($data['doctor_id']) || $data['doctor_id'] == 0) {
            return false;
        }

        // Generate Token Number
        // Independent Series per Doctor
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where('doctor_id', $data['doctor_id']);
        $today_count = $this->db->count_all_results(db_prefix() . 'tokens');
        $next_number = $today_count + 1;

        // Format: 001
        $token_number = str_pad($next_number, 3, '0', STR_PAD_LEFT);

        $insert_data = [
            'token_number' => $token_number,
            'patient_name' => $data['patient_name'],
            'status' => 0, // Pending
            'created_at' => date('Y-m-d H:i:s'),
            'staff_id' => get_staff_user_id() ? get_staff_user_id() : 0, // 0 if system/auto
            'doctor_id' => $data['doctor_id'],
        ];

        if (isset($data['patient_id']) && $data['patient_id'] != '') {
            $insert_data['patient_id'] = $data['patient_id'];
        }

        $this->db->insert(db_prefix() . 'tokens', $insert_data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Token Created [ID:' . $insert_id . ', Number:' . $token_number . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update token status
     * @param  int $id
     * @param  int $status
     * @return boolean
     */
    public function update_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tokens', [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Token Status Updated [ID:' . $id . ', Status:' . $status . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete token
     * @param  int $id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tokens');
        if ($this->db->affected_rows() > 0) {
            log_activity('Token Deleted [ID:' . $id . ']');
            return true;
        }
        return false;
    }
}
