<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <!-- Create Token Form -->
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('create_new_token'); ?></h4>
                        <!-- You may need to add translation or use hardcoded string -->
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('token_system')); ?>

                        <?php if (get_option('token_system_workflow') != 'smart') { ?>
                            <div class="form-group">
                                <label for="doctor_id" class="control-label">Select Doctor</label>
                                <select name="doctor_id" id="doctor_id" class="form-control selectpicker"
                                    data-live-search="true" required>
                                    <option value="">-- Select Doctor --</option>
                                    <?php foreach ($doctors as $doc) { ?>
                                        <option value="<?php echo $doc['staffid']; ?>">
                                            <?php echo $doc['firstname'] . ' ' . $doc['lastname']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                Smart Mode Active: Tokens are automatically generated from Appointments. Manual creation is
                                disabled.
                            </div>
                        <?php } ?>

                        <?php if (get_option('token_system_workflow') == 'select_patient') { ?>
                            <div class="form-group">
                                <label for="patient_id" class="control-label"><?php echo _l('select_patient'); ?></label>
                                <div class="form-group">
                                    <select name="patient_id" id="patient_id" class="ajax-search"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                        data-live-search="true">
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="patient_name" id="patient_name_hidden">
                        <?php } else { ?>
                            <div class="form-group">
                                <label for="patient_name"
                                    class="control-label"><?php echo _l('patient_name_reference'); ?></label>
                                <input type="text" id="patient_name" name="patient_name" class="form-control" required
                                    autofocus <?php if (get_option('token_system_workflow') == 'smart') {
                                        echo 'disabled placeholder="Smart Mode Active - Tokens created via Appointments"';
                                    } ?>>
                            </div>
                        <?php } ?>

                        <!-- Optional: Patient Select if you want to link to existing patients
                        <div class="form-group">
                            <label for="patient_id" class="control-label">Select Patient (Optional)</label>
                            <select id="patient_id" name="patient_id" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="Non-Registered">
                                <option value=""></option>
                                <?php // Fetch and loop through patients here or use ajax search ?>
                            </select>
                        </div> 
                        -->

                        <button type="submit"
                            class="btn btn-info pull-right"><?php echo _l('generate_token'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>

            <!-- Token Queue List -->
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-margin"><?php echo _l('todays_queue'); ?> <span
                                        class="text-muted small"><?php echo date('Y-m-d'); ?></span></h4>
                            </div>
                            <div class="col-md-6">
                                <form method="get" action="<?php echo admin_url('token_system'); ?>">
                                    <div class="input-group">
                                        <select name="doctor_id" class="form-control" onchange="this.form.submit()">
                                            <option value="">All Doctors</option>
                                            <?php foreach ($doctors as $doc) { ?>
                                                <option value="<?php echo $doc['staffid']; ?>" <?php if (isset($doctor_filter) && $doctor_filter == $doc['staffid']) {
                                                       echo 'selected';
                                                   } ?>>
                                                    <?php echo $doc['firstname'] . ' ' . $doc['lastname']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="0" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('token_number'); ?></th>
                                        <th>Doctor</th>
                                        <th><?php echo _l('patient_name'); ?></th>
                                        <th><?php echo _l('token_status'); ?></th>
                                        <th><?php echo _l('token_time'); ?></th>
                                        <th><?php echo _l('token_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tokens as $token) { ?>
                                        <tr>
                                            <td><span class="label label-default"
                                                    style="font-size: 110%;"><?php echo $token['token_number']; ?></span>
                                            </td>
                                            <td><?php echo $token['doctor_name']; ?></td>
                                            <td><?php echo $token['patient_name']; ?></td>
                                            <td>
                                                <?php
                                                if ($token['status'] == 0)
                                                    echo '<span class="label label-warning">' . _l('token_status_pending') . '</span>';
                                                elseif ($token['status'] == 1)
                                                    echo '<span class="label label-info">' . _l('token_status_calling') . '</span>';
                                                elseif ($token['status'] == 2)
                                                    echo '<span class="label label-success">' . _l('token_status_completed') . '</span>';
                                                elseif ($token['status'] == 3)
                                                    echo '<span class="label label-danger">' . _l('token_status_skipped') . '</span>';
                                                ?>
                                            </td>
                                            <td><?php echo date('H:i', strtotime($token['created_at'])); ?></td>
                                            <td>
                                                <?php if ($token['status'] != 2) { ?>
                                                    <?php if ($token['status'] == 0 || $token['status'] == 3) { ?>
                                                        <a href="#"
                                                            onclick="update_token_status(<?php echo $token['id']; ?>, 1); return false;"
                                                            class="btn btn-info btn-xs"> <i class="fa fa-bullhorn"></i>
                                                            <?php echo _l('call'); ?></a>
                                                    <?php } ?>

                                                    <?php if ($token['status'] == 1) { ?>
                                                        <!-- If calling, can complete or recall -->
                                                        <a href="#"
                                                            onclick="update_token_status(<?php echo $token['id']; ?>, 1); return false;"
                                                            class="btn btn-default btn-xs" title="<?php echo _l('recall'); ?>"> <i
                                                                class="fa fa-bullhorn"></i></a>
                                                        <a href="#"
                                                            onclick="update_token_status(<?php echo $token['id']; ?>, 2); return false;"
                                                            class="btn btn-success btn-xs"> <i class="fa fa-check"></i>
                                                            <?php echo _l('complete'); ?></a>
                                                        <a href="#"
                                                            onclick="update_token_status(<?php echo $token['id']; ?>, 3); return false;"
                                                            class="btn btn-warning btn-xs"><?php echo _l('skip'); ?></a>
                                                    <?php } ?>

                                                <?php } ?>
                                                <a href="<?php echo admin_url('token_system/delete/' . $token['id']); ?>"
                                                    class="btn btn-danger btn-xs _delete"><i class="fa fa-remove"></i></a>
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
</div>
<?php init_tail(); ?>
<script>
    function update_token_status(id, status) {
        $.post(admin_url + 'token_system/update_status/' + id + '/' + status, function (response) {
            response = JSON.parse(response);
            if (response.success) {
                // simple reload for now, can be optimized with DOM update
                window.location.reload();
            } else {
                alert_float('warning', '<?php echo _l('failed_to_update_status'); ?>');
            }
        });
    }

    $(function () {
        // Init ajax search for patients if element exists
        init_ajax_search('customer', '#patient_id', { type: 'customer' });

        $('#patient_id').on('change', function () {
            var name = $(this).find('option:selected').text();
            $('#patient_name_hidden').val(name);
        });
    });
</script>
</body>

</html>