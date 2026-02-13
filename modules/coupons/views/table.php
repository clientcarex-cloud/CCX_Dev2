<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'code',
    'name',
    'type',
    'amount',
    'start_date',
    'end_date',
    'active',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'coupons';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id', 'type_settings']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Code
    $row[] = '<a href="' . admin_url('coupons/coupon/' . $aRow['id']) . '">' . $aRow['code'] . '</a>';

    // Name
    $row[] = $aRow['name'];

    // Type
    $type_name = '';
    switch ($aRow['type']) {
        case 1:
            $type_name = _l('coupon_type_percentage');
            break;
        case 2:
            $type_name = _l('coupon_type_fixed_amount');
            break;
        case 3:
            $type_name = _l('coupon_type_price_cap');
            break;
        default:
            $type_name = _l('coupon_type_unknown');
            break;
    }
    // Fallback
    if ($type_name == 'coupon_type_percentage')
        $type_name = 'Percentage';
    if ($type_name == 'coupon_type_fixed_amount')
        $type_name = 'Fixed Amount';
    if ($type_name == 'coupon_type_price_cap')
        $type_name = 'Price Cap';

    // Append price cap details
    if ($aRow['type'] == 3 && !empty($aRow['type_settings'])) {
        $settings = json_decode($aRow['type_settings'], true);
        $min = isset($settings['min_total']) ? app_format_money($settings['min_total'], '') : '0';
        $max = isset($settings['max_total']) ? app_format_money($settings['max_total'], '') : '∞';
        $type_name .= '<br><span class="text-muted text-xs">(' . $min . ' - ' . $max . ')</span>';
    }

    $row[] = $type_name;

    // Amount
    $amount_display = app_format_money($aRow['amount'], '');
    if ($aRow['type'] == 1) {
        $amount_display .= '%';
    } elseif ($aRow['type'] == 3) {
        // Check sub-type
        $settings = json_decode($aRow['type_settings'], true);
        $mode = $settings['discount_mode'] ?? '1';
        if ($mode == '1') {
            $amount_display .= '%';
        }
    }

    $row[] = $amount_display;

    // Start Date
    $row[] = _d($aRow['start_date']);

    // End Date (with time left tag)
    $endDate = _d($aRow['end_date']);
    $timeLeft = '';
    if (!empty($aRow['end_date'])) {
        $now = new DateTime();
        $end = new DateTime($aRow['end_date']);
        $interval = $now->diff($end);

        if ($now > $end) {
            $timeLeft = '<br /><span class="label label-danger">Expired</span>';
        } else {
            $daysLeft = $interval->days;
            $class = 'success';
            if ($daysLeft < 3) {
                $class = 'danger';
            } elseif ($daysLeft < 7) {
                $class = 'warning';
            }
            $timeLeft = '<br /><span class="label label-' . $class . '">' . $daysLeft . ' days left</span>';
        }
    }
    $row[] = $endDate . $timeLeft;

    // Active (Status)
    $row[] = ($aRow['active'] == 1) ? '<span class="label label-success">' . _l('is_active_export') . '</span>' : '<span class="label label-danger">' . _l('is_not_active_export') . '</span>';

    // Options
    $options = '<a href="' . admin_url('coupons/coupon/' . $aRow['id']) . '" class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>';

    $row[] = $options;

    $output['aaData'][] = $row;
}
