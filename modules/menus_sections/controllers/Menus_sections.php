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

        // Now get our saved config
        $saved_config = get_option('menus_sections_active');
        $saved_config = json_decode($saved_config, true) ?? [];

        $data['items'] = $items;
        $data['saved_config'] = $saved_config;

        $this->load->view('manage', $data);
    }

    public function save()
    {
        if ($this->input->post()) {
            $data = $this->input->post('data'); // This should be the JSON string or array of the new order
            // $data structure expected: [ {id: 'dashboard', type: 'item'}, {id: 'section-1', name: 'Core', type: 'section'} ... ]

            update_option('menus_sections_active', json_encode($data));
            echo json_encode(['success' => true]);
        }
    }
}
