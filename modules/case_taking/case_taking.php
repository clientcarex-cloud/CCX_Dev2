<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Case Taking
Description: Module to manage case taking
Version: 1.0.0
Requires at least: 2.3.*
*/

define('CASE_TAKING_MODULE_NAME', 'case_taking');

hooks()->add_action('admin_init', 'case_taking_init_menu_items');

function case_taking_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('case_taking', [
        'name' => 'Case Taking',
        'href' => admin_url('case_taking'),
        'icon' => 'fa fa-file-medical', // Using a relevant icon
        'position' => 30,
    ]);
}

/**
 * Register activation module hook
 */
register_activation_hook(CASE_TAKING_MODULE_NAME, 'case_taking_activation_hook');

function case_taking_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}
