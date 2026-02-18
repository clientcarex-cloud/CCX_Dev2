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

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [
    db_prefix() . 'leads_status',
    db_prefix() . 'staff',
], [
    'addedfrom',
    db_prefix() . 'leads_status.name as status_name',
    db_prefix() . 'leads_status.color as status_color',
    db_prefix() . 'staff.firstname',
    db_prefix() . 'staff.lastname',
    'staffid',
], [
    'LEFT JOIN ' . db_prefix() . 'leads_status ON ' . db_prefix() . 'leads_status.id = ' . db_prefix() . 'ccx_leads.status',
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'ccx_leads.assigned',
]);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $name = '<a href="' . admin_url('ccx_leads/lead/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';
    $name .= '<div class="row-options">';
    $name .= '<a href="' . admin_url('ccx_leads/lead/' . $aRow['id']) . '">' . _l('ccx_leads_view') . '</a>';
    $name .= ' | <a href="' . admin_url('ccx_leads/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('ccx_leads_delete') . '</a>';
    $name .= '</div>';

    $row[] = $name;
    $row[] = '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>';
    $row[] = '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>';

    // Status
    $status_name = $aRow['status_name'];
    $status_color = $aRow['status_color'];
    if ($status_name) {
        $status_name = '<span class="label label-default" style="color:' . $status_color . ';border:1px solid ' . $status_color . ';background:transparent;">' . $status_name . '</span>';
    } else {
        $status_name = '<span class="label label-default">' . _l('unknown') . '</span>';
    }
    $row[] = $status_name;

    // Assigned
    $assigned_name = '';
    if ($aRow['assigned'] != 0) {
        $assigned_name = '<a href="' . admin_url('profile/' . $aRow['assigned']) . '">' . staff_profile_image($aRow['assigned'], [
            'staff-profile-image-small',
        ]) . '</a>';
        $assigned_name .= ' <a href="' . admin_url('profile/' . $aRow['assigned']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
    }
    $row[] = $assigned_name;

    $row[] = time_ago($aRow['dateadded']) . '<br><span class="text-muted small">' . _dt($aRow['dateadded']) . '</span>';

    $options = icon_btn('ccx_leads/lead/' . $aRow['id'], 'pencil-square-o', 'btn-default');
    $options .= icon_btn('ccx_leads/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    $row[] = $options;

    $output['aaData'][] = $row;
}
