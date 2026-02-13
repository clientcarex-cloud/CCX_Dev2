<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Blood_mgmt_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get donor(s)
     * @param  mixed $id Optional donor ID
     * @return mixed     Array of donors or single donor object
     */
    public function get_donors($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'blood_donors')->row();
        }

        $this->db->order_by('created_at', 'desc');
        return $this->db->get(db_prefix() . 'blood_donors')->result_array();
    }

    /**
     * Add new donor
     * @param array $data Donor data
     */
    public function add_donor($data)
    {
        $this->db->insert(db_prefix() . 'blood_donors', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Blood Donor Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update donor
     * @param  array $data Donor data
     * @param  mixed $id   Donor ID
     * @return boolean
     */
    public function update_donor($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'blood_donors', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Blood Donor Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete donor
     * @param  mixed $id Donor ID
     * @return boolean
     */
    public function delete_donor($id)
    {
        // Check if donor has connected blood bags? Maybe restrict delete if used.
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'blood_donors');
        if ($this->db->affected_rows() > 0) {
            log_activity('Blood Donor Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get blood bag(s)
     * @param  mixed $id Optional bag ID
     * @return mixed
     */
    public function get_blood_bags($id = '')
    {
        $this->db->select(db_prefix() . 'blood_bags.*, ' . db_prefix() . 'blood_donors.name as donor_name');
        $this->db->join(db_prefix() . 'blood_donors', db_prefix() . 'blood_donors.id = ' . db_prefix() . 'blood_bags.donor_id', 'left');

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'blood_bags.id', $id);
            return $this->db->get(db_prefix() . 'blood_bags')->row();
        }

        $this->db->order_by(db_prefix() . 'blood_bags.created_at', 'desc');
        return $this->db->get(db_prefix() . 'blood_bags')->result_array();
    }

    public function get_available_blood_bags()
    {
        $this->db->select(db_prefix() . 'blood_bags.*, ' . db_prefix() . 'blood_donors.name as donor_name');
        $this->db->join(db_prefix() . 'blood_donors', db_prefix() . 'blood_donors.id = ' . db_prefix() . 'blood_bags.donor_id', 'left');
        $this->db->where('status', 1); // 1 = Available
        // Filter out expired?
        $this->db->where('expiry_date >=', date('Y-m-d'));

        return $this->db->get(db_prefix() . 'blood_bags')->result_array();
    }

    /**
     * Add blood bag
     * @param array $data
     */
    public function add_blood_bag($data)
    {
        if (isset($data['donation_date'])) {
            $data['donation_date'] = to_sql_date($data['donation_date']);
        }
        if (isset($data['expiry_date'])) {
            $data['expiry_date'] = to_sql_date($data['expiry_date']);
        }
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . 'blood_bags', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Blood Bag Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update blood bag
     * @param  array $data
     * @param  mixed $id
     * @return boolean
     */
    public function update_blood_bag($data, $id)
    {
        if (isset($data['donation_date'])) {
            $data['donation_date'] = to_sql_date($data['donation_date']);
        }
        if (isset($data['expiry_date'])) {
            $data['expiry_date'] = to_sql_date($data['expiry_date']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'blood_bags', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Blood Bag Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete blood bag
     * @param  mixed $id
     * @return boolean
     */
    public function delete_blood_bag($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'blood_bags');
        if ($this->db->affected_rows() > 0) {
            log_activity('Blood Bag Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get Issue(s)
     * @param  mixed $id
     * @return mixed
     */
    public function get_issues($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $issue = $this->db->get(db_prefix() . 'blood_issues')->row();
            if ($issue) {
                // Get items
                $this->db->select(db_prefix() . 'blood_bags.*');
                $this->db->join(db_prefix() . 'blood_bags', db_prefix() . 'blood_bags.id = ' . db_prefix() . 'blood_issue_items.bag_id', 'left');
                $this->db->where('issue_id', $id);
                $issue->items = $this->db->get(db_prefix() . 'blood_issue_items')->result_array();
            }
            return $issue;
        }
        $this->db->order_by('issue_date', 'desc');
        return $this->db->get(db_prefix() . 'blood_issues')->result_array();
    }

    /**
     * Add Issue
     * @param array $data
     */
    public function add_issue($data)
    {
        $items = [];
        if (isset($data['bag_ids'])) {
            $items = $data['bag_ids'];
            unset($data['bag_ids']);
        }

        // if (isset($data['issue_date'])) {
        //     $data['issue_date'] = to_sql_date($data['issue_date'], true);
        // }

        // Ensure issue_date is current if not set, or format it
        if (empty($data['issue_date'])) {
            $data['issue_date'] = date('Y-m-d H:i:s');
        } else {
            $data['issue_date'] = to_sql_date($data['issue_date'], true);
        }

        $this->db->insert(db_prefix() . 'blood_issues', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            foreach ($items as $bag_id) {
                $this->db->insert(db_prefix() . 'blood_issue_items', [
                    'issue_id' => $insert_id,
                    'bag_id' => $bag_id
                ]);
                // Update bag status to Issued (2)
                $this->db->where('id', $bag_id);
                $this->db->update(db_prefix() . 'blood_bags', ['status' => 2]);
            }
            log_activity('Blood Issue Created [ID: ' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }

    public function delete_issue($id)
    {
        // Get items to restore bag status?
        $this->db->where('issue_id', $id);
        $items = $this->db->get(db_prefix() . 'blood_issue_items')->result_array();

        foreach ($items as $item) {
            // Restore bag status to Available (1)
            $this->db->where('id', $item['bag_id']);
            $this->db->update(db_prefix() . 'blood_bags', ['status' => 1]);
        }

        $this->db->where('issue_id', $id);
        $this->db->delete(db_prefix() . 'blood_issue_items');

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'blood_issues');

        if ($this->db->affected_rows() > 0) {
            log_activity('Blood Issue Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
}
