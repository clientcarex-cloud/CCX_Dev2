<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">Edit Report</h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('dr_authorization/save_report')); ?>
                        <input type="hidden" name="id" value="<?php echo $report->id; ?>">

                        <div class="form-group">
                            <label for="content">Report Content</label>
                            <?php echo render_textarea('content', '', $report->content, ['id' => 'content'], [], '', 'tinymce'); ?>
                        </div>

                        <div class="text-right">
                            <a href="<?php echo admin_url('dr_authorization'); ?>" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info">Save</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>