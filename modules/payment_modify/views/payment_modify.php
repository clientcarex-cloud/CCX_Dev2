<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Filters tabs -->
                <div class="_filters _hidden_inputs hidden">
                    <?php echo form_hidden('status', $filters['status']); ?>
                </div>

                <div class="panel_s">
                    <div class="panel-body">

                        <div class="row mbot15">
                            <div class="col-md-12">
                                <div class="btn-group pull-left">
                                    <a href="<?php echo admin_url('payment_modify?status=all'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'all') ? 'btn-info' : 'btn-default'; ?>">All</a>
                                    <a href="<?php echo admin_url('payment_modify?status=dues'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'dues') ? 'btn-info' : 'btn-default'; ?>">Dues</a>
                                    <a href="<?php echo admin_url('payment_modify?status=partial'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'partial') ? 'btn-info' : 'btn-default'; ?>">Partial</a>
                                    <a href="<?php echo admin_url('payment_modify?status=refund'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'refund') ? 'btn-info' : 'btn-default'; ?>">Refund</a>
                                    <a href="<?php echo admin_url('payment_modify?status=paid'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'paid') ? 'btn-info' : 'btn-default'; ?>">Paid</a>
                                </div>
                                <div class="pull-right">
                                    <!-- Print/Export can be DataTables buttons -->
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <!-- Search input handled by DataTables usually, but image shows specific inputs -->
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('report_from', '', $filters['report_from'], ['placeholder' => 'From Date', 'autocomplete' => 'off']); ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('report_to', '', $filters['report_to'], ['placeholder' => 'To Date', 'autocomplete' => 'off']); ?>
                            </div>
                            <div class="col-md-3">
                                <select name="user_id" class="selectpicker" data-width="100%"
                                    data-none-selected-text="Select User" data-live-search="true">
                                    <option value=""></option>
                                    <?php foreach ($staff as $s) { ?>
                                        <option value="<?php echo $s['staffid']; ?>" <?php if ($filters['user_id'] == $s['staffid']) {
                                               echo 'selected';
                                           } ?>>
                                            <?php echo $s['firstname'] . ' ' . $s['lastname']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success" onclick="apply_filters();">Filter</button>
                            </div>
                        </div>

                        <div class="clearfix mtop20"></div>

                        <div class="table-responsive">
                            <table class="table dt-table table-payment-modify" data-order-col="1"
                                data-order-type="desc">
                                <thead>
                                    <tr>
                                        <!-- Removed Mass Select Checkbox Column as Actions are disabled -->
                                        <!-- Keeping column but empty or hidden might be safer if JS expects specific index, 
                                             but cleanest is to remove if no bulk actions. 
                                             However, typically DataTables is used, let's keep it simple and remove the bulk checkbox column.
                                        -->
                                        <th>Sno.</th>
                                        <th>Patients</th>
                                        <th>Lab Test</th>
                                        <th>Invoices & Receipts</th>
                                        <th>Ref Dr.</th>
                                        <th>Total(₹)</th>
                                        <th>Discount</th>
                                        <th>Net Total</th>
                                        <th>Paid</th>
                                        <th>Refund</th>
                                        <th>Total Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $sum_total = 0;
                                    $sum_discount = 0;
                                    $sum_net = 0;
                                    $sum_paid = 0;
                                    $sum_refund = 0;
                                    $sum_due = 0;
                                    foreach ($payments as $p) {
                                        $sum_total += $p['subtotal'];
                                        $sum_discount += $p['discount'];
                                        $sum_net += $p['total'];
                                        $sum_paid += $p['paid_amount'];
                                        $sum_refund += $p['refund_amount'];
                                        $sum_due += $p['due_amount'];
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $i++; ?>
                                            </td>
                                            <td>
                                                <span class="bold">
                                                    <?php echo $p['patient_name']; ?>
                                                </span><br>
                                                <span class="text-muted text-xs">
                                                    <?php echo _dt($p['visit_date']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                // Show first 2 tests
                                                $count = 0;
                                                foreach ($p['tests'] as $test) {
                                                    if ($count < 2) {
                                                        echo $test['description'] . '<br>';
                                                    }
                                                    $count++;
                                                }
                                                if ($count > 0) {
                                                    echo '<span class="text-xs">Total: ' . $count . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="pointer"
                                                onclick="toggle_invoice_details(<?php echo $p['visit_id']; ?>)">
                                                <span class="text-xs">Invoices:
                                                    <?php echo ($p['invoice_id'] ? 1 : 0); ?>
                                                </span><br>
                                                <span class="text-xs">Receipts:
                                                    <?php echo $p['receipt_count']; ?>
                                                </span>
                                                <i class="fa fa-chevron-down pull-right"
                                                    id="icon_<?php echo $p['visit_id']; ?>"></i>
                                            </td>
                                            <td><span class="bold uppercase">
                                                    <?php echo $p['doctor_name']; ?>
                                                </span></td>
                                            <td>
                                                <?php echo app_format_money($p['subtotal'], ''); ?>
                                            </td>
                                            <td>
                                                <?php echo app_format_money($p['discount'], ''); ?>
                                            </td>
                                            <td>
                                                <?php echo app_format_money($p['total'], ''); ?>
                                            </td>
                                            <td>
                                                <?php echo app_format_money($p['paid_amount'], ''); ?>
                                            </td>
                                            <td>
                                                <?php echo app_format_money($p['refund_amount'], ''); ?>
                                            </td>
                                            <td>
                                                <?php echo app_format_money($p['due_amount'], ''); ?>
                                            </td>
                                        </tr>
                                        <tr id="details_<?php echo $p['visit_id']; ?>" style="display:none;" class="active">
                                            <td colspan="11">
                                                <div class="row">
                                                    <?php if ($p['invoice_id']) { ?>
                                                        <div class="col-md-6">
                                                            <h4 class="bold">Invoice Details</h4>
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Invoice #</th>
                                                                        <th>Date</th>
                                                                        <th>Collected By</th>
                                                                        <th>Discount</th>
                                                                        <th>Total</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><a href="<?php echo admin_url('invoices/list_invoices/' . $p['invoice_id']); ?>"
                                                                                target="_blank"><?php echo format_invoice_number($p['invoice_id']); ?></a>
                                                                        </td>
                                                                        <td><?php echo _d($p['invoice_date']); ?></td>
                                                                        <td>
                                                                            <select class="form-control input-sm"
                                                                                id="sale_agent_<?php echo $p['invoice_id']; ?>"
                                                                                <?php echo ($settings['pm_allow_edit_collected_by'] == 0) ? 'disabled' : ''; ?>>
                                                                                <option value="0">System</option>
                                                                                <?php foreach ($staff as $s) { ?>
                                                                                    <option value="<?php echo $s['staffid']; ?>"
                                                                                        <?php echo ($s['staffid'] == $p['sale_agent']) ? 'selected' : ''; ?>>
                                                                                        <?php echo $s['firstname'] . ' ' . $s['lastname']; ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" step="any"
                                                                                class="form-control input-sm"
                                                                                id="discount_<?php echo $p['invoice_id']; ?>"
                                                                                value="<?php echo $p['discount']; ?>"
                                                                                <?php echo ($settings['pm_allow_edit_discount'] == 0) ? 'disabled' : ''; ?>>
                                                                        </td>
                                                                        <td><?php echo app_format_money($p['total'], $base_currency); ?>
                                                                        </td>
                                                                        <td><button class="btn btn-primary btn-xs"
                                                                                onclick="update_invoice(<?php echo $p['invoice_id']; ?>)">Save</button>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h4 class="bold">Receipts</h4>
                                                            <?php if (!empty($p['receipts'])) { ?>
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Mode</th>
                                                                            <th>Amount</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($p['receipts'] as $receipt) { ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control input-sm datepicker"
                                                                                        id="r_date_<?php echo $receipt['id']; ?>"
                                                                                        value="<?php echo _d($receipt['date']); ?>"
                                                                                        <?php echo ($settings['pm_allow_edit_date'] == 0) ? 'disabled' : ''; ?>>
                                                                                </td>
                                                                                <td>
                                                                                    <select class="form-control input-sm"
                                                                                        id="r_mode_<?php echo $receipt['id']; ?>"
                                                                                        <?php echo ($settings['pm_allow_edit_mode'] == 0) ? 'disabled' : ''; ?>>
                                                                                        <?php foreach ($payment_modes as $mode) { ?>
                                                                                            <option value="<?php echo $mode['id']; ?>" <?php echo ($mode['id'] == $receipt['paymentmode']) ? 'selected' : ''; ?>>
                                                                                                <?php echo $mode['name']; ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" step="any"
                                                                                        class="form-control input-sm"
                                                                                        id="r_amount_<?php echo $receipt['id']; ?>"
                                                                                        value="<?php echo $receipt['amount']; ?>"
                                                                                        <?php echo ($settings['pm_allow_edit_amount'] == 0) ? 'disabled' : ''; ?>>
                                                                                </td>
                                                                                <td><button class="btn btn-primary btn-xs"
                                                                                        onclick="update_receipt(<?php echo $receipt['id']; ?>)">Save</button>
                                                                                </td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                    </tbody>
                                                                </table>
                                                            <?php } else {
                                                                echo '<p class="text-muted">No receipts found.</p>';
                                                            } ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="col-md-12">
                                                            <p class="text-muted">No Invoice Generated</p>
                                                        </div>
                                                    <?php } ?>
                                                </div>
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
<?php $base_currency = get_base_currency(); ?>
<script>
    function apply_filters() {
        var report_from = $('input[name="report_from"]').val();
        var report_to = $('input[name="report_to"]').val();
        var user_id = $('select[name="user_id"]').val();
        var status = '<?php echo $filters['status']; ?>';
        window.location.href = admin_url + 'payment_modify?status=' + status + '&report_from=' + report_from + '&report_to=' + report_to + '&user_id=' + user_id;
    }

    function toggle_invoice_details(visit_id) {
        var row = $('#details_' + visit_id);
        var icon = $('#icon_' + visit_id);

        if (row.is(':visible')) {
            row.hide();
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            row.show();
            // row.css('display', 'table-row'); // jquery show() should handle this for tr usually, but explicit is safe if needed.
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    }

    function update_receipt(id) {
        var date = $('#r_date_' + id).val();
        var mode = $('#r_mode_' + id).val();
        var amount = $('#r_amount_' + id).val();

        $.post(admin_url + 'payment_modify/update_receipt', {
            id: id,
            date: date,
            paymentmode: mode,
            amount: amount,
            [csrfData.token_name]: csrfData.hash
        }).done(function(response) {
            response = JSON.parse(response);
            if(response.success) {
                alert_float('success', 'Receipt updated successfully');
                // Optional: reload page to reflect changes in main table totals
                // window.location.reload(); 
            } else {
                alert_float('danger', response.message);
            }
        });
    }

    function update_invoice(id) {
        var agent = $('#sale_agent_' + id).val();
        var discount = $('#discount_' + id).val();

        $.post(admin_url + 'payment_modify/update_invoice', {
            id: id,
            sale_agent: agent,
            discount: discount,
            [csrfData.token_name]: csrfData.hash
        }).done(function(response) {
            response = JSON.parse(response);
            if(response.success) {
                alert_float('success', 'Invoice details updated successfully');
                 // Optional: reload
            } else {
                alert_float('danger', 'Failed to update invoice');
            }
        });
    }
</script>

<?php if (is_admin()) { ?>
<!-- Floating Settings Button -->
<div class="fixed-action-btn horizontal click-to-toggle" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large text-white" style="background-color: #f0f0f0;" data-toggle="modal" data-target="#payment_modify_settings">
        <i class="fa fa-cog fa-2x" style="color: #555; line-height: 55px;"></i>
    </a>
</div>

<style>
    .fixed-action-btn {
        position: fixed;
        right: 23px;
        bottom: 23px;
        padding-top: 15px;
        margin-bottom: 0;
        z-index: 998;
    }
    .fixed-action-btn .btn-floating {
        position: relative;
        overflow: hidden;
        z-index: 1;
        width: 55px;
        height: 55px;
        line-height: 55px;
        padding: 0;
        border-radius: 50%;
        cursor: pointer;
        vertical-align: middle;
        box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);
        display: inline-block;
        text-align: center;
        transition: .3s;
    }
    .fixed-action-btn .btn-floating:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18), 0 4px 15px 0 rgba(0,0,0,0.15);
    }
</style>

<!-- Settings Modal -->
<div class="modal fade" id="payment_modify_settings" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Payment Modify Settings</h4>
            </div>
            <div class="modal-body">
                <p class="text-info"><i class="fa fa-info-circle"></i> Enable or disable editing for specific fields.</p>
                <form id="pm_settings_form">
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="pm_allow_edit_mode" id="pm_allow_edit_mode" <?php echo ($settings['pm_allow_edit_mode'] == 1) ? 'checked' : ''; ?>>
                        <label for="pm_allow_edit_mode">Allow Edit Payment Mode</label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="pm_allow_edit_amount" id="pm_allow_edit_amount" <?php echo ($settings['pm_allow_edit_amount'] == 1) ? 'checked' : ''; ?>>
                        <label for="pm_allow_edit_amount">Allow Edit Amount</label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="pm_allow_edit_date" id="pm_allow_edit_date" <?php echo ($settings['pm_allow_edit_date'] == 1) ? 'checked' : ''; ?>>
                        <label for="pm_allow_edit_date">Allow Edit Date</label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="pm_allow_edit_collected_by" id="pm_allow_edit_collected_by" <?php echo ($settings['pm_allow_edit_collected_by'] == 1) ? 'checked' : ''; ?>>
                        <label for="pm_allow_edit_collected_by">Allow Edit Collected By (Staff)</label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="pm_allow_edit_discount" id="pm_allow_edit_discount" <?php echo ($settings['pm_allow_edit_discount'] == 1) ? 'checked' : ''; ?>>
                        <label for="pm_allow_edit_discount">Allow Edit Discount</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="save_pm_settings()">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
    function save_pm_settings() {
        var data = $('#pm_settings_form').serialize();
        // Append CSRF token
        if (typeof csrfData !== 'undefined') {
            data += '&' + csrfData['token_name'] + '=' + csrfData['hash'];
        }

        $.post(admin_url + 'payment_modify/save_settings', data).done(function(response) {
            response = JSON.parse(response);
            if(response.success) {
                alert_float('success', 'Settings saved successfully');
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }
        });
    }
</script>
<?php init_tail(); ?>