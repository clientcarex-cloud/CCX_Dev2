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
                                        :: Shift Transaction Report of
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

                            <h4 class="bold" style="margin-top: 0; margin-bottom: 10px;">Mini Statement</h4>
                            <h4 class="bold" style="margin-top: 0; margin-bottom: 10px;">OP Patients</h4>
                            <h5 style="margin-top: 0; font-weight: normal; font-size: 13px;">Bill</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th>Bill No</th>
                                            <th>Date</th>
                                            <th>Mode</th>
                                            <th>MR No.</th>
                                            <th>Visit-Id</th>
                                            <th>Patient Name</th>
                                            <th>Phone No</th>
                                            <th>Remarks</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_amount = 0;

                                        if (!empty($report_data['transactions'])) {
                                            foreach ($report_data['transactions'] as $trans) {
                                                $total_amount += $trans['amount'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo 'REC-' . date('dmy', strtotime($trans['payment_date'])) . '-' . $trans['payment_id']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d-m-Y h:i A', strtotime($trans['payment_date'])); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $trans['payment_mode_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $trans['mr_number']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $trans['visit_code']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $trans['patient_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $trans['phonenumber']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $trans['remarks']; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($trans['amount'], 2); ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="9" class="text-center">No transactions found</td>
                                            </tr>
                                        <?php } ?>

                                        <tr style="font-weight: bold;">
                                            <td colspan="8" class="text-right">Total</td>
                                            <td class="text-right">
                                                <?php echo number_format($total_amount, 2); ?>
                                            </td>
                                        </tr>
                                        <tr style="font-weight: bold;">
                                            <td colspan="8" class="text-right">OP Total</td>
                                            <td class="text-right">
                                                <?php echo number_format($total_amount, 2); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <hr style="margin-top: 10px; margin-bottom: 10px;">

                            <?php
                            // Footer Calculation (Re-used generally, as totals should match across reports ideally)
                            $cash_payments = 0;
                            $non_cash_payments = 0;

                            if (!empty($report_data['transactions'])) {
                                foreach ($report_data['transactions'] as $trans) {
                                    if (stripos($trans['payment_mode_name'], 'Cash') !== false) {
                                        $cash_payments += $trans['amount'];
                                    } else {
                                        $non_cash_payments += $trans['amount'];
                                    }
                                }
                            }

                            $cash_expense = 0;
                            $non_cash_expense = 0;
                            if (!empty($report_data['expenses'])) {
                                foreach ($report_data['expenses'] as $exp) {
                                    if (stripos($exp['payment_mode_name'], 'Cash') !== false || empty($exp['payment_mode_name'])) {
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
                            // Image shows NetAmt matching Total if no exp/refund.
                            // e.g. 4350.00
                            ?>

                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr style="font-weight: bold; background-color: #f9f9f9;">
                                                <td>Cash Payments : <br><span style="font-size: 14px;">
                                                        <?php echo number_format($cash_payments, 2); ?>
                                                    </span></td>
                                                <td>NonCash Payments : <br><span style="font-size: 14px;">
                                                        <?php echo number_format($non_cash_payments, 2); ?>
                                                    </span></td>
                                                <td>CashExpense : <br><span style="font-size: 14px;">
                                                        <?php echo number_format($cash_expense, 2); ?>
                                                    </span></td>
                                                <td>NonCash Expense : <br><span style="font-size: 14px;">
                                                        <?php echo number_format($non_cash_expense, 2); ?>
                                                    </span></td>
                                                <td>Refund : <br><span style="font-size: 14px;">
                                                        <?php echo number_format($refund_total, 2); ?>
                                                    </span></td>
                                                <td>NetAmt : <br><span style="font-size: 14px;">
                                                        <?php echo number_format($net_amt, 2); ?>
                                                    </span></td>
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