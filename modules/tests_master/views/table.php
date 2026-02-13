<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'description',
    'code',
    db_prefix() . 'departments.name',
    'rate',
    'b2b_price',
    'active_template_type',
    'is_active',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'items';

$join = [
    'LEFT JOIN ' . db_prefix() . 'items_groups ON ' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id',
    'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'items.department_id',
];


$where = [
    'AND ' . db_prefix() . 'items_groups.name = "Tests"',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'items.id', 'long_description', 'group_id', 'department_id', 'is_blood_sample_required', 'is_price_changable', 'is_authorization_required', 'b2b_price', 'test_method_id', 'is_active']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $descriptionOutput = '<a href="#" onclick="edit_test(this, ' . $aRow['id'] . '); return false;" data-name="' . $aRow['description'] . '" data-code="' . $aRow['code'] . '" data-group_id="' . $aRow['group_id'] . '" data-department_id="' . $aRow['department_id'] . '" data-rate="' . $aRow['rate'] . '" data-is_blood_sample_required="' . $aRow['is_blood_sample_required'] . '" data-description="' . htmlspecialchars($aRow['long_description']) . '">' . $aRow['description'] . '</a>';

    if ($aRow['is_blood_sample_required'] == 1) {
        $descriptionOutput .= '<br /><small>Sample Required</small>';
    }

    $row[] = $descriptionOutput;

    $row[] = $aRow['code'];

    // Item Group column removed

    $row[] = $aRow[db_prefix() . 'departments.name'];

    $row[] = app_format_money($aRow['rate'], get_base_currency());

    $row[] = app_format_money($aRow['b2b_price'], get_base_currency());

    // Price Change
    if ($aRow['is_price_changable'] == 1) {
        $row[] = '<span class="label label-success">Yes</span>';
    } else {
        $row[] = '<span class="label label-default">No</span>';
    }

    // Auth Req
    if ($aRow['is_authorization_required'] == 1) {
        $row[] = '<span class="label label-warning">Yes</span>';
    } else {
        $row[] = '<span class="label label-default">No</span>';
    }

    $activeType = $aRow['active_template_type'];
    if ($activeType == 'fixed') {
        $row[] = '<span class="label label-info">Fixed Template</span>';
    } else {
        $row[] = '<span class="label label-default">Word Template</span>';
    }

    $toggle = '<div class="onoffswitch">
        <input type="checkbox" data-switch-url="' . admin_url() . 'tests_master/change_status_active" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow['is_active'] == 1 ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
    </div>';
    $row[] = $toggle;

    $row[] = '<a href="' . admin_url('tests_master/templates/' . $aRow['id']) . '" class="btn btn-info btn-icon"><i class="fa fa-cog"></i></a>';


    $options = '<a href="#" class="btn btn-default btn-icon" onclick="edit_test(this, ' . $aRow['id'] . '); return false;" 
        data-name="' . $aRow['description'] . '" 
        data-code="' . $aRow['code'] . '" 
        data-group_id="' . $aRow['group_id'] . '" 
        data-department_id="' . $aRow['department_id'] . '"
        data-rate="' . $aRow['rate'] . '" 
        data-b2b_price="' . $aRow['b2b_price'] . '"
        data-is_blood_sample_required="' . $aRow['is_blood_sample_required'] . '"
        data-is_price_changable="' . $aRow['is_price_changable'] . '"
        data-is_authorization_required="' . $aRow['is_authorization_required'] . '"
        data-test_method_id="' . $aRow['test_method_id'] . '"
        data-is_active="' . $aRow['is_active'] . '"
        data-description="' . htmlspecialchars($aRow['long_description']) . '">
        <i class="fa fa-pencil"></i>
    </a>';



    $row[] = $options;

    $output['aaData'][] = $row;
}
