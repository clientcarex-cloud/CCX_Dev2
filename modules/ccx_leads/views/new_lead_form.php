<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CCX Leads — Independent New Lead Form
 * Adapted from core profile.php for new-lead creation only.
 * Field Management settings are applied server-side.
 * Fields are rendered in configurable order via field_order.
 */

// Helper to check field visibility, label, required status, and order
if (!function_exists('ccx_field')) {
    function ccx_field($slug, $field_settings, $default_label = '')
    {
        $setting = [
            'active' => 1,
            'label' => $default_label,
            'required' => 0,
            'field_order' => 999,
        ];
        if (isset($field_settings[$slug])) {
            $s = $field_settings[$slug];
            $setting['active'] = isset($s['active']) ? (int) $s['active'] : 1;
            $setting['label'] = !empty($s['label']) ? $s['label'] : $default_label;
            $setting['required'] = isset($s['required']) ? (int) $s['required'] : 0;
            $setting['field_order'] = isset($s['field_order']) ? (int) $s['field_order'] : 999;
        }
        return $setting;
    }
}

// Helper to render label with optional required asterisk
if (!function_exists('ccx_label')) {
    function ccx_label($for, $text, $required = false)
    {
        $req = $required ? ' <small class="req text-danger">*</small>' : '';
        return '<label for="' . $for . '" class="control-label">' . e($text) . $req . '</label>';
    }
}

