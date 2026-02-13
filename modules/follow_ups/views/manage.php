<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('follow_ups', '', 'create')) { ?>
                            <!-- <div class="_buttons">
                                    <a href="#" class="btn btn-info pull-left display-block"><?php echo _l('new_follow_up'); ?></a>
                                </div> 
                                <div class="clearfix"></div>
                                <hr class="hr-panel-heading" /> -->
                        <?php } ?>
                        <!-- <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" /> -->

                        <!-- Filter Bar -->
                        <div class="row mbot15">
                            <div class="col-md-12">
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">

                                    <!-- Status Tabs -->
                                    <div style="flex: 1; min-width: 0; overflow-x: auto;">
                                        <div
                                            style="display: flex; background-color: #f1f5f9; padding: 5px; border-radius: 25px; align-items: center; white-space: nowrap; width: fit-content; min-width: 100%;">
                                            <!-- All Tab -->
                                            <a href="<?php echo admin_url('follow_ups'); ?>"
                                                style="text-decoration: none; color: #333; padding: 6px 15px; border-radius: 20px; font-weight: 600; font-size: 13px; margin-right: 5px; white-space: nowrap; <?php echo ($selected_status == '') ? 'background-color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : ''; ?>">
                                                All
                                            </a>

                                            <?php foreach ($statuses as $status) {
                                                $count = isset($status_counts[$status['id']]) ? $status_counts[$status['id']] : 0;
                                                $isActive = ($selected_status == $status['id']);
                                                // Check visibility if needed, or if controller filtered it already? 
                                                // Controller uses get_statuses() which doesn't filter by default in index(), let's trust the loop.
                                                // Optional: Check filter_visible if implemented in future.
                                                ?>
                                                <a href="<?php echo admin_url('follow_ups?status_id=' . $status['id']); ?>"
                                                    style="text-decoration: none; color: #475569; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; margin-right: 5px; white-space: nowrap; <?php echo $isActive ? 'background-color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); color: #333;' : ''; ?>">
                                                    <?php echo $status['name']; ?>
                                                    <span
                                                        style="background-color: <?php echo $status['color']; ?>; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 8px; font-weight: 700;">
                                                        <?php echo $count; ?>
                                                    </span>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- Date Filters -->
                                    <div class="text-right" style="flex-shrink: 0;">
                                        <?php echo form_open(admin_url('follow_ups'), ['method' => 'GET', 'style' => 'display:inline-flex; align-items: center;']); ?>

                                        <?php if ($selected_status != '') { ?>
                                            <input type="hidden" name="status_id" value="<?php echo $selected_status; ?>">
                                        <?php } ?>

                                        <div class="input-group" style="width: 150px; margin-right: 5px;">
                                            <input type="text" name="from_date" class="form-control datepicker"
                                                value="<?php echo $from_date; ?>" placeholder="From Date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar-plus-o"></i>
                                            </div>
                                        </div>
                                        <div class="input-group" style="width: 150px; margin-right: 5px;">
                                            <input type="text" name="to_date" class="form-control datepicker"
                                                value="<?php echo $to_date; ?>" placeholder="To Date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar-plus-o"></i>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-info">Filter</button>
                                        <?php echo form_close(); ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <div class="table-responsive mtop15">
                            <table class="table dt-table" data-order-col="4" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th>Name & Last Visit</th>
                                        <th>Mr. No & Visits Count</th>
                                        <th>Last Contact</th>
                                        <th>Status</th>
                                        <th>Next Visit</th>
                                        <th>Doctor</th>
                                        <th>Prescription</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($follow_ups as $row) {
                                        $last_visit_date = _d($row['last_visit']);
                                        $next_visit_date = _d($row['next_visit_date']);
                                        $visits_count = $row['visits_count'];

                                        // Calculate Next Visit stats for data attributes (needed for Name link)
                                        $nextVisitObj = new DateTime($row['next_visit_date']);
                                        $nowObj = new DateTime();
                                        $nowObj->setTime(0, 0, 0);
                                        $nextVisitObj->setTime(0, 0, 0);
                                        $next_interval = $nowObj->diff($nextVisitObj);
                                        $next_days = $next_interval->days;
                                        $next_invert = $next_interval->invert;

                                        // Status Logic
                                        if (isset($row['status_name']) && !empty($row['status_name'])) {
                                            $status_label = '<span class="label" style="background-color: ' . $row['status_color'] . '; color: #fff; border: 1px solid ' . $row['status_color'] . '">' . $row['status_name'] . '</span>';
                                        } else {
                                            // Dynamic Default from controller
                                            $status_label = '<span class="label" style="background-color: ' . $new_status_default['color'] . '; color: #fff; border: 1px solid ' . $new_status_default['color'] . '">' . $new_status_default['name'] . '</span>';
                                        }

                                        ?>
                                        <tr>
                                            <td>
                                                <a href="#" data-title="<?php echo $row['patient_title']; ?>"
                                                    data-name="<?php echo $row['patient_name']; ?>"
                                                    data-gender="<?php echo $row['gender']; ?>"
                                                    data-mr="<?php echo $row['mr_number']; ?>"
                                                    data-next="<?php echo $next_visit_date; ?>"
                                                    data-days="<?php echo $next_days; ?>"
                                                    data-invert="<?php echo $next_invert; ?>"
                                                    data-patient-id="<?php echo $row['patient_id']; ?>"
                                                    onclick="open_action_modal(this, <?php echo $row['prescription_id']; ?>); return false;">
                                                    <?php echo (isset($row['patient_title']) && !empty($row['patient_title']) ? $row['patient_title'] . ' ' : '') . $row['patient_name']; ?>
                                                </a>
                                                <br />
                                                <span class="text-muted small">
                                                    <?php echo $last_visit_date; ?>
                                                    <?php
                                                    $visitDateTime = new DateTime($row['last_visit']);
                                                    $currentDateTime = new DateTime();
                                                    $interval = $currentDateTime->diff($visitDateTime);
                                                    $days = $interval->days;

                                                    if ($days == 0) {
                                                        echo ' <span class="label label-success">Today</span>';
                                                    } elseif ($days == 1) {
                                                        echo ' <span class="label label-info">Yesterday</span>';
                                                    } else {
                                                        echo ' <span class="label label-default">' . $days . ' days ago</span>';
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $row['mr_number']; ?>
                                                <br />
                                                <span class="badge badge-info">
                                                    <?php echo $visits_count; ?> Visits
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                if (!empty($row['last_note_content'])) {
                                                    echo '<span style="font-size: 13px; color: #333; display: block; margin-bottom: 2px;">' . mb_substr(strip_tags($row['last_note_content']), 0, 50) . (mb_strlen(strip_tags($row['last_note_content'])) > 50 ? '...' : '') . '</span>';

                                                    // Calculate time ago
                                                    $noteDate = new DateTime($row['last_note_date']);
                                                    $now = new DateTime();
                                                    $interval = $now->diff($noteDate);

                                                    $timeAgo = '';
                                                    if ($interval->y > 0) {
                                                        $timeAgo = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                                                    } elseif ($interval->m > 0) {
                                                        $timeAgo = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                                                    } elseif ($interval->d > 0) {
                                                        $timeAgo = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                                                    } elseif ($interval->h > 0) {
                                                        $timeAgo = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                                                    } elseif ($interval->i > 0) {
                                                        $timeAgo = $interval->i . ' min' . ($interval->i > 1 ? 's' : '') . ' ago';
                                                    } else {
                                                        $timeAgo = 'Just now';
                                                    }

                                                    echo '<span class="text-muted small">' . _dt($row['last_note_date']) . '</span>';
                                                    echo ' <span class="label label-default" style="font-size: 85%;">' . $timeAgo . '</span>';
                                                } else {
                                                    echo '<span class="text-muted">-</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $status_label; ?>
                                            </td>
                                            <td>
                                                <?php echo $next_visit_date; ?>
                                                <br />
                                                <span class="text-muted small">
                                                    <?php
                                                    $nextVisit = new DateTime($row['next_visit_date']);
                                                    $now = new DateTime();
                                                    $now->setTime(0, 0, 0); // Reset time to compare dates only
                                                    $nextVisit->setTime(0, 0, 0);
                                                    $interval = $now->diff($nextVisit);
                                                    $days = $interval->days;
                                                    $invert = $interval->invert; // 1 if past
                                                
                                                    if ($days == 0) {
                                                        echo '<span class="label label-danger">Today only!</span>';
                                                    } elseif ($invert) {
                                                        echo '<span class="label label-danger">-' . $days . ' Day' . ($days > 1 ? 's' : '') . '</span>';
                                                    } else {
                                                        // Future
                                                        if ($days == 7) {
                                                            echo '<span class="label label-info">1 week</span>';
                                                        } elseif ($days < 30) {
                                                            echo '<span class="label label-info">' . $days . ' days left</span>';
                                                        } elseif ($days < 365) {
                                                            $months = round($days / 30);
                                                            echo '<span class="label label-default">' . $months . ' Month' . ($months > 1 ? 's' : '') . '</span>';
                                                        } else {
                                                            $years = round($days / 365);
                                                            echo '<span class="label label-default">' . $years . ' Year' . ($years > 1 ? 's' : '') . '</span>';
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $row['doctor_name']; ?>
                                            </td>
                                            <td>
                                                <a href="#"
                                                    onclick="view_prescription('<?php echo admin_url('prescription/view/' . $row['prescription_id']); ?>'); return false;"
                                                    class="btn btn-default btn-xs">View Rx</a>
                                            </td>


                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('patient_master_modal/modal_content'); ?>


<a href="<?php echo admin_url('follow_ups/settings'); ?>" class="btn btn-info btn-icon"
    style="position: fixed; bottom: 20px; right: 20px; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); z-index: 9999;">
    <i class="fa fa-cog" style="font-size: 24px;"></i>
</a>

<?php init_tail(); ?>
<script src="<?php echo module_dir_url('patient_master_modal', 'assets/js/script.js'); ?>"></script>