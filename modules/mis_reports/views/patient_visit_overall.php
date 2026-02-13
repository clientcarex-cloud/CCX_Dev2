<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-center font-bold" style="background-color: #badcfb; padding: 10px; margin-bottom: 0;">Total
            Patient Visits</h3>
    </div>
</div>

<?php
$total_amount_overall = 0;
$total_discount_overall = 0;
$total_paid_overall = 0;
$total_balance_overall = 0;
$total_refund_overall = 0;

$sections = [
    'New Patient' => $new_patients,
    'Old Patient' => $old_patients
];

foreach ($sections as $title => $patients):
    ?>

    <div class="row" style="margin-top: 20px;">
        <div class="col-md-12">
            <h4 class="text-center font-bold" style="background-color: #badcfb; padding: 5px; margin: 0;">
                <?php echo $title; ?>
            </h4>
            <h5 class="font-bold" style="background-color: #f0f0f0; padding: 5px; margin: 0; border: 1px solid #ddd;">
                <?php echo $title; ?> :: Report :: From :
                <?php echo $from_date; ?> To :
                <?php echo $to_date; ?>
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-condensed table-striped">
                    <thead>
                        <tr style="background-color: #badcfb;">
                            <th>Sl.No</th>
                            <th>Date</th>
                            <th>MR No.</th>
                            <th>VisitId</th>
                            <th>Patient Type</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>PhoneNO</th>
                            <th>ReferDoctor</th>
                            <th>Laboratory</th>
                            <th>Tests</th>
                            <th>CollectedBy</th>
                            <th>Amount</th>
                            <th>Discount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>UserName</th>
                            <th>Refund</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $sum_amount = 0;
                        $sum_discount = 0;
                        $sum_paid = 0;
                        $sum_balance = 0;
                        $sum_refund = 0;

                        foreach ($patients as $row):
                            $balance = $row['amount'] - $row['paid'];

                            $sum_amount += $row['amount'];
                            $sum_discount += $row['discount'];
                            $sum_paid += $row['paid'];
                            $sum_balance += $balance;
                            $sum_refund += $row['refund'];
                            ?>
                            <tr>
                                <td>
                                    <?php echo $i++; ?>
                                </td>
                                <td>
                                    <?php echo _d($row['created_at']); ?>
                                </td>
                                <td>
                                    <?php echo $row['mr_number']; ?>
                                </td>
                                <td>
                                    <?php echo $row['visit_code']; ?>
                                </td>
                                <td>
                                    <?php echo $row['visit_type']; ?>
                                </td>
                                <td>
                                    <?php echo $row['patient_name']; ?>
                                </td>
                                <td>
                                    <?php echo $row['age'] . ' ' . $row['age_unit']; ?>
                                </td>
                                <td>
                                    <?php echo $row['gender']; ?>
                                </td>
                                <td>
                                    <?php echo $row['phonenumber']; ?>
                                </td>
                                <td>
                                    <?php echo $row['refer_doctor']; ?>
                                </td>
                                <td>
                                    <?php echo $row['laboratory']; ?>
                                </td>
                                <td>
                                    <?php echo $row['tests']; ?>
                                </td>
                                <td>
                                    <?php echo $row['collected_by']; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo app_format_money($row['amount'], ''); ?>
                                </td>
                                <td class="text-right">
                                    <?php echo app_format_money($row['discount'], ''); ?>
                                </td>
                                <td class="text-right">
                                    <?php echo app_format_money($row['paid'], ''); ?>
                                </td>
                                <td class="text-right">
                                    <?php echo app_format_money($balance, ''); ?>
                                </td>
                                <td>
                                    <?php echo $row['username']; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo app_format_money($row['refund'], ''); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-bold" style="background-color: #f0f0f0;">
                            <td colspan="13" class="text-right">Total</td>
                            <td class="text-right">
                                <?php echo app_format_money($sum_amount, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($sum_discount, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($sum_paid, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($sum_balance, ''); ?>
                            </td>
                            <td></td>
                            <td class="text-right">
                                <?php echo app_format_money($sum_refund, ''); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <?php
    $total_amount_overall += $sum_amount;
    $total_discount_overall += $sum_discount;
    $total_paid_overall += $sum_paid;
    $total_balance_overall += $sum_balance;
    $total_refund_overall += $sum_refund;

endforeach;
?>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-right font-bold">Grand Total</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-condensed">
                <tfoot>
                    <tr class="font-bold" style="background-color: #badcfb;">
                        <td colspan="13" class="text-right">Grand Total</td>
                        <td class="text-right">
                            <?php echo app_format_money($total_amount_overall, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($total_discount_overall, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($total_paid_overall, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($total_balance_overall, ''); ?>
                        </td>
                        <td></td>
                        <td class="text-right">
                            <?php echo app_format_money($total_refund_overall, ''); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>