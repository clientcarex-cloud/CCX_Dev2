<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
// Get Filter Options
$show_search = get_option('patients_visit_show_search');
$show_date_range = get_option('patients_visit_show_date_range');
$show_user = get_option('patients_visit_show_user');
$show_item_status = get_option('patients_visit_show_item_status');
$show_payment_status = get_option('patients_visit_show_payment_status');
$show_limit = get_option('patients_visit_show_limit');
$show_reset = get_option('patients_visit_show_reset');
$sort_order = get_option('patients_visit_sort_order');
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Quick Stats -->
                        <div class="row mbot15" id="visits-stats">
                            <div class="col-md-3 col-sm-6">
                                <div class="widget-stat-card"
                                    style="background: linear-gradient(135deg, #1fa2ff 0%, #12d8fa 100%, #a6ffcb 100%); background: linear-gradient(to right, #0ba360, #3cba92); color: white; padding: 20px; border-radius: 8px; position: relative; overflow: hidden;">
                                    <div style="font-size: 24px; font-weight: bold;" id="stat_total_visits">
                                        <?php echo $stats['total_visits']; ?>
                                    </div>
                                    <div
                                        style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Total Visits
                                    </div>
                                    <i class="fa fa-users"
                                        style="position: absolute; right: 15px; bottom: 10px; font-size: 50px; opacity: 0.2;"></i>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="widget-stat-card"
                                    style="background: linear-gradient(to right, #3a7bd5, #3a6073); color: white; padding: 20px; border-radius: 8px; position: relative; overflow: hidden;">
                                    <div style="font-size: 24px; font-weight: bold;" id="stat_unique_patients">
                                        <?php echo $stats['unique_patients']; ?>
                                    </div>
                                    <div
                                        style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Unique Patients
                                    </div>
                                    <i class="fa fa-calendar-check-o"
                                        style="position: absolute; right: 15px; bottom: 10px; font-size: 50px; opacity: 0.2;"></i>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="widget-stat-card"
                                    style="background: linear-gradient(to right, #f7971e, #ffd200); color: white; padding: 20px; border-radius: 8px; position: relative; overflow: hidden;">
                                    <div style="font-size: 24px; font-weight: bold;" id="stat_total_tests">
                                        <?php echo $stats['total_tests'] ? $stats['total_tests'] : 0; ?>
                                    </div>
                                    <div
                                        style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Services / Tests
                                    </div>
                                    <i class="fa fa-user-plus"
                                        style="position: absolute; right: 15px; bottom: 10px; font-size: 50px; opacity: 0.2;"></i>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="widget-stat-card"
                                    style="background: linear-gradient(to right, #8e2de2, #4a00e0); color: white; padding: 20px; border-radius: 8px; position: relative; overflow: hidden;">
                                    <div style="font-size: 24px; font-weight: bold;" id="stat_unique_doctors">
                                        <?php echo $stats['unique_doctors']; ?>
                                    </div>
                                    <div
                                        style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Doctors Involved
                                    </div>
                                    <i class="fa fa-heartbeat"
                                        style="position: absolute; right: 15px; bottom: 10px; font-size: 50px; opacity: 0.2;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="row mbot15">
                            <?php if ($show_search == '1') { ?>
                                <div class="col-md-2">
                                    <label>Search</label>
                                    <input type="text" name="search" id="search" class="form-control"
                                        placeholder="Search by Patient Name, MR No, Visit ID, Mobile Number..."
                                        value="<?php echo $filters['search']; ?>">
                                </div>
                            <?php } ?>
                            <?php if ($show_date_range == '1') { ?>
                                <div class="col-md-2">
                                    <label>From Date</label>
                                    <?php echo render_date_input('from_date', '', $filters['from_date']); ?>
                                </div>
                                <div class="col-md-2">
                                    <label>To Date</label>
                                    <?php echo render_date_input('to_date', '', $filters['to_date']); ?>
                                </div>
                            <?php } ?>
                            <?php if ($show_user == '1') { ?>
                                <div class="col-md-2">
                                    <label>User</label>
                                    <select name="user_id" id="user_id" class="selectpicker" data-width="100%"
                                        data-live-search="true" title="Select User">
                                        <option value=""></option>
                                        <?php foreach ($staff_members as $s) { ?>
                                            <option value="<?php echo $s['staffid']; ?>" <?php echo ($filters['user_id'] == $s['staffid']) ? 'selected' : ''; ?>>
                                                <?php echo $s['firstname'] . ' ' . $s['lastname']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } ?>
                            <?php if ($show_item_status == '1') { ?>
                                <div class="col-md-2">
                                    <label>Item Status</label>
                                    <select name="status" id="status" class="selectpicker" data-width="100%"
                                        data-live-search="true" title="All">
                                        <option value=""></option>
                                        <?php foreach ($item_statuses as $st) { ?>
                                            <option value="<?php echo $st['id']; ?>" <?php echo ($filters['status'] == $st['id']) ? 'selected' : ''; ?>>
                                                <?php echo $st['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } ?>
                            <?php if ($show_payment_status == '1') { ?>
                                <div class="col-md-2">
                                    <label>Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="selectpicker" data-width="100%"
                                        title="All">
                                        <option value=""></option>
                                        <?php foreach ($payment_statuses as $ps) { ?>
                                            <option value="<?php echo $ps; ?>" <?php echo ($filters['payment_status'] == $ps) ? 'selected' : ''; ?>>
                                                <?php echo format_invoice_status($ps, '', false); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row mbot15">
                            <?php if ($show_limit == '1') { ?>
                                <div class="col-md-1">
                                    <label>Limit</label>
                                    <select name="limit" id="limit" class="selectpicker" data-width="100%">
                                        <option value="20" selected>20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            <?php } ?>
                            <?php if ($show_reset == '1') { ?>
                                <div class="col-md-1">
                                    <label>&nbsp;</label>
                                    <button type="button" id="reset_filters" class="btn btn-default display-block"
                                        style="display:none;">Reset</button>
                                </div>
                            <?php } ?>
                            <?php
                            $add_visit_col = 12;
                            if ($show_limit == '1') {
                                $add_visit_col -= 1;
                            }
                            if ($show_reset == '1') {
                                $add_visit_col -= 1;
                            }
                            ?>
                            <div class="col-md-<?php echo $add_visit_col; ?> text-right">
                                <label>&nbsp;</label>
                                <a href="<?php echo admin_url('patients/visits/add'); ?>"
                                    class="btn btn-info pull-right">+ Add Visit</a>
                                <button type="button" class="btn btn-info pull-left mright5"
                                    id="estimate_btn">Estimate</button>
                                <?php if (get_instance()->app_modules->is_active('shift_report')) { ?>
                                    <a href="#" class="btn btn-warning pull-left" data-toggle="modal"
                                        data-target="#shift_report_modal">Shift Report</a>
                                <?php } ?>
                            </div>
                        </div>


                        <div id="visits_table_container">
                            <?php $this->load->view('visits_list_template'); ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (is_admin()) { ?>
    <a href="<?php echo admin_url('patients/settings'); ?>" class="btn btn-info"
        style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
        <i class="fa fa-cogs" style="font-size: 20px;"></i>
    </a>
<?php } ?>
<?php init_tail(); ?>
<script>
    $(document).ready(function () {
        // Toggle Details Logic - delegated event for AJAX loaded content
        $(document).on('click', '.toggle-details', function () {
            var targetId = $(this).data('target');
            var invoiceId = $(this).data('invoice-id');
            var row = $(targetId);
            var icon = $(this).find('i');

            if (row.is(':visible')) {
                row.hide();
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                row.show();
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');

                // Load details if not loaded
                var body = $('#body_' + invoiceId);
                // Check if 'Loading...' text is present (simplest check)
                if (body.text().indexOf('Loading...') !== -1) {
                    $.getJSON('<?php echo admin_url("patients/visits/get_visit_details/"); ?>' + invoiceId, function (data) {
                        var html = '';
                        $.each(data, function (index, item) {
                            html += '<tr>';
                            html += '<td class="bold">' + item.test_name + '</td>';
                            html += '<td></td>'; // Sourcing empty as per image
                            html += '<td>' + item.status + '</td>';
                            html += '<td>' + item.staff_name + '</td>';
                            html += '</tr>';
                        });
                        body.html(html);
                    });
                }
            }
        });

        // Real-time Filters
        function fetchVisits(pageUrl) {
            var url = pageUrl || '<?php echo admin_url("patients/visits/index"); ?>';
            var data = {
                user_id: $('#user_id').val(),
                from_date: $('input[name="from_date"]').val(),
                to_date: $('input[name="to_date"]').val(),
                status: $('#status').val(),
                payment_status: $('#payment_status').val(),
                search: $('#search').val(),
                limit: $('#limit').val(),
                sort_order: '<?php echo $sort_order; ?>'
            };

            // Toggle Reset Button
            if (data.user_id || data.from_date || data.to_date || data.search || data.status || data.payment_status) {
                $('#reset_filters').show();
            } else {
                $('#reset_filters').hide();
            }

            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                dataType: 'json', // Expect JSON
                success: function (response) {
                    // Update Table
                    $('#visits_table_container').html(response.html);

                    // Update Stats
                    if (response.stats) {
                        $('#stat_total_visits').text(response.stats.total_visits);
                        $('#stat_unique_patients').text(response.stats.unique_patients);
                        $('#stat_total_tests').text(response.stats.total_tests ? response.stats.total_tests : 0);
                        $('#stat_unique_doctors').text(response.stats.unique_doctors);
                    }
                },
                error: function (xhr) {
                    // Fallback for non-JSON responses (legacy support if needed, though we changed controller)
                    if (xhr.status == 200) {
                        // Maybe it returned HTML directly?
                        // $('#visits_table_container').html(xhr.responseText);
                        console.error("Expected JSON but got something else", xhr);
                    }
                }
            });
        }


        // Debounce function
        function debounce(func, wait) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    func.apply(context, args);
                }, wait);
            };
        }

        // Event Listeners
        $('#user_id').on('change', function () { fetchVisits(); });
        $('input[name="from_date"]').on('change', function () { fetchVisits(); });
        $('input[name="to_date"]').on('change', function () { fetchVisits(); });
        $('#status').on('change', function () { fetchVisits(); });
        $('#payment_status').on('change', function () { fetchVisits(); });
        $('#limit').on('change', function () { fetchVisits(); });
        $('#search').on('keyup', debounce(function () { fetchVisits(); }, 500));

        // Pagination Click Intercept
        $(document).on('click', '#ajax_pagination a', function (e) {
            e.preventDefault();
            var href = $(this).attr('href');
            if (href && href != '#') {
                fetchVisits(href);
            }
        });

        // Reset Filter
        $('#reset_filters').on('click', function () {
            $('#user_id').selectpicker('val', '');
            $('input[name="from_date"]').val('');
            $('input[name="to_date"]').val('');
            $('#status').selectpicker('val', '');
            $('#payment_status').selectpicker('val', '');
            $('#search').val('');
            fetchVisits();
        });

    });
