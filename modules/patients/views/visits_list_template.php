<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="table-responsive">
    <table class="table" data-order-col="0" data-order-type="desc">
        <thead>
            <tr>
                <th width="5%">S.no</th>
                <th width="20%">Patients</th>
                <th width="30%">Services</th>
                <th width="20%">Patient IDs</th>
                <th width="15%">Amount Due</th>
                <th width="15%">Doctor</th>
                <th width="10%">User</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            if (!empty($visits)) {
                foreach ($visits as $visit) {
                    $i++;
                    $due = $visit['invoice_amount'] - $visit['total_paid'];
                    $is_fully_paid = $due <= 0;
                    // Format Test Names (Truncate if long)
                    $test_names = isset($visit['test_names']) ? $visit['test_names'] : '';
                    if ($test_names && strlen($test_names) > 50) {
                        $test_names = substr($test_names, 0, 50) . '...';
                    }

                    // Date Logic
                    $visit_time = strtotime($visit['created_at']);
                    $date_display = (date('Y-m-d') == date('Y-m-d', $visit_time)) ? 'Today, ' . date('h:iA', $visit_time) : date('d M Y, h:iA', $visit_time);
                    ?>
                    <!-- Main Row -->
                    <tr>
                        <td><?php echo $visit['id']; // Using ID or Counter? user asked specifically for S.no usually counter. But pagination breaks counter. let's stick to ID or just row num if needed ?>.
                        </td>
                        <td>
                            <a href="<?php echo admin_url('patients/visits/add/' . $visit['patient_id']); ?>">
                                <span
                                    class="bold"><?php echo (isset($visit['title']) && $visit['title'] ? $visit['title'] . ' ' : '') . $visit['patient_name']; ?></span>
                            </a><br>
                            <span class="text-muted" style="font-size:12px;">
                                <?php echo $date_display; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <span class="bold"><?php echo $test_names; ?></span><br>
                                    <span class="text-info" style="font-size:12px;">Total:
                                        <?php echo $visit['test_count']; ?></span>
                                </div>
                                <button class="btn btn-default btn-icon toggle-details"
                                    data-invoice-id="<?php echo $visit['invoice_id']; ?>"
                                    data-target="#details_<?php echo $visit['id']; ?>">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:12px;">
                                <span class="bold" style="width:60px; display:inline-block;">MR NO
                                    :</span> <?php echo $visit['mr_number']; ?><br>
                                <span class="bold" style="width:60px; display:inline-block;">VISIT ID
                                    :</span> <?php echo $visit['visit_code']; ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($is_fully_paid) { ?>
                                <span class="text-success bold">Fully paid</span>
                            <?php } else { ?>
                                <span class="bold text-danger"><?php echo app_format_money($due, get_base_currency()); ?></span>
                            <?php } ?>
                        </td>
                        <td>
                            <span class="" style="font-size:12px; color:#555;">
                                <?php if ($visit['primary_doctor_name']) { ?>
                                    <span class="text-info bold">Primary:</span> <?php echo $visit['primary_doctor_name']; ?><br>
                                <?php } ?>
                                <?php if ($visit['doctor_name']) { ?>
                                    <span class="text-warning bold">Referral:</span> <?php echo $visit['doctor_name']; ?>
                                <?php } ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-uppercase" style="font-size:12px; font-weight:600; color:#555;">
                                <?php echo $visit['staff_name']; ?>
                            </span>
                        </td>
                    </tr>
                    <!-- Expanded Details Row (Hidden by default) -->
                    <tr id="details_<?php echo $visit['id']; ?>" style="display:none; background-color:#f9f9f9;">
                        <td colspan="6">
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <table class="table table-bordered table-condensed sub-table">
                                        <thead>
                                            <tr style="background:#000; color:#fff;">
                                                <th>Services</th>
                                                <th>Sourcing</th>
                                                <th>Status</th>
                                                <th>User</th>
                                            </tr>
                                        </thead>
                                        <tbody class="details-body" id="body_<?php echo $visit['invoice_id']; ?>">
                                            <tr>
                                                <td colspan="4" class="text-center">Loading...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="6" class="text-center">No visits found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-md-12 text-right">
        <div id="ajax_pagination">
            <?php echo $pagination_links; ?>
        </div>
    </div>
</div>