<table class="ccx-rpt-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Lead</th>
            <th>Phone</th>
            <th>Reminder</th>
            <th>Date</th>
            <th>Assigned To</th>
            <th>Created By</th>
            <th>Lead Status</th>
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
</table>