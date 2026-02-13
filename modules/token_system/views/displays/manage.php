<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <!-- Create/Edit Display Form -->
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo isset($display) ? _l('edit', _l('display_name')) : _l('create_new_display'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('token_system/token_displays/index/' . (isset($display) ? $display['id'] : ''))); ?>

                        <div class="form-group">
                            <label for="name" class="control-label"><?php echo _l('display_name'); ?></label>
                            <input type="text" id="name" name="name" class="form-control" required
                                value="<?php echo isset($display) ? $display['name'] : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="passcode" class="control-label">Access Passcode (Optional)</label>
                            <div class="input-group">
                                <input type="password" id="passcode" name="passcode" class="form-control"
                                    value="<?php echo isset($display) ? $display['passcode'] : ''; ?>"
                                    autocomplete="new-password">
                                <span class="input-group-addon pointer"
                                    onclick="togglePasscodeVisibility('passcode')"><i class="fa fa-eye"></i></span>
                            </div>
                            <p class="help-block">If set, users must enter this code to view the display.</p>
                        </div>

                        <div class="form-group">
                            <label for="doctor_id" class="control-label">Doctor (Optional - For Filters)</label>
                            <select name="doctor_id" id="doctor_id" class="form-control selectpicker"
                                data-live-search="true">
                                <option value="0">All Doctors</option>
                                <?php foreach ($doctors as $doc) { ?>
                                    <option value="<?php echo $doc['staffid']; ?>" <?php echo (isset($display) && $display['doctor_id'] == $doc['staffid']) ? 'selected' : ''; ?>>
                                        <?php echo $doc['firstname'] . ' ' . $doc['lastname']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="layout" class="control-label">Display Layout</label>
                            <select name="layout" id="layout" class="form-control">
                                <option value="modern" <?php echo (isset($display) && $display['layout'] == 'modern') ? 'selected' : ''; ?>>Modern (Default)</option>
                                <option value="medical" <?php echo (isset($display) && $display['layout'] == 'medical') ? 'selected' : ''; ?>>Medical (Clean Blue)</option>
                                <option value="dark_futuristic" <?php echo (isset($display) && $display['layout'] == 'dark_futuristic') ? 'selected' : ''; ?>>Dark Futuristic (Neon)
                                </option>
                                <option value="focus" <?php echo (isset($display) && $display['layout'] == 'focus') ? 'selected' : ''; ?>>Focus (Big Numbers)</option>
                                <option value="grid" <?php echo (isset($display) && $display['layout'] == 'grid') ? 'selected' : ''; ?>>Grid (Cards)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ad_type" class="control-label"><?php echo _l('ad_type'); ?></label>
                            <select name="ad_type" id="ad_type" class="form-control">
                                <option value="none" <?php echo (isset($display) && $display['ad_type'] == 'none') ? 'selected' : ''; ?>><?php echo _l('ad_type_none'); ?></option>
                                <option value="image" <?php echo (isset($display) && $display['ad_type'] == 'image') ? 'selected' : ''; ?>><?php echo _l('ad_type_image'); ?></option>
                                <option value="youtube" <?php echo (isset($display) && $display['ad_type'] == 'youtube') ? 'selected' : ''; ?>><?php echo _l('ad_type_youtube'); ?></option>
                            </select>
                        </div>

                        <div class="form-group" id="ad_url_wrapper"
                            style="<?php echo (isset($display) && $display['ad_type'] != 'none') ? '' : 'display:none;'; ?>">
                            <label for="ad_url" class="control-label"><?php echo _l('ad_url'); ?></label>
                            <input type="text" id="ad_url" name="ad_url" class="form-control"
                                placeholder="Image URL or YouTube Video ID"
                                value="<?php echo isset($display) ? $display['ad_url'] : ''; ?>">
                            <p class="help-block">For YouTube, enter the Video ID (e.g., dQw4w9WgXcQ). For Image, enter
                                full URL.</p>
                        </div>

                        <div id="yt_options_wrapper"
                            style="<?php echo (isset($display) && $display['ad_type'] == 'youtube') ? '' : 'display:none;'; ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="yt_mute" class="control-label">Mute Video?</label>
                                        <select name="yt_mute" id="yt_mute" class="form-control">
                                            <option value="1" <?php echo (isset($display) && $display['yt_mute'] == 1) ? 'selected' : ''; ?>>Yes (Mute)</option>
                                            <option value="0" <?php echo (isset($display) && $display['yt_mute'] == 0) ? 'selected' : ''; ?>>No (Sound On)</option>
                                        </select>
                                        <p class="help-block text-warning" style="font-size:10px;">Note: Browsers
                                            blocked autoplay with sound.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="yt_loop" class="control-label">Loop Video?</label>
                                        <select name="yt_loop" id="yt_loop" class="form-control">
                                            <option value="1" <?php echo (isset($display) && $display['yt_loop'] == 1) ? 'selected' : ''; ?>>Yes (Loop)</option>
                                            <option value="0" <?php echo (isset($display) && $display['yt_loop'] == 0) ? 'selected' : ''; ?>>No (Play Once)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
                        <?php echo form_close(); ?>

                        <?php if (isset($display)) { ?>
                            <a href="<?php echo admin_url('token_system/token_displays'); ?>"
                                class="btn btn-default pull-left"><?php echo _l('cancel'); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- List -->
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('display_management'); ?></h4>
                        <hr class="hr-panel-heading" />

                        <div class="table-responsive">
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('display_name'); ?></th>
                                        <th>Doctor</th>
                                        <th>Layout</th>
                                        <th>Passcode</th>
                                        <th><?php echo _l('ad_type'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($displays as $d) { ?>
                                        <tr>
                                            <td><a href="<?php echo site_url('token_system/token_display/view/' . $d['id']); ?>"
                                                    target="_blank"><?php echo $d['name']; ?> <i
                                                        class="fa fa-external-link"></i></a></td>
                                            <td><?php echo (isset($d['doctor_id']) && $d['doctor_id'] > 0 && isset($staff_map[$d['doctor_id']])) ? $staff_map[$d['doctor_id']] : 'All Doctors'; ?>
                                            </td>
                                            <td><?php echo ucfirst($d['layout']); ?></td>
                                            <td>
                                                <?php if(!empty($d['passcode'])){ ?>
                                                    <span class="passcode-text" id="pass_<?php echo $d['id']; ?>" data-pass="<?php echo $d['passcode']; ?>">******</span>
                                                    <a href="#" onclick="toggleTablePasscode('<?php echo $d['id']; ?>'); return false;" class="text-muted"><i class="fa fa-eye"></i></a>
                                                <?php } else { echo '-'; } ?>
                                            </td>
                                            <td><?php echo ucfirst($d['ad_type']); ?></td>
                                            <td>
                                                <a href="<?php echo admin_url('token_system/token_displays/index/' . $d['id']); ?>"
                                                    class="btn btn-default btn-xs mright5"><i class="fa fa-pencil"></i></a>
                                                <a href="<?php echo admin_url('token_system/token_displays/delete/' . $d['id']); ?>"
                                                    class="btn btn-danger btn-xs _delete"><i class="fa fa-remove"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    function togglePasscodeVisibility(id) {
        var x = document.getElementById(id);
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function toggleTablePasscode(id) {
        var el = $('#pass_' + id);
        var current = el.text();
        if (current === '******') {
            el.text(el.data('pass'));
        } else {
            el.text('******');
        }
    }

    $('#ad_type').on('change', function () {
        if (this.value === 'none') {
            $('#ad_url_wrapper').hide();
            $('#yt_options_wrapper').hide();
        } else if (this.value === 'youtube') {
            $('#ad_url_wrapper').show();
            $('#yt_options_wrapper').show();
        } else {
            $('#ad_url_wrapper').show();
            $('#yt_options_wrapper').hide();
        }
    });
</script>
</body>

</html>