</script>
<div class="modal fade" id="shift_report_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Shift Report</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="shift_report_date" id="shift_report_date" class="form-control"
                        value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Select User</label>
                    <select name="shift_report_user" id="shift_report_user" class="form-control selectpicker">
                        <option value="">Select User</option>
                        <?php foreach ($staff_members as $s) { ?>
                            <option value="<?php echo $s['staffid']; ?>" <?php echo (get_staff_user_id() == $s['staffid']) ? 'selected' : ''; ?>>
                                <?php echo $s['firstname'] . ' ' . $s['lastname']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Report</label>
                    <select name="shift_report_type" id="shift_report_type" class="form-control selectpicker">
                        <option value="shift_report_hospital">Shift Report (Only Hospital)</option>
                        <option value="shift_report_lab">Shift Report (Only Lab)</option>
                        <option value="overall_collection_hospital">Overall Collection (Only Hospital)</option>
                        <option value="overall_collection_lab">Overall Collection (Only Lab)</option>
                        <option value="consolidated_shift_hospital_lab">Consolidated Shift (Hospital + Lab)</option>
                        <option value="consolidated_overall_hospital_lab">Consolidated Overall (Hospital + Lab)</option>
                        <option value="consolidated_all_groups_shift">Consolidated all groups Shift</option>
                        <option value="consolidated_overall_all_groups">Consolidated Overall all groups</option>
                        <option value="general_userwise_shift">General Userwise Shift</option>
                        <option value="business_userwise_shift">Business Userwise Shift</option>
                        <option value="transactions_userwise_shift">Transactions Userwise Shift</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" id="btn_shift_generate">Generate Report</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        function getShiftReportParams() {
            var date = $('#shift_report_date').val();
            var staff_id = $('#shift_report_user').val();
            var type = $('#shift_report_type').val();

            if (!date) { alert('Please select a date'); return null; }
            if (!staff_id) { alert('Please select a user'); return null; }

            return { date: date, staff_id: staff_id, type: type };
        }

        $('#btn_shift_generate').click(function () {
            var params = getShiftReportParams();
            if (params) {
                var url = '<?php echo admin_url("shift_report/report"); ?>?date=' + params.date + '&staff_id=' + params.staff_id + '&type=' + params.type;
                window.open(url, '_blank');
            }
        });
    });
</script>
<!-- Estimate Modal Wrapper -->
<div id="estimate_modal_wrapper"></div>

<script>
    // Estimate Modal Dynamic Loader
    $(document).ready(function () {
        $('#estimate_btn').on('click', function () {
            if ($('#estimate_modal').length == 0) {
                // Load Modal via AJAX
                $.get(admin_url + 'patients/visits/get_estimate_modal', function (response) {
                    $('#estimate_modal_wrapper').html(response);

                    // Initialize Selectpicker for dynamic content
                    init_selectpicker();

                    $('#estimate_modal').modal('show');
                });
            } else {
                // Already Loaded, just show
                $('#estimate_modal').modal('show');
            }
        });
    });
</script>
</body>

</html>