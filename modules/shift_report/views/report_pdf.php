<?php defined('BASEPATH') or exit('No direct script access allowed');

// The App_pdf class includes this file, so $this refers to the PDF instance.
// We need to capture the HTML and pass it to writeHTML.
ob_start();
?>
<style type="text/css">
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        font-weight: bold;
        background-color: #f0f0f0;
        border-bottom: 1px solid #000;
    }

    td {
        border-bottom: 1px solid #ccc;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .font-weight-bold {
        font-weight: bold;
    }
</style>

<h3 class="text-center">HOSPITAL Shift Report of
    <?php echo isset($staff_details) ? $staff_details->firstname . ' ' . $staff_details->lastname : ''; ?> on
    <?php echo $date; ?>
</h3>

<h4>OP Patients</h4>
<table cellpadding="5">
    <thead>
        <tr>
            <th width="5%">S.No</th>
            <th width="12%">Bill No.</th>
            <th width="15%">Time</th>
            <th width="8%">Mode</th>
            <th width="10%">Cash</th>
            <th width="10%">Online</th>
            <th width="10%">MR.No.</th>
            <th width="12%">Visit Id</th>
            <th width="18%">Patient</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_op_sales = 0;
        $total_cash = 0;
        $total_online = 0;
        $sno = 1;
        ?>
        <?php if (!empty($report_data['op_sales'])): ?>
            <?php foreach ($report_data['op_sales'] as $row): ?>
                <?php
                $is_cash = (stripos($row['payment_mode'], 'Cash') !== false);
                $cash_amount = $is_cash ? $row['paid_amount'] : 0.00;
                $online_amount = !$is_cash ? $row['paid_amount'] : 0.00;

                $total_op_sales += $row['paid_amount'];
                $total_cash += $cash_amount;
                $total_online += $online_amount;
                ?>
                <tr>
                    <td width="5%">
                        <?php echo $sno++; ?>
                    </td>
                    <td width="12%">
                        <?php echo $row['bill_no']; ?>
                    </td>
                    <td width="15%">
                        <?php echo date('h:i A', strtotime($row['created_at'])); ?>
                    </td>
                    <td width="8%">
                        <?php echo $row['payment_mode'] ? $row['payment_mode'] : 'Unpaid'; ?>
                    </td>
                    <td width="10%">
                        <?php echo number_format($cash_amount, 2); ?>
                    </td>
                    <td width="10%">
                        <?php echo number_format($online_amount, 2); ?>
                    </td>
                    <td width="10%">
                        <?php echo $row['mr_number']; ?>
                    </td>
                    <td width="12%">
                        <?php echo $row['visit_code']; ?>
                    </td>
                    <td width="18%">
                        <?php echo $row['patient_name']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center">No records found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="text-right font-weight-bold">Total OP Sales:</td>
            <td colspan="5" class="font-weight-bold text-left">
                <?php echo number_format($total_op_sales, 2); ?>
            </td>
        </tr>
    </tfoot>
</table>

<br><br>

<h4>HOSPITAL Cancellations</h4>
<table cellpadding="5">
    <thead>
        <tr>
            <th width="5%">S.No</th>
            <th width="20%">Bill No.</th>
            <th width="20%">Time</th>
            <th width="20%">MR.No.</th>
            <th width="20%">Amount</th>
            <th width="15%">Refund</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_refund = 0;
        $c_sno = 1;
        ?>
        <?php if (!empty($report_data['cancellations'])): ?>
            <?php foreach ($report_data['cancellations'] as $cancel): ?>
                <?php
                $refund_amount = $cancel['invoice_amount'];
                $total_refund += $refund_amount;
                ?>
                <tr>
                    <td>
                        <?php echo $c_sno++; ?>
                    </td>
                    <td>
                        <?php echo $cancel['bill_no']; ?>
                    </td>
                    <td>
                        <?php echo date('h:i A', strtotime($cancel['created_at'])); ?>
                    </td>
                    <td>
                        <?php echo $cancel['mr_number']; ?>
                    </td>
                    <td>
                        <?php echo number_format($cancel['invoice_amount'], 2); ?>
                    </td>
                    <td>
                        <?php echo number_format($refund_amount, 2); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No cancellations.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<br><br>

<h4>Summary</h4>
<table cellpadding="5" border="1" style="width: 50%;">
    <tbody>
        <tr>
            <th class="font-weight-bold">Cash Total</th>
            <td class="text-right">
                <?php echo number_format($total_cash, 2); ?>
            </td>
        </tr>
        <tr>
            <th class="font-weight-bold">Non Cash Total</th>
            <td class="text-right">
                <?php echo number_format($total_online, 2); ?>
            </td>
        </tr>
        <tr>
            <th class="font-weight-bold">Total Collection</th>
            <td class="text-right">
                <?php echo number_format($total_cash + $total_online, 2); ?>
            </td>
        </tr>
        <tr>
            <th class="font-weight-bold">Refund</th>
            <td class="text-right">
                <?php echo number_format($total_refund, 2); ?>
            </td>
        </tr>
        <tr>
            <th class="font-weight-bold">Net Amount</th>
            <td class="text-right">
                <?php echo number_format(($total_cash + $total_online) - $total_refund, 2); ?>
            </td>
        </tr>
        <tr>
            <th class="font-weight-bold">Cash In Hand</th>
            <td class="text-right">
                <?php echo number_format($total_cash - $total_refund, 2); ?>
            </td>
        </tr>
        <tr>
            <th class="font-weight-bold">UPI</th>
            <td class="text-right">
                <?php echo number_format($total_online, 2); ?>
            </td>
        </tr>
    </tbody>
</table>
<?php
$content = ob_get_clean();
// $this refers to App_pdf instance which extends TCPDF
$this->writeHTML($content, true, false, true, false, '');
?>