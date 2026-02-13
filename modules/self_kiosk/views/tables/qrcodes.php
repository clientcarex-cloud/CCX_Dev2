<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    'description',
    'date_created',
    'staff_id'
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'self_kiosk_qrcodes';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id', 'slug', 'settings']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $details = '<a href="#" onclick="edit_code(' . $aRow['id'] . ', \'' . $aRow['name'] . '\', \'' . htmlspecialchars($aRow['description']) . '\', \'' . htmlspecialchars($aRow['settings'] ?? '') . '\'); return false;">' . $aRow['name'] . '</a>';
    $details .= '<div class="row-options">';
    $details .= '<a href="#" onclick="edit_code(' . $aRow['id'] . ', \'' . $aRow['name'] . '\', \'' . htmlspecialchars($aRow['description']) . '\', \'' . htmlspecialchars($aRow['settings'] ?? '') . '\'); return false;">' . _l('edit') . '</a>';
    $details .= ' | <a href="' . admin_url('self_kiosk/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    $details .= '</div>';

    $row[] = $details;

    $row[] = $aRow['description'];

    $row[] = _dt($aRow['date_created']);

    $staff_full_name = get_staff_full_name($aRow['staff_id']);
    $row[] = '<a href="' . admin_url('staff/profile/' . $aRow['staff_id']) . '">' . $staff_full_name . '</a>';

    // Feedback entries button
    $row[] = '<a href="' . admin_url('self_kiosk/feedbacks/' . $aRow['id']) . '" class="btn btn-default btn-xs">View Entries</a>';

    // Options column with QR Code button
    $options = icon_btn('#', 'fa fa-qrcode', 'btn-default', ['onclick' => 'view_qr_code(\'' . $aRow['slug'] . '\'); return false;']);
    $row[] = $options;

    $output['aaData'][] = $row;
}
