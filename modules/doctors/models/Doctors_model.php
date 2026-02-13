<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Doctors_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get pricing for a specific doctor
     * @param  mixed $staff_id
     * @return array
     */
    public function get_pricing($staff_id)
    {
        // Ensure table exists (redundant check but safe)
        if (!$this->db->table_exists(db_prefix() . 'doctor_pricing')) {
            return [];
        }

        $this->db->where('staff_id', $staff_id);
        $pricing = $this->db->get(db_prefix() . 'doctor_pricing')->result_array();

        $result = [];
        foreach ($pricing as $p) {
            $result[$p['test_id']] = $p['referral_amount'];
        }
        return $result;
    }

    /**
     * Update pricing for a specific doctor
     * @param  mixed $staff_id
     * @param  array $data    [test_id => price, ...]
     * @return boolean
     */
    public function update_pricing($staff_id, $data)
    {
        $this->db->trans_start();

        // Delete existing pricing for this doctor
        $this->db->where('staff_id', $staff_id);
        $this->db->delete(db_prefix() . 'doctor_pricing');

        $insert_data = [];
        if (isset($data['referral_amount']) && is_array($data['referral_amount'])) {
            foreach ($data['referral_amount'] as $test_id => $price) {
                if ($price !== '' && $price !== null) {
                    $insert_data[] = [
                        'staff_id' => $staff_id,
                        'test_id' => $test_id,
                        'referral_amount' => $price
                    ];
                }
            }
        }

        if (!empty($insert_data)) {
            $this->db->insert_batch(db_prefix() . 'doctor_pricing', $insert_data);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Get specific test price for a doctor
     * @param  mixed $staff_id
     * @param  mixed $test_id
     * @return float|null
     */
    public function get_test_price($staff_id, $test_id)
    {
        if (!$this->db->table_exists(db_prefix() . 'doctor_pricing')) {
            return null;
        }

        $this->db->select('referral_amount');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('test_id', $test_id);
        $row = $this->db->get(db_prefix() . 'doctor_pricing')->row();

        return $row ? $row->referral_amount : null;
    }
}
