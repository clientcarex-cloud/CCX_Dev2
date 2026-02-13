<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'description',
    'addedfrom',
    'dateadded',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'notes';
$where = ['AND rel_id=' . $patient_id . ' AND rel_type="customer"'];

$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'notes.addedfrom',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['firstname', 'lastname']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Description
    $row[] = $aRow['description'];

    // Staff
    $staffName = get_staff_full_name($aRow['addedfrom']);
    $row[] = '<a href="' . admin_url('profile/' . $aRow['addedfrom']) . '">' . $staffName . '</a>';

    // Date Added
    $row[] = _dt($aRow['dateadded']);

    $output['aaData'][] = $row;
}
