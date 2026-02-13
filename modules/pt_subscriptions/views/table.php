<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'clients.userid as patient_id',
    db_prefix() . 'clients.company as patient_name',
    db_prefix() . 'name_titles.name as patient_title',
    db_prefix() . 'patients_extra.mr_number as mr_number',
    db_prefix() . 'patients_extra.gender as gender',
    // Subqueries for basic columns
    '(SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM ' . db_prefix() . 'customers_groups JOIN ' . db_prefix() . 'customer_groups ON ' . db_prefix() . 'customer_groups.groupid = ' . db_prefix() . 'customers_groups.id WHERE customer_id=' . db_prefix() . 'clients.userid) as customer_groups'
];

$sIndexColumn = 'userid';
$sTable = db_prefix() . 'clients';

$join = [
    'LEFT JOIN ' . db_prefix() . 'patients_extra ON ' . db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'clients.userid',
    'LEFT JOIN ' . db_prefix() . 'name_titles ON ' . db_prefix() . 'name_titles.id = ' . db_prefix() . 'patients_extra.title_id'
];

// Define SQL snippets for reuse in WHERE clause (DataTables doesn't support Alias in WHERE)
$total_subs_sql = '(SELECT COUNT(*) FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1)';
$latest_sub_status_sql = '(SELECT status FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1 ORDER BY date DESC LIMIT 1)';
$latest_sub_state_sql = '(SELECT pt_subscription_status FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1 ORDER BY date DESC LIMIT 1)';
$latest_sub_date_sql = '(SELECT date FROM ' . db_prefix() . 'invoices WHERE clientid=' . db_prefix() . 'clients.userid AND is_pt_subscription=1 ORDER BY date DESC LIMIT 1)';

$additionalSelect = [
    '(SELECT description FROM ' . db_prefix() . 'notes WHERE rel_type="customer" AND rel_id=' . db_prefix() . 'clients.userid ORDER BY dateadded DESC LIMIT 1) as last_note_content',
    '(SELECT dateadded FROM ' . db_prefix() . 'notes WHERE rel_type="customer" AND rel_id=' . db_prefix() . 'clients.userid ORDER BY dateadded DESC LIMIT 1) as last_note_date',
    '(SELECT COUNT(*) FROM ' . db_prefix() . 'visits WHERE patient_id=' . db_prefix() . 'clients.userid) as visits_count',
    '(SELECT datecreated FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid ORDER BY datecreated DESC LIMIT 1) as last_visit_date',
    '(SELECT id FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid ORDER BY datecreated DESC LIMIT 1) as prescription_id',
    '(SELECT next_visit_date FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid AND next_visit_date >= CURDATE() ORDER BY next_visit_date ASC LIMIT 1) as next_visit_date',
    '(SELECT staff_id FROM ' . db_prefix() . 'prescriptions WHERE patient_id=' . db_prefix() . 'clients.userid AND next_visit_date >= CURDATE() ORDER BY next_visit_date ASC LIMIT 1) as next_doctor_id',
    // Subscription Aggregates
    $latest_sub_status_sql . ' as latest_sub_status',
    $latest_sub_state_sql . ' as subscription_state',
    $latest_sub_date_sql . ' as latest_sub_date',
    $total_subs_sql . ' as total_subs'
];

// Build WHERE clause based on filters
$where = [];

// Filter: Only patients with at least 1 subscription
$where[] = 'AND ' . $total_subs_sql . ' > 0';

$CI =& get_instance();
$status = $CI->input->post('status');
$from = $CI->input->post('from');
$to = $CI->input->post('to');

// Filter: Status
if ($status != '' && $status != 'all') {
    if ($status == 'active' || $status == 'inactive' || $status == 'cancelled') {
        $where[] = 'AND ' . $latest_sub_state_sql . ' = "' . $status . '"';
    } else {
        $where[] = 'AND ' . $latest_sub_status_sql . ' = "' . $status . '"';
    }
}

