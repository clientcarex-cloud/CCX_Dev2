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
                        <?php
                        $selected = isset($status_id) ? $status_id : get_option('leads_default_status');
                        echo render_leads_status_select($statuses, $selected, $f['label']);
                        if ($f['required']) {
                            echo '<script>$(function(){ $("#ccx_new_lead_form select[name=status]").attr("required", true); });</script>';
                        }
                        ?>
                    </div>
                <?php } ?>

                <?php
                // ── Source ──
                $f = ccx_field('source', $fs, _l('lead_add_edit_source'));
                if ($f['active']) { ?>
                    <div class="col-md-4">
                        <?= render_leads_source_select($sources, get_option('leads_default_source'), $f['label']); ?>
                        <?php if ($f['required']) { ?>
                            <script>$(function () { $('#ccx_new_lead_form select[name=source]').attr('required', true); });</script>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php
                // ── Assigned ──
                $f = ccx_field('assigned', $fs, _l('lead_add_edit_assigned'));
                if ($f['active']) { ?>
                    <div class="col-md-4">
                        <?= render_select('assigned', $members, ['staffid', ['firstname', 'lastname']], $f['label'], get_staff_user_id()); ?>
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
                    if ($f['active']) {
                        $attrs = [];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_input('name', $f['label'], '', 'text', $attrs);
                    }

                    // ── Title ──
                    $f = ccx_field('title', $fs, _l('lead_title'));
                    if ($f['active']) {
                        $attrs = [];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_input('title', $f['label'], '', 'text', $attrs);
                    }

                    // ── Email ──
                    $f = ccx_field('email', $fs, _l('lead_add_edit_email'));
                    if ($f['active']) {
                        $attrs = [];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_input('email', $f['label'], '', 'text', $attrs);
                    }

                    // ── Website ──
                    $f = ccx_field('website', $fs, _l('lead_website'));
                    if ($f['active']) {
                        $attrs = [];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_input('website', $f['label'], '', 'text', $attrs);
                    }

                    // ── Phone ──
                    $f = ccx_field('phonenumber', $fs, _l('lead_add_edit_phonenumber'));
                    if ($f['active']) {
                        $attrs = [];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_input('phonenumber', $f['label'], '', 'text', $attrs);
                    }

                    // ── Lead Value ──
                    $f = ccx_field('lead_value', $fs, _l('lead_value'));
                    if ($f['active']) { ?>
                        <div class="form-group">
                            <label for="lead_value">
                                <?= $f['label']; ?>
                            </label>
                            <div class="input-group" data-toggle="tooltip" title="<?= _l('lead_value_tooltip'); ?>">
                                <input type="number" class="form-control" name="lead_value" value="" <?= $f['required'] ? ' required' : ''; ?>>
                                <div class="input-group-addon">
                                    <?= e($base_currency->symbol); ?>
                                </div>
                            </div>
                        </div>
                    <?php }

                    // ── Company ──
                    $f = ccx_field('company', $fs, _l('lead_company'));
                    if ($f['active']) {
                        $attrs = [];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_input('company', $f['label'], '', 'text', $attrs);
                    }
                    ?>
                </div>

                <div class="col-md-6">
                    <?php
                    // ── Address ──
                    $f = ccx_field('address', $fs, _l('lead_address'));
                    if ($f['active']) {
                        $attrs = ['rows' => 1, 'style' => 'height:36px;font-size:100%;'];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_textarea('address', $f['label'], '', $attrs);
                    }

                    // ── City ──
                    $f = ccx_field('city', $fs, _l('lead_city'));
                    if ($f['active']) {
                        $attrs = [];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_input('city', $f['label'], '', 'text', $attrs);
                    }

                    // ── State ──
                    $f = ccx_field('state', $fs, _l('lead_state'));
                    if ($f['active']) {
                        $attrs = [];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_input('state', $f['label'], '', 'text', $attrs);
                    }

                    // ── Country ──
                    $f = ccx_field('country', $fs, _l('lead_country'));
                    if ($f['active']) {
                        $countries = get_all_countries();
                        $customer_default_country = get_option('customer_default_country');
                        $select_attrs = ['data-none-selected-text' => _l('dropdown_non_selected_tex')];
                        if ($f['required']) {
                            $select_attrs['required'] = true;
                        }
                        echo render_select('country', $countries, ['country_id', ['short_name']], $f['label'], $customer_default_country, $select_attrs);
                    }

                    // ── Zip ──
                    $f = ccx_field('zip', $fs, _l('lead_zip'));
                    if ($f['active']) {
                        $attrs = [];
                        if ($f['required']) {
                            $attrs['required'] = true;
                        }
                        echo render_input('zip', $f['label'], '', 'text', $attrs);
                    }
                    ?>
                </div>
            </div>

            <div class="col-md-12" style="padding:0;">
                <?php
                // ── Description ──
                $f = ccx_field('description', $fs, _l('lead_description'));
                if ($f['active']) {
                    $attrs = [];
                    if ($f['required']) {
                        $attrs['required'] = true;
                    }
                    echo render_textarea('description', $f['label'], '', $attrs);
                }
                ?>

                <div class="row">
                    <div class="col-md-12">
                        <?php
                        // ── Is Public ──
                        $f = ccx_field('is_public', $fs, _l('lead_public'));
                        if ($f['active']) { ?>
                            <div class="checkbox-inline checkbox">
                                <input type="checkbox" name="is_public" id="ccx_lead_public">
                                <label for="ccx_lead_public">
                                    <?= $f['label']; ?>
                                </label>
                            </div>
                        <?php } ?>

                        <div class="checkbox-inline checkbox checkbox-primary">
                            <input type="checkbox" name="contacted_today" id="ccx_contacted_today" checked>
                            <label for="ccx_contacted_today">
                                <?= _l('lead_add_edit_contacted_today'); ?>
                            </label>
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