<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Self-Kiosk
Description: Self-Kiosk Module
Version: 1.0.0
Requires at least: 2.3.*
*/

define('SELF_KIOSK_MODULE_NAME', 'self_kiosk');
register_language_files(SELF_KIOSK_MODULE_NAME, [SELF_KIOSK_MODULE_NAME]);

hooks()->add_action('admin_init', 'self_kiosk_module_init_menu_items');

register_activation_hook(SELF_KIOSK_MODULE_NAME, 'self_kiosk_module_activation_hook');

function self_kiosk_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function self_kiosk_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('self-kiosk', [
        'name' => _l('self_qr'),
        'href' => admin_url('self_kiosk'),
        'position' => 60,
        'icon' => 'fa fa-qrcode', // Using a relevant icon
    ]);
}
