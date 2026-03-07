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
     * Get the 3-column field layout.
     * Returns ['1' => [...fields], '2' => [...], '3' => [...]]
     */
    public function get_field_layout()
    {
        $saved = get_option('ccx_leads_field_layout');
        if ($saved) {
            $layout = json_decode($saved, true);
            if (is_array($layout) && !empty($layout)) {
                return $layout;
            }
        }

        // Build default layout from current fields
        return $this->build_default_layout();
    }

    /**
     * Build default 3-column layout based on slug conventions
     */
    private function build_default_layout()
    {
        $col1_slugs = ['status', 'source', 'assigned'];
        $col2_slugs = ['name', 'title', 'email', 'website', 'phonenumber', 'lead_value', 'company'];
        $col3_slugs = ['address', 'city', 'state', 'country', 'zip', 'description', 'is_public', 'tags'];

        $layout = ['1' => [], '2' => [], '3' => []];

        // Standard fields
        $this->db->order_by('field_order', 'asc');
        $standard = $this->db->get(db_prefix() . 'ccx_lead_field_settings')->result_array();
        foreach ($standard as $f) {
            $item = [
                'type' => 'standard',
                'id' => $f['id'],
                'slug' => $f['slug'],
                'label' => $f['label'],
                'active' => $f['active'],
            ];
            if (in_array($f['slug'], $col1_slugs)) {
                $layout['1'][] = $item;
            } elseif (in_array($f['slug'], $col2_slugs)) {
                $layout['2'][] = $item;
            } else {
                $layout['3'][] = $item;
            }
        }

        // Custom fields go to column 3 by default
        $this->db->where('fieldto', 'leads');
        $this->db->order_by('field_order', 'asc');
        $custom = $this->db->get(db_prefix() . 'customfields')->result_array();
        foreach ($custom as $f) {
            $layout['3'][] = [
                'type' => 'custom',
                'id' => $f['id'],
                'slug' => $f['slug'],
                'label' => $f['name'],
                'active' => $f['active'],
            ];
        }

        return $layout;
    }

    /**
     * Save the 3-column field layout + field_order values
     * @param array $columns ['1' => [{type, id}, ...], '2' => [...], '3' => [...]]
     */
    public function save_field_layout($columns)
    {
        // Build layout JSON for storage and update field_order in DB
        $layout = ['1' => [], '2' => [], '3' => []];
        $global_order = 1;

        foreach (['1', '2', '3'] as $col) {
            if (!isset($columns[$col]) || !is_array($columns[$col]))
                continue;
            foreach ($columns[$col] as $item) {
                $type = $item['type'];
                $id = intval($item['id']);

                // Get field info for storage
                if ($type === 'standard') {
                    $row = $this->db->where('id', $id)->get(db_prefix() . 'ccx_lead_field_settings')->row_array();
                    if ($row) {
                        $layout[$col][] = [
                            'type' => 'standard',
                            'id' => $id,
                            'slug' => $row['slug'],
                            'label' => $row['label'],
                            'active' => $row['active'],
                        ];
                        $this->db->where('id', $id);
                        $this->db->update(db_prefix() . 'ccx_lead_field_settings', ['field_order' => $global_order]);
                    }
                } elseif ($type === 'custom') {
                    $row = $this->db->where('id', $id)->where('fieldto', 'leads')->get(db_prefix() . 'customfields')->row_array();
                    if ($row) {
                        $layout[$col][] = [
                            'type' => 'custom',
                            'id' => $id,
                            'slug' => $row['slug'],
                            'label' => $row['name'],
                            'active' => $row['active'],
                        ];
                        $this->db->where('id', $id);
                        $this->db->where('fieldto', 'leads');
                        $this->db->update(db_prefix() . 'customfields', ['field_order' => $global_order]);
                    }
                }
                $global_order++;
            }
        }

        // Save layout JSON as option
        if (get_option('ccx_leads_field_layout') === false) {
            add_option('ccx_leads_field_layout', json_encode($layout));
        } else {
            update_option('ccx_leads_field_layout', json_encode($layout));
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
