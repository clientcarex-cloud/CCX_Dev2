<div class="modal fade" id="member_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('Issue Privilege Card'); ?>
                </h4>
            </div>
            <?php echo form_open(admin_url('privilege_card/member')); ?>
            <div class="modal-body">
                <div class="form-group" id="card_number_group" style="display:none;">
                    <label for="card_number">
                        <?php echo _l('Card Number'); ?>
                    </label>
                    <input type="text" class="form-control" id="card_number" name="card_number" readonly>
                </div>
                <div class="form-group">
                    <label for="patient_id">
                        <?php echo _l('Patient'); ?>
                    </label>
                    <select name="patient_id" id="patient_id" class="form-control selectpicker" data-live-search="true"
                        required>
                        <option value=""></option>
                        <?php
                        $this->load->model('clients_model');
                        $clients = $this->clients_model->get();
                        foreach ($clients as $client) { ?>
                            <option value="<?php echo $client['userid']; ?>">
                                <?php echo $client['company']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="card_type_id">
                        <?php echo _l('Plan'); ?>
                    </label>
                    <select name="card_type_id" id="card_type_id" class="form-control selectpicker" required>
                        <option value=""></option>
                        <?php foreach ($types as $type) { ?>
                            <option value="<?php echo $type['id']; ?>">
                                <?php echo $type['name']; ?> (
                                <?php echo app_format_money($type['price'], get_base_currency()); ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <?php echo render_date_input('issue_date', 'Issue Date', _d(date('Y-m-d'))); ?>
                <?php echo render_date_input('expiry_date', 'Expiry Date'); ?>
                <div class="form-group">
                    <label for="status">
                        <?php echo _l('Status'); ?>
                    </label>
                    <select name="status" id="status" class="form-control selectpicker">
                        <option value="active">
                            <?php echo _l('active'); ?>
                        </option>
                        <option value="inactive">
                            <?php echo _l('inactive'); ?>
                        </option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-info">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>