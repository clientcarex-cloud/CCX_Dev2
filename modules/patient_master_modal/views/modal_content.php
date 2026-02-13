<?php defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Ensure 'members' variable for Reminders tab
if (!isset($members)) {
    $CI->load->model('staff_model');
    $members = $CI->staff_model->get('', ['active' => 1]);
}

// Ensure 'statuses' variable for Call Logs tab
if (!isset($statuses)) {
    $CI->load->model('follow_ups/follow_ups_model');
    $statuses = $CI->follow_ups_model->get_statuses();
}
?>

<!-- Modal for Action -->
<div class="modal fade" id="action_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 90%; height: 90%;">
        <div class="modal-content"
            style="height: 100%; border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.2); display: flex; flex-direction: column;">

            <!-- Header -->
            <div class="modal-header"
                style="background: #fff; border-bottom: 1px solid #e5e7eb; padding: 15px 20px; border-radius: 12px 12px 0 0; display: flex; align-items: center; justify-content: space-between;">
                <h4 class="modal-title"
                    style="font-weight: 400; color: #1e293b; font-size: 16px; margin: 0; text-align: left; flex: 1;">
                    <span id="action_modal_title"></span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="float: none !important; font-size: 24px; line-height: 1; font-weight: 300; opacity: 1; color: #64748b; margin: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body (Tabs + Content) -->
            <div class="modal-body"
                style="padding: 20px 25px; background: #f8fafc; flex: 1; overflow: hidden; display: flex; flex-direction: column;">

                <!-- Navigation Tabs -->
                <div
                    style="background: #eff6ff; padding: 8px; border-radius: 12px; margin-bottom: 20px; white-space: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <ul class="nav nav-pills custom-action-tabs" role="tablist"
                        style="display: inline-flex; width: 100%; gap: 5px;">
                        <li role="presentation" class="active">
                            <a href="#tab_overview" aria-controls="tab_overview" role="tab" data-toggle="tab">
                                <i class="fa fa-user"></i> Overview
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_reminders" aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                <i class="fa fa-bell"></i> Reminders
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_prescriptions" aria-controls="tab_prescriptions" role="tab" data-toggle="tab">
                                <i class="fa fa-file-text"></i> Prescriptions
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_notes" aria-controls="tab_notes" role="tab" data-toggle="tab">
                                <i class="fa fa-phone"></i> Call Logs
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_appointments" aria-controls="tab_appointments" role="tab" data-toggle="tab">
                                <i class="fa fa-calendar"></i> Appointments
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_subscriptions" aria-controls="tab_subscriptions" role="tab" data-toggle="tab">
                                <i class="fa fa-refresh"></i> Pt. Subscriptions
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_invoices" aria-controls="tab_invoices" role="tab" data-toggle="tab">
                                <i class="fa fa-credit-card"></i> Invoices & Payments
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_expenses" aria-controls="tab_expenses" role="tab" data-toggle="tab">
                                <i class="fa fa-usd"></i> Expenses
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                                <i class="fa fa-tasks"></i> Tasks
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_tickets" aria-controls="tab_tickets" role="tab" data-toggle="tab">
                                <i class="fa fa-ticket"></i> Tickets
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_files" aria-controls="tab_files" role="tab" data-toggle="tab">
                                <i class="fa fa-folder-open"></i> Files
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Tab Panes -->
                <div class="tab-content"
                    style="background: #fff; border-radius: 12px; padding: 25px; flex: 1; overflow-y: auto; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <div role="tabpanel" class="tab-pane active" id="tab_overview">
                        <h4 class="text-muted text-center mtop30">Hello Overview</h4>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_reminders">
                        <!-- Add Reminder Form -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="reminder_date">
                                        <?php echo _l('reminder_date'); ?>
                                    </label>
                                    <div class="input-group date">
                                        <input type="text" id="reminder_date" name="date"
                                            class="form-control datetimepicker"
                                            value="<?php echo _d(date('Y-m-d H:i')); ?>">
                                        <div class="input-group-addon">
                                            <i class="calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="reminder_staff">
                                        <?php echo _l('reminder_set_to'); ?>
                                    </label>
                                    <select name="staff" id="reminder_staff" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php foreach ($members as $member) { ?>
                                            <option value="<?php echo $member['staffid']; ?>" <?php if ($member['staffid'] == get_staff_user_id()) {
                                                   echo 'selected';
                                               } ?>>
                                                <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="reminder_description">
                                        <?php echo _l('reminder_description'); ?>
                                    </label>
                                    <textarea id="reminder_description" name="description" class="form-control"
                                        rows="4"></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="notify_by_email" id="reminder_notify_by_email">
                                        <label for="reminder_notify_by_email">
                                            <?php echo _l('reminder_notify_me_by_email'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <button type="button" class="btn btn-info" onclick="save_reminder()">
                                        <?php echo _l('set_reminder_title'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <!-- Reminders Table -->
                        <?php
                        $table_data = [
                            _l('reminder_description'),
                            _l('reminder_date'),
                            _l('reminder_staff'),
                            _l('reminder_is_notified'),
                            '',
                        ];
                        render_datatable($table_data, 'patient-reminders');
                        ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_prescriptions">
                        <?php
                        $table_data = [
                            'Date',
                            'Visit No / Code',
                            'Staff / Doctor',
                            'Next Visit',
                            'Action'
                        ];
                        render_datatable($table_data, 'patient-prescriptions');
                        ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_notes">
                        <!-- Add Note Form -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea id="note_description" class="form-control" rows="4"
                                        placeholder="Add a new call log..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="note_status_id">Follow-Up Status:</label>
                                    <select id="note_status_id" class="form-control selectpicker">
                                        <option value="">-- Keep Current Status --</option>
                                        <?php
                                        if (isset($statuses)) {
                                            $excluded_statuses = ['New', 'On-Paid Appointment', 'On-unpaid Appointment', 'On-Unpaid Appointment'];
                                            foreach ($statuses as $status) {
                                                if (!in_array($status['name'], $excluded_statuses)) {
                                                    echo '<option value="' . $status['id'] . '">' . $status['name'] . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-info pull-right" onclick="save_note()">Add
                                    Call Log</button>
                            </div>
                        </div>
                        <hr />
                        <!-- Notes List -->
                        <?php
                        $table_data = [
                            'Call Log',
                            'Staff',
                            'Date'
                        ];
                        render_datatable($table_data, 'patient-notes');
                        ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_appointments">
                        <h4 class="text-muted text-center mtop30">Hello Appointments</h4>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_subscriptions">
                        <h4 class="text-muted text-center mtop30">Hello Pt. Subscriptions</h4>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_invoices">
                        <h4 class="font-bold mbot15">Invoices</h4>
                        <?php
                        $table_data_invoices = [
                            _l('invoice_dt_table_heading_number'),
                            _l('invoice_dt_table_heading_amount'),
                            _l('invoice_estimate_year'),
                            _l('invoice_dt_table_heading_date'),
                            _l('invoice_dt_table_heading_status'),
                            _l('invoice_dt_table_heading_duedate'),
                        ];
                        render_datatable($table_data_invoices, 'patient-invoices');
                        ?>
                        <hr />
                        <h4 class="font-bold mbot15">Payments</h4>
                        <?php
                        $table_data_payments = [
                            _l('payments_table_number_heading'),
                            _l('payments_table_invoicenumber_heading'),
                            _l('payments_table_mode_heading'),
                            _l('payment_transaction_id'),
                            _l('payments_table_amount_heading'),
                            _l('payments_table_date_heading'),
                        ];
                        render_datatable($table_data_payments, 'patient-payments');
                        ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_expenses">
                        <a href="#"
                            onclick="window.location.href = admin_url + 'expenses/expense?customer_id=' + current_patient_id; return false;"
                            class="btn btn-info mbot15">
                            <i class="fa fa-plus"></i>
                            <?php echo _l('add_new', _l('expense_lowercase')); ?>
                        </a>
                        <?php
                        $table_data_expenses = [
                            _l('expense_dt_table_heading_category'),
                            _l('expense_dt_table_heading_amount'),
                            _l('expense_dt_table_heading_reference_no'),
                            _l('expense_dt_table_heading_date'),
                            _l('expense_dt_table_heading_payment_mode'),
                        ];
                        render_datatable($table_data_expenses, 'patient-expenses');
                        ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_tasks">
                        <!-- Add Task Form -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="task_name">
                                        <?php echo _l('task_add_edit_subject'); ?>
                                    </label>
                                    <input type="text" id="task_name" name="name" class="form-control" value="">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="task_startdate">
                                                <?php echo _l('task_add_edit_start_date'); ?>
                                            </label>
                                            <div class="input-group date">
                                                <input type="text" id="task_startdate" name="startdate"
                                                    class="form-control datetimepicker"
                                                    value="<?php echo _d(date('Y-m-d')); ?>">
                                                <div class="input-group-addon">
                                                    <i class="calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="task_duedate">
                                                <?php echo _l('task_add_edit_due_date'); ?>
                                            </label>
                                            <div class="input-group date">
                                                <input type="text" id="task_duedate" name="duedate"
                                                    class="form-control datetimepicker" value="">
                                                <div class="input-group-addon">
                                                    <i class="calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="task_priority">
                                        <?php echo _l('task_add_edit_priority'); ?>
                                    </label>
                                    <select name="priority" id="task_priority" class="selectpicker" data-width="100%">
                                        <option value="1">
                                            <?php echo _l('task_priority_low'); ?>
                                        </option>
                                        <option value="2" selected>
                                            <?php echo _l('task_priority_medium'); ?>
                                        </option>
                                        <option value="3">
                                            <?php echo _l('task_priority_high'); ?>
                                        </option>
                                        <option value="4">
                                            <?php echo _l('task_priority_urgent'); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group text-right">
                                    <button type="button" class="btn btn-info" onclick="save_task()">
                                        <?php echo _l('submit'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <!-- Tasks Table -->
                        <?php
                        $table_data = [
                            _l('task_add_edit_subject'),
                            _l('task_add_edit_start_date'),
                            _l('task_add_edit_due_date'),
                            _l('task_status'),
                            _l('task_add_edit_priority'),
                            '',
                        ];
                        render_datatable($table_data, 'patient-tasks');
                        ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_tickets">
                        <h4 class="text-muted text-center mtop30">Hello Tickets</h4>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_files">
                        <!-- Add File Form -->
                        <div class="row">
                            <div class="col-md-12">
                                <form id="form_add_file" enctype="multipart/form-data">
                                    <div class="dropzone-container"
                                        style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px; text-align: center; background: #f8fafc; transition: all 0.2s; cursor: pointer; position: relative;"
                                        onclick="document.getElementById('file_input').click()">

                                        <input type="file" id="file_input" name="file[]" class="hidden" multiple
                                            style="display:none;" onchange="updateFileLabel(this)">

                                        <div id="dropzone_content">
                                            <i class="fa fa-cloud-upload"
                                                style="font-size: 40px; color: #94a3b8; margin-bottom: 10px; display: block;"></i>
                                            <h5 class="text-muted" style="margin: 0; font-weight: 500; color: #64748b;">
                                                <span class="tw-font-bold text-info">Click to browse</span> or drag
                                                files here
                                            </h5>
                                        </div>
                                        <div id="file_selected_info" style="display:none; margin-top: 10px;">
                                            <i class="fa fa-file-text-o text-info" style="font-size: 24px;"></i>
                                            <h5 class="text-info mtop5" id="file_selected_count"></h5>
                                        </div>
                                    </div>
                                    <div class="text-right mtop15">
                                        <button type="submit" class="btn btn-primary shadow-sm"
                                            style="border-radius: 6px; padding: 8px 20px;">
                                            <i class="fa fa-upload"></i>
                                            <?php echo _l('upload'); ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <hr />
                        <!-- Files Table -->
                        <?php
                        $table_data_files = [
                            'Filename',
                            'Show in Patient App',
                            _l('date_created'),
                            _l('options'),
                        ];
                        render_datatable($table_data_files, 'patient-files');
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for horizontal tabs if needed */
    .custom-action-tabs::-webkit-scrollbar {
        height: 0px;
        background: transparent;
    }

    /* Nav Pills styling to match reference */
    .custom-action-tabs>li>a {
        border-radius: 8px;
        padding: 8px 15px;
        color: #475569;
        /* Slate 600 */
        font-weight: 500;
        font-size: 13px;
        transition: all 0.2s;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
        background: transparent;
    }

    .custom-action-tabs>li>a:hover {
        background-color: rgba(255, 255, 255, 0.5);
        color: #1e293b;
    }

    .custom-action-tabs>li.active>a,
    .custom-action-tabs>li.active>a:hover,
    .custom-action-tabs>li.active>a:focus {
        background-color: #ffffff;
        color: #0f172a;
        /* Slate 900 */
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .custom-action-tabs i {
        font-size: 14px;
        color: #64748b;
    }

    .custom-action-tabs>li.active>a i {
        color: #0f172a;
    }
</style>

<!-- Modal for Prescription Preview -->
<div class="modal fade" id="prescription_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 90%; height: 90%;">
        <div class="modal-content" style="height: 100%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Prescription Preview</h4>
            </div>
            <div class="modal-body" style="height: calc(100% - 60px); padding: 0;">
                <iframe id="prescription_iframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>