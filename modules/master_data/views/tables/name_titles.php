<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'name_titles';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $row[] = $aRow['name'];

    $options = '<a href="#" class="btn btn-default btn-icon" onclick="edit_name_title(this, ' . $aRow['id'] . '); return false;" data-name="' . $aRow['name'] . '">
        <i class="fa fa-pencil"></i>
    </a>';

    $row[] = $options;

    $output['aaData'][] = $row;
}
