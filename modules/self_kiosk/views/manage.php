<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" class="btn btn-info pull-left display-block"
                                onclick="new_code(); return false;">
                                <?php echo _l('new_qr_code'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <?php render_datatable(array(
                            _l('name'),
                            _l('description'),
                            _l('date_created'),
                            _l('created_by'),
                            'Feedback',
                            _l('options')
                        ), 'self-kiosk-qrcodes'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="code_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('self_kiosk/code')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('new_qr_code'); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_hidden('id', ''); ?>
                <?php echo render_input('name', _l('name')); ?>
                <?php echo render_textarea('description', _l('description')); ?>

                <hr />
                <h4>Services</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th class="text-center">View</th>
                            <th class="text-center">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $services = [
                            'update_info' => 'Update Pt. Info',
                            'visits' => 'Visits',
                            'appointments' => 'Appointments',
                            'feedback' => 'Feedback',
                            'google_review' => 'Google Map'
                        ];
                        foreach ($services as $key => $label) { ?>
                            <tr>
                                <td>
                                    <?php echo $label; ?>
                                    <?php if ($key == 'google_review') { ?>
                                        <input type="text" name="services[<?php echo $key; ?>][link]"
                                            id="link_<?php echo $key; ?>" class="form-control mtop5"
                                            placeholder="Google My Business Link">
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <div class="checkbox checkbox-primary" style="margin:0;">
                                        <input type="hidden" name="services[<?php echo $key; ?>][view]" value="0">
                                        <input type="checkbox" name="services[<?php echo $key; ?>][view]"
                                            id="view_<?php echo $key; ?>" value="1" checked>
                                        <label for="view_<?php echo $key; ?>"></label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="checkbox checkbox-primary" style="margin:0;">
                                        <input type="hidden" name="services[<?php echo $key; ?>][edit]" value="0">
                                        <input type="checkbox" name="services[<?php echo $key; ?>][edit]"
                                            id="edit_<?php echo $key; ?>" value="1" checked>
                                        <label for="edit_<?php echo $key; ?>"></label>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- QR Modal -->
<div class="modal fade" id="qr_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('qr_code'); ?></h4>
            </div>
            <div class="modal-body text-center">
                <div id="kiosk_qr_code" style="display: inline-block;"></div>
                <p class="mtop15 text-muted" id="qr_link_container"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    $(function () {
        initDataTable('.table-self-kiosk-qrcodes', window.location.href);
    });

    function new_code() {
        $('#code_modal').modal('show');
        $('#code_modal input[name="id"]').val('');
        $('#code_modal input[name="name"]').val('');
        $('#code_modal textarea[name="description"]').val('');
        $('#code_modal input[id^="link_"]').val('');
        $('#code_modal input[type="checkbox"]').prop('checked', true); // Default all checked for new
        $('#code_modal .modal-title').text('<?php echo _l('new_qr_code'); ?>');
    }

    function edit_code(id, name, description, settings) {
        $('#code_modal').modal('show');
        $('#code_modal input[name="id"]').val(id);
        $('#code_modal input[name="name"]').val(name);
        $('#code_modal textarea[name="description"]').val(description);

        // Handle services checkboxes
        var services = {};
        try {
            services = JSON.parse(settings);
        } catch (e) {
            // If settings is empty or invalid, check all by default
            $('#code_modal input[type="checkbox"]').prop('checked', true);
            return;
        }

        $('#code_modal input[type="checkbox"]').prop('checked', false);
        for (var key in services) {
            if (typeof services[key] === 'object') {
                if (services[key].view == "1") {
                    $('#view_' + key).prop('checked', true);
                }
                if (services[key].edit == "1") {
                    $('#edit_' + key).prop('checked', true);
                }
                if (services[key].link) {
                    $('#link_' + key).val(services[key].link);
                }
            } else if (services[key] == "1") {
                // Backward compatibility for old single-checkbox settings
                $('#view_' + key).prop('checked', true);
                $('#edit_' + key).prop('checked', true);
            }
        }

        $('#code_modal .modal-title').text('<?php echo _l('edit_qr_code'); ?>');
    }

    function view_qr_code(slug) {
        $('#qr_modal').modal('show');
        $('#kiosk_qr_code').html('');
        var url = "<?php echo site_url('self_kiosk/kiosk/go/'); ?>" + slug;
        new QRCode(document.getElementById("kiosk_qr_code"), {
            text: url,
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        $('#qr_link_container').html('<a href="' + url + '" target="_blank">' + url + '</a>');
    }
</script>