<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ccx_leads_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
        $this->_ensure_field_settings_table();
    }

    /**
     * Self-healing: ensure the field settings table exists and is seeded
     */
    private function _ensure_field_settings_table()
    {
        if (!$this->db->table_exists(db_prefix() . 'ccx_lead_field_settings')) {
            $CI = &get_instance();
            require_once(module_dir_path('ccx_leads') . 'install.php');
        } elseif ($this->db->count_all(db_prefix() . 'ccx_lead_field_settings') == 0) {
            $this->seed_default_fields();
        }
    }

    /**
     * Get all field settings ordered by field_order
     */
    public function get_field_settings()
    {
        $this->db->order_by('field_order', 'asc');
        return $this->db->get(db_prefix() . 'ccx_lead_field_settings')->result_array();
    }

    /**
     * Get field settings as slug => settings map for quick lookup
     */
    public function get_field_settings_map()
    {
        $fields = $this->get_field_settings();
        $map = [];
        foreach ($fields as $f) {
            $map[$f['slug']] = $f;
        }
        return $map;
    }

    /**
     * Update a single field setting
     */
    public function update_field_setting($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'ccx_lead_field_settings', $data);
    }

    /**
     * Seed default fields into the table
     */
    public function seed_default_fields()
    {
        $defaults = [
            ['slug' => 'name', 'label' => 'Name', 'active' => 1, 'required' => 1, 'field_order' => 1],
            ['slug' => 'title', 'label' => 'Title', 'active' => 1, 'required' => 0, 'field_order' => 2],
            ['slug' => 'email', 'label' => 'Email', 'active' => 1, 'required' => 0, 'field_order' => 3],
            ['slug' => 'phonenumber', 'label' => 'Phone', 'active' => 1, 'required' => 0, 'field_order' => 4],
            ['slug' => 'website', 'label' => 'Website', 'active' => 1, 'required' => 0, 'field_order' => 5],
            ['slug' => 'lead_value', 'label' => 'Lead Value', 'active' => 1, 'required' => 0, 'field_order' => 6],
            ['slug' => 'company', 'label' => 'Company', 'active' => 1, 'required' => 0, 'field_order' => 7],
            ['slug' => 'address', 'label' => 'Address', 'active' => 1, 'required' => 0, 'field_order' => 8],
            ['slug' => 'city', 'label' => 'City', 'active' => 1, 'required' => 0, 'field_order' => 9],
            ['slug' => 'state', 'label' => 'State', 'active' => 1, 'required' => 0, 'field_order' => 10],
            ['slug' => 'country', 'label' => 'Country', 'active' => 1, 'required' => 0, 'field_order' => 11],
            ['slug' => 'zip', 'label' => 'Zip Code', 'active' => 1, 'required' => 0, 'field_order' => 12],
            ['slug' => 'description', 'label' => 'Description', 'active' => 1, 'required' => 0, 'field_order' => 13],
            ['slug' => 'status', 'label' => 'Status', 'active' => 1, 'required' => 1, 'field_order' => 14],
            ['slug' => 'source', 'label' => 'Source', 'active' => 1, 'required' => 0, 'field_order' => 15],
            ['slug' => 'assigned', 'label' => 'Assigned', 'active' => 1, 'required' => 0, 'field_order' => 16],
            ['slug' => 'tags', 'label' => 'Tags', 'active' => 1, 'required' => 0, 'field_order' => 17],
            ['slug' => 'is_public', 'label' => 'Public', 'active' => 1, 'required' => 0, 'field_order' => 18],
        ];

        foreach ($defaults as $field) {
            // Only insert if slug not already present
            if ($this->db->where('slug', $field['slug'])->count_all_results(db_prefix() . 'ccx_lead_field_settings') == 0) {
                $this->db->insert(db_prefix() . 'ccx_lead_field_settings', $field);
            }
        }
    }

    /**
     * Get all fields (standard + custom) merged and sorted by field_order
     */
    public function get_all_fields_ordered()
    {
        $fields = [];

        // Standard fields from ccx_lead_field_settings
        $this->db->order_by('field_order', 'asc');
        $standard = $this->db->get(db_prefix() . 'ccx_lead_field_settings')->result_array();
        foreach ($standard as $f) {
            $fields[] = [
                'type' => 'standard',
                'id' => $f['id'],
                'slug' => $f['slug'],
                'label' => $f['label'],
                'active' => $f['active'],
                'field_order' => $f['field_order'],
            ];
        }

        // Custom fields for leads
        $this->db->where('fieldto', 'leads');
        $this->db->order_by('field_order', 'asc');
        $custom = $this->db->get(db_prefix() . 'customfields')->result_array();
        foreach ($custom as $f) {
            $fields[] = [
                'type' => 'custom',
                'id' => $f['id'],
                'slug' => $f['slug'],
                'label' => $f['name'],
                'active' => $f['active'],
                'field_order' => $f['field_order'],
            ];
        }

        // Sort by field_order
        usort($fields, function ($a, $b) {
            return ($a['field_order'] ?? 999) - ($b['field_order'] ?? 999);
        });

        return $fields;
    }

    /**
     * Save field order for both standard and custom fields
     * @param array $items Array of ['type' => 'standard'|'custom', 'id' => int]
     */
    public function save_field_order($items)
    {
        foreach ($items as $index => $item) {
            $order = $index + 1;
            if ($item['type'] === 'standard') {
                $this->db->where('id', $item['id']);
                $this->db->update(db_prefix() . 'ccx_lead_field_settings', ['field_order' => $order]);
            } elseif ($item['type'] === 'custom') {
                $this->db->where('id', $item['id']);
                $this->db->where('fieldto', 'leads');
                $this->db->update(db_prefix() . 'customfields', ['field_order' => $order]);
            }
        }
        return true;
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

        if (!is_admin()) {
            $this->db->group_start();
            $this->db->where('assigned', get_staff_user_id());
            $this->db->or_where('addedfrom', get_staff_user_id());
            $this->db->or_where('is_public', 1);
            $this->db->group_end();
        }

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
