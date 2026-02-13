<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Billing Section -->
<div class="row">
    <div class="col-md-12">
        <!-- Tabs for Item Groups -->
        <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="active filter-tab" data-group="all">
                        <a href="#all_transactions" aria-controls="all_transactions"
                            role="tab" data-toggle="tab">All Transactions</a>
                    </li>
                    <?php if (isset($item_groups)) {
                        foreach ($item_groups as $group) { ?>
                            <li role="presentation" class="filter-tab"
                                data-group="<?php echo $group['name']; ?>">
                                <a href="#group_<?php echo $group['id']; ?>"
                                    aria-controls="group_<?php echo $group['id']; ?>" role="tab"
                                    data-toggle="tab"><?php echo $group['name']; ?></a>
                            </li>
                        <?php }
                    } ?>
                </ul>
            </div>
        </div>

        <div class="table-responsive s_table">
            <table class="table items table-main-invoice-edit has-calculations no-mtop">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 30px;"><input type="checkbox" id="select_all_items_master"></th>
                        <th>S.no</th>
                        <th width="35%">Services</th>
                        <th width="15%">Item Group</th>
                        <th>User</th>
                        <th>Status</th>
                        <th class="text-center" style="font-size: 20px;">🚨</th>
                        <th align="right">Total (<?php echo $base_currency->symbol; ?>)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="ui-sortable" id="billing_items_table">
                    <?php
                    $i = 0;
                    if (isset($tests)) {
                        foreach ($tests as $test) {
                            $i++;
                            ?>
                            <tr class="item" data-group-name="<?php echo $test['group_name']; ?>"
                                data-group-id="<?php echo isset($test['group_id']) ? $test['group_id'] : ''; ?>">
                                <td class="text-center">
                                    <div class="checkbox">
                                        <input type="checkbox" class="select_item_checkbox" name="selected_items[]" value="<?php echo $test['item_id']; ?>"> <!-- Or index -->
                                        <label></label>
                                    </div>
                                </td>
                                <td><?php echo $i; ?></td>
                                <td>
                                    <?php echo $test['description']; ?>
                                    <input type="hidden" name="items[<?php echo $i; ?>][id]"
                                        value="<?php echo $test['item_id']; ?>">
                                    <input type="hidden" name="items[<?php echo $i; ?>][test_id]"
                                        value="<?php echo $test['test_id']; ?>">
                                </td>
                                <td><?php echo $test['group_name']; ?></td>
                                <td>
                                    <?php
                                    // Show staff name if available
                                    echo isset($test['staff_name']) ? $test['staff_name'] : '';
                                    ?>
                                </td>
                                <td>
                                    <?php echo $test['status']; ?>
                                    <input type="hidden" name="items[<?php echo $i; ?>][status]"
                                        value="<?php echo $test['status']; ?>">
                                </td>
                                <td class="text-center">
                                    <div class="checkbox">
                                        <input type="checkbox"
                                            name="items[<?php echo $i; ?>][is_emergency]" value="1"
                                            <?php echo (isset($test['is_emergency']) && $test['is_emergency'] == 1) ? 'checked' : ''; ?>>
                                        <label></label>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $rate_readonly = '';
                                    if (isset($test['group_name']) && $test['group_name'] == 'Tests') {
                                        if (isset($test['is_price_changable']) && $test['is_price_changable'] == 0) {
                                            $rate_readonly = 'readonly';
                                        }
                                    }
                                    ?>
                                    <input type="number" name="items[<?php echo $i; ?>][rate]"
                                        class="form-control item_rate"
                                        value="<?php echo $test['rate']; ?>" <?php echo $rate_readonly; ?>>
                                    <input type="hidden"
                                        name="items[<?php echo $i; ?>][description]"
                                        class="item_desc"
                                        value="<?php echo $test['description']; ?>">
                                </td>
                                <td>
                                    <!-- Saved items cannot be removed -->
                                </td>

                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-12 mtop10">
            <div class="<?php echo (isset($visit_data) && $visit_data) ? 'input-group' : ''; ?>">

                <select id="quick_add_items" class="selectpicker" data-live-search="true"
                    data-width="100%"
                    data-none-selected-text="Add Services / Packages"></select>
                <?php if (isset($visit_data) && $visit_data) { ?>
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="dropdown-header">Print</li>
                        <li><a href="#" class="action-print-invoice">Invoice</a></li>
                        <li><a href="#" class="action-print-receipts">Receipts</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Reports</li>
                        <li><a href="#" class="action-report-wa-web">WA Web</a></li>
                        <li><a href="#" class="action-report-sms">SMS</a></li>
                        <li><a href="#" class="action-report-official-wa">Official WhatsApp</a></li>
                        <li><a href="#" class="action-report-email">Email</a></li>
                    </ul>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
