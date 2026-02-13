<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'blood_bags.id',
    db_prefix() . 'blood_bags.blood_group',
    db_prefix() . 'blood_donors.name as donor_name',
    'donation_date',
    'expiry_date',
    'volume',
    'status',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'blood_bags';
$join = [
    'LEFT JOIN ' . db_prefix() . 'blood_donors ON ' . db_prefix() . 'blood_donors.id = ' . db_prefix() . 'blood_bags.donor_id',
];
$where = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'blood_bags.id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $subjectHtml = '<a href="#" onclick="edit_bag(' . $aRow['id'] . '); return false;">' . $aRow[db_prefix() . 'blood_bags.blood_group'] . '</a>';
    $subjectHtml .= '<div class="row-options">';
    $subjectHtml .= '<a href="#" onclick="edit_bag(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
    $subjectHtml .= ' | <a href="' . admin_url('blood_mgmt/delete_blood_bag/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    $subjectHtml .= '</div>';

    $row[] = $subjectHtml;
    $row[] = $aRow['donor_name'];
    $row[] = _d($aRow['donation_date']);
    $row[] = _d($aRow['expiry_date']);
    $row[] = $aRow['volume'];

    $status_name = '';
    $label_class = 'default';
    switch ($aRow['status']) {
        case 1:
            $status_name = _l('available');
            $label_class = 'success';
            break;
        case 2:
            $status_name = _l('issued');
            $label_class = 'info';
            break;
        case 3:
            $status_name = _l('expired');
            $label_class = 'danger';
            break;
        case 4:
            $status_name = _l('discarded');
            $label_class = 'warning';
            break;
    }
    $row[] = '<span class="label label-' . $label_class . '">' . $status_name . '</span>';

    $output['aaData'][] = $row;
}
