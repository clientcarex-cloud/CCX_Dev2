<table class="ccx-rpt-table">
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
                    <?php echo $r['name'] ?: '-'; ?>
                </td>
                <td>
                    <?php echo $r['phonenumber'] ?: '-'; ?>
                </td>
                <td>
                    <?php echo $r['email'] ?: '-'; ?>
                </td>
                <td>
                    <?php echo isset($r['staff_name']) && $r['staff_name'] ? $r['staff_name'] : '<span style="color:#9ca3af;">Unassigned</span>'; ?>
                </td>
                <td>
                    <?php if (isset($r['status_name']) && $r['status_name']) { ?>
                        <span class="status-badge"
                            style="background:<?php echo isset($r['status_color']) && $r['status_color'] ? $r['status_color'] : '#6b7280'; ?>;">
                            <?php echo $r['status_name']; ?>
                        </span>
                    <?php } else {
                        echo '-';
                    } ?>
                </td>
                <td>
                    <?php echo isset($r['dateadded']) && $r['dateadded'] ? date('d M Y H:i', strtotime($r['dateadded'])) : '-'; ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>