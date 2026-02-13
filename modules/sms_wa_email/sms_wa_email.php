<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: SMS/WA/Email
Description: Module for SMS/WA/Email.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('SMS_WA_EMAIL_MODULE_NAME', 'sms_wa_email');

hooks()->add_action('admin_init', 'sms_wa_email_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(SMS_WA_EMAIL_MODULE_NAME, 'sms_wa_email_module_activation_hook');

function sms_wa_email_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(SMS_WA_EMAIL_MODULE_NAME, [SMS_WA_EMAIL_MODULE_NAME]);

/**
 * Init sms_wa_email module menu items in setup in admin_init hook
 * @return null
 */
function sms_wa_email_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('sms_wa_email', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('sms_wa_email', [
            'name' => 'SMS/WA/Email', // Directly using name for now, usually _l()
            'href' => admin_url('sms_wa_email'),
            'icon' => 'fa fa-envelope',
            'position' => 30,
        ]);
    }
}
