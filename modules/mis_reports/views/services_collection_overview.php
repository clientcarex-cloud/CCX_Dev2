<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-center font-bold" style="background-color: #badcfb;">Service Collection Report</h3>
        <h4 class="font-bold">Report :: From :
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
                    <tr style="background-color: #f0f0f0;">
                        <th>Sl.No</th>
                        <th>ID</th>
                        <th>ServiceName</th>
                        <th class="text-center">Count</th>
                        <th class="text-center">Patients</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $total_count = 0;
                    $total_patients = 0;
                    $total_amount = 0;

                    foreach ($report_data as $row):
                        $count = (int) $row['usage_count'];
                        $patients = (int) $row['patient_count'];
                        $amount = (float) $row['amount'];

                        $total_count += $count;
                        $total_patients += $patients;
                        $total_amount += $amount;
                        ?>
                        <tr>
                            <td>
                                <?php echo $i++; ?>
                            </td>
                            <td>
                                <?php echo $row['service_id']; ?>
                            </td>
                            <td>
                                <?php echo $row['service_name']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $count; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $patients; ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($amount, ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-bold">
                        <td colspan="3" class="text-right">Total</td>
                        <td class="text-center">
                            <?php echo $total_count; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $total_patients; ?>
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