<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'userid',
    'company',
    db_prefix() . 'patients_extra.mr_number as mr_number',
    'phonenumber',
    db_prefix() . 'patients_extra.age as age',
    'datecreated',
    '(SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM ' . db_prefix() . 'customers_groups JOIN ' . db_prefix() . 'customer_groups ON ' . db_prefix() . 'customer_groups.groupid = ' . db_prefix() . 'customers_groups.id WHERE customer_id=' . db_prefix() . 'clients.userid) as customer_groups'
];

$sIndexColumn = 'userid';
$sTable = db_prefix() . 'clients';

$join = [
    'LEFT JOIN ' . db_prefix() . 'patients_extra ON ' . db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'clients.userid',
    'LEFT JOIN ' . db_prefix() . 'name_titles ON ' . db_prefix() . 'name_titles.id = ' . db_prefix() . 'patients_extra.title_id'
];



$additionalSelect = [
    db_prefix() . 'patients_extra.age_unit',
    db_prefix() . 'patients_extra.dob',
    db_prefix() . 'patients_extra.gender',
    db_prefix() . 'name_titles.name as title',
    db_prefix() . 'patients_extra.mobile_number as extra_mobile',
    '(SELECT email FROM ' . db_prefix() . 'contacts WHERE userid=' . db_prefix() . 'clients.userid AND is_primary=1 LIMIT 1) as email',
    '(SELECT COUNT(*) FROM ' . db_prefix() . 'visits WHERE patient_id=' . db_prefix() . 'clients.userid) as visits_count',
    'address', // Added address
    db_prefix() . 'patients_extra.uid_no', // Added UID
    db_prefix() . 'patients_extra.attender_name', // Added Attender
    db_prefix() . 'patients_extra.referral_doctor_id', // Added Referral Doc ID

    // Ticket Optimization
    '(SELECT COUNT(*) FROM ' . db_prefix() . 'contacts WHERE userid=' . db_prefix() . 'clients.userid AND active=1) as total_contacts',
    '(SELECT id FROM ' . db_prefix() . 'contacts WHERE userid=' . db_prefix() . 'clients.userid AND active=1 AND is_primary=1 LIMIT 1) as primary_contact_id',
    // Fallback if no primary, just get one
    // But let's rely on primary first. The buildTicketsHtml logic can handle partials or I can get any ID.
    // If I want "ANY" contact ID if primary is missing?
    // Let's stick to primary for now. If missing, button might generic link.

    '(SELECT CONCAT(firstname, " ", lastname) FROM ' . db_prefix() . 'staff WHERE staffid=' . db_prefix() . 'patients_extra.referral_doctor_id) as referral_doctor_name'
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], $additionalSelect);

$output = $result['output'];
$rResult = $result['rResult'];

// Fix start variable
$CI = &get_instance();
$start = $CI->input->post('start');
if (!is_numeric($start)) {
    $start = 0;
}
$i = $start + 1;

