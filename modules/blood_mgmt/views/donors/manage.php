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
                                <a href="#" class="btn btn-info pull-left" onclick="new_donor(); return false;">
                                    <?php echo _l('new_blood_donor'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <?php render_datatable([
                            _l('id'),
                            _l('donor_name'),
                            _l('blood_group'),
                            _l('age'),
                            _l('gender'),
                            _l('mobile'),
                            _l('last_donation_date'),
                        ], 'donors'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="donor_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('blood_mgmt/donor'), ['id' => 'donor-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('blood_donor'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <?php echo render_input('name', 'donor_name'); ?>
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
                <div class="row">
                    <div class="col-md-6">
                        <?php echo render_input('age', 'age', '', 'number'); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_select('gender', [
                            ['id' => 'Male', 'name' => 'Male'],
                            ['id' => 'Female', 'name' => 'Female'],
                            ['id' => 'Other', 'name' => 'Other'],
                        ], ['id', 'name'], 'gender'); ?>
                    </div>
                </div>
                <?php echo render_input('mobile', 'mobile'); ?>
                <?php echo render_input('email', 'email', '', 'email'); ?>
                <?php echo render_date_input('last_donation_date', 'last_donation_date'); ?>
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
        initDataTable('.table-donors', window.location.href);
        appValidateForm($('#donor-form'), {
            name: 'required',
            blood_group: 'required',
            age: 'required',
            gender: 'required',
            mobile: 'required'
        }, manage_donor);
    });

    function manage_donor(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('#donor_modal').modal('hide');
                $('.table-donors').DataTable().ajax.reload();
            }
        });
        return false;
    }

    function new_donor() {
        $('#donor_modal').modal('show');
        $('#donor_modal input[name="id"]').val('');
        $('#donor_modal input[type="text"]').val('');
        $('#donor_modal input[type="email"]').val('');
        $('#donor_modal input[type="number"]').val('');
        $('#donor_modal select').selectpicker('val', '');
        $('.modal-title').text('<?php echo _l('new_blood_donor'); ?>');
    }

    function edit_donor(id) {
        $.get(admin_url + 'blood_mgmt/get_donor_data/' + id, function (response) {
            response = JSON.parse(response);
            $('#donor_modal').modal('show');
            $('.modal-title').text('<?php echo _l('edit'); ?> <?php echo _l('blood_donor'); ?>');

            $('#donor_modal input[name="id"]').val(response.id);
            $('#donor_modal input[name="name"]').val(response.name);
            $('#donor_modal select[name="blood_group"]').selectpicker('val', response.blood_group);
            $('#donor_modal input[name="age"]').val(response.age);
            $('#donor_modal select[name="gender"]').selectpicker('val', response.gender);
            $('#donor_modal input[name="mobile"]').val(response.mobile);
            $('#donor_modal input[name="email"]').val(response.email);
            $('#donor_modal input[name="last_donation_date"]').val(response.last_donation_date);
        });
    }
</script>