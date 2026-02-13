<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'description',
    'date',
    'staff',
    'isnotified',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'reminders';
$where = ['AND rel_id=' . $patient_id . ' AND rel_type="customer"'];
$join = [
    'JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'reminders.staff',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'firstname',
    'lastname',
    'id',
    'creator',
    'rel_type',
]);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Description
    $row[] = process_text_content_for_display($aRow['description']);

    // Date
    $row[] = _dt($aRow['date']);

    // Staff
    $row[] = '<a href="' . admin_url('staff/profile/' . $aRow['staff']) . '">' . staff_profile_image($aRow['staff'], [
        'staff-profile-image-small',
    ]) . ' ' . e($aRow['firstname'] . ' ' . $aRow['lastname']) . '</a>';

    // Is Notified
    $isNotified = '';
    if ($aRow['isnotified'] == 1) {
        $isNotified = _l('reminder_is_notified_boolean_yes');
    } else {
        $isNotified = _l('reminder_is_notified_boolean_no');
    }
    $row[] = $isNotified;

    // Options
    $options = '';
    if ($aRow['creator'] == get_staff_user_id() || is_admin()) {
        // Using standard icon format
        // $options .= icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit_reminder(' . $aRow['id'] . ', this); return false;']); // Edit not fully implemented yet
        $options .= icon_btn('#', 'remove', 'btn-danger', ['onclick' => 'delete_reminder(' . $aRow['id'] . '); return false;']);
    }
    $row[] = $options;

    $output['aaData'][] = $row;
}
