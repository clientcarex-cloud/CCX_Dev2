<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-center font-bold" style="background-color: #badcfb;">Test Collection</h3>
    </div>
</div>

<?php
$current_test = '';
$i = 1;
$test_total_lab = 0;
$test_total_hospital = 0;
$test_total_amount = 0;
// We need to group by test name first
$tests = [];
foreach ($report_data as $row) {
    $tests[$row['test_name']][] = $row;
}

$test_number = 1;
?>

<?php foreach ($tests as $test_name => $rows): ?>
    <div class="row" style="margin-top: 20px;">
        <div class="col-md-12">
            <h4 class="font-bold">
                <?php echo $test_number++; ?>.
                <?php echo $test_name; ?>
            </h4>
            <h5 class="font-bold">Report :: From :
                <?php echo $from_date; ?> To :
                <?php echo $to_date; ?>
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
                    <thead>
                        <tr style="background-color: #badcfb;">
                            <th>Sl.No</th>
                            <th>Visit-Id</th>
                            <th>Date</th>
                            <th>Patient Name</th>
                            <th>Phone No</th>
                            <th>Email Id</th>
                            <th>Tests</th>
                            <th class="text-right">LABORATORY</th>
                            <th class="text-right">Hospital</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sub_i = 1;
                        $sub_lab = 0;
                        $sub_hosp = 0;
                        $sub_total = 0;

                        foreach ($rows as $row):
                            $amount = (float) $row['amount'];
                            $sub_lab += $amount;
                            $sub_total += $amount;
                            ?>
                            <tr>
                                <td>
                                    <?php echo $sub_i++; ?>
                                </td>
                                <td>
                                    <?php echo $row['visit_code']; ?>
                                </td>
                                <td>
                                    <?php echo _d($row['test_date']); ?>
                                </td>
                                <td>
                                    <?php echo $row['patient_name']; ?>
                                </td>
                                <td>
                                    <?php echo $row['phonenumber']; ?>
                                </td>
                                <td></td> <!-- Email Id Placeholder -->
                                <td></td> <!-- Tests Placeholder -->
                                <td class="text-right">
                                    <?php echo app_format_money($amount, ''); ?>
                                </td>
                                <td class="text-right">0</td>
                                <td class="text-right">
                                    <?php echo app_format_money($amount, ''); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-bold">
                            <td colspan="7" class="text-right">Total</td>
                            <td class="text-right">
                                <?php echo app_format_money($sub_lab, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($sub_hosp, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($sub_total, ''); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php endforeach; ?>