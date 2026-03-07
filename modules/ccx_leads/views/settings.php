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

                            <!-- ==================== ORDERING TAB ==================== -->
                            <div role="tabpanel" class="tab-pane" id="ordering">
                                <p class="text-muted">Ordering settings coming soon...</p>
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
    });

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
</script>
</body>

</html>