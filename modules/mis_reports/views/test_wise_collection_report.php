<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-left font-bold" style="text-transform: uppercase;">TEST WISE COLLECTION</h3>
        <h4 class="text-left font-bold">From:
            <?php echo $from_date; ?> To
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
                        <th>S.No.</th>
                        <th>Name</th>
                        <th class="text-center">Test Count</th>
                        <th class="text-center">Gross Amt</th>
                        <th class="text-center">Refunded</th>
                        <th class="text-center">Net Count</th>
                        <th class="text-center">Discount Amt</th>
                        <th class="text-center">Refunded Amt</th>
                        <th class="text-center">Net Amt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $sum_test_count = 0;
                    $sum_gross_amt = 0;
                    $sum_refunded = 0;
                    $sum_net_count = 0;
                    $sum_discount_amt = 0; // Currently 0 based on query
                    $sum_refunded_amt = 0;
                    $sum_net_amt = 0;

                    foreach ($report_data as $row):
                        $test_count = (int) $row['test_count'];
                        $gross_amt = (float) $row['gross_amount'];
                        $refunded = (int) $row['refunded_count'];
                        $refunded_amt = (float) $row['refunded_amount'];

                        $net_count = $test_count - $refunded;
                        $net_amt = $gross_amt - $refunded_amt; // Assuming Net Amt matches Image logic (Gross - Refund)
                    
                        // Accumulate Totals
                        $sum_test_count += $test_count;
                        $sum_gross_amt += $gross_amt;
                        $sum_refunded += $refunded;
                        $sum_net_count += $net_count;
                        $sum_refunded_amt += $refunded_amt;
                        $sum_net_amt += $net_amt;
                        ?>
                        <tr>
                            <td>
                                <?php echo $i++; ?>
                            </td>
                            <td>
                                <?php echo $row['test_name']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $test_count; ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($gross_amt, ''); ?>
                            </td>
                            <td class="text-center">
                                <?php echo $refunded; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $net_count; ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money(0, ''); ?>
                            </td> <!-- Discount not per test line in query yet -->
                            <td class="text-right">
                                <?php echo app_format_money($refunded_amt, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($net_amt, ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-bold" style="background-color: #f0f0f0;">
                        <td colspan="2" class="text-right">Total:</td>
                        <td class="text-center">
                            <?php echo $sum_test_count; ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($sum_gross_amt, ''); ?>
                        </td>
                        <td class="text-center">
                            <?php echo $sum_refunded; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $sum_net_count; ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($sum_discount_amt, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($sum_refunded_amt, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($sum_net_amt, ''); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>