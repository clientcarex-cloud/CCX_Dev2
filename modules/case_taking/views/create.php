<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="<?php echo base_url('assets/plugins/tagsinput/css/jquery.tagit.css'); ?>" rel="stylesheet"
    type="text/css" />
<link href="<?php echo base_url('assets/plugins/tagsinput/css/tagit.ui-zendesk.css'); ?>" rel="stylesheet"
    type="text/css" />
<style>
    ul.tagit {
        border-radius: 4px;
        padding: 5px 12px;
        margin-bottom: 15px;
        border: 1px solid #bfcbd9;
        /* Matching typical admin theme border */
        background-color: #fff;
        min-height: 80px;
        /* Make it look like a textarea */
    }

    ul.tagit li.tagit-choice {
        background-color: #e8ecf1;
        border-color: #dce1ef;
        color: #333;
        padding: 3px 8px;
        border-radius: 3px;
    }

    ul.tagit li.tagit-new {
        padding: 3px 5px;
    }

    .quick-add-wrapper {
        position: relative;
        margin-bottom: 5px;
    }

    .quick-add-input {
        width: 100%;
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #fdfdfd;
    }

    .ui-autocomplete {
        z-index: 99999 !important;
        max-height: 200px;
        overflow-y: auto;
    }
</style>
<?php
$read_only = isset($read_only) && $read_only === true;
?>
<?php if ($read_only) { ?>
    <style>
        /* Force disable TinyMCE editors */
        .tox-tinymce,
        .mce-container,
        .tinymce-wrap {
            pointer-events: none !important;
            opacity: 0.6 !important;
            background-color: #f9f9f9 !important;
        }





        /* Disable radio inputs explicitly */
        input[type="radio"],
        input[type="checkbox"] {
            pointer-events: none !important;
        }
    </style>
<?php } ?>
<?php if ($this->input->get('popup')) { ?>
    <style>
        #header,
        aside,
        #setup-menu-wrapper,
        .admin-logo,
        .navbar-header,
        .page-footer {
            display: none !important;
        }

        #wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        .content {
            padding: 15px !important;
            margin: 0 !important;
        }

        /* Hide top back button if existing */
        .btn-default.btn-xs.mright5 {
            display: none !important;
        }
    </style>
