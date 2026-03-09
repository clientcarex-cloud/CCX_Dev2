<table class="ccx-rpt-table">
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
</table>