<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CCX Leads — Independent New Lead Form
 * Fields rendered in 3-column layout based on saved field_layout.
 */

// Helper to check field visibility, label, required status
if (!function_exists('ccx_field')) {
    function ccx_field($slug, $field_settings, $default_label = '')
    {
        $setting = ['active' => 1, 'label' => $default_label, 'required' => 0];
        if (isset($field_settings[$slug])) {
            $s = $field_settings[$slug];
            $setting['active'] = isset($s['active']) ? (int) $s['active'] : 1;
            $setting['label'] = !empty($s['label']) ? $s['label'] : $default_label;
            $setting['required'] = isset($s['required']) ? (int) $s['required'] : 0;
        }
        return $setting;
    }
}

if (!function_exists('ccx_label')) {
    function ccx_label($for, $text, $required = false)
    {
        $req = $required ? ' <small class="req text-danger">*</small>' : '';
        return '<label for="' . $for . '" class="control-label">' . e($text) . $req . '</label>';
    }
}

$fs = isset($field_settings) ? $field_settings : [];
$layout = isset($field_layout) ? $field_layout : ['1' => [], '2' => [], '3' => []];

// Build a lookup of slug → column for standard fields
$slug_to_col = [];
foreach (['1', '2', '3'] as $cn) {
    if (!isset($layout[$cn]))
        continue;
    foreach ($layout[$cn] as $idx => $fi) {
        if ($fi['type'] === 'standard') {
            $slug_to_col[$fi['slug']] = ['col' => $cn, 'order' => $idx];
        }
    }
}

// Helper: get fields for a column (only standard field slugs)
function ccx_col_slugs($layout, $col)
{
    $slugs = [];
    if (isset($layout[$col])) {
        foreach ($layout[$col] as $fi) {
            if ($fi['type'] === 'standard') {
                $slugs[] = $fi['slug'];
            }
        }
    }
    return $slugs;
}

// Helper: check if a column has any custom fields
function ccx_col_has_custom($layout, $col)
{
    if (isset($layout[$col])) {
        foreach ($layout[$col] as $fi) {
            if ($fi['type'] === 'custom')
                return true;
        }
    }
    return false;
}
?>