foreach ($rResult as $aRow) {
    $row = [];

    // 1. S.No
    $row[] = $i++;

    // Prepare attributes safely
    $att_address = htmlspecialchars($aRow['address'] ?? '', ENT_QUOTES);
    $att_uid = htmlspecialchars($aRow['uid_no'] ?? '', ENT_QUOTES);
    $att_attender = htmlspecialchars($aRow['attender_name'] ?? '', ENT_QUOTES);
    $att_doc = htmlspecialchars($aRow['referral_doctor_name'] ?? '', ENT_QUOTES);
    $att_groups = htmlspecialchars($aRow['customer_groups'] ?? '', ENT_QUOTES);
    $att_email = htmlspecialchars($aRow['email'] ?? '', ENT_QUOTES);

    // 4. Mobile Logic for Data Attribute
    $mobile = $aRow['extra_mobile'] ? $aRow['extra_mobile'] : $aRow['phonenumber'];
    $mobile = (string) $mobile;
    $att_mobile = htmlspecialchars($mobile, ENT_QUOTES);

    // 2. Patient Name & Date
    $nameHtml = '<a href="#" onclick="open_action_modal(this, null); return false;"
        data-patient-id="' . $aRow['userid'] . '"
        data-name="' . $aRow['company'] . '"
        data-mr="' . $aRow['mr_number'] . '"
        data-gender="' . $aRow['gender'] . '"
        data-title="' . $aRow['title'] . '"
        data-age="' . $aRow['age'] . '"
        data-age-unit="' . $aRow['age_unit'] . '"
        data-dob="' . _d($aRow['dob']) . '"
        data-phone="' . $att_mobile . '"
        data-email="' . $att_email . '"
        data-address="' . $att_address . '"
        data-uid="' . $att_uid . '"
        data-attender="' . $att_attender . '"
        data-referral-doc="' . $att_doc . '"
        data-visits="' . $aRow['visits_count'] . '"
        data-groups="' . $att_groups . '"
        data-reg-date="' . _d($aRow['datecreated']) . '"
        data-total-contacts="' . ($aRow['total_contacts'] ?? 0) . '"
        data-primary-contact-id="' . ($aRow['primary_contact_id'] ?? '') . '"
        data-next="" data-days="" data-invert=""
        style="font-weight:bold; font-size:14px; color:#333;">' .
        ($aRow['title'] ? $aRow['title'] . ' ' : '') . $aRow['company'] .
        '</a><br>';
    $nameHtml .= '<small class="text-muted">' . _d($aRow['datecreated']) . '</small>';
    $row[] = $nameHtml;

    // 3. MR No & Visits
    $mrHtml = '<span style="color:#333;">MR No: ' . $aRow['mr_number'] . '</span><br>';
    $mrHtml .= '<small class="text-muted">Total Visits: <b>' . $aRow['visits_count'] . '</b></small>';
    $row[] = $mrHtml;

    // 4. Contact Info (Masked)
    $masked_mobile = (strlen($mobile) > 4) ? substr($mobile, 0, 2) . '******' . substr($mobile, -2) : $mobile;

    $email = $aRow['email'];
    $masked_email = '';
    if ($email) {
        $parts = explode('@', $email);
        if (count($parts) == 2) {
            $masked_email = substr($parts[0], 0, 2) . '****@' . $parts[1];
        } else {
            $masked_email = $email;
        }
    }

    $contactHtml = '<div class="contact-info-wrapper">';
    $contactHtml .= '<i class="fa fa-phone text-success"></i> ';
    $contactHtml .= '<span class="masked-content" data-real="' . $mobile . '">' . $masked_mobile . '</span> ';
    $contactHtml .= '<a href="#" class="toggle-mask text-muted mleft5"><i class="fa fa-eye"></i></a>';
    $contactHtml .= '<br>';
    if ($email) {
        $contactHtml .= '<i class="fa fa-envelope text-info"></i> ';
        $contactHtml .= '<span class="masked-content" data-real="' . $email . '">' . $masked_email . '</span> ';
        $contactHtml .= '<a href="#" class="toggle-mask text-muted mleft5"><i class="fa fa-eye"></i></a>';
    }
    $contactHtml .= '</div>';
    $row[] = $contactHtml;

    // 5. Age / DOB
    $ageHtml = $aRow['age'] . ' ' . $aRow['age_unit'];
    if ($aRow['dob']) {
        $ageHtml .= '<br><small class="text-muted">DOB: ' . _d($aRow['dob']) . '</small>';
    }
    $row[] = $ageHtml;

    // 6. Since
    $since = '';
    if ($aRow['datecreated']) {
        $d1 = new DateTime($aRow['datecreated']);
        $d2 = new DateTime();
        $diff = $d2->diff($d1);
        if ($diff->y > 0) {
            $since .= $diff->y . ' Yrs ';
        }
        $since .= $diff->m . ' Months';
    }
    $row[] = $since;

    // 7. Branch
    $row[] = $aRow['customer_groups'];

    $output['aaData'][] = $row;
}

echo json_encode($output);
die;
