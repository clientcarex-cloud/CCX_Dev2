<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo form_open(admin_url('mis_reports'), ['method' => 'GET', 'id' => 'mis-report-form']); ?>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="report_id">Select Report</label>
                                            <select name="report_id" id="report_id" class="selectpicker"
                                                data-width="100%" data-live-search="true">
                                                <option value="">Select Report</option>
                                                <?php foreach ($reports as $report) { ?>
                                                    <option value="<?php echo $report['id']; ?>" <?php echo $selected_report == $report['id'] ? 'selected' : ''; ?>>
                                                        <?php echo $report['name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="staff_id">Staff</label>
                                            <select name="staff_id" id="staff_id" class="selectpicker" data-width="100%"
                                                data-live-search="true">
                                                <option value="">All Staff</option>
                                                <?php foreach ($staff_list as $s) { ?>
                                                    <option value="<?php echo $s['staffid']; ?>" <?php echo $s['staffid'] == $staff_id ? 'selected' : ''; ?>>
                                                        <?php echo $s['firstname'] . ' ' . $s['lastname']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <?php echo render_date_input('from_date', 'From Date', $this->input->get('from_date') ? $this->input->get('from_date') : _d(date('Y-m-d'))); ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php echo render_date_input('to_date', 'To Date', $this->input->get('to_date') ? $this->input->get('to_date') : _d(date('Y-m-d'))); ?>
                                    </div>
                                    <div class="col-md-4" style="margin-top: 25px;">
                                        <button type="submit" class="btn btn-info">Preview</button>

                                        <?php if ($selected_report): ?>
                                            <a href="<?php echo admin_url('mis_reports/excel?' . http_build_query($this->input->get())); ?>"
                                                class="btn btn-default" target="_blank">Download Excel</a>
                                            <a href="<?php echo admin_url('mis_reports/print_report?' . http_build_query($this->input->get())); ?>"
                                                class="btn btn-default" target="_blank">Print</a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-default" disabled>Download Excel</button>
                                            <button type="button" class="btn btn-default" disabled>Print</button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($selected_report): ?>
                                    <hr />
                                    <?php if (isset($view_type) && $view_type == 'patient_visit_overall'): ?>
                                        <?php $this->load->view('patient_visit_overall'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'bill_wise_consultation_report'): ?>
                                        <?php $this->load->view('bill_wise_consultation_report'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'test_wise_collection_report'): ?>
                                        <?php $this->load->view('test_wise_collection_report'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'doctor_wise_consultation_summary'): ?>
                                        <?php $this->load->view('doctor_wise_consultation_summary'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'lab_departments_collection_overview'): ?>
                                        <?php $this->load->view('lab_departments_collection_overview'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'departments_collection_lab_hospital'): ?>
                                        <?php $this->load->view('departments_collection_lab_hospital'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'tests_collection_patient_wise'): ?>
                                        <?php $this->load->view('tests_collection_patient_wise'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'services_collection_overview'): ?>
                                        <?php $this->load->view('services_collection_overview'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'services_collection_patient_wise'): ?>
                                        <?php $this->load->view('services_collection_patient_wise'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'refunds_overview'): ?>
                                        <?php $this->load->view('refunds_overview'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'refunds_in_detail'): ?>
                                        <?php $this->load->view('refunds_in_detail'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'general_userwise_report'): ?>
                                        <?php $this->load->view('general_userwise_report'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'business_userwise_report'): ?>
                                        <?php $this->load->view('business_userwise_report'); ?>
                                    <?php elseif (isset($view_type) && $view_type == 'transactions_userwise_report'): ?>
                                        <?php $this->load->view('transactions_userwise_report'); ?>
                                    <?php else: ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="no-margin">Report Content Here</h4>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<a href="<?php echo admin_url('mis_reports/settings'); ?>" class="btn btn-info btn-icon"
    style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <i class="fa fa-cog fa-2x"></i>
</a>
<?php init_tail(); ?>
</body>

</html>