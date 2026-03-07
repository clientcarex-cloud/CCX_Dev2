<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CCX Leads — Independent New Lead Form
 * Adapted from core profile.php for new-lead creation only.
 * Field Management settings are applied server-side.
 */

// Helper to check field visibility, label, and required status
if (!function_exists('ccx_field')) {
    function ccx_field($slug, $field_settings, $default_label = '')
    {
        $setting = [
            'active' => 1,
            'label' => $default_label,
            'required' => 0,
        ];
        if (isset($field_settings[$slug])) {
            $s = $field_settings[$slug];
            $setting['active'] = isset($s['active']) ? (int) $s['active'] : 1;
            $setting['label'] = !empty($s['label']) ? $s['label'] : $default_label;
            $setting['required'] = isset($s['required']) ? (int) $s['required'] : 0;
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

            <div class="row">
                <?php
                // ── Status ──
                $f = ccx_field('status', $fs, _l('lead_add_edit_status'));
                if ($f['active']) { ?>
                    <div class="col-md-4">
                        <div class="select-placeholder form-group" app-field-wrapper="status">
                            <?= ccx_label('status', $f['label'], $f['required']); ?>
                            <select id="status" name="status" class="selectpicker" data-live-search="true" data-width="100%"
                                data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>" <?= $f['required'] ? ' required' : ''; ?>>
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

                <?php
                // ── Source ──
                $f = ccx_field('source', $fs, _l('lead_add_edit_source'));
                if ($f['active']) { ?>
                    <div class="col-md-4">
                        <div class="select-placeholder form-group" app-field-wrapper="source">
                            <?= ccx_label('source', $f['label'], $f['required']); ?>
                            <select id="source" name="source" class="selectpicker" data-live-search="true" data-width="100%"
                                data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>" <?= $f['required'] ? ' required' : ''; ?>>
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

                <?php
                // ── Assigned ──
                $f = ccx_field('assigned', $fs, _l('lead_add_edit_assigned'));
                if ($f['active']) { ?>
                    <div class="col-md-4">
                        <div class="select-placeholder form-group" app-field-wrapper="assigned">
                            <?= ccx_label('assigned', $f['label'], $f['required']); ?>
                            <select id="assigned" name="assigned" class="selectpicker" data-live-search="true"
                                data-width="100%" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                <?= $f['required'] ? ' required' : ''; ?>>
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

            <div class="row">
                <div class="col-md-6">
                    <?php
                    // ── Name ──
                    $f = ccx_field('name', $fs, _l('lead_add_edit_name'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="name">
                            <?= ccx_label('name', $f['label'], $f['required']); ?>
                            <input type="text" id="name" name="name" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
                        </div>
                    <?php }

                    // ── Title ──
                    $f = ccx_field('title', $fs, _l('lead_title'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="title">
                            <?= ccx_label('title', $f['label'], $f['required']); ?>
                            <input type="text" id="title" name="title" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
                        </div>
                    <?php }

                    // ── Email ──
                    $f = ccx_field('email', $fs, _l('lead_add_edit_email'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="email">
                            <?= ccx_label('email', $f['label'], $f['required']); ?>
                            <input type="text" id="email" name="email" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
                        </div>
                    <?php }

                    // ── Website ──
                    $f = ccx_field('website', $fs, _l('lead_website'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="website">
                            <?= ccx_label('website', $f['label'], $f['required']); ?>
                            <input type="text" id="website" name="website" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
                        </div>
                    <?php }

                    // ── Phone ──
                    $f = ccx_field('phonenumber', $fs, _l('lead_add_edit_phonenumber'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="phonenumber">
                            <?= ccx_label('phonenumber', $f['label'], $f['required']); ?>
                            <input type="text" id="phonenumber" name="phonenumber" class="form-control" value=""
                                <?= $f['required'] ? ' required' : ''; ?>>
                        </div>
                    <?php }

                    // ── Lead Value ──
                    $f = ccx_field('lead_value', $fs, _l('lead_value'));
                    if ($f['active']) { ?>
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
                    <?php }

                    // ── Company ──
                    $f = ccx_field('company', $fs, _l('lead_company'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="company">
                            <?= ccx_label('company', $f['label'], $f['required']); ?>
                            <input type="text" id="company" name="company" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-md-6">
                    <?php
                    // ── Address ──
                    $f = ccx_field('address', $fs, _l('lead_address'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="address">
                            <?= ccx_label('address', $f['label'], $f['required']); ?>
                            <textarea id="address" name="address" class="form-control" rows="1"
                                style="height:36px;font-size:100%;" <?= $f['required'] ? ' required' : ''; ?>></textarea>
                        </div>
                    <?php }

                    // ── City ──
                    $f = ccx_field('city', $fs, _l('lead_city'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="city">
                            <?= ccx_label('city', $f['label'], $f['required']); ?>
                            <input type="text" id="city" name="city" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
                        </div>
                    <?php }

                    // ── State ──
                    $f = ccx_field('state', $fs, _l('lead_state'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="state">
                            <?= ccx_label('state', $f['label'], $f['required']); ?>
                            <input type="text" id="state" name="state" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
                        </div>
                    <?php }

                    // ── Country ──
                    $f = ccx_field('country', $fs, _l('lead_country'));
                    if ($f['active']) {
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
                    <?php }

                    // ── Zip ──
                    $f = ccx_field('zip', $fs, _l('lead_zip'));
                    if ($f['active']) { ?>
                        <div class="form-group" app-field-wrapper="zip">
                            <?= ccx_label('zip', $f['label'], $f['required']); ?>
                            <input type="text" id="zip" name="zip" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="col-md-12" style="padding:0;">
                <?php
                // ── Description ──
                $f = ccx_field('description', $fs, _l('lead_description'));
                if ($f['active']) { ?>
                    <div class="form-group" app-field-wrapper="description">
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

            <?php
            // ── Custom fields ──
            echo render_custom_fields('leads', false);
            ?>

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