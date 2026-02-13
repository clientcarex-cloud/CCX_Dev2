<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h2 style="text-align: center; background-color: #badcfb; padding: 10px;">Total Patient Visits</h2>

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

    <h3 style="text-align: center; background-color: #badcfb; padding: 5px;">
        <?php echo $title; ?>
    </h3>
    <h4 style="background-color: #f0f0f0; padding: 5px; border: 1px solid #ddd;">
        <?php echo $title; ?> :: Report :: From :
        <?php echo $from_date; ?> To :
        <?php echo $to_date; ?>
    </h4>

    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #badcfb;">
                <th width="4%">Sl.No</th>
                <th width="8%">Date</th>
                <!-- <th width="8%">MR No.</th> -->
                <!-- <th width="8%">VisitId</th> -->
                <th width="8%">Type</th>
                <th width="12%">Name</th>
                <!-- <th width="5%">Age</th> -->
                <!-- <th width="5%">Gen</th> -->
                <!-- <th width="8%">Phone</th> -->
                <!-- <th width="10%">Doctor</th> -->
                <!-- <th width="12%">Tests</th> -->
                <th width="7%">Amount</th>
                <!-- <th width="5%">Disc</th> -->
                <th width="7%">Paid</th>
                <th width="7%">Bal</th>
                <!-- <th width="8%">User</th> -->
                <th width="7%">Refund</th>
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
                    <td width="4%">
                        <?php echo $i++; ?>
                    </td>
                    <td width="8%">
                        <?php echo _d($row['created_at']); ?>
                    </td>
                    <!-- <td width="8%"><?php echo $row['mr_number']; ?></td> -->
                    <!-- <td width="8%"><?php echo $row['visit_code']; ?></td> -->
                    <td width="8%">
                        <?php echo $row['visit_type']; ?>
                    </td>
                    <td width="12%">
                        <?php echo $row['patient_name']; ?>
                    </td>
                    <!-- <td width="5%"><?php echo $row['age']; ?></td> -->
                    <!-- <td width="5%"><?php echo $row['gender']; ?></td> -->
                    <!-- <td width="8%"><?php echo $row['phonenumber']; ?></td> -->
                    <!-- <td width="10%"><?php echo $row['refer_doctor']; ?></td> -->
                    <!-- <td width="12%"><?php echo $row['tests']; ?></td> -->
                    <td width="7%" align="right">
                        <?php echo app_format_money($row['amount'], ''); ?>
                    </td>
                    <!-- <td width="5%" align="right"><?php echo app_format_money($row['discount'], ''); ?></td> -->
                    <td width="7%" align="right">
                        <?php echo app_format_money($row['paid'], ''); ?>
                    </td>
                    <td width="7%" align="right">
                        <?php echo app_format_money($balance, ''); ?>
                    </td>
                    <!-- <td width="8%"><?php echo $row['username']; ?></td> -->
                    <td width="7%" align="right">
                        <?php echo app_format_money($row['refund'], ''); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="4" align="right">Total</td> <!-- Adjusted colspan -->
                <td align="right">
                    <?php echo app_format_money($sum_amount, ''); ?>
                </td>
                <!-- <td align="right"><?php echo app_format_money($sum_discount, ''); ?></td> -->
                <td align="right">
                    <?php echo app_format_money($sum_paid, ''); ?>
                </td>
                <td align="right">
                    <?php echo app_format_money($sum_balance, ''); ?>
                </td>
                <td align="right">
                    <?php echo app_format_money($sum_refund, ''); ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <br><br>

    <?php
    $total_amount_overall += $sum_amount;
    $total_discount_overall += $sum_discount;
    $total_paid_overall += $sum_paid;
    $total_balance_overall += $sum_balance;
    $total_refund_overall += $sum_refund;

endforeach;
?>

<h4 style="text-align: right;">Grand Total</h4>
<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <tfoot>
        <tr style="background-color: #badcfb; font-weight: bold;">
            <td width="50%" align="right">Grand Total</td> <!-- Adjusted width/colspan -->
            <td width="7%" align="right">
                <?php echo app_format_money($total_amount_overall, ''); ?>
            </td>
            <!-- <td width="5%" align="right"><?php echo app_format_money($total_discount_overall, ''); ?></td> -->
            <td width="7%" align="right">
                <?php echo app_format_money($total_paid_overall, ''); ?>
            </td>
            <td width="7%" align="right">
                <?php echo app_format_money($total_balance_overall, ''); ?>
            </td>
            <td width="7%" align="right">
                <?php echo app_format_money($total_refund_overall, ''); ?>
            </td>
        </tr>
    </tfoot>
</table>
<br>
<small>Note: Some columns hidden in PDF for better fit. Please use Landscape mode or Print view for full
    details.</small>