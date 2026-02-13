<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
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
            <?php echo form_open(admin_url('dr_authorization/save_report'), ['style' => 'display: block;']); ?>
            <div class="modal-body" style="padding: 0; min-height: 500px; background: #f9fafb;">
                <input type="hidden" name="id" value="<?php echo $report->id; ?>">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped no-mtop">
                        <thead class="bg-dark text-white" style="background: #323743; color: white;">
                            <tr>
                                <th style="width: 40%;">Parameter</th>
                                <th style="width: 20%;">Value</th>
                                <th style="width: 10%;">Unit</th>
                                <th style="width: 20%;">Reference Range</th>
                                <th style="width: 10%; text-align: center;">Bold</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($params)) {
                                foreach ($params as $param) { ?>
                                    <tr>
                                        <td>
                                            <span class="bold">
                                                <?php echo $param['parameter_name']; ?>
                                            </span>
                                            <?php if ($param['is_bold']) {
                                                echo ' <span class="label label-default">B</span>';
                                            } // Indicator for param name boldness setting from master ?>
                                        </td>
                                        <td>
                                            <input type="text" name="params[<?php echo $param['id']; ?>]"
                                                value="<?php echo html_escape($param['result_value']); ?>" class="form-control"
                                                style="font-weight: bold; font-size: 14px;">
                                        </td>
                                        <td>
                                            <?php echo $param['unit']; ?>
                                        </td>
                                        <td>
                                            <?php echo $param['referral_range']; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="checkbox">
                                                <input type="checkbox" disabled <?php if ($param['is_bold']) {
                                                    echo 'checked';
                                                } ?>>
                                                <label></label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center">No parameters found for this fixed template.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer" style="background: #f4f5f7;">
                <div class="row">
                    <div class="col-md-6 text-left">
                        <button type="button" class="btn btn-info">Remarks</button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" name="action" value="save" class="btn btn-info">Save</button>
                        <?php if (isset($report->is_authorization_required) && $report->is_authorization_required == 1) { ?>
                            <button type="submit" name="action" value="send_for_authorization" class="btn btn-warning">Send
                                for Authorization</button>
                        <?php } else { ?>
                            <button type="submit" name="action" value="complete" class="btn btn-success">Mark as
                                Completed</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>