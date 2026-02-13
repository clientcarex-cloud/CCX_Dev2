<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-center font-bold" style="background-color: #badcfb;">Business Userwise Report</h3>
    </div>
</div>

<?php
// Group data by User Name
$users = [];
$users_totals = [];

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
        $user_test_total = 0;
        $user_paid_total = 0;
        $user_discount_total = 0;
        $user_balance_total = 0;

        $user_cash_paid = 0;
        $user_non_cash_paid = 0;

        foreach ($rows as $row) {
            $user_test_total += (float) $row['test_total'];
            $user_paid_total += (float) $row['paid'];
            $user_discount_total += (float) $row['discount'];
            // Balance: Invoice Total - Paid - Discount
            $user_balance_total += ((float) $row['invoice_total'] - (float) $row['paid'] - (float) $row['discount']);

            $user_cash_paid += (float) $row['cash_paid'];
            $user_non_cash_paid += (float) $row['non_cash_paid'];
        }

        // Refund (Placeholder as query doesn't fetch refund explicitly yet, can imply or add later)
        $user_refund = 0;
        $net_amount = $user_paid_total - $user_refund; // Net Amount = Collection - Refund
        ?>
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading font-bold" style="border-bottom: 1px solid #ddd;">
                        :: Collection Report of
                        <?php echo $user_name; ?> on
                        <?php echo $from_date; ?>.
                        <span class="pull-right">Generated on :
                            <?php echo date('Y-m-d h:i:s A'); ?>
                        </span>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <!-- Out Patient Details -->
                        <h4 class="font-bold" style="padding: 10px; margin: 0; border-bottom: 1px solid #ddd;">Out Patient
                            Details</h4>

                        <h5 class="font-bold" style="padding: 5px 10px; margin: 0; border-bottom: 1px solid #ddd;">Lab</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th>VisitID</th>
                                        <th>Patient</th>
                                        <th>TestName</th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td>
                                                <?php echo $row['visit_code']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['patient_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo nl2br($row['test_names']); ?>
                                            </td>
                                            <td>
                                                <?php echo app_format_money($row['test_total'], ''); ?>
                                            </td>
                                            <td>
                                                <?php echo $row['remarks']; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="font-bold">
                                        <td>Total :
                                            <?php echo app_format_money($user_test_total, ''); ?>
                                        </td>
                                        <td>Paid :
                                            <?php echo app_format_money($user_paid_total, ''); ?>
                                        </td>
                                        <td>Discount :
                                            <?php echo app_format_money($user_discount_total, ''); ?>
                                        </td>
                                        <td>Balance :
                                            <?php echo app_format_money($user_balance_total, ''); ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Due Collection (Placeholder structure) -->
                        <h4 class="font-bold"
                            style="padding: 10px; margin: 0; border-bottom: 1px solid #ddd; border-top: 1px solid #ddd;">Due
                            Collection</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Last Due</th>
                                        <th>Paid</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- No data for Due Collection in current query scope -->
                                    <tr>
                                        <td></td>
                                        <td class="text-right font-bold">Total</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- In Patient Details (Placeholder structure) -->
                        <h4 class="font-bold"
                            style="padding: 10px; margin: 0; border-bottom: 1px solid #ddd; border-top: 1px solid #ddd;">In
                            Patient Details</h4>
                        <div style="padding: 20px;"></div> <!-- Empty space as per image empty section -->

                        <!-- Footer Payment Splits -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed font-bold" style="margin-bottom: 0;">
                                <tbody>
                                    <tr>
                                        <td style="width: 16%;">Cash Payments : <br>
                                            <?php echo app_format_money($user_cash_paid, ''); ?>
                                        </td>
                                        <td style="width: 16%;">NonCash Payments : <br>
                                            <?php echo app_format_money($user_non_cash_paid, ''); ?>
                                        </td>
                                        <td style="width: 16%;">CashExpense : <br> 0.00</td>
                                        <td style="width: 16%;">NonCash Expense : <br> 0.00</td>
                                        <td style="width: 16%;">Refund : <br>
                                            <?php echo app_format_money($user_refund, ''); ?>
                                        </td>
                                        <td style="width: 20%;">NetAmt : <br>
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
    <?php endforeach; ?>
<?php endif; ?>