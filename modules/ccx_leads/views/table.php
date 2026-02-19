<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    '1', // Bulk actions
    db_prefix() . 'leads.id as id',
    db_prefix() . 'leads.name as name',
    db_prefix() . 'leads.company as company',
    db_prefix() . 'leads.email as email',
    db_prefix() . 'leads.phonenumber as phonenumber',
    db_prefix() . 'leads.assigned as assigned',
    db_prefix() . 'leads.status as status',
    db_prefix() . 'leads.lastcontact as lastcontact',
    db_prefix() . 'leads.dateadded as dateadded',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'leads';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
    // Additional columns to fetch but not display
    'junk',
    'lost',
    'source'
]);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

    $row[] = $aRow['id'];

    // Name
    $nameRow = '<a href="#" onclick="ccx_lead_profile(' . $aRow['id'] . '); return false;">' . $aRow['name'] . '</a>';
    $nameRow .= '<div class="row-options">';
    $nameRow .= '<a href="#" onclick="ccx_lead_profile(' . $aRow['id'] . '); return false;">' . _l('view') . '</a>';
    $nameRow .= ' | <a href="' . admin_url('leads/index/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    $nameRow .= '</div>';
    $row[] = $nameRow;

    $row[] = $aRow['company'];
    $row[] = ($aRow['email'] != '' ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');
    $row[] = ($aRow['phonenumber'] != '' ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');

    // Assigned
    $assignedOutput = '';
    if ($aRow['assigned'] != 0) {
        $full_name = get_staff_full_name($aRow['assigned']);
        $assignedOutput = '<a href="' . admin_url('profile/' . $aRow['assigned']) . '">' . staff_profile_image($aRow['assigned'], [
            'staff-profile-image-small',
        ]) . '</a>';
        $assignedOutput .= ' <a href="' . admin_url('profile/' . $aRow['assigned']) . '">' . $full_name . '</a>';
    }
    $row[] = $assignedOutput;

    // Status
    /* $status = $this->leads_model->get_status($aRow['status']);
    $statusOutput = '';
    if ($status) {
        $statusOutput = '<span class="label label-default inline-block" style="color:' . $status->color . ';border:1px solid ' . $status->color . '">' . $status->name . '</span>';
    } else {
        $statusOutput = $aRow['status'];
    }
    $row[] = $statusOutput; */
    $row[] = $aRow['status'];

    $row[] = ($aRow['lastcontact'] ? time_ago($aRow['lastcontact']) . ' <span class="text-has-action" data-toggle="tooltip" data-title="' . _dt($aRow['lastcontact']) . '">' . _dt($aRow['lastcontact']) . '</span>' : '');

    $row[] = ($aRow['dateadded'] ? time_ago($aRow['dateadded']) . ' <span class="text-has-action" data-toggle="tooltip" data-title="' . _dt($aRow['dateadded']) . '">' . _dt($aRow['dateadded']) . '</span>' : '');

    $output['aaData'][] = $row;
}
