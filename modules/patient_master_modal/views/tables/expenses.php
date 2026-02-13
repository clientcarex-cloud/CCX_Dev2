<?php
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'expenses_categories.name as category_name',
    'amount',
    'reference_no',
    'date',
    db_prefix() . 'payment_modes.name as payment_mode_name',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'expenses';

$join = [
    'LEFT JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
    'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'expenses.paymentmode',
    'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'expenses.currency',
];

$where = [];

if ($this->ci->input->post('clientid')) {
    array_push($where, 'AND ' . db_prefix() . 'expenses.clientid = ' . $this->ci->db->escape_str($this->ci->input->post('clientid')));
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'expenses.id',
    db_prefix() . 'expenses.currency',
    db_prefix() . 'currencies.name as currency_name',
    'category',
    'paymentmode'
]);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Category
    $categoryOutput = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['category_name'] . '</a>';
    $row[] = $categoryOutput;

    // Amount
    $row[] = app_format_money($aRow['amount'], $aRow['currency_name']);

    // Reference
    $row[] = $aRow['reference_no'];

    // Date
    $row[] = _d($aRow['date']);

    // Payment Mode
    $row[] = $aRow['payment_mode_name'];

    $output['aaData'][] = $row;
}
