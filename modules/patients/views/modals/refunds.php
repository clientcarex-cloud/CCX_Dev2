<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="refunds_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Refunds</h4>
            </div>
            <!-- Common Hidden Inputs -->
            <input type="hidden" id="refund_invoice_id" value="<?php echo isset($invoice_id) ? $invoice_id : ''; ?>">

            <div class="modal-body">
                <div class="horizontal-scrollable-tabs preview-tabs-top">
                    <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                    <div class="horizontal-tabs">
                        <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#refund_cancellation" aria-controls="refund_cancellation" role="tab"
                                    data-toggle="tab">Refund & Cancellation</a>
                            </li>
                            <li role="presentation">
                                <a href="#only_refund" aria-controls="only_refund" role="tab" data-toggle="tab">Only
                                    Refund</a>
                            </li>
                            <li role="presentation">
                                <a href="#only_cancellation" aria-controls="only_cancellation" role="tab"
                                    data-toggle="tab">Only Cancellation</a>
                            </li>
                            <li role="presentation">
                                <a href="#refunds_receipts" aria-controls="refunds_receipts" role="tab"
                                    data-toggle="tab">Refunds Receipts</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content">
                    <!-- Tab 1: Refund & Cancellation -->
                    <div role="tabpanel" class="tab-pane active" id="refund_cancellation">
                        <div class="table-responsive">
                            <table class="table table-bordered refund-items-table" id="table_rc">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Item Name</th>
                                        <th>Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Refund Amount</label>
                                    <input type="number" class="form-control" id="rc_amount" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Mode</label>
                                    <select class="form-control" id="rc_mode"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Refunded On</label>
                                    <input type="date" class="form-control" id="rc_date"
                                        value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea class="form-control" id="rc_note"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-info pull-right save-refund-btn"
                            data-type="refund_cancellation">Save Refund & Cancel Items</button>
                    </div>

                    <!-- Tab 2: Only Refund -->
                    <div role="tabpanel" class="tab-pane" id="only_refund">
                        <div class="alert alert-info">Select items to help calculate refund amount. Items status will
                            <b>NOT</b> be changed.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered refund-items-table" id="table_or">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Item Name</th>
                                        <th>Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Refund Amount</label>
                                    <input type="number" class="form-control" id="or_amount" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Mode</label>
                                    <select class="form-control" id="or_mode"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Refunded On</label>
                                    <input type="date" class="form-control" id="or_date"
                                        value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea class="form-control" id="or_note"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-info pull-right save-refund-btn"
                            data-type="only_refund">Save Refund Only</button>
                    </div>

                    <!-- Tab 3: Only Cancellation -->
                    <div role="tabpanel" class="tab-pane" id="only_cancellation">
                        <div class="alert alert-warning">Select items to Cancel. No refund will be processed. Items
                            status <b>WILL</b> be changed to Cancelled.</div>
                        <div class="table-responsive">
                            <table class="table table-bordered refund-items-table" id="table_oc">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Item Name</th>
                                        <th>Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="row">
                            <!-- Hidden Amount 0 -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remarks / Reason</label>
                                    <textarea class="form-control" id="oc_note"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger pull-right save-refund-btn"
                            data-type="only_cancellation">Cancel Items (No Refund)</button>
                    </div>

                    <!-- Tab 4: Refunds Receipts -->
                    <div role="tabpanel" class="tab-pane" id="refunds_receipts">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Payment Mode</th>
                                        <th>Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($refunds) && count($refunds) > 0): ?>
                                        <?php foreach ($refunds as $refund): ?>
                                            <tr>
                                                <td><?php echo _d($refund['refunded_on']); ?></td>
                                                <td><?php echo app_format_money($refund['amount'], get_base_currency()); ?></td>
                                                <td>
                                                    <?php
                                                    $type_label = '';
                                                    if ($refund['refund_type'] == 'refund_cancellation')
                                                        $type_label = 'Refund & Cancellation';
                                                    elseif ($refund['refund_type'] == 'only_refund')
                                                        $type_label = 'Refund Only';
                                                    elseif ($refund['refund_type'] == 'only_cancellation')
                                                        $type_label = 'Cancellation Only';
                                                    else
                                                        $type_label = $refund['refund_type'];
                                                    echo $type_label;
                                                    ?>
                                                </td>
                                                <td><?php echo $refund['payment_mode']; ?></td>
                                                <td>
                                                    <?php if ($refund['refund_type'] != 'only_cancellation'): ?>
                                                        <a href="<?php echo site_url('patients/visits/print_receipt/' . $refund['invoice_id'] . '?refund_id=' . $refund['id']); ?>"
                                                            target="_blank" class="btn btn-default btn-xs"><i
                                                                class="fa fa-print"></i></a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No refunds found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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