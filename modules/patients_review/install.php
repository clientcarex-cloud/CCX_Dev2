<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Initialize options if they don't exist
if (get_option('patients_records') === null) {
    add_option('patients_records', 'none');
}
if (get_option('delete_system') === null) {
    add_option('delete_system', '');
}
if (get_option('archive_system') === null) {
    add_option('archive_system', '');
}
if (get_option('enable_unarchive_password') === null) {
    add_option('enable_unarchive_password', 0);
}
if (get_option('unarchive_password') === null) {
    add_option('unarchive_password', '');
}
if (get_option('enable_delete_password') === null) {
    add_option('enable_delete_password', 0);
}
if (get_option('delete_password') === null) {
    add_option('delete_password', '');
}

// New Options
if (get_option('patients_review_item_groups') === null) {
    add_option('patients_review_item_groups', '');
}
if (get_option('patients_review_authorized_user') === null) {
    add_option('patients_review_authorized_user', '');
}
if (get_option('show_archived_tab') === null) {
    add_option('show_archived_tab', 1);
}

// Password Timestamps
if (get_option('unarchive_password_last_changed') === null) {
    add_option('unarchive_password_last_changed', '');
}
if (get_option('delete_password_last_changed') === null) {
    add_option('delete_password_last_changed', '');
}

// Auto-migration for archive system
$CI->load->dbforge();
if (!$CI->db->field_exists('is_archived', db_prefix() . 'visits')) {
    $fields = [
        'is_archived' => [
            'type' => 'TINYINT',
            'constraint' => 1,
            'default' => 0
        ]
    ];
    $CI->dbforge->add_column('visits', $fields);
}

// Auto-migration for Deep Archive Tables
// tblinvoices_archive
if (!$CI->db->table_exists(db_prefix() . 'invoices_archive')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . 'invoices_archive LIKE ' . db_prefix() . 'invoices');
}
// tblinvoicepaymentrecords_archive
if (!$CI->db->table_exists(db_prefix() . 'invoicepaymentrecords_archive')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . 'invoicepaymentrecords_archive LIKE ' . db_prefix() . 'invoicepaymentrecords');
}
// tblitemable_archive
if (!$CI->db->table_exists(db_prefix() . 'itemable_archive')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . 'itemable_archive LIKE ' . db_prefix() . 'itemable');
}
