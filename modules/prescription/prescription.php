<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Prescription
Description: Module to manage prescriptions
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PRESCRIPTION_MODULE_NAME', 'prescription');

hooks()->add_action('admin_init', 'prescription_init_menu_items');

function prescription_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('prescription', [
        'name' => 'Prescriptions',
        'href' => admin_url('prescription'),
        'icon' => 'fa fa-file-medical', // Using a relevant icon
        'position' => 30,
    ]);
}

/**
 * Register activation module hook
 */
register_activation_hook(PRESCRIPTION_MODULE_NAME, 'prescription_activation_hook');

function prescription_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}
