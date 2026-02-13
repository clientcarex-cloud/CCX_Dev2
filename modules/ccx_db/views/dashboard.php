<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />

                        <div class="clearfix"></div>

                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#overview" aria-controls="overview"
                                    role="tab" data-toggle="tab">Overview</a></li>
                            <li role="presentation"><a href="#compare" aria-controls="compare" role="tab"
                                    data-toggle="tab">Compare Database</a></li>
                            <li role="presentation"><a href="#copy" aria-controls="copy" role="tab"
                                    data-toggle="tab">Copy Database</a></li>
                            <li role="presentation"><a href="#backup" aria-controls="backup" role="tab"
                                    data-toggle="tab">Backup / Restore</a></li>
                            <li role="presentation"><a href="#modules" aria-controls="modules" role="tab"
                                    data-toggle="tab">Manage Modules</a></li>
                            <li role="presentation"><a href="#module_data" aria-controls="module_data" role="tab"
                                    data-toggle="tab">Module Data</a></li>
                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="overview">
                                <div class="table-responsive">
                                    <table class="table dt-table" data-order-col="0" data-order-type="asc">
                                        <thead>
                                            <tr>
                                                <th>Database Name / Tenant</th>
                                                <th>Type</th>
                                                <th>Size (MB)</th>
                                                <th>Table Count</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Master DB -->
                                            <tr>
                                                <td><span class="label label-info">MASTER</span>
                                                    <?php echo $master_db['name']; ?></td>
                                                <td>Master</td>
                                                <td><?php echo $master_db['stats']['size_mb']; ?> MB</td>
                                                <td><?php echo $master_db['stats']['table_count']; ?></td>
                                                <td>-</td>
                                            </tr>

                                            <!-- Tenants -->
                                            <?php foreach ($tenants as $tenant): ?>
                                                <tr>
                                                    <td><?php echo $tenant['name']; ?></td>
                                                    <td>Tenant</td>
                                                    <td>
                                                        <?php
                                                        if (isset($tenant['stats']['error'])) {
                                                            echo '<span class="text-danger" title="' . $tenant['stats']['error'] . '">Error</span>';
                                                        } else {
                                                            echo $tenant['stats']['size_mb'] . ' MB';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (isset($tenant['stats']['error'])) {
                                                            echo '-';
                                                        } else {
                                                            echo $tenant['stats']['table_count'];
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-default btn-icon"><i
                                                                class="fa fa-eye"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="compare">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="source_slug">Source Database</label>
                                            <select name="source_slug" id="source_slug"
                                                class="form-control selectpicker" data-live-search="true">
                                                <option value="master">Master DB</option>
                                                <?php foreach ($tenants as $tenant): ?>
                                                    <option value="<?php echo $tenant['slug']; ?>">
                                                        <?php echo $tenant['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center" style="padding-top: 25px;">
                                        <i class="fa fa-exchange fa-2x"></i>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="dest_slug">Destination Database</label>
                                            <select name="dest_slug" id="dest_slug" class="form-control selectpicker"
                                                data-live-search="true">
                                                <!-- Destination shouldn't optionally be master, but for comparison it's fine -->
                                                <option value="master">Master DB</option>
                                                <?php foreach ($tenants as $tenant): ?>
                                                    <option value="<?php echo $tenant['slug']; ?>">
                                                        <?php echo $tenant['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <button class="btn btn-primary" id="btn_compare">Compare</button>
                                </div>
                                <hr />
                                <div id="compare_results"></div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="copy">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="panel_s">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">Copy Full Database</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="alert alert-danger">
                                                    <strong>WARNING:</strong> This will overwrite the destination
                                                    database! All data in the destination database will be lost.
                                                </div>
                                                <div class="form-group">
                                                    <label for="copy_source_slug">Source Database</label>
                                                    <select name="copy_source_slug" id="copy_source_slug"
                                                        class="form-control selectpicker" data-live-search="true">
                                                        <option value="master">Master DB</option>
                                                        <?php foreach ($tenants as $tenant): ?>
                                                            <option value="<?php echo $tenant['slug']; ?>">
                                                                <?php echo $tenant['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="copy_dest_slug">Destination Database</label>
                                                    <select name="copy_dest_slug" id="copy_dest_slug"
                                                        class="form-control selectpicker" data-live-search="true">
                                                        <!-- Master cannot be destination for safety reasons usually, but requested -->
                                                        <option value="" selected disabled>Select Destination</option>
                                                        <?php foreach ($tenants as $tenant): ?>
                                                            <option value="<?php echo $tenant['slug']; ?>">
                                                                <?php echo $tenant['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <button class="btn btn-danger" id="btn_copy_db">Copy Database</button>
                                                <div id="copy_db_result" class="mtop10"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="panel_s">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">Copy Specific Table</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="alert alert-warning">
                                                    <strong>NOTE:</strong> This will drop the table in the destination
                                                    database if it exists and replace it.
                                                </div>
                                                <div class="form-group">
                                                    <label for="copy_table_source_slug">Source Database</label>
                                                    <select name="copy_table_source_slug" id="copy_table_source_slug"
                                                        class="form-control selectpicker" data-live-search="true">
                                                        <option value="" selected disabled>Select Source</option>
                                                        <option value="master">Master DB</option>
                                                        <?php foreach ($tenants as $tenant): ?>
                                                            <option value="<?php echo $tenant['slug']; ?>">
                                                                <?php echo $tenant['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="copy_table_name">Table Name</label>
                                                    <select name="copy_table_name" id="copy_table_name"
                                                        class="form-control selectpicker" data-live-search="true"
                                                        disabled>
                                                        <option value="">Select Source First</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="copy_table_dest_slug">Destination Database</label>
                                                    <select name="copy_table_dest_slug" id="copy_table_dest_slug"
                                                        class="form-control selectpicker" data-live-search="true">
                                                        <option value="" selected disabled>Select Destination</option>
                                                        <?php foreach ($tenants as $tenant): ?>
                                                            <option value="<?php echo $tenant['slug']; ?>">
                                                                <?php echo $tenant['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <button class="btn btn-danger" id="btn_copy_table">Copy Table</button>
                                                <div id="copy_table_result" class="mtop10"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div role="tabpanel" class="tab-pane" id="backup">
                                <div class="row">
                                    <!-- Backup Section -->
                                    <div class="col-md-6">
                                        <div class="panel_s">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">Download Backup</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="alert alert-info">
                                                    Download a SQL dump of the selected database. This is compatible
                                                    with
                                                    standard MySQL tools.
                                                </div>
                                                <form action="<?php echo admin_url('ccx_db/backup_redirect'); ?>"
                                                    method="post" target="_blank" id="form_backup">
                                                    <!-- We use JS to redirect purely due to complexity of dynamic URL building in form action if we used slug directly in URL -->
                                                    <!-- Actually easier to just use JS to window.open or set location -->
                                                    <div class="form-group">
                                                        <label for="backup_slug">Select Database</label>
                                                        <select name="backup_slug" id="backup_slug"
                                                            class="form-control selectpicker" data-live-search="true">
                                                            <option value="master">Master DB</option>
                                                            <?php foreach ($tenants as $tenant): ?>
                                                                <option value="<?php echo $tenant['slug']; ?>">
                                                                    <?php echo $tenant['name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <button type="button" class="btn btn-info"
                                                        id="btn_backup_download">Download
                                                        Backup</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Restore Section -->
                                    <div class="col-md-6">
                                        <div class="panel_s">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">Restore Database</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="alert alert-danger">
                                                    <strong>CRITICAL:</strong> This will overwrite the selected database
                                                    with
                                                    the uploaded file! Existing data will be lost.
                                                </div>
                                                <form id="form_restore" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label for="restore_dest_slug">Target Database</label>
                                                        <select name="dest_slug" id="restore_dest_slug"
                                                            class="form-control selectpicker" data-live-search="true">
                                                            <option value="" selected disabled>Select Target</option>
                                                            <option value="master">Master DB</option>
                                                            <?php foreach ($tenants as $tenant): ?>
                                                                <option value="<?php echo $tenant['slug']; ?>">
                                                                    <?php echo $tenant['name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="backup_file">Backup File (.sql)</label>
                                                        <input type="file" name="backup_file" id="backup_file"
                                                            class="form-control" accept=".sql" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-danger"
                                                        id="btn_restore">Restore
                                                        Database</button>
                                                </form>
                                                <div id="restore_result" class="mtop10"></div>
                                                <!-- Progress Bar (Simple) -->
                                                <div id="restore_progress" style="display:none;" class="mtop10">
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-striped active"
                                                            role="progressbar" style="width: 100%">
                                                            Restoring...
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="modules">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label for="module_tenant_slug">Select Tenant</label>
                                                    <select name="module_tenant_slug" id="module_tenant_slug"
                                                        class="form-control selectpicker" data-live-search="true">
                                                        <option value="" selected disabled>Select Tenant</option>
                                                        <?php foreach ($tenants as $tenant): ?>
                                                            <option value="<?php echo $tenant['slug']; ?>">
                                                                <?php echo $tenant['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <hr />
                                                <div id="modules_list_container">
                                                    <div class="alert alert-info">Please select a tenant to view
                                                        modules.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="module_data">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="module_data_tenant_slug">Select Tenant</label>
                                                        <select name="module_data_tenant_slug"
                                                            id="module_data_tenant_slug"
                                                            class="form-control selectpicker" data-live-search="true">
                                                            <option value="" selected disabled>Select Tenant</option>
                                                            <option value="master">Master DB (master)</option>
                                                            <?php foreach ($tenants as $tenant): ?>
                                                                <option value="<?php echo $tenant['slug']; ?>">
                                                                    <?php echo $tenant['name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="module_data_module_name">Select Module</label>
                                                        <select name="module_data_module_name"
                                                            id="module_data_module_name"
                                                            class="form-control selectpicker" data-live-search="true">
                                                            <option value="" selected disabled>Select Module</option>
                                                            <?php foreach ($system_modules as $module): ?>
                                                                <option value="<?php echo $module['system_name']; ?>">
                                                                    <?php echo $module['headers']['module_name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="clearfix"></div>
                                                <hr />

                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-info"
                                                        id="btn_export_module_data">
                                                        <i class="fa fa-download"></i> Export Module Data
                                                    </button>

                                                    <button type="button" class="btn btn-warning"
                                                        id="btn_import_module_data_trigger">
                                                        <i class="fa fa-upload"></i> Import Module Data
                                                    </button>

                                                    <!-- Hidden Form for Export -->
                                                    <?php echo form_open(admin_url('ccx_db/export_module_action'), ['id' => 'form_export_module_data', 'target' => '_blank']); ?>
                                                    <input type="hidden" name="slug" id="export_slug">
                                                    <input type="hidden" name="module_name" id="export_module_name">
                                                    <?php echo form_close(); ?>

                                                    <!-- Hidden Upload Input -->
                                                    <input type="file" id="module_data_import_file"
                                                        style="display:none;" accept=".sql">
                                                </div>

                                                <div class="clearfix"></div>
                                                <div id="module_data_result" class="mtop15"></div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script src="<?php echo module_dir_url('ccx_db', 'assets/js/ccx_db.js'); ?>?v=<?php echo time(); ?>"></script>