<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title . ' - ' . $doctor->firstname . ' ' . $doctor->lastname; ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string()); ?>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('days'); ?></th>
                                        <th><?php echo _l('start_time'); ?></th>
                                        <th><?php echo _l('end_time'); ?></th>
                                        <th><?php echo _l('slot_duration'); ?></th>
                                        <th><?php echo _l('is_available'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                    foreach ($days as $index => $day) {
                                        $day_schedule = null;
                                        foreach ($schedule as $s) {
                                            if ($s['day_of_week'] == $day) {
                                                $day_schedule = $s;
                                                break;
                                            }
                                        }

                                        $start_time = $day_schedule ? $day_schedule['start_time'] : '09:00:00';
                                        $end_time = $day_schedule ? $day_schedule['end_time'] : '17:00:00';
                                        $slot_duration = $day_schedule ? $day_schedule['slot_duration'] : 30;
                                        $is_available = $day_schedule ? $day_schedule['is_available'] : 1;
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $day; ?>
                                                <input type="hidden" name="schedule[<?php echo $index; ?>][day_of_week]"
                                                    value="<?php echo $day; ?>">
                                            </td>
                                            <td>
                                                <input type="time" name="schedule[<?php echo $index; ?>][start_time]"
                                                    class="form-control" value="<?php echo $start_time; ?>">
                                            </td>
                                            <td>
                                                <input type="time" name="schedule[<?php echo $index; ?>][end_time]"
                                                    class="form-control" value="<?php echo $end_time; ?>">
                                            </td>
                                            <td>
                                                <input type="number" name="schedule[<?php echo $index; ?>][slot_duration]"
                                                    class="form-control" value="<?php echo $slot_duration; ?>">
                                            </td>
                                            <td>
                                                <div class="checkbox checkbox-primary">
                                                    <input type="checkbox"
                                                        name="schedule[<?php echo $index; ?>][is_available]" value="1" <?php if ($is_available)
                                                               echo 'checked'; ?>>
                                                    <label></label>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>

</html>