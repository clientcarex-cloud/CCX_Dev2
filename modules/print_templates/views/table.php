<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    'type',
    'is_default',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'print_templates';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $subjectOutput = '<a href="' . admin_url('print_templates/template/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';
    $subjectOutput .= '<div class="row-options">';
    $subjectOutput .= '<a href="' . admin_url('print_templates/template/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    $subjectOutput .= '</div>';

    $row[] = $subjectOutput;
    $row[] = $aRow['type'];

    $is_default = '';
    if ($aRow['is_default'] == 1) {
        $is_default = '<span class="label label-success">' . _l('yes') . '</span>';
    } else {
        $is_default = '<span class="label label-default">' . _l('no') . '</span>';
    }
    $row[] = $is_default;

    // Options
    $options = '<a href="' . admin_url('print_templates/template/' . $aRow['id']) . '" class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>';

    $row[] = $options;

    $output['aaData'][] = $row;
}
