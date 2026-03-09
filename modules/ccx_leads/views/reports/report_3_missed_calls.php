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
</table>