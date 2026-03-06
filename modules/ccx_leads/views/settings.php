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

                                <script>
                                    $(function () {
                                        $('#ccx-save-field-settings').on('click', function () {
                                            var btn = $(this);
                                            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin tw-mr-1"></i> Saving...');

                                            $.ajax({
                                                url: admin_url + 'ccx_leads/save_field_settings',
                                                type: 'POST',
                                                data: $('#ccx-field-settings-form').serialize() + '&' + csrfData.token_name + '=' + csrfData.hash,
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
                                    });
                                </script>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="ordering">
                                <p class="text-muted">Ordering settings coming soon...</p>
                            </div>
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
                                            <th><?= _l('id'); ?></th>
                                            <th><?= _l('leads_status_table_name'); ?></th>
                                            <th class="options"><?= _l('options'); ?></th>
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
                                                                <a href="<?= admin_url('leads/delete_status/' . $status['id']); ?>"
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

                                <?php include_once APPPATH . 'views/admin/leads/status.php'; ?>
                            </div>
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
<?php init_tail(); ?>
</body>

</html>