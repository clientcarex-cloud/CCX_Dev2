<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CCX Leads — Independent New Lead Form (Modern UI)
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
        return '<label for="' . $for . '" class="control-label ccx-form-label">' . e($text) . $req . '</label>';
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
            <div class="select-placeholder form-group ccx-field-group" app-field-wrapper="status">
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
            <div class="select-placeholder form-group ccx-field-group" app-field-wrapper="source">
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
            <div class="select-placeholder form-group ccx-field-group" app-field-wrapper="assigned">
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

        case 'title':
            // Title is now merged into the Name field — skip standalone rendering
            break;

        case 'name':
            // Merged Title (prefix dropdown) + Name input
            $title_f = ccx_field('title', $fs, _l('lead_title'));
            ?>
            <div class="form-group ccx-field-group" app-field-wrapper="name">
                <?= ccx_label('name', $f['label'], $f['required']); ?>
                <div class="input-group ccx-input-combo">
                    <?php if ($title_f['active']) { ?>
                        <div class="input-group-addon ccx-addon-select">
                            <select name="title" id="title" class="selectpicker" data-width="auto"
                                data-style="btn-default ccx-prefix-btn">
                                <option value="">--</option>
                                <option value="Mr." selected>Mr.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Ms.">Ms.</option>
                                <option value="Miss">Miss</option>
                                <option value="Dr.">Dr.</option>
                                <option value="Prof.">Prof.</option>
                            </select>
                        </div>
                    <?php } ?>
                    <input type="text" id="name" name="name" class="form-control" value="" placeholder="Full Name" <?= $f['required'] ? ' required' : ''; ?>>
                </div>
            </div>
            <?php break;

        case 'phonenumber':
            // Country Code dropdown with flags + Phone Number input
            ?>
            <div class="form-group ccx-field-group" app-field-wrapper="phonenumber">
                <?= ccx_label('phonenumber', $f['label'], $f['required']); ?>
                <div class="input-group ccx-input-combo">
                    <div class="input-group-addon ccx-addon-select">
                        <select name="phone_country_code" id="phone_country_code" class="selectpicker" data-live-search="true"
                            data-width="auto" data-style="btn-default ccx-prefix-btn">
                            <option value="" data-content="🌐 Code">Code</option>
                            <option value="+93" data-content="🇦🇫 +93">+93 AF</option>
                            <option value="+355" data-content="🇦🇱 +355">+355 AL</option>
                            <option value="+213" data-content="🇩🇿 +213">+213 DZ</option>
                            <option value="+376" data-content="🇦🇩 +376">+376 AD</option>
                            <option value="+244" data-content="🇦🇴 +244">+244 AO</option>
                            <option value="+54" data-content="🇦🇷 +54">+54 AR</option>
                            <option value="+374" data-content="🇦🇲 +374">+374 AM</option>
                            <option value="+61" data-content="🇦🇺 +61">+61 AU</option>
                            <option value="+43" data-content="🇦🇹 +43">+43 AT</option>
                            <option value="+994" data-content="🇦🇿 +994">+994 AZ</option>
                            <option value="+973" data-content="🇧🇭 +973">+973 BH</option>
                            <option value="+880" data-content="🇧🇩 +880">+880 BD</option>
                            <option value="+375" data-content="🇧🇾 +375">+375 BY</option>
                            <option value="+32" data-content="🇧🇪 +32">+32 BE</option>
                            <option value="+501" data-content="🇧🇿 +501">+501 BZ</option>
                            <option value="+229" data-content="🇧🇯 +229">+229 BJ</option>
                            <option value="+975" data-content="🇧🇹 +975">+975 BT</option>
                            <option value="+591" data-content="🇧🇴 +591">+591 BO</option>
                            <option value="+387" data-content="🇧🇦 +387">+387 BA</option>
                            <option value="+267" data-content="🇧🇼 +267">+267 BW</option>
                            <option value="+55" data-content="🇧🇷 +55">+55 BR</option>
                            <option value="+673" data-content="🇧🇳 +673">+673 BN</option>
                            <option value="+359" data-content="🇧🇬 +359">+359 BG</option>
                            <option value="+226" data-content="🇧🇫 +226">+226 BF</option>
                            <option value="+257" data-content="🇧🇮 +257">+257 BI</option>
                            <option value="+855" data-content="🇰🇭 +855">+855 KH</option>
                            <option value="+237" data-content="🇨🇲 +237">+237 CM</option>
                            <option value="+1" data-content="🇺🇸 +1">+1 US/CA</option>
                            <option value="+236" data-content="🇨🇫 +236">+236 CF</option>
                            <option value="+235" data-content="🇹🇩 +235">+235 TD</option>
                            <option value="+56" data-content="🇨🇱 +56">+56 CL</option>
                            <option value="+86" data-content="🇨🇳 +86">+86 CN</option>
                            <option value="+57" data-content="🇨🇴 +57">+57 CO</option>
                            <option value="+269" data-content="🇰🇲 +269">+269 KM</option>
                            <option value="+242" data-content="🇨🇬 +242">+242 CG</option>
                            <option value="+243" data-content="🇨🇩 +243">+243 CD</option>
                            <option value="+506" data-content="🇨🇷 +506">+506 CR</option>
                            <option value="+385" data-content="🇭🇷 +385">+385 HR</option>
                            <option value="+53" data-content="🇨🇺 +53">+53 CU</option>
                            <option value="+357" data-content="🇨🇾 +357">+357 CY</option>
                            <option value="+420" data-content="🇨🇿 +420">+420 CZ</option>
                            <option value="+45" data-content="🇩🇰 +45">+45 DK</option>
                            <option value="+253" data-content="🇩🇯 +253">+253 DJ</option>
                            <option value="+593" data-content="🇪🇨 +593">+593 EC</option>
                            <option value="+20" data-content="🇪🇬 +20">+20 EG</option>
                            <option value="+503" data-content="🇸🇻 +503">+503 SV</option>
                            <option value="+240" data-content="🇬🇶 +240">+240 GQ</option>
                            <option value="+291" data-content="🇪🇷 +291">+291 ER</option>
                            <option value="+372" data-content="🇪🇪 +372">+372 EE</option>
                            <option value="+251" data-content="🇪🇹 +251">+251 ET</option>
                            <option value="+679" data-content="🇫🇯 +679">+679 FJ</option>
                            <option value="+358" data-content="🇫🇮 +358">+358 FI</option>
                            <option value="+33" data-content="🇫🇷 +33">+33 FR</option>
                            <option value="+241" data-content="🇬🇦 +241">+241 GA</option>
                            <option value="+220" data-content="🇬🇲 +220">+220 GM</option>
                            <option value="+995" data-content="🇬🇪 +995">+995 GE</option>
                            <option value="+49" data-content="🇩🇪 +49">+49 DE</option>
                            <option value="+233" data-content="🇬🇭 +233">+233 GH</option>
                            <option value="+30" data-content="🇬🇷 +30">+30 GR</option>
                            <option value="+502" data-content="🇬🇹 +502">+502 GT</option>
                            <option value="+224" data-content="🇬🇳 +224">+224 GN</option>
                            <option value="+592" data-content="🇬🇾 +592">+592 GY</option>
                            <option value="+509" data-content="🇭🇹 +509">+509 HT</option>
                            <option value="+504" data-content="🇭🇳 +504">+504 HN</option>
                            <option value="+852" data-content="🇭🇰 +852">+852 HK</option>
                            <option value="+36" data-content="🇭🇺 +36">+36 HU</option>
                            <option value="+354" data-content="🇮🇸 +354">+354 IS</option>
                            <option value="+91" data-content="🇮🇳 +91" selected>+91 IN</option>
                            <option value="+62" data-content="🇮🇩 +62">+62 ID</option>
                            <option value="+98" data-content="🇮🇷 +98">+98 IR</option>
                            <option value="+964" data-content="🇮🇶 +964">+964 IQ</option>
                            <option value="+353" data-content="🇮🇪 +353">+353 IE</option>
                            <option value="+972" data-content="🇮🇱 +972">+972 IL</option>
                            <option value="+39" data-content="🇮🇹 +39">+39 IT</option>
                            <option value="+225" data-content="🇨🇮 +225">+225 CI</option>
                            <option value="+81" data-content="🇯🇵 +81">+81 JP</option>
                            <option value="+962" data-content="🇯🇴 +962">+962 JO</option>
                            <option value="+7" data-content="🇰🇿 +7">+7 KZ</option>
                            <option value="+254" data-content="🇰🇪 +254">+254 KE</option>
                            <option value="+965" data-content="🇰🇼 +965">+965 KW</option>
                            <option value="+996" data-content="🇰🇬 +996">+996 KG</option>
                            <option value="+856" data-content="🇱🇦 +856">+856 LA</option>
                            <option value="+371" data-content="🇱🇻 +371">+371 LV</option>
                            <option value="+961" data-content="🇱🇧 +961">+961 LB</option>
                            <option value="+266" data-content="🇱🇸 +266">+266 LS</option>
                            <option value="+231" data-content="🇱🇷 +231">+231 LR</option>
                            <option value="+218" data-content="🇱🇾 +218">+218 LY</option>
                            <option value="+423" data-content="🇱🇮 +423">+423 LI</option>
                            <option value="+370" data-content="🇱🇹 +370">+370 LT</option>
                            <option value="+352" data-content="🇱🇺 +352">+352 LU</option>
                            <option value="+853" data-content="🇲🇴 +853">+853 MO</option>
                            <option value="+389" data-content="🇲🇰 +389">+389 MK</option>
                            <option value="+261" data-content="🇲🇬 +261">+261 MG</option>
                            <option value="+265" data-content="🇲🇼 +265">+265 MW</option>
                            <option value="+60" data-content="🇲🇾 +60">+60 MY</option>
                            <option value="+960" data-content="🇲🇻 +960">+960 MV</option>
                            <option value="+223" data-content="🇲🇱 +223">+223 ML</option>
                            <option value="+356" data-content="🇲🇹 +356">+356 MT</option>
                            <option value="+222" data-content="🇲🇷 +222">+222 MR</option>
                            <option value="+230" data-content="🇲🇺 +230">+230 MU</option>
                            <option value="+52" data-content="🇲🇽 +52">+52 MX</option>
                            <option value="+373" data-content="🇲🇩 +373">+373 MD</option>
                            <option value="+377" data-content="🇲🇨 +377">+377 MC</option>
                            <option value="+976" data-content="🇲🇳 +976">+976 MN</option>
                            <option value="+382" data-content="🇲🇪 +382">+382 ME</option>
                            <option value="+212" data-content="🇲🇦 +212">+212 MA</option>
                            <option value="+258" data-content="🇲🇿 +258">+258 MZ</option>
                            <option value="+95" data-content="🇲🇲 +95">+95 MM</option>
                            <option value="+264" data-content="🇳🇦 +264">+264 NA</option>
                            <option value="+977" data-content="🇳🇵 +977">+977 NP</option>
                            <option value="+31" data-content="🇳🇱 +31">+31 NL</option>
                            <option value="+64" data-content="🇳🇿 +64">+64 NZ</option>
                            <option value="+505" data-content="🇳🇮 +505">+505 NI</option>
                            <option value="+227" data-content="🇳🇪 +227">+227 NE</option>
                            <option value="+234" data-content="🇳🇬 +234">+234 NG</option>
                            <option value="+850" data-content="🇰🇵 +850">+850 KP</option>
                            <option value="+47" data-content="🇳🇴 +47">+47 NO</option>
                            <option value="+968" data-content="🇴🇲 +968">+968 OM</option>
                            <option value="+92" data-content="🇵🇰 +92">+92 PK</option>
                            <option value="+970" data-content="🇵🇸 +970">+970 PS</option>
                            <option value="+507" data-content="🇵🇦 +507">+507 PA</option>
                            <option value="+675" data-content="🇵🇬 +675">+675 PG</option>
                            <option value="+595" data-content="🇵🇾 +595">+595 PY</option>
                            <option value="+51" data-content="🇵🇪 +51">+51 PE</option>
                            <option value="+63" data-content="🇵🇭 +63">+63 PH</option>
                            <option value="+48" data-content="🇵🇱 +48">+48 PL</option>
                            <option value="+351" data-content="🇵🇹 +351">+351 PT</option>
                            <option value="+974" data-content="🇶🇦 +974">+974 QA</option>
                            <option value="+40" data-content="🇷🇴 +40">+40 RO</option>
                            <option value="+7" data-content="🇷🇺 +7">+7 RU</option>
                            <option value="+250" data-content="🇷🇼 +250">+250 RW</option>
                            <option value="+966" data-content="🇸🇦 +966">+966 SA</option>
                            <option value="+221" data-content="🇸🇳 +221">+221 SN</option>
                            <option value="+381" data-content="🇷🇸 +381">+381 RS</option>
                            <option value="+232" data-content="🇸🇱 +232">+232 SL</option>
                            <option value="+65" data-content="🇸🇬 +65">+65 SG</option>
                            <option value="+421" data-content="🇸🇰 +421">+421 SK</option>
                            <option value="+386" data-content="🇸🇮 +386">+386 SI</option>
                            <option value="+252" data-content="🇸🇴 +252">+252 SO</option>
                            <option value="+27" data-content="🇿🇦 +27">+27 ZA</option>
                            <option value="+82" data-content="🇰🇷 +82">+82 KR</option>
                            <option value="+211" data-content="🇸🇸 +211">+211 SS</option>
                            <option value="+34" data-content="🇪🇸 +34">+34 ES</option>
                            <option value="+94" data-content="🇱🇰 +94">+94 LK</option>
                            <option value="+249" data-content="🇸🇩 +249">+249 SD</option>
                            <option value="+597" data-content="🇸🇷 +597">+597 SR</option>
                            <option value="+268" data-content="🇸🇿 +268">+268 SZ</option>
                            <option value="+46" data-content="🇸🇪 +46">+46 SE</option>
                            <option value="+41" data-content="🇨🇭 +41">+41 CH</option>
                            <option value="+963" data-content="🇸🇾 +963">+963 SY</option>
                            <option value="+886" data-content="🇹🇼 +886">+886 TW</option>
                            <option value="+992" data-content="🇹🇯 +992">+992 TJ</option>
                            <option value="+255" data-content="🇹🇿 +255">+255 TZ</option>
                            <option value="+66" data-content="🇹🇭 +66">+66 TH</option>
                            <option value="+228" data-content="🇹🇬 +228">+228 TG</option>
                            <option value="+676" data-content="🇹🇴 +676">+676 TO</option>
                            <option value="+216" data-content="🇹🇳 +216">+216 TN</option>
                            <option value="+90" data-content="🇹🇷 +90">+90 TR</option>
                            <option value="+993" data-content="🇹🇲 +993">+993 TM</option>
                            <option value="+256" data-content="🇺🇬 +256">+256 UG</option>
                            <option value="+380" data-content="🇺🇦 +380">+380 UA</option>
                            <option value="+971" data-content="🇦🇪 +971">+971 AE</option>
                            <option value="+44" data-content="🇬🇧 +44">+44 UK</option>
                            <option value="+598" data-content="🇺🇾 +598">+598 UY</option>
                            <option value="+998" data-content="🇺🇿 +998">+998 UZ</option>
                            <option value="+58" data-content="🇻🇪 +58">+58 VE</option>
                            <option value="+84" data-content="🇻🇳 +84">+84 VN</option>
                            <option value="+967" data-content="🇾🇪 +967">+967 YE</option>
                            <option value="+260" data-content="🇿🇲 +260">+260 ZM</option>
                            <option value="+263" data-content="🇿🇼 +263">+263 ZW</option>
                        </select>
                    </div>
                    <input type="text" id="phonenumber" name="phonenumber" class="form-control" value="" placeholder="Phone Number"
                        <?= $f['required'] ? ' required' : ''; ?>>
                </div>
            </div>
            <?php break;

        case 'lead_value':
            ?>
            <div class="form-group ccx-field-group" app-field-wrapper="lead_value">
                <?= ccx_label('lead_value', $f['label'], $f['required']); ?>
                <div class="input-group" data-toggle="tooltip" title="<?= _l('lead_value_tooltip'); ?>">
                    <input type="number" class="form-control" name="lead_value" id="lead_value" value="" <?= $f['required'] ? ' required' : ''; ?>>
                    <div class="input-group-addon"><?= e($base_currency->symbol); ?></div>
                </div>
            </div>
            <?php break;

        case 'address':
            ?>
            <div class="form-group ccx-field-group" app-field-wrapper="address">
                <?= ccx_label('address', $f['label'], $f['required']); ?>
                <textarea id="address" name="address" class="form-control" rows="1"
                    style="height:36px;font-size:100%;resize:vertical;" <?= $f['required'] ? ' required' : ''; ?>></textarea>
            </div>
            <?php break;

        case 'country':
            $countries = get_all_countries();
            $customer_default_country = get_option('customer_default_country');
            ?>
            <div class="select-placeholder form-group ccx-field-group" app-field-wrapper="country">
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
            <div class="form-group ccx-field-group" app-field-wrapper="description">
                <?= ccx_label('description', $f['label'], $f['required']); ?>
                <textarea id="description" name="description" class="form-control" rows="3" style="resize:vertical;"
                    <?= $f['required'] ? ' required' : ''; ?>></textarea>
            </div>
            <?php break;

        case 'is_public':
            ?>
            <div class="ccx-field-group" style="padding-top:8px;">
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="is_public" id="ccx_lead_public">
                    <label for="ccx_lead_public"><?= e($f['label']); ?></label>
                </div>
            </div>
            <?php break;

        default:
            // Simple text field (email, website, city, state, zip, company)
            $icon = '';
            if ($slug === 'email')
                $icon = '<i class="fa fa-envelope-o text-muted" style="margin-right:6px;"></i>';
            if ($slug === 'website')
                $icon = '<i class="fa fa-globe text-muted" style="margin-right:6px;"></i>';
            if ($slug === 'city')
                $icon = '<i class="fa fa-map-marker text-muted" style="margin-right:6px;"></i>';
            ?>
            <div class="form-group ccx-field-group" app-field-wrapper="<?= $slug; ?>">
                <?= ccx_label($slug, $f['label'], $f['required']); ?>
                <?php if ($icon) { ?>
                    <div class="input-group">
                        <div class="input-group-addon" style="background:transparent;border-right:0;"><?= $icon; ?></div>
                        <input type="text" id="<?= $slug; ?>" name="<?= $slug; ?>" class="form-control" value="" style="border-left:0;"
                            <?= $f['required'] ? ' required' : ''; ?>>
                    </div>
                <?php } else { ?>
                    <input type="text" id="<?= $slug; ?>" name="<?= $slug; ?>" class="form-control" value="" <?= $f['required'] ? ' required' : ''; ?>>
                <?php } ?>
            </div>
            <?php break;
    }
}
?>

