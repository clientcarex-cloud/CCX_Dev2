<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Discounts/Coupons
Description: Manage offers and coupons to satisfy customers (Fixed, %, Volume, Time-based, etc.)
Version: 1.0.0
Requires at least: 2.3.*
*/

define('COUPONS_MODULE_NAME', 'coupons');

hooks()->add_action('admin_init', 'coupons_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(COUPONS_MODULE_NAME, 'coupons_activation_hook');

function coupons_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(COUPONS_MODULE_NAME, [COUPONS_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function coupons_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('coupons', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('coupons', [
            'name' => 'Coupons',
            'href' => admin_url('coupons'),
            'icon' => 'fa fa-percent',
            'position' => 60,
        ]);
    }
}
