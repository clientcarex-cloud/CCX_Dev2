<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Filter Form -->
                        <div class="row no-print">
                            <div class="col-md-12">
                                <?php echo form_open(admin_url('shift_report/report'), ['method' => 'GET', 'id' => 'shift-report-form']); ?>
                                <input type="hidden" name="type" value="<?php echo $this->input->get('type'); ?>">

                                <div style="display: flex; align-items: flex-end; gap: 15px;">
                                    <div class="form-group mbot0" style="width: 200px;">
                                        <label class="control-label" for="staff_id">Staff</label>
                                        <select name="staff_id" id="staff_id" class="selectpicker" data-width="100%"
                                            data-none-selected-text="Select Staff" data-live-search="true">
                                            <option value="">All Staff</option>
                                            <?php foreach ($staff_list as $s) { ?>
                                                <option value="<?php echo $s['staffid']; ?>" <?php echo $s['staffid'] == $staff_id ? 'selected' : ''; ?>>
                                                    <?php echo $s['firstname'] . ' ' . $s['lastname']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div style="width: 150px;">
                                        <?php echo render_date_input('date', 'Date', _d($date), ['placeholder' => 'Date'], [], 'mbot0'); ?>
                                    </div>
                                    <div class="form-group mbot0">
                                        <button type="submit" class="btn btn-info">Preview</button>
                                        <?php if ($date): ?>
                                            <button type="button" onclick="window.print();" class="btn btn-default"><i
                                                    class="fa fa-print"></i> Print</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>

                        <!-- Print Styles -->
                        <style>
                            @media print {
                                @page {
                                    size: A4 portrait;
                                    margin: 5mm;
                                }

                                body {
                                    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                                    font-size: 11px;
                                    color: #000;
                                    background: #fff;
                                }

                                .no-print,
                                #header,
                                aside,
                                .navbar,
                                .admin-navbar,
                                .sidebar {
                                    display: none !important;
                                }

                                #wrapper {
                                    margin: 0 !important;
                                    padding: 0 !important;
                                    width: 100% !important;
                                }

                                .content {
                                    padding: 0 !important;
                                    margin: 0 !important;
                                }

                                .table {
                                    width: 100% !important;
                                    border-collapse: collapse !important;
                                    border: 1px solid #000 !important;
                                }

                                .table th,
                                .table td {
                                    border: 1px solid #000 !important;
                                    padding: 4px 6px !important;
                                }

                                .text-right {
                                    text-align: right !important;
                                }

                                .text-center {
                                    text-align: center !important;
                                }

                                .bold {
                                    font-weight: bold !important;
                                }

                                .pull-left {
                                    float: left !important;
                                }

                                .pull-right {
                                    float: right !important;
                                }
                            }
                        </style>

                        <div class="no-print">
                            <hr />
                        </div>

                        <div id="shift-report-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="pull-left bold">
                                        :: Collection Report of
                                        <?php echo isset($staff_details) ? $staff_details->firstname . ' ' . $staff_details->lastname : 'All Staff'; ?>
                                        on
                                        <?php echo _d($date); ?>.
                                    </p>
                                    <p class="pull-right">
                                        Generated on :
                                        <?php echo date('Y-m-d h:i:s A'); ?>
                                    </p>
                                    <div class="clearfix"></div>
                                    <hr style="margin-top: 5px; margin-bottom: 15px; border-top: 1px solid #000;" />
                                </div>
                            </div>

                            <!-- Out Patient Details -->
                            <h4 class="bold" style="margin-top: 0; margin-bottom: 5px;">Out Patient Details</h4>
                            <h5 style="margin-top: 0; font-weight: bold;">Lab</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th>VisitID</th>
                                            <th>Patient</th>
                                            <th>TestName</th>
                                            <th class="text-right">Amount</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $op_total_amount = 0;
                                        $op_total_paid = 0;
                                        $op_total_discount = 0;
                                        $op_total_balance = 0;

                                        if (!empty($report_data['out_patient_details'])) {
                                            foreach ($report_data['out_patient_details'] as $visit) {
                                                $op_total_amount += $visit['invoice_amount'];
                                                $op_total_paid += $visit['paid'];
                                                $op_total_discount += $visit['discount'];
                                                $op_total_balance += $visit['balance'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $visit['visit_code']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $visit['patient_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $visit['test_names']; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo app_format_money($visit['invoice_amount'], get_base_currency()); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $visit['remarks']; ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No visits found</td>
                                            </tr>
                                        <?php } ?>

                                        <!-- Footer for OP -->
                                        <tr style="font-weight: bold;">
                                            <td colspan="2">Total :
                                                <?php echo app_format_money($op_total_amount, get_base_currency()); ?>
                                            </td>
                                            <td>Paid :
                                                <?php echo app_format_money($op_total_paid, get_base_currency()); ?>
                                            </td>
                                            <td>Discount :
                                                <?php echo app_format_money($op_total_discount, get_base_currency()); ?>
                                            </td>
                                            <td>Balance :
                                                <?php echo app_format_money(abs($op_total_balance), get_base_currency()); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Due Collection -->
                            <h4 class="bold" style="margin-top: 15px;">Due Collection</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <!-- ID in image seems empty for header or 'ID' -->
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Last Due</th>
                                            <th>Paid</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $due_total_paid = 0;
                                        if (!empty($report_data['due_collection'])) {
                                            foreach ($report_data['due_collection'] as $due) {
                                                $due_total_paid += $due['paid_amount'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $due['payment_id']; // Using Pay ID as ID ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $due['patient_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo app_format_money($due['last_due'], get_base_currency()); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo app_format_money($due['paid_amount'], get_base_currency()); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $due['remarks']; ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td style="height: 30px;"></td>
                                                <!-- Empty row as per image if empty? Or simple message -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        <?php } ?>
                                        <tr style="font-weight: bold;">
                                            <td colspan="2" class="text-right">Total</td>
                                            <td>0</td> <!-- Last Due Total? Image says 0 -->
                                            <td>
                                                <?php echo app_format_money($due_total_paid, get_base_currency()); ?>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- In Patient Details -->
                            <h4 class="bold" style="margin-top: 15px;">In Patient Details</h4>
                            <div class="table-responsive" style="min-height: 50px; border-bottom: 1px solid #ddd;">
                                <!-- Empty as requested and in image -->
                            </div>

                            <hr style="margin-top: 10px; margin-bottom: 10px;">

                            <?php
                            // Footer Calculation
                            $cash_payments = 0;
                            $non_cash_payments = 0;

                            if (!empty($report_data['all_payments'])) {
                                foreach ($report_data['all_payments'] as $pymt) {
                                    // Determine Cash vs NonCash
                                    // Assuming 'Cash' or ID 1. Checking name.
                                    if (stripos($pymt['payment_mode_name'], 'Cash') !== false) {
                                        $cash_payments += $pymt['amount'];
                                    } else {
                                        $non_cash_payments += $pymt['amount'];
                                    }
                                }
                            }

                            $cash_expense = 0;
                            $non_cash_expense = 0; // Assuming 0 unless expenses have mode
                            if (!empty($report_data['expenses'])) {
                                foreach ($report_data['expenses'] as $exp) {
                                    // Expenses table joins paymentmode
                                    if (stripos($exp['payment_mode_name'], 'Cash') !== false || empty($exp['payment_mode_name'])) {
                                        // If no mode, assume cash? OR strictly check.
                                        // Usually petty cash is cash.
                                        $cash_expense += $exp['amount'];
                                    } else {
                                        $non_cash_expense += $exp['amount'];
                                    }
                                }
                            }

                            $refund_total = 0;
                            if (!empty($report_data['refunds'])) {
                                foreach ($report_data['refunds'] as $ref) {
                                    $refund_total += $ref['amount'];
                                }
                            }

                            $net_amt = ($cash_payments + $non_cash_payments) - ($cash_expense + $non_cash_expense + $refund_total);
                            // Verify logic with image:
                            // Image: Paid 3550. NetAmt 3550.
                            // Assuming all payments are Cash.
                            // If all Cash, NetAmt = Cash In Hand.
                            // If NetAmt includes NonCash, it is Total Collection.
                            // The image label "NetAmt" is usually Cash + NonCash - Refund.
                            // Let's stick to (Inc - Exp).
                            ?>

                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td>Cash Payments : <br><b>
                                                        <?php echo number_format($cash_payments, 2); ?>
                                                    </b></td>
                                                <td>NonCash Payments : <br><b>
                                                        <?php echo number_format($non_cash_payments, 2); ?>
                                                    </b></td>
                                                <td>CashExpense : <br><b>
                                                        <?php echo number_format($cash_expense, 2); ?>
                                                    </b></td>
                                                <td>NonCash Expense : <br><b>
                                                        <?php echo number_format($non_cash_expense, 2); ?>
                                                    </b></td>
                                                <td>Refund : <br><b>
                                                        <?php echo number_format($refund_total, 2); ?>
                                                    </b></td>
                                                <td>NetAmt : <br><b>
                                                        <?php echo number_format($net_amt, 2); ?>
                                                    </b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div> <!-- End Shift Report Content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    </body>

    </html>