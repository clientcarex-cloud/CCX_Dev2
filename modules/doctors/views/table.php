<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'firstname',
    'specialization',
    'doctor_profile_type',
    'email',
    'active',
];

$sIndexColumn = 'staffid';
$sTable = db_prefix() . 'staff';

$join = [
    'LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role',
];

// Only show Doctors
$where = ['AND (' . db_prefix() . 'roles.name IN ("Doctor", "Jr. Doctor", "Sr. Doctor"))'];

if ($this->ci->input->post('status') !== null) {
    $status = $this->ci->input->post('status');
    // If it's an array (standard DataTables filter), take the first element, otherwise take the value directly
    if (is_array($status)) {
        $status = $status[0];
    }

    if ($status == '0') {
        array_push($where, 'AND active = 0');
    } else {
        array_push($where, 'AND active = 1');
    }
} else {
    // Default show active if no filter
    // array_push($where, 'AND active = 1');
}

if ($this->ci->input->post('profile_type')) {
    array_push($where, 'AND doctor_profile_type = "' . $this->ci->db->escape_str($this->ci->input->post('profile_type')) . '"');
}

if ($this->ci->input->post('from_date')) {
    array_push($where, 'AND datecreated >= "' . to_sql_date($this->ci->input->post('from_date')) . ' 00:00:00"');
}

if ($this->ci->input->post('to_date')) {
    array_push($where, 'AND datecreated <= "' . to_sql_date($this->ci->input->post('to_date')) . ' 23:59:59"');
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'lastname',
    'staffid',
    'phonenumber',
    'profile_image'
]);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Doctor Name Column
    $doctor_name = $aRow['firstname'] . ' ' . $aRow['lastname'];
    $doctor_id = 'D-' . $aRow['staffid']; // Custom ID format
    $profile_image = staff_profile_image($aRow['staffid'], ['staff-profile-image-small']);

    $nameHtml = '<div style="display:flex; align-items:center;">';
    $nameHtml .= '<div style="margin-right:10px;">' . $profile_image . '</div>';
    $nameHtml .= '<div>';
    $nameHtml .= '<div style="font-weight:bold;">' . $doctor_name . '</div>';
    $nameHtml .= '<div class="text-muted" style="font-size:12px;">ID: ' . $doctor_id . '</div>';
    $nameHtml .= '</div>';
    $nameHtml .= '</div>';

    $row[] = $nameHtml;

    // Specialization
    $row[] = $aRow['specialization'] ? $aRow['specialization'] : '-';

    // Profile Type
    $row[] = $aRow['doctor_profile_type'] ? $aRow['doctor_profile_type'] : '-';

    // Contact
    $contactHtml = '<div>';
    $contactHtml .= '<div>' . $aRow['email'] . '</div>';
    $contactHtml .= '<div class="text-muted">' . $aRow['phonenumber'] . '</div>';
    $contactHtml .= '</div>';
    $row[] = $contactHtml;

    // Status
    $statusClass = $aRow['active'] == 1 ? 'success' : 'danger';
    $statusText = $aRow['active'] == 1 ? 'Active' : 'Inactive';
    // Using a simpler badge style to match image
    $statusHtml = '<span class="label label-' . $statusClass . '" style="border-radius:15px; padding:5px 15px;">' . $statusText . '</span>';
    $row[] = $statusHtml;

    // Pricing
    $pricingLink = admin_url('doctors/pricing/' . $aRow['staffid']);
    $pricingBtn = '<a href="' . $pricingLink . '" class="btn btn-success btn-xs">Referral</a>';
    $row[] = $pricingBtn;

    // Actions
    // Using explicit HTML to ensure it renders
    $editLink = admin_url('doctors/member/' . $aRow['staffid']);
    $options = '<a href="' . $editLink . '" class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>';
    $row[] = $options;

    $output['aaData'][] = $row;
}
