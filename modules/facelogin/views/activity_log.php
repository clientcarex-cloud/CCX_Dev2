<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <h4 class="no-margin"><?php echo $title; ?></h4>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('facelogin/activity_log'), ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo render_select('staff_id', $staff_members, ['staffid', ['firstname', 'lastname']], 'staff', $this->input->get('staff_id'), ['data-none-selected-text' => _l('all_staff_members')]); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('from', 'from_date', $this->input->get('from')); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('to', 'to_date', $this->input->get('to')); ?>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info mtop25"><?php echo _l('filter'); ?></button>
                                <a href="<?php echo admin_url('facelogin/activity_log'); ?>"
                                    class="btn btn-default mtop25"><?php echo _l('reset'); ?></a>
                            </div>
                        </div>
                        <?php echo form_close(); ?>

                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="1" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('staff'); ?></th>
                                        <th><?php echo _l('date'); ?></th>
                                        <th>Active Time</th>
                                        <th>Idle Time</th>
                                        <th>Total Tracked</th>
                                        <th>Last Heartbeat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log) {
                                        $total = $log['active_seconds'] + $log['idle_seconds'];
                                        ?>
                                        <tr>
                                            <td><?php echo $log['staff_name']; ?></td>
                                            <td><?php echo _d($log['date']); ?></td>
                                            <td><?php echo gmdate("H:i:s", $log['active_seconds']); ?></td>
                                            <td><?php echo gmdate("H:i:s", $log['idle_seconds']); ?></td>
                                            <td><?php echo gmdate("H:i:s", $total); ?></td>
                                            <td><?php echo _dt($log['last_heartbeat']); ?></td>
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