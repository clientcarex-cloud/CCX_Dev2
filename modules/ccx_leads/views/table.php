<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'phonenumber',
    'email',
    'status',
    'assigned',
    'dateadded',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'ccx_leads';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
    'addedfrom',
]);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $name = '<a href="' . admin_url('ccx_leads/lead/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';
    $name .= '<div class="row-options">';
    $name .= '<a href="' . admin_url('ccx_leads/lead/' . $aRow['id']) . '">' . _l('view') . '</a>';
    $name .= ' | <a href="' . admin_url('ccx_leads/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    $name .= '</div>';

    $row[] = $name;
    $row[] = '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>';
    $row[] = '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>';

    // Status
    $status_name = '';
    if ($aRow['status'] == 1) {
        $status_name = '<span class="label label-info">New</span>';
    } else {
        $status_name = '<span class="label label-default">Unknown</span>';
    }
    // You can extend this with actual status names if you have a statuses table or array
    $row[] = $status_name;

    // Assigned
    $assigned_name = '';
    if ($aRow['assigned'] != 0) {
        $staff = get_staff($aRow['assigned']);
        if ($staff) {
            $assigned_name = $staff->firstname . ' ' . $staff->lastname;
        }
    }
    $row[] = $assigned_name;

    $row[] = _dt($aRow['dateadded']);

    $options = icon_btn('ccx_leads/lead/' . $aRow['id'], 'pencil-square-o');
    $options .= icon_btn('ccx_leads/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    $row[] = $options;

    $output['aaData'][] = $row;
}
