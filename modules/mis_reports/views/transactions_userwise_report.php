<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-center font-bold" style="background-color: #badcfb;">Transactions Userwise Report</h3>
    </div>
</div>

<?php
// Group data by User Name
$users = [];
if (!empty($report_data)) {
    foreach ($report_data as $row) {
        $users[$row['user_name']][] = $row;
    }
}
?>

<?php if (empty($users)): ?>
    <div class="alert alert-info">No records found.</div>
<?php else: ?>
    <?php foreach ($users as $user_name => $rows): ?>
        <?php
        // Calculate Totals for this User
        $user_total = 0;
        $user_cash_paid = 0;
        $user_non_cash_paid = 0;

        foreach ($rows as $row) {
            $amt = (float) $row['amount'];
            $user_total += $amt;

            // Check if payment mode contains "Cash"
            if (stripos($row['payment_mode_name'], 'Cash') !== false) {
                $user_cash_paid += $amt;
            } else {
                $user_non_cash_paid += $amt;
            }
        }
        // Net Amount = Total - Refund (0 for now)
        $net_amount = $user_total;
        ?>
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading font-bold" style="border-bottom: 1px solid #ddd;">
                        :: Shift Transaction Report of
                        <?php echo $user_name; ?> on
                        <?php echo $from_date; ?>.
                        <span class="pull-right">Generated on :
                            <?php echo date('Y-m-d h:i:s A'); ?>
                        </span>
                    </div>
                    <div class="panel-body" style="padding: 0;">

                        <!-- Header Sections -->
                        <h4 class="font-bold" style="padding: 10px; margin: 0; border-bottom: 1px solid #ddd;">Mini Statement
                        </h4>
                        <h4 class="font-bold" style="padding: 10px; margin: 0; border-bottom: 1px solid #ddd;">OP Patients</h4>
                        <h5 class="font-bold" style="padding: 5px 10px; margin: 0; border-bottom: 1px solid #ddd;">Bill</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
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
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td><?php echo 'REC-' . date('dmy', strtotime($row['transaction_date'])) . '-' . $row['receipt_id']; ?>
                                            </td>
                                            <td><?php echo _dt($row['transaction_date']); ?></td>
                                            <td><?php echo $row['payment_mode_name']; ?></td>
                                            <td><?php echo $row['mr_number']; ?></td>
                                            <td><?php echo $row['visit_code']; ?></td>
                                            <td><?php echo $row['patient_name']; ?></td>
                                            <td><?php echo $row['phonenumber']; ?></td>
                                            <td><?php echo $row['remarks']; ?></td>
                                            <td class="text-right"><?php echo app_format_money($row['amount'], ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="font-bold">
                                        <td colspan="8" class="text-right">Total</td>
                                        <td class="text-right">
                                            <?php echo app_format_money($user_total, ''); ?>
                                        </td>
                                    </tr>
                                    <tr class="font-bold">
                                        <td colspan="8" class="text-right">OP Total</td>
                                        <td class="text-right">
                                            <?php echo app_format_money($user_total, ''); ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Footer Payment Splits -->
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-condensed font-bold" style="margin-bottom: 0;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 20%;">Cash Payments : <br>
                                                    <?php echo app_format_money($user_cash_paid, ''); ?>
                                                </td>
                                                <td style="width: 20%;">NonCash Payments : <br>
                                                    <?php echo app_format_money($user_non_cash_paid, ''); ?>
                                                </td>
                                                <td style="width: 20%;">CashExpense : <br> 0.00</td>
                                                <td style="width: 20%;">NonCash Expense : <br> 0.00</td>
                                                <td style="width: 10%;">Refund : <br> 0.00</td>
                                                <td style="width: 10%;">NetAmt : <br>
                                                    <?php echo app_format_money($net_amount, ''); ?>
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
    <?php endforeach; ?>
<?php endif; ?>