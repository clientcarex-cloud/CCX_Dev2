<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .case_taking-tabs-container {
        background-color: #f1f5f9;
        padding: 5px;
        border-radius: 25px;
        display: inline-flex;
    }

    .case_taking-tab {
        padding: 8px 20px;
        border-radius: 20px;
        color: #475569;
        font-weight: 500;
        text-decoration: none !important;
        margin-right: 5px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
    }

    .case_taking-tab:last-child {
        margin-right: 0;
    }

    .case_taking-tab.active {
        background-color: #ffffff;
        color: #0f172a;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .case_taking-tab:hover {
        color: #1e293b;
    }

    .case_taking-badge {
        margin-left: 8px;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        color: #fff;
    }

    .bg-red {
        background-color: #ef4444;
    }

    .bg-blue {
        background-color: #3b82f6;
    }

    .bg-green {
        background-color: #22c55e;
    }

    .bg-gray {
        background-color: #94a3b8;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Tabs -->
                                <div class="case_taking-tabs-container">
                                    <a href="<?php echo admin_url('case_taking?status=all'); ?>"
                                        class="case_taking-tab <?php echo ($filters['status'] == 'all' ? 'active' : ''); ?>">
                                        All
                                    </a>

                                    <a href="<?php echo admin_url('case_taking?status=Processing'); ?>"
                                        class="case_taking-tab <?php echo ($filters['status'] == 'Processing' ? 'active' : ''); ?>">
                                        Processing
                                        <?php if (isset($status_counts['Processing']) && $status_counts['Processing'] > 0) { ?>
                                            <span
                                                class="case_taking-badge bg-blue"><?php echo $status_counts['Processing']; ?></span>
                                        <?php } ?>
                                    </a>

                                    <a href="<?php echo admin_url('case_taking?status=Emergency'); ?>"
                                        class="case_taking-tab <?php echo ($filters['status'] == 'Emergency' ? 'active' : ''); ?>">
                                        Emergency
                                        <?php if (isset($status_counts['Emergency']) && $status_counts['Emergency'] > 0) { ?>
                                            <span
                                                class="case_taking-badge bg-red"><?php echo $status_counts['Emergency']; ?></span>
                                        <?php } ?>
                                    </a>

                                    <a href="<?php echo admin_url('case_taking?status=Completed'); ?>"
                                        class="case_taking-tab <?php echo ($filters['status'] == 'Completed' ? 'active' : ''); ?>">
                                        Completed
                                        <?php if (isset($status_counts['Completed']) && $status_counts['Completed'] > 0) { ?>
                                            <span
                                                class="case_taking-badge bg-green"><?php echo $status_counts['Completed']; ?></span>
                                        <?php } ?>
                                    </a>

                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <!-- Filters -->
                                <form action="<?php echo admin_url('case_taking'); ?>" method="GET" class="form-inline"
                                    style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                                    <input type="hidden" name="status" value="<?php echo $filters['status']; ?>">
                                    <?php if (get_option('case_taking_show_date_filter') == '1' || get_option('case_taking_show_date_filter') === '') { ?>
                                        <div class="form-group" style="margin-bottom: 0;">
                                            <?php echo render_date_input('from_date', '', $filters['from_date'], ['placeholder' => 'From Date', 'style' => 'width:150px;']); ?>
                                        </div>
                                        <div class="form-group" style="margin-bottom: 0;">
                                            <?php echo render_date_input('to_date', '', $filters['to_date'], ['placeholder' => 'To Date', 'style' => 'width:150px;']); ?>
                                        </div>
                                    <?php } ?>
                                    <button type="submit" class="btn btn-info" style="margin-top: 0;">Filter</button>
                                </form>
                            </div>
                        </div>



                        <hr class="hr-panel-heading" />

                        <div class="table-responsive">
                            <table class="table table-bordered dt-table" data-order-col="0" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th>Sno.</th>
                                        <th>Patients</th>
                                        <th>Age/Gender</th>
                                        <th>Patient IDs</th>
                                        <th>Doctor</th>
                                        <th>Consultation</th>
                                        <th>Status</th>
                                        <th>Consultation Time</th>
                                        <?php $mode = get_option('case_taking_mode_type'); ?>
                                        <?php if ($mode == 'digital' || $mode == '') { ?>
                                            <th>Digital Rx</th>
                                        <?php } ?>
                                        <?php if ($mode == 'manual') { ?>
                                            <th>Manual Rx</th>
                                        <?php } ?>
                                        <?php if (get_option('case_taking_show_options_column') == '1' || get_option('case_taking_show_options_column') === '') { ?>
                                            <th>Options</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($consultations as $key => $row) { ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?></td>
                                            <td>
                                                <a
                                                    href="<?php echo admin_url('patients/patient/' . $row['patient_id']); ?>">
                                                    <strong><?php echo $row['patient_name']; ?></strong>
                                                </a>
                                                <br />
                                                <small class="text-muted"><?php echo _dt($row['visit_date']); ?></small>
                                            </td>
                                            <td>
                                                <?php echo $row['age']; ?>     <?php echo $row['age_unit']; ?>
                                                <br />
                                                <?php echo $row['gender']; ?>
                                            </td>
                                            <td>
                                                <strong>MR NO:</strong> <?php echo $row['mr_number']; ?>
                                                <br />
                                                <strong>VISIT ID:</strong> <?php echo $row['visit_code']; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo $row['doctor_name']; ?></strong>
                                            </td>
                                            <td>
                                                <strong><?php echo $row['consultation_name']; ?></strong>
                                                <br />
                                                <small class="text-muted"><?php echo _dt($row['visit_date']); ?></small>
                                            </td>
                                            <td>
                                                <span
                                                    class="label label-<?php echo ($row['status'] == 'Completed' ? 'success' : ($row['status'] == 'Pending' ? 'warning' : 'default')); ?>">
                                                    <?php echo $row['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $duration = isset($row['consultation_duration']) ? intval($row['consultation_duration']) : 0;
                                                echo gmdate("H:i:s", $duration);
                                                ?>
                                            </td>
                                            <?php if ($mode == 'digital' || $mode == '') { ?>
                                                <td class="text-center">
                                                    <?php if (!empty($row['case_taking_id'])) {
                                                        $can_edit = true;
                                                        if ($row['status'] == 'Completed') {
                                                            $completed_date = !empty($row['case_taking_completed_date']) ? $row['case_taking_completed_date'] : $row['case_taking_date'];

                                                            // Get Timeout Setting (Minutes to Seconds)
                                                            $timeout_min = get_option('case_taking_edit_timeout');
                                                            if (empty($timeout_min))
                                                                $timeout_min = 5; // Default 5 mins
                                                            $timeout_sec = $timeout_min * 60;

                                                            if (time() - strtotime($completed_date) > $timeout_sec) {
                                                                $can_edit = false;
                                                            }
                                                        }

                                                        if ($can_edit) {
                                                            ?>
                                                            <a href="<?php echo admin_url('case_taking/edit/' . $row['case_taking_id']); ?>"
                                                                class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>
                                                        <?php } else { ?>
                                                            <button type="button" class="btn btn-default btn-icon" disabled
                                                                title="Edit disabled after 2 minutes"><i
                                                                    class="fa fa-pencil"></i></button>
                                                        <?php } ?>

                                                        <a href="<?php echo admin_url('case_taking/view/' . $row['case_taking_id']); ?>"
                                                            class="btn btn-default btn-icon" title="View Case Taking"><i
                                                                class="fa fa-eye"></i></a>
                                                        <?php
                                                    } else { ?>
                                                        <a href="<?php echo admin_url('case_taking/create/' . $row['id']); ?>"
                                                            class="btn btn-default btn-icon"><i class="fa fa-plus-square"></i></a>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>

                                            <?php if ($mode == 'manual') { ?>
                                                <td class="text-center">
                                                    <?php if ($row['status'] != 'Completed') { ?>
                                                        <button type="button" class="btn btn-info btn-manual-rx"
                                                            data-id="<?php echo $row['id']; ?>">Manual Rx</button>
                                                    <?php } else { ?>
                                                        <button type="button" class="btn btn-success" disabled>Completed</button>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <?php if (get_option('case_taking_show_options_column') == '1' || get_option('case_taking_show_options_column') === '') { ?>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                            data-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <?php if (!empty($row['case_taking_id'])) { ?>
                                                                <li><a href="#" class="action-recreate"
                                                                        data-id="<?php echo $row['case_taking_id']; ?>">Re-Create</a>
                                                                </li>
                                                            <?php } ?>
                                                            <?php if ($row['status'] != 'Cancel') { ?>
                                                                <li><a href="<?php echo admin_url('case_taking/cancel/' . $row['id']); ?>"
                                                                        class="action-cancel"
                                                                        data-id="<?php echo $row['id']; ?>">Cancel this!</a>
                                                                </li>
                                                            <?php } ?>

                                                            <?php if ($row['status'] == 'Cancel') { ?>
                                                                <li><a href="<?php echo admin_url('case_taking/uncancel/' . $row['id']); ?>"
                                                                        class="action-uncancel"
                                                                        data-id="<?php echo $row['id']; ?>">un-cancell</a>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </td>
                                            <?php } ?>

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
</div>
<a href="<?php echo admin_url('case_taking/settings'); ?>" class="btn btn-info btn-icon" title="Settings"
    style="position: fixed; bottom: 30px; right: 30px; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999; font-size: 24px;">
    <i class="fa fa-cog"></i>
</a>
<?php init_tail(); ?>
<script>
    $(function () {
        // Manual Rx Click
        $('.btn-manual-rx').on('click', function () {
            var btn = $(this);
            var id = btn.data('id');

            if (confirm('Are you sure you want to mark this as Completed?')) {
                $.post(admin_url + 'case_taking/change_status/' + id + '/Completed', function (response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        alert_float('success', 'Status Updated Successfully');
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert_float('warning', 'Failed to update status');
                    }
                });
            }
        });

        // Re-Create Click
        $('.action-recreate').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this case_taking and create a new one? This action cannot be undone.')) {
                window.location.href = admin_url + 'case_taking/recreate/' + id;
            }
        });
    });
</script>