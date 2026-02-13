<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open($this->uri->uri_string()); ?>

                        <div class="form-group">
                            <label for="appointments_buffer_time" class="control-label">
                                <?php echo _l('appointment_buffer_time'); ?>
                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                    title="<?php echo _l('helper_appointment_buffer_time'); ?>"></i>
                            </label>
                            <input type="number" name="appointments_buffer_time" id="appointments_buffer_time"
                                class="form-control" value="<?php echo get_option('appointments_buffer_time'); ?>"
                                required min="0">
                        </div>

                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
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