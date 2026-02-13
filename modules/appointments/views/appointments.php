<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <div class="clearfix"></div>

                        <!-- Quick Stats Cards -->
                        <div class="row mbot15">
                            <style>
                                .stats-card {
                                    background: #fff;
                                    border-radius: 12px;
                                    padding: 12px 20px;
                                    margin-bottom: 15px;
                                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.03);
                                    position: relative;
                                    overflow: hidden;
                                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                    border: 1px solid #f3f4f6;
                                }

                                .stats-card:hover {
                                    transform: translateY(-3px);
                                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                                    border-color: transparent;
                                }

                                .stats-card-icon {
                                    position: absolute;
                                    top: 50%;
                                    transform: translateY(-50%) rotate(0deg);
                                    right: 20px;
                                    font-size: 2.2rem;
                                    opacity: 0.15;
                                    transition: transform 0.3s ease;
                                }

                                .stats-card:hover .stats-card-icon {
                                    transform: translateY(-50%) rotate(-15deg) scale(1.1);
                                    opacity: 0.25;
                                }

                                .stats-card-title {
                                    font-size: 0.8rem;
                                    font-weight: 600;
                                    color: #64748b;
                                    text-transform: uppercase;
                                    letter-spacing: 1px;
                                    margin-bottom: 2px;
                                    z-index: 1;
                                    position: relative;
                                }

                                .stats-card-value {
                                    font-size: 1.8rem;
                                    font-weight: 800;
                                    color: #111827;
                                    z-index: 1;
                                    position: relative;
                                    line-height: 1.2;
                                }

                                /* Card Color Variants - Using Left Border & Subtle Background Tint on Hover? No, just keep clean white with border accents */
                                .stats-card.primary {
                                    border-left: 4px solid #3b82f6;
                                }

                                .stats-card.success {
                                    border-left: 4px solid #10b981;
                                }

                                .stats-card.info {
                                    border-left: 4px solid #0ea5e9;
                                }

                                .stats-card.warning {
                                    border-left: 4px solid #f59e0b;
                                }

                                .stats-card.danger {
                                    border-left: 4px solid #ef4444;
                                }

                                .stats-card.missed {
                                    border-left: 4px solid #6366f1;
                                }

                                .stats-card.primary .stats-card-icon {
                                    color: #3b82f6;
                                }

                                .stats-card.success .stats-card-icon {
                                    color: #10b981;
                                }

                                .stats-card.info .stats-card-icon {
                                    color: #0ea5e9;
                                }

                                .stats-card.warning .stats-card-icon {
                                    color: #f59e0b;
                                }

                                .stats-card.danger .stats-card-icon {
                                    color: #ef4444;
                                }

                                .stats-card.missed .stats-card-icon {
                                    color: #6366f1;
                                }
                            </style>

                            <div class="col-md-2 col-xs-6"> <!-- 5 cards, using col-md-2.4 sort of, but col-md-2 works if we center or fill. Let's use 5 columns via custom width or just standard grid. Bootstrap 3 assumes 12 cols. 12/5 is not integer. Let's do col-md-2 and offset? No, let's use a 5-col utility or just fit them nicely. Let's use col-md-2 and make the first one larger or just wrap? 
                            Actually, let's stick to standard grid. 
                            Total, Confirmed, Visited, Pending, Cancelled. 5 items.
                            Col-md-2 * 5 = 10. We have 2 left. We can make the 'Total' slightly larger or use col-md-offset-1.
                            -->
                                <div class="stats-card primary">
                                    <div class="stats-card-title"><?php echo _l('total_appointments'); ?></div>
                                    <div class="stats-card-value"><?php echo $stats['total']; ?></div>
                                    <i class="fa fa-calendar stats-card-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <div class="stats-card success">
                                    <div class="stats-card-title"><?php echo _l('visited'); ?></div>
                                    <div class="stats-card-value"><?php echo $stats['visited']; ?></div>
                                    <i class="fa fa-check-circle stats-card-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <div class="stats-card info">
                                    <div class="stats-card-title"><?php echo _l('confirmed'); ?></div>
                                    <div class="stats-card-value"><?php echo $stats['confirmed']; ?></div>
                                    <i class="fa fa-thumbs-up stats-card-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <div class="stats-card warning">
                                    <div class="stats-card-title"><?php echo _l('pending'); ?></div>
                                    <div class="stats-card-value"><?php echo $stats['pending']; ?></div>
                                    <i class="fa fa-clock-o stats-card-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <div class="stats-card danger">
                                    <div class="stats-card-title"><?php echo _l('cancelled'); ?></div>
                                    <div class="stats-card-value"><?php echo $stats['cancelled']; ?></div>
                                    <i class="fa fa-times-circle stats-card-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <div class="stats-card missed">
                                    <div class="stats-card-title"><?php echo _l('missed'); ?></div>
                                    <div class="stats-card-value"><?php echo $stats['missed']; ?></div>
                                    <i class="fa fa-minus-circle stats-card-icon"></i>
                                </div>
                            </div>
                            <!-- Optional: Completed if needed, but Visited is the main 'done' state usually for appointments in this system -->
                            <!-- If we have 2 cols space left, we could add 'Completed' if logic distinguishes, but user asked for these core statuses usually. Let's check logic: I added 'completed' to stats array initially. -->
                            <?php if (isset($stats['completed']) && $stats['completed'] > 0) { ?>
                                <div class="col-md-2 col-xs-6">
                                    <div class="stats-card success">
                                        <div class="stats-card-title"><?php echo _l('completed'); ?></div>
                                        <div class="stats-card-value"><?php echo $stats['completed']; ?></div>
                                        <i class="fa fa-flag-checkered stats-card-icon"></i>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Filter Form -->
                        <form method="GET" action="<?php echo admin_url('appointments'); ?>">
                            <div class="row">
                                <div class="col-md-2">
                                    <?php if (has_permission('appointments', '', 'create')) { ?>
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;</label>
                                            <a href="<?php echo admin_url('appointments/appointment'); ?>"
                                                class="btn btn-info btn-block"><?php echo _l('new_appointment'); ?></a>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="doctor_id" class="control-label"><?php echo _l('doctor'); ?></label>
                                        <select name="doctor_id" id="doctor_id" class="selectpicker" data-width="100%"
                                            data-live-search="true">
                                            <option value="">All</option>
                                            <?php
                                            // Get doctors directly from model or pass from controller more cleanly.
                                            // Controller doesn't pass 'doctors' to index() yet?
                                            // Ah, it does not. We need to fetch doctors or load them.
                                            // Let's rely on standard way or inject them. 
                                            // For now, let's use the same method model uses if not passed.
                                            // Ideally controller should pass it. Let's fix controller first? 
                                            // Wait, I can't fix controller in this tool call. 
                                            // I'll grab them via CI instance for now to be safe, or just assume controller will pass them.
                                            // Wait, I am editing View. Code below assumes $doctors variable exists. 
                                            // I should verify if controller passes it. Controller passed $data which includes appointments.
                                            // Controller index() does NOT pass doctors list.
                                            // I will fix controller to pass doctors list in next step or use direct call here.
                                            // Direct call is easier for now to avoid rapid context switching issues.
                                            $CI =& get_instance();
                                            $CI->load->model('appointments/appointments_model');
                                            $doctors = $CI->appointments_model->get_doctors();
                                            foreach ($doctors as $doctor) {
                                                $selected = (isset($filters['doctor_id']) && $filters['doctor_id'] == $doctor['staffid']) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $doctor['staffid']; ?>" <?php echo $selected; ?>>
                                                    <?php echo $doctor['firstname'] . ' ' . $doctor['lastname']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date" class="control-label"><?php echo _l('date'); ?></label>
                                        <input type="text" class="form-control datepicker" name="date" id="date"
                                            value="<?php echo isset($filters['date']) ? $filters['date'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status" class="control-label"><?php echo _l('status'); ?></label>
                                        <select name="status" id="status" class="selectpicker" data-width="100%">
                                            <option value="">All</option>
                                            <?php
                                            $statuses = ['pending', 'confirmed', 'visited', 'cancelled', 'completed', 'missed'];
                                            foreach ($statuses as $status) {
                                                $selected = (isset($filters['status']) && $filters['status'] == $status) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $status; ?>" <?php echo $selected; ?>>
                                                    <?php echo ucfirst($status); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label>
                                        <div class="btn-group btn-group-justified">
                                            <div class="btn-group">
                                                <button type="submit" class="btn btn-info">Filter</button>
                                            </div>
                                            <div class="btn-group">
                                                <a href="<?php echo admin_url('appointments'); ?>"
                                                    class="btn btn-default">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="0" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('id'); ?></th>
                                        <th><?php echo _l('patient'); ?></th>
                                        <th><?php echo _l('doctor'); ?></th>
                                        <th><?php echo _l('date'); ?> & <?php echo _l('time'); ?></th>
                                        <th><?php echo _l('time_left'); ?></th>
                                        <th><?php echo _l('status'); ?></th>
                                        <th><?php echo _l('visit'); ?></th>
                                        <th><?php echo _l('registered_date_time'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment) { ?>
                                        <tr>
                                            <td><?php echo $appointment['id']; ?></td>
                                            <td>
                                                <?php if ($appointment['type'] == 'client') { ?>
                                                    <a href="#"
                                                        onclick="view_patient_modal(<?php echo $appointment['patient_id']; ?>, 'client'); return false;">
                                                        <?php echo $appointment['patient_name']; ?>
                                                    </a>
                                                    <?php if (!empty($appointment['mr_no'])) { ?>
                                                        <br><span class="text-muted"><?php echo $appointment['mr_no']; ?></span>
                                                    <?php } ?>
                                                <?php } elseif ($appointment['type'] == 'guest') { ?>
                                                    <a href="#"
                                                        onclick="view_patient_modal(<?php echo $appointment['guest_id']; ?>, 'guest'); return false;">
                                                        <?php echo $appointment['patient_name']; ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <?php echo $appointment['patient_name']; ?>
                                                <?php } ?>
                                                <?php if ($appointment['type'] == 'guest') { ?>
                                                    <span class="label label-info">New</span>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $appointment['doctor_name']; ?></td>
                                            <td>
                                                <?php echo _d($appointment['appointment_date']); ?><br>
                                                <span
                                                    class="text-muted"><?php echo date('h:i A', strtotime($appointment['start_time'])) . ' - ' . date('h:i A', strtotime($appointment['end_time'])); ?></span>
                                                <?php if (isset($appointment['appointment_type']) && $appointment['appointment_type'] == 'Paid') {
                                                    $invoice_status = isset($appointment['invoice_status']) ? $appointment['invoice_status'] : null;
                                                    $invoice_total = isset($appointment['invoice_total']) ? $appointment['invoice_total'] : 0;
                                                    $total_payment = isset($appointment['total_payment']) ? $appointment['total_payment'] : 0;
                                                    $due_amount = $invoice_total - $total_payment;

                                                    if ($invoice_status == 2) { // Paid
                                                        echo '<br><span class="label label-success">Paid</span>';
                                                    } elseif ($invoice_status == 3 || ($invoice_status == 1 && $total_payment > 0)) { // Partial (3 is standard, checking payment just in case)
                                                        echo '<br><span class="label label-warning">Partial</span>';
                                                        echo '<br><small class="text-danger">Due: ' . app_format_money($due_amount, get_base_currency()) . '</small>';
                                                    } elseif ($invoice_status == 1) { // Unpaid
                                                        echo '<br><span class="label label-danger">Unpaid</span>';
                                                        echo '<br><small class="text-danger">Due: ' . app_format_money($due_amount, get_base_currency()) . '</small>';
                                                    } else {
                                                        // Fallback for old data or no linked invoice yet
                                                        echo '<br><span class="label label-success">Paid</span>';
                                                    }
                                                } ?>
                                            </td>
                                            <td>
                                                <?php
                                                $appt_time = strtotime($appointment['appointment_date'] . ' ' . $appointment['start_time']);
                                                $now = time();
                                                $diff = $appt_time - $now;

                                                if ($diff > 0) {
                                                    $days = floor($diff / (60 * 60 * 24));
                                                    $hours = floor(($diff % (60 * 60 * 24)) / (60 * 60));
                                                    $minutes = floor(($diff % (60 * 60)) / 60);

                                                    $time_left = '';
                                                    if ($days > 0)
                                                        $time_left .= $days . ' ' . _l('time_days') . ' ';
                                                    if ($hours > 0)
                                                        $time_left .= $hours . ' ' . _l('time_hours') . ' ';
                                                    if ($minutes > 0)
                                                        $time_left .= $minutes . ' ' . _l('time_minutes');

                                                    echo '<span class="label label-info">' . trim($time_left) . '</span>';
                                                } else {
                                                    echo '<span class="label label-danger">' . _l('passed') . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = 'default';
                                                if ($appointment['status'] == 'confirmed')
                                                    $status_class = 'success';
                                                elseif ($appointment['status'] == 'cancelled')
                                                    $status_class = 'danger';
                                                elseif ($appointment['status'] == 'completed')
                                                    $status_class = 'info';
                                                elseif ($appointment['status'] == 'visited')
                                                    $status_class = 'success'; // Visited is also a positive status
                                                elseif ($appointment['status'] == 'pending')
                                                    $status_class = 'warning';
                                                ?>
                                                <span
                                                    class="label label-<?php echo $status_class; ?>"><?php echo ucfirst($appointment['status']); ?></span>
                                            </td>
                                            <td>
                                                <?php if ($appointment['status'] == 'visited') { ?>
                                                    <span class="text-success" data-toggle="tooltip" title="Confirmed At">
                                                        <?php echo _dt($appointment['visit_confirmed_at']); ?>
                                                    </span>
                                                    <br>
                                                    <?php
                                                    $appt_start = strtotime($appointment['appointment_date'] . ' ' . $appointment['start_time']);
                                                    $visit_time = strtotime($appointment['visit_confirmed_at']);
                                                    $diff_seconds = $visit_time - $appt_start;

                                                    $is_late = $diff_seconds > 0;
                                                    $abs_diff = abs($diff_seconds);

                                                    $days = floor($abs_diff / (60 * 60 * 24));
                                                    $hours = floor(($abs_diff % (60 * 60 * 24)) / (60 * 60));
                                                    $minutes = floor(($abs_diff % (60 * 60)) / 60);

                                                    $diff_text = '';
                                                    if ($days > 0)
                                                        $diff_text .= $days . ' ' . _l('time_days') . ' ';
                                                    if ($hours > 0)
                                                        $diff_text .= $hours . ' ' . _l('time_hours') . ' ';
                                                    if ($minutes > 0)
                                                        $diff_text .= $minutes . ' ' . _l('time_minutes');
                                                    $diff_text = trim($diff_text);
                                                    if (empty($diff_text))
                                                        $diff_text = _l('on_time');

                                                    if ($is_late) {
                                                        echo '<span class="label label-danger">' . _l('late_by') . ' ' . $diff_text . '</span>';
                                                    } else {
                                                        echo '<span class="label label-success">' . _l('early_by') . ' ' . $diff_text . '</span>';
                                                    }
                                                    ?>
                                                <?php } else {
                                                    $is_today = $appointment['appointment_date'] == date('Y-m-d');
                                                    if ($is_today) { ?>
                                                        <a href="<?php echo admin_url('appointments/confirm_visit/' . $appointment['id']); ?>"
                                                            class="btn btn-success btn-icon" data-toggle="tooltip"
                                                            title="<?php echo _l('confirm_visit'); ?>">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    <?php } else { ?>
                                                        <button type="button" class="btn btn-default btn-icon" disabled
                                                            data-toggle="tooltip" title="Can only confirm on appointment date">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span data-toggle="tooltip"
                                                    title="<?php echo _dt($appointment['created_at']); ?>">
                                                    <?php echo _dt($appointment['created_at']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($appointment['has_history']) { ?>
                                                    <a href="#"
                                                        onclick="view_reschedule_history(<?php echo $appointment['id']; ?>); return false;"
                                                        class="btn btn-default btn-icon" data-toggle="tooltip"
                                                        title="<?php echo _l('reschedule_history'); ?>">
                                                        <i class="fa fa-history"></i>
                                                    </a>
                                                <?php } ?>

                                                <?php if (has_permission('patients', '', 'edit') && $appointment['status'] != 'visited') { ?>
                                                    <a href="<?php echo admin_url('appointments/appointment/' . $appointment['id']); ?>"
                                                        class="btn btn-default" data-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-pencil-square-o"></i> Edit
                                                    </a>
                                                <?php } ?>
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
<div id="patient_modal_wrapper"></div>
<div id="reschedule_history_modal_wrapper"></div>

<?php if (is_admin()) { ?>
    <a href="<?php echo admin_url('appointments/settings'); ?>" class="btn btn-info"
        style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
        <i class="fa fa-cogs" style="font-size: 20px;"></i>
    </a>
<?php } ?>

<?php init_tail(); ?>
<script>
    function view_patient_modal(id, type) {
        $.post(admin_url + 'appointments/get_patient_details_ajax', {
            id: id,
            type: type
        }).done(function (response) {
            $('#patient_modal_wrapper').html(response);
            $('#patient_details_modal').modal('show');
        });
    }

    function view_reschedule_history(id) {
        $.post(admin_url + 'appointments/get_reschedule_history_ajax', {
            id: id
        }).done(function (response) {
            $('#reschedule_history_modal_wrapper').html(response);
            $('#reschedule_history_modal').modal('show');
        });
    }
</script>
</body>
</html>