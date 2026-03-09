<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .ccx-rpt-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .ccx-rpt-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 18px;
        color: #1f2937;
    }

    .ccx-rpt-filters {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 22px;
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 14px;
    }

    .ccx-rpt-fg {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .ccx-rpt-fg label {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 0;
    }

    .ccx-rpt-fg input,
    .ccx-rpt-fg select {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 13px;
        min-width: 160px;
        color: #374151;
        background: #fff;
    }

    .ccx-rpt-fg input:focus,
    .ccx-rpt-fg select:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, .15);
    }

    .ccx-rpt-apply {
        background: #3b82f6;
        color: #fff;
        border: none;
        padding: 7px 18px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 13px;
        cursor: pointer;
    }

    .ccx-rpt-apply:hover {
        background: #2563eb;
    }

    .ccx-rpt-reset {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
        padding: 7px 14px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
    }

    .ccx-rpt-reset:hover {
        background: #e5e7eb;
        color: #111827;
        text-decoration: none;
    }

    .ccx-rpt-summary {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .ccx-rpt-badge {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 18px;
        text-align: center;
        min-width: 110px;
    }

    .ccx-rpt-badge .num {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        display: block;
    }

    .ccx-rpt-badge .lbl {
        font-size: 11px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .ccx-rpt-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .ccx-rpt-table th {
        background: #f9fafb;
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .ccx-rpt-table td {
        padding: 9px 12px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
    }

    .ccx-rpt-table tr:hover td {
        background: #f9fafb;
    }

    .ccx-rpt-table .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
    }

    .ccx-rpt-table .conversion-yes {
        color: #059669;
        font-weight: 600;
    }

    .ccx-rpt-table .conversion-no {
        color: #9ca3af;
    }

    .ccx-rpt-no-data {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
        font-size: 14px;
    }

    .ccx-rpt-tat-good {
        color: #059669;
        font-weight: 600;
    }

    .ccx-rpt-tat-avg {
        color: #d97706;
        font-weight: 600;
    }

    .ccx-rpt-tat-bad {
        color: #dc2626;
        font-weight: 600;
    }

    .ccx-rpt-tat-na {
        color: #9ca3af;
    }

    .ccx-rpt-status-bar {
        display: inline-flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .ccx-rpt-status-chip {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
        color: #fff;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <!-- Header -->
                        <div class="ccx-rpt-header">
                            <h4><i class="fa fa-chart-bar" style="color:#3b82f6;"></i>
                                <?php echo $report_name; ?>
                            </h4>
                            <a href="<?php echo admin_url('ccx_leads/reports'); ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to Reports
                            </a>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="<?php echo admin_url('ccx_leads/report_view/' . $report_id); ?>"
                            class="ccx-rpt-filters">
                            <div class="ccx-rpt-fg">
                                <label>Date From</label>
                                <input type="date" name="date_from" value="<?php echo $date_from; ?>" />
                            </div>
                            <div class="ccx-rpt-fg">
                                <label>Date To</label>
                                <input type="date" name="date_to" value="<?php echo $date_to; ?>" />
                            </div>
                            <div class="ccx-rpt-fg">
                                <label>Staff</label>
                                <select name="staff_id">
                                    <option value="">All Staff</option>
                                    <?php foreach ($members as $m) { ?>
                                        <option value="<?php echo $m['staffid']; ?>" <?php echo ($staff_id == $m['staffid']) ? 'selected' : ''; ?>>
                                            <?php echo $m['firstname'] . ' ' . $m['lastname']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <button type="submit" class="ccx-rpt-apply"><i class="fa fa-filter"></i> Apply</button>
                            <a href="<?php echo admin_url('ccx_leads/report_view/' . $report_id); ?>"
                                class="ccx-rpt-reset">Reset</a>
                        </form>

                        <!-- Summary badge -->
                        <div class="ccx-rpt-summary">
                            <div class="ccx-rpt-badge">
                                <span class="num">
                                    <?php echo count($report_data); ?>
                                </span>
                                <span class="lbl">Total Records</span>
                            </div>
                            <?php if (!empty($date_from) || !empty($date_to)) { ?>
                                <div class="ccx-rpt-badge">
                                    <span class="num" style="font-size:13px; color:#3b82f6;">
                                        <?php echo $date_from ?: '∞'; ?> →
                                        <?php echo $date_to ?: '∞'; ?>
                                    </span>
                                    <span class="lbl">Date Range</span>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Report Table -->
                        <?php if (empty($report_data)) { ?>
                            <div class="ccx-rpt-no-data"><i class="fa fa-inbox fa-2x"></i><br>No data found for the selected
                                filters.</div>
                        <?php } else { ?>
                            <div style="overflow-x:auto;">
                                <table class="ccx-rpt-table">

                                    <?php if ($report_id == 1) { // Overall Leads Assigned ?>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Staff Name</th>
                                                <th>Total Leads Assigned</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            foreach ($report_data as $r) { ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['staff_name']; ?>
                                                    </td>
                                                    <td><strong>
                                                            <?php echo $r['total_assigned']; ?>
                                                        </strong></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    <?php } elseif ($report_id == 2) { // Staff New Leads & Calls ?>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Staff Name</th>
                                                <th>New Leads</th>
                                                <th>Call Logs</th>
                                                <th>Total Activity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            foreach ($report_data as $r) { ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['staff_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['new_leads']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['call_logs']; ?>
                                                    </td>
                                                    <td><strong>
                                                            <?php echo $r['total']; ?>
                                                        </strong></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    <?php } elseif ($report_id == 3) { // Missed Leads Calls ?>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Lead ID</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Assigned To</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            foreach ($report_data as $r) { ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['id']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['phonenumber']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['email']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['staff_name'] ?: '<span style="color:#9ca3af;">Unassigned</span>'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['status_name'] ?: '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['dateadded'] ? date('d M Y H:i', strtotime($r['dateadded'])) : '-'; ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    <?php } elseif ($report_id == 4) { // Staff Wise Call TAT ?>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Lead ID</th>
                                                <th>Lead Name</th>
                                                <th>Phone</th>
                                                <th>Assigned To</th>
                                                <th>Lead Created</th>
                                                <th>First Call</th>
                                                <th>TAT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            foreach ($report_data as $r) {
                                                $tat = $r['tat_minutes'];
                                                if ($tat === null || $r['first_call'] === null) {
                                                    $tat_display = '<span class="ccx-rpt-tat-na">No Call</span>';
                                                } elseif ($tat <= 30) {
                                                    $tat_display = '<span class="ccx-rpt-tat-good">' . $tat . ' min</span>';
                                                } elseif ($tat <= 120) {
                                                    $h = floor($tat / 60);
                                                    $m = $tat % 60;
                                                    $tat_display = '<span class="ccx-rpt-tat-avg">' . ($h > 0 ? $h . 'h ' : '') . $m . 'min</span>';
                                                } else {
                                                    $h = floor($tat / 60);
                                                    $m = $tat % 60;
                                                    if ($h >= 24) {
                                                        $d = floor($h / 24);
                                                        $h = $h % 24;
                                                        $tat_display = '<span class="ccx-rpt-tat-bad">' . $d . 'd ' . $h . 'h ' . $m . 'm</span>';
                                                    } else {
                                                        $tat_display = '<span class="ccx-rpt-tat-bad">' . $h . 'h ' . $m . 'm</span>';
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['id']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['phonenumber']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['staff_name'] ?: '<span style="color:#9ca3af;">Unassigned</span>'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d M Y H:i', strtotime($r['lead_created'])); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['first_call'] ? date('d M Y H:i', strtotime($r['first_call'])) : '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $tat_display; ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    <?php } elseif ($report_id == 5) { // Leads Follow-ups ?>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Lead</th>
                                                <th>Phone</th>
                                                <th>Reminder</th>
                                                <th>Date</th>
                                                <th>Assigned To</th>
                                                <th>Created By</th>
                                                <th>Status</th>
                                                <th>Notified</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            foreach ($report_data as $r) { ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['lead_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['phonenumber']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['reminder_desc']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d M Y H:i', strtotime($r['reminder_date'])); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['staff_name'] ?: '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['created_by'] ?: '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['lead_status'] ?: '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['isnotified'] ? '<span style="color:#059669;">✓ Yes</span>' : '<span style="color:#d97706;">Pending</span>'; ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    <?php } elseif ($report_id == 6) { // Overview of Leads by Staff ?>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Staff Name</th>
                                                <th>Total Leads</th>
                                                <th>Contacted</th>
                                                <th>Not Contacted</th>
                                                <th>Junk</th>
                                                <th>Lost</th>
                                                <th>Status Breakdown</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            foreach ($report_data as $r) { ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['staff_name']; ?>
                                                    </td>
                                                    <td><strong>
                                                            <?php echo $r['total_leads']; ?>
                                                        </strong></td>
                                                    <td style="color:#059669;">
                                                        <?php echo $r['contacted']; ?>
                                                    </td>
                                                    <td style="color:#dc2626;">
                                                        <?php echo $r['not_contacted']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['junk']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['lost']; ?>
                                                    </td>
                                                    <td>
                                                        <div class="ccx-rpt-status-bar">
                                                            <?php foreach ($r['statuses'] as $st) { ?>
                                                                <span class="ccx-rpt-status-chip"
                                                                    style="background:<?php echo $st['color'] ?: '#6b7280'; ?>;">
                                                                    <?php echo $st['name']; ?>:
                                                                    <?php echo $st['count']; ?>
                                                                </span>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    <?php } elseif ($report_id == 7) { // Complete Overview ?>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Company</th>
                                                <th>Source</th>
                                                <th>Assigned</th>
                                                <th>Status</th>
                                                <th>Value</th>
                                                <th>Last Contact</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            foreach ($report_data as $r) {
                                                $st_label = '';
                                                if ($r['junk'] == 1)
                                                    $st_label = '<span class="status-badge" style="background:#9CA3AF;">Junk</span>';
                                                elseif ($r['lost'] == 1)
                                                    $st_label = '<span class="status-badge" style="background:#EF4444;">Lost</span>';
                                                elseif ($r['status_name'])
                                                    $st_label = '<span class="status-badge" style="background:' . ($r['status_color'] ?: '#6b7280') . ';">' . $r['status_name'] . '</span>';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['id']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['email']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['phonenumber']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['company']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['source_name'] ?: '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['staff_name'] ?: '<span style="color:#9ca3af;">Unassigned</span>'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $st_label; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['lead_value'] ? number_format($r['lead_value'], 2) : '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo ($r['lastcontact'] && $r['lastcontact'] != '0000-00-00 00:00:00') ? date('d M Y H:i', strtotime($r['lastcontact'])) : '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d M Y H:i', strtotime($r['dateadded'])); ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    <?php } elseif ($report_id == 8 || $report_id == 9) { // Week / 2-Week Leads & Conversion ?>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Source</th>
                                                <th>Assigned</th>
                                                <th>Status</th>
                                                <th>Contacted</th>
                                                <th>Converted</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            foreach ($report_data as $r) {
                                                $st_label = '';
                                                if ($r['junk'] == 1)
                                                    $st_label = '<span class="status-badge" style="background:#9CA3AF;">Junk</span>';
                                                elseif ($r['lost'] == 1)
                                                    $st_label = '<span class="status-badge" style="background:#EF4444;">Lost</span>';
                                                elseif ($r['status_name'])
                                                    $st_label = '<span class="status-badge" style="background:' . ($r['status_color'] ?: '#6b7280') . ';">' . $r['status_name'] . '</span>';

                                                $contacted = ($r['lastcontact'] && $r['lastcontact'] != '0000-00-00 00:00:00') ? '<span style="color:#059669;">✓ Yes</span>' : '<span style="color:#dc2626;">✗ No</span>';
                                                $converted = !empty($r['client_id']) ? '<span class="conversion-yes">✓ Converted</span>' : '<span class="conversion-no">Not Converted</span>';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['id']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['phonenumber']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['source_name'] ?: '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $r['staff_name'] ?: '<span style="color:#9ca3af;">Unassigned</span>'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $st_label; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $contacted; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $converted; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d M Y H:i', strtotime($r['dateadded'])); ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    <?php } elseif ($report_id == 10) { // Overall Leads Statuses ?>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Status</th>
                                                <th>Color</th>
                                                <th>Total Leads</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $grand_total = array_sum(array_column($report_data, 'total'));
                                            $i = 1;
                                            foreach ($report_data as $r) {
                                                $pct = $grand_total > 0 ? round(($r['total'] / $grand_total) * 100, 1) : 0;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge"
                                                            style="background:<?php echo $r['color'] ?: '#6b7280'; ?>;">
                                                            <?php echo $r['status_name']; ?>
                                                        </span>
                                                    </td>
                                                    <td><span
                                                            style="display:inline-block;width:14px;height:14px;border-radius:3px;background:<?php echo $r['color'] ?: '#6b7280'; ?>;vertical-align:middle;"></span>
                                                    </td>
                                                    <td><strong>
                                                            <?php echo $r['total']; ?>
                                                        </strong></td>
                                                    <td>
                                                        <div style="display:flex;align-items:center;gap:8px;">
                                                            <div
                                                                style="flex:1;max-width:200px;height:8px;background:#f3f4f6;border-radius:4px;overflow:hidden;">
                                                                <div
                                                                    style="width:<?php echo $pct; ?>%;height:100%;background:<?php echo $r['color'] ?: '#6b7280'; ?>;border-radius:4px;">
                                                                </div>
                                                            </div>
                                                            <span>
                                                                <?php echo $pct; ?>%
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($grand_total > 0) { ?>
                                                <tr style="font-weight:700;background:#f9fafb;">
                                                    <td colspan="3" style="text-align:right;">Grand Total</td>
                                                    <td>
                                                        <?php echo $grand_total; ?>
                                                    </td>
                                                    <td>100%</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    <?php } ?>

                                </table>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>

</html>