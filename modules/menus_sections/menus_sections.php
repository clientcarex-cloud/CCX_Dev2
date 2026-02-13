<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Menu Sections
Description: Add separators and reorder sidebar menu items
Version: 1.0.0
Requires at least: 2.3.*
*/

define('MENUS_SECTIONS_MODULE_NAME', 'menus_sections');

$CI = &get_instance();

hooks()->add_action('admin_init', 'menus_sections_init_menu_items');
hooks()->add_action('app_admin_head', 'menus_sections_add_head_components');
hooks()->add_filter('sidebar_menu_items', 'menus_sections_inject', 1000);

/**
 * Load the module helper
 */
$CI->load->helper(MENUS_SECTIONS_MODULE_NAME . '/menus_sections');

/**
 * Register activation module hook
 */
register_activation_hook(MENUS_SECTIONS_MODULE_NAME, 'menus_sections_activation_hook');

function menus_sections_activation_hook()
{
    require_once(__DIR__ . '/install.php');
}

/**
 * Init menu setup module menu items in setup in admin_init hook
 * @return null
 */
function menus_sections_init_menu_items()
{
    if (is_admin()) {
        $CI = &get_instance();

        $CI->app_menu->add_setup_menu_item('menus_sections_setup', [
            'slug' => 'menus_sections_setup',
            'name' => 'Menu Sections',
            'href' => admin_url('menus_sections'),
            'position' => 65,
        ]);
    }
}

function menus_sections_add_head_components()
{
    echo '<style>
        .menu-section-label {
            padding: 0 !important; /* Remove container padding */
            border-top: 1px solid rgba(255,255,255,0.05);
            margin-top: 5px;
            pointer-events: auto !important;
            cursor: pointer;
            width: 100%;
        }
        /* Target the div instead of anchor */
        .menu-section-label > .menu-section-heading {
            padding: 10px 15px !important; /* Compact padding */
            min-height: 20px !important;
            display: flex !important;
            justify-content: space-between !important;
            width: 100%;
            align-items: center !important;
            cursor: pointer;
        }
        
        /* Force no background on the li itself when hovering */
        .menu-section-label:hover,
        .menu-section-label:focus,
        .menu-section-label.active,
        .sidebar .nav > li.menu-section-label:hover,
        .sidebar .nav > li.menu-section-label.active {
            background: transparent !important;
        }

        /* Specific targeting for text inside the div */
        .menu-section-label > .menu-section-heading > .menu-text {
            color: #d1d5db !important;
            text-transform: uppercase !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            letter-spacing: 0.8px;
            line-height: normal !important;
        }
        /* Ensure arrow matches the text color */
        .menu-section-label > .menu-section-heading > .section-arrow {
            color: #d1d5db !important;
            opacity: 0.8; /* Slight opacity difference to be subtle but visible */
        }
        .menu-section-label:first-child {
            margin-top: 5px;
            border-top: none;
        }
        .menu-section-label.collapsed .section-arrow {
            transform: rotate(-90deg);
        }
        .menu-section-label > a {
            display: flex !important;
            justify-content: space-between !important;
            width: 100%;
            color: inherit !important;
            text-decoration: none !important;
        }
    </style>
    <script>
        // Init Collapsed State on Load
        document.addEventListener("DOMContentLoaded", function() {
            var sections = document.querySelectorAll(".menu-section-label");
            sections.forEach(function(section) {
                // Find slug/id
                var cl = section.classList;
                var slug = "";
                for(var i=0; i<cl.length; i++) {
                    if(cl[i].startsWith("menu-item-")) {
                        slug = cl[i].replace("menu-item-", "");
                        break;
                    }
                }
                
                if(slug) {
                    var isCollapsed = localStorage.getItem("menus_sections_collapsed_" + slug) === "true";
                    if(isCollapsed) {
                        section.classList.add("collapsed");
                        // Trigger hide
                        var next = section.nextElementSibling;
                        while(next && !next.classList.contains("menu-section-label")) {
                            next.style.display = "none";
                            next = next.nextElementSibling;
                        }
                    }
                }
            });
        });
    </script>';
}
