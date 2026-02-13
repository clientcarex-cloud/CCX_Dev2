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
                                        <!-- Staff selection is ignored for overall report but kept for consistency/potential filter -->
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
                                            <a href="<?php echo admin_url('shift_report/pdf?staff_id=' . ($staff_id ? $staff_id : 'all') . '&date=' . $date . '&type=' . $this->input->get('type')); ?>"
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
                                    size: A4 landscape;
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

                                #shift-report-content {
                                    width: 100%;
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

                                .font-weight-bold {
                                    font-weight: bold !important;
                                }
                            }

                            /* Screen styles for table similar to print */
                            #shift-report-content table th,
                            #shift-report-content table td {
                                vertical-align: middle;
                            }
                        </style>

                        <div class="no-print">
                            <hr />
                        </div>

                        <div id="shift-report-content">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 0px solid #000;">
                                <h3 class="font-weight-bold" style="margin: 0;">CONSOLIDATED OVERALL (HOSPITAL + LAB)
                                    REPORT</h3>
                                <h3 class="font-weight-bold" style="margin: 0;">
                                    <?php echo _d($date); ?>
                                </h3>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="text-center" style="vertical-align: middle;">S.No.
                                            </th>
                                            <th rowspan="2" style="vertical-align: middle;">User Name</th>
                                            <th colspan="2" class="text-center">Sales</th>
                                            <th rowspan="2" class="text-center" style="vertical-align: middle;">Total
                                                Sales</th>
                                            <th rowspan="2" class="text-center" style="vertical-align: middle;">Refund
                                            </th>
                                            <th rowspan="2" class="text-center" style="vertical-align: middle;">Net
                                                Amount</th>
                                            <th colspan="2" class="text-center" style="border-bottom:0;"></th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Cash</th>
                                            <th class="text-right">Online</th>
                                            <th class="text-right">Cash In Hand</th>
                                            <th class="text-right">Online</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sno = 1;
                                        $grand_total_cash = 0;
                                        $grand_total_online = 0;
                                        $grand_total_sales = 0;
                                        $grand_total_refund = 0;
                                        $grand_net_amount = 0;
                                        $grand_cash_in_hand = 0;
                                        $grand_online_final = 0;
                                        ?>
                                        <?php if (!empty($report_data)): ?>
                                            <?php foreach ($report_data as $row): ?>
                                                <?php
                                                $total_sales = $row['cash_sales'] + $row['online_sales'];
                                                $net_amount = $total_sales - $row['refunds'];
                                                $cash_in_hand = $row['cash_sales'] - $row['refunds']; // Assuming refunds are paid from cash
                                                $online_final = $row['online_sales'];

                                                $grand_total_cash += $row['cash_sales'];
                                                $grand_total_online += $row['online_sales'];
                                                $grand_total_sales += $total_sales;
                                                $grand_total_refund += $row['refunds'];
                                                $grand_net_amount += $net_amount;
                                                $grand_cash_in_hand += $cash_in_hand;
                                                $grand_online_final += $online_final;
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <?php echo $sno++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['staff_name']; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($row['cash_sales'], 2); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($row['online_sales'], 2); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($total_sales, 2); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($row['refunds'], 2); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($net_amount, 2); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($cash_in_hand, 2); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($online_final, 2); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="text-center">No records found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="font-weight: bold; font-size: 14px;">
                                            <td colspan="2" class="text-right">Total:</td>
                                            <td class="text-right">
                                                <?php echo number_format($grand_total_cash, 2); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_format($grand_total_online, 2); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_format($grand_total_sales, 2); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_format($grand_total_refund, 2); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_format($grand_net_amount, 2); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_format($grand_cash_in_hand, 2); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_format($grand_online_final, 2); ?>
                                            </td>
                                        </tr>
                                    </tfoot>
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
</body>

</html>