$(function () {
    $('#btn_compare').on('click', function () {
        var source_slug = $('#source_slug').val();
        var dest_slug = $('#dest_slug').val();

        if (source_slug === dest_slug) {
            alert('Source and Destination cannot be the same!');
            return;
        }

        $('#compare_results').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br>Comparing... This may take a while.</div>');

        $.post(admin_url + 'ccx_db/compare_db', {
            source_slug: source_slug,
            dest_slug: dest_slug
        }).done(function (response) {
            $('#compare_results').html(response);
        }).fail(function (data) {
            var error = "Unknown Error";
            if (data.responseText) error = data.responseText;
            $('#compare_results').html('<div class="alert alert-danger">Error: ' + error + '</div>');
        });
    });

    // Copy DB Logic
    $('#btn_copy_db').on('click', function () {
        var source_slug = $('#copy_source_slug').val();
        var dest_slug = $('#copy_dest_slug').val();

        if (!dest_slug) {
            alert('Please select a destination database.');
            return;
        }

        if (source_slug === dest_slug) {
            alert('Source and Destination cannot be the same!');
            return;
        }

        if (!confirm('DANGER: Are you sure you want to overwrite the destination database? This cannot be undone!')) {
            return;
        }

        $('#copy_db_result').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Copying...</div>');
        $('#btn_copy_db').prop('disabled', true);

        $.post(admin_url + 'ccx_db/copy_db_action', {
            source_slug: source_slug,
            dest_slug: dest_slug
        }).done(function (response) {
            var res = JSON.parse(response);
            if (res.success) {
                $('#copy_db_result').html('<div class="alert alert-success">' + res.message + '</div>');
            } else {
                $('#copy_db_result').html('<div class="alert alert-danger">' + res.message + '</div>');
            }
        }).fail(function (data) {
            $('#copy_db_result').html('<div class="alert alert-danger">Error copying database.</div>');
        }).always(function () {
            $('#btn_copy_db').prop('disabled', false);
        });
    });


    // Copy Table Logic - Fetch Tables
    $('#copy_table_source_slug').on('change', function () {
        var source_slug = $(this).val();
        var tableSelect = $('#copy_table_name');

        tableSelect.prop('disabled', true).html('<option>Loading...</option>').selectpicker('refresh');

        $.get(admin_url + 'ccx_db/get_tables_json/' + source_slug, function (response) {
            var tables = JSON.parse(response);
            var options = '';
            if (tables.error) {
                options = '<option>Error loading tables</option>';
            } else {
                $.each(tables, function (i, table) {
                    options += '<option value="' + table + '">' + table + '</option>';
                });
            }
            tableSelect.html(options).prop('disabled', false).selectpicker('refresh');
        });
    });

    // Copy Table Action
    $('#btn_copy_table').on('click', function () {
        var source_slug = $('#copy_table_source_slug').val();
        var dest_slug = $('#copy_table_dest_slug').val();
        var table_name = $('#copy_table_name').val();

        if (!source_slug || !dest_slug || !table_name) {
            alert('Please select all fields.');
            return;
        }

        if (source_slug === dest_slug) {
            alert('Source and Destination database cannot be the same!');
            return;
        }

        if (!confirm('Are you sure you want to copy table ' + table_name + ' to destination? If it exists, it will be overwritten.')) {
            return;
        }

        $('#copy_table_result').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Copying...</div>');
        $('#btn_copy_table').prop('disabled', true);

        $.post(admin_url + 'ccx_db/copy_table_action', {
            source_slug: source_slug,
            dest_slug: dest_slug,
            table_name: table_name
        }).done(function (response) {
            var res = JSON.parse(response);
            if (res.success) {
                $('#copy_table_result').html('<div class="alert alert-success">' + res.message + '</div>');
            } else {
                $('#copy_table_result').html('<div class="alert alert-danger">' + res.message + '</div>');
            }
        }).fail(function (data) {
            $('#copy_table_result').html('<div class="alert alert-danger">Error copying table.</div>');
        }).always(function () {
            $('#btn_copy_table').prop('disabled', false);
        });
    });

    // Backup Download
    $('#btn_backup_download').on('click', function () {
        var slug = $('#backup_slug').val();
        if (!slug) return;

        window.location.href = admin_url + 'ccx_db/backup/' + slug;
    });

    // Restore Action
    $('#form_restore').on('submit', function (e) {
        e.preventDefault();
        var dest_slug = $('#restore_dest_slug').val();
        if (!dest_slug) {
            alert('Please select a target database');
            return;
        }

        if (!confirm('CRITICAL WARNING: This will OVERWRITE the target database (' + dest_slug + '). Are you absolutely sure?')) {
            return;
        }

        var formData = new FormData(this);

        // Add CSRF token
        if (typeof (csrfData) !== 'undefined') {
            formData.append(csrfData.token_name, csrfData.hash);
        }

        $('#btn_restore').prop('disabled', true);
        $('#restore_progress').show();
        $('#restore_result').html('');

        $.ajax({
            url: admin_url + 'ccx_db/restore_action',
            type: 'POST',
            data: formData,
            success: function (response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#restore_result').html('<div class="alert alert-success">' + res.message + '</div>');
                    $('#form_restore')[0].reset();
                    $('#restore_dest_slug').selectpicker('refresh');
                } else {
                    $('#restore_result').html('<div class="alert alert-danger">' + res.message + '</div>');
                }
            },
            error: function (data) {
                $('#restore_result').html('<div class="alert alert-danger">Restore failed. Server error.</div>');
            },
            cache: false,
            contentType: false,
            processData: false,
            complete: function () {
                $('#btn_restore').prop('disabled', false);
                $('#restore_progress').hide();
            }
        });
    });


    // Modules Tab Logic
    $('#module_tenant_slug').on('change', function () {
        var slug = $(this).val();
        var container = $('#modules_list_container');

        if (!slug) return;

        container.html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');

        $.get(admin_url + 'ccx_db/get_tenant_modules_json/' + slug, function (response) {
            console.log('Server response:', response); // Debugging
            try {
                // Check if response is already an object
                var modules = (typeof response === 'object') ? response : JSON.parse(response);
            } catch (e) {
                console.error('JSON Parse Error:', e, response);
                container.html('<div class="alert alert-danger">Invalid server response</div>');
                return;
            }

            if (modules.error) {
                container.html('<div class="alert alert-danger">' + modules.error + '</div>');
                return;
            }

            if (modules.length === 0) {
                container.html('<div class="alert alert-warning">No modules found in this tenant database.</div>');
                return;
            }

            var html = '<div class="table-responsive"><table class="table table-striped dt-table">';
            html += '<thead><tr><th>Module Name</th><th>Version</th><th>Status</th><th>Action</th></tr></thead>';
            html += '<tbody>';

            $.each(modules, function (i, module) {
                var is_active = module.active == 1;
                var status_label = is_active ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
                var btn_class = is_active ? 'btn-default' : 'btn-success';
                var btn_text = is_active ? 'Deactivate' : 'Activate';
                var action_val = is_active ? 0 : 1;

                html += '<tr>';
                html += '<td>' + module.module_name + '</td>';
                html += '<td>' + module.installed_version + '</td>';
                html += '<td>' + status_label + '</td>';
                html += '<td>';
                html += '<button class="btn ' + btn_class + ' btn-xs btn-toggle-module" data-id="' + module.id + '" data-active="' + action_val + '">' + btn_text + '</button>';
                html += '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';

            container.html(html);

            // Re-init DataTable if needed
            if ($.fn.DataTable.isDataTable('#modules_list_container table')) {
                $('#modules_list_container table').DataTable().destroy();
            }
            // Initialize DataTable if function exists, otherwise ignore
            if (typeof initDataTableOffline === 'function') {
                initDataTableOffline('#modules_list_container table');
            }

        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
            container.html('<div class="alert alert-danger">Failed to fetch modules. Error: ' + textStatus + '</div>');
        });
    });

    $(document).on('click', '.btn-toggle-module', function () {
        var btn = $(this);
        var id = btn.data('id');
        var active = btn.data('active');
        var slug = $('#module_tenant_slug').val();

        btn.prop('disabled', true).text('Processing...');

        $.post(admin_url + 'ccx_db/update_tenant_module_action', {
            slug: slug,
            id: id,
            active: active
        }, function (response) {
            btn.prop('disabled', false);
            try {
                var res = (typeof response === 'object') ? response : JSON.parse(response);
                if (res.success) {
                    alert_float('success', res.message);
                    // Refresh list
                    $('#module_tenant_slug').trigger('change');
                } else {
                    alert_float('danger', res.message);
                    // Reset button text
                    if (active == 1) btn.text('Activate'); else btn.text('Deactivate');
                }
            } catch (e) {
                console.error('JSON Parse Error:', e, response);
                alert_float('danger', 'Invalid server response');
            }
        }).fail(function () {
            btn.prop('disabled', false);
            alert_float('danger', 'Request failed');
        });
    });

    // Module Data Export/Import Logic
    $('#btn_export_module_data').on('click', function () {
        var slug = $('#module_data_tenant_slug').val();
        var module_name = $('#module_data_module_name').val();

        if (!slug || !module_name) {
            alert_float('warning', 'Please select both a Tenant and a Module.');
            return;
        }

        $('#export_slug').val(slug);
        $('#export_module_name').val(module_name);
        $('#form_export_module_data').submit();
    });

    $('#btn_import_module_data_trigger').on('click', function () {
        var slug = $('#module_data_tenant_slug').val();
        if (!slug) {
            alert_float('warning', 'Please select a Tenant to import data into.');
            return;
        }
        $('#module_data_import_file').click();
    });

    $('#module_data_import_file').on('change', function () {
        var file = this.files[0];
        if (!file) return;

        var slug = $('#module_data_tenant_slug').val();
        var module_name = $('#module_data_module_name').val(); // Get module name
        var fd = new FormData();
        fd.append('backup_file', file);
        fd.append('dest_slug', slug);
        fd.append('module_name', module_name); // Send it

        // Append CSRF Token
        if (typeof (csrfData) !== 'undefined') {
            fd.append(csrfData.token_name, csrfData.hash);
        }

        var btn = $('#btn_import_module_data_trigger');
        var original_text = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importing...');
        $('#module_data_result').html('<div class="alert alert-info">Importing data... please wait.</div>');

        $.ajax({
            url: admin_url + 'ccx_db/import_module_action',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (response) {
                btn.prop('disabled', false).html(original_text);
                try {
                    var res = JSON.parse(response);
                    if (res.success) {
                        alert_float('success', res.message);
                        $('#module_data_result').html('<div class="alert alert-success">' + res.message + '</div>');
                    } else {
                        alert_float('danger', res.message);
                        $('#module_data_result').html('<div class="alert alert-danger">' + res.message + '</div>');
                    }
                } catch (e) {
                    $('#module_data_result').html('<div class="alert alert-danger">Server Error</div>');
                }
                // Reset file input
                $('#module_data_import_file').val('');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                btn.prop('disabled', false).html(original_text);
                var msg = 'Request failed: ' + errorThrown;
                if (jqXHR.responseText) {
                    msg = jqXHR.responseText;
                }
                alert_float('danger', msg);
                $('#module_data_result').html('<div class="alert alert-danger">' + msg + '</div>');
                $('#module_data_import_file').val('');
            }
        });
    });

});
