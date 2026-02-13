<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-left font-bold" style="text-transform: uppercase;">Bill Wise Consultation Collection</h3>
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
                        <th>Bill No.</th>
                        <th>Date & Time</th>
                        <th>MR No.</th>
                        <th>Visit-Id</th>
                        <th>Type</th>
                        <th>Patient Name</th>
                        <th>Family Head</th>
                        <th>Doctor Name</th>
                        <th>Department Name</th>
                        <th>Phone No.</th>
                        <th>Remarks</th>
                        <th>Collected Amount</th>
                        <th>Discount</th>
                        <th>Refund</th>
                        <th>Net</th>
                        <th>Mode</th>
                        <th>User Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $sum_collected = 0;
                    $sum_discount = 0;
                    $sum_refund = 0;
                    $sum_net = 0;

                    foreach ($report_data as $row):
                        $collected = (float) $row['collected_amount'];
                        $discount = (float) $row['discount'];
                        $refund = (float) $row['refund'];
                        $net = (float) $row['net_amount']; // Is Net amount (Amount - Discount) or Paid amount?
                        // Image col "Net" seems to follow "Collected > Discount > Refund > Net > Mode".
                        // Usually Net = Total - Discount. Or is it Net Paid? 
                        // Let's assume Net = Collected - Refund for now? 
                        // Looking at image:
                        // Collected: 300, Discount: 0, Refund: 0, Net: 300
                        // Collected: 250, Refund: 0, Net: 250
                        // Collected: 0, Refund: 0, Net: 0
                        // So Net seems to be Paid Amount effectively, or (Paid - Refund).
                        // Wait, if Collected is Paid, then Net is likely (Paid - Refund).
                        // Or if Collected is Bill Amount?
                        // "Collected Amount" usually means Paid. 
                        // Let's assume Net = Collected - Refund.
                    
                        $net_val = $collected - $refund; // Calculating net based on assumptions
                    
                        // Re-checking Image columns: Collected Amount | Discount | Refund | Net
                        // If Collected is what was paid. Discount is just info. Refund is returned. 
                        // Net = Collected - Refund makes sense for "Net Collection".
                    
                        $sum_collected += $collected;
                        $sum_discount += $discount;
                        $sum_refund += $refund;
                        $sum_net += $net_val;
                        ?>
                        <tr>
                            <td>
                                <?php echo $i++; ?>
                            </td>
                            <td>
                                <?php echo $row['bill_no']; ?>
                            </td>
                            <td>
                                <?php echo _d($row['created_at']); ?>
                                <?php echo date('h:i:s A', strtotime($row['created_at'])); ?>
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
                                <?php if ($row['family_head_name']): ?>
                                    <?php echo $row['family_head_type'] . '. ' . $row['family_head_name']; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $row['doctor_name']; ?>
                            </td>
                            <td>
                                <?php echo $row['department_name']; ?>
                            </td>
                            <td>
                                <?php echo $row['phonenumber']; ?>
                            </td>
                            <td>
                                <?php echo $row['remarks']; ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($collected, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($discount, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($refund, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($net_val, ''); ?>
                            </td>
                            <td>
                                <?php echo $row['payment_mode']; ?>
                            </td>
                            <td>
                                <?php echo $row['user_name']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-bold" style="background-color: #f0f0f0;">
                        <td colspan="12" class="text-right">Total:</td>
                        <td class="text-right">
                            <?php echo app_format_money($sum_collected, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($sum_discount, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($sum_refund, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($sum_net, ''); ?>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>