<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #sidebar_toggle_btn {
        position: absolute;
        top: 50%;
        left: -14px;
        transform: translateY(-50%);
        z-index: 1000;
        background: #fff;
        border: 1px solid #e5e5e5;
        border-left: 0;
        border-radius: 0 50% 50% 0;
        padding: 15px 10px 15px 5px;
        /* Larger hit area */
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        outline: none;
        transition: all 0.2s ease;
    }

    #sidebar_toggle_btn:hover {
        background: #f0f0f0;
        padding-right: 15px;
        /* Subtle hover effect */
    }

    #main_content {
        transition: width 0.3s ease;
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-3" id="patient_sidebar" style="display:none;">
                <?php $this->load->view('patients/partials/sidebar_patients'); ?>
            </div>
            <div class="col-md-12" id="main_content" style="position: relative; min-height: 500px;">
                <button type="button" id="sidebar_toggle_btn" onclick="toggle_sidebar()" title="Toggle Sidebar">
                    <i class="fa fa-chevron-right"></i>
                </button>
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <a href="<?php echo admin_url('patients/visits'); ?>" class="btn btn-default mright5"><i
                                    class="fa fa-arrow-left"></i></a>

                            <div class="pull-right">
                                <button type="button" class="btn btn-info mright5" id="estimate_btn">Estimate</button>
                                <?php if (isset($visit_data) && $visit_data) { ?>
                                    <?php if (has_permission('patients', '', 'refunds')) { ?>
                                        <button type="button" class="btn btn-default mright5"
                                            onclick="load_visit_modal('refunds')">Refunds</button>
                                    <?php } ?>
                                    <?php if (has_permission('patients', '', 'history')) { ?>
                                        <button type="button" class="btn btn-default"
                                            onclick="load_visit_modal('history')">History</button>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <?php
                            if (isset($patient)) {
                                echo $patient->full_name;
                                if (!empty($patient->mr_number)) {
                                    echo ' <span class="label label-info mleft5" style="font-size:12px;">MR No: ' . $patient->mr_number . '</span>';
                                }
                                if (isset($patient->latest_visit_code) && !empty($patient->latest_visit_code)) {
                                    echo ' <span class="label label-warning mleft5" style="font-size:12px;">Visit No: ' . $patient->latest_visit_code . '</span>';
                                }
                            } else {
                                echo $title;
                            }
                            ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php
                        $show_optional = false;
                        $toggle_text = 'If you want to add more details, Click here';
                        if (isset($patient)) {
                            if (!empty($patient->attender_name) || !empty($patient->email) || !empty($patient->address) || !empty($patient->referral_lab_id) || !empty($patient->company_id) || !empty($patient->prescription_file)) {
                                $show_optional = true;
                                $toggle_text = 'Hide additional patient details, Click here';
                            }
                        }
                        ?>
                        <?php echo form_open_multipart($this->uri->uri_string(), ['autocomplete' => 'off']); ?>
                        <input type="hidden" name="create_visit" value="1">
                        <?php if (isset($visit_data) && $visit_data) { ?>
                            <input type="hidden" name="existing_invoice_id" value="<?php echo $visit_data->invoice_id; ?>">
                        <?php } ?>

                        <!-- Patient Details Partial -->
                        <?php $this->load->view('patients/partials/patient_details'); ?>

                        <!-- Billing Items Partial -->
                        <?php $this->load->view('patients/partials/billing_items'); ?>

                        <!-- Payment Summary Partial -->
                        <?php $this->load->view('patients/partials/payment_summary'); ?>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="referral_doctor_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Quick Add Referral Doctor</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-control" id="rd_full_name">
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" class="form-control" id="rd_mobile_number">
                </div>
                <div class="form-group">
                    <label>Area</label>
                    <input type="text" class="form-control" id="rd_area">
                </div>
                <input type="hidden" id="rd_profile_type" value="Referral">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" id="save_referral_doctor">Save</button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(document).ready(function () {
        const urlParams = new URLSearchParams(window.location.search);
        const printVisitId = urlParams.get('print_visit_id');
        if (printVisitId && printVisitId !== 'undefined') {
            // Create hidden iframe
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = admin_url + 'patients/visits/print_op_bill/' + printVisitId;
            document.body.appendChild(iframe);
            // Note: The print view itself has window.onload = window.print(); which works in iframe usually.
        }
    });

    var lab_test_statuses = <?php echo json_encode($lab_test_statuses); ?>;
    var item_statuses_grouped = <?php echo json_encode($item_statuses_grouped); ?>;
    var payment_modes = <?php echo json_encode($payment_modes); ?>;
    var patients_name_format = '<?php echo get_option("patients_name_format"); ?>';
    var billing_rules = <?php echo get_option('patients_billing_rules') ? get_option('patients_billing_rules') : '{}'; ?>;
    var current_user_fullname = '<?php echo get_staff_full_name(get_staff_user_id()); ?>';
    var doctor_service_items = <?php echo json_encode($doctor_service_items); ?>;

    function toggle_sidebar() {
        var sidebar = $('#patient_sidebar');
        var main = $('#main_content');
        var icon = $('#sidebar_toggle_btn i');

        if (sidebar.is(':visible')) {
            sidebar.hide();
            main.removeClass('col-md-9').addClass('col-md-12');
            icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
        } else {
            sidebar.show();
            main.removeClass('col-md-12').addClass('col-md-9');
            icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
        }
    }

    $(document).ready(function () {
        // UID No Restriction & Counter
        $('#uid_no').on('input paste', function () {
            var val = $(this).val().replace(/\D/g, ''); // Only digits
            if (val.length > 12) val = val.slice(0, 12);
            $(this).val(val);
            $('#uid_no_counter').text(val.length + '/12');
        });
        // Init counter
        if ($('#uid_no').length > 0) {
            $('#uid_no').trigger('input');
        }

        // Mobile Number Counter
        $('#phonenumber_input').on('input paste keyup', function () {
            var val = $(this).val();
            $('#mobile_counter').text(val.length + '/10');
        });
        // Init mobile counter
        if ($('#phonenumber_input').length > 0) {
            $('#phonenumber_input').trigger('input');
        }

        // Patient Search Logic
        var searchTimeout;
        var patientSuggestions = $('#patient_suggestions');

        $('#phonenumber_input').on('input', function () {
            var mobile = $(this).val().replace(/[^0-9]/g, '');
            // Update count handled by other handler

            if (mobile.length >= 4) { // Start searching from 4 digits? User said 10 digits strict in appointments but search starts at 5. Let's start at 4 or 5.
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    $.post(admin_url + 'patients/visits/search_patient', {
                        mobile: mobile,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    }, function (response) {
                        var patients = JSON.parse(response);

                        // Converted to Selectable Table
                        patientSuggestions.empty();
                        if (patients.length > 0) {
                            patientSuggestions.show();
                            $.each(patients, function (index, patient) {
                                var mr_display = patient.mr_number ? 'MR: ' + patient.mr_number : 'No MR';
                                var item = $('<a href="#" class="list-group-item list-group-item-action"></a>');
                                item.html('<strong>' + patient.name + '</strong> (' + patient.phone + ')<br><small>' + mr_display + '</small>');
                                item.on('click', function (e) {
                                    e.preventDefault();
                                    // Redirect to Add Visit for this patient
                                    // window.location.href = admin_url + 'patients/visits/add/' + patient.userid;

                                    // REAL-TIME POPULATION
                                    $.get(admin_url + 'patients/visits/get_patient_json/' + patient.userid, function (response) {
                                        var p = JSON.parse(response);
                                        if (p) {
                                            $('input[name="mobile_number"]').val(p.phonenumber);
                                            $('select[name="title_id"]').selectpicker('val', p.title_id);
                                            $('input[name="full_name"]').val(p.full_name || (p.firstname + ' ' + p.lastname));
                                            $('select[name="gender"]').selectpicker('val', p.gender);

                                            // Age/DOB Logic
                                            if (p.age_unit == 'DOB' && p.dob && p.dob != '0000-00-00') {
                                                var parts = p.dob.split('-');
                                                $('input[name="age"]').val(parts[2] + '-' + parts[1] + '-' + parts[0]);
                                            } else {
                                                $('input[name="age"]').val(p.age);
                                            }
                                            $('select[name="age_unit"]').selectpicker('val', p.age_unit || 'Years');

                                            $('input[name="uid_no"]').val(p.uid_no).trigger('input');
                                            $('input[name="email"]').val(p.email);
                                            $('textarea[name="address"]').val(p.address);
                                            $('select[name="attender_title_id"]').selectpicker('val', p.attender_title_id);
                                            $('input[name="attender_name"]').val(p.attender_name);

                                            $('select[name="referral_lab_id"]').selectpicker('val', p.referral_lab_id);
                                            $('select[name="company_id"]').selectpicker('val', p.company_id);

                                            if (p.last_primary_doctor_id) {
                                                $('select[name="primary_doctor_id"]').selectpicker('val', p.last_primary_doctor_id);
                                            }

                                            // Update Form Action
                                            var newAction = admin_url + 'patients/visits/add/' + patient.userid;
                                            $('form').attr('action', newAction);

                                            // Update Browser Address Bar (History API)
                                            window.history.pushState({ path: newAction }, '', newAction);

                                            // Show Optional Fields if data exists
                                            if (p.attender_name || p.email || p.address || p.referral_lab_id || p.company_id) {
                                                $('#optional_fields').show();
                                                $('#optional_fields_toggle').text('Hide additional patient details, Click here');
                                            }

                                            patientSuggestions.hide();
                                        }
                                    });
                                });
                                patientSuggestions.append(item);
                            });
                            // A                                                                                       dd "Add New Patient" Option
                            var newItem = $('<a href="#" class="list-group-item list-group-item-action list-group-item-success" style="font-weight:bold;"></a>');
                            newItem.html('<i class="fa fa-plus"></i> Add New Patient');
                            newItem.on('click', function (e) {
                                e.preventDefault();
                                patientSuggestions.empty().hide();
                                // Optional: Clear other fields if they were partly filled? 
                                // For now, just hiding suggestion allows them to proceed with current mobile number
                            });
                            patientSuggestions.append(newItem);
                        } else {
                            patientSuggestions.hide();
                        }
                    });

                }, 300);
            } else {
                patientSuggestions.hide();
            }
        });

        // Hide suggestions when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#phonenumber_input').length && !$(e.target).closest('#patient_suggestions').length) {
                patientSuggestions.hide();
            }
        });

        // Patient Name Formatting
        $('input[name="full_name"]').on('input', function () {
            if (patients_name_format === 'full_caps') {
                var start = this.selectionStart, end = this.selectionEnd;
                $(this).val($(this).val().toUpperCase());
                this.setSelectionRange(start, end);
            } else if (patients_name_format === 'title_case') {
                var start = this.selectionStart, end = this.selectionEnd;
                var str = $(this).val();
                str = str.toLowerCase().replace(/(?:^|\s)\S/g, function (a) { return a.toUpperCase(); });
                $(this).val(str);
                this.setSelectionRange(start, end);
            }
        });

        // ... (Existing events) ...

        // Items Filter Logic
        $('.filter-tab').on('click', function (e) {
            e.preventDefault();
            // Update active state
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');

            var groupName = $(this).data('group');
            if (groupName == 'all') {
                $('#billing_items_table tr.item').show();
            } else {
                $('#billing_items_table tr.item').each(function () {
                    if ($(this).data('group-name') == groupName) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });

        // ... (Rest of existing JS) ...
        $('select[name="title_id"]').on('change', function () {
            var titleText = $(this).find("option:selected").text().trim();
            if (titleText === 'Mr.') {
                $('select[name="gender"]').selectpicker('val', 'Male');
            } else if (titleText === 'Ms.' || titleText === 'Mrs.') {
                $('select[name="gender"]').selectpicker('val', 'Female');
            }
        });

        $('select[name="gender"]').on('change', function () {
            var gender = $(this).val();
            if (gender === 'Male') {
                var mrId = $('select[name="title_id"] option').filter(function () { return $(this).text().trim() == "Mr."; }).val();
                if (mrId) $('select[name="title_id"]').selectpicker('val', mrId);
            } else if (gender === 'Female') {
                var msId = $('select[name="title_id"] option').filter(function () { return $(this).text().trim() == "Ms."; }).val();
                if (!msId) msId = $('select[name="title_id"] option').filter(function () { return $(this).text().trim() == "Mrs."; }).val();
                if (msId) $('select[name="title_id"]').selectpicker('val', msId);
            }
        });

        // ADDED: Referral Lab Change Handler for Dynamic Pricing
        $('select[name="referral_lab_id"]').on('change', function () {
            var referralLabId = $(this).val();
            // Iterate over all items in the table
            $('#billing_items_table tr.item').each(function () {
                var row = $(this);
                var itemId = row.find('input[name*="[id]"]').val();
                var rateInput = row.find('input.item_rate');

                // Skip if item id invalid
                if (!itemId) return;

                // We need to re-fetch price for this item with new referral lab
                $.get(admin_url + 'patients/visits/get_item_details/' + itemId, {
                    referral_lab_id: referralLabId
                }).done(function (response) {
                    var item = typeof response === 'string' ? JSON.parse(response) : response;
                    if (item && !item.error) {
                        // Update Rate
                        rateInput.val(parseFloat(item.rate));
                        // Re-calculate total
                        calculate_total();

                        // Visual feedback? maybe flash the input
                        rateInput.css('background-color', '#dff0d8');
                        setTimeout(function () { rateInput.css('background-color', ''); }, 500);
                    }
                });
            });
        });

        $('#save_referral_doctor').on('click', function () {
            var fullName = $('#rd_full_name').val();
            var mobile = $('#rd_mobile_number').val();
            var area = $('#rd_area').val();

            if (fullName == '') { alert('Full Name is required'); return; }

            $.post('<?php echo admin_url("patients/add_referral_doctor"); ?>', {
                full_name: fullName,
                mobile_number: mobile,
                area: area,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            }, function (response) {
                var res = JSON.parse(response);
                if (res.success) {
                    var newOption = new Option(res.name, res.id, true, true);
                    $('select[name="referral_doctor_id"]').append(newOption).trigger('change');
                    $('#referral_doctor_modal').modal('hide');
                    // Reset
                    $('#rd_full_name').val('');
                    $('#rd_mobile_number').val('');
                    $('#rd_area').val('');
                } else {
                    alert('Error adding doctor');
                }
            });
        });
        $('#optional_fields_toggle').on('click', function (e) {
            e.preventDefault();
            $('#optional_fields').slideToggle(function () {
                if ($(this).is(':visible')) {
                    $('#optional_fields_toggle').text('Hide additional patient details, Click here');
                } else {
                    $('#optional_fields_toggle').text('If you want to add more details, Click here');
                }
            });
        });

        // Billing Logic
        var itemIndex = <?php echo isset($tests) ? count($tests) : 0; ?>;
        calculate_total(); // Calc initial total

        // Init Quick Add Search
        // REPLACED: Use custom search to filter restricted groups
        init_ajax_search('items', '#quick_add_items', undefined, admin_url + 'patients/visits/search_items');

        $('#quick_add_items').on('change', function () {
            var itemId = $(this).val();
            if (itemId) {
                // Check for duplicates
                var exists = false;
                $('input[name*="[id]"]').each(function () {
                    if ($(this).val() == itemId) {
                        exists = true;
                        return false; // Break loop
                    }
                });

                if (exists) {
                    alert('This test is already added.');
                    $(this).val('').selectpicker('refresh');
                    return;
                }

                add_item_to_table(itemId);
                // Reset and clear completely to remove "Currently Selected" group/item
                $(this).selectpicker('val', '');
                $(this).html('').selectpicker('refresh');
                // Ensure text is reset (though refresh should handle it if empty, explicit is safer)
                $(this).parent().find('.dropdown-toggle').attr('title', 'Add Services / Packages');
                $(this).parent().find('.filter-option-inner-inner').text('Add Services / Packages');
            }
        });

        function add_item_to_table(preselectedItemId) {
            itemIndex++;
            // Temporarily create row without group name, will update on AJAX return
            var html = '<tr class="item " data-group-name="">'; // Empty group initially

            // Checkbox Column
            html += '<td class="text-center"><div class="checkbox"><input type="checkbox" class="select_item_checkbox"><label></label></div></td>';

            html += '<td>' + itemIndex + '</td>';
            // REPLACED: Select with Plain Text + Hidden Inputs
            html += '<td><span class="item_name_display"></span><input type="hidden" name="items[' + itemIndex + '][id]"><input type="hidden" name="items[' + itemIndex + '][test_id]" value=""></td>';
            html += '<td><span class="item_group_display"></span></td>'; // New Group Column
            // ADDED: User Column
            html += '<td>' + (current_user_fullname || '') + '</td>';
            html += '<td><span class="item_status_display"></span><input type="hidden" name="items[' + itemIndex + '][status]" class="item_status_input"></td>';
            html += '<td class="text-center"><div class="checkbox"><input type="checkbox" name="items[' + itemIndex + '][is_emergency]" value="1"><label></label></div></td>';
            html += '<td><input type="number" name="items[' + itemIndex + '][rate]" class="form-control item_rate" value="0"> <input type="hidden" name="items[' + itemIndex + '][description]" class="item_desc"></td>';
            // Added Delete Button
            html += '<td class="text-center"><a href="#" class="btn btn-danger btn-xs remove_item_row"><i class="fa fa-remove"></i></a></td>';

            html += '</tr>';

            $('#billing_items_table').append(html);
            var newRow = $('#billing_items_table').find('tr').last();
            init_selectpicker();

            // Auto switch to All Transactions so they see it added
            $('.filter-tab[data-group="all"]').click();

            // If preselected, trigger it
            if (preselectedItemId) {
                // Fetch item details - UPDATED to use get_item_details with referral lab context
                var referralLabId = $('select[name="referral_lab_id"]').val();

                $.get(admin_url + 'patients/visits/get_item_details/' + preselectedItemId, {
                    referral_lab_id: referralLabId
                }).done(function (response) {
                    var item = typeof response === 'string' ? JSON.parse(response) : response;

                    if (item.error) {
                        alert(item.error);
                        newRow.remove();
                        return;
                    }

                    // Set text and hidden ID
                    newRow.find('.item_name_display').text(item.description);
                    // Populate Group Name
                    newRow.find('.item_group_display').text(item.group_name);
                    // Add data attribute for filtering
                    newRow.attr('data-group-name', item.group_name);
                    newRow.attr('data-group-id', item.group_id);

                    newRow.find('input[name*="[id]"]').val(item.itemid);
                    // FIXED: Do NOT set test_id for new items. test_id implies it's an existing patient_test record.
                    newRow.find('input[name*="[test_id]"]').val('');

                    // Also populate rate/desc
                    newRow.find('input.item_rate').val(parseFloat(item.rate));
                    newRow.find('input.item_desc').val(item.description);

                    // Add Price Source Label
                    var sourceLabel = '';
                    if (item.price_source == 'referral') sourceLabel = '<span class="text-success">Referral Price</span>';
                    else if (item.price_source == 'b2b') sourceLabel = '<span class="text-info">B2B Default Price</span>';
                    else sourceLabel = '<span class="text-warning">Standard Price</span>';

                    newRow.find('.item_rate').after('<div class="price-source-label" style="font-size:10px; margin-top:2px;">' + sourceLabel + '</div>');

                    // Check Price Changable Logic
                    if (item.group_name == 'Tests') {
                        // Default is 0 if not set, strict check 0
                        if (item.is_price_changable == 0) {
                            newRow.find('input.item_rate').prop('readonly', true);
                        }

                    }

                    // Update Statuses based on Group
                    var groupStatuses = item_statuses_grouped[item.group_id] || lab_test_statuses;
                    var defaultStatus = '';
                    if (groupStatuses && groupStatuses.length > 0) {
                        // Priority: 'Regular', then first available
                        var regular = groupStatuses.find(function (s) { return s.name === 'Regular'; });
                        if (regular) {
                            defaultStatus = regular.name;
                        } else {
                            defaultStatus = groupStatuses[0].name;
                        }
                    }
                    newRow.find('.item_status_display').text(defaultStatus);
                    newRow.find('.item_status_input').val(defaultStatus);

                    calculate_total();
                });
            }
        }
        window.add_item_to_table = add_item_to_table;

        // Remove Item Row
        $('body').on('click', '.remove_item_row', function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            calculate_total();
        });



        // Calculation
        $('body').on('change keyup', '.item_rate', function () {
            calculate_total();
        });

        $('#discount_amount, select[name="discount_type"]').on('change keyup', function () {
            calculate_total();
        });

        // Payment Row Logic
        var paymentRowIndex = 0;

        $('#add_payment_row').on('click', function () {
            if ($('.payment-row').length >= 3) {
                alert('You can only add up to 3 payment methods.');
                return;
            }
            paymentRowIndex++;
            var options = '';
            if (payment_modes) {
                payment_modes.forEach(function (mode) {
                    options += '<option value="' + mode.id + '">' + mode.name + '</option>';
                });
            }

            var html = '<tr class="payment-row">' +
                '<td width="30%" style="padding-left:0; padding-right:5px; border-top:0;">' +
                '<select name="payments[' + paymentRowIndex + '][paymentmode]" class="selectpicker" data-width="100%">' + options + '</select>' +
                '</td>' +
                '<td style="padding-left:0; padding-right:5px; border-top:0;">' +
                '<input type="number" name="payments[' + paymentRowIndex + '][amount]" class="form-control text-right payment_amount_input" value="" placeholder="Amount" step="0.01" min="0">' +
                '</td>' +
                '<td width="30px" style="vertical-align: middle; border-top:0;">' +
                '<a href="#" class="btn btn-danger btn-xs remove_payment_row"><i class="fa fa-remove"></i></a>' +
                '</td>' +
                '</tr>' +
                '<tr class="payment-note-row">' +
                '<td colspan="3" style="padding-left:0; padding-right:5px; border-top:0; padding-bottom:10px;">' +
                '<input type="text" name="payments[' + paymentRowIndex + '][note]" class="form-control" placeholder="Remarks">' +
                '</td>' +
                '</tr>';

            $('#payment_rows').append(html);
            init_selectpicker();
            updatePaymentOptions();
        });

        // Detect Duplicate Payment Modes & Disable Used Options
        $('body').on('change', 'select[name*="[paymentmode]"]', function () {
            updatePaymentOptions();
        });

        $('body').on('click', '.remove_payment_row', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            // Remove the next row (remarks) as well
            row.next('.payment-note-row').remove();
            row.remove();
            calculate_total();
            updatePaymentOptions();
        });

        function updatePaymentOptions() {
            // Collect all selected values
            var selectedValues = [];
            $('select[name*="[paymentmode]"]').each(function () {
                var val = $(this).val();
                if (val) {
                    selectedValues.push(val);
                }
            });

            // Update each dropdown
            $('select[name*="[paymentmode]"]').each(function () {
                var currentSelect = $(this);
                var myValue = currentSelect.val();

                currentSelect.find('option').each(function () {
                    var optionValue = $(this).val();
                    if (optionValue && selectedValues.includes(optionValue) && optionValue != myValue) {
                        $(this).attr('disabled', true);
                    } else {
                        $(this).removeAttr('disabled');
                    }
                });

                // Refresh selectpicker
                currentSelect.selectpicker('refresh');
            });
        }

        $('body').on('input', '.payment_amount_input', function () {
            var $this = $(this);
            var val = parseFloat($this.val());
            if (isNaN(val)) val = 0;

            // Calculate max allowed for this input
            var netAmount = parseFloat($('.total').text().replace(/,/g, '')) || 0;
            var historicPaid = parseFloat($('#historic_total_paid').val()) || 0;

            var otherPayments = 0;
            $('.payment_amount_input').not($this).each(function () {
                var p = parseFloat($(this).val());
                if (!isNaN(p)) otherPayments += p;
            });

            var maxAllowed = netAmount - historicPaid - otherPayments;
            // Floating point precision fix
            maxAllowed = parseFloat(maxAllowed.toFixed(2));

            // Remove previous error/return msg
            $this.next('.return-msg').remove();

            if (val > maxAllowed) {
                // Show Return Amount instead of clamping
                var returnAmount = val - maxAllowed;
                $this.after('<small class="text-danger return-msg" style="display:block; margin-top:2px;">Return Amount: ' + returnAmount.toFixed(2) + '</small>');
            }

            calculate_total();
        });

        function calculate_total() {
            var subtotal = 0;
            $('.item_rate').each(function () {
                var rate = parseFloat($(this).val());
                if (!isNaN(rate)) {
                    subtotal += rate;
                }
            });
            // Ensure subtotal is fixed to 2 decimals for calculations
            subtotal = parseFloat(subtotal.toFixed(2));

            $('.subtotal').text(subtotal.toFixed(2));

            var discount = 0;
            var discountType = $('select[name="discount_type"]').val();
            var discountAmount = parseFloat($('#discount_amount').val());

            if (isNaN(discountAmount) || discountAmount < 0) {
                discountAmount = 0;
                // $('#discount_amount').val(0); // Allow empty
            }

            if (discountType == 'fixed') {
                if (discountAmount > subtotal) {
                    discountAmount = subtotal;
                    $('#discount_amount').val(subtotal);
                }
                discount = discountAmount;
            } else if (discountType == '%') {
                if (discountAmount > 100) {
                    discountAmount = 100;
                    $('#discount_amount').val(100);
                }
                discount = (subtotal * discountAmount) / 100;
            }

            // Show calculated amount in the new row
            $('#discount_total_display').text(discount.toFixed(2));

            var total = subtotal - discount;
            $('.total').text(total.toFixed(2));

            var historicPaid = parseFloat($('#historic_total_paid').val());
            if (isNaN(historicPaid)) historicPaid = 0;

            // Prevent Overpayment Logic
            var maxPayable = total - historicPaid;
            if (maxPayable < 0) maxPayable = 0;

            var paid = 0;
            $('.payment_amount_input').each(function () {
                var pVal = parseFloat($(this).val());
                if (!isNaN(pVal)) paid += pVal;
            });

            // if (paid > maxPayable) {
            //    // Relaxed overpayment check for multiple splits
            // }

            var due = total - historicPaid - paid;
            // Clamp display to 0 if negative
            $('.amount_due').text((due < 0 ? 0 : due).toFixed(2));

            check_billing_rules(total, historicPaid, paid);
        }
        window.calculate_total = calculate_total;

        function check_billing_rules(totalNet, historicPaid, currentPaid) {
            // 1. Calculate Minimum Required for each Item based on its Group Rule
            var minRequired = 0;
            var groupDetails = {}; // { groupId: { name: '', rule: '', amount: 0 } }

            $('#billing_items_table tr.item').each(function () {
                var $row = $(this);
                var groupId = $row.data('group-id');
                var groupName = $row.data('group-name') || 'Unknown Group'; // Fetch name
                var rate = parseFloat($row.find('.item_rate').val()) || 0;

                var rule = billing_rules[groupId] || 'none';
                var requiredForThis = 0;

                if (rule === 'full') {
                    requiredForThis = rate;
                } else if (rule === 'half') {
                    requiredForThis = rate * 0.5;
                }

                if (requiredForThis > 0) {
                    if (!groupDetails[groupId]) {
                        groupDetails[groupId] = { name: groupName, rule: rule, amount: 0 };
                    }
                    groupDetails[groupId].amount += requiredForThis;
                }

                minRequired += requiredForThis;
            });

            var subtotal = parseFloat($('.subtotal').text()) || 1;
            var ratio = totalNet / subtotal;
            if (ratio > 1) ratio = 1;

            // Scale total requirement
            minRequired = minRequired * ratio;

            // Also scale group details for accuracy? 
            // Technically, if we scale total, the individual group reqs are also scaled.
            // But for message simplicity, we can just list the groups involved.

            var totalPaidSoFar = historicPaid + currentPaid;

            // Check
            $('#billing_rule_alert').remove();
            var isValid = true;
            var missing = 0;
            var msg = '';

            if (totalPaidSoFar < (minRequired - 0.01)) {
                isValid = false;
                missing = minRequired;

                // Build Detailed Message
                var details = [];
                for (var gid in groupDetails) {
                    var g = groupDetails[gid];
                    // Apply ratio to group amount for display?
                    var gAmount = g.amount * ratio;
                    var ruleText = g.rule === 'full' ? 'Full Payment' : '50% Payment';
                    details.push(g.name + ' (' + ruleText + '): ' + gAmount.toFixed(2));
                }

                msg = 'Minimum payment required: ' + minRequired.toFixed(2) + '\nDetails:\n' + details.join('\n');

                /* Red Alert Banner removed as per request */
            }
            return { valid: isValid, required: minRequired, message: msg };
        }
        window.check_billing_rules = check_billing_rules;

        // Intercept Form Submission
        $('form').on('submit', function (e) {
            var netAmount = parseFloat($('.total').text().replace(/,/g, '')) || 0;
            var historicPaid = parseFloat($('#historic_total_paid').val()) || 0;

            // Calc current paid from inputs
            var currentPaidInput = 0;
            $('.payment_amount_input').each(function () {
                var pVal = parseFloat($(this).val());
                if (!isNaN(pVal)) currentPaidInput += pVal;
            });

            // 1. Enforce Billing Rules
            var ruleCheck = check_billing_rules(netAmount, historicPaid, currentPaidInput);
            if (!ruleCheck.valid) {
                alert('Action Blocked:\n' + ruleCheck.message);
                e.preventDefault();
                return false;
            }

            // 2. Correct Overpayments (Existing Logic)
            var remainingPayable = netAmount - historicPaid;
            remainingPayable = parseFloat(remainingPayable.toFixed(2));

            $('.payment_amount_input').each(function () {
                var $input = $(this);
                var val = parseFloat($input.val()) || 0;

                if (val > remainingPayable) {
                    $input.val(remainingPayable.toFixed(2));
                    remainingPayable = 0;
                } else {
                    remainingPayable -= val;
                    remainingPayable = parseFloat(remainingPayable.toFixed(2));
                }

                if (remainingPayable < 0) remainingPayable = 0;
            });

            return true;
        });

        // Age/DOB Logic
        // Age/DOB Logic - DELEGATED
        $('body').on('change', '#age_unit', function () {
            var unit = $(this).val();
            var $input = $('#age_input');

            // cleanup datepicker
            if ($input.hasClass('hasDatepicker')) {
                $input.datepicker('destroy');
                $input.removeClass('hasDatepicker');
            }
            if ($input.data('datetimepicker')) {
                $input.datetimepicker('destroy');
            }

            // Reset Event Handlers to prevent stacking
            $input.off('keypress paste input');
            $input.removeAttr('oninput'); // Remove inline if present

            // Reset attributes
            $input.val('');

            if (unit == 'DOB') {
                $input.attr('type', 'text');
                $input.attr('placeholder', 'DD-MM-YYYY');

                $input.datetimepicker({
                    format: 'd-m-Y',
                    timepicker: false,
                    scrollInput: false,
                    dayOfWeekStart: 1, // Monday
                    maxDate: 0, // Prevent future selection
                    closeOnDateSelect: true
                });

            } else {
                // Numeric Only Mode
                $input.attr('type', 'text');
                $input.attr('inputmode', 'numeric');
                $input.attr('placeholder', 'Enter Age in ' + unit);

                // Strict Number Restriction
                $input.on('keypress', function (e) {
                    // Allow only 0-9
                    // charCode 48-57 are numbers
                    if (e.which < 48 || e.which > 57) {
                        e.preventDefault();
                    }
                });

                $input.on('input paste', function () {
                    var val = $(this).val().replace(/\D/g, ''); // Replace any non-digit
                    if (val.length > 3) val = val.slice(0, 3);
                    $(this).val(val);
                });
            }
            // Trigger age update to clear text
            updateAgeText();
        });
        // Remove Trigger on load - handled by AJAX callback


        // Calculate Age from DOB
        function updateAgeText() {
            var unit = $('#age_unit').val();
            var val = $('#age_input').val();
            var $display = $('#age_calc_text');
            $display.text('');

            if (unit == 'DOB' && val) {
                // Parse DD-MM-YYYY
                var items = val.split('-');
                if (items.length == 3) {
                    var d = parseInt(items[0]);
                    var m = parseInt(items[1]) - 1; // zero based
                    var y = parseInt(items[2]);

                    var dobDate = new Date(y, m, d);
                    var today = new Date();

                    if (!isNaN(dobDate.getTime())) {
                        var ageYears = today.getFullYear() - dobDate.getFullYear();
                        var ageMonths = today.getMonth() - dobDate.getMonth();
                        var ageDays = today.getDate() - dobDate.getDate();

                        if (ageDays < 0) {
                            ageMonths--;
                            // Days in previous month
                            var prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                            ageDays += prevMonth.getDate();
                        }
                        if (ageMonths < 0) {
                            ageYears--;
                            ageMonths += 12;
                        }

                        if (ageYears < 0) {
                            $display.text('Invalid Date (Future)');
                        } else {
                            $display.text('Age: ' + ageYears + ' Yrs, ' + ageMonths + ' Months, ' + ageDays + ' Days old');
                        }
                    }
                }
            }
        }

        $('body').on('change input', '#age_input', updateAgeText);
        $('body').on('change', '#age_unit', updateAgeText);
        // Run once on load if needed
        setTimeout(updateAgeText, 500);

        // Real-time Patient Search by Mobile
        $('input[name="mobile_number"]').attr('maxlength', '10');

        // Auto-add Consultation Fee on Primary Doctor Change
        // Auto-add Consultation Fee on Primary Doctor Change - DELEGATED
        $('body').on('change', 'select[name="primary_doctor_id"]', function () {
            var doctorId = $(this).val();
            if (doctorId && typeof doctor_service_items !== 'undefined' && doctor_service_items[doctorId]) {
                var itemId = doctor_service_items[doctorId];
                // Fix: Ensure itemId is valid and not "0" string or 0 number
                if (itemId && itemId != 0 && itemId != '0') {
                    // Check if item already exists
                    var exists = false;
                    $('#billing_items_table input[name*="[id]"]').each(function () {
                        if ($(this).val() == itemId) {
                            exists = true;
                        }
                    });

                    if (!exists) {
                        add_item_to_table(itemId);
                    }
                }
            }
        });

        // jQuery UI Autocomplete REMOVED to prevent conflict with custom suggestions
        /*
        if (typeof $.ui !== 'undefined' && typeof $.ui.autocomplete !== 'undefined') {
            // Code block removed
        }
        */

        // Intercept Form Submission to Correct Overpayments
        $('form').on('submit', function () {
            var netAmount = parseFloat($('.total').text().replace(/,/g, '')) || 0;
            var historicPaid = parseFloat($('#historic_total_paid').val()) || 0;
            var remainingPayable = netAmount - historicPaid;

            // Floating point fix
            remainingPayable = parseFloat(remainingPayable.toFixed(2));

            $('.payment_amount_input').each(function () {
                var $input = $(this);
                var val = parseFloat($input.val()) || 0;

                if (val > remainingPayable) {
                    // Correct the value to the remaining payable amount
                    $input.val(remainingPayable.toFixed(2));
                    remainingPayable = 0;
                } else {
                    remainingPayable -= val;
                    remainingPayable = parseFloat(remainingPayable.toFixed(2));
                }

                if (remainingPayable < 0) remainingPayable = 0;
            });

            return true;
        });


    });



    // Auto-Trigger on Load (Moved from AJAX callback)
    $(document).ready(function () {
        // Initialize Selectpicker (if not already handled by layout)
        init_selectpicker();

        // Trigger Age Logic
        var initialAgeVal = $('#age_input').val();
        $('#age_unit').trigger('change');
        if (initialAgeVal) {
            $('#age_input').val(initialAgeVal);
        }
        if (typeof updateAgeText === 'function') updateAgeText();

        // Process URL Parameters from Estimate Modal
        var urlParams = new URLSearchParams(window.location.search);
        var estLab = urlParams.get('estimate_lab');
        var estItems = urlParams.get('estimate_items');

        if (estLab) {
            $('select[name="referral_lab_id"]').selectpicker('val', estLab).trigger('change');
        }

        if (estItems) {
            var items = estItems.split(',');
            // Small delay to ensure lab change event might process or just standard add
            setTimeout(function () {
                items.forEach(function (itemId) {
                    if (itemId) {
                        // Check duplicates just in case
                        var exists = false;
                        $('input[name*="[id]"]').each(function () {
                            if ($(this).val() == itemId) exists = true;
                        });
                        if (!exists) {
                            add_item_to_table(itemId);
                        }
                    }
                });
            }, 500);
        }
    });
