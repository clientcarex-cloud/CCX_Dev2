<table class="ccx-rpt-table">
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
</table>