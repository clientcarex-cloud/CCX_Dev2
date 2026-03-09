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
                            <span class="ccx-rpt-status-chip" style="background:<?php echo $st['color'] ?: '#6b7280'; ?>;">
                                <?php echo $st['name']; ?>:
                                <?php echo $st['count']; ?>
                            </span>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>