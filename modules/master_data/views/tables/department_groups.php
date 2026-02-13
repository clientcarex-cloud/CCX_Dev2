<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'id', // Placeholder for departments
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'department_groups';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $row[] = '<a href="#" onclick="edit_department_group(this, ' . $aRow['id'] . '); return false" data-name="' . $aRow['name'] . '" data-departments=\'' . json_encode($this->ci->master_data_model->get_department_group_items($aRow['id'])) . '\'>' . $aRow['name'] . '</a>';

    // Fetch associated departments for display
    $departments_ids = $this->ci->master_data_model->get_department_group_items($aRow['id']);
    $departments_names = [];
    if (!empty($departments_ids)) {
        $this->ci->db->where_in('departmentid', $departments_ids);
        $depts = $this->ci->db->get(db_prefix() . 'departments')->result_array();
        foreach ($depts as $dept) {
            $departments_names[] = $dept['name'];
        }
    }
    $row[] = implode(', ', $departments_names);

    $options = icon_btn('#', 'pencil-square-o', 'btn-default', [
        'onclick' => 'edit_department_group(this, ' . $aRow['id'] . '); return false',
        'data-name' => $aRow['name'],
        'data-departments' => json_encode($departments_ids)
    ]);
    $options .= icon_btn('master_data/delete_department_group/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $row[] = $options;

    $output['aaData'][] = $row;
}
