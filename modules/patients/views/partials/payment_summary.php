<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Totals Section -->
<div class="row mtop20">
    <!-- Right Side (Calculations) -->
    <!-- Left Side: Today's Invoices -->
    <div class="col-md-6">

    </div>

    <!-- Right Side (Calculations) -->
    <div class="col-md-6">
        <table class="table text-right billing-totals-table">
            <tbody>
                <tr>
                    <td style="vertical-align: middle;"><span class="text-muted">Sub
                            Total
                            :</span></td>
                    <td class="subtotal" width="50%">0.00</td>
                </tr>
                <?php
                // Init Discount Values
                $discount_type_val = 'fixed';
                $discount_amount_val = ''; // Default empty
                if (isset($invoice)) {
                    if ($invoice->discount_type == 'before_tax') {
                        $discount_type_val = '%';
                        $discount_amount_val = $invoice->discount_percent;
                    } else {
                        $discount_type_val = 'fixed';
                        $discount_amount_val = ($invoice->discount_total > 0) ? $invoice->discount_total : '';
                    }
                }
                ?>
                <tr>
                    <td style="vertical-align: middle;"><span class="text-muted">Discount
                            Type</span></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <select name="discount_type" class="selectpicker" data-width="auto"
                                    data-none-selected-text="Fixed">
                                    <option value="fixed" <?php echo ($discount_type_val == 'fixed') ? 'selected' : ''; ?>>Fixed</option>
                                    <option value="%" <?php echo ($discount_type_val == '%') ? 'selected' : ''; ?>>
                                        Percentage (%)</option>
                                </select>
                            </span>
                            <input type="number" value="<?php echo $discount_amount_val; ?>" name="discount_amount"
                                class="form-control text-right" id="discount_amount" placeholder="">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: middle;"><span class="text-muted">Discount Amount:</span></td>
                    <td class="text-danger bold" id="discount_total_display">0.00</td>
                </tr>
                <tr>
                    <td style="vertical-align: middle;"><span class="text-muted">Net
                            Amount
                            :</span></td>
                    <td class="total bold" style="font-size: 16px;">0.00</td>
                </tr>
                <tr>
                    <td style="vertical-align: middle;"><span class="text-muted">Total
                            Paid
                            :</span></td>
                    <td class="total_paid bold" style="font-size: 16px;">
                        <?php echo isset($total_paid) ? number_format($total_paid, 2) : '0.00'; ?>
                        <input type="hidden" id="historic_total_paid"
                            value="<?php echo isset($total_paid) ? $total_paid : 0; ?>">
                    </td>
                </tr>
                <?php if (isset($total_refunded) && $total_refunded > 0) { ?>
                    <tr>
                        <td style="vertical-align: middle;"><span class="bold text-warning">Total Refunded :</span></td>
                        <td class="bold text-warning" style="font-size: 16px;">
                            <?php echo number_format($total_refunded, 2); ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="vertical-align: top; padding-top: 10px;">
                        <span class="bold">Payments</span>
                        <button type="button" class="btn btn-xs btn-success pull-right" id="add_payment_row"
                            style="margin-left:5px;"><i class="fa fa-plus"></i></button>
                    </td>
                    <td style="padding:0;">
                        <table class="table no-mtop no-mbot" style="background:transparent;">
                            <tbody id="payment_rows">
                                <tr class="payment-row">
                                    <td width="30%" style="padding-left:0; padding-right:5px; border-top:0;">
                                        <select name="payments[0][paymentmode]" class="selectpicker" data-width="100%"
                                            data-none-selected-text="Mode">
                                            <?php foreach ($payment_modes as $mode) { ?>
                                                <option value="<?php echo $mode['id']; ?>" <?php echo (strtolower($mode['name']) == 'cash') ? 'selected' : ''; ?>>
                                                    <?php echo $mode['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td style="padding-left:0; padding-right:5px; border-top:0;">
                                        <input type="number" name="payments[0][amount]"
                                            class="form-control text-right payment_amount_input" value=""
                                            placeholder="Amount" step="0.01" min="0">
                                    </td>

                                    <td width="30px" style="vertical-align: middle; border-top:0;">
                                    </td>
                                </tr>
                                <tr class="payment-note-row">
                                    <td colspan="3"
                                        style="padding-left:0; padding-right:5px; border-top:0; padding-bottom:10px;">
                                        <input type="text" name="payments[0][note]" class="form-control"
                                            placeholder="Remarks">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: middle;"><span class="bold text-danger">Amount Due :</span></td>
                    <td><span class="amount_due bold text-danger" style="font-size: 16px;">0.00</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php
        $submit_text = _l('submit');
        if (get_option('patients_print_invoice_on_visit') == '1') {
            $submit_text = 'Save & Print';
        }
        ?>
        <button type="submit" class="btn btn-info pull-right">
            <?php echo $submit_text; ?>
        </button>
    </div>
</div>