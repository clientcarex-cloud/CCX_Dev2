<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">Patients Review Settings</h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('patients_review/save_settings'), ['autocomplete' => 'off']); ?>

                        <!-- 1. Patients Records -->
                        <?php
                        $patients_records_options = [
                            ['key' => 'none', 'value' => 'None'],
                            ['key' => 'only_archive', 'value' => 'Only Archive'],
                            ['key' => 'only_delete', 'value' => 'Only Delete'],
                            ['key' => 'archive_and_delete', 'value' => 'Archive & Delete'],
                        ];
                        $selected_patients_records = get_option('patients_records');
                        if (empty($selected_patients_records)) {
                            $selected_patients_records = 'none';
                        }
                        echo render_select('patients_records', $patients_records_options, ['key', 'value'], 'Patients Records', $selected_patients_records);

                        // Show Archived Tab
                        echo render_yes_no_option('show_archived_tab', 'Show Archive Tab In Review Page');
                        ?>
                        <?php
                        // Item Groups (Multi-select)
                        $selected_groups = get_option('patients_review_item_groups');
                        if ($selected_groups) {
                            $selected_groups = explode(',', $selected_groups);
                        }
                        echo render_select('patients_review_item_groups[]', $item_groups, ['id', 'name'], 'Select Item groups', $selected_groups, ['multiple' => true]);

                        // Authorized User (Perfex Staff)
                        $selected_users = get_option('patients_review_authorized_user');
                        if ($selected_users) {
                            $selected_users = explode(',', $selected_users);
                        }
                        echo render_select('patients_review_authorized_user[]', $staff, ['staffid', ['firstname', 'lastname']], 'Authorized User for Actions', $selected_users, ['multiple' => true]);
                        ?>
                        <hr />

                        <!-- Delete Wrapper -->
                        <div id="delete_settings_wrapper" class="hide">
                            <h4 class="bold">Delete Settings</h4>
                            <!-- 2. Delete System (Multi Select) -->
                            <?php
                            $system_options = [
                                ['key' => 'paid_receipts', 'value' => 'Paid Receipts'],
                                ['key' => 'whole_invoices', 'value' => 'Whole invoices'],
                                ['key' => 'refund_receipts', 'value' => 'Refund Receipts'],
                            ];
                            $selected_delete_system = get_option('delete_system');
                            if ($selected_delete_system) {
                                $selected_delete_system = explode(',', $selected_delete_system);
                            }
                            echo render_select('delete_system[]', $system_options, ['key', 'value'], 'Delete System', $selected_delete_system, ['multiple' => true]);
                            ?>

                            <!-- 5. Delete Password -->
                            <?php echo render_yes_no_option('enable_delete_password', 'Delete Password'); ?>
                            <div id="delete_password_wrapper" class="hide">
                                <div class="form-group">
                                    <label for="delete_password" class="control-label">Set Password</label>
                                    <div class="input-group">
                                        <input type="password" id="delete_password" name="delete_password"
                                            class="form-control" value="" autocomplete="new-password">
                                        <span class="input-group-addon pointer toggle-password"
                                            onclick="togglePasswordVisibility('delete_password')"><i
                                                class="fa fa-eye"></i></span>
                                    </div>
                                    <?php
                                    $last_changed = get_option('delete_password_last_changed');
                                    if ($last_changed) {
                                        echo '<p class="text-muted help-block">Last time changed: ' . _dt($last_changed) . '</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <hr />
                        </div>

                        <!-- Archive Wrapper -->
                        <div id="archive_settings_wrapper" class="hide">
                            <h4 class="bold">Archive Settings</h4>
                            <!-- 3. Archive System (Multi Select) -->
                            <?php
                            $selected_archive_system = get_option('archive_system');
                            if ($selected_archive_system) {
                                $selected_archive_system = explode(',', $selected_archive_system);
                            }
                            echo render_select('archive_system[]', $system_options, ['key', 'value'], 'Archive System', $selected_archive_system, ['multiple' => true]);
                            ?>

                            <!-- New Archive Password -->
                            <?php echo render_yes_no_option('enable_archive_password', 'Archive Password'); ?>
                            <div id="archive_password_wrapper" class="hide">
                                <div class="form-group">
                                    <label for="archive_password" class="control-label">Set Password</label>
                                    <div class="input-group">
                                        <input type="password" id="archive_password" name="archive_password"
                                            class="form-control" value="" autocomplete="new-password">
                                        <span class="input-group-addon pointer toggle-password"
                                            onclick="togglePasswordVisibility('archive_password')"><i
                                                class="fa fa-eye"></i></span>
                                    </div>
                                    <?php
                                    $last_changed = get_option('archive_password_last_changed');
                                    if ($last_changed) {
                                        echo '<p class="text-muted help-block">Last time changed: ' . _dt($last_changed) . '</p>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- 4. Unarchive Password -->
                            <?php echo render_yes_no_option('enable_unarchive_password', 'Unarchive Password'); ?>
                            <div id="unarchive_password_wrapper" class="hide">
                                <div class="form-group">
                                    <label for="unarchive_password" class="control-label">Set Password</label>
                                    <div class="input-group">
                                        <input type="password" id="unarchive_password" name="unarchive_password"
                                            class="form-control" value="" autocomplete="new-password">
                                        <span class="input-group-addon pointer toggle-password"
                                            onclick="togglePasswordVisibility('unarchive_password')"><i
                                                class="fa fa-eye"></i></span>
                                    </div>
                                    <?php
                                    $last_changed = get_option('unarchive_password_last_changed');
                                    if ($last_changed) {
                                        echo '<p class="text-muted help-block">Last time changed: ' . _dt($last_changed) . '</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <hr />
                        </div>

                        <button type="submit" class="btn btn-info pull-right">Save Settings</button>
                        <a href="<?php echo admin_url('patients_review'); ?>" class="btn btn-default">Back to Main
                            Page</a>

                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        // Main Patients Records Logic
        function togglePatientsRecords() {
            var val = $('select[name="patients_records"]').val();

            // Reset
            $('#delete_settings_wrapper').addClass('hide');
            $('#archive_settings_wrapper').addClass('hide');

            if (val == 'only_archive') {
                $('#archive_settings_wrapper').removeClass('hide');
            } else if (val == 'only_delete') {
                $('#delete_settings_wrapper').removeClass('hide');
            } else if (val == 'archive_and_delete') {
                $('#archive_settings_wrapper').removeClass('hide');
                $('#delete_settings_wrapper').removeClass('hide');
            }
        }
        $('select[name="patients_records"]').on('change', togglePatientsRecords);
        togglePatientsRecords();

        // Unarchive Password Toggle
        function toggleUnarchivePassword() {
            var val = $('input[name="settings[enable_unarchive_password]"]:checked').val();
            // Fallback
            if (val === undefined) {
                val = $('input[name="enable_unarchive_password"]:checked').val();
            }

            if (val == 1) {
                $('#unarchive_password_wrapper').removeClass('hide');
            } else {
                $('#unarchive_password_wrapper').addClass('hide');
            }
        }
        $('input[name="enable_unarchive_password"]').on('change', toggleUnarchivePassword);
        $('input[name="settings[enable_unarchive_password]"]').on('change', toggleUnarchivePassword);
        toggleUnarchivePassword();

        // Archive Password Toggle
        function toggleArchivePassword() {
            var val = $('input[name="settings[enable_archive_password]"]:checked').val();
            // Fallback
            if (val === undefined) {
                val = $('input[name="enable_archive_password"]:checked').val();
            }

            if (val == 1) {
                $('#archive_password_wrapper').removeClass('hide');
            } else {
                $('#archive_password_wrapper').addClass('hide');
            }
        }
        $('input[name="enable_archive_password"]').on('change', toggleArchivePassword);
        $('input[name="settings[enable_archive_password]"]').on('change', toggleArchivePassword);
        toggleArchivePassword();

        // Delete Password Toggle
        function toggleDeletePassword() {
            var val = $('input[name="settings[enable_delete_password]"]:checked').val();
            if (val === undefined) {
                val = $('input[name="enable_delete_password"]:checked').val();
            }

            if (val == 1) {
                $('#delete_password_wrapper').removeClass('hide');
            } else {
                $('#delete_password_wrapper').addClass('hide');
            }
        }
        $('input[name="enable_delete_password"]').on('change', toggleDeletePassword);
        $('input[name="settings[enable_delete_password]"]').on('change', toggleDeletePassword);
        toggleDeletePassword();
        toggleDeletePassword();
    });

    function togglePasswordVisibility(id) {
        var input = $('#' + id);
        var icon = input.next('.input-group-addon').find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }
</script>