<?php } ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <a href="<?php echo admin_url('case_taking'); ?>" class="btn btn-default btn-xs mright5">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <?php echo $title; ?>
                            <span class="label label-warning pull-right"
                                style="font-size: 14px; padding: 5px 10px; margin-top: -2px;">
                                Consultation Time: <span id="consultation_timer">00:00:00</span>
                            </span>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php
                        $action = admin_url('case_taking/save');
                        if (isset($case_taking)) {
                            $action = admin_url('case_taking/update/' . $case_taking->id);
                        }
                        ?>

                        <?php echo form_open($action, ['id' => 'case_taking_form']); ?>
                        <!-- Hidden Fields -->
                        <input type="hidden" name="patient_id"
                            value="<?php echo (isset($case_taking) ? $case_taking->patient_id : $consultation->patient_id); ?>">
                        <input type="hidden" name="visit_id"
                            value="<?php echo (isset($case_taking) ? $case_taking->visit_id : (isset($visit) ? $visit->id : '')); ?>">
                        <input type="hidden" name="consultation_id" value="<?php echo $consultation_id; ?>">
                        <input type="hidden" name="consultation_duration"
                            value="<?php echo (isset($case_taking) ? $case_taking->consultation_duration : 0); ?>">


                        <!-- Vitals -->
                        <?php
                        $show_vitals = (isset($input_settings['case_taking_show_vitals']) && $input_settings['case_taking_show_vitals'] != '0');
                        if ($show_vitals) { ?>
                            <div class="row mtop15">
                                <div class="col-md-2">
                                    <?php $vitals_mandatory = (isset($input_settings['vitals_mandatory']) && $input_settings['vitals_mandatory'] == '1'); ?>
                                    <label>Vitals
                                        <?php echo $vitals_mandatory ? '<span class="text-danger">*</span>' : ''; ?></label>
                                </div>
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>BP (mmHg)</label>
                                                <div class="input-group">
                                                    <input type="text" name="bp_sys" class="form-control" placeholder="120"
                                                        value="<?php echo (isset($case_taking) ? $case_taking->bp_sys : ''); ?>"
                                                        <?php echo $vitals_mandatory ? 'required' : ''; ?>>
                                                    <span class="input-group-addon">/</span>
                                                    <input type="text" name="bp_dia" class="form-control" placeholder="80"
                                                        value="<?php echo (isset($case_taking) ? $case_taking->bp_dia : ''); ?>"
                                                        <?php echo $vitals_mandatory ? 'required' : ''; ?>>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <?php echo render_input('pulse', 'Pulse (bpm)', (isset($case_taking) ? $case_taking->pulse : ''), 'text', $vitals_mandatory ? ['required' => true] : []); ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?php echo render_input('height', 'Height (cm)', (isset($case_taking) ? $case_taking->height : ''), 'number', array_merge(['onchange' => 'calculateBMI()'], $vitals_mandatory ? ['required' => true] : [])); ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?php echo render_input('weight', 'Weight (kg)', (isset($case_taking) ? $case_taking->weight : ''), 'number', array_merge(['onchange' => 'calculateBMI()'], $vitals_mandatory ? ['required' => true] : [])); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php echo render_input('temperature', 'Temp (F)', (isset($case_taking) ? $case_taking->temperature : ''), 'text', $vitals_mandatory ? ['required' => true] : []); ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?php echo render_input('bmi', 'BMI (Kg/m2)', (isset($case_taking) ? $case_taking->bmi : ''), 'text', ['readonly' => true]); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php echo render_input('spo2', 'SPO2 %', (isset($case_taking) ? $case_taking->spo2 : ''), 'text', $vitals_mandatory ? ['required' => true] : []); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Dynamic Custom Fields (Complaints, Diagnosis, Advice, History, Allergies + Custom) -->
                        <div class="row mtop15">
                            <?php foreach ($custom_fields as $field) {
                                if ($field['is_active'] == 0)
                                    continue;

                                $slug = $field['slug'];
                                $label = $field['label'];
                                $type = $field['type'];
                                $mandatory = ($field['mandatory'] == 1);

                                // Value Retrieval
                                $value = '';
                                if ($field['is_system'] == 1) {
                                    $value = isset($case_taking) ? $case_taking->$slug : '';
                                } else {
                                    // For custom fields, data is inside custom_data array
                                    // The model decodes custom_data into an array on get_case_taking
                                    $custom_data = (isset($case_taking) && isset($case_taking->custom_data)) ? $case_taking->custom_data : [];
                                    $value = isset($custom_data[$slug]) ? $custom_data[$slug] : '';
                                }
                                ?>
                                <div class="col-md-6">
                                    <label class="control-label"><?php echo $label; ?>
                                        <?php echo $mandatory ? '<span class="text-danger">*</span>' : ''; ?></label>
                                    <?php
                                    if ($type == 'qas' && isset($qa_questions[$slug])) {
                                        $saved_answers = [];
                                        if (!empty($value)) {
                                            $decoded = json_decode($value, true);
                                            if (is_array($decoded)) {
                                                foreach ($decoded as $item) {
                                                    if (isset($item['q']))
                                                        $saved_answers[$item['q']] = $item['a'];
                                                }
                                            }
                                        }

                                        echo '<div style="padding:10px; background:#f9f9f9; border:1px solid #e5e5e5; border-radius:4px;">';

                                        if (isset($qa_questions[$slug]) && is_array($qa_questions[$slug])) {
                                            foreach ($qa_questions[$slug] as $idx => $q_item) {
                                                $q_text = is_array($q_item) ? $q_item['text'] : $q_item;
                                                $q_req = (is_array($q_item) && isset($q_item['required']) && $q_item['required'] == '1');
                                                $q_type = (is_array($q_item) && isset($q_item['type'])) ? $q_item['type'] : 'text';

                                                $a_val = isset($saved_answers[$q_text]) ? $saved_answers[$q_text] : '';

                                                echo '<div class="form-group mbottom10">';
                                                echo '<label class="small">' . $q_text . ($q_req ? ' <span class="text-danger">*</span>' : '') . '</label>';
                                                echo '<input type="hidden" name="qa_input[' . $slug . '][' . $idx . '][q]" value="' . html_escape($q_text) . '">';

                                                if ($q_type == 'checkbox') {
                                                    echo '<div class="radio radio-primary radio-inline" style="margin-left: 25px;">';
                                                    echo '<input type="radio" name="qa_input[' . $slug . '][' . $idx . '][a]" id="' . $slug . '_yes_' . $idx . '" value="Yes" ' . ($a_val == 'Yes' ? 'checked' : '') . ' ' . ($q_req ? 'required' : '') . '>';
                                                    echo '<label for="' . $slug . '_yes_' . $idx . '">Yes</label>';
                                                    echo '</div>';
                                                    echo '<div class="radio radio-primary radio-inline">';
                                                    echo '<input type="radio" name="qa_input[' . $slug . '][' . $idx . '][a]" id="' . $slug . '_no_' . $idx . '" value="No" ' . ($a_val == 'No' ? 'checked' : '') . ' ' . ($q_req ? 'required' : '') . '>';
                                                    echo '<label for="' . $slug . '_no_' . $idx . '">No</label>';
                                                    echo '</div>';
                                                } else {
                                                    echo '<input type="text" name="qa_input[' . $slug . '][' . $idx . '][a]" class="form-control input-sm" value="' . $a_val . '" ' . ($q_req ? 'required' : '') . '>';
                                                }
                                                echo '</div>';
                                            }
                                        }
                                        echo '</div>';
                                    } else {
                                        // Smart Box or Editor
                                        // Name must be the slug.
                                        // If System field: name=slug
                                        // If Custom field: name=slug (Controller separates them based on is_system flag in get_custom_fields check? No, controller loops POST params? 
                                        // Wait, Model logic:
                                        // custom_fields = get_custom_fields(); foreach(fields) check if POST has slug.
                                        // So using `name="$slug"` works perfectly for both!
                                
                                        $attrs = ($type == 'smart_box') ? ['rows' => 5, 'class' => 'smart-box-input'] : ['class' => 'tinymce'];
                                        if ($mandatory && $type == 'smart_box')
                                            $attrs['required'] = true;

                                        echo render_textarea($slug, '', $value, $attrs);
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>













                        <hr />
                        <div class="row">
                            <div class="col-md-12">
                                <?php if (!$read_only) { ?>
                                    <button type="submit" name="save_type" value="completed"
                                        class="btn btn-success pull-right mleft5">Save & Mark Completed</button>
                                    <?php
                                    $is_disabled = (isset($is_completed) && $is_completed) ? 'disabled' : '';
                                    ?>
                                    <button type="submit" name="save_type" value="draft" class="btn btn-warning pull-right"
                                        formnovalidate <?php echo $is_disabled; ?>>Save as
                                        Draft</button>
                                <?php } else { ?>
                                    <a href="<?php echo admin_url('case_taking'); ?>"
                                        class="btn btn-default pull-right">Back to List</a>
                                <?php } ?>
                            </div>
                        </div>

                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        <?php if ($read_only) { ?>
            // 1. Disable Standard Inputs
            $('input, select, textarea').prop('disabled', true);
            $('input[type="radio"]').prop('disabled', true);
            $('input[type="checkbox"]').prop('disabled', true);

            // 2. Disable Selectpickers
            $('.selectpicker').prop('disabled', true);
            $('.selectpicker').addClass('disabled');
            setTimeout(function () {
                $('.selectpicker').selectpicker('refresh');
            }, 100);

            // 3. Disable TinyMCE Editors
            if (typeof tinymce !== 'undefined') {
                // Method to disable an editor
                function disableEditor(editor) {
                    if (editor) {
                        try {
                            editor.mode.set("readonly");
                            $(editor.getContainer()).find('.tox-editor-header').css('pointer-events', 'none').css('opacity', '0.6');
                        } catch (e) { console.log('TinyMCE disable error', e); }
                    }
                }

                // Loop existing
                for (var i = 0; i < tinymce.editors.length; i++) {
                    disableEditor(tinymce.editors[i]);
                }

                // Listen for any new inits (rare here but safe)
                tinymce.on('init', function (e) {
                    disableEditor(e.editor);
                });

                // Backup timeout
                setTimeout(function () {
                    for (var i = 0; i < tinymce.editors.length; i++) {
                        disableEditor(tinymce.editors[i]);
                    }
                }, 1000);
                setTimeout(function () {
                    for (var i = 0; i < tinymce.editors.length; i++) {
                        disableEditor(tinymce.editors[i]);
                    }
                }, 3000);
            }



            // 5. Disable specific QA radio wrappers if any
            $('.radio.radio-primary').css('pointer-events', 'none');

            // 6. Remove dynamic rows UI
            $('.ghost-row').remove();
            $('.remove_row').remove();
        <?php } ?>
    });


    // Run calculation on load if values exist
    $(document).ready(function () {


        // Initialize ghost row check listener
        initGhostRow();

        // Submit Validation
        $('#case_taking_form').on('submit', function (e) {
        });

        // Capture clicked button
        $('form button[type=submit]').click(function () {
            $('button[type=submit]', $(this).parents('form')).removeAttr('clicked');
            $(this).attr('clicked', 'true');
        });
    });

    function initGhostRow() {
        // Unbind previous to avoid duplicates if re-init
        $('.table-main-case_taking-items tbody').off('input change keydown', 'input, select');

        // Bind to table body inputs
        $('.table-main-case_taking-items tbody').on('input change keydown', 'input, select', function (e) {
            var $tr = $(this).closest('tr');

            // Check if it's the last row
            if ($tr.is(':last-child')) {
                // Check if any input in this row has a value
                var hasData = false;
                $tr.find('input, select').each(function () {
                    if ($(this).val() !== '' && $(this).val() !== null) {
                        hasData = true;
                        return false; // break
                    }
                });

                // Also specifically check for user intent via Tabbing on last fields or Enter
                // If typing in the last row, we generally want a new row ready
                // Simple logic: If last row is dirty, add new row.
                if (hasData) {
                    add_item_row();
                }
            }
        });
    }


    // Dose Options
    var dose_opts = '<option value=""></option>';
    <?php foreach ($medicine_doses as $dose) { ?>
        dose_opts += '<option value="<?php echo $dose['name']; ?>"><?php echo $dose['name']; ?></option>';
    <?php } ?>
    html += '<td><select name="items[' + item_row + '][dose]" class="form-control selectpicker" data-live-search="true" data-container="body">' + dose_opts + '</select></td>';

    // When Options
    var when_opts = '<option value=""></option>';
    <?php foreach ($medicine_whens as $when) { ?>
        when_opts += '<option value="<?php echo $when['name']; ?>"><?php echo $when['name']; ?></option>';
    <?php } ?>
    html += '<td><select name="items[' + item_row + '][when_f]" class="form-control selectpicker" data-live-search="true" data-container="body">' + when_opts + '</select></td>';

    // Frequency Options
    var freq_opts = '<option value=""></option>';
    <?php foreach ($medicine_frequencies as $freq) { ?>
        freq_opts += '<option value="<?php echo $freq['name']; ?>"><?php echo $freq['name']; ?></option>';
    <?php } ?>
    html += '<td><select name="items[' + item_row + '][frequency]" class="form-control selectpicker" data-live-search="true" data-container="body">' + freq_opts + '</select></td>';

    // Duration Options
    var dur_opts = '<option value=""></option>';
    <?php foreach ($medicine_durations as $duration) { ?>
        dur_opts += '<option value="<?php echo $duration['name']; ?>"><?php echo $duration['name']; ?></option>';
    <?php } ?>
    html += '<td><select name="items[' + item_row + '][duration]" class="form-control selectpicker" data-live-search="true" data-container="body">' + dur_opts + '</select></td>';

    html += '<td><input type="text" name="items[' + item_row + '][instruction]" class="form-control"></td>';
    html += '<td><button type="button" class="btn btn-danger btn-icon remove_row"><i class="fa fa-remove"></i></button></td>';
    html += '</tr>';

    $('.table-main-case_taking-items tbody').append(html);

    // Re-init selectpicker for the new row
    $('.table-main-case_taking-items tbody tr:last-child .selectpicker').selectpicker('refresh');

    item_row++;
    }

    $('body').on('click', '.remove_row', function () {
        var $tr = $(this).closest('tr');
        // Prevent removing the last row if it's the only one, or ensure we always have one
        if ($('.table-main-case_taking-items tbody tr').length > 1) {
            $tr.remove();
        } else {
            // If last row, just clear it
            $tr.find('input').val('');
            $tr.find('select').val('Before Food');
        }
    });

    function calculateBMI() {
        var height = $('input[name="height"]').val();
        var weight = $('input[name="weight"]').val();

        if (height > 0 && weight > 0) {
            var height_m = height / 100;
            var bmi = weight / (height_m * height_m);
            $('input[name="bmi"]').val(bmi.toFixed(2));
        }
    }



    // --- Consultation Timer Logic ---
    var seconds = parseInt($('input[name="consultation_duration"]').val()) || 0;

    function updateTimerDisplay(s) {
        var h = Math.floor(s / 3600);
        var m = Math.floor((s % 3600) / 60);
        var sec = s % 60;
        var timeString =
            String(h).padStart(2, '0') + ':' +
            String(m).padStart(2, '0') + ':' +
            String(sec).padStart(2, '0');
        $('#consultation_timer').text(timeString);
    }

    // Initialize display
    updateTimerDisplay(seconds);

    <?php if (!$read_only) { ?>
        setInterval(function () {
            seconds++;
            updateTimerDisplay(seconds);
            $('input[name="consultation_duration"]').val(seconds);
        }, 1000);

        // --- Autosave Logic ---
        <?php if (get_option('case_taking_enable_autosave') == '1') { ?>
            var is_autosaving = false;
            var current_case_taking_id = '<?php echo isset($case_taking) ? $case_taking->id : ""; ?>';

            var autosave_interval = setInterval(function () {
                if (is_autosaving) return;

                // Collect Form Data
                var $form = $('#case_taking_form');
                if ($form.length === 0) return;

                var formData = $form.serialize();

                // FORCE APPEND ID if we have one (Critical for Autosave)
                if (current_case_taking_id) {
                    formData += '&id=' + current_case_taking_id;
                }

                is_autosaving = true;
                $.ajax({
                    url: '<?php echo admin_url("case_taking/autosave"); ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        // Update CSRF Token for next request (Critical)
                        if (response.csrf_token_name && response.csrf_hash) {
                            // Update all inputs with this name (there might be one in form and maybe others)
                            $('input[name="' + response.csrf_token_name + '"]').val(response.csrf_hash);

                            // Perfex Global CSRF Data Update
                            if (typeof csrfData !== 'undefined') {
                                csrfData[response.csrf_token_name] = response.csrf_hash;
                            }
                        }

                        if (response.success) {
                            if (response.action == 'create') {
                                // Update Global ID - CRITICAL for preventing duplicates
                                current_case_taking_id = response.id;

                                // Transition to Edit Mode
                                // Transition to Edit Mode
                                if ($form.find('input[name="id"]').length == 0) {
                                    $form.prepend('<input type="hidden" name="id" value="' + response.id + '">');
                                } else {
                                    $form.find('input[name="id"]').val(response.id);
                                }

                                // Update URL silently
                                var newUrl = '<?php echo admin_url("case_taking/edit/"); ?>' + response.id;
                                window.history.pushState({ path: newUrl }, '', newUrl);

                                // Update Form Action
                                $form.attr('action', '<?php echo admin_url("case_taking/edit/"); ?>' + response.id);
                            }
                            console.log('Autosaved.');
                        }
                    },
                    error: function (xhr) {
                        // If 403 or Error, stop autosave to prevent spamming
                        if (xhr.status == 403) {
                            console.error('Autosave failed: CSRF Mismatch. Stopping autosave.');
                            clearInterval(autosave_interval);
                        }
                    },
                    complete: function () {
                        is_autosaving = false;
                    }
                });

            }, 10000);
        <?php } ?>
    <?php } ?>
