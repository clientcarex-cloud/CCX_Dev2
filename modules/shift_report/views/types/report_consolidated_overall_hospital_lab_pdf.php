<?php defined('BASEPATH') or exit('No direct script access allowed');
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
        border: 1px solid #000;
        text-align: center;
    }

    td {
        border: 1px solid #000;
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

<table style="width: 100%; border: none;">
    <tr>
        <td style="border: none; width: 70%;">
            <h3 style="margin: 0;">CONSOLIDATED OVERALL (HOSPITAL + LAB) REPORT</h3>
        </td>
        <td style="border: none; width: 30%; text-align: right;">
            <h3 style="margin: 0;">
                <?php echo _d($date); ?>
            </h3>
        </td>
    </tr>
</table>
<br>

<table cellpadding="4">
    <thead>
        <tr>
            <th width="5%" rowspan="2" style="vertical-align: middle;">S.No.</th>
            <th width="20%" rowspan="2" style="vertical-align: middle;">User Name</th>
            <th width="16%" colspan="2">Sales</th>
            <th width="11%" rowspan="2" style="vertical-align: middle;">Total Sales</th>
            <th width="10%" rowspan="2" style="vertical-align: middle;">Refund</th>
            <th width="11%" rowspan="2" style="vertical-align: middle;">Net Amount</th>
            <th width="11%" rowspan="2">Cash In Hand</th>
            <th width="11%" rowspan="2">Online</th>
        </tr>
        <tr>
            <th width="8%">Cash</th>
            <th width="8%">Online</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sno = 1;
        $grand_total_cash = 0;
        $grand_total_online = 0;
        $grand_total_sales = 0;
        $grand_total_refund = 0;
        $grand_net_amount = 0;
        $grand_cash_in_hand = 0;
        $grand_online_final = 0;
        ?>
        <?php if (!empty($report_data)): ?>
            <?php foreach ($report_data as $row): ?>
                <?php
                $total_sales = $row['cash_sales'] + $row['online_sales'];
                $net_amount = $total_sales - $row['refunds'];
                $cash_in_hand = $row['cash_sales'] - $row['refunds'];
                $online_final = $row['online_sales'];

                $grand_total_cash += $row['cash_sales'];
                $grand_total_online += $row['online_sales'];
                $grand_total_sales += $total_sales;
                $grand_total_refund += $row['refunds'];
                $grand_net_amount += $net_amount;
                $grand_cash_in_hand += $cash_in_hand;
                $grand_online_final += $online_final;
                ?>
                <tr>
                    <td class="text-center">
                        <?php echo $sno++; ?>
                    </td>
                    <td>
                        <?php echo $row['staff_name']; ?>
                    </td>
                    <td class="text-right">
                        <?php echo number_format($row['cash_sales'], 2); ?>
                    </td>
                    <td class="text-right">
                        <?php echo number_format($row['online_sales'], 2); ?>
                    </td>
                    <td class="text-right">
                        <?php echo number_format($total_sales, 2); ?>
                    </td>
                    <td class="text-right">
                        <?php echo number_format($row['refunds'], 2); ?>
                    </td>
                    <td class="text-right">
                        <?php echo number_format($net_amount, 2); ?>
                    </td>
                    <td class="text-right">
                        <?php echo number_format($cash_in_hand, 2); ?>
                    </td>
                    <td class="text-right">
                        <?php echo number_format($online_final, 2); ?>
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
        <tr style="font-weight: bold;">
            <td colspan="2" class="text-right">Total:</td>
            <td class="text-right">
                <?php echo number_format($grand_total_cash, 2); ?>
            </td>
            <td class="text-right">
                <?php echo number_format($grand_total_online, 2); ?>
            </td>
            <td class="text-right">
                <?php echo number_format($grand_total_sales, 2); ?>
            </td>
            <td class="text-right">
                <?php echo number_format($grand_total_refund, 2); ?>
            </td>
            <td class="text-right">
                <?php echo number_format($grand_net_amount, 2); ?>
            </td>
            <td class="text-right">
                <?php echo number_format($grand_cash_in_hand, 2); ?>
            </td>
            <td class="text-right">
                <?php echo number_format($grand_online_final, 2); ?>
            </td>
        </tr>
    </tfoot>
</table>

<?php
$content = ob_get_clean();
$this->writeHTML($content, true, false, true, false, '');
?>