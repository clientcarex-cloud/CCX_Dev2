<div class="modal fade" id="reschedule_history_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('appointment_reschedule_history'); ?></h4>
            </div>
            <div class="modal-body">
                <?php if (empty($history)) { ?>
                    <div class="alert alert-info"><?php echo _l('no_reschedule_history'); ?></div>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo _l('date'); ?></th>
                                    <th><?php echo _l('previous_slot'); ?></th>
                                    <th><?php echo _l('new_slot'); ?></th>
                                    <th><?php echo _l('changed_by'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $log) { ?>
                                    <tr>
                                        <td><?php echo _dt($log['created_at']); ?></td>
                                        <td>
                                            <?php echo _d($log['prev_date']); ?><br>
                                            <small><?php echo date('h:i A', strtotime($log['prev_start_time'])) . ' - ' . date('h:i A', strtotime($log['prev_end_time'])); ?></small>
                                        </td>
                                        <td>
                                            <?php echo _d($log['new_date']); ?><br>
                                            <small><?php echo date('h:i A', strtotime($log['new_start_time'])) . ' - ' . date('h:i A', strtotime($log['new_end_time'])); ?></small>
                                        </td>
                                        <td>
                                            <?php echo get_staff_full_name($log['rescheduled_by']); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>