<?php
// ── Rendering helper for a standard field by slug ──
function ccx_render_new_field($slug, $fs, $statuses, $sources, $members, $base_currency)
{
    $labels = [
        'status' => 'lead_add_edit_status',
        'source' => 'lead_add_edit_source',
        'assigned' => 'lead_add_edit_assigned',
        'name' => 'lead_add_edit_name',
        'title' => 'lead_title',
        'email' => 'lead_add_edit_email',
        'website' => 'lead_website',
        'phonenumber' => 'lead_add_edit_phonenumber',
        'lead_value' => 'lead_value',
        'company' => 'lead_company',
        'address' => 'lead_address',
        'city' => 'lead_city',
        'state' => 'lead_state',
        'country' => 'lead_country',
        'zip' => 'lead_zip',
        'description' => 'lead_description',
        'is_public' => 'lead_public',
    ];
    $default_label = isset($labels[$slug]) ? _l($labels[$slug]) : $slug;
    $f = ccx_field($slug, $fs, $default_label);
    if (!$f['active'])
        return;

    switch ($slug) {
        case 'status':
            ?>
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
                    } ?>
                </select>
            </div>
            <?php break;

        case 'source':
            ?>
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
                    } ?>
                </select>
            </div>
            <?php break;

        case 'assigned':
            ?>
            <div class="select-placeholder form-group" app-field-wrapper="assigned">
                <?= ccx_label('assigned', $f['label'], $f['required']); ?>
                <select id="assigned" name="assigned" class="selectpicker" data-live-search="true" data-width="100%"
                    data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>" <?= $f['required'] ? ' required' : ''; ?>>
                    <option value=""></option>
                    <?php
                    $current_staff = get_staff_user_id();
                    foreach ($members as $m) {
                        $sel = ($m['staffid'] == $current_staff) ? ' selected' : '';
                        echo '<option value="' . $m['staffid'] . '"' . $sel . '>' . e($m['firstname'] . ' ' . $m['lastname']) . '</option>';
                    } ?>
                </select>
            </div>
            <?php break;

        case 'lead_value':
            ?>
            <div class="form-group" app-field-wrapper="lead_value">
                <?= ccx_label('lead_value', $f['label'], $f['required']); ?>
                <div class="input-group" data-toggle="tooltip" title="<?= _l('lead_value_tooltip'); ?>">
                    <input type="number" class="form-control" name="lead_value" id="lead_value" value="" <?= $f['required'] ? ' required' : ''; ?>>
                    <div class="input-group-addon"><?= e($base_currency->symbol); ?></div>
                </div>
            </div>
            <?php break;

        case 'address':
            ?>
            <div class="form-group" app-field-wrapper="address">
                <?= ccx_label('address', $f['label'], $f['required']); ?>
                <textarea id="address" name="address" class="form-control" rows="1" style="height:36px;font-size:100%;"
                    <?= $f['required'] ? ' required' : ''; ?>></textarea>
            </div>
            <?php break;

        case 'country':
            $countries = get_all_countries();
            $customer_default_country = get_option('customer_default_country');
            ?>
            <div class="select-placeholder form-group" app-field-wrapper="country">
                <?= ccx_label('country', $f['label'], $f['required']); ?>
                <select id="country" name="country" class="selectpicker" data-live-search="true" data-width="100%"
                    data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>" <?= $f['required'] ? ' required' : ''; ?>>
                    <option value=""></option>
                    <?php foreach ($countries as $c) {
                        $sel = ($c['country_id'] == $customer_default_country) ? ' selected' : '';
                        echo '<option value="' . $c['country_id'] . '"' . $sel . '>' . e($c['short_name']) . '</option>';
                    } ?>
                </select>
            </div>
            <?php break;

        case 'description':
            ?>
            <div class="form-group" app-field-wrapper="description">
                <?= ccx_label('description', $f['label'], $f['required']); ?>
                <textarea id="description" name="description" class="form-control" rows="4" <?= $f['required'] ? ' required' : ''; ?>></textarea>
            </div>
            <?php break;

        case 'is_public':
            ?>
            <div class="checkbox-inline checkbox">
                <input type="checkbox" name="is_public" id="ccx_lead_public">
                <label for="ccx_lead_public"><?= e($f['label']); ?></label>
            </div>
            <?php break;

        default:
            // Simple text field (name, title, email, website, phonenumber, city, state, zip, company)
            ?>
            <div class="form-group" app-field-wrapper="<?= $slug; ?>">
                <?= ccx_label($slug, $f['label'], $f['required']); ?>
                <input type="text" id="<?= $slug; ?>" name="<?= $slug; ?>" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
            </div>
            <?php break;
    }
}
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
            // ==================== COLUMN 1 — Top Row ====================
            $col1_slugs = ccx_col_slugs($layout, '1');
            if (!empty($col1_slugs)) { ?>
                <div class="row">
                    <?php foreach ($col1_slugs as $slug) { ?>
                        <div class="col-md-4">
                            <?php ccx_render_new_field($slug, $fs, $statuses, $sources, $members, $base_currency); ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
                <hr class="mtop5 mbot10" />
            <?php } ?>

            <div class="row">
                <?php
                // ==================== COLUMN 2 — Left ====================
                $col2_slugs = ccx_col_slugs($layout, '2');
                ?>
                <div class="col-md-6">
                    <?php foreach ($col2_slugs as $slug) {
                        ccx_render_new_field($slug, $fs, $statuses, $sources, $members, $base_currency);
                    } ?>
                    <?php
                    // Render custom fields assigned to col 2
                    if (ccx_col_has_custom($layout, '2')) {
                        echo render_custom_fields('leads', false);
                    }
                    ?>
                </div>

                <?php
                // ==================== COLUMN 3 — Right ====================
                $col3_slugs = ccx_col_slugs($layout, '3');
                ?>
                <div class="col-md-6">
                    <?php foreach ($col3_slugs as $slug) {
                        ccx_render_new_field($slug, $fs, $statuses, $sources, $members, $base_currency);
                    } ?>
                    <?php
                    // Render custom fields assigned to col 3
                    if (ccx_col_has_custom($layout, '3') && !ccx_col_has_custom($layout, '2')) {
                        echo render_custom_fields('leads', false);
                    }
                    ?>
                </div>
            </div>

            <?php
            // If no custom fields in col 2 or col 3, render them at the bottom
            if (!ccx_col_has_custom($layout, '2') && !ccx_col_has_custom($layout, '3')) {
                $cf_html = render_custom_fields('leads', false);
                if (trim($cf_html) != '') {
                    echo '<div class="row"><div class="col-md-12">' . $cf_html . '</div></div>';
                }
            }
            ?>

            <div class="clearfix"></div>

            <!-- Call Log Section -->
            <input type="hidden" name="add_call_log" value="1">
            <div class="mtop10">
                <div class="panel panel-default">
                    <div class="panel-body" style="padding:15px;">
                        <div class="form-group">
                            <label for="call_log_description" class="control-label">
                                <i class="fa fa-pencil"></i> Call Notes
                            </label>
                            <textarea id="call_log_description" name="call_log_description" class="form-control"
                                rows="3" placeholder="Enter call notes..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= render_datetime_input(
                                    'call_log_contact_date',
                                    'lead_add_edit_datecontacted',
                                    _dt(date('Y-m-d H:i:s')),
                                    ['data-date-end-date' => date('Y-m-d')]
                                ); ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-top:25px;">
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" name="call_log_contacted" id="call_log_contacted_yes"
                                            value="yes" checked>
                                        <label
                                            for="call_log_contacted_yes"><?= _l('lead_add_edit_contacted_this_lead'); ?></label>
                                    </div>
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" name="call_log_contacted" id="call_log_contacted_no"
                                            value="no">
                                        <label for="call_log_contacted_no"><?= _l('lead_not_contacted'); ?></label>
                                    </div>
                                </div>
                            </div>
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