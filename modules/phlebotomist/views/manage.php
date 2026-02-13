<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Filters -->
                        <style>
                            .phlebotomist-filters {
                                display: inline-flex;
                                background-color: #f1f5f9;
                                /* Light gray/blue bg */
                                padding: 5px;
                                border-radius: 50px;
                                /* Fully rounded */
                            }

                            .phlebotomist-filter-item {
                                padding: 5px 15px;
                                border-radius: 50px;
                                text-decoration: none !important;
                                color: #475569;
                                font-weight: 500;
                                font-size: 13px;
                                margin-right: 2px;
                                transition: all 0.2s;
                            }

                            .phlebotomist-filter-item:hover {
                                color: #1e293b;
                            }

                            .phlebotomist-filter-item.active {
                                background-color: #ffffff;
                                color: #0f172a;
                                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                                font-weight: 600;
                            }
                        </style>
                        <div class="phlebotomist-filters">
                            <?php
                            $statuses = ['All', 'Regular', 'Emergency', 'Sample Collected', 'Pending'];
                            foreach ($statuses as $s) {
                                $active = ($selected_status == $s) ? 'active' : '';
                                // Preserve date in URL
                                $url = admin_url('phlebotomist?status=' . $s . '&date=' . $selected_date);

                                $label = $s;
                                if ($s == 'Emergency' && isset($emergency_count) && $emergency_count > 0) {
                                    $label .= ' <span class="badge" style="background-color: #ef4444; color: white; margin-left: 5px;">' . $emergency_count . '</span>';
                                }
                                if ($s == 'Pending' && isset($pending_count) && $pending_count > 0) {
                                    $label .= ' <span class="badge" style="background-color: #2563eb; color: white; margin-left: 5px;">' . $pending_count . '</span>';
                                }

                                echo '<a href="' . $url . '" class="phlebotomist-filter-item ' . $active . '">' . $label . '</a>';
                            }
                            ?>
                        </div>
                        <div style="float: right; display: inline-flex; align-items: center;">
                            <label for="filter_date"
                                style="margin-right: 10px; font-weight: 500; color: #475569;">Date:</label>
                            <input type="date" id="filter_date" class="form-control"
                                style="width: 150px; border-radius: 6px; border: 1px solid #e2e8f0;"
                                value="<?php echo $selected_date; ?>" onchange="applyFilters()">
                        </div>

                        <script>
                            function applyFilters() {
                                var date = document.getElementById('filter_date').value;
                                var status = '<?php echo $selected_status; ?>';
                                window.location.href = admin_url + 'phlebotomist?status=' + status + '&date=' + date;
                            }
                        </script>

                        <hr class="hr-panel-heading" />

                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="0" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th>S.no</th>
                                        <th>Patient Name & Date</th>
                                        <th>MR No & Visit ID</th>
                                        <th>Lab Test Name</th>
                                        <th>Collection</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($requests as $request) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>
                                                <span class="bold">
                                                    <?php
                                                    if (isset($request['is_emergency']) && $request['is_emergency'] == 1) {
                                                        echo '🚨 ';
                                                    }
                                                    echo $request['patient_name'];
                                                    ?>
                                                </span><br>
                                                <small class="text-muted"><?php echo _dt($request['created_at']); ?></small>
                                            </td>
                                            <td>
                                                <span class="bold"><?php echo $request['mr_number']; ?></span><br>
                                                <small class="text-muted"><?php echo $request['visit_code']; ?></small>
                                                <!-- Hidden patient id if needed, but it is in $request['patient_id'] from query select? -->
                                                <!-- Query select: clients.userid = patient_tests.patient_id. -->
                                                <!-- But we didn't select patient_id explicitly in model? 
                                                     Let's check model select. It selects clients.userid as patient_id? No.
                                                     It joined clients on valid... but selects clients.company as patient_name.
                                                     Did it select patient_id? 
                                                     Let's check model code.
                                                     Model selects: patient_tests.id, status, items.description, clients.company... visits.visit_code, patients_extra.mr_number.
                                                     It does NOT select patient_id. I need to add that to model select. -->
                                            </td>
                                            <td>
                                                <?php echo $request['test_name']; ?><br>
                                                <?php
                                                if (!$request['status'])
                                                    $request['status'] = 'Pending';

                                                $statusClass = 'label-info';
                                                if ($request['status'] == 'Emergency') {
                                                    $statusClass = 'label-danger';
                                                }

                                                echo '<span class="label ' . $statusClass . '">' . $request['status'] . '</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($request['collection_id']) {
                                                    // Already collected
                                                    // Calculate time ago
                                                    $time_ago = time_ago($request['collected_at']);
                                                    // Enable Print button and add click handler
                                                    ?>
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        onclick="print_barcode(<?php echo $request['id']; ?>)">Print</button>
                                                    <br><small class="text-success"><?php echo $time_ago; ?></small>

                                                <?php } else { ?>

                                                    <button type="button" id="btn_<?php echo $request['id']; ?>"
                                                        onclick="collect_sample(<?php echo $request['id']; ?>, <?php echo $request['patient_id']; ?>)"
                                                        class="btn btn-info btn-sm"
                                                        data-patient_id="<?php echo $request['patient_id']; ?>"
                                                        data-test_name="<?php echo $request['test_name']; ?>">Collect &
                                                        Print</button>

                                                    <div id="collected_info_<?php echo $request['id']; ?>"
                                                        style="display:none;">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="print_barcode(<?php echo $request['id']; ?>)">Print</button>
                                                        <br><small class="text-success">Just now</small>
                                                    </div>

                                                <?php } ?>
                                            </td>
                                            <td><?php echo $request['user_name']; ?></td>
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
<script>
    function collect_sample(id) {
        // Get data from button attributes 
        // passing id specific to row (patient_test_id)
        var btn = $('#btn_' + id);
        var patient_id = btn.data('patient_id');
        var test_name = btn.data('test_name');

        // Disable button to prevent double click
        btn.prop('disabled', true);
        btn.text('Processing...');

        $.post(admin_url + 'phlebotomist/collect_sample', {
            patient_test_id: id,
            patient_id: patient_id, // Is this available in request? Yes
            test_name: test_name
        }).done(function (response) {
            response = JSON.parse(response);
            if (response.success) {
                // Hide original button
                btn.hide();
                // Show collected info div
                $('#collected_info_' + id).show();
                alert_float('success', 'Sample Collected Logged');

                // Immediately open print dialog? User says "when user is clicking on collect & Print"
                // "and when it's becoming Print button So let user to print"
                // It implies the user can click Print later.
                // But "collect & print" implies immediate action? 
                // Let's trigger print automatically?
                // "So let user to print from the content" -> sounds like permission.
                // For now, I'll just enable the button. If they want auto-popup, I can add `print_barcode(id)` here.
                // Re-reading: "change button text... to print... So let user to print" -> Manual click.
            } else {
                alert_float('danger', 'Failed to log collection');
                btn.prop('disabled', false);
                btn.text('Collect & Print');
            }
        });
    }

    function print_barcode(id) {
        var width = 800;
        var height = 600;
        var left = (screen.width - width) / 2;
        var top = (screen.height - height) / 2;
        var params = 'width=' + width + ', height=' + height;
        params += ', top=' + top + ', left=' + left;
        params += ', directories=no';
        params += ', location=no';
        params += ', menubar=no';
        params += ', resizable=no';
        params += ', scrollbars=no';
        params += ', status=no';
        params += ', toolbar=no';

        var newwin = window.open(admin_url + 'phlebotomist/print_label/' + id, 'Print Window', params);
        if (window.focus) {
            newwin.focus();
        }
    }
</script>
<?php init_tail(); ?>
</body>

</html>