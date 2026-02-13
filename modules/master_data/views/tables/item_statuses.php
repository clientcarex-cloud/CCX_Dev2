<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'item_statuses';

$where = [];
if (isset($group_id)) {
    array_push($where, 'AND group_id = ' . $this->ci->db->escape_str($group_id));
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, ['id', 'group_id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $nameHtml = '<a href="#" onclick="edit_status(this,' . $aRow['id'] . '); return false" data-name="' . $aRow['name'] . '">' . $aRow['name'] . '</a>';
    $row[] = $nameHtml;

    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit_status(this,' . $aRow['id'] . '); return false;', 'data-name' => $aRow['name']]);
    $options .= icon_btn('master_data/delete_item_status/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $row[] = $options;

    $output['aaData'][] = $row;
}
