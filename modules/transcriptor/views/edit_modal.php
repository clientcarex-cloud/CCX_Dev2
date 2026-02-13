<div class="modal fade fullscreen-modal" id="report_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 90%; margin: 30px auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $report->patient_name . ' | ' . $report->age . ' | ' . $report->gender . ' | ' . $report->test_name . ' | ' . ($report->ref_doc_name ? $report->ref_doc_name : '-') . ' | ' . _d($report->test_date); ?>
                </h4>
            </div>
            <?php echo form_open(admin_url('transcriptor/save_report'), ['style' => 'display: block;']); ?>
            <div class="modal-body" style="padding: 0;">
                <input type="hidden" name="id" value="<?php echo $report->id; ?>">
                <?php echo render_textarea('content', '', $report->content, ['id' => 'modal_content', 'style' => 'width:100%; min-height:500px; border:none;'], [], '', 'tinymce'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" name="action" value="save" class="btn btn-info">Save & Close</button>
                <?php if (isset($report->is_authorization_required) && $report->is_authorization_required == 1) { ?>
                    <button type="submit" name="action" value="send_for_authorization" class="btn btn-warning">Send for
                        Authorization</button>
                <?php } else { ?>
                    <button type="submit" name="action" value="complete" class="btn btn-success">Mark as Completed</button>
                <?php } ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    // Re-init TinyMCE for the modal
    init_editor('#modal_content');
</script>