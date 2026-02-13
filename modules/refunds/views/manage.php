<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" onclick="new_refund(); return false;"
                                class="btn btn-info pull-left display-block">
                                <?php echo _l('new_refund'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="0" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('id'); ?></th>
                                        <th><?php echo _l('date'); ?></th>
                                        <th><?php echo _l('patient'); ?></th>
                                        <th><?php echo _l('invoice'); ?></th>
                                        <th><?php echo _l('amount'); ?></th>
                                        <th><?php echo _l('payment_mode'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($refunds as $refund) { ?>
                                        <tr>
                                            <td><?php echo $refund['id']; ?></td>
                                            <td><?php echo _d($refund['refunded_on']); ?></td>
                                            <td><a
                                                    href="<?php echo admin_url('clients/client/' . $refund['invoice_id']); // Link key fix needed ?>"><?php echo $refund['patient_name']; ?></a>
                                            </td>
                                            <td><a
                                                    href="<?php echo admin_url('invoices/list_invoices/' . $refund['invoice_id']); ?>"><?php echo $refund['invoice_number']; ?></a>
                                            </td>
                                            <td><?php echo app_format_money($refund['amount'], ''); ?></td>
                                            <td><?php echo $refund['payment_mode']; ?></td>
                                            <td>
                                                <?php if (is_admin()) { ?>
                                                    <a href="<?php echo admin_url('refunds/delete/' . $refund['id']); ?>"
                                                        class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Refund Modal -->
<div class="modal fade" id="refund_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('new_refund'); ?></h4>
            </div>
            <?php echo form_open(admin_url('refunds'), ['id' => 'refund_form']); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="clientid" class="control-label"><?php echo _l('patient'); ?></label>
                    <div class="form-group">
                        <select id="clientid" name="patient_id" class="ajax-search" data-width="100%"
                            data-live-search="true"
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="invoice_id" class="control-label"><?php echo _l('invoice'); ?></label>
                    <select name="invoice_id" id="invoice_id" class="form-control selectpicker" data-live-search="true"
                        disabled>
                        <option value=""></option>
                    </select>
                    <p class="text-muted" id="invoice_details"></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php echo render_date_input('refunded_on', 'refund_date', _d(date('Y-m-d'))); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_input('amount', 'refund_amount', '', 'number'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="payment_mode" class="control-label"><?php echo _l('payment_mode'); ?></label>
                    <select name="payment_mode" class="form-control selectpicker">
                        <option value=""></option>
                        <?php foreach ($payment_modes as $mode) { ?>
                            <option value="<?php echo $mode['name']; ?>"><?php echo $mode['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <?php echo render_textarea('note', 'note_reason'); ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    function new_refund() {
        $('#refund_modal').modal('show');
    }

    init_ajax_search('customer', '#clientid', { type: 'customer' });

    $('#clientid').on('change', function () {
        var patientId = $(this).val();
        var $invoiceSelect = $('#invoice_id');
        $invoiceSelect.empty().append('<option value=""></option').prop('disabled', true).selectpicker('refresh');

        if (patientId) {
            $.get(admin_url + 'refunds/get_patient_invoices/' + patientId, function (response) {
                response = JSON.parse(response);
                if (response.length > 0) {
                    $invoiceSelect.prop('disabled', false);
                    $.each(response, function (i, inv) {
                        var label = inv.number + ' - Total: ' + inv.total + ' (Paid: ' + (inv.total_paid || 0) + ')';
                        $invoiceSelect.append('<option value="' + inv.id + '" data-max="' + (inv.total_paid || 0) + '">' + label + '</option>');
                    });
                }
                $invoiceSelect.selectpicker('refresh');
            });
        }
    });

    $('#invoice_id').on('change', function () {
        var max = $(this).find(':selected').data('max');
        $('#amount').attr('max', max);
        $('#invoice_details').text('Max Refundable: ' + max);
    });
</script>
</body>

</html>