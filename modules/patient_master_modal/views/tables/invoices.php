<?php
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'number',
    'total',
    'YEAR(date) as year',
    'date',
    db_prefix() . 'invoices.status',
    'duedate',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'invoices';

$join = [
    'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency',
];

$where = [];

if ($this->ci->input->post('clientid')) {
    array_push($where, 'AND ' . db_prefix() . 'invoices.clientid = ' . $this->ci->db->escape_str($this->ci->input->post('clientid')));
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'invoices.id',
    db_prefix() . 'invoices.clientid',
    db_prefix() . 'currencies.name as currency_name',
    'formatted_number',
    'hash',
]);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Number
    $formattedNumber = format_invoice_number($aRow['id']);
    $numberOutput = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . $formattedNumber . '</a>';
    $row[] = $numberOutput;

    // Amount
    $row[] = app_format_money($aRow['total'], $aRow['currency_name']);

    // Year (Formatted as YYYY, Mon)
    // Using the 'date' field to format
    if (!empty($aRow['date'])) {
        $row[] = date('Y, M', strtotime($aRow['date']));
    } else {
        $row[] = $aRow['year'];
    }

    // Date
    $row[] = _d($aRow['date']);

    // Status
    $row[] = format_invoice_status($aRow[db_prefix() . 'invoices.status']);

    // Due Date
    $row[] = _d($aRow['duedate']);

    $output['aaData'][] = $row;
}