// Filter: Date Range (Applied to latest_sub_date)
if ($from != '') {
    $where[] = 'AND ' . $latest_sub_date_sql . ' >= "' . to_sql_date($from) . '"';
}
if ($to != '') {
    $where[] = 'AND ' . $latest_sub_date_sql . ' <= "' . to_sql_date($to) . '"';
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output = $result['output'];
$rResult = $result['rResult'];

// Fix start variable if undefined
$start = $CI->input->post('start');
if (!is_numeric($start)) {
    $start = 0;
}
$i = $start + 1;

$CI->load->model('staff_model');
$CI->load->model('pt_subscriptions_model');

foreach ($rResult as $aRow) {
    $row = [];

    // 1. Name & Last Visit
    $last_visit_date = !empty($aRow['last_visit_date']) ? _d($aRow['last_visit_date']) : '-';

    // Relative Time for Last Visit
    $lastVisitTag = '';
    if (!empty($aRow['last_visit_date'])) {
        $visitDateTime = new DateTime($aRow['last_visit_date']);
        $currentDateTime = new DateTime();
        $interval = $currentDateTime->diff($visitDateTime);
        $days = $interval->days;

        if ($days == 0) {
            $lastVisitTag = '<span class="label label-success">Today</span>';
        } elseif ($days == 1) {
            $lastVisitTag = '<span class="label label-info">Yesterday</span>';
        } else {
            $lastVisitTag = '<span class="label label-default">' . $days . ' days ago</span>';
        }
    }

    // Next Visit Calc
    $next_days = null;
    $next_invert = null;
    if (!empty($aRow['next_visit_date'])) {
        $nextVisitObj = new DateTime($aRow['next_visit_date']);
        $nowObj = new DateTime();
        $nowObj->setTime(0, 0, 0);
        $nextVisitObj->setTime(0, 0, 0);
        $next_interval = $nowObj->diff($nextVisitObj);
        $next_days = $next_interval->days;
        $next_invert = $next_interval->invert;
    }

    $nameHtml = '<a href="#" data-title="' . html_escape($aRow['patient_title']) . '"
        data-name="' . html_escape($aRow['patient_name']) . '"
        data-gender="' . html_escape($aRow['gender']) . '"
        data-mr="' . html_escape($aRow['mr_number']) . '"
        data-next="' . (!empty($aRow['next_visit_date']) ? _d($aRow['next_visit_date']) : '') . '"
        data-days="' . $next_days . '"
        data-invert="' . $next_invert . '"
        data-patient-id="' . $aRow['patient_id'] . '"
        onclick="open_action_modal(this, \'' . $aRow['prescription_id'] . '\'); return false;">' .
        (isset($aRow['patient_title']) ? $aRow['patient_title'] . ' ' : '') . $aRow['patient_name'] .
        '</a><br />' .
        '<span class="text-muted small">' . $last_visit_date . ' ' . $lastVisitTag . '</span>';
    $row[] = $nameHtml;

    // 2. Mr. No & Visits Count
    $mrHtml = $aRow['mr_number'] . '<br /><span class="badge badge-info">' . $aRow['visits_count'] . ' Visits</span>';
    $row[] = $mrHtml;

    // 3. Last Contact
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

    // 4. Status
    // Fetch full subscription data efficiently? 
    // We already have latest_sub_status and subscription_state from subqueries.
    // However, to get the detailed recurring info (type, value, ends_on) we might still need a query or more select subqueries.
    // For now, let's just do the lightweight query per row as in the original code, 
    // BUT since we are server-side, this N+1 is slightly less painful but still not ideal. 
    // Better to add more subqueries or joins if possible. 
    // Given the complexity of the invoice table structure, let's stick to the model helper for the complex details 
    // to match exact original functionality, optimizing later if needed.

    $sub = $CI->pt_subscriptions_model->get_subscription_data($aRow['patient_id']);

    $statusHtml = '<span class="label label-default">Inactive</span>';
    $subscriptionStatusHtml = '';

    if (isset($sub->pt_subscription_status)) {
        $sState = $sub->pt_subscription_status;
        $sLabelClass = 'default';
        if ($sState == 'active')
            $sLabelClass = 'success';
        elseif ($sState == 'inactive')
            $sLabelClass = 'warning';
        elseif ($sState == 'cancelled')
            $sLabelClass = 'danger';

        $subscriptionStatusHtml = '<span class="label label-' . $sLabelClass . '">' . _l($sState, ucfirst($sState)) . '</span> ';
    }

    if ($sub) {
        $statusHtml = format_invoice_status($sub->status, '', true);
    }

    $row[] = $subscriptionStatusHtml;
    $row[] = $statusHtml;

    // 5. Doctor
    $doctorHtml = '-';
    // Prioritize Subscription Doctor (Sale Agent)
    if ($sub && !empty($sub->sale_agent) && $sub->sale_agent != 0) {
        $doctor = $CI->staff_model->get($sub->sale_agent);
        if ($doctor) {
            $doctorHtml = $doctor->firstname . ' ' . $doctor->lastname;
        }
    } elseif (!empty($aRow['next_doctor_id'])) {
        // Fallback to Next Visit Doctor if no subscription doctor (legacy support or just info)
        $doctor = $CI->staff_model->get($aRow['next_doctor_id']);
        if ($doctor) {
            $doctorHtml = $doctor->firstname . ' ' . $doctor->lastname . ' <span class="text-muted small">(Visit)</span>';
        }
    }
    $row[] = $doctorHtml;

    // 6. Next Visit
    $row[] = !empty($aRow['next_visit_date']) ? _d($aRow['next_visit_date']) : '-';

    // 7. Renewal Status
    $renewalStatusHtml = '-';
    if ($sub) {
        $type = $sub->recurring_type;
        $val = $sub->recurring;
        if ($val != 0) {
            if ($type == 'month') {
                $renewalStatusHtml = ($val == 1 ? 'Monthly' : 'Every ' . $val . ' Months');
            } elseif ($type == 'year') {
                $renewalStatusHtml = ($val == 1 ? 'Yearly' : 'Every ' . $val . ' Years');
            } elseif ($type == 'day') {
                $renewalStatusHtml = ($val == 1 ? 'Daily' : 'Every ' . $val . ' Days');
            } elseif ($type == 'week') {
                $renewalStatusHtml = ($val == 1 ? 'Weekly' : 'Every ' . $val . ' Weeks');
            }

            // Calculate Next Renewal
            try {
                // Check if finished by cycles
                // aRow['total_subs'] is the count of generated invoices so far
                $is_finished = false;
                if (!empty($sub->cycles) && $sub->cycles > 0 && $aRow['total_subs'] >= $sub->cycles) {
                    $is_finished = true;
                }

                if (!$is_finished && !empty($sub->date) && $sub->date != '0000-00-00' && $sub->pt_subscription_status == 'active') {
                    $last_date = new DateTime($sub->date);
                    $modify_str = "+ " . $val . " " . $type; // e.g. +1 month
                    $next_date = clone $last_date;
                    $next_date->modify($modify_str);

                    $now = new DateTime();
                    $now->setTime(0, 0, 0); // normalize
                    $next_date_compare = clone $next_date;
                    $next_date_compare->setTime(0, 0, 0);

                    $diff = $now->diff($next_date_compare);
                    $days_diff = $diff->days;
                    $is_past = $diff->invert; // 1 if past

                    $days_text = '';
                    $class = 'info';

                    if ($is_past) {
                        $days_text = $days_diff . ' days overdue';
                        $class = 'danger';
                    } else {
                        if ($days_diff == 0) {
                            $days_text = 'Today';
                            $class = 'success';
                        } elseif ($days_diff == 1) {
                            $days_text = 'Tomorrow';
                            $class = 'warning';
                        } else {
                            $days_text = $days_diff . ' days left';
                            if ($days_diff <= 3)
                                $class = 'warning';
                        }
                    }

                    $renewalStatusHtml .= '<br /><small class="text-' . $class . '">Next: ' . _d($next_date->format('Y-m-d')) . ' (' . $days_text . ')</small>';
                } elseif ($is_finished) {
                    $renewalStatusHtml .= '<br /><small class="text-success">Completed</small>';
                }
            } catch (Exception $e) {
            }
        }
    }
    $row[] = $renewalStatusHtml;

    // 8. Renewal Start & End Date
    $renewalDatesHtml = '-';
    if ($sub) {
        $start_d = _d($sub->date);
        $end_d = 'Forever';
        $cycles_text = '';

        // Check if there is a fixed number of cycles
        if (isset($sub->cycles) && $sub->cycles > 0) {
            $end_d = '-';
            try {
                if (!empty($sub->date) && $sub->date != '0000-00-00') {
                    $original_date = new DateTime($sub->date);
                    $cycles_to_add = $sub->cycles - 1;

                    if ($cycles_to_add > 0 && !empty($sub->recurring_type) && is_numeric($sub->recurring)) {
                        $rec_type = strtolower($sub->recurring_type);
                        if (in_array($rec_type, ['day', 'days', 'week', 'weeks', 'month', 'months', 'year', 'years'])) {
                            $interval_str = "+ " . ($sub->recurring * $cycles_to_add) . " " . $rec_type;
                            $original_date->modify($interval_str);
                        }
                    }
                    $end_d = _d($original_date->format('Y-m-d'));
                    $cycles_text = '<br /><span class="text-muted small">(Total ' . $sub->cycles . ' Cycles)</span>';
                }
            } catch (Exception $e) {
            }
        } elseif (isset($sub->recurring_ends_on) && $sub->recurring_ends_on && $sub->recurring_ends_on > '1970-01-01') {
            $end_d = _d($sub->recurring_ends_on);
        }

        $renewalDatesHtml = '<span class="text-success">Start: ' . $start_d . '</span><br /><span class="text-danger">End: ' . $end_d . '</span>' . $cycles_text;
    }
    $row[] = $renewalDatesHtml;

    // 9. Total Subscriptions Count
    $row[] = '<span class="badge badge-primary">' . $aRow['total_subs'] . '</span>';

    // 10. Actions
    $actionsHtml = '';
    if ($sub) {
        $actionsHtml .= '<a href="#" class="btn btn-default btn-icon" onclick="edit_subscription(' . $sub->id . '); return false;"><i class="fa fa-pencil"></i></a>';

        $status = $sub->pt_subscription_status ?? 'active';
    }
    $row[] = $actionsHtml;

    $output['aaData'][] = $row;
}

echo json_encode($output);
die;
