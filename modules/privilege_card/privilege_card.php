<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Privilege Card
Description: Module for Privilege Card.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PRIVILEGE_CARD_MODULE_NAME', 'privilege_card');

hooks()->add_action('admin_init', 'privilege_card_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(PRIVILEGE_CARD_MODULE_NAME, 'privilege_card_module_activation_hook');

function privilege_card_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(PRIVILEGE_CARD_MODULE_NAME, [PRIVILEGE_CARD_MODULE_NAME]);

/**
 * Init privilege_card module menu items in setup in admin_init hook
 * @return null
 */
function privilege_card_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('privilege_card', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('privilege_card', [
            'name' => 'Privilege Card',
            'href' => admin_url('privilege_card'),
            'icon' => 'fa fa-id-card',
            'position' => 30,
        ]);

        $CI->app_menu->add_sidebar_children_item('privilege_card', [
            'slug' => 'privilege_card_members',
            'name' => 'Issued Cards',
            'href' => admin_url('privilege_card/members'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item('privilege_card', [
            'slug' => 'privilege_card_types',
            'name' => 'Card Plans',
            'href' => admin_url('privilege_card/types'),
            'position' => 10,
        ]);
    }
}
