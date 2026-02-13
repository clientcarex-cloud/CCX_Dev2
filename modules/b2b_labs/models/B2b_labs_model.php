<?php

defined('BASEPATH') or exit('No direct script access allowed');

class B2b_labs_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get pricing for a specific staff member (Referral Lab)
     * @param  mixed $staff_id
     * @return array
     */
    public function get_pricing($staff_id)
    {
        $this->db->where('staff_id', $staff_id);
        $pricing = $this->db->get(db_prefix() . 'referral_lab_pricing')->result_array();

        $result = [];
        foreach ($pricing as $p) {
            $result[$p['test_id']] = $p['referral_price'];
        }
        return $result;
    }

    /**
     * Update pricing for a specific staff member
     * @param  mixed $staff_id
     * @param  array $data    [test_id => price, ...]
     * @return boolean
     */
    public function update_pricing($staff_id, $data)
    {
        // First delete existing pricing? Or update/insert?
        // Deleting and re-inserting is easiest but not efficient if many.
        // Given typically < 1000 tests, update/insert loop is fine.
        // Actually, let's delete all for this staff and insert new ones where price > 0 or diff from standard?
        // Requirement: "set each their own referral_price"

        // Let's use a transaction
        $this->db->trans_start();

        // We can just loop and use checking.
        // Or delete all for this user and simple insert. 
        // Safer to delete all and insert valid ones.
        $this->db->where('staff_id', $staff_id);
        $this->db->delete(db_prefix() . 'referral_lab_pricing');

        $insert_data = [];
        foreach ($data['referral_price'] as $test_id => $price) {
            if ($price !== '' && $price !== null) {
                $insert_data[] = [
                    'staff_id' => $staff_id,
                    'test_id' => $test_id,
                    'referral_price' => $price
                ];
            }
        }

        if (!empty($insert_data)) {
            $this->db->insert_batch(db_prefix() . 'referral_lab_pricing', $insert_data);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Get specific test price for a referral lab
     * @param  mixed $staff_id
     * @param  mixed $test_id
     * @return float|null
     */
    public function get_test_price($staff_id, $test_id)
    {
        $this->db->select('referral_price');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('test_id', $test_id);
        $row = $this->db->get(db_prefix() . 'referral_lab_pricing')->row();

        return $row ? $row->referral_price : null;
    }
}
