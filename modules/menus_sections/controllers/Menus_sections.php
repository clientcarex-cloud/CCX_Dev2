<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Menus_sections extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            access_denied('Menus Sections');
        }
    }

    public function index()
    {
        $data['title'] = 'Menu Sections & Order';

        // Get original menu items (without our modifications if possible, or we just take what's there)
        // actually app_menu->get_sidebar_menu_items() runs filters, so it might include our changes if we are not careful.
        // We need to disable our own filter for a moment to get the "raw" list + other modules.

        hooks()->remove_filter('sidebar_menu_items', 'menus_sections_inject', 1000);

        // Also remove the default menu_setup filters effectively to get the raw list, 
        // OR we might want to respect them. The user wants to "ordering of menus up and down".
        // The safest bet is to grab the items as they are currently rendered (minus our own potential future injection).

        $items = $this->app_menu->get_sidebar_menu_items();

        // Now get our saved config from the database table
        $CI = &get_instance();
        if ($CI->db->table_exists(db_prefix() . 'menus_sections')) {
            $saved_config = $CI->db->select('*')
                ->from(db_prefix() . 'menus_sections')
                ->order_by('position', 'ASC')
                ->get()
                ->result_array();

            // Format to match expected view structure if needed, or update view.
            // The view likely expects a list of objects/arrays with 'id', 'type', 'name'
            // Our table has 'slug' which maps to 'id' in the JS logic usually.
            // Let's map it back to ensure compatibility with the JS builder if strict.
            // However, looking at install.php logic, we saved 'slug' as 'id' equivalent.
            // Let's standardise the output for the view.
            foreach ($saved_config as &$row) {
                $row['id'] = $row['slug'];
                // Decode options if needed, though view might not use them yet
            }
        } else {
            $saved_config = [];
        }

        $data['items'] = $items;
        $data['saved_config'] = $saved_config;

        $this->load->view('manage', $data);
    }

    public function save()
    {
        if ($this->input->post()) {
            $data = $this->input->post('data');
            // $data structure: [ {id: 'dashboard', type: 'item'}, {id: 'section-1', name: 'Core', type: 'section'} ... ]

            if (!is_array($data)) {
                $data = json_decode($data, true);
            }

            if (is_array($data)) {
                $this->db->trans_start();
                // We truncate and re-insert to handle reordering efficiently
                $this->db->truncate(db_prefix() . 'menus_sections');

                $position = 1;
                foreach ($data as $item) {
                    $slug = isset($item['id']) ? $item['id'] : (isset($item['slug']) ? $item['slug'] : '');
                    if (empty($slug))
                        continue;

                    $type = isset($item['type']) ? $item['type'] : 'item';
                    $name = isset($item['name']) ? $item['name'] : '';

                    $this->db->insert(db_prefix() . 'menus_sections', [
                        'slug' => $slug,
                        'name' => $name,
                        'type' => $type,
                        'position' => $position,
                        'options' => json_encode($item)
                    ]);
                    $position++;
                }

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    echo json_encode(['success' => false, 'message' => 'Database error']);
                } else {
                    echo json_encode(['success' => true]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
            }
        }
    }
}
