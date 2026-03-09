<table class="ccx-rpt-table">
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
</table>