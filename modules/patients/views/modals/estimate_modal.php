<!-- Estimate Modal -->
<div class="modal fade" id="estimate_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-calculator mright5"></i> Estimate</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="est_referral_lab">Lab :</label>
                            <select id="est_referral_lab" class="selectpicker" data-width="100%"
                                data-live-search="true">
                                <option value="">Nothing Selected</option>
                                <?php foreach ($referral_labs as $lab) { ?>
                                    <option value="<?php echo $lab['staffid']; ?>">
                                        <?php echo $lab['firstname'] . ' ' . $lab['lastname']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="est_test_search">Test :</label>
                            <select id="est_test_search" class="selectpicker" data-width="100%" data-live-search="true">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mtop15">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="estimate_table"
                                style="margin-bottom:0;">
                                <thead>
                                    <tr>
                                    <tr>
                                        <th style="width:50px;">SNo</th>
                                        <th>Name</th>
                                        <th style="text-align:right;">Price</th>
                                        <th style="width:80px; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Items will be added here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer" style="padding: 15px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Total : <span id="est_total_display"
                                            style="float:right; margin-right: 50%;">0</span></h4>
                                </div>
                                <div class="col-md-6 text-right">
                                    <!-- <button type="button" class="btn btn-info" id="est_add_to_billing">Add</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form id="estimate_print_form" action="<?php echo admin_url('patients/visits/print_estimate'); ?>"
                    method="POST" target="_blank" class="hidden">
                    <input type="hidden" name="referral_lab" value="">
                    <!-- items will be appended here -->
                    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                </form>
                <div class="pull-left">
                    <!-- Left side content if any -->
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="est_print_btn">Print</button>
                <button type="button" class="btn btn-success" id="est_add_billing_main_btn">Add to Billing</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Estimate Modal Logic
    $(document).ready(function () {
        var estItemIndex = 0;
        var estTestsGroupId = '<?php echo $tests_group_id; ?>';

        // Initialize Search with Filter (Custom Init to fix auto-select bug)
        var estTestSearchOptions = {
            ajax: {
                url: admin_url + 'patients/visits/search_items',
                data: function () {
                    var data = {
                        q: '{{{q}}}'
                    };
                    if (estTestsGroupId) {
                        data.group_id = estTestsGroupId;
                    }
                    return data;
                }
            },
            locale: {
                emptyTitle: app.lang.search_ajax_empty,
                statusInitialized: app.lang.search_ajax_initialized,
                statusSearching: app.lang.search_ajax_searching,
                statusNoResults: app.lang.not_results_found,
                searchPlaceholder: app.lang.search_ajax_placeholder,
                currentlySelected: app.lang.currently_selected,
            },
            requestDelay: 500,
            cache: false,
            preprocessData: function (processData) {
                var bs_data = [];
                // Add explicit placeholder option as the first item
                // This absorbs the default highlight/auto-select behavior but is visually hidden
                bs_data.push({ value: '', text: '', style: 'height: 0; margin: 0; padding: 0;' });

                var len = processData.length;
                for (var i = 0; i < len; i++) {
                    var tmp_data = {
                        value: processData[i].id,
                        text: processData[i].name
                    };
                    if (processData[i].subtext) {
                        tmp_data.data = {
                            subtext: processData[i].subtext
                        };
                    }
                    bs_data.push(tmp_data);
                }
                return bs_data;
            },
            preserveSelectedPosition: 'after',
            preserveSelected: false
        };

        $('#est_test_search').selectpicker().ajaxSelectPicker(estTestSearchOptions);

        // Add Item via Selection Change
        $('#est_test_search').on('change', function () {
            var itemId = $(this).val();
            if (itemId) {
                // Check duplicates in Estimate Table
                var exists = false;
                $('#estimate_table tbody tr').each(function () {
                    if ($(this).data('item-id') == itemId) {
                        exists = true;
                        return false;
                    }
                });

                if (exists) {
                    alert('Item already in estimate');
                    $('#est_test_search').val('').selectpicker('refresh');
                    return;
                }

                addEstimateItem(itemId);
                $('#est_test_search').val('').selectpicker('refresh');
                // No need to reset text manually as selectpicker refresh handles it
            }
        });

        // Logic moved to change event above

        function addEstimateItem(itemId) {
            estItemIndex++;
            var referralLabId = $('#est_referral_lab').val();

            $.get(admin_url + 'patients/visits/get_item_details/' + itemId, {
                referral_lab_id: referralLabId
            }).done(function (response) {
                var item = JSON.parse(response);
                if (item.error) {
                    alert(item.error);
                    return;
                }

                var row = '<tr data-item-id="' + item.itemid + '">';
                row += '<td>' + estItemIndex + '</td>';
                row += '<td>' + item.description + '<input type="hidden" class="est-desc" value="' + item.description + '"></td>';
                row += '<td class="text-right"><span class="est-price-display">' + parseFloat(item.rate) + '</span><input type="hidden" class="est-rate" value="' + parseFloat(item.rate) + '"></td>';
                row += '<td class="text-center"><a href="#" class="btn btn-danger btn-xs est-remove-row"><i class="fa fa-remove"></i></a></td>';
                row += '</tr>';

                $('#estimate_table tbody').append(row);
                calculateEstimateTotal();
            });
        }

        // Remove Item
        $('body').on('click', '.est-remove-row', function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            calculateEstimateTotal();
            reindexEstimateRows();
        });

        function reindexEstimateRows() {
            var idx = 1;
            $('#estimate_table tbody tr').each(function () {
                $(this).find('td:eq(0)').text(idx++);
            });
            estItemIndex = idx - 1;
        }

        // Calculate Total
        function calculateEstimateTotal() {
            var total = 0;
            $('.est-rate').each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $('#est_total_display').text(total.toFixed(2));
        }

        // Referral Lab Change -> Update Prices
        $('#est_referral_lab').on('change', function () {
            var referralLabId = $(this).val();
            $('#estimate_table tbody tr').each(function () {
                var row = $(this);
                var itemId = row.data('item-id');
                if (itemId) {
                    $.get(admin_url + 'patients/visits/get_item_details/' + itemId, {
                        referral_lab_id: referralLabId
                    }).done(function (response) {
                        var item = JSON.parse(response);
                        if (item && !item.error) {
                            row.find('.est-price-display').text(parseFloat(item.rate));
                            row.find('.est-rate').val(parseFloat(item.rate));
                            calculateEstimateTotal();
                        }
                    });
                }
            });
        });

        // Add to Billing (Main Button)
        $('#est_add_billing_main_btn, #est_add_to_billing').on('click', function () {
            var refLab = $('#est_referral_lab').val();
            // Check context: Add Visit Page vs List Page
            if (typeof add_item_to_table === 'function') {
                // Add Visit Page Logic (Existing)
                var mainRefLab = $('select[name="referral_lab_id"]').val();

                if (refLab && refLab != mainRefLab) {
                    $('select[name="referral_lab_id"]').selectpicker('val', refLab).trigger('change');
                }

                // Loop through estimate items and add to main table
                $('#estimate_table tbody tr').each(function () {
                    var itemId = $(this).data('item-id');
                    // Check if already exists in main table
                    var exists = false;
                    $('input[name*="[id]"]').each(function () {
                        if ($(this).val() == itemId) exists = true;
                    });

                    if (!exists) {
                        add_item_to_table(itemId);
                    }
                });

                $('#estimate_modal').modal('hide');
            } else {
                // Visits List Page Logic (Redirect)
                var itemIds = [];
                $('#estimate_table tbody tr').each(function () {
                    var itemId = $(this).data('item-id');
                    if (itemId) itemIds.push(itemId);
                });

                if (itemIds.length > 0) {
                    var url = admin_url + 'patients/visits/add?estimate_lab=' + (refLab || '') + '&estimate_items=' + itemIds.join(',');
                    window.location.href = url;
                } else {
                    alert('Please add items to estimate first.');
                }
            }
        });

        // Print Estimate
        $('#est_print_btn').on('click', function () {
            var form = $('#estimate_print_form');
            // Clean previous inputs
            form.find('.dynamic-input').remove();

            // Add Items
            var i = 0;
            $('#estimate_table tbody tr').each(function () {
                var desc = $(this).find('.est-desc').val();
                var rate = $(this).find('.est-rate').val();

                $('<input>').attr({ type: 'hidden', class: 'dynamic-input', name: 'items[' + i + '][description]', value: desc }).appendTo(form);
                $('<input>').attr({ type: 'hidden', class: 'dynamic-input', name: 'items[' + i + '][rate]', value: rate }).appendTo(form);
                i++;
            });

            // Add Lab Name
            var labName = $('#est_referral_lab option:selected').text();
            $('<input>').attr({ type: 'hidden', class: 'dynamic-input', name: 'lab_name', value: labName }).appendTo(form);

            // Add Total
            var total = $('#est_total_display').text();
            $('<input>').attr({ type: 'hidden', class: 'dynamic-input', name: 'total', value: total }).appendTo(form);

            form.submit();
        });


    });
</script>