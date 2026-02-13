<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Patients Review
Description: Module for Patients Review with Hello page and Settings.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PATIENTS_REVIEW_MODULE_NAME', 'patients_review');

hooks()->add_action('admin_init', 'patients_review_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(PATIENTS_REVIEW_MODULE_NAME, 'patients_review_module_activation_hook');

function patients_review_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(PATIENTS_REVIEW_MODULE_NAME, [PATIENTS_REVIEW_MODULE_NAME]);

/**
 * Init patients_review module menu items in setup in admin_init hook
 * @return null
 */
function patients_review_module_init_menu_items()
{
    $CI = &get_instance();

    // Giving access to all for now or check 'patients_review' permission if you plan to add it
    // if (has_permission('patients_review', '', 'view')) {
    $CI->app_menu->add_sidebar_menu_item('patients_review', [
        'name' => 'Patients Review',
        'href' => admin_url('patients_review'),
        'icon' => 'fa fa-user-md', // Choosing a relevant icon
        'position' => 30,
    ]);
    // }
}
