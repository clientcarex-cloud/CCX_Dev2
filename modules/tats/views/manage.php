<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php if (empty($groups)) { ?>
                            <p class="text-center">No data found.</p>
                        <?php } else { ?>

                            <?php foreach ($groups as $group_name => $tests) { ?>
                                <div class="mtop20">
                                    <h4 class="text-info bold"><i class="fa fa-layer-group"></i> <?php echo $group_name; ?></h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover dt-table" data-order-col="0"
                                            data-order-type="asc">
                                            <thead>
                                                <tr>
                                                    <th>Test Name</th>
                                                    <th>Status History & TAT</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tests as $test) { ?>
                                                    <tr>
                                                        <td width="30%"><strong><?php echo $test['item_name']; ?></strong></td>
                                                        <td>
                                                            <ul class="list-unstyled">
                                                                <?php
                                                                $prev_time = null;
                                                                $first = true;
                                                                foreach ($test['history'] as $h) {
                                                                    $curr_time = strtotime($h['created_at']);
                                                                    $duration_display = '';

                                                                    if (!$first && $prev_time) {
                                                                        $diff = $curr_time - $prev_time;

                                                                        // Calculate readable duration
                                                                        $days = floor($diff / (60 * 60 * 24));
                                                                        $hours = floor(($diff % (60 * 60 * 24)) / (60 * 60));
                                                                        $minutes = floor(($diff % (60 * 60)) / 60);
                                                                        $seconds = $diff % 60;

                                                                        $duration_parts = [];
                                                                        if ($days > 0)
                                                                            $duration_parts[] = $days . 'd';
                                                                        if ($hours > 0)
                                                                            $duration_parts[] = $hours . 'h';
                                                                        if ($minutes > 0)
                                                                            $duration_parts[] = $minutes . 'm';
                                                                        // if($seconds > 0) $duration_parts[] = $seconds . 's'; // Optional detail
                                                    
                                                                        if (empty($duration_parts))
                                                                            $duration_parts[] = '< 1m';

                                                                        $duration_str = implode(' ', $duration_parts);
                                                                        $duration_display = " <span class='label label-default mleft5' data-toggle='tooltip' title='Time since last status'>Took {$duration_str}</span>";
                                                                    }

                                                                    $prev_time = $curr_time;
                                                                    $first = false;

                                                                    $status_label = '<span class="label label-info">' . $h['status'] . '</span>';
                                                                    // Determine color based on status (optional, matching Perfex styles)
                                                                    // if($h['status'] == 'Completed') $status_label = '<span class="label label-success">' . $h['status'] . '</span>';
                                                    
                                                                    echo "<li style='margin-bottom: 5px;'>
                                                                            <i class='fa fa-circle-o text-muted mright5'></i>
                                                                            {$status_label} 
                                                                            <span class='text-muted'>by " . ($h['staff_name'] ? $h['staff_name'] : 'System') . "</span> 
                                                                            <span class='text-has-action' data-toggle='tooltip' title='" . _dt($h['created_at']) . "'>at " . time_ago($h['created_at']) . "</span> 
                                                                            {$duration_display}
                                                                          </li>";
                                                                }
                                                                ?>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>

                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>