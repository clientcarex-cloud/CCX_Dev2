<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'medicine_doses';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $row[] = $aRow['name'];

    $options = '<a href="#" class="btn btn-default btn-icon" onclick="edit_medicine_dose(this, ' . $aRow['id'] . '); return false;" data-name="' . $aRow['name'] . '">
        <i class="fa fa-pencil"></i>
    </a>';
    $options .= '<a href="' . admin_url('master_data/delete_medicine_dose/' . $aRow['id']) . '" class="btn btn-danger btn-icon _delete">
        <i class="fa fa-remove"></i>
    </a>';

    $row[] = $options;

    $output['aaData'][] = $row;
}
