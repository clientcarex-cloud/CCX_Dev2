<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Filters tabs -->
                <div class="_filters _hidden_inputs hidden">
                    <?php echo form_hidden('status', $filters['status']); ?>
                </div>

                <div class="panel_s">
                    <div class="panel-body">

                        <div class="row mbot15">
                            <div class="col-md-12">
                                <div class="btn-group pull-left">
                                    <a href="<?php echo admin_url('manage_payments?status=all'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'all') ? 'btn-info' : 'btn-default'; ?>">All</a>
                                    <a href="<?php echo admin_url('manage_payments?status=dues'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'dues') ? 'btn-info' : 'btn-default'; ?>">Dues</a>
                                    <a href="<?php echo admin_url('manage_payments?status=partial'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'partial') ? 'btn-info' : 'btn-default'; ?>">Partial</a>
                                    <a href="<?php echo admin_url('manage_payments?status=refund'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'refund') ? 'btn-info' : 'btn-default'; ?>">Refund</a>
                                    <a href="<?php echo admin_url('manage_payments?status=paid'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'paid') ? 'btn-info' : 'btn-default'; ?>">Paid</a>
                                </div>
                                <div class="pull-right">
                                    <!-- Print/Export can be DataTables buttons -->
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <!-- Search input handled by DataTables usually, but image shows specific inputs -->
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('date', '', $filters['date'], ['placeholder' => 'Today\'s']); ?>
                            </div>
                            <div class="col-md-3">
                                <select name="user_id" class="selectpicker" data-width="100%"
                                    data-none-selected-text="Select User" data-live-search="true">
                                    <option value=""></option>
                                    <?php foreach ($staff as $s) { ?>
                                        <option value="<?php echo $s['staffid']; ?>" <?php if ($filters['user_id'] == $s['staffid']) {
                                               echo 'selected';
                                           } ?>>
                                            <?php echo $s['firstname'] . ' ' . $s['lastname']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success" onclick="apply_filters();">Filter</button>
                            </div>
                        </div>

                        <div class="clearfix mtop20"></div>

                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="0" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th>Sno.</th>
                                        <th>Patients</th>
                                        <th>Lab Test</th>
                                        <th>Invoices & Receipts</th>
                                        <th>Ref Dr.</th>
                                        <th>Total(₹)</th>
                                        <th>Discount</th>
                                        <th>Net Total</th>
                                        <th>Paid</th>
                                        <th>Refund</th>
                                        <th>Total Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $sum_total = 0;
                                    $sum_discount = 0;
                                    $sum_net = 0;
                                    $sum_paid = 0;
                                    $sum_refund = 0;
                                    $sum_due = 0;
                                    foreach ($payments as $p) {
                                        $sum_total += $p['subtotal']; // Using subtotal as 'Total' before discount? Image shows Total, Discount, Net Total (Net = Total - Discount). Perfex 'total' is final. 'subtotal' is before. 
                                        // Wait, Perfex: subtotal (before tax/disc), total (final). 
                                        // Let's assume Subtotal is 'Total' column, then Discount, then Net Total (Total - Discount).
                                        // But Perfex logic includes Tax. Simple: use subtotal.
                                        $sum_discount += $p['discount'];
                                        $sum_net += $p['total'];
                                        $sum_paid += $p['paid_amount'];
                                        $sum_refund += $p['refund_amount'];
                                        $sum_due += $p['due_amount'];
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>
                                                <span class="bold"><?php echo $p['patient_name']; ?></span><br>
                                                <span class="text-muted text-xs"><?php echo _dt($p['visit_date']); ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                // Show first 2 tests
                                                $count = 0;
                                                foreach ($p['tests'] as $test) {
                                                    if ($count < 2) {
                                                        echo $test['description'] . '<br>';
                                                    }
                                                    $count++;
                                                }
                                                if ($count > 0) {
                                                    echo '<span class="text-xs">Total: ' . $count . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="text-xs">Invoices: <?php echo ($p['invoice_id'] ? 1 : 0); ?></span><br>
                                                <span class="text-xs">Receipts: <?php echo $p['receipt_count']; ?></span>
                                            </td>
                                            <td><span class="bold uppercase"><?php echo $p['doctor_name']; ?></span></td>
                                            <td><?php echo app_format_money($p['subtotal'], ''); ?></td>
                                            <td><?php echo app_format_money($p['discount'], ''); ?></td>
                                            <td><?php echo app_format_money($p['total'], ''); ?></td>
                                            <td><?php echo app_format_money($p['paid_amount'], ''); ?></td>
                                            <td><?php echo app_format_money($p['refund_amount'], ''); ?></td>
                                            <td><?php echo app_format_money($p['due_amount'], ''); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f0f4f7; font-weight: bold;">
                                        <td colspan="5" class="text-right"></td>
                                        <td><?php echo app_format_money($sum_total, ''); ?></td>
                                        <td><?php echo app_format_money($sum_discount, ''); ?></td>
                                        <td><?php echo app_format_money($sum_net, ''); ?></td>
                                        <td><?php echo app_format_money($sum_paid, ''); ?></td>
                                        <td><?php echo app_format_money($sum_refund, ''); ?></td>
                                        <td><?php echo app_format_money($sum_due, ''); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    function apply_filters() {
        var date = $('input[name="date"]').val();
        var user_id = $('select[name="user_id"]').val();
        var status = '<?php echo $filters['status']; ?>';
        window.location.href = admin_url + 'manage_payments?status=' + status + '&date=' + date + '&user_id=' + user_id;
    }
</script>