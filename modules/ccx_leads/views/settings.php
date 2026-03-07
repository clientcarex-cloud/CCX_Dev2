<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                            <a href="<?php echo admin_url('ccx_leads'); ?>" class="btn btn-default pull-right">
                                <?php echo _l('back'); ?>
                            </a>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#fields" aria-controls="fields" role="tab" data-toggle="tab">
                                            <?php echo _l('Fields'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#custom_fields_tab" aria-controls="custom_fields_tab" role="tab" data-toggle="tab">
                                            <?php echo _l('custom_fields'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#ordering" aria-controls="ordering" role="tab" data-toggle="tab">
                                            <?php echo _l('Ordering'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#statuses" aria-controls="statuses" role="tab" data-toggle="tab">
                                            <?php echo _l('Statuses'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#sources" aria-controls="sources" role="tab" data-toggle="tab">
                                            <?php echo _l('Sources'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#roller_coaster" aria-controls="roller_coaster" role="tab"
                                            data-toggle="tab">
                                            <?php echo _l('Roller Coaster'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#wa_web" aria-controls="wa_web" role="tab" data-toggle="tab">
                                            <?php echo _l('WA Web'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#call_mgmt" aria-controls="call_mgmt" role="tab" data-toggle="tab">
                                            <?php echo _l('Call Mgmt'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#reporting" aria-controls="reporting" role="tab" data-toggle="tab">
                                            <?php echo _l('Reporting'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#api_tab" aria-controls="api_tab" role="tab" data-toggle="tab">
                                            <i class="fa-solid fa-plug tw-mr-1"></i> API
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            <!-- ==================== FIELDS TAB ==================== -->
                            <div role="tabpanel" class="tab-pane active" id="fields">
                                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                                    <p class="text-muted tw-mb-0">
                                        <i class="fa-solid fa-circle-info tw-mr-1"></i>
                                        Manage which fields are visible in the lead form, set custom labels, and mark
                                        fields as mandatory.
                                    </p>
                                    <button type="button" class="btn btn-primary" id="ccx-save-field-settings">
                                        <i class="fa-regular fa-floppy-disk tw-mr-1"></i>
                                        Save Changes
                                    </button>
                                </div>

                                <form id="ccx-field-settings-form">
                                    <table class="table table-striped" id="ccx-field-settings-table">
                                        <thead>
                                            <tr>
                                                <th style="width:5%">#</th>
                                                <th style="width:25%">Name (Rename)</th>
                                                <th style="width:20%">Status</th>
                                                <th style="width:25%">Slug</th>
                                                <th style="width:15%">Mandatory</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($field_settings as $index => $field) { ?>
                                                <tr>
                                                    <td class="tw-align-middle">
                                                        <span class="text-muted"><?= $index + 1; ?></span>
                                                        <input type="hidden" name="fields[<?= $index; ?>][id]"
                                                            value="<?= e($field['id']); ?>">
                                                    </td>
                                                    <td class="tw-align-middle">
                                                        <input type="text" class="form-control"
                                                            name="fields[<?= $index; ?>][label]"
                                                            value="<?= e($field['label']); ?>" placeholder="Field label">
                                                    </td>
                                                    <td class="tw-align-middle">
                                                        <div class="onoffswitch">
                                                            <input type="checkbox" class="onoffswitch-checkbox"
                                                                id="field_active_<?= e($field['id']); ?>"
                                                                name="fields[<?= $index; ?>][active]" value="1"
                                                                <?= $field['active'] == 1 ? 'checked' : ''; ?>>
                                                            <label class="onoffswitch-label"
                                                                for="field_active_<?= e($field['id']); ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td class="tw-align-middle">
                                                        <span class="label label-default"
                                                            style="font-size:12px; font-weight:500; letter-spacing:0.5px;">
                                                            <?= e($field['slug']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="tw-align-middle">
                                                        <div class="onoffswitch">
                                                            <input type="checkbox" class="onoffswitch-checkbox"
                                                                id="field_required_<?= e($field['id']); ?>"
                                                                name="fields[<?= $index; ?>][required]" value="1"
                                                                <?= $field['required'] == 1 ? 'checked' : ''; ?>>
                                                            <label class="onoffswitch-label"
                                                                for="field_required_<?= e($field['id']); ?>"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>

                            <!-- ==================== CUSTOM FIELDS TAB ==================== -->
                            <div role="tabpanel" class="tab-pane" id="custom_fields_tab">
                                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                                    <p class="text-muted tw-mb-0">
                                        <i class="fa-solid fa-circle-info tw-mr-1"></i>
                                        Add custom fields to capture additional lead information beyond the standard fields.
                                    </p>
                                    <button type="button" class="btn btn-primary" onclick="ccx_open_cf_modal(); return false;">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        Add Custom Field
                                    </button>
                                </div>

                                <?php if (isset($custom_fields) && count($custom_fields) > 0) { ?>
                                    <table class="table table-striped" id="ccx-custom-fields-table">
                                        <thead>
                                            <tr>
                                                <th style="width:5%">#</th>
                                                <th style="width:25%">Name</th>
                                                <th style="width:15%">Type</th>
                                                <th style="width:10%">Order</th>
                                                <th style="width:15%">Active</th>
                                                <th style="width:10%">Required</th>
                                                <th style="width:20%" class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($custom_fields as $ci => $cf) { ?>
                                                <tr id="cf-row-<?= e($cf['id']); ?>">
                                                    <td class="tw-align-middle">
                                                        <span class="text-muted"><?= $ci + 1; ?></span>
                                                    </td>
                                                    <td class="tw-align-middle">
                                                        <span class="tw-font-medium"><?= e($cf['name']); ?></span>
                                                        <?php if (!empty($cf['slug'])) { ?>
                                                            <br><span class="label label-default" style="font-size:11px;"><?= e($cf['slug']); ?></span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="tw-align-middle">
                                                        <span class="label label-info" style="font-size:12px;"><?= e(ucwords(str_replace('_', ' ', $cf['type']))); ?></span>
                                                    </td>
                                                    <td class="tw-align-middle">
                                                        <?= e($cf['field_order']); ?>
                                                    </td>
                                                    <td class="tw-align-middle">
                                                        <div class="onoffswitch">
                                                            <input type="checkbox" class="onoffswitch-checkbox ccx-cf-active-toggle"
                                                                id="cf_active_<?= e($cf['id']); ?>"
                                                                data-id="<?= e($cf['id']); ?>"
                                                                <?= $cf['active'] == 1 ? 'checked' : ''; ?>>
                                                            <label class="onoffswitch-label"
                                                                for="cf_active_<?= e($cf['id']); ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td class="tw-align-middle">
                                                        <?php if ($cf['required'] == 1) { ?>
                                                            <span class="label label-danger">Yes</span>
                                                        <?php } else { ?>
                                                            <span class="text-muted">No</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="tw-align-middle text-right">
                                                        <div class="tw-flex tw-items-center tw-justify-end tw-space-x-2">
                                                            <a href="#"
                                                                onclick="ccx_edit_cf(<?= e($cf['id']); ?>);return false;"
                                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"
                                                                data-toggle="tooltip" title="Edit">
                                                                <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                            </a>
                                                            <a href="#"
                                                                onclick="ccx_delete_cf(<?= e($cf['id']); ?>);return false;"
                                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete"
                                                                data-toggle="tooltip" title="Delete">
                                                                <i class="fa-regular fa-trash-can fa-lg"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <div class="text-center tw-py-8">
                                        <i class="fa-solid fa-puzzle-piece fa-3x text-muted tw-mb-3" style="display:block;"></i>
                                        <p class="text-muted">No custom fields have been added yet.</p>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="ccx_open_cf_modal(); return false;">
                                            <i class="fa-regular fa-plus tw-mr-1"></i>
                                            Add Your First Custom Field
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- ==================== ORDERING TAB ==================== -->
                            <div role="tabpanel" class="tab-pane" id="ordering">
                                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                                    <p class="text-muted tw-mb-0">
                                        <i class="fa-solid fa-circle-info tw-mr-1"></i>
                                        Drag and drop fields between columns to control form layout. Reorder within each column.
                                    </p>
                                    <button type="button" class="btn btn-primary" id="ccx-save-field-order">
                                        <i class="fa-regular fa-floppy-disk tw-mr-1"></i>
                                        Save Order
                                    </button>
                                </div>

                                <div class="row">
                                    <?php
                                    $col_labels = [
                                        '1' => ['title' => 'Top Row', 'desc' => 'Status, Source, Assigned (col-md-4)', 'icon' => 'fa-table-columns'],
                                        '2' => ['title' => 'Left Column', 'desc' => 'Contact & business fields (col-md-6)', 'icon' => 'fa-arrow-left'],
                                        '3' => ['title' => 'Right Column', 'desc' => 'Address & other fields (col-md-6)', 'icon' => 'fa-arrow-right'],
                                    ];
                                    $layout = isset($field_layout) ? $field_layout : ['1' => [], '2' => [], '3' => []];
                                    foreach (['1', '2', '3'] as $col_num) {
                                        $col_info = $col_labels[$col_num];
                                        $fields_in_col = isset($layout[$col_num]) ? $layout[$col_num] : [];
                                    ?>
                                        <div class="col-md-4">
                                            <div style="background:#f8f9fa; border:1px solid #e5e5e5; border-radius:8px; padding:12px; min-height:300px;">
                                                <div style="margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #e0e0e0;">
                                                    <h5 style="margin:0 0 2px 0; font-weight:600;">
                                                        <i class="fa-solid <?= $col_info['icon']; ?> tw-mr-1 text-muted"></i>
                                                        <?= $col_info['title']; ?>
                                                    </h5>
                                                    <small class="text-muted"><?= $col_info['desc']; ?></small>
                                                </div>
                                                <ul class="ccx-col-sortable list-unstyled" data-column="<?= $col_num; ?>"
                                                    style="min-height:200px; padding:4px 0;">
                                                    <?php foreach ($fields_in_col as $fo) {
                                                        $is_custom = ($fo['type'] === 'custom');
                                                        $badge_class = $is_custom ? 'label-primary' : 'label-default';
                                                        $badge_text = $is_custom ? 'Custom' : 'Standard';
                                                        $inactive = ($fo['active'] == 0) ? ' opacity:0.5;' : '';
                                                    ?>
                                                        <li class="ccx-field-order-item"
                                                            data-type="<?= e($fo['type']); ?>"
                                                            data-id="<?= e($fo['id']); ?>"
                                                            style="padding:8px 10px; margin-bottom:3px; background:#fff; border:1px solid #ddd; border-radius:5px; cursor:grab; display:flex; align-items:center; justify-content:space-between; font-size:13px;<?= $inactive; ?>">
                                                            <div style="display:flex; align-items:center; gap:8px;">
                                                                <i class="fa-solid fa-grip-vertical text-muted" style="font-size:12px;"></i>
                                                                <span class="tw-font-medium"><?= e($fo['label']); ?></span>
                                                            </div>
                                                            <span class="label <?= $badge_class; ?>" style="font-size:10px;"><?= $badge_text; ?></span>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- ==================== STATUSES TAB ==================== -->
                            <div role="tabpanel" class="tab-pane" id="statuses">
                                <div class="tw-mb-2">
                                    <a href="#" onclick="new_status(); return false;" class="btn btn-primary">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        <?= _l('lead_new_status'); ?>
                                    </a>
                                </div>

                                <?php if (isset($statuses) && count($statuses) > 0) { ?>
                                    <table class="table dt-table" data-order-col="1" data-order-type="asc">
                                        <thead>
                                            <tr>
                                                <th><?= _l('id'); ?></th>
                                                <th><?= _l('leads_status_table_name'); ?></th>
                                                <th class="options"><?= _l('options'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($statuses as $status) { ?>
                                                <tr>
                                                    <td><?= e($status['id']); ?></td>
                                                    <td>
                                                        <?php $color = ($status['color'] ? $status['color'] : '#757575'); ?>
                                                        <a href="#"
                                                            onclick="edit_status(this,<?= e($status['id']); ?>);return false;"
                                                            data-color="<?= e($status['color']); ?>"
                                                            data-name="<?= e($status['name']); ?>"
                                                            data-order="<?= e($status['statusorder']); ?>">
                                                            <span
                                                                style="display:inline-block;width:14px;height:14px;border-radius:50%;margin-right:8px;background:<?= e($color); ?>;vertical-align:middle;border:1px solid rgba(0,0,0,0.06);"></span>
                                                            <?= e($status['name']); ?></a>
                                                        <br /><span
                                                            class="text-muted"><?= _l('leads_table_total', total_rows(db_prefix() . 'leads', ['status' => $status['id']])); ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="tw-flex tw-items-center tw-space-x-2">
                                                            <a href="#"
                                                                onclick="edit_status(this,<?= e($status['id']); ?>);return false;"
                                                                data-color="<?= e($status['color']); ?>"
                                                                data-name="<?= e($status['name']); ?>"
                                                                data-order="<?= e($status['statusorder']); ?>"
                                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                                <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                            </a>
                                                            <?php if ($status['isdefault'] == 0) { ?>
                                                                <a href="#"
                                                                    onclick="ccx_delete_status(<?= e($status['id']); ?>);return false;"
                                                                    class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                                                    <i class="fa-regular fa-trash-can fa-lg"></i>
                                                                </a>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <p class="no-margin"><?= _l('lead_statuses_not_found'); ?></p>
                                <?php } ?>
                            </div>

                            <!-- ==================== SOURCES TAB ==================== -->
                            <div role="tabpanel" class="tab-pane" id="sources">
                                <div class="tw-mb-2">
                                    <a href="#" onclick="new_source(); return false;" class="btn btn-primary">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        <?= _l('lead_new_source'); ?>
                                    </a>
                                </div>

                                <?php if (isset($sources) && count($sources) > 0) { ?>
                                    <table class="table dt-table" data-order-col="1" data-order-type="asc">
                                        <thead>
                                            <tr>
                                                <th><?= _l('id'); ?></th>
                                                <th><?= _l('leads_sources_table_name'); ?></th>
                                                <th class="options"><?= _l('options'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sources as $source) { ?>
                                                <tr>
                                                    <td><?= e($source['id']); ?></td>
                                                    <td>
                                                        <a href="#" class="tw-font-medium"
                                                            onclick="edit_source(this,<?= e($source['id']); ?>); return false"
                                                            data-name="<?= e($source['name']); ?>"><?= e($source['name']); ?></a>
                                                        <br />
                                                        <span class="text-muted">
                                                            <?= _l('leads_table_total', total_rows(db_prefix() . 'leads', ['source' => $source['id']])); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="tw-flex tw-items-center tw-space-x-2">
                                                            <a href="#"
                                                                onclick="edit_source(this,<?= e($source['id']); ?>); return false"
                                                                data-name="<?= e($source['name']); ?>"
                                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                                <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                            </a>
                                                            <a href="#"
                                                                onclick="ccx_delete_source(<?= e($source['id']); ?>);return false;"
                                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                                                <i class="fa-regular fa-trash-can fa-lg"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <p class="no-margin"><?= _l('leads_sources_not_found'); ?></p>
                                <?php } ?>
                            </div>

                            <!-- ==================== PLACEHOLDER TABS ==================== -->
                            <div role="tabpanel" class="tab-pane" id="roller_coaster">
                                <p class="text-muted">Roller Coaster settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="wa_web">
                                <p class="text-muted">WA Web settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="call_mgmt">
                                <p class="text-muted">Call Management settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="reporting">
                                <p class="text-muted">Reporting settings coming soon...</p>
                            </div>

                            <!-- ==================== API TAB ==================== -->
                            <div role="tabpanel" class="tab-pane" id="api_tab">

                                <!-- API Key Section -->
                                <div style="border:1px solid #e5e5e5; border-radius:8px; padding:20px; margin-bottom:20px; background:#fff;">
                                    <h4 style="margin:0 0 12px 0; font-weight:600;">
                                        <i class="fa-solid fa-key tw-mr-1 text-warning"></i> API Key
                                    </h4>
                                    <p class="text-muted" style="margin-bottom:12px; font-size:13px;">
                                        Use this key to authenticate API requests. Include it as <code>X-Api-Key</code> header or <code>api_key</code> parameter.
                                    </p>
                                    <div class="form-group" style="margin-bottom:10px;">
                                        <div class="input-group">
                                            <input type="text" id="ccx-api-key-display" class="form-control" readonly
                                                value="<?= e(isset($api_key) && $api_key ? $api_key : ''); ?>"
                                                placeholder="No API key generated yet — click Generate"
                                                style="font-family:monospace; letter-spacing:0.5px; font-size:13px;">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" id="ccx-copy-api-key"
                                                    data-toggle="tooltip" title="Copy to clipboard">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                                <button type="button" class="btn btn-primary" id="ccx-generate-api-key">
                                                    <i class="fa-solid fa-rotate tw-mr-1"></i>
                                                    <?= (isset($api_key) && $api_key) ? 'Regenerate' : 'Generate'; ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (isset($api_key) && $api_key) { ?>
                                        <div class="alert alert-warning" style="font-size:12px; margin-bottom:0; padding:8px 12px;">
                                            <i class="fa-solid fa-triangle-exclamation tw-mr-1"></i>
                                            <strong>Keep this key secret.</strong> Anyone with this key can create leads. Regenerating invalidates the old key.
                                        </div>
                                    <?php } ?>
                                </div>

                                <!-- Base URL -->
                                <div style="border:1px solid #e5e5e5; border-radius:8px; padding:20px; margin-bottom:20px; background:#fff;">
                                    <h4 style="margin:0 0 12px 0; font-weight:600;">
                                        <i class="fa-solid fa-link tw-mr-1 text-primary"></i> Base URL
                                    </h4>
                                    <div class="input-group">
                                        <input type="text" class="form-control" readonly id="ccx-api-base-url"
                                            value="<?= e(isset($api_base_url) ? $api_base_url : site_url('ccx_leads_api/')); ?>"
                                            style="font-family:monospace; font-size:13px;">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default ccx-copy-btn" data-target="#ccx-api-base-url">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Endpoints Reference -->
                                <div style="border:1px solid #e5e5e5; border-radius:8px; padding:20px; margin-bottom:20px; background:#fff;">
                                    <h4 style="margin:0 0 12px 0; font-weight:600;">
                                        <i class="fa-solid fa-list tw-mr-1 text-info"></i> Endpoints
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" style="margin-bottom:0;">
                                            <thead style="background:#f8f9fa;">
                                                <tr><th style="width:80px;">Method</th><th>Endpoint</th><th>Description</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="label label-success">POST</span></td>
                                                    <td><code>/ccx_leads_api/add_lead</code></td>
                                                    <td>Create a new lead</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="label label-info">GET</span></td>
                                                    <td><code>/ccx_leads_api/get_leads</code></td>
                                                    <td>List leads (params: <code>status</code>, <code>limit</code>, <code>offset</code>)</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="label label-info">GET</span></td>
                                                    <td><code>/ccx_leads_api/get_lead/{id}</code></td>
                                                    <td>Get a single lead by ID</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Add Lead — Fields & Example -->
                                <div style="border:1px solid #e5e5e5; border-radius:8px; padding:20px; margin-bottom:20px; background:#fff;">
                                    <h4 style="margin:0 0 4px 0; font-weight:600;">
                                        <span class="label label-success">POST</span> Add Lead
                                    </h4>
                                    <p class="text-muted" style="font-size:13px; margin-bottom:12px;">Create a new lead. Only <code>name</code> is required.</p>

                                    <h5 style="font-weight:600; margin-bottom:8px;">Request Fields</h5>
                                    <div class="table-responsive" style="margin-bottom:16px;">
                                        <table class="table table-bordered table-condensed" style="margin-bottom:0; font-size:12px;">
                                            <thead style="background:#f8f9fa;"><tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                            <tbody>
                                                <tr><td><code>name</code></td><td>string</td><td><span class="text-danger">Yes</span></td><td>Lead name</td></tr>
                                                <tr><td><code>email</code></td><td>string</td><td>No</td><td>Email address</td></tr>
                                                <tr><td><code>phonenumber</code></td><td>string</td><td>No</td><td>Phone number</td></tr>
                                                <tr><td><code>title</code></td><td>string</td><td>No</td><td>Job title</td></tr>
                                                <tr><td><code>company</code></td><td>string</td><td>No</td><td>Company name</td></tr>
                                                <tr><td><code>website</code></td><td>string</td><td>No</td><td>Website URL</td></tr>
                                                <tr><td><code>lead_value</code></td><td>number</td><td>No</td><td>Monetary value</td></tr>
                                                <tr><td><code>address</code></td><td>string</td><td>No</td><td>Street address</td></tr>
                                                <tr><td><code>city</code></td><td>string</td><td>No</td><td>City</td></tr>
                                                <tr><td><code>state</code></td><td>string</td><td>No</td><td>State/Province</td></tr>
                                                <tr><td><code>country</code></td><td>integer</td><td>No</td><td>Country ID</td></tr>
                                                <tr><td><code>zip</code></td><td>string</td><td>No</td><td>ZIP/Postal code</td></tr>
                                                <tr><td><code>description</code></td><td>string</td><td>No</td><td>Description</td></tr>
                                                <tr><td><code>status</code></td><td>integer</td><td>No</td><td>Status ID (auto-defaults)</td></tr>
                                                <tr><td><code>source</code></td><td>integer</td><td>No</td><td>Source ID (auto-defaults)</td></tr>
                                                <tr><td><code>assigned</code></td><td>integer</td><td>No</td><td>Staff member ID</td></tr>
                                                <tr><td><code>is_public</code></td><td>integer</td><td>No</td><td>1 = public, 0 = private</td></tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php $_api_url = isset($api_base_url) ? $api_base_url : site_url('ccx_leads_api/'); ?>

                                    <h5 style="font-weight:600; margin-bottom:8px;">cURL Example</h5>
                                    <pre style="background:#1e1e2e; color:#cdd6f4; padding:14px 16px; border-radius:6px; font-size:12px; overflow-x:auto; margin-bottom:16px;"><code>curl -X POST "<?= e($_api_url); ?>add_lead" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phonenumber": "+1234567890",
    "company": "Acme Inc",
    "source": 1,
    "status": 1
  }'</code></pre>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 style="font-weight:600; margin-bottom:8px;">Success Response <span class="label label-success">201</span></h5>
                                            <pre style="background:#1e1e2e; color:#a6e3a1; padding:12px 14px; border-radius:6px; font-size:12px;"><code>{
  "success": true,
  "lead_id": 42,
  "message": "Lead created successfully."
}</code></pre>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 style="font-weight:600; margin-bottom:8px;">Error Response <span class="label label-danger">401</span></h5>
                                            <pre style="background:#1e1e2e; color:#f38ba8; padding:12px 14px; border-radius:6px; font-size:12px;"><code>{
  "success": false,
  "error": "UNAUTHORIZED",
  "message": "Invalid or missing API key."
}</code></pre>
                                        </div>
                                    </div>
                                </div>

                                <!-- Code Examples -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div style="border:1px solid #e5e5e5; border-radius:8px; padding:20px; background:#fff;">
                                            <h4 style="margin:0 0 12px 0; font-weight:600;">
                                                <i class="fa-brands fa-php tw-mr-1" style="color:#777BB3;"></i> PHP
                                            </h4>
                                            <pre style="background:#1e1e2e; color:#cdd6f4; padding:12px 14px; border-radius:6px; font-size:11px; overflow-x:auto;"><code>$ch = curl_init('<?= e($_api_url); ?>add_lead');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'name'  =&gt; 'Jane Smith',
    'email' =&gt; 'jane@example.com',
    'phonenumber' =&gt; '9876543210',
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Api-Key: YOUR_API_KEY',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = json_decode(curl_exec($ch), true);
curl_close($ch);
echo $result['lead_id'];</code></pre>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div style="border:1px solid #e5e5e5; border-radius:8px; padding:20px; background:#fff;">
                                            <h4 style="margin:0 0 12px 0; font-weight:600;">
                                                <i class="fa-brands fa-js tw-mr-1" style="color:#F7DF1E;"></i> JavaScript
                                            </h4>
                                            <pre style="background:#1e1e2e; color:#cdd6f4; padding:12px 14px; border-radius:6px; font-size:11px; overflow-x:auto;"><code>const res = await fetch(
  '<?= e($_api_url); ?>add_lead',
  {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Api-Key': 'YOUR_API_KEY'
    },
    body: JSON.stringify({
      name: 'Jane Smith',
      email: 'jane@example.com',
      phonenumber: '9876543210'
    })
  }
);
const data = await res.json();
console.log(data.lead_id);</code></pre>
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

<!-- ==================== STATUS MODAL (from core Perfex) ==================== -->
<!-- Must be placed AFTER the wrapper div, before init_tail or after -->
<?php include_once APPPATH . 'views/admin/leads/status.php'; ?>

<!-- ==================== SOURCE MODAL ==================== -->
<div class="modal fade" id="source" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('leads/source'), ['id' => 'leads-source-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('edit_source'); ?></span>
                    <span class="add-title"><?= _l('lead_new_source'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="source_additional"></div>
                        <?= render_input('name', 'leads_source_add_edit_name'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>

<!-- ==================== CUSTOM FIELD ADD/EDIT MODAL ==================== -->
<div class="modal fade" id="ccx_cf_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="ccx-cf-modal-add-title"><?= _l('add_new', _l('custom_field')); ?></span>
                    <span class="ccx-cf-modal-edit-title hide"><?= _l('edit', _l('custom_field')); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <form id="ccx-cf-form">
                    <input type="hidden" name="id" id="ccx_cf_id" value="">
                    <input type="hidden" name="fieldto" value="leads">

                    <?= render_input('name', 'custom_field_name', '', 'text', ['required' => true]); ?>

                    <div class="select-placeholder form-group">
                        <label for="ccx_cf_type"><?= _l('custom_field_add_edit_type'); ?></label>
                        <select name="type" id="ccx_cf_type" class="selectpicker" data-width="100%"
                            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>" required>
                            <option value=""></option>
                            <option value="input">Input</option>
                            <option value="number">Number</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Select</option>
                            <option value="multiselect">Multi Select</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="date_picker">Date Picker</option>
                            <option value="date_picker_time">Datetime Picker</option>
                            <option value="colorpicker">Color Picker</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>

                    <div id="ccx_cf_options_wrapper" class="hide">
                        <span class="pull-left fa-regular fa-circle-question" data-toggle="tooltip"
                            title="Separate each option by comma. e.g: Option1, Option2"></span>
                        <?= render_textarea('options', 'custom_field_add_edit_options', '', ['rows' => 3]); ?>
                    </div>

                    <div id="ccx_cf_default_value_wrapper">
                        <?= render_input('default_value', 'custom_field_add_edit_default_value', ''); ?>
                    </div>

                    <?= render_input('field_order', 'custom_field_add_edit_order', '', 'number'); ?>

                    <div class="form-group">
                        <label for="bs_column"><?= _l('custom_field_column'); ?></label>
                        <div class="input-group">
                            <span class="input-group-addon">col-md-</span>
                            <input type="number" max="12" min="1" class="form-control" name="bs_column" id="ccx_cf_bs_column" value="12">
                        </div>
                    </div>

                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="required" id="ccx_cf_required">
                        <label for="ccx_cf_required"><?= _l('custom_field_required'); ?></label>
                    </div>

                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="show_on_table" id="ccx_cf_show_on_table">
                        <label for="ccx_cf_show_on_table"><?= _l('custom_field_show_on_table'); ?></label>
                    </div>

                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="disabled" id="ccx_cf_disabled">
                        <label for="ccx_cf_disabled"><?= _l('custom_field_add_edit_disabled'); ?></label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="button" class="btn btn-primary" id="ccx-cf-save-btn"><?= _l('submit'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    $(function () {
        // ==================== FIELDS TAB JS ====================
        $('#ccx-save-field-settings').on('click', function () {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin tw-mr-1"></i> Saving...');

            var postData = $('#ccx-field-settings-form').serialize();
            if (typeof csrfData !== 'undefined') {
                postData += '&' + csrfData.token_name + '=' + csrfData.hash;
            }

            $.ajax({
                url: admin_url + 'ccx_leads/save_field_settings',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert_float('success', response.message);
                    } else {
                        alert_float('danger', response.message || 'Failed to save');
                    }
                },
                error: function () {
                    alert_float('danger', 'An error occurred while saving');
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa-regular fa-floppy-disk tw-mr-1"></i> Save Changes');
                }
            });
        });

        // ==================== SOURCE MODAL VALIDATION ====================
        appValidateForm($('#leads-source-form'), {
            name: 'required'
        }, manage_leads_sources);

        $('#source').on('hidden.bs.modal', function (event) {
            $('#source_additional').html('');
            $('#source input[name="name"]').val('');
            $('#source .add-title').removeClass('hide');
            $('#source .edit-title').removeClass('hide');
        });

        // ==================== ORDERING TAB JS ====================
        $('.ccx-col-sortable').sortable({
            connectWith: '.ccx-col-sortable',
            handle: '.fa-grip-vertical',
            cursor: 'grabbing',
            placeholder: 'ccx-sort-placeholder',
            tolerance: 'pointer',
            forcePlaceholderSize: true
        });

        $('#ccx-save-field-order').on('click', function () {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin tw-mr-1"></i> Saving...');

            // Build PHP-compatible POST params: columns[1][0][type]=..., columns[1][0][id]=...
            var params = [];
            if (typeof csrfData !== 'undefined') {
                params.push(encodeURIComponent(csrfData.token_name) + '=' + encodeURIComponent(csrfData.hash));
            }

            $('.ccx-col-sortable').each(function () {
                var colNum = $(this).data('column');
                $(this).find('.ccx-field-order-item').each(function (i) {
                    params.push('columns[' + colNum + '][' + i + '][type]=' + encodeURIComponent($(this).data('type')));
                    params.push('columns[' + colNum + '][' + i + '][id]=' + encodeURIComponent($(this).data('id')));
                });
            });

            $.ajax({
                url: admin_url + 'ccx_leads/save_field_order',
                type: 'POST',
                data: params.join('&'),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert_float('success', response.message);
                    } else {
                        alert_float('danger', response.message || 'Failed to save');
                    }
                },
                error: function () {
                    alert_float('danger', 'An error occurred while saving');
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa-regular fa-floppy-disk tw-mr-1"></i> Save Order');
                }
            });
        });

        // ==================== CUSTOM FIELDS TAB JS ====================
        // Toggle active/inactive
        $(document).on('change', '.ccx-cf-active-toggle', function () {
            var cfId = $(this).data('id');
            var status = $(this).is(':checked') ? 1 : 0;
            $.ajax({
                url: admin_url + 'ccx_leads/toggle_custom_field/' + cfId + '/' + status,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert_float('success', 'Custom field status updated');
                    }
                }
            });
        });

        // Type change in modal — show/hide options
        $('#ccx_cf_type').on('change', function () {
            var type = $(this).val();
            if (type == 'select' || type == 'multiselect' || type == 'checkbox') {
                $('#ccx_cf_options_wrapper').removeClass('hide');
            } else {
                $('#ccx_cf_options_wrapper').addClass('hide');
            }
            if (type == 'link') {
                $('#ccx_cf_default_value_wrapper').addClass('hide');
            } else {
                $('#ccx_cf_default_value_wrapper').removeClass('hide');
            }
        });

        // Save custom field
        $('#ccx-cf-save-btn').on('click', function () {
            var name = $('#ccx-cf-form input[name="name"]').val();
            var type = $('#ccx_cf_type').val();
            if (!name || name.trim() == '') {
                alert_float('warning', 'Field name is required');
                return;
            }
            if (!type || type == '') {
                alert_float('warning', 'Field type is required');
                return;
            }

            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin tw-mr-1"></i> Saving...');

            var postData = $('#ccx-cf-form').serialize();
            if (typeof csrfData !== 'undefined') {
                postData += '&' + csrfData.token_name + '=' + csrfData.hash;
            }

            $.ajax({
                url: admin_url + 'ccx_leads/save_custom_field',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert_float('success', response.message);
                        $('#ccx_cf_modal').modal('hide');
                        window.location.reload();
                    } else {
                        alert_float('danger', response.message || 'Failed to save');
                    }
                },
                error: function () {
                    alert_float('danger', 'An error occurred while saving');
                },
                complete: function () {
                    btn.prop('disabled', false).html('<?= _l("submit"); ?>');
                }
            });
        });

        // Reset modal on close
        $('#ccx_cf_modal').on('hidden.bs.modal', function () {
            $('#ccx-cf-form')[0].reset();
            $('#ccx_cf_id').val('');
            $('#ccx_cf_type').val('').selectpicker('refresh');
            $('#ccx_cf_options_wrapper').addClass('hide');
            $('#ccx_cf_default_value_wrapper').removeClass('hide');
            $('.ccx-cf-modal-add-title').removeClass('hide');
            $('.ccx-cf-modal-edit-title').addClass('hide');
        });
    });

    // ==================== CUSTOM FIELD FUNCTIONS ====================
    function ccx_open_cf_modal() {
        $('.ccx-cf-modal-add-title').removeClass('hide');
        $('.ccx-cf-modal-edit-title').addClass('hide');
        $('#ccx_cf_modal').modal('show');
    }

    function ccx_edit_cf(id) {
        var cfData = ccx_cf_data[id];
        if (!cfData) {
            alert_float('danger', 'Custom field data not found');
            return;
        }

        $('#ccx_cf_id').val(cfData.id);
        $('#ccx-cf-form input[name="name"]').val(cfData.name);
        $('#ccx_cf_type').val(cfData.type).selectpicker('refresh');

        if (cfData.type == 'select' || cfData.type == 'multiselect' || cfData.type == 'checkbox') {
            $('#ccx_cf_options_wrapper').removeClass('hide');
            $('#ccx-cf-form textarea[name="options"]').val(cfData.options);
        } else {
            $('#ccx_cf_options_wrapper').addClass('hide');
        }

        $('#ccx-cf-form input[name="default_value"]').val(cfData.default_value);
        $('#ccx-cf-form input[name="field_order"]').val(cfData.field_order);
        $('#ccx_cf_bs_column').val(cfData.bs_column || 12);
        $('#ccx_cf_required').prop('checked', cfData.required == 1);
        $('#ccx_cf_show_on_table').prop('checked', cfData.show_on_table == 1);
        $('#ccx_cf_disabled').prop('checked', cfData.active == 0);

        $('.ccx-cf-modal-add-title').addClass('hide');
        $('.ccx-cf-modal-edit-title').removeClass('hide');
        $('#ccx_cf_modal').modal('show');
    }

    function ccx_delete_cf(id) {
        if (confirm_delete()) {
            $.ajax({
                url: admin_url + 'ccx_leads/delete_custom_field/' + id,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert_float('success', response.message);
                        window.location.reload();
                    } else {
                        alert_float('danger', response.message || 'Failed to delete');
                    }
                },
                error: function () {
                    alert_float('danger', 'An error occurred');
                }
            });
        }
        return false;
    }

    // Store custom fields data for edit modal population
    var ccx_cf_data = <?= json_encode(
        array_column(
            array_map(function($cf) {
                return [
                    'id' => $cf['id'],
                    'name' => $cf['name'],
                    'type' => $cf['type'],
                    'options' => $cf['options'],
                    'default_value' => $cf['default_value'],
                    'field_order' => $cf['field_order'],
                    'bs_column' => $cf['bs_column'],
                    'required' => $cf['required'],
                    'show_on_table' => $cf['show_on_table'],
                    'active' => $cf['active'],
                ];
            }, isset($custom_fields) ? $custom_fields : []),
            null, 'id'
        )
    ); ?>;

    // ==================== STATUS FUNCTIONS ====================
    function new_status() {
        $('#status').modal('show');
        $('#status .edit-title').addClass('hide');
    }

    function edit_status(invoker, id) {
        $('#additional').append(hidden_input('id', id));
        $('#status input[name="name"]').val($(invoker).data('name'));
        $('#status .colorpicker-input').colorpicker('setValue', $(invoker).data('color'));
        $('#status input[name="statusorder"]').val($(invoker).data('order'));
        $('#status').modal('show');
        $('#status .add-title').addClass('hide');
    }

    function manage_leads_statuses(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            window.location.reload();
        });
        return false;
    }

    function ccx_delete_status(id) {
        if (confirm_delete()) {
            $.get(admin_url + 'leads/delete_status/' + id).done(function () {
                window.location.reload();
            });
        }
        return false;
    }

    // ==================== SOURCE FUNCTIONS ====================
    function new_source() {
        $('#source').modal('show');
        $('#source .edit-title').addClass('hide');
    }

    function edit_source(invoker, id) {
        $('#source_additional').append(hidden_input('id', id));
        $('#source input[name="name"]').val($(invoker).data('name'));
        $('#source').modal('show');
        $('#source .add-title').addClass('hide');
    }

    function manage_leads_sources(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            window.location.reload();
        });
        return false;
    }

    function ccx_delete_source(id) {
        if (confirm_delete()) {
            $.get(admin_url + 'leads/delete_source/' + id).done(function () {
                window.location.reload();
            });
        }
        return false;
    }

    // ==================== API TAB JS ====================
    function ccx_copy_text(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function () {
                alert_float('success', 'Copied to clipboard!');
            });
        } else {
            var tmp = document.createElement('textarea');
            tmp.value = text;
            document.body.appendChild(tmp);
            tmp.select();
            document.execCommand('copy');
            document.body.removeChild(tmp);
            alert_float('success', 'Copied to clipboard!');
        }
    }

    $(document).on('click', '#ccx-copy-api-key', function () {
        var key = $('#ccx-api-key-display').val();
        if (key) {
            ccx_copy_text(key);
        } else {
            alert_float('warning', 'No API key to copy. Generate one first.');
        }
    });

    $(document).on('click', '.ccx-copy-btn', function () {
        var target = $(this).data('target');
        if (target) {
            ccx_copy_text($(target).val());
        }
    });

    $(document).on('click', '#ccx-generate-api-key', function () {
        var btn = $(this);
        var existing = $('#ccx-api-key-display').val();
        if (existing && !confirm('This will invalidate the current API key. All integrations using the old key will stop working. Continue?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin tw-mr-1"></i> Generating...');

        var postData = {};
        if (typeof csrfData !== 'undefined') {
            postData[csrfData.token_name] = csrfData.hash;
        }

        $.ajax({
            url: admin_url + 'ccx_leads/generate_api_key',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#ccx-api-key-display').val(response.api_key);
                    btn.html('<i class="fa-solid fa-rotate tw-mr-1"></i> Regenerate');
                    alert_float('success', 'API key generated successfully!');
                } else {
                    alert_float('danger', 'Failed to generate API key');
                }
            },
            error: function () {
                alert_float('danger', 'An error occurred');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });

</script>
</body>

</html>