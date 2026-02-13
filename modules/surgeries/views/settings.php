<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('surgeries', '', 'create')) { ?>

                                <a href="#" class="btn btn-info pull-left display-block mright5 new-surgery-type-btn">
                                    <?php echo _l('new_surgery_type'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">

                                    <li role="presentation" class="active">
                                        <a href="#surgery_types" aria-controls="surgery_types" role="tab"
                                            data-toggle="tab">
                                            <?php echo _l('surgery_types'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane active" id="surgery_types">
                                <?php render_datatable([
                                    _l('id'),
                                    _l('name'),
                                    _l('description'),
                                    _l('price'),
                                    _l('created_at'),
                                    _l('options'),
                                ], 'surgery-types'); ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="surgery_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('new_surgery'); ?>
                </h4>
            </div>
            <?php echo form_open('admin/surgeries/surgery', ['id' => 'surgery-form']); ?>
            <div class="modal-body">
                <!-- Content loaded via ajax or static form -->
                <input type="hidden" name="id" id="surgery_id">

                <div class="form-group">
                    <label for="patient_id" class="control-label">
                        <?php echo _l('patient'); ?>
                    </label>
                    <select name="patient_id" id="patient_id" class="selectpicker" data-width="100%"
                        data-live-search="true" title="Select Patient" required>
                        <?php
                        $patients = $this->clients_model->get();
                        foreach ($patients as $patient) {
                            echo '<option value="' . $patient['userid'] . '">' . $patient['company'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="surgery_type_id" class="control-label">
                        <?php echo _l('surgery_type'); ?>
                    </label>
                    <select name="surgery_type_id" id="surgery_type_id" class="selectpicker" data-width="100%"
                        data-live-search="true" title="Select Type" required>
                        <?php
                        $types = $this->surgeries_model->get_surgery_types();
                        foreach ($types as $type) {
                            echo '<option value="' . $type['id'] . '">' . $type['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="surgeon_id" class="control-label">
                        <?php echo _l('surgeon'); ?>
                    </label>
                    <select name="surgeon_id" id="surgeon_id" class="selectpicker" data-width="100%"
                        data-live-search="true" title="Select Surgeon" required>
                        <?php
                        $staff = $this->staff_model->get();
                        foreach ($staff as $s) {
                            echo '<option value="' . $s['staffid'] . '">' . $s['firstname'] . ' ' . $s['lastname'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php echo render_date_input('surgery_date', 'surgery_date', '', ['required' => true]); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="surgery_time" class="control-label">
                                <?php echo _l('surgery_time'); ?>
                            </label>
                            <input type="time" id="surgery_time" name="surgery_time" class="form-control" required>
                        </div>
                    </div>
                </div>

                <?php echo render_input('ot_room_number', 'ot_room_number'); ?>

                <div class="form-group">
                    <label for="status" class="control-label">
                        <?php echo _l('status'); ?>
                    </label>
                    <select name="status" id="status" class="selectpicker" data-width="100%" required>
                        <option value="scheduled">Scheduled</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <?php echo render_textarea('notes', 'notes'); ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-info">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="surgery_type_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('new_surgery_type'); ?>
                </h4>
            </div>
            <?php echo form_open('admin/surgeries/type', ['id' => 'surgery-type-form']); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="type_id">
                <?php echo render_input('name', 'name', '', 'text', ['required' => true]); ?>
                <?php echo render_textarea('description', 'description'); ?>
                <?php echo render_input('price', 'price', '', 'number', ['step' => '0.01']); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-info">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-surgeries', admin_url + 'surgeries/table/surgeries');
        initDataTable('.table-surgery-types', admin_url + 'surgeries/table/types');

        // Toggle buttons based on tab
        // logic removed as per request to remove surgeries tab
        // var updateButtons = function () {
        //     var activeTab = $('.nav-tabs li.active a').attr('href');
        //     if (activeTab === '#surgeries') {
        //         $('.new-surgery-btn').removeClass('hidden');
        //         $('.new-surgery-type-btn').addClass('hidden');
        //     } else {
        //         $('.new-surgery-btn').addClass('hidden');
        //         $('.new-surgery-type-btn').removeClass('hidden');
        //     }
        // };

        // $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        //     updateButtons();
        // });
        // updateButtons(); // Initial check

        // Open Surgery Modal
        $('.new-surgery-btn').on('click', function (e) {
            e.preventDefault();
            $('#surgery_modal').modal('show');
            $('#surgery_modal .modal-title').text('<?php echo _l("new_surgery"); ?>');
            $('#surgery-form')[0].reset();
            $('#surgery_id').val('');
            $('.selectpicker').selectpicker('refresh');
        });

        // Open Type Modal
        $('.new-surgery-type-btn').on('click', function (e) {
            e.preventDefault();
            $('#surgery_type_modal').modal('show');
            $('#surgery_type_modal .modal-title').text('<?php echo _l("new_surgery_type"); ?>');
            $('#surgery-type-form')[0].reset();
            $('#type_id').val('');
        });

        // Form Submissions
        appValidateForm($('#surgery-form'), {
            patient_id: 'required',
            surgery_type_id: 'required',
            surgeon_id: 'required',
            surgery_date: 'required',
            surgery_time: 'required'
        }, manage_surgery);

        appValidateForm($('#surgery-type-form'), {
            name: 'required'
        }, manage_surgery_type);

        function manage_surgery(form) {
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function (response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);
                    $('#surgery_modal').modal('hide');
                    $('.table-surgeries').DataTable().ajax.reload();
                }
            });
            return false;
        }

        function manage_surgery_type(form) {
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function (response) {
                response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);
                    $('#surgery_type_modal').modal('hide');
                    $('.table-surgery-types').DataTable().ajax.reload();
                    // Optional: Refresh surgery type select in surgery modal
                }
            });
            return false;
        }
    });

    // Edit functions (global scope for onClick)
    // Edit functions
    function edit_surgery(id, rowData) {
        $('#surgery_modal').modal('show');
        $('#surgery_modal .modal-title').text('<?php echo _l("edit_surgery"); ?>');
        $('#surgery-form').attr('action', admin_url + 'surgeries/surgery/' + id);
        $('#surgery_id').val(id);

        // Populate fields
        $('#patient_id').selectpicker('val', rowData.patient_id);
        $('#surgery_type_id').selectpicker('val', rowData.surgery_type_id);
        $('#surgeon_id').selectpicker('val', rowData.surgeon_id);
        $('#surgery_date').val(rowData.surgery_date);
        $('#surgery_time').val(rowData.surgery_time);
        $('#ot_room_number').val(rowData.ot_room_number);
        $('#status').selectpicker('val', rowData.status);
        $('textarea[name="notes"]').val(rowData.notes);

        $('.selectpicker').selectpicker('refresh');
    }

    function edit_surgery_type(id, rowData) {
        $('#surgery_type_modal').modal('show');
        $('#surgery_type_modal .modal-title').text('<?php echo _l("edit_surgery_type"); ?>');
        $('#surgery-type-form').attr('action', admin_url + 'surgeries/type/' + id);
        $('#type_id').val(id);

        // Populate fields
        $('input[name="name"]').val(rowData.name);
        $('textarea[name="description"]').val(rowData.description);
        $('input[name="price"]').val(rowData.price);
    }
</script>
</body>

</html>