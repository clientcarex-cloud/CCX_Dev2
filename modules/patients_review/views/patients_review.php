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
                                    <a href="<?php echo admin_url('patients_review?status=all'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'all') ? 'btn-info' : 'btn-default'; ?>">All</a>
                                    <a href="<?php echo admin_url('patients_review?status=dues'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'dues') ? 'btn-info' : 'btn-default'; ?>">Dues</a>
                                    <a href="<?php echo admin_url('patients_review?status=partial'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'partial') ? 'btn-info' : 'btn-default'; ?>">Partial</a>
                                    <a href="<?php echo admin_url('patients_review?status=refund'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'refund') ? 'btn-info' : 'btn-default'; ?>">Refund</a>
                                    <a href="<?php echo admin_url('patients_review?status=paid'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'paid') ? 'btn-info' : 'btn-default'; ?>">Paid</a>
                                    <?php if (get_option('show_archived_tab') == 1) { ?>
                                    <a href="<?php echo admin_url('patients_review?status=archived'); ?>"
                                        class="btn <?php echo ($filters['status'] == 'archived') ? 'btn-info' : 'btn-default'; ?>">Archived</a>
                                    <?php } ?>
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
                            <div class="col-md-2">
                                <?php echo render_date_input('report_from', '', $filters['report_from'], ['placeholder' => 'From Date', 'autocomplete' => 'off']); ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('report_to', '', $filters['report_to'], ['placeholder' => 'To Date', 'autocomplete' => 'off']); ?>
                            </div>
                            <div class="col-md-3">
                                <select name="user_id" class="selectpicker" data-width="100%"
                                    data-none-selected-text="Select User" data-live-search="true">
                                    <option value=""></option>
                                    <?php foreach ($staff as $s) { ?>
                                        <option value="<?php echo $s['staffid']; ?>" <?php if ($filters['user_id'] == $s['staffid']) {
                                               echo 'selected';
                                           } ?>>
                                            <?php echo $s['firstname'] . ' ' . $s['lastname']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success" onclick="apply_filters();">Filter</button>
                                <?php
                                $authorized_users = get_option('patients_review_authorized_user');
                                if (!empty($authorized_users)) {
                                    $authorized_users = explode(',', $authorized_users);
                                    if (in_array(get_staff_user_id(), $authorized_users)) { ?>
                                        <button class="btn btn-danger"
                                            onclick="show_actions_modal(); return false;">Actions</button>
                                    <?php }
                                } ?>
                            </div>
                        </div>

                        <div class="clearfix mtop20"></div>

                        <div class="table-responsive">
                            <table class="table dt-table table-patients-review" data-order-col="1"
                                data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th data-orderable="false"><span class="hide"> - </span>
                                            <div class="checkbox mass_select_all_wrap"><input type="checkbox"
                                                    id="mass_select_all" data-to-table="patients-review"><label></label>
                                            </div>
                                        </th>
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
                                        $sum_total += $p['subtotal'];
                                        $sum_discount += $p['discount'];
                                        $sum_net += $p['total'];
                                        $sum_paid += $p['paid_amount'];
                                        $sum_refund += $p['refund_amount'];
                                        $sum_due += $p['due_amount'];
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="checkbox"><input type="checkbox"
                                                        value="<?php echo $p['visit_id']; ?>"
                                                        data-net-total="<?php echo $p['total']; ?>"
                                                        data-subtotal="<?php echo $p['subtotal']; ?>"
                                                        data-discount="<?php echo $p['discount']; ?>"
                                                        data-refund="<?php echo $p['refund_amount']; ?>"
                                                        data-paid="<?php echo $p['paid_amount']; ?>"
                                                        data-date="<?php echo $p['visit_date']; ?>"><label></label></div>
                                            </td>
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
                                                <span class="text-xs">Invoices:
                                                    <?php echo ($p['invoice_id'] ? 1 : 0); ?></span><br>
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
                                        <td colspan="6" class="text-right"></td>
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
<?php $base_currency = get_base_currency(); ?>
<script>
    function togglePasswordVisibility(id) {
        var input = $('#' + id);
        var icon = input.next('.input-group-addon').find('i');
        // Check if currently masked (using webkit-text-security)
        if (input.css('-webkit-text-security') === 'disc') {
            // Show password
            input.css('-webkit-text-security', 'none');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            // Hide password
            input.css('-webkit-text-security', 'disc');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }

    function apply_filters() {
        var report_from = $('input[name="report_from"]').val();
        var report_to = $('input[name="report_to"]').val();
        var user_id = $('select[name="user_id"]').val();
        var status = '<?php echo $filters['status']; ?>';
        window.location.href = admin_url + 'patients_review?status=' + status + '&report_from=' + report_from + '&report_to=' + report_to + '&user_id=' + user_id;
    }

    function show_actions_modal() {
        var selected_rows = $('.table-patients-review tbody input[type="checkbox"]:checked');
        if (selected_rows.length == 0) {
            alert_float('warning', 'Please select at least one item');
            return;
        }

        var total_net = 0;
        var total_subtotal = 0;
        var total_discount = 0;
        var total_refund = 0;
        var total_paid = 0;
        var dates = [];

        selected_rows.each(function () {
            var net = $(this).data('net-total');
            var sub = $(this).data('subtotal');
            var disc = $(this).data('discount');
            var ref = $(this).data('refund');
            var pd = $(this).data('paid');
            var dt = $(this).data('date');

            if (net) total_net += parseFloat(net);
            if (sub) total_subtotal += parseFloat(sub);
            if (disc) total_discount += parseFloat(disc);
            if (ref) total_refund += parseFloat(ref);
            if (pd) total_paid += parseFloat(pd);
            if (dt) dates.push(new Date(dt));
        });

        // Calculate Date Range
        var date_range_str = '-';
        if (dates.length > 0) {
            // Sort dates
            dates.sort(function(a,b){return a.getTime() - b.getTime()});
            var min_date = dates[0].toISOString().split('T')[0];
            var max_date = dates[dates.length-1].toISOString().split('T')[0];
            
            // Format to likely user preference (or keeping simple YYYY-MM-DD for JS simplicity, or use moment if available)
            // Assuming simplified format for now
            date_range_str = min_date;
            if (min_date !== max_date) {
                date_range_str += ' to ' + max_date;
            }
        }

        var base_currency_symbol = '<?php echo $base_currency->symbol; ?>';
        
        var table_html = '<table class="table table-bordered table-striped bold">';
        table_html += '<thead><tr>';
        table_html += '<th>Total (Subtotal)</th>';
        table_html += '<th>Discount</th>';
        table_html += '<th>Net Amount</th>';
        table_html += '<th>Paid</th>';
        table_html += '<th>Refund</th>';
        table_html += '<th>Date Duration</th>';
        table_html += '</tr></thead><tbody><tr>';
        
        table_html += '<td>' + format_money(total_subtotal, true) + '</td>';
        table_html += '<td>' + format_money(total_discount, true) + '</td>';
        table_html += '<td>' + format_money(total_net, true) + '</td>';
        table_html += '<td>' + format_money(total_paid, true) + '</td>';
        table_html += '<td>' + format_money(total_refund, true) + '</td>';
        table_html += '<td>' + date_range_str + '</td>';

        table_html += '</tr></tbody></table>';


        $('#delete_conf_content').html('<p>Are you sure you want to proceed with <span class="bold">' + selected_rows.length + '</span> selected items?</p>');
        $('#delete_summary_table_wrapper').html(table_html);
        
        // Hide old simple total
        $('#delete_total_amount_wrapper').hide();

        $('#delete_modal').modal('show');
    }

    function delete_selected(action_type) {
        var selected_ids = [];
        $('.table-patients-review tbody input[type="checkbox"]:checked').each(function () {
            selected_ids.push($(this).val());
        });

        var delete_option = $('input[name="delete_option"]:checked').val();
        var delete_password = '';
        
        if ($('#delete_password_input').length > 0) {
            delete_password = $('#delete_password_input').val();
            if(delete_password == '') {
                alert_float('warning', 'Please enter delete password');
                return;
            }
        }

        $.post(admin_url + 'patients_review/bulk_action', {
            ids: selected_ids,
            action: action_type,
            delete_option: delete_option,
            delete_password: delete_password,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        }).done(function (response) {
            response = JSON.parse(response);
            if (response.success) {
                window.location.reload();
            } else {
                alert_float('danger', response.message);
            }
        });
    }
</script>
<?php $base_currency = get_base_currency(); ?>

<!-- Floating Action Button for Settings -->
<a href="<?php echo admin_url('patients_review/settings'); ?>" class="btn btn-info btn-icon"
    style="position: fixed; bottom: 30px; right: 30px; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0,0,0,0.2); z-index: 9999;">
    <i class="fa fa-cog fa-lg"></i>
</a>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Actions Confirmation</h4>
            </div>
            <div class="modal-body">
                <div id="delete_conf_content">
                    <p>Are you sure you want to proceed with the selected items?</p>
                </div>
                <!-- Summary Table Container -->
                 <div id="delete_summary_table_wrapper" class="mbot15 table-responsive"></div>

                <div class="text-danger bold mbot15" id="delete_total_amount_wrapper">
                    Total Amount: <span id="delete_total_amount">0.00</span>
                </div>
                <div class="form-group">
                    <label>Delete Options:</label>
                    <?php
                    $delete_system = get_option('delete_system');
                    $delete_system_array = [];
                    if ($delete_system) {
                        $delete_system_array = explode(',', $delete_system);
                    }
                    $show_paid = in_array('paid_receipts', $delete_system_array);
                    $show_whole = in_array('whole_invoices', $delete_system_array);
                    ?>

                    <?php if ($show_paid) { ?>
                        <div class="radio radio-primary">
                            <input type="radio" name="delete_option" id="delete_option_receipts" value="paid_receipts"
                                checked>
                            <label for="delete_option_receipts">Delete Paid Receipts</label>
                        </div>
                    <?php } ?>

                    <?php if ($show_whole) { ?>
                        <div class="radio radio-primary">
                            <input type="radio" name="delete_option" id="delete_option_invoice" value="whole_invoices"
                                <?php if (!$show_paid) {
                                    echo 'checked';
                                } ?>>
                            <label for="delete_option_invoice">Delete Whole Invoice & Receipts</label>
                        </div>
                    <?php } ?>
                </div>

                <?php if (get_option('enable_delete_password') == 1) { ?>
                    <div class="form-group">
                        <label for="delete_password_input" class="control-label">Enter Action Password</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="delete_password_input" name="delete_password_input" autocomplete="off" style="-webkit-text-security: disc;">
                            <span class="input-group-addon pointer" onclick="togglePasswordVisibility('delete_password_input')"><i class="fa fa-eye"></i></span>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <?php
                $patients_records_setting = get_option('patients_records');
                // Archive Button
                if ($patients_records_setting == 'only_archive' || $patients_records_setting == 'archive_and_delete') { 
                    if ($filters['status'] != 'archived') { ?>
                    <button type="button" class="btn btn-warning" onclick="delete_selected('archive')">Archive</button>
                    <?php } else { ?>
                    <button type="button" class="btn btn-success" onclick="delete_selected('unarchive')">Unarchive</button>
                <?php } 
                }
                // Delete Button
                if ($patients_records_setting == 'only_delete' || $patients_records_setting == 'archive_and_delete') { ?>
                    <button type="button" class="btn btn-danger" onclick="delete_selected('delete')">Delete</button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>