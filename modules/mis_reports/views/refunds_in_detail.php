<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <p class="text-info bold" style="font-size: 16px;">
            Refunds In Detail From:
            <?php echo $from_date; ?> To:
            <?php echo $to_date; ?>
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="bold">Sl.No</th>
                        <th class="bold">Date</th>
                        <th class="bold">Visit No</th>
                        <th class="bold">MR No</th>
                        <th class="bold">Patient Name</th>
                        <th class="bold">Item Group</th>
                        <th class="bold">Item Name</th>
                        <th class="bold">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $total_amount = 0;
                    if (!empty($report_data)) {
                        foreach ($report_data as $row) {
                            $total_amount += $row['amount'];
                            ?>
                            <tr>
                                <td>
                                    <?php echo $i++; ?>
                                </td>
                                <td>
                                    <?php echo _d($row['date']); ?>
                                </td>
                                <td>
                                    <?php echo $row['visit_code']; ?>
                                </td>
                                <td>
                                    <?php echo $row['mr_number']; ?>
                                </td>
                                <td>
                                    <?php echo $row['patient_name']; ?>
                                </td>
                                <td>
                                    <?php echo $row['item_group']; ?>
                                </td>
                                <td>
                                    <?php echo $row['item_name']; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_format($row['amount'], 2); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr style="background-color: #f0f0f0; font-weight: bold;">
                            <td colspan="7" class="text-right">Total</td>
                            <td class="text-right">
                                <?php echo number_format($total_amount, 2); ?>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="text-center">No records found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>