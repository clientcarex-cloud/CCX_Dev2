<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-left font-bold" style="text-transform: uppercase;">Dept Collection Department Wise Report</h3>
        <h4 class="text-left font-bold">Report :: From :
            <?php echo $from_date; ?> To :
            <?php echo $to_date; ?>
        </h4>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                    <tr style="background-color: #badcfb;">
                        <th>Sl.No</th>
                        <th>Department Name</th>
                        <th class="text-center">Test Count</th>
                        <th class="text-right">LABORATORY</th>
                        <th class="text-right">Hospital</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $total_lab = 0;
                    $total_hospital = 0;
                    $total_amount = 0;

                    foreach ($report_data as $row):
                        $lab_amt = (float) $row['laboratory_amount'];
                        $hosp_amt = (float) $row['hospital_amount'];
                        $amt = (float) $row['amount'];

                        $total_lab += $lab_amt;
                        $total_hospital += $hosp_amt;
                        $total_amount += $amt;
                        ?>
                        <tr>
                            <td>
                                <?php echo $i++; ?>
                            </td>
                            <td>
                                <?php echo $row['department_name']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $row['test_count']; ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($lab_amt, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($hosp_amt, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($amt, ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-bold" style="background-color: #f0f0f0;">
                        <td colspan="3" class="text-right">Total</td>
                        <td class="text-right">
                            <?php echo app_format_money($total_lab, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($total_hospital, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($total_amount, ''); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>