<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade fullscreen-modal" id="view_report_modal" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 95%; margin: 20px auto;">
        <div class="modal-content" style="background: #f4f5f7;">
            <div class="modal-header" style="background: white; border-bottom: 1px solid #e5e5e5; padding: 15px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <div class="pull-right mright15">
                    <button class="btn btn-default btn-sm"
                        onclick="$('#view_report_modal').modal('hide'); open_report(<?php echo $report->patient_test_id; ?>);"><i
                            class="fa fa-pencil"></i> Edit</button>
                </div>
                <h4 class="modal-title" style="font-size: 16px; font-weight: 600;">
                    <?php echo $report->patient_name . ' | ' . $report->age . ' Years | ' . $report->gender . ' | ' . $report->test_name . ' | Ref Dr - ' . ($report->ref_doc_name ? $report->ref_doc_name : '-') . ' | ' . _d($report->test_date); ?>
                </h4>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div class="row no-margin">
                    <!-- Left Column: Report Preview -->
                    <div class="col-md-9"
                        style="padding: 20px; background: #333; text-align: center; min-height: 80vh; overflow-y: auto;">
                        <iframe src="<?php echo admin_url('dr_authorization/preview/' . $report->id); ?>"
                            style="width: 210mm; height: 297mm; border: none; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.5);"></iframe>
                    </div>

                    <!-- Right Column: Details & Actions -->
                    <div class="col-md-3"
                        style="padding: 20px; background: white; min-height: 80vh; border-left: 1px solid #e5e5e5;">
                        <h5 class="bold text-muted" style="text-decoration: underline;">Patient Details</h5>
                        <p class="bold font-medium no-mbot">
                            <?php echo $report->patient_name; ?>
                        </p>
                        <p class="text-muted">
                            <?php echo $report->age; ?> Years |
                            <?php echo $report->gender; ?>
                        </p>
                        <p class="text-muted">Ref. Doctor: <span class="text-dark bold">
                                <?php echo $report->ref_doc_name; ?>
                            </span></p>

                        <div class="mtop20">
                            <h5 class="bold text-muted" style="text-decoration: underline;">Payment Details</h5>
                            <?php if ($report->invoice_status == 2) { // 2 = Paid ?>
                                <p class="text-success bold">Paid</p>
                            <?php } else { ?>
                                <p class="text-danger bold">Due:
                                    <?php echo app_format_money($report->invoice_amount, ''); ?>
                                </p>
                            <?php } ?>
                        </div>

                        <div class="mtop20">
                            <h5 class="bold text-muted" style="text-decoration: underline;">Test Details</h5>
                            <p class="bold no-mbot">
                                <?php echo $report->test_name; ?>
                            </p>
                            <p class="text-muted">Reported At: <span class="text-dark bold">
                                    <?php echo _dt($report->updated_at); ?>
                                </span></p>
                        </div>

                        <div class="mtop30 ptop20"
                            style="border-top: 1px solid #eee; background: #e0f7fa; padding: 15px; border-radius: 4px; text-align: center;">
                            <h4 class="bold">Print Report</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-success btn-block">With Letterhead</button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-success btn-block">Without Letterhead</button>
                                </div>
                            </div>
                        </div>

                        <div class="mtop20 ptop20"
                            style="border-top: 1px solid #eee; background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;">
                            <h4 class="bold">Download Report</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-info btn-block">With Letterhead</button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-info btn-block">Without Letterhead</button>
                                </div>
                            </div>
                        </div>
                        <div class="mtop20">
                            <p class="text-primary bold">No. of times reviewed : 2</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>