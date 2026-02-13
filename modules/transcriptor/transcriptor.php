<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Transcriptor
Description: Module for Transcriptor management
Version: 1.0.0
Requires at least: 2.3.*
*/

define('TRANSCRIPTOR_MODULE_NAME', 'transcriptor');

hooks()->add_action('admin_init', 'transcriptor_init_menu_items');

register_activation_hook(TRANSCRIPTOR_MODULE_NAME, 'transcriptor_activation_hook');

function transcriptor_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

register_language_files(TRANSCRIPTOR_MODULE_NAME, [TRANSCRIPTOR_MODULE_NAME]);

function transcriptor_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('transcriptor', [
        'name' => 'Transcriptor',
        'href' => admin_url('transcriptor'),
        'icon' => 'fa-solid fa-x-ray', // Trying modern FA class, fallback might be needed if old FA version
        'position' => 30,
    ]);
}
