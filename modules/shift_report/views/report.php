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

                                <div style="display: flex; align-items: flex-end; gap: 15px;">
                                    <div class="form-group mbot0" style="width: 200px;">
                                        <label class="control-label" for="staff_id">Staff</label>
                                        <select name="staff_id" id="staff_id" class="selectpicker" data-width="100%"
                                            data-none-selected-text="Select Staff" data-live-search="true">
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
                                        <?php if ($staff_id && $date): ?>
                                            <a href="<?php echo admin_url('shift_report/pdf?staff_id=' . $staff_id . '&date=' . $date); ?>"
                                                class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i>
                                                Download PDF</a>
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
                                    size: A4;
                                    margin: 10mm;
                                }

                                body {
                                    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                                    font-size: 11px;
                                    color: #000;
                                    background: #fff;
                                    margin: 0;
                                    padding: 0;
                                }

                                /* Hide everything by default using display none to prevent extra pages */
                                /* Hide standard Perfex CRM layout elements */
                                #header,
                                aside,
                                .navbar,
                                .admin-navbar,
                                #setup-menu-wrapper,
                                .sidebar {
                                    display: none !important;
                                }

                                /* Ensure main wrapper is full width and visible */
                                #wrapper {
                                    margin: 0 !important;
                                    padding: 0 !important;
                                    width: 100% !important;
                                    min-height: auto !important;
                                }

                                .content {
                                    padding: 0 !important;
                                    margin: 0 !important;
                                }

                                /* Show only the report content and its children */
                                #shift-report-content,
                                #shift-report-content * {
                                    display: block;
                                    visibility: visible;
                                }

                                /* Restore table display properties */
                                #shift-report-content table {
                                    display: table;
                                }

                                #shift-report-content thead {
                                    display: table-header-group;
                                }

                                #shift-report-content tbody {
                                    display: table-row-group;
                                }

                                #shift-report-content tr {
                                    display: table-row;
                                }

                                #shift-report-content th,
                                #shift-report-content td {
                                    display: table-cell;
                                }

                                #shift-report-content {
                                    position: static;
                                    /* Remove absolute positioning */
                                    width: 100%;
                                    margin: 0;
                                    padding: 0;
                                    left: auto;
                                    top: auto;
                                }

                                .no-print {
                                    display: none !important;
                                }

                                /* Reset wrapper margins if any interfere */
                                #wrapper,
                                .content,
                                .panel_s,
                                .panel-body {
                                    margin: 0 !important;
                                    padding: 0 !important;
                                    border: none !important;
                                    box-shadow: none !important;
                                    width: 100% !important;
                                }

                                /* Table Styling for Print */
                                .table {
                                    width: 100% !important;
                                    border-collapse: collapse !important;
                                    border: 1px solid #000 !important;
                                    margin-bottom: 20px !important;
                                }

                                .table th,
                                .table td {
                                    border: 1px solid #000 !important;
                                    padding: 4px 6px !important;
                                    font-size: 11px !important;
                                    color: #000 !important;
                                }

                                .table thead th {
                                    font-weight: bold !important;
                                    background-color: #eee !important;
                                    -webkit-print-color-adjust: exact;
                                }

                                .table-bordered>thead>tr>th,
                                .table-bordered>tbody>tr>th,
                                .table-bordered>tfoot>tr>th,
                                .table-bordered>thead>tr>td,
                                .table-bordered>tbody>tr>td,
                                .table-bordered>tfoot>tr>td {
                                    border: 1px solid #000 !important;
                                }

                                /* Summary Table specific */
                                .table-responsive {
                                    overflow: visible !important;
                                    width: 100% !important;
                                    max-width: none !important;
                                    /* Override inline style if any */
                                }

                                h3,
                                h4,
                                h5 {
                                    margin-top: 10px;
                                    margin-bottom: 10px;
                                    font-weight: bold;
                                    color: #000;
                                }

                                h3 {
                                    font-size: 16px;
                                    margin-bottom: 20px;
                                }

                                h4 {
                                    font-size: 14px;
                                    text-decoration: underline;
                                    margin-top: 20px;
                                }

                                h5 {
                                    font-size: 12px;
                                }

                                .text-right {
                                    text-align: right !important;
                                }

                                .text-center {
                                    text-align: center !important;
                                }

                                .font-weight-bold {
                                    font-weight: bold !important;
                                }
                            }
                        </style>
                        <?php if ($this->input->get('print') == 'true'): ?>
                            <script>
                                        window.onload = function () { window.print(); }
                            </script>
                        <?php endif; ?>

                        <div class="no-print">
                            <hr />
                        </div>

                        <!-- Report Content -->
                        <div id="shift-report-content">
                            <h3 class="text-center font-weight-bold">HOSPITAL Shift Report of
                                <?php echo isset($staff_details) ? $staff_details->firstname . ' ' . $staff_details->lastname : ''; ?>
                                on
                                <?php echo $date; ?>
                            </h3>

                            <!-- OP Patients Section -->
                            <h4>OP Patients</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Bill No.</th>
                                            <th>Datetime</th>
                                            <th>Mode</th>
                                            <th>Cash</th>
                                            <th>Online</th>
                                            <th>MR.No.</th>
                                            <th>Visit Id</th>
                                            <th>Patient Name</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                            <th>Phone No.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_op_sales = 0;
                                        $total_cash = 0;
                                        $total_online = 0;
                                        $sno = 1;
                                        ?>
                                        <?php if (!empty($report_data['op_sales'])): ?>
                                            <?php foreach ($report_data['op_sales'] as $row): ?>
                                                <?php
                                                $is_cash = (stripos($row['payment_mode'], 'Cash') !== false);
                                                $cash_amount = $is_cash ? $row['paid_amount'] : 0.00;
                                                $online_amount = !$is_cash ? $row['paid_amount'] : 0.00;

                                                // Accumulate totals
                                                $total_op_sales += $row['paid_amount']; // Assume paid amount is the sale amount realized
                                                $total_cash += $cash_amount;
                                                $total_online += $online_amount;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $sno++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['bill_no']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d-m-Y h:i A', strtotime($row['created_at'])); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['payment_mode'] ? $row['payment_mode'] : 'Unpaid'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo number_format($cash_amount, 2); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo number_format($online_amount, 2); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['mr_number']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['visit_code']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['patient_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo number_format($row['paid_amount'], 2); ?>
                                                    </td>
                                                    <td></td> <!-- Remarks placeholder -->
                                                    <td>
                                                        <?php echo $row['phonenumber']; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="12" class="text-center">No records found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="9" class="text-right font-weight-bold">Total OP Sales:</td>
                                            <td colspan="3" class="font-weight-bold">
                                                <?php echo number_format($total_op_sales, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-right font-weight-bold">Cash:</td>
                                            <td colspan="3" class="font-weight-bold">
                                                <?php echo number_format($total_cash, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-right font-weight-bold">Online:</td>
                                            <td colspan="3" class="font-weight-bold">
                                                <?php echo number_format($total_online, 2); ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <br>

                            <!-- Cancellations Section -->
                            <h4>HOSPITAL Cancellations</h4>
                            <h5>OP Patients</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Bill No.</th>
                                            <th>Datetime</th>
                                            <th>Mode</th>
                                            <th>Cash</th>
                                            <th>Online</th>
                                            <th>MR.No.</th>
                                            <th>Visit Id</th>
                                            <th>Patient Name</th>
                                            <th>Amount</th>
                                            <th>Paid</th>
                                            <th>Refund</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_refund = 0;
                                        $c_sno = 1;
                                        ?>
                                        <?php if (!empty($report_data['cancellations'])): ?>
                                            <?php foreach ($report_data['cancellations'] as $cancel): ?>
                                                <?php
                                                // Refund logic: needed fields not fully fetched, assuming simplistic for now
                                                $refund_amount = $cancel['invoice_amount']; // Assuming full refund
                                                $total_refund += $refund_amount;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $c_sno++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $cancel['bill_no']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d-m-Y h:i A', strtotime($cancel['created_at'])); ?>
                                                    </td>
                                                    <td>-</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>
                                                        <?php echo $cancel['mr_number']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $cancel['visit_code']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $cancel['patient_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo number_format($cancel['invoice_amount'], 2); ?>
                                                    </td>
                                                    <td>0.00</td>
                                                    <td>
                                                        <?php echo number_format($refund_amount, 2); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="12" class="text-center">No cancellations.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="11" class="text-right font-weight-bold">Total OP Refund:</td>
                                            <td class="font-weight-bold">
                                                <?php echo number_format($total_refund, 2); ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <br>

                            <!-- Summary Section -->
                            <h4>Summary</h4>
                            <div class="table-responsive" style="max-width: 400px;">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th class="font-weight-bold">Cash Total</th>
                                            <td class="text-right font-weight-bold">
                                                <?php echo number_format($total_cash, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="font-weight-bold">Non Cash Total</th>
                                            <td class="text-right font-weight-bold">
                                                <?php echo number_format($total_online, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="font-weight-bold">Total Collection</th>
                                            <td class="text-right font-weight-bold">
                                                <?php echo number_format($total_cash + $total_online, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="font-weight-bold">Refund</th>
                                            <td class="text-right font-weight-bold">
                                                <?php echo number_format($total_refund, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="font-weight-bold">Net Amount</th>
                                            <td class="text-right font-weight-bold">
                                                <?php echo number_format(($total_cash + $total_online) - $total_refund, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="font-weight-bold">Cash In Hand</th>
                                            <td class="text-right font-weight-bold">
                                                <?php echo number_format($total_cash - $total_refund, 2); ?>
                                            </td> <!-- Assuming refunds are paid from cash? -->
                                        </tr>
                                        <tr>
                                            <th class="font-weight-bold">UPI</th>
                                            <td class="text-right font-weight-bold">
                                                <?php echo number_format($total_online, 2); ?>
                                            </td>
                                        </tr>
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
<?php init_tail(); ?>
<script>
    // Optional: Add print button script or auto-print if needed
</script>
</body>

</html>