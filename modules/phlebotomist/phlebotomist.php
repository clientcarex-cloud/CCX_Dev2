<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Phlebotomist
Description: Module for Phlebotomist management
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PHLEBOTOMIST_MODULE_NAME', 'phlebotomist');

hooks()->add_action('admin_init', 'phlebotomist_init_menu_items');

register_activation_hook(PHLEBOTOMIST_MODULE_NAME, 'phlebotomist_activation_hook');

function phlebotomist_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

register_language_files(PHLEBOTOMIST_MODULE_NAME, [PHLEBOTOMIST_MODULE_NAME]);

function phlebotomist_init_menu_items()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');

    $CI->app_menu->add_sidebar_menu_item('phlebotomist', [
        'name' => 'Phlebotomist',
        'href' => admin_url('phlebotomist'),
        'icon' => 'fa fa-user-md',
        'position' => 30,
    ]);
}