$fs = isset($field_settings) ? $field_settings : [];
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">
        <?= _l('add_new', _l('lead_lowercase')); ?>
    </h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= form_open(admin_url('ccx_leads/save_lead'), ['id' => 'ccx_new_lead_form']); ?>

            <?php
            // ── Top row: Status, Source, Assigned ──
            $f_status = ccx_field('status', $fs, _l('lead_add_edit_status'));
            $f_source = ccx_field('source', $fs, _l('lead_add_edit_source'));
            $f_assigned = ccx_field('assigned', $fs, _l('lead_add_edit_assigned'));
            ?>
            <div class="row">
                <?php if ($f_status['active']) { ?>
                    <div class="col-md-4" style="order:<?= $f_status['field_order']; ?>">
                        <div class="select-placeholder form-group" app-field-wrapper="status">
                            <?= ccx_label('status', $f_status['label'], $f_status['required']); ?>
                            <select id="status" name="status" class="selectpicker" data-live-search="true" data-width="100%"
                                data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>" <?= $f_status['required'] ? ' required' : ''; ?>>
                                <option value=""></option>
                                <?php
                                $default_status = get_option('leads_default_status');
                                foreach ($statuses as $s) {
                                    $sel = ($s['id'] == $default_status) ? ' selected' : '';
                                    echo '<option value="' . $s['id'] . '" data-content="<span class=\'lead-status-' . $s['id'] . ' label\' style=\'color:' . $s['color'] . ';border:1px solid ' . adjust_hex_brightness($s['color'], 0.4) . ';background:' . adjust_hex_brightness($s['color'], 0.04) . '\'>' . e($s['name']) . '</span>"' . $sel . '>' . e($s['name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($f_source['active']) { ?>
                    <div class="col-md-4" style="order:<?= $f_source['field_order']; ?>">
                        <div class="select-placeholder form-group" app-field-wrapper="source">
                            <?= ccx_label('source', $f_source['label'], $f_source['required']); ?>
                            <select id="source" name="source" class="selectpicker" data-live-search="true" data-width="100%"
                                data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>" <?= $f_source['required'] ? ' required' : ''; ?>>
                                <option value=""></option>
                                <?php
                                $default_source = get_option('leads_default_source');
                                foreach ($sources as $s) {
                                    $sel = ($s['id'] == $default_source) ? ' selected' : '';
                                    echo '<option value="' . $s['id'] . '"' . $sel . '>' . e($s['name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($f_assigned['active']) { ?>
                    <div class="col-md-4" style="order:<?= $f_assigned['field_order']; ?>">
                        <div class="select-placeholder form-group" app-field-wrapper="assigned">
                            <?= ccx_label('assigned', $f_assigned['label'], $f_assigned['required']); ?>
                            <select id="assigned" name="assigned" class="selectpicker" data-live-search="true"
                                data-width="100%" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                <?= $f_assigned['required'] ? ' required' : ''; ?>>
                                <option value=""></option>
                                <?php
                                $current_staff = get_staff_user_id();
                                foreach ($members as $m) {
                                    $sel = ($m['staffid'] == $current_staff) ? ' selected' : '';
                                    echo '<option value="' . $m['staffid'] . '"' . $sel . '>' . e($m['firstname'] . ' ' . $m['lastname']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="clearfix"></div>
            <hr class="mtop5 mbot10" />

            <?php
            // ==================== MAIN FIELDS — flex container with configurable order ====================
            // Build array of field configs for the main area
            $main_fields = [
                'name' => ['default_label' => _l('lead_add_edit_name'), 'type' => 'text'],
                'title' => ['default_label' => _l('lead_title'), 'type' => 'text'],
                'email' => ['default_label' => _l('lead_add_edit_email'), 'type' => 'text'],
                'website' => ['default_label' => _l('lead_website'), 'type' => 'text'],
                'phonenumber' => ['default_label' => _l('lead_add_edit_phonenumber'), 'type' => 'text'],
                'lead_value' => ['default_label' => _l('lead_value'), 'type' => 'lead_value'],
                'company' => ['default_label' => _l('lead_company'), 'type' => 'text'],
                'address' => ['default_label' => _l('lead_address'), 'type' => 'address'],
                'city' => ['default_label' => _l('lead_city'), 'type' => 'text'],
                'state' => ['default_label' => _l('lead_state'), 'type' => 'text'],
                'country' => ['default_label' => _l('lead_country'), 'type' => 'country'],
                'zip' => ['default_label' => _l('lead_zip'), 'type' => 'text'],
            ];

            // Get custom fields for leads to render inline
            $lead_custom_fields = get_custom_fields('leads');
            ?>
            <div class="row ccx-ordered-fields" style="display:flex; flex-wrap:wrap;">
                <?php
                // Render standard fields
                foreach ($main_fields as $slug => $cfg) {
                    $f = ccx_field($slug, $fs, $cfg['default_label']);
                    if (!$f['active'])
                        continue;
                    $order = $f['field_order'];
                    ?>
                    <div class="col-md-6" style="order:<?= $order; ?>">
                        <?php if ($cfg['type'] === 'text') { ?>
                            <div class="form-group" app-field-wrapper="<?= $slug; ?>">
                                <?= ccx_label($slug, $f['label'], $f['required']); ?>
                                <input type="text" id="<?= $slug; ?>" name="<?= $slug; ?>" class="form-control" value=""
                                    <?= $f['required'] ? ' required' : ''; ?>>
                            </div>
                        <?php } elseif ($cfg['type'] === 'address') { ?>
                            <div class="form-group" app-field-wrapper="address">
                                <?= ccx_label('address', $f['label'], $f['required']); ?>
                                <textarea id="address" name="address" class="form-control" rows="1"
                                    style="height:36px;font-size:100%;" <?= $f['required'] ? ' required' : ''; ?>></textarea>
                            </div>
                        <?php } elseif ($cfg['type'] === 'lead_value') { ?>
                            <div class="form-group" app-field-wrapper="lead_value">
                                <?= ccx_label('lead_value', $f['label'], $f['required']); ?>
                                <div class="input-group" data-toggle="tooltip" title="<?= _l('lead_value_tooltip'); ?>">
                                    <input type="number" class="form-control" name="lead_value" id="lead_value" value=""
                                        <?= $f['required'] ? ' required' : ''; ?>>
                                    <div class="input-group-addon">
                                        <?= e($base_currency->symbol); ?>
                                    </div>
                                </div>
                            </div>
                        <?php } elseif ($cfg['type'] === 'country') { ?>
                            <?php
                            $countries = get_all_countries();
                            $customer_default_country = get_option('customer_default_country');
                            ?>
                            <div class="select-placeholder form-group" app-field-wrapper="country">
                                <?= ccx_label('country', $f['label'], $f['required']); ?>
                                <select id="country" name="country" class="selectpicker" data-live-search="true"
                                    data-width="100%" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                    <?= $f['required'] ? ' required' : ''; ?>>
                                    <option value=""></option>
                                    <?php foreach ($countries as $c) {
                                        $sel = ($c['country_id'] == $customer_default_country) ? ' selected' : '';
                                        echo '<option value="' . $c['country_id'] . '"' . $sel . '>' . e($c['short_name']) . '</option>';
                                    } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php
                // ── Render custom fields as a block inside the flex container ──
                $cf_html = render_custom_fields('leads', false);
                if (trim($cf_html) != '') {
                    $cf_min_order = 999;
                    if (!empty($lead_custom_fields)) {
                        $cf_orders = array_map(function ($c) {
                            return isset($c['field_order']) ? (int) $c['field_order'] : 999;
                        }, $lead_custom_fields);
                        $cf_min_order = min($cf_orders);
                    }
                    ?>
                    <div class="col-md-12" style="order:<?= $cf_min_order; ?>; padding:0;">
                        <?= $cf_html; ?>
                    </div>
                <?php } ?>
            </div>

            <div class="col-md-12" style="padding:0;">
                <?php
                // ── Description ──
                $f = ccx_field('description', $fs, _l('lead_description'));
                if ($f['active']) { ?>
                    <div class="form-group" app-field-wrapper="description" style="order:<?= $f['field_order']; ?>">
                        <?= ccx_label('description', $f['label'], $f['required']); ?>
                        <textarea id="description" name="description" class="form-control" rows="4" <?= $f['required'] ? ' required' : ''; ?>></textarea>
                    </div>
                <?php } ?>

                <div class="row">
                    <div class="col-md-12">
                        <?php
                        // ── Is Public ──
                        $f = ccx_field('is_public', $fs, _l('lead_public'));
                        if ($f['active']) { ?>
                            <div class="checkbox-inline checkbox">
                                <input type="checkbox" name="is_public" id="ccx_lead_public">
                                <label for="ccx_lead_public"><?= e($f['label']); ?></label>
                            </div>
                        <?php } ?>

                        <div class="checkbox-inline checkbox checkbox-primary">
                            <input type="checkbox" name="contacted_today" id="ccx_contacted_today" checked>
                            <label for="ccx_contacted_today"><?= _l('lead_add_edit_contacted_today'); ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>
            <hr class="-tw-mx-5 tw-border-neutral-200" />
            <div class="text-right">
                <button type="button" class="btn btn-default mright5" data-dismiss="modal">
                    <?= _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-primary ccx-new-lead-save-btn">
                    <?= _l('submit'); ?>
                </button>
            </div>

            <?= form_close(); ?>
        </div>
    </div>
</div>