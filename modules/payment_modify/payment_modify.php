<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Payment Modify
Description: Module for Payment Modify with Hello page.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PAYMENT_MODIFY_MODULE_NAME', 'payment_modify');

hooks()->add_action('admin_init', 'payment_modify_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(PAYMENT_MODIFY_MODULE_NAME, 'payment_modify_module_activation_hook');

function payment_modify_module_activation_hook()
{
    $CI = &get_instance();
    // require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(PAYMENT_MODIFY_MODULE_NAME, [PAYMENT_MODIFY_MODULE_NAME]);

/**
 * Init payment_modify module menu items in setup in admin_init hook
 * @return null
 */
function payment_modify_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('payment_modify', [
        'name' => 'Payment Modify',
        'href' => admin_url('payment_modify'),
        'icon' => 'fa fa-pencil',
        'position' => 30,
    ]);
}
