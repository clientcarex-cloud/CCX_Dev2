<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ccx_leads_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $data
     * @return int
     */
    public function add_lead($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'ccx_leads', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New CCX Lead Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }

        return $insert_id;
    }

    /**
     * @param array $data
     * @param int $id
     * @return boolean
     */
    public function update_lead($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ccx_leads', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('CCX Lead Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param int $id
     * @return object
     */
    public function get_lead($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'ccx_leads')->row();
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function delete_lead($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ccx_leads');

        if ($this->db->affected_rows() > 0) {
            // Delete associated call logs
            $this->db->where('lead_id', $id);
            $this->db->delete(db_prefix() . 'ccx_leads_call_logs');

            log_activity('CCX Lead Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param array $data
     * @return int
     */
    public function add_call_log($data)
    {
        $data['date'] = date('Y-m-d H:i:s');
        $data['staff_id'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'ccx_leads_call_logs', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    /**
     * @param int $lead_id
     * @return array
     */
    public function get_call_logs($lead_id)
    {
        $this->db->select(db_prefix() . 'ccx_leads_call_logs.*, ' . db_prefix() . 'staff.firstname, ' . db_prefix() . 'staff.lastname');
        $this->db->from(db_prefix() . 'ccx_leads_call_logs');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'ccx_leads_call_logs.staff_id', 'left');
        $this->db->where('lead_id', $lead_id);
        $this->db->order_by('date', 'desc');
        return $this->db->get()->result_array();
    }
}
