<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('blood_mgmt', '', 'create')) { ?>
                                <a href="#" class="btn btn-info pull-left" onclick="new_bag(); return false;">
                                    <?php echo _l('new_blood_bag'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <?php render_datatable([
                            _l('id'),
                            _l('blood_group'),
                            _l('donor_name'),
                            _l('donation_date'),
                            _l('expiry_date'),
                            _l('volume'),
                            _l('status'),
                        ], 'blood_bags'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bag_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('blood_mgmt/blood_bag'), ['id' => 'bag-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('blood_bag'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">

                <div class="row">
                    <div class="col-md-6">
                        <?php echo render_select('blood_group', [
                            ['id' => 'A+', 'name' => 'A+'],
                            ['id' => 'A-', 'name' => 'A-'],
                            ['id' => 'B+', 'name' => 'B+'],
                            ['id' => 'B-', 'name' => 'B-'],
                            ['id' => 'AB+', 'name' => 'AB+'],
                            ['id' => 'AB-', 'name' => 'AB-'],
                            ['id' => 'O+', 'name' => 'O+'],
                            ['id' => 'O-', 'name' => 'O-'],
                        ], ['id', 'name'], 'blood_group'); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_select('donor_id', $donors, ['id', 'name'], 'blood_donor'); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php echo render_date_input('donation_date', 'donation_date'); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_date_input('expiry_date', 'expiry_date'); ?>
                    </div>
                </div>

                <?php echo render_input('volume', 'volume'); ?>

                <?php echo render_select('status', [
                    ['id' => 1, 'name' => _l('available')],
                    ['id' => 2, 'name' => _l('issued')],
                    ['id' => 3, 'name' => _l('expired')],
                    ['id' => 4, 'name' => _l('discarded')],
                ], ['id', 'name'], 'status'); ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-info">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-blood_bags', window.location.href);
        appValidateForm($('#bag-form'), {
            blood_group: 'required',
            donor_id: 'required',
            donation_date: 'required',
            expiry_date: 'required',
            status: 'required'
        }, manage_bag);
    });

    function manage_bag(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('#bag_modal').modal('hide');
                $('.table-blood_bags').DataTable().ajax.reload();
            }
        });
        return false;
    }

    function new_bag() {
        $('#bag_modal').modal('show');
        $('#bag-form input').not('[type="hidden"]').val('');
        $('#bag-form select').selectpicker('val', '');
        $('#bag_modal select[name="status"]').selectpicker('val', 1); // Default to Available
        $('.modal-title').text('<?php echo _l('new_blood_bag'); ?>');
    }

    function edit_bag(id) {
        $.get(admin_url + 'blood_mgmt/get_bag_data/' + id, function (response) {
            response = JSON.parse(response);
            $('#bag_modal').modal('show');
            $('.modal-title').text('<?php echo _l('edit'); ?> <?php echo _l('blood_bag'); ?>');

            $('#bag_modal input[name="id"]').val(response.id);
            $('#bag_modal select[name="blood_group"]').selectpicker('val', response.blood_group);
            $('#bag_modal select[name="donor_id"]').selectpicker('val', response.donor_id);
            $('#bag_modal input[name="donation_date"]').val(response.donation_date);
            $('#bag_modal input[name="expiry_date"]').val(response.expiry_date);
            $('#bag_modal input[name="volume"]').val(response.volume);
            $('#bag_modal select[name="status"]').selectpicker('val', response.status);
        });
    }
</script>