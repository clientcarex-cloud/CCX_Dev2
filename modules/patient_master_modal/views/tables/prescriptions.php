<?php
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'prescriptions.datecreated',
    db_prefix() . 'visits.visit_code',
    'CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as staff_name',
    db_prefix() . 'prescriptions.next_visit_date',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'prescriptions';

$join = [
    'LEFT JOIN ' . db_prefix() . 'visits ON ' . db_prefix() . 'visits.id = ' . db_prefix() . 'prescriptions.visit_id',
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'prescriptions.staff_id',
];

$where = [];

if ($this->ci->input->post('patient_id')) {
    array_push($where, 'AND ' . db_prefix() . 'prescriptions.patient_id = ' . $this->ci->db->escape_str($this->ci->input->post('patient_id')));
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'prescriptions.id',
    db_prefix() . 'prescriptions.staff_id'
]);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Date with Relative Time
    $date = _d($aRow[db_prefix() . 'prescriptions.datecreated']);
    $created = new DateTime($aRow[db_prefix() . 'prescriptions.datecreated']);
    $now = new DateTime();
    $interval = $now->diff($created);
    $days = $interval->days;

    $tag = '';
    if ($days == 0) {
        $tag = '<span class="label label-success" style="margin-left: 5px;">Today</span>';
    } elseif ($days == 1) {
        $tag = '<span class="label label-info" style="margin-left: 5px;">Yesterday</span>';
    } else {
        $tag = '<span class="label label-default" style="margin-left: 5px;">' . $days . ' days ago</span>';
    }

    $row[] = $date . '<br />' . $tag;

    // Visit Code
    $row[] = '<span class="label label-default">' . $aRow[db_prefix() . 'visits.visit_code'] . '</span>';

    // Staff
    $row[] = $aRow['staff_name'];

    // Next Visit
    if (!empty($aRow[db_prefix() . 'prescriptions.next_visit_date']) && $aRow[db_prefix() . 'prescriptions.next_visit_date'] != '0000-00-00') {
        $row[] = _d($aRow[db_prefix() . 'prescriptions.next_visit_date']);
    } else {
        $row[] = '';
    }

    // Options
    // Manually constructing button to ensure icon visibility
    $options = '<a href="#" onclick="view_prescription(\'' . admin_url('prescription/view/' . $aRow['id']) . '\'); return false;" class="btn btn-default btn-icon"><i class="fa fa-eye"></i></a>';

    $row[] = $options;

    $output['aaData'][] = $row;
}
