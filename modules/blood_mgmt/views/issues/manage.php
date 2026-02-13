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
                                <a href="#" class="btn btn-info pull-left" onclick="new_issue(); return false;">
                                    <?php echo _l('new_blood_issue'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <?php render_datatable([
                            _l('id'),
                            _l('patient_name'),
                            _l('doctor_name'),
                            _l('hospital'),
                            _l('issue_date'),
                            _l('amount'),
                        ], 'issues'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="issue_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('blood_mgmt/issue'), ['id' => 'issue-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('blood_issue'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">

                <?php echo render_input('patient_name', 'patient_name'); ?>

                <div class="row">
                    <div class="col-md-6">
                        <?php echo render_input('doctor_name', 'doctor_name'); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_input('hospital', 'hospital'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bag_ids">
                        <?php echo _l('bag_ids'); ?>
                    </label>
                    <select name="bag_ids[]" id="bag_ids" class="selectpicker" multiple data-width="100%"
                        data-none-selected-text="<?php echo _l('dropdown_non_selected_text'); ?>"
                        data-live-search="true">
                        <?php foreach ($available_bags as $bag) { ?>
                            <option value="<?php echo $bag['id']; ?>">
                                <?php echo $bag['blood_group'] . ' - ' . $bag['volume'] . ' (' . _l('expiry_date') . ': ' . _d($bag['expiry_date']) . ')'; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <?php echo render_datetime_input('issue_date', 'issue_date'); ?>
                <?php echo render_input('amount', 'amount', '', 'number'); ?>
                <?php echo render_textarea('remarks', 'remarks'); ?>

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
        initDataTable('.table-issues', window.location.href);
        appValidateForm($('#issue-form'), {
            patient_name: 'required',
            'bag_ids[]': 'required',
            issue_date: 'required'
        }, manage_issue);
    });

    function manage_issue(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('#issue_modal').modal('hide');
                $('.table-issues').DataTable().ajax.reload();
            }
        });
        return false;
    }

    function new_issue() {
        $('#issue_modal').modal('show');
        $('#issue-form input').not('[type="hidden"]').val('');
        $('#issue-form textarea').val('');
        $('#issue-form select').selectpicker('val', '');
        $('.modal-title').text('<?php echo _l('new_blood_issue'); ?>');
    }
</script>