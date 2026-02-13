<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-left font-bold" style="text-transform: uppercase;">CONSULTANT DOCTOR WISE DETAIL REPORT</h3>
        <h4 class="text-left font-bold">From:
            <?php echo $from_date; ?> To
            <?php echo $to_date; ?>
        </h4>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-striped" style="font-size: 12px;">
                <thead>
                    <tr style="background-color: #badcfb;">
                        <th>S.No.</th>
                        <th>Consultant</th>
                        <th class="text-center">Reg. Count</th>
                        <th class="text-center">Sub Visit</th>
                        <th class="text-center">After 5PM</th>
                        <th class="text-center">Total Consultation</th>

                        <th class="text-center">Reg. Amt (Gross)</th>
                        <th class="text-center">Sub Visit Amt (Gross)</th>
                        <th class="text-center">After 5PM Amt (Gross)</th>
                        <th class="text-center">Total Consultation Amt (Gross)</th>

                        <th class="text-center">Reg. Refund</th>
                        <th class="text-center">Sub Visit Refund Count</th>
                        <th class="text-center">After 5PM Refund</th>
                        <th class="text-center">Total Refund Count</th>
                        <th class="text-center">Total Refund</th>

                        <th class="text-center">Discount Count</th>
                        <th class="text-center">Discount Amt</th>

                        <th class="text-center">Net</th>
                        <th class="text-center">Net Amt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    // Initialize Grand Totals
                    $gt_reg_count = 0;
                    $gt_sub_visit_count = 0;
                    $gt_after_5pm_count = 0;
                    $gt_total_consultation_count = 0;
                    $gt_reg_amt = 0;
                    $gt_sub_visit_amt = 0;
                    $gt_after_5pm_amt = 0;
                    $gt_total_consultation_amt = 0;
                    $gt_reg_refund = 0;
                    $gt_sub_visit_refund_count = 0;
                    $gt_after_5pm_refund = 0;
                    $gt_total_refund_count = 0;
                    $gt_total_refund = 0;
                    $gt_discount_count = 0;
                    $gt_discount_amt = 0;
                    $gt_net_count = 0;
                    $gt_net_amt = 0;

                    foreach ($report_data as $row):
                        // Counts
                        $reg_count = (int) $row['reg_count'];
                        $sub_visit_count = (int) $row['sub_visit_count'];
                        $after_5pm_count = (int) $row['after_5pm_count'];
                        $total_consultation_count = (int) $row['total_consultation_count'];

                        // Amounts
                        $reg_amt = (float) $row['reg_amount'];
                        $sub_visit_amt = (float) $row['sub_visit_amount'];
                        $after_5pm_amt = (float) $row['after_5pm_amount'];
                        $total_consultation_amt = (float) $row['total_consultation_amount'];

                        // Refunds
                        $reg_refund_count = (int) $row['reg_refund_count'];
                        $sub_visit_refund_count = (int) $row['sub_visit_refund_count'];
                        $after_5pm_refund_count = (int) $row['after_5pm_refund_count'];
                        $total_refund_count = (int) $row['total_refund_count'];
                        $total_refund_amt = (float) $row['total_refund_amount']; // Assume Total Refund Amount provided or sum
                    
                        // Discounts
                        $discount_count = (int) $row['discount_count'];
                        $discount_amt = (float) $row['discount_amount'];

                        // Net (Total Consultation - Total Refund)
                        $net_count = $total_consultation_count - $total_refund_count;
                        $net_amt = $total_consultation_amt - $total_refund_amt - $discount_amt; // Original logic usually Gross - Refund - Discount
                    
                        // Accumulate Totals
                        $gt_reg_count += $reg_count;
                        $gt_sub_visit_count += $sub_visit_count;
                        $gt_after_5pm_count += $after_5pm_count;
                        $gt_total_consultation_count += $total_consultation_count;

                        $gt_reg_amt += $reg_amt;
                        $gt_sub_visit_amt += $sub_visit_amt;
                        $gt_after_5pm_amt += $after_5pm_amt;
                        $gt_total_consultation_amt += $total_consultation_amt;

                        $gt_reg_refund += $reg_refund_count;
                        $gt_sub_visit_refund_count += $sub_visit_refund_count;
                        $gt_after_5pm_refund += $after_5pm_refund_count;
                        $gt_total_refund_count += $total_refund_count;
                        $gt_total_refund += $total_refund_amt;

                        $gt_discount_count += $discount_count;
                        $gt_discount_amt += $discount_amt;

                        $gt_net_count += $net_count;
                        $gt_net_amt += $net_amt;
                        ?>
                        <tr>
                            <td>
                                <?php echo $i++; ?>
                            </td>
                            <td>
                                <?php echo $row['doctor_name']; ?>
                            </td>

                            <td class="text-center">
                                <?php echo $reg_count; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $sub_visit_count; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $after_5pm_count; ?>
                            </td>
                            <td class="text-center font-bold" style="background-color: #f9f9f9;">
                                <?php echo $total_consultation_count; ?>
                            </td>

                            <td class="text-right">
                                <?php echo app_format_money($reg_amt, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($sub_visit_amt, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($after_5pm_amt, ''); ?>
                            </td>
                            <td class="text-right font-bold" style="background-color: #f9f9f9;">
                                <?php echo app_format_money($total_consultation_amt, ''); ?>
                            </td>

                            <td class="text-center">
                                <?php echo $reg_refund_count; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $sub_visit_refund_count; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $after_5pm_refund_count; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $total_refund_count; ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($total_refund_amt, ''); ?>
                            </td>

                            <td class="text-center">
                                <?php echo $discount_count; ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($discount_amt, ''); ?>
                            </td>

                            <td class="text-center font-bold">
                                <?php echo $net_count; ?>
                            </td>
                            <td class="text-right font-bold">
                                <?php echo app_format_money($net_amt, ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-bold" style="background-color: #f0f0f0;">
                        <td colspan="2" class="text-right">Total:</td>
                        <td class="text-center">
                            <?php echo $gt_reg_count; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $gt_sub_visit_count; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $gt_after_5pm_count; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $gt_total_consultation_count; ?>
                        </td>

                        <td class="text-right">
                            <?php echo app_format_money($gt_reg_amt, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($gt_sub_visit_amt, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($gt_after_5pm_amt, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($gt_total_consultation_amt, ''); ?>
                        </td>

                        <td class="text-center">
                            <?php echo $gt_reg_refund; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $gt_sub_visit_refund_count; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $gt_after_5pm_refund; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $gt_total_refund_count; ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($gt_total_refund, ''); ?>
                        </td>

                        <td class="text-center">
                            <?php echo $gt_discount_count; ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($gt_discount_amt, ''); ?>
                        </td>

                        <td class="text-center">
                            <?php echo $gt_net_count; ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($gt_net_amt, ''); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>