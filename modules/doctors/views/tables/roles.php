<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
];

$sIndexColumn = 'roleid';
$sTable = db_prefix() . 'roles';

$where = [
    'AND ' . db_prefix() . 'roles.name IN ("Doctor", "Jr. Doctor", "Sr. Doctor")'
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, ['roleid']);
$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="' . admin_url('doctors/roles/role/' . $aRow['roleid']) . '" class="mbot10 display-block tw-font-medium">' . e($_data) . '</a>';
            $_data .= '<span class="mtop10 display-block">' . _l('roles_total_users') . ' ' . total_rows(db_prefix() . 'staff', [
                'role' => $aRow['roleid'],
            ]) . '</span>';
        }
        $row[] = $_data;
    }

    $options = '<div class="tw-flex tw-items-center tw-space-x-2">';
    $options .= '<a href="' . admin_url('doctors/roles/role/' . $aRow['roleid']) . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
        <i class="fa-regular fa-pen-to-square fa-lg"></i>
    </a>';

    // Disable delete for system roles if we want, but for now I'll just change link to doctors/roles/delete
    // Actually, 'Doctor' role might be critical, better not delete. But user didn't explicitly say "prevent delete". 
    // I will include delete but point to my controller which will have safeguards.

    $options .= '</div>';

    $row[] = $options;

    $output['aaData'][] = $row;
}