<!-- Modern New Lead Form Styles -->
<style>
    /* ── Modal Chrome ── */
    #ccx-lead-modal .modal-content {
        border: none;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.18);
    }

    .ccx-modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 22px 28px;
        border: none;
        position: relative;
    }

    .ccx-modal-header .modal-title {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ccx-modal-header .modal-title i {
        font-size: 18px;
        opacity: 0.85;
    }

    .ccx-modal-header .close {
        color: #fff;
        opacity: 0.8;
        text-shadow: none;
        font-size: 26px;
        position: absolute;
        right: 20px;
        top: 18px;
    }

    .ccx-modal-header .close:hover {
        opacity: 1;
    }

    /* ── Form Body ── */
    .ccx-form-body {
        padding: 24px 28px 12px 28px;
        background: #f8f9fb;
    }

    .ccx-form-body .ccx-form-label {
        font-size: 12.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 6px;
    }

    /* ── Section Cards ── */
    .ccx-section-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 18px;
        border: 1px solid #e8ecf1;
        transition: box-shadow 0.2s ease;
    }

    .ccx-section-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }

    .ccx-section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94a3b8;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ccx-section-title i {
        font-size: 14px;
        color: #667eea;
    }

    /* ── Field Groups ── */
    .ccx-field-group {
        margin-bottom: 16px;
    }

    .ccx-field-group .form-control {
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        padding: 9px 14px;
        font-size: 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        background: #fff;
    }

    .ccx-field-group .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.12);
        outline: none;
    }

    .ccx-field-group .form-control::placeholder {
        color: #cbd5e1;
    }

    /* ── Input Combos (Title+Name, Code+Phone) ── */
    .ccx-input-combo {
        display: flex;
        align-items: stretch;
        border-radius: 8px;
        overflow: visible;
    }

    .ccx-addon-select {
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
    }

    .ccx-addon-select .bootstrap-select .btn {
        border-radius: 8px 0 0 8px !important;
        border: 1.5px solid #e2e8f0 !important;
        border-right: 1px solid #f1f5f9 !important;
        background: #f8fafc !important;
        height: 100%;
        min-height: 38px;
        font-size: 14px;
        font-weight: 500;
        color: #334155;
        padding: 6px 12px;
    }

    .ccx-addon-select .bootstrap-select .btn:focus,
    .ccx-addon-select .bootstrap-select .btn:active {
        box-shadow: none !important;
        border-color: #667eea !important;
    }

    .ccx-input-combo .form-control {
        border-radius: 0 8px 8px 0 !important;
    }

    /* ── Selectpicker Tweaks ── */
    .ccx-field-group .bootstrap-select .btn {
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        font-size: 14px;
        min-height: 38px;
    }

    .ccx-field-group .bootstrap-select .btn:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.12) !important;
    }

    /* ── Call Log Section ── */
    .ccx-calllog-card {
        background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 100%);
        border: 1px solid #e0e5f0;
        border-radius: 10px;
        padding: 18px 20px;
        margin-top: 6px;
    }

    .ccx-calllog-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #7c3aed;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ccx-calllog-title i {
        font-size: 14px;
    }

    /* ── Footer ── */
    .ccx-form-footer {
        padding: 16px 28px;
        background: #fff;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .ccx-form-footer .btn {
        border-radius: 8px;
        padding: 9px 22px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.15s ease;
    }

    .ccx-btn-cancel {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .ccx-btn-cancel:hover {
        background: #e2e8f0;
        color: #475569;
    }

    .ccx-btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
        box-shadow: 0 4px 14px rgba(102, 126, 234, 0.35);
    }

    .ccx-btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.45);
        color: #fff;
    }

    .ccx-btn-save:active {
        transform: translateY(0);
    }

    /* ── Animations ── */
    @keyframes ccx-fadeSlide {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ccx-section-card {
        animation: ccx-fadeSlide 0.3s ease forwards;
    }

    .ccx-section-card:nth-child(2) {
        animation-delay: 0.05s;
    }

    .ccx-section-card:nth-child(3) {
        animation-delay: 0.1s;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .ccx-form-body {
            padding: 16px;
        }

        .ccx-section-card {
            padding: 14px;
        }

        .ccx-modal-header {
            padding: 18px 20px;
        }

        .ccx-form-footer {
            padding: 14px 20px;
        }
    }
</style>

<!-- ═══════════ MODAL HEADER ═══════════ -->
<div class="ccx-modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">
        <i class="fa fa-user-plus"></i>
        <?= _l('add_new', _l('lead_lowercase')); ?>
    </h4>
</div>

<!-- ═══════════ FORM BODY ═══════════ -->
<div class="ccx-form-body">
    <?= form_open(admin_url('ccx_leads/save_lead'), ['id' => 'ccx_new_lead_form']); ?>

    <?php
    // ==================== SECTION 1 — Top Row (Status/Source/Assigned) ====================
    $col1_slugs = ccx_col_slugs($layout, '1');
    if (!empty($col1_slugs)) { ?>
        <div class="ccx-section-card">
            <div class="ccx-section-title"><i class="fa fa-sliders"></i> Lead Classification</div>
            <div class="row">
                <?php foreach ($col1_slugs as $slug) { ?>
                    <div class="col-md-4">
                        <?php ccx_render_new_field($slug, $fs, $statuses, $sources, $members, $base_currency); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <!-- ==================== SECTION 2 — Contact Details ==================== -->
    <div class="ccx-section-card">
        <div class="ccx-section-title"><i class="fa fa-address-card-o"></i> Contact Information</div>
        <div class="row">
            <?php
            // ── Column 2 — Left ──
            $col2_slugs = ccx_col_slugs($layout, '2');
            ?>
            <div class="col-md-6">
                <?php foreach ($col2_slugs as $slug) {
                    ccx_render_new_field($slug, $fs, $statuses, $sources, $members, $base_currency);
                } ?>
                <?php
                if (ccx_col_has_custom($layout, '2')) {
                    echo render_custom_fields('leads', false);
                }
                ?>
            </div>

            <?php
            // ── Column 3 — Right ──
            $col3_slugs = ccx_col_slugs($layout, '3');
            ?>
            <div class="col-md-6">
                <?php foreach ($col3_slugs as $slug) {
                    ccx_render_new_field($slug, $fs, $statuses, $sources, $members, $base_currency);
                } ?>
                <?php
                if (ccx_col_has_custom($layout, '3') && !ccx_col_has_custom($layout, '2')) {
                    echo render_custom_fields('leads', false);
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    // If no custom fields in col 2 or col 3, render them at the bottom
    if (!ccx_col_has_custom($layout, '2') && !ccx_col_has_custom($layout, '3')) {
        $cf_html = render_custom_fields('leads', false);
        if (trim($cf_html) != '') {
            echo '<div class="ccx-section-card"><div class="ccx-section-title"><i class="fa fa-puzzle-piece"></i> Additional Fields</div><div class="row"><div class="col-md-12">' . $cf_html . '</div></div></div>';
        }
    }
    ?>

    <!-- ==================== SECTION 3 — Call Log ==================== -->
    <input type="hidden" name="add_call_log" value="1">
    <div class="ccx-calllog-card">
        <div class="ccx-calllog-title">
            <i class="fa fa-phone"></i> Call Log
        </div>
        <div class="form-group ccx-field-group">
            <label for="call_log_description" class="control-label ccx-form-label">
                Call Notes
            </label>
            <textarea id="call_log_description" name="call_log_description" class="form-control" rows="2"
                placeholder="Enter call notes..." style="resize:vertical;"></textarea>
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
                        <input type="radio" name="call_log_contacted" id="call_log_contacted_yes" value="yes" checked>
                        <label for="call_log_contacted_yes"><?= _l('lead_add_edit_contacted_this_lead'); ?></label>
                    </div>
                    <div class="radio radio-primary radio-inline">
                        <input type="radio" name="call_log_contacted" id="call_log_contacted_no" value="no">
                        <label for="call_log_contacted_no"><?= _l('lead_not_contacted'); ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= form_close(); ?>
</div>

<!-- ═══════════ FOOTER ═══════════ -->
<div class="ccx-form-footer">
    <button type="button" class="btn ccx-btn-cancel" data-dismiss="modal">
        <?= _l('close'); ?>
    </button>
    <button type="submit" form="ccx_new_lead_form" class="btn ccx-btn-save ccx-new-lead-save-btn">
        <i class="fa fa-check" style="margin-right:6px;"></i> <?= _l('submit'); ?>
    </button>
</div>