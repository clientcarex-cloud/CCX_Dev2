<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <!-- Floating Settings Button -->
        <a href="<?php echo admin_url('transcriptor/settings'); ?>" class="btn btn-info btn-icon"
            style="position: fixed; bottom: 30px; right: 30px; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999;">
            <i class="fa fa-cog" style="font-size: 20px;"></i>
        </a>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <style>
                            .transcriptor-filters {
                                display: inline-flex;
                                background-color: #f1f5f9;
                                padding: 5px;
                                border-radius: 50px;
                                margin-bottom: 15px;
                            }

                            .transcriptor-filter-item {
                                padding: 5px 15px;
                                border-radius: 50px;
                                text-decoration: none !important;
                                color: #475569;
                                font-weight: 500;
                                font-size: 13px;
                                margin-right: 2px;
                                transition: all 0.2s;
                            }

                            .transcriptor-filter-item:hover {
                                color: #1e293b;
                            }

                            .transcriptor-filter-item.active {
                                background-color: #ffffff;
                                color: #0f172a;
                                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                                font-weight: 600;
                            }

                            /* Fix Vertical Alignment */
                            .transcriptor-controls-wrapper .form-group {
                                margin-bottom: 0 !important;
                            }
                        </style>
                        <!-- Flex Wrapper: Left Aligned -->
                        <div class="transcriptor-controls-wrapper"
                            style="display: flex; align-items: center; justify-content: flex-start; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">

                            <!-- Tabs -->
                            <div class="transcriptor-filters" style="margin-bottom: 0;">
                                <?php
                                $statuses = ['All', 'Regular', 'Emergency', 'Processing', 'Completed'];
                                foreach ($statuses as $s) {
                                    $active = (isset($selected_status) && $selected_status == $s) ? 'active' : '';
                                    if (!isset($selected_status) && $s == 'All')
                                        $active = 'active';

                                    // Build URL with current filters preserved
                                    $params = $_GET;
                                    $params['status'] = $s;
                                    $url = admin_url('transcriptor?' . http_build_query($params));

                                    echo '<a href="' . $url . '" class="transcriptor-filter-item ' . $active . '">' . $s . '</a>';
                                }
                                ?>
                            </div>

                            <!-- Filters Form: Flexbox Layout -->
                            <form method="get" action="<?php echo admin_url('transcriptor'); ?>">
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <?php
                                    $show_date = get_option('transcriptor_show_date_filter') != '0';
                                    $show_user = get_option('transcriptor_show_user_filter') != '0';
                                    $show_dept = get_option('transcriptor_show_department_filter') != '0';
                                    $show_ref = get_option('transcriptor_show_ref_doctor_filter') != '0';
                                    ?>
                                    <?php if ($show_date) { ?>
                                        <div style="width: 140px;">
                                            <?php echo render_date_input('from_date', '', $filters['from_date'], ['placeholder' => 'From Date', 'onchange' => 'this.form.submit()']); ?>
                                        </div>
                                        <div style="width: 140px;">
                                            <?php echo render_date_input('to_date', '', $filters['to_date'], ['placeholder' => 'To Date', 'onchange' => 'this.form.submit()']); ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($show_user) { ?>
                                        <div style="width: 150px;">
                                            <?php echo render_select('user_id', $staff, ['staffid', ['firstname', 'lastname']], '', $filters['user_id'], ['data-none-selected-text' => 'User', 'onchange' => 'this.form.submit()']); ?>
                                        </div>
                                    <?php } ?>

                                    <?php if ($show_dept) { ?>
                                        <div style="width: 150px;">
                                            <?php echo render_select('department_id', $departments, ['departmentid', 'name'], '', $filters['department_id'], ['data-none-selected-text' => 'Department', 'onchange' => 'this.form.submit()']); ?>
                                        </div>
                                    <?php } ?>

                                    <?php if ($show_ref) { ?>
                                        <div style="width: 150px;">
                                            <?php echo render_select('ref_doctor_id', $doctors, ['staffid', ['firstname', 'lastname']], '', $filters['ref_doctor_id'], ['data-none-selected-text' => 'Ref. Doctor', 'onchange' => 'this.form.submit()']); ?>
                                        </div>
                                    <?php } ?>

                                    <?php
                                    $has_filters = !empty($filters['from_date']) || !empty($filters['to_date']) || !empty($filters['user_id']) || !empty($filters['department_id']) || !empty($filters['ref_doctor_id']);
                                    if (($show_date || $show_user || $show_dept || $show_ref) && $has_filters) {
                                        ?>
                                        <div>
                                            <a href="<?php echo admin_url('transcriptor'); ?>"
                                                class="btn btn-default btn-icon"
                                                style="border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"><i
                                                    class="fa fa-remove"></i></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                        <hr class="hr-panel-heading" />

                        <table class="table dt-table" data-order-col="0" data-order-type="asc">
                            <thead>
                                <tr>
                                    <th>Sno.</th>
                                    <th>Patients</th>
                                    <th>Age & Gender</th>
                                    <th>Lab Test</th>
                                    <th>Ref. Doc</th>
                                    <th>Patient IDs</th>
                                    <th>Status</th>
                                    <th>Report</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $key => $r) {
                                    // Payment Status Logic
                                    // 2 = Paid. Others = Unpaid/Partial
                                    $payment_text = '';
                                    if (get_option('transcriptor_show_payment_info') == '1') {
                                        $is_paid = $r['invoice_status'] == 2;
                                        if ($is_paid) {
                                            $payment_text = '<span class="text-success"> | <b>Paid</b></span>';
                                        } else {
                                            // Ideally calculate due amount. For now showing "Due" unless we fetch balance.
                                            // Using 'total' as due if unpaid, which is roughly correct if no partial.
                                            $currency = '₹'; // Hardcoded based on screenshot symbols
                                            $payment_text = '<span class="text-danger"> | <b>' . $currency . ' ' . app_format_money($r['total'], '') . ' Due</b></span>';
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $key + 1; ?>.</td>
                                        <td>
                                            <b>
                                                <?php
                                                if (isset($r['is_emergency']) && $r['is_emergency'] == 1) {
                                                    echo '🚨 ';
                                                }
                                                echo $r['patient_name'];
                                                ?>
                                            </b><br>
                                            <small class="text-muted">
                                                <?php echo _dt($r['created_at']); ?>
                                                <?php echo $payment_text; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php echo $r['age']; ?><br>
                                            <?php echo $r['gender']; ?>
                                        </td>
                                        <td><b><?php echo $r['test_name']; ?></b></td>
                                        <td>
                                            <?php echo $r['ref_doc_name'] ? strtoupper($r['ref_doc_name']) : '-'; ?>
                                        </td>
                                        <td>
                                            <b>MR NO : <?php echo $r['mr_number']; ?></b><br>
                                            <b>VISIT ID : <?php echo $r['visit_code']; ?></b>
                                        </td>
                                        <td><!-- Status -->
                                            <?php
                                            if ($r['status'] == '8') {
                                                echo '<span class="label label-warning">Authorization Required</span>';
                                            } else {
                                                echo $r['status'];
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($r['status'] == 'Processing') { ?>
                                                <a href="#" onclick="open_report(<?php echo $r['id']; ?>); return false;"
                                                    class="btn btn-warning btn-sm" style="color:white;">Draft Report</a>
                                            <?php } elseif ($r['status'] == 'Completed') { ?>
                                                <a href="#" onclick="view_report(<?php echo $r['id']; ?>); return false;"
                                                    class="btn btn-success btn-sm" style="color:white;">Printed</a>
                                            <?php } elseif ($r['status'] == '8') { ?>
                                                <a href="#" onclick="open_report(<?php echo $r['id']; ?>); return false;"
                                                    class="btn btn-warning btn-sm" style="color:white;">Pending
                                                    Authorization</a>
                                            <?php } else { ?>
                                                <a href="#" onclick="open_report(<?php echo $r['id']; ?>); return false;"
                                                    class="btn btn-info btn-sm" style="color:white;">Create Report</a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-default btn-icon"><i
                                                    class="fa fa-angle-down"></i></button>
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
<div id="modal_wrapper"></div>
<?php init_tail(); ?>
<script>
    function open_report(test_id) {
        $.post(admin_url + 'transcriptor/create_report/' + test_id).done(function (response) {
            response = JSON.parse(response);
            if (response.success) {
                $.get(admin_url + 'transcriptor/edit_report/' + response.id).done(function (html) {
                    $('#modal_wrapper').html(html);
                    $('#report_modal').modal('show');
                });
            }
        });
    }
    function view_report(id) {
        $.get(admin_url + 'transcriptor/view_report/' + id).done(function (html) {
            $('#modal_wrapper').html(html);
            $('#view_report_modal').modal('show');
        });
    }
</script>