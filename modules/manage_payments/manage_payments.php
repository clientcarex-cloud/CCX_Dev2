<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Manage Payments
Description: Module to manage payments
Version: 1.0.0
Requires at least: 2.3.*
*/

define('MANAGE_PAYMENTS_MODULE_NAME', 'manage_payments');

hooks()->add_action('admin_init', 'manage_payments_init_menu_items');

function manage_payments_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('manage_payments', [
        'name' => 'Manage Payments',
        'href' => admin_url('manage_payments'),
        'icon' => 'fa fa-credit-card',
        'position' => 30,
    ]);
}

hooks()->add_action('app_admin_footer', 'manage_payments_head_components');

function manage_payments_head_components()
{
    // Check if we are on the module page
    // $CI = &get_instance();
}

/**
 * Register activation module hook
 */
register_activation_hook(MANAGE_PAYMENTS_MODULE_NAME, 'manage_payments_activation_hook');

function manage_payments_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}
