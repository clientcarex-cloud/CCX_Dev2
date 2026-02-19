<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Using existing tblleads and related tables.
// No table creation needed.

if (!$CI->db->table_exists(db_prefix() . 'ccx_leads_settings')) {
    // Placeholder for future settings if needed, though likely using core options or separate config
}
