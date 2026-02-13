<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'lab_tests_statuses';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $row[] = $aRow['name'];

    $output['aaData'][] = $row;
}
