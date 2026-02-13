<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-center font-bold" style="background-color: #badcfb;">Refund Patient Report</h3>
        <h4 class="font-bold">Report :: From :
            <?php echo $from_date; ?> To :
            <?php echo $to_date; ?>
        </h4>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
                <thead>
                    <tr style="background-color: #badcfb;">
                        <th>Sl.No</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Discount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th class="text-right">Refund</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Aggregate data by Patient
                    $patients = [];
                    foreach ($report_data as $row) {
                        $name = $row['patient_name'];
                        if (!isset($patients[$name])) {
                            $patients[$name] = [
                                'amount' => 0, // Bill Amount
                                'discount' => 0,
                                'paid' => 0,
                                'refund' => 0,
                                'invoices' => []
                            ];
                        }

                        // Add Refund Amount (Always additive)
                        $patients[$name]['refund'] += (float) $row['refund_amount'];

                        // Add Invoice Details ONLY if not already added for this patient
                        // The query returns one row per Refund.
                        // If multiple refunds share an invoice, we shouldn't double count invoice total.
                        $inv_id = $row['invoice_id'];
                        if (!in_array($inv_id, $patients[$name]['invoices'])) {
                            $patients[$name]['invoices'][] = $inv_id;
                            $patients[$name]['amount'] += (float) $row['invoice_amount'];
                            $patients[$name]['discount'] += (float) $row['discount'];
                            $patients[$name]['paid'] += (float) $row['paid_amount'];
                        }
                    }

                    $i = 1;
                    $total_amount = 0;
                    $total_discount = 0;
                    $total_paid = 0;
                    $total_refund = 0;
                    $total_balance = 0;

                    foreach ($patients as $name => $data):
                        // Calculate Balance: Amount - Discount - Paid - Refund?
                        // Usually Balance = Amount - Discount - Paid.
                        // But if refunded, does it affect balance?
                        // If I paid 100, and got 50 Refund.
                        // Amount 100. Paid 100. Refund 50.
                        // Balance usually means "Amount Due".
                        // Logic: (Amount - Discount) - (Paid - Refunded)?
                        // Or just Amount - Discount - Paid.
                        // Let's assume Balance is simply (Amount - Discount - Paid).
                        // If a refund happens, it's usually returns cash.
                        // The image shows "Balance" column mostly empty.
                        // Let's use simple logic.
                        $balance = $data['amount'] - $data['discount'] - $data['paid'];

                        $total_amount += $data['amount'];
                        $total_discount += $data['discount'];
                        $total_paid += $data['paid'];
                        $total_balance += $balance;
                        $total_refund += $data['refund'];
                        ?>
                        <tr>
                            <td>
                                <?php echo $i++; ?>
                            </td>
                            <td>
                                <?php echo $name; ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($data['amount'], ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($data['discount'], ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($data['paid'], ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($balance, ''); ?>
                            </td>
                            <td class="text-right">
                                <?php echo app_format_money($data['refund'], ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-bold">
                        <td colspan="2" class="text-right">Total</td>
                        <td class="text-right">
                            <?php echo app_format_money($total_amount, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($total_discount, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($total_paid, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($total_balance, ''); ?>
                        </td>
                        <td class="text-right">
                            <?php echo app_format_money($total_refund, ''); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>