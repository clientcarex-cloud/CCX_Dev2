<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ccx_leads_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
    }

    public function do_kanban_query($status, $search = '', $page = 1, $sort = [], $count = false)
    {
        // Wrapper for core kanban query but allows for future optimization
        // For now, we reuse core logic or replicate if we need custom fields in list

        $limit = 10; // Load 10 at a time for infinite scroll
        $start = ($page - 1) * $limit;

        $this->db->select('*');
        $this->db->from(db_prefix() . 'leads');
        $this->db->where('status', $status);

        if ($search != '') {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('company', $search);
            $this->db->or_like('phonenumber', $search);
            $this->db->group_end();
        }

        if (!$count) {
            $this->db->limit($limit, $start);
        }

        $this->db->order_by('leadorder', 'asc');
        $this->db->order_by('dateadded', 'desc');

        if ($count) {
            return $this->db->count_all_results();
        }

        return $this->db->get()->result_array();
    }

    public function get_status_summary()
    {
        $this->db->select('status, junk, lost, count(*) as total');
        $this->db->from(db_prefix() . 'leads');

        if (!is_admin()) {
            $this->db->group_start();
            $this->db->where('assigned', get_staff_user_id());
            $this->db->or_where('addedfrom', get_staff_user_id());
            $this->db->or_where('is_public', 1);
            $this->db->group_end();
        }

        $this->db->group_by('status, junk, lost');
        $results = $this->db->get()->result_array();

        // Process results to separate Junk and Lost
        $summary = [];
        $junk_count = 0;
        $lost_count = 0;

        foreach ($results as $row) {
            if ($row['junk'] == 1) {
                $junk_count += $row['total'];
            } elseif ($row['lost'] == 1) {
                $lost_count += $row['total'];
            } else {
                // Regular status
                $found = false;
                foreach ($summary as &$s) {
                    if ($s['id'] == $row['status']) {
                        $s['total'] += $row['total'];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $summary[] = [
                        'id' => $row['status'],
                        'total' => $row['total']
                    ];
                }
            }
        }

        // Add Junk and Lost to summary with special IDs
        $summary[] = ['id' => 'junk', 'total' => $junk_count, 'name' => _l('leads_junk')];
        $summary[] = ['id' => 'lost', 'total' => $lost_count, 'name' => _l('leads_lost')];

        return $summary;
    }
}
