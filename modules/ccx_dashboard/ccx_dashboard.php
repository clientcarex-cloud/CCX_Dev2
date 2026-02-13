<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: CCX Dashboard
Description: Unlimited dashboard builder for CCX CRM
Version: 1.0.1
Requires at least: 2.3.*
*/

define('CCX_DASHBOARD_MODULE_NAME', 'ccx_dashboard');
define('CCX_DASHBOARD_ASSETS_PATH', 'modules/ccx_dashboard/assets');
// Backwards compatibility for any legacy references that may still use the old constants.
if (!defined('PERFEX_DASHBOARD_MODULE_NAME')) {
    define('PERFEX_DASHBOARD_MODULE_NAME', CCX_DASHBOARD_MODULE_NAME);
}
if (!defined('PERFEX_DASHBOARD_ASSETS_PATH')) {
    define('PERFEX_DASHBOARD_ASSETS_PATH', CCX_DASHBOARD_ASSETS_PATH);
}

$CI = &get_instance();

hooks()->add_action('admin_init', 'ccx_dashboard_module_menu_admin_items');
hooks()->add_action('admin_init', 'ccx_dashboard_permissions');

function ccx_dashboard_module_menu_admin_items()
{
  $CI = &get_instance();

  if (has_permission('ccx_dashboard', '', 'my_dashboard_view') || has_permission('ccx_dashboard', '', 'all_dashboard_view') || has_permission('ccx_dashboard', '', 'widget_view') || has_permission('ccx_dashboard', '', 'dashboard_settings')) {
    $CI->app_menu->add_sidebar_menu_item('ccx-dashboard-menu', [
        'name'     => _l('ccx_dashboard'),
        'href'     => 'javascript:void(0);',
        'position' => 2,
        'icon'     => 'fa fa-home menu-icon',
    ]);
  }

  if (has_permission('ccx_dashboard', '', 'my_dashboard_view')) {
    $CI->app_menu->add_sidebar_children_item('ccx-dashboard-menu', [
      'name'     => _l('my_dashboard'),
      'href'     => admin_url('ccx_dashboard/dashboards/my_dashboard'),
      'position' => 1,
      'slug'     => 'ccx_dashboards_my_dashboard',
    ]);
  }
  if (has_permission('ccx_dashboard', '', 'all_dashboard_view')) {
    $CI->app_menu->add_sidebar_children_item('ccx-dashboard-menu', [
      'name'     => _l('all_dashboards'),
      'href'     => admin_url('ccx_dashboard/dashboards'),
      'position' => 2,
      'slug'     => 'ccx_dashboards_list',
    ]);
  }
  if (has_permission('ccx_dashboard', '', 'widget_view')) {
    $CI->app_menu->add_sidebar_children_item('ccx-dashboard-menu', [
      'name'     => _l('all_widgets'),
      'href'     => admin_url('ccx_dashboard/widgets'),
      'position' => 3,
      'slug'     => 'ccx_dashboard_widgets',
    ]);
  }
  if (has_permission('ccx_dashboard', '', 'widget_category_view')) {
    $CI->app_menu->add_sidebar_children_item('ccx-dashboard-menu', [
      'name'     => _l('widget_categories'),
      'href'     => admin_url('ccx_dashboard/categories'),
      'position' => 4,
      'slug'     => 'ccx_dashboard_categories',
    ]);
  }
}

function ccx_dashboard_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'my_dashboard_view'   => _l('my_dashboard_view'),
            'all_dashboard_view'   => _l('all_dashboard_view'),
            'dashboard_create' => _l('dashboard_create'),
            'dashboard_edit'   => _l('dashboard_edit'),
            'dashboard_delete' => _l('dashboard_delete'),
            'dashboard_clone' => _l('dashboard_clone'),
            'widget_view'   => _l('widget_view'),
            'widget_create' => _l('widget_create'),
            'widget_edit'   => _l('widget_edit'),
            'widget_delete' => _l('widget_delete'),
            'widget_category_view'   => _l('widget_category_view'),
            'widget_category_create' => _l('widget_category_create'),
            'widget_category_edit'   => _l('widget_category_edit'),
            'widget_category_delete' => _l('widget_category_delete'),
            'dashboard_settings' => _l('dashboard_settings'),
    ];

    register_staff_capabilities('ccx_dashboard', $capabilities, _l('ccx_dashboard'));
}

$CI->load->helper(CCX_DASHBOARD_MODULE_NAME . '/ccx_dashboard');

/**
 * Register activation module hook
 */
register_activation_hook(CCX_DASHBOARD_MODULE_NAME, 'ccx_dashboard_module_activation_hook');

function ccx_dashboard_module_activation_hook()
{
  $CI = &get_instance();
  require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(CCX_DASHBOARD_MODULE_NAME, [CCX_DASHBOARD_MODULE_NAME]);
