<table class="ccx-rpt-table">
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
                    <?php echo isset($r['staff_name']) ? $r['staff_name'] : '-'; ?>
                </td>
                <td><strong>
                        <?php echo isset($r['total_leads']) ? $r['total_leads'] : 0; ?>
                    </strong></td>
                <td style="color:#059669;">
                    <?php echo isset($r['contacted']) ? $r['contacted'] : 0; ?>
                </td>
                <td style="color:#dc2626;">
                    <?php echo isset($r['not_contacted']) ? $r['not_contacted'] : 0; ?>
                </td>
                <td>
                    <?php echo isset($r['junk']) ? $r['junk'] : 0; ?>
                </td>
                <td>
                    <?php echo isset($r['lost']) ? $r['lost'] : 0; ?>
                </td>
                <td>
                    <?php if (isset($r['statuses']) && is_array($r['statuses']) && count($r['statuses']) > 0) { ?>
                        <div class="ccx-rpt-status-bar">
                            <?php foreach ($r['statuses'] as $st) { ?>
                                <span class="ccx-rpt-status-chip"
                                    style="background:<?php echo isset($st['color']) && $st['color'] ? $st['color'] : '#6b7280'; ?>;">
                                    <?php echo isset($st['name']) ? $st['name'] : '?'; ?>:
                                    <?php echo isset($st['count']) ? $st['count'] : 0; ?>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } else {
                        echo '<span style="color:#9ca3af;">—</span>';
                    } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>