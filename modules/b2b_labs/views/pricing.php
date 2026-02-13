<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string()); ?>

                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="1" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th>
                                            <?php echo _l('id'); ?>
                                        </th>
                                        <th>Code</th>
                                        <th>Test Name</th>
                                        <th>Department</th>
                                        <th>Standard Price</th>
                                        <th>B2B Price (Default)</th>
                                        <th>Referral Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tests as $test) { ?>
                                        <tr>
                                            <td>
                                                <?php echo $test['id']; ?>
                                            </td>
                                            <td>
                                                <?php echo $test['code']; ?>
                                            </td>
                                            <td>
                                                <?php echo $test['description']; ?>
                                            </td>
                                            <td>
                                                <?php echo $test['department_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo app_format_money($test['rate'], $currency); ?>
                                            </td>
                                            <td>
                                                <?php echo app_format_money($test['b2b_price'], $currency); ?>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control"
                                                    name="referral_price[<?php echo $test['id']; ?>]"
                                                    value="<?php echo isset($pricing[$test['id']]) ? $pricing[$test['id']] : ''; ?>"
                                                    placeholder="Referral Price">
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-info">
                                <?php echo _l('save'); ?>
                            </button>
                            <button type="button" class="btn btn-default" data-toggle="modal"
                                data-target="#bulk_pricing_modal">
                                Bulk Pricing
                            </button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Pricing Modal -->
<div class="modal fade" id="bulk_pricing_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('b2b_labs/bulk_update_pricing/' . $this->uri->segment(4))); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Bulk Pricing Options</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bulk_action">Select Action</label>
                    <select name="bulk_action" id="bulk_action" class="form-control" required>
                        <option value="">-- Select Action --</option>
                        <option value="percent_inc">% increment</option>
                        <option value="percent_dec">% decrement</option>
                        <option value="amount_inc">+one amount</option>
                        <option value="amount_dec">-minus one lumsum amount</option>
                        <option value="sync_b2b">Sync same as B2B price</option>
                        <option value="sync_standard">Sync same as Standard Price</option>
                    </select>
                </div>
                <div class="form-group" id="bulk_value_wrapper">
                    <label for="bulk_value">Value</label>
                    <input type="number" step="0.01" name="bulk_value" id="bulk_value" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var bulkActionSelect = document.getElementById('bulk_action');
        var bulkValueWrapper = document.getElementById('bulk_value_wrapper');
        var bulkValueInput = document.getElementById('bulk_value');

        bulkActionSelect.addEventListener('change', function () {
            var action = this.value;
            if (action === 'sync_b2b' || action === 'sync_standard') {
                bulkValueWrapper.style.display = 'none';
                bulkValueInput.removeAttribute('required');
            } else {
                bulkValueWrapper.style.display = 'block';
                bulkValueInput.setAttribute('required', 'required');
            }
        });
    });
</script>
<?php init_tail(); ?>