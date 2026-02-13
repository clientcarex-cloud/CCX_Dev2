<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'blood_group',
    'age',
    'gender',
    'mobile',
    'last_donation_date',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'blood_donors';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $nameHtml = '<a href="#" onclick="edit_donor(' . $aRow['id'] . '); return false;">' . $aRow['name'] . '</a>';
    $nameHtml .= '<div class="row-options">';
    $nameHtml .= '<a href="#" onclick="edit_donor(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
    $nameHtml .= ' | <a href="' . admin_url('blood_mgmt/delete_donor/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    $nameHtml .= '</div>';

    $row[] = $nameHtml;
    $row[] = $aRow['blood_group'];
    $row[] = $aRow['age'];
    $row[] = $aRow['gender'];
    $row[] = $aRow['mobile'];
    $row[] = _d($aRow['last_donation_date']);

    $output['aaData'][] = $row;
}
