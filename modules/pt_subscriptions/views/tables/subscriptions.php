<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'clients.userid as userid', // 0
    db_prefix() . 'clients.company', // 1
    db_prefix() . 'patients_extra.mr_number', // 2
    // Subqueries for sorting/filtering
    '(SELECT dateadded FROM ' . db_prefix() . 'notes WHERE rel_type="customer" AND rel_id=' . db_prefix() . 'clients.userid ORDER BY dateadded DESC LIMIT 1) as last_note_date', // 3
    '1', // Status placeholder // 4
    '1', // Doctor placeholder // 5
    '(SELECT next_visit_date FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid AND next_visit_date >= CURDATE() ORDER BY next_visit_date ASC LIMIT 1) as next_visit_date', // 6
    '1', // Renewal Status // 7
    '1', // Renewal Dates // 8
    '(SELECT COUNT(*) FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND recurring > 0) as total_subs' // 9
];

$sIndexColumn = 'userid';
$sTable = db_prefix() . 'clients';

$join = [
    'LEFT JOIN ' . db_prefix() . 'patients_extra ON ' . db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'clients.userid',
    'LEFT JOIN ' . db_prefix() . 'name_titles ON ' . db_prefix() . 'name_titles.id = ' . db_prefix() . 'patients_extra.title_id',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], [
    db_prefix() . 'name_titles.name as title_name',
    db_prefix() . 'patients_extra.gender',
    '(SELECT description FROM ' . db_prefix() . 'notes WHERE rel_type="customer" AND rel_id=' . db_prefix() . 'clients.userid ORDER BY dateadded DESC LIMIT 1) as last_note_content',
    '(SELECT staff_id FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid AND next_visit_date >= CURDATE() ORDER BY next_visit_date ASC LIMIT 1) as next_doctor_id'
]);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // [0] Name & Last Visit (using Visit Count? No, Last Visit from Prescriptions usually)
    // Let's get "Last Visit" date (active prescription create date)
    // For efficiency, we might want to join or subquery it, but let's grab it quick or use empty for now.
    // Actually user requested "Name & Last Visit".
    $last_visit_query = "SELECT datecreated FROM " . db_prefix() . "prescriptions WHERE patient_id = " . $aRow['userid'] . " ORDER BY datecreated DESC LIMIT 1";
    $last_visit_res = $this->ci->db->query($last_visit_query)->row();
    $last_visit_date = $last_visit_res ? _d($last_visit_res->datecreated) : '-';

    // Name Link
    $nameHtml = '<a href="#" onclick="open_action_modal(this); return false;">';
    if (!empty($aRow['title_name'])) {
        $nameHtml .= $aRow['title_name'] . ' ';
    }
    $nameHtml .= $aRow['company'];
    $nameHtml .= '</a>';
    $nameHtml .= '<br /><span class="text-muted small">' . $last_visit_date;
    // Relative time for Last Visit
    if ($last_visit_res) {
        $visitDateTime = new DateTime($last_visit_res->datecreated);
        $currentDateTime = new DateTime();
        $interval = $currentDateTime->diff($visitDateTime);
        $days = $interval->days;

        if ($days == 0) {
            $nameHtml .= ' <span class="label label-success">Today</span>';
        } elseif ($days == 1) {
            $nameHtml .= ' <span class="label label-info">Yesterday</span>';
        } else {
            $nameHtml .= ' <span class="label label-default">' . $days . ' days ago</span>';
        }
    }
    $nameHtml .= '</span>';

    $row[] = $nameHtml;

    // [1] Mr. No & Visits Count
    $visits_count_query = "SELECT COUNT(*) as count FROM " . db_prefix() . "visits WHERE patient_id = " . $aRow['userid'];
    $visits_count = $this->ci->db->query($visits_count_query)->row()->count;

    $mrHtml = $aRow['tblpatients_extra.mr_number'];
    $mrHtml .= '<br /><span class="badge badge-info">' . $visits_count . ' Visits</span>';
    $row[] = $mrHtml;

    // [2] Last Contact
    // Using simple logic from Follow Ups
    $contactHtml = '';
    if (!empty($aRow['last_note_content'])) {
        $contactHtml .= '<span style="font-size: 13px; color: #333; display: block; margin-bottom: 2px;">' . mb_substr(strip_tags($aRow['last_note_content']), 0, 50) . (mb_strlen(strip_tags($aRow['last_note_content'])) > 50 ? '...' : '') . '</span>';

        $noteDate = new DateTime($aRow['last_note_date']);
        $now = new DateTime();
        $interval = $now->diff($noteDate);
        $timeAgo = '';
        if ($interval->y > 0)
            $timeAgo = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        elseif ($interval->m > 0)
            $timeAgo = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        elseif ($interval->d > 0)
            $timeAgo = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        elseif ($interval->h > 0)
            $timeAgo = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        elseif ($interval->i > 0)
            $timeAgo = $interval->i . ' min' . ($interval->i > 1 ? 's' : '') . ' ago';
        else
            $timeAgo = 'Just now';

        $contactHtml .= '<span class="text-muted small">' . _dt($aRow['last_note_date']) . '</span>';
        $contactHtml .= ' <span class="label label-default" style="font-size: 85%;">' . $timeAgo . '</span>';
    } else {
        $contactHtml = '<span class="text-muted">-</span>';
    }
    $row[] = $contactHtml;

    // [3] Status (Subscription Status)
    // Find latest recurring invoice
    $sub_query = "SELECT * FROM " . db_prefix() . "invoices WHERE clientid = " . $aRow['userid'] . " AND recurring > 0 ORDER BY date DESC LIMIT 1";
    $sub = $this->ci->db->query($sub_query)->row();
    $statusHtml = '';
    if ($sub) {
        $status = format_invoice_status($sub->status, '', true);
        $statusHtml = $status;
    } else {
        $statusHtml = '<span class="label label-default">Inactive</span>';
    }
    $row[] = $statusHtml;

    // [4] Doctor
    $doctorHtml = '-';
    if (!empty($aRow['next_doctor_id'])) {
        $doctor = $this->ci->staff_model->get($aRow['next_doctor_id']);
        if ($doctor) {
            $doctorHtml = $doctor->firstname . ' ' . $doctor->lastname;
        }
    }
    $row[] = $doctorHtml;

    // [5] Next Visit
    $nextVisitHtml = '-';
    if (!empty($aRow['next_visit_date'])) {
        $nextVisitHtml = _d($aRow['next_visit_date']);
        // Add relative tag if needed (omitted for brevity unless requested)
    }
    $row[] = $nextVisitHtml;

    // [6] Renewal Status
    $renewalStatusHtml = '-';
    if ($sub) {
        // recurring_type, recurring
        // e.g., recurring=1, recurring_type=month -> Monthly
        $type = $sub->recurring_type;
        $val = $sub->recurring;
        if ($val == 0) {
            $renewalStatusHtml = 'Once'; // Shouldn't happen with our filter
        } else {
            if ($type == 'month') {
                $renewalStatusHtml = ($val == 1 ? 'Monthly' : 'Every ' . $val . ' Months');
            } elseif ($type == 'year') {
                $renewalStatusHtml = ($val == 1 ? 'Yearly' : 'Every ' . $val . ' Years');
            } elseif ($type == 'day') {
                $renewalStatusHtml = ($val == 1 ? 'Daily' : 'Every ' . $val . ' Days');
            } elseif ($type == 'week') {
                $renewalStatusHtml = ($val == 1 ? 'Weekly' : 'Every ' . $val . ' Weeks');
            } else {
                $renewalStatusHtml = 'Custom';
            }
        }
    }
    $row[] = $renewalStatusHtml;

    // [7] Renewal Start & End Date
    $renewalDatesHtml = '-';
    if ($sub) {
        // Start date is invoice date
        $start = _d($sub->date);
        // End date calculation based on cycles? Or just next renewal? 
        // Perfex invoices usually have 'recurring_ends_on' (optional) or we calculate next date?
        // Let's just show Start Date / Next Renewal Date? 
        // User asked "Renewal Start & End Date". 
        // "End Date" for infinite subscriptions is null. 
        // Let's check 'recurring_ends_on'
        $end = ($sub->recurring_ends_on && $sub->recurring_ends_on > '1970-01-01') ? _d($sub->recurring_ends_on) : 'Forever'; // Or Indefinite

        $renewalDatesHtml = '<span class="text-success">Start: ' . $start . '</span><br />';
        $renewalDatesHtml .= '<span class="text-danger">End: ' . $end . '</span>';
    }
    $row[] = $renewalDatesHtml;

    // [8] Total Subscriptions Count
    $row[] = '<span class="badge badge-primary">' . $aRow['total_subs'] . '</span>';

    $output['aaData'][] = $row;
}
