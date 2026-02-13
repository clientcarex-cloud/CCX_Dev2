<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Coupons_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get coupon by id
     * @param  mixed $id coupon id
     * @return object
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'coupons')->row();
        }

        return $this->db->get(db_prefix() . 'coupons')->result_array();
    }

    /**
     * Add new coupon
     * @param array $data coupon data
     */
    public function add($data)
    {
        $data['start_date'] = to_sql_date($data['start_date']);
        $data['end_date'] = to_sql_date($data['end_date']);

        if (isset($data['amount']) && $data['amount'] == '') {
            $data['amount'] = 0;
        }

        if (isset($data['active'])) {
            $data['active'] = 1;
        } else {
            $data['active'] = 0;
        }

        // Handle type_settings if needed (future proofing)
        if (isset($data['type_settings']) && is_array($data['type_settings'])) {
            $data['type_settings'] = json_encode($data['type_settings']);
        }

        $this->db->insert(db_prefix() . 'coupons', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Coupon Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update coupon
     * @param  array $data coupon data
     * @param  mixed $id   coupon id
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['start_date'] = to_sql_date($data['start_date']);
        $data['end_date'] = to_sql_date($data['end_date']);

        if (isset($data['amount']) && $data['amount'] == '') {
            $data['amount'] = 0;
        }

        if (isset($data['active'])) {
            $data['active'] = 1;
        } else {
            $data['active'] = 0;
        }

        if (isset($data['type_settings']) && is_array($data['type_settings'])) {
            $data['type_settings'] = json_encode($data['type_settings']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'coupons', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Coupon Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete coupon
     * @param  mixed $id coupon id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'coupons');

        if ($this->db->affected_rows() > 0) {
            log_activity('Coupon Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }
}
