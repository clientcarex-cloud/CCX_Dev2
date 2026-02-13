<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" class="btn btn-info pull-left display-block" data-toggle="modal"
                                data-target="#member_modal">
                                <?php echo _l('Issue New Card'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <table class="table dt-table items" data-order-col="0" data-order-type="desc">
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo _l('ID'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('Card Number'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('Patient'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('Plan'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('Issue Date'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('Expiry Date'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('Status'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('options'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member) { ?>
                                    <tr>
                                        <td>
                                            <?php echo $member['id']; ?>
                                        </td>
                                        <td>
                                            <?php echo $member['card_number']; ?>
                                        </td>
                                        <td>
                                            <?php echo $member['patient_name']; ?>
                                        </td>
                                        <td>
                                            <?php echo $member['plan_name']; ?>
                                        </td>
                                        <td>
                                            <?php echo _d($member['issue_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo _d($member['expiry_date']); ?>
                                        </td>
                                        <td><span
                                                class="label label-<?php echo ($member['status'] == 'active' ? 'success' : 'danger'); ?>">
                                                <?php echo _l($member['status']); ?>
                                            </span></td>
                                        <td>
                                            <a href="#" class="btn btn-default btn-icon" data-toggle="modal"
                                                data-target="#member_modal" data-id="<?php echo $member['id']; ?>"
                                                data-patient_id="<?php echo $member['patient_id']; ?>"
                                                data-card_type_id="<?php echo $member['card_type_id']; ?>"
                                                data-card_number="<?php echo $member['card_number']; ?>"
                                                data-issue_date="<?php echo _d($member['issue_date']); ?>"
                                                data-expiry_date="<?php echo _d($member['expiry_date']); ?>"
                                                data-status="<?php echo $member['status']; ?>">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
                                            <a href="<?php echo admin_url('privilege_card/delete_member/' . $member['id']); ?>"
                                                class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
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
<?php $this->load->view('member_modal'); ?>
<?php init_tail(); ?>
<script>
    $(function () {
        $('#member_modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var modal = $(this);
            modal.find('.modal-title').text(id ? "<?php echo _l('Edit Privilege Card'); ?>" : "<?php echo _l('Issue Privilege Card'); ?>");
            modal.find('form').attr('action', id ? "<?php echo admin_url('privilege_card/member/'); ?>" + id : "<?php echo admin_url('privilege_card/member'); ?>");
            modal.find('#patient_id').val(button.data('patient_id')).selectpicker('refresh');
            modal.find('#card_type_id').val(button.data('card_type_id')).selectpicker('refresh');
            modal.find('#issue_date').val(button.data('issue_date'));
            modal.find('#expiry_date').val(button.data('expiry_date'));
            modal.find('#status').val(button.data('status')).selectpicker('refresh');
            if (id) {
                modal.find('#card_number_group').show();
                modal.find('#card_number').val(button.data('card_number'));
            } else {
                modal.find('#card_number_group').hide();
                modal.find('#card_number').val('');
            }
        });
    });
</script>
</body>

</html>