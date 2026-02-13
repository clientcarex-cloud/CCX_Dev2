<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Surgeries_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get surgery types
     * @param  mixed $id Optional surgery type ID
     * @return mixed     Object or Array
     */
    public function get_surgery_types($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'surgery_types')->row();
        }

        return $this->db->get(db_prefix() . 'surgery_types')->result_array();
    }

    /**
     * Add new surgery type
     * @param array $data
     */
    public function add_surgery_type($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        // Filter valid columns
        $valid_columns = ['name', 'description', 'price', 'created_at'];
        $data = array_intersect_key($data, array_flip($valid_columns));

        $this->db->insert(db_prefix() . 'surgery_types', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Surgery Type Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update surgery type
     * @param  array $data
     * @param  mixed $id
     * @return boolean
     */
    public function update_surgery_type($data, $id)
    {
        // Filter valid columns
        $valid_columns = ['name', 'description', 'price'];
        $data = array_intersect_key($data, array_flip($valid_columns));

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'surgery_types', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Surgery Type Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete surgery type
     * @param  mixed $id
     * @return boolean|array
     */
    public function delete_surgery_type($id)
    {
        if (is_reference_in_table('surgery_type_id', db_prefix() . 'surgeries', $id)) {
            return ['referenced' => true];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'surgery_types');
        if ($this->db->affected_rows() > 0) {
            log_activity('Surgery Type Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get surgeries
     * @param  mixed $id Optional surgery ID
     * @return mixed     Object or Array
     */
    public function get_surgeries($id = '')
    {
        $this->db->select(db_prefix() . 'surgeries.*, ' .
            db_prefix() . 'surgery_types.name as surgery_name, ' .
            'p.company as patient_name, ' .
            'CONCAT(s1.firstname, " ", s1.lastname) as surgeon_name');
        $this->db->from(db_prefix() . 'surgeries');
        $this->db->join(db_prefix() . 'surgery_types', db_prefix() . 'surgery_types.id = ' . db_prefix() . 'surgeries.surgery_type_id', 'left');
        $this->db->join(db_prefix() . 'clients as p', 'p.userid = ' . db_prefix() . 'surgeries.patient_id', 'left');
        $this->db->join(db_prefix() . 'staff as s1', 's1.staffid = ' . db_prefix() . 'surgeries.surgeon_id', 'left');

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'surgeries.id', $id);
            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    /**
     * Add new surgery
     * @param array $data
     */
    public function add_surgery($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        if (isset($data['surgery_date']) && !empty($data['surgery_date'])) {
            $data['surgery_date'] = to_sql_date($data['surgery_date']);
        }

        if (isset($data['id'])) {
            unset($data['id']);
        }

        // Handle nullable integer fields
        if (isset($data['assistant_surgeon_id']) && $data['assistant_surgeon_id'] == '') {
            $data['assistant_surgeon_id'] = null;
        }
        if (isset($data['anesthetist_id']) && $data['anesthetist_id'] == '') {
            $data['anesthetist_id'] = null;
        }

        // Filter valid columns
        $valid_columns = ['patient_id', 'surgery_type_id', 'surgeon_id', 'assistant_surgeon_id', 'anesthetist_id', 'surgery_date', 'surgery_time', 'ot_room_number', 'status', 'notes', 'created_at'];
        $data = array_intersect_key($data, array_flip($valid_columns));

        $this->db->insert(db_prefix() . 'surgeries', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Surgery Record Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update surgery
     * @param  array $data
     * @param  mixed $id
     * @return boolean
     */
    public function update_surgery($data, $id)
    {
        if (isset($data['surgery_date']) && !empty($data['surgery_date'])) {
            $data['surgery_date'] = to_sql_date($data['surgery_date']);
        }

        // Handle nullable integer fields
        if (isset($data['assistant_surgeon_id']) && $data['assistant_surgeon_id'] == '') {
            $data['assistant_surgeon_id'] = null;
        }
        if (isset($data['anesthetist_id']) && $data['anesthetist_id'] == '') {
            $data['anesthetist_id'] = null;
        }

        // Filter valid columns
        $valid_columns = ['patient_id', 'surgery_type_id', 'surgeon_id', 'assistant_surgeon_id', 'anesthetist_id', 'surgery_date', 'surgery_time', 'ot_room_number', 'status', 'notes'];
        $data = array_intersect_key($data, array_flip($valid_columns));

        $this->db->where('id', $id);
        $success = $this->db->update(db_prefix() . 'surgeries', $data);
        if ($success && $this->db->affected_rows() > 0) {
            log_activity('Surgery Record Updated [ID: ' . $id . ']');
        }
        return $success;
    }

    /**
     * Delete surgery
     * @param  mixed $id
     * @return boolean
     */
    public function delete_surgery($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'surgeries');
        if ($this->db->affected_rows() > 0) {
            log_activity('Surgery Record Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
}
