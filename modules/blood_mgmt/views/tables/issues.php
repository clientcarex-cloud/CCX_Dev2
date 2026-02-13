<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'patient_name',
    'doctor_name',
    'hospital',
    'issue_date',
    'amount',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'blood_issues';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $subjectHtml = $aRow['patient_name'];
    $subjectHtml .= '<div class="row-options">';
    // $subjectHtml .= '<a href="#" onclick="edit_issue(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>'; // Edit disabled for now as per controller logic
    $subjectHtml .= ' <a href="' . admin_url('blood_mgmt/delete_issue/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    $subjectHtml .= '</div>';

    $row[] = $subjectHtml;
    $row[] = $aRow['doctor_name'];
    $row[] = $aRow['hospital'];
    $row[] = _dt($aRow['issue_date']);
    $row[] = app_format_money($aRow['amount'], ''); // Assuming base currency

    $output['aaData'][] = $row;
}
