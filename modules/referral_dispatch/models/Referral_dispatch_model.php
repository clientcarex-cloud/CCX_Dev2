<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Referral_dispatch_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_referral_doctor_stats()
    {
        $this->db->select('CONCAT(firstname, " ", lastname) as name, staffid');
        $this->db->select('COUNT(' . db_prefix() . 'visits.id) as total_visits');

        $this->db->from(db_prefix() . 'visits');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'visits.referral_doctor_id', 'inner');

        $this->db->where(db_prefix() . 'visits.referral_doctor_id IS NOT NULL');
        $this->db->where(db_prefix() . 'visits.referral_doctor_id !=', 0);

        $this->db->group_by(db_prefix() . 'visits.referral_doctor_id');

        $results = $this->db->get()->result_array();

        foreach ($results as &$row) {
            $row['total_collected'] = $this->get_collected_amount_for_doctor($row['staffid']);
            $row['total_billed'] = $this->get_billed_amount_for_doctor($row['staffid']);
        }

        return $results;
    }

    public function get_referral_lab_stats()
    {
        // Labs are also staff? Or separate table?
        // In Patients model, get_referral_labs uses get_staff_by_role(['Referral Lab'])
        // So they are in tblstaff.

        $this->db->select('CONCAT(firstname, " ", lastname) as name, staffid');
        $this->db->select('COUNT(' . db_prefix() . 'visits.id) as total_visits');

        $this->db->from(db_prefix() . 'visits');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'visits.referral_lab_id', 'inner');

        $this->db->where(db_prefix() . 'visits.referral_lab_id IS NOT NULL');
        $this->db->where(db_prefix() . 'visits.referral_lab_id !=', 0);

        $this->db->group_by(db_prefix() . 'visits.referral_lab_id');

        $results = $this->db->get()->result_array();

        foreach ($results as &$row) {
            $row['total_collected'] = $this->get_collected_amount_for_lab($row['staffid']);
            $row['total_billed'] = $this->get_billed_amount_for_lab($row['staffid']);
        }

        return $results;
    }

    private function get_collected_amount_for_doctor($doctor_id)
    {
        $this->db->select('SUM(amount) as total');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->where('invoiceid IN (SELECT invoice_id FROM ' . db_prefix() . 'visits WHERE referral_doctor_id = ' . $this->db->escape_str($doctor_id) . ')', NULL, FALSE);
        $res = $this->db->get()->row();
        return $res ? $res->total : 0;
    }

    private function get_billed_amount_for_doctor($doctor_id)
    {
        $this->db->select('SUM(total) as total');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where('id IN (SELECT invoice_id FROM ' . db_prefix() . 'visits WHERE referral_doctor_id = ' . $this->db->escape_str($doctor_id) . ')', NULL, FALSE);
        $res = $this->db->get()->row();
        return $res ? $res->total : 0;
    }

    private function get_collected_amount_for_lab($lab_id)
    {
        $this->db->select('SUM(amount) as total');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->where('invoiceid IN (SELECT invoice_id FROM ' . db_prefix() . 'visits WHERE referral_lab_id = ' . $this->db->escape_str($lab_id) . ')', NULL, FALSE);
        $res = $this->db->get()->row();
        return $res ? $res->total : 0;
    }

    private function get_billed_amount_for_lab($lab_id)
    {
        $this->db->select('SUM(total) as total');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where('id IN (SELECT invoice_id FROM ' . db_prefix() . 'visits WHERE referral_lab_id = ' . $this->db->escape_str($lab_id) . ')', NULL, FALSE);
        $res = $this->db->get()->row();
        return $res ? $res->total : 0;
    }
}
