<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Web Integration
Description: Module for Web Integration.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('WEB_INTEGRATION_MODULE_NAME', 'web_integration');

hooks()->add_action('admin_init', 'web_integration_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(WEB_INTEGRATION_MODULE_NAME, 'web_integration_module_activation_hook');

function web_integration_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(WEB_INTEGRATION_MODULE_NAME, [WEB_INTEGRATION_MODULE_NAME]);

/**
 * Init web_integration module menu items in setup in admin_init hook
 * @return null
 */
function web_integration_module_init_menu_items()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');

    if (has_permission('web_integration', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('web_integration', [
            'name' => 'Web Integration', // Directly using name for now, usually _l()
            'href' => admin_url('web_integration'),
            'icon' => 'fa fa-globe',
            'position' => 30,
        ]);
    }
}
