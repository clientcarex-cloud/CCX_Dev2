<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Referral Dispatch
Description: Module for Referral Dispatch.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('REFERRAL_DISPATCH_MODULE_NAME', 'referral_dispatch');

hooks()->add_action('admin_init', 'referral_dispatch_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(REFERRAL_DISPATCH_MODULE_NAME, 'referral_dispatch_module_activation_hook');

function referral_dispatch_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(REFERRAL_DISPATCH_MODULE_NAME, [REFERRAL_DISPATCH_MODULE_NAME]);

/**
 * Init referral_dispatch module menu items in setup in admin_init hook
 * @return null
 */
function referral_dispatch_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('referral_dispatch', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('referral_dispatch', [
            'name' => 'Referral Dispatch', // Directly using name for now, usually _l()
            'href' => admin_url('referral_dispatch'),
            'icon' => 'fa fa-share-square',
            'position' => 30,
        ]);
    }
}
