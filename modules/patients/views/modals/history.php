<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="history_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">History</h4>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#history_receipts" aria-controls="history_receipts" role="tab"
                            data-toggle="tab">Receipts</a>
                    </li>
                    <li role="presentation">
                        <a href="#history_activity_logs" aria-controls="history_activity_logs" role="tab"
                            data-toggle="tab">Activity Logs</a>
                    </li>
                </ul>

                <!-- Tab Panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="history_receipts">
                        <div class="row mtop15">
                            <div class="col-md-12">
                                <?php if (!empty($payments)) { ?>
                                    <table class="table dt-table table-hover" data-order-col="0" data-order-type="desc">
                                        <thead>
                                            <tr>
                                                <th>Receipt #</th>
                                                <th>Mode</th>
                                                <th>Date</th>
                                                <th class="text-right">Amount</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($payments as $payment) { ?>
                                                <tr>
                                                    <td><?php echo $payment['paymentid']; ?></td>
                                                    <td><?php echo $payment['name']; ?></td>
                                                    <td><?php echo _dt($payment['daterecorded']); ?></td>
                                                    <td class="text-right">
                                                        <?php echo app_format_money($payment['amount'], $invoice->currency_name); ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="#"
                                                            onclick="var i=document.createElement('iframe');i.style.display='none';i.src='<?php echo admin_url('patients/visits/print_receipt/' . $payment['paymentid']); ?>';document.body.appendChild(i);return false;"
                                                            class="btn btn-default btn-icon">
                                                            <i class="fa fa-print"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <p class="text-muted">No receipts found for this invoice.</p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="history_activity_logs">
                        <div class="row mtop15">
                            <div class="col-md-12">
                                <?php if (!empty($activity_log)) { ?>
                                    <table class="table dt-table table-hover" data-order-col="0" data-order-type="desc">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Staff</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($activity_log as $log) { ?>
                                                <tr>
                                                    <td><?php echo _dt($log['date']); ?></td>
                                                    <td><?php echo $log['staff_name']; ?></td>
                                                    <td><?php echo $log['description']; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <p class="text-muted">No activity logs found for this patient.</p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>