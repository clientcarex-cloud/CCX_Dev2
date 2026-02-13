<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Master Data
Description: Master Data Management Module
Version: 1.0.0
Requires at least: 2.3.*
*/

define('MASTER_DATA_MODULE_NAME', 'master_data');

hooks()->add_action('admin_init', 'master_data_init_menu_items');

function master_data_init_menu_items()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');

    $CI->app_menu->add_sidebar_menu_item('master_data', [
        'name' => 'Master Data',
        'icon' => 'fa fa-database',
        'position' => 10,
    ]);

    // 1. Departments
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_departments',
        'name' => 'Departments',
        'href' => admin_url('departments'),
        'position' => 1,
    ]);

    // 1.5 Department Groups
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_department_groups',
        'name' => 'Department Groups',
        'href' => admin_url('master_data/department_groups'),
        'position' => 11, // Adjust position as needed
    ]);

    // 2. Support Statuses
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_support_statuses',
        'name' => 'Support Statuses',
        'href' => admin_url('tickets/statuses'),
        'position' => 2,
    ]);

    // 3. Tickets Priority
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_tickets_priority',
        'name' => 'Tickets Priority',
        'href' => admin_url('tickets/priorities'),
        'position' => 3,
    ]);

    // 4. Tickets Predefined Reply
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_predefined_replies',
        'name' => 'Predefined Replies',
        'href' => admin_url('tickets/predefined_replies'),
        'position' => 4,
    ]);

    // 5. Ticket Services
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_ticket_services',
        'name' => 'Ticket Services',
        'href' => admin_url('tickets/services'),
        'position' => 5,
    ]);

    // 6. Lead Sources
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_lead_sources',
        'name' => 'Lead Sources',
        'href' => admin_url('leads/sources'),
        'position' => 6,
    ]);

    // 7. Lead Statuses
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_lead_statuses',
        'name' => 'Lead Statuses',
        'href' => admin_url('leads/statuses'),
        'position' => 7,
    ]);

    // 8. Taxes
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_taxes',
        'name' => 'Taxes',
        'href' => admin_url('taxes'),
        'position' => 8,
    ]);

    // 9. Currencies
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_currencies',
        'name' => 'Currencies',
        'href' => admin_url('currencies'),
        'position' => 9,
    ]);

    // 10. Payment Modes
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_payment_modes',
        'name' => 'Payment Modes',
        'href' => admin_url('paymentmodes'),
        'position' => 10,
    ]);

    // 11. Expenses Categories
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_expenses_categories',
        'name' => 'Expenses Categories',
        'href' => admin_url('expenses/categories'),
        'position' => 11,
    ]);

    // 12. Contract Types
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_contract_types',
        'name' => 'Contract Types',
        'href' => admin_url('contracts/types'),
        'position' => 12,
    ]);

    // 13. Staff Roles
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_staff_roles',
        'name' => 'Staff Roles',
        'href' => admin_url('roles'),
        'position' => 13,
    ]);
    // 14. Name Titles
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_name_titles',
        'name' => 'Name Titles',
        'href' => admin_url('master_data/name_titles'),
        'position' => 14,
    ]);

    // 15. Name Care Titles
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_name_care_titles',
        'name' => 'Name Care Titles',
        'href' => admin_url('master_data/name_care_titles'),
        'position' => 15,
    ]);

    // 16. Medicine Types
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_medicine_types',
        'name' => 'Medicine Types',
        'href' => admin_url('master_data/medicine_types'),
        'position' => 16,
    ]);

    // 17. Medicine Doses
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_medicine_doses',
        'name' => 'Medicine Doses',
        'href' => admin_url('master_data/medicine_doses'),
        'position' => 17,
    ]);

    // 18. Medicine Frequencies
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_medicine_frequencies',
        'name' => 'Medicine Frequencies',
        'href' => admin_url('master_data/medicine_frequencies'),
        'position' => 18,
    ]);

    // 19. Medicine Durations
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_medicine_durations',
        'name' => 'Medicine Durations',
        'href' => admin_url('master_data/medicine_durations'),
        'position' => 19,
    ]);

    // 20. Medicine When
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_medicine_whens',
        'name' => 'Medicine When',
        'href' => admin_url('master_data/medicine_whens'),
        'position' => 20,
    ]);

    // 21. Treatments
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_treatments',
        'name' => 'Treatments',
        'href' => admin_url('master_data/treatments'),
        'position' => 21,
    ]);

    // 22. Tests Methods
    $CI->app_menu->add_sidebar_children_item('master_data', [
        'slug' => 'master_data_tests_methods',
        'name' => 'Tests Methods',
        'href' => admin_url('master_data/tests_methods'),
        'position' => 22,
    ]);

    // Dynamic Item Group Statuses
    if ($CI->db->table_exists(db_prefix() . 'items_groups')) {
        $groups = $CI->db->get(db_prefix() . 'items_groups')->result_array();
        $i = 20;
        foreach ($groups as $group) {
            $CI->app_menu->add_sidebar_children_item('master_data', [
                'slug' => 'master_data_status_' . $group['id'],
                'name' => $group['name'] . ' Statuses',
                'href' => admin_url('master_data/item_statuses/' . $group['id']),
                'position' => $i++,
            ]);
        }
    }
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(MASTER_DATA_MODULE_NAME, [MASTER_DATA_MODULE_NAME]);

/**
 * Register activation module hook
 */
register_activation_hook(MASTER_DATA_MODULE_NAME, 'master_data_module_activation_hook');

function master_data_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}
