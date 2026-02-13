<?php

defined('BASEPATH') or exit('No direct script access allowed');

$qr_code_id = isset($extra_param['qr_code_id']) ? $extra_param['qr_code_id'] : (isset($qr_code_id) ? $qr_code_id : '');

$aColumns = [
    'patient_name',
    'mobile_number',
    'rating',
    'message',
    'date_created'
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'self_kiosk_feedback';

$where = [];
if ($qr_code_id != '') {
    $where = ['AND qr_code_id = ' . $qr_code_id];
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['patient_name'];
    $row[] = $aRow['mobile_number'];

    // Star rating display
    $rating_html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $aRow['rating']) {
            $rating_html .= '<i class="fa fa-star text-warning"></i>';
        } else {
            $rating_html .= '<i class="fa fa-star-o text-muted"></i>';
        }
    }
    $row[] = $rating_html;

    $row[] = $aRow['message'];
    $row[] = _dt($aRow['date_created']);

    $output['aaData'][] = $row;
}
