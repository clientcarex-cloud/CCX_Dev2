<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'description',
    'price',
    'created_at',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'surgery_types';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $row[] = '<a href="#" onclick="edit_surgery_type(' . $aRow['id'] . ', ' . htmlspecialchars(json_encode($aRow)) . '); return false;">' . $aRow['name'] . '</a>';

    $row[] = $aRow['description'];

    $row[] = app_format_money($aRow['price'], '');

    $row[] = _dt($aRow['created_at']);

    $options = '<a href="#" class="btn btn-default btn-icon" onclick="edit_surgery_type(' . $aRow['id'] . ', ' . htmlspecialchars(json_encode($aRow)) . '); return false;"><i class="fa fa-pencil"></i></a>';
    $options .= icon_btn('surgeries/delete_type/' . $aRow['id'], 'trash', 'btn-danger _delete');

    $row[] = $options;

    $output['aaData'][] = $row;
}

echo json_encode($output);
die;
