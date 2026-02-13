<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Patient Master Modal
Description: Reusable Patient Action Modal for managing patient details, reminders, notes, etc.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PATIENT_MASTER_MODAL_MODULE_NAME', 'patient_master_modal');

hooks()->add_action('admin_init', 'patient_master_modal_init_menu_items');

function patient_master_modal_init_menu_items()
{
    // No menu items needed as this is a utility module for now.
}
