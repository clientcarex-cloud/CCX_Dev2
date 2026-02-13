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
                                            <!-- PDF Download Button (If needed later) -->
                                            <!-- <a href="<?php echo admin_url('shift_report/pdf?staff_id=' . ($staff_id ? $staff_id : 'all') . '&date=' . $date . '&type=' . $this->input->get('type')); ?>"
                                                class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i>
                                                Download PDF</a> -->
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
                                    /* Maybe portrait for this long list? Or landscape. using portrait as per image verticality */
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

                            /* Screen styles matching print for consisteny inside panel */
                        </style>

                        <div class="no-print">
                            <hr />
                        </div>

                        <div id="shift-report-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="pull-left bold">
                                        :: Shift Collection Report of
                                        <?php echo isset($staff_details) ? $staff_details->firstname . ' ' . $staff_details->lastname : 'All Staff'; ?>
                                        on <?php echo _d($date); ?>.
                                    </p>
                                    <p class="pull-right">
                                        Generated on : <?php echo date('Y-m-d h:i:s A'); ?>
                                    </p>
                                    <div class="clearfix"></div>
                                    <hr style="margin-top: 5px; margin-bottom: 15px; border-top: 1px solid #000;" />
                                </div>
                            </div>

                            <h4 class="bold" style="margin-top: 0;">Patients</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Ref Doc</th>
                                            <th>Ref Lab</th>
                                            <th class="text-right">Invoice Amount</th>
                                            <th class="text-right">Paid</th>
                                            <th class="text-right">Discount</th>
                                            <th class="text-right">Due Amount</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $grand_total = 0;
                                        $total_paid = 0;
                                        $total_discount = 0;
                                        $total_balance = 0;

                                        if (!empty($report_data['visits'])) {
                                            foreach ($report_data['visits'] as $visit) {
                                                $grand_total += $visit['invoice_amount'];
                                                $total_paid += $visit['paid']; // Paid today for this invoice
                                                $total_discount += $visit['discount'];
                                                $total_balance += $visit['balance'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $visit['visit_code']; ?></td>
                                                    <td><?php echo $visit['patient_name']; ?></td>
                                                    <td><?php echo $visit['ref_doc_text']; ?></td>
                                                    <td><?php echo $visit['ref_lab']; ?></td>
                                                    <td class="text-right">
                                                        <?php echo app_format_money($visit['invoice_amount'], get_base_currency()); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo app_format_money($visit['paid'], get_base_currency()); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo app_format_money($visit['discount'], get_base_currency()); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo app_format_money(abs($visit['balance']), get_base_currency()); ?>
                                                    </td>
                                                    <td><?php echo $visit['remarks']; ?></td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="9" class="text-center">No visits found</td>
                                            </tr>
                                        <?php } ?>

                                        <!-- Total Row -->
                                        <tr style="border-top: 2px solid #000; font-weight: bold;">
                                            <td colspan="4" class="text-right"><strong>Total</strong></td>
                                            <td class="text-right">
                                                <strong><?php echo app_format_money($grand_total, get_base_currency()); ?></strong>
                                            </td>
                                            <td class="text-right">
                                                <strong><?php echo app_format_money($total_paid, get_base_currency()); ?></strong>
                                            </td>
                                            <td class="text-right">
                                                <strong><?php echo app_format_money($total_discount, get_base_currency()); ?></strong>
                                            </td>
                                            <td class="text-right">
                                                <strong><?php echo app_format_money(abs($total_balance), get_base_currency()); ?></strong>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <br>

                            <?php
                            // Calculate footer totals
                            // Credit Collection (Past invoices paid today)
                            $credit_collection_total = 0;
                            if (!empty($report_data['credit_collections'])) {
                                foreach ($report_data['credit_collections'] as $cc) {
                                    $credit_collection_total += $cc['amount'];
                                }
                            }

                            // Refunds
                            $refund_total = 0;
                            if (!empty($report_data['refunds'])) {
                                foreach ($report_data['refunds'] as $ref) {
                                    $refund_total += $ref['amount'];
                                }
                            }

                            // Expenses
                            $expense_total = 0;
                            if (!empty($report_data['expenses'])) {
                                foreach ($report_data['expenses'] as $exp) {
                                    $expense_total += $exp['amount'];
                                }
                            }

                            // Card Payments (From all payments today)
                            $card_payment_total = 0;
                            if (!empty($report_data['all_payments'])) {
                                foreach ($report_data['all_payments'] as $pymt) {
                                    if (stripos($pymt['payment_mode_name'], 'Card') !== false) {
                                        $card_payment_total += $pymt['amount'];
                                    }
                                }
                            }
                            ?>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td class="bold">Total Invoice Amount: <?php echo app_format_money($grand_total, get_base_currency()); ?></td>
                                                <td class="bold">Total Due Amount: <?php echo app_format_money(abs($total_balance), get_base_currency()); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td class="bold" width="5%">(+)</td>
                                                <td class="bold" width="20%">Paid Amount</td>
                                                <td class="text-right" width="25%">
                                                    <?php echo app_format_money($total_paid, get_base_currency()); ?>
                                                </td>
                                                <td class="bold" width="20%">Credit Collection</td>
                                                <td class="text-right" width="20%">
                                                    <?php echo app_format_money($credit_collection_total, get_base_currency()); ?>
                                                </td>
                                                <td class="bold" width="5%">Total</td>
                                                <td class="text-right" width="10%">
                                                    <?php echo app_format_money($total_paid + $credit_collection_total, get_base_currency()); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bold">(-)</td>
                                                <td class="bold">Refund</td>
                                                <td class="text-right">
                                                    <?php echo app_format_money($refund_total, get_base_currency()); ?>
                                                </td>
                                                <td class="bold">Expense</td>
                                                <td class="text-right">
                                                    <?php echo app_format_money($expense_total, get_base_currency()); ?>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td class="bold" width="5%">(+)</td>
                                                <td class="bold" width="20%">Paid Amount</td>
                                                <td class="text-right" width="15%">
                                                    <?php echo app_format_money($total_paid, get_base_currency()); ?>
                                                </td>
                                                <td class="bold" width="20%">Credit Collection</td>
                                                <td class="text-right" width="15%">
                                                    <?php echo app_format_money($credit_collection_total, get_base_currency()); ?>
                                                </td>
                                                <td width="15%"></td>
                                                <td class="bold" width="5%">Total</td>
                                                <td class="text-right" width="10%">
                                                    <?php echo app_format_money($total_paid + $credit_collection_total, get_base_currency()); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bold">(-)</td>
                                                <td class="bold">Refund</td>
                                                <td class="text-right">
                                                    <?php echo app_format_money($refund_total, get_base_currency()); ?>
                                                </td>
                                                <td class="bold">Expense</td>
                                                <td class="text-right">
                                                    <?php echo app_format_money($expense_total, get_base_currency()); ?>
                                                </td>
                                                <td class="bold">Card Payment</td>
                                                <td class="text-right">
                                                    <?php echo app_format_money($card_payment_total, get_base_currency()); ?>
                                                </td>
                                                <td class="bold">Total</td>
                                                <td class="text-right">
                                                    <?php echo app_format_money($refund_total + $expense_total + $card_payment_total, get_base_currency()); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7" class="text-right bold">Net Amount</td>
                                                <td colspan="2" class="text-right bold">
                                                    <?php
                                                    $row1_total = $total_paid + $credit_collection_total;
                                                    $row2_total = $refund_total + $expense_total + $card_payment_total;
                                                    $net_amount = $row1_total - $row2_total;

                                                    echo app_format_money($net_amount, get_base_currency());
                                                    ?>
                                                </td>
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