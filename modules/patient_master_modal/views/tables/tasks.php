<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    'startdate',
    'duedate',
    'status',
    'priority',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'tasks';
$where = ['AND rel_id=' . $patient_id . ' AND rel_type="customer"'];
$join = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Name
    $outputName = '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['name'] . '</a>';
    $row[] = $outputName;

    // Start Date
    $row[] = _d($aRow['startdate']);

    // Due Date
    $row[] = _d($aRow['duedate']);

    // Status
    $status = get_task_status_by_id($aRow['status']);
    $outputStatus = '<span class="label label-default inline-block" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '; color:' . $status['color'] . '">' . $status['name'] . '</span>';
    $row[] = $outputStatus;

    // Priority
    $outputPriority = '<span class="text-' . get_task_priority_class($aRow['priority']) . ' inline-block">' . task_priority($aRow['priority']) . '</span>';
    $row[] = $outputPriority;

    // Options
    $options = '';
    if (has_permission('tasks', '', 'edit')) {
        $options .= icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'init_task_modal(' . $aRow['id'] . '); return false;']);
    }
    if (has_permission('tasks', '', 'delete')) {
        $options .= icon_btn('#', 'remove', 'btn-danger', ['onclick' => 'delete_task_inline(' . $aRow['id'] . '); return false;']);
    }
    $row[] = $options;

    $output['aaData'][] = $row;
}