</script>
<script>
    function load_visit_modal(type) {
        var modal_id = '#' + type + '_modal';
        // Check if modal already exists in DOM
        if (type != 'refunds' && $(modal_id).length > 0) {
            $(modal_id).modal('show');
            return;
        }

        // If refunds modal exists, remove it to force reload
        if (type == 'refunds' && $(modal_id).length > 0) {
            $(modal_id).remove();
        }

        var invoiceId = $('input[name="existing_invoice_id"]').val();

        // Fetch and append
        $.get(admin_url + 'patients/visits/get_' + type + '_modal', { invoice_id: invoiceId }, function (response) {
            $('#modal_wrapper').append(response);
            $(modal_id).modal('show');
        });
    }

    /* Refunds JS Logic */
    $(document).ready(function () {
        // Function to calculate totals
        function calculateRefundTotal(tableId) {
            var total = 0;
            $(tableId).find('.item-checkbox:checked').each(function () {
                total += parseFloat($(this).data('rate')) || 0;
            });
            return total.toFixed(2);
        }

        // Render Items Function
        function renderRefundItems(items) {
            var tables = ['#table_rc', '#table_or', '#table_oc'];
            $.each(tables, function (i, tableId) {
                var tbody = $(tableId).find('tbody');
                tbody.empty();
                $.each(items, function (index, item) {
                    // Disable if already cancelled
                    var disabled = (item.status == 'Cancelled' || item.status == 'cancelled') ? 'disabled' : '';
                    var tr = '<tr>';
                    tr += '<td><input type="checkbox" class="item-checkbox" data-id="' + item.test_id + '" data-rate="' + item.rate + '" ' + disabled + '></td>';
                    tr += '<td>' + item.test_name + '</td>';
                    tr += '<td>' + item.rate + '</td>';
                    tr += '<td>' + item.status + '</td>';
                    tr += '</tr>';
                    tbody.append(tr);
                });
            });
        }

        // Event Delegation for Modal Show (Initialization)
        $('body').on('shown.bs.modal', '#refunds_modal', function () {
            var invoiceId = $('#refund_invoice_id').val();

            // Populate Payment Modes
            var paymentModeOptions = '';
            // Assume payment_modes global is available from add_visit
            if (typeof payment_modes !== 'undefined') {
                $.each(payment_modes, function (i, mode) {
                    paymentModeOptions += '<option value="' + mode.id + '">' + mode.name + '</option>';
                });
                $('#rc_mode, #or_mode').html(paymentModeOptions);
            }

            // Fetch Items
            if (invoiceId) {
                $.get(admin_url + 'refunds/get_visit_items/' + invoiceId, function (response) {
                    var allItems = JSON.parse(response);
                    renderRefundItems(allItems);
                });
            }
        });

        // Event Delegation for Checkboxes
        $('body').on('change', '.refund-items-table .item-checkbox', function () {
            var table = $(this).closest('table');
            var tabId = table.attr('id');
            var total = calculateRefundTotal('#' + tabId);

            if (tabId == 'table_rc') {
                $('#rc_amount').val(total);
            } else if (tabId == 'table_or') {
                $('#or_amount').val(total);
            }
        });

        // Save Button Handler
        $('body').on('click', '.save-refund-btn', function (e) {
            e.preventDefault();

            var type = $(this).data('type');
            var invoiceId = $('#refund_invoice_id').val();
            var items = [];
            var amount = 0;
            var mode = '';
            var note = '';
            var refundedOn = '';

            var tableId = '';
            if (type == 'refund_cancellation') {
                tableId = '#table_rc';
                amount = $('#rc_amount').val();
                mode = $('#rc_mode').val();
                note = $('#rc_note').val();
                refundedOn = $('#rc_date').val();
            } else if (type == 'only_refund') {
                tableId = '#table_or';
                amount = $('#or_amount').val();
                mode = $('#or_mode').val();
                note = $('#or_note').val();
                refundedOn = $('#or_date').val();
            } else if (type == 'only_cancellation') {
                tableId = '#table_oc';
                amount = 0; // Explicitly 0
                mode = '';
                note = $('#oc_note').val();
                // Current Date
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                refundedOn = yyyy + '-' + mm + '-' + dd;
            }

            // Collect Items
            $(tableId).find('.item-checkbox:checked').each(function () {
                var testId = $(this).data('id');
                items.push({ test_id: testId, amount: 0 });
            });

            // Validation
            if (type != 'only_cancellation' && (amount <= 0 || amount == '')) {
                alert('Please enter a valid amount');
                return;
            }
            if (items.length == 0 && (type == 'refund_cancellation' || type == 'only_cancellation')) {
                alert('Please select at least one item');
                return;
            }

            var data = {
                invoice_id: invoiceId,
                refund_type: type,
                amount: amount,
                payment_mode: mode,
                note: note,
                refunded_on: refundedOn,
                items: items
            };

            // Explicit CSRF Injection
            data['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

            $.post(admin_url + 'refunds/save_refund', data, function (response) {
                try {
                    var res = JSON.parse(response);
                    if (res.success) {
                        alert_float('success', res.message);
                        $('#refunds_modal').modal('hide');
                        setTimeout(function () { location.reload(); }, 1000);
                    } else {
                        alert(res.message);
                    }
                } catch (e) {
                    console.log('Error parsing response', response);
                    alert('Error saving refund: ' + response);
                }
            }).fail(function (xhr) {
                alert('Server Error: ' + xhr.status + ' ' + xhr.statusText);
            }).always(function () {
                // Re-enable button if disabled
                $('.save-refund-btn').prop('disabled', false);
            });
        });
        // Select All Handler
        $(document).on("change", "#select_all_items_master", function () {
            var isChecked = $(this).prop("checked");
            $(".select_item_checkbox").prop("checked", isChecked);
        });

        // Individual Checkbox Handler to update master
        $(document).on("change", ".select_item_checkbox", function () {
            var allChecked = $(".select_item_checkbox").length === $(".select_item_checkbox:checked").length;
            $("#select_all_items_master").prop("checked", allChecked);
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

                    // Sync Main Page Lab if exists
                    var mainRefLab = $('select[name="referral_lab_id"]').val();
                    if (mainRefLab && !$('#est_referral_lab').val()) {
                        $('#est_referral_lab').selectpicker('val', mainRefLab);
                    }
                });
            } else {
                // Already Loaded, just show
                var mainRefLab = $('select[name="referral_lab_id"]').val();
                if (mainRefLab && !$('#est_referral_lab').val()) {
                    $('#est_referral_lab').selectpicker('val', mainRefLab);
                }
                $('#estimate_modal').modal('show');
            }
        });
    });
</script>

<div id="modal_wrapper"></div>
</body>

</html>