</script>
<style>
    .smart-box-container {
        border: 1px solid #bfcbd9;
        background-color: #fff;
        border-radius: 4px;
        padding: 5px;
        min-height: 100px;
        /* ~4 lines height */
        cursor: text;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .smart-box-container:focus-within {
        border-color: #03a9f4;
        /* Highlight on focus */
        box-shadow: 0 0 0 1px #03a9f4;
    }

    .tag-item {
        display: inline-flex;
        align-items: center;
        background-color: #fcf8e3;
        /* Yellow-ish */
        color: #8a6d3b;
        border: 1px solid #faebcc;
        padding: 4px 8px;
        margin: 2px 4px 2px 0;
        border-radius: 3px;
        font-size: 13px;
        height: 28px;
    }

    .tag-remove {
        cursor: pointer;
        margin-left: 6px;
        color: #666;
        font-weight: bold;
        font-size: 14px;
        line-height: 1;
    }

    .tag-remove:hover {
        color: #a94442;
    }

    .smart-input-wrapper {
        flex: 1;
        min-width: 150px;
        margin: 2px 0;
    }

    /* Remove default input styles to blend in */
    .smart-box-input-field {
        width: 100%;
        border: none;
        outline: none;
        height: 28px;
        padding: 0 5px;
        background: transparent;
        font-size: 14px;
        color: #555;
    }
</style>
<script>
    var master_data = <?php echo json_encode($master_data); ?>;

    $(function () {
        // --- Smart Box (Combined Tags + Input) Integration ---
        $('.smart-box-input').each(function () {
            var textarea = $(this);
            var fieldName = textarea.attr('name');
            if (!fieldName) return;

            var availableTags = [];
            if (master_data[fieldName]) {
                availableTags = master_data[fieldName].map(function (item) { return item.name; });
            }

            // Hide textarea
            textarea.hide();

            // Create UI Structure
            // Container acts as the visible "box"
            var container = $('<div class="smart-box-container"></div>').insertAfter(textarea);

            // Input Wrapper & Input
            var inputWrapper = $('<div class="smart-input-wrapper"></div>').appendTo(container);
            var input = $('<input type="text" class="smart-box-input-field" placeholder="Type to add ' + fieldName + '... (Press Enter)">').appendTo(inputWrapper);

            // Focus input when clicking container
            container.on('click', function (e) {
                if (e.target === container[0] || e.target === inputWrapper[0]) {
                    input.focus();
                }
            });

            // Load existing tags
            var existing = textarea.val().split(',').filter(v => v.trim() !== '');
            $.each(existing, function (i, val) {
                addTag(val.trim());
            });

            // Autocomplete
            input.autocomplete({
                source: availableTags,
                select: function (event, ui) {
                    addTag(ui.item.value);
                    $(this).val('');
                    return false;
                }
            });

            // Handle Enter Key
            input.on('keypress', function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    var val = $(this).val().trim();
                    if (val) {
                        addTag(val);
                        $(this).val('');
                        input.autocomplete("close");
                    }
                }
            });

            function addTag(text) {
                // Check duplicate in UI
                var exists = false;
                container.find('.tag-item').each(function () {
                    if ($(this).data('val').toLowerCase() === text.toLowerCase()) exists = true;
                });
                if (exists) return;

                var tag = $('<span class="tag-item" data-val="' + text + '">' + text + ' <span class="tag-remove">&times;</span></span>');
                // Insert before input wrapper
                tag.insertBefore(inputWrapper);

                tag.find('.tag-remove').click(function () {
                    tag.remove();
                    syncTextarea();
                });

                syncTextarea();
            }

            function syncTextarea() {
                var tags = [];
                container.find('.tag-item').each(function () {
                    tags.push($(this).data('val'));
                });
                textarea.val(tags.join(', '));
            }
        });

        // --- Smart Editor (TinyMCE) Quick Add ---
        // ... (keep existing logic) ...
        $('.tinymce').each(function () {
            var textarea = $(this);
            var fieldName = textarea.attr('name');
            var id = textarea.attr('id');

            if (!fieldName || !master_data[fieldName]) return;

            var availableTags = master_data[fieldName].map(function (item) { return item.name; });

            var wrapper = $('<div class="quick-add-wrapper"></div>').insertBefore(textarea);
            var input = $('<input type="text" class="quick-add-input form-control" placeholder="Type to add ' + fieldName + '..." />').appendTo(wrapper);

            input.autocomplete({
                source: availableTags,
                select: function (event, ui) {
                    var content = ui.item.value;
                    if (typeof tinymce !== 'undefined' && tinymce.get(id)) {
                        tinymce.get(id).insertContent(content + ', ');
                    } else {
                        var cur = textarea.val();
                        textarea.val(cur + (cur ? ', ' : '') + content);
                    }
                    $(this).val('');
                    return false;
                }
            });
        });
    });
</script>