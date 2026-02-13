<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string()); ?>

                        <?php $value = (isset($template) ? $template->name : ''); ?>
                        <?php echo render_input('name', 'print_template_name', $value); ?>

                        <div class="form-group">
                            <label for="type" class="control-label"><?php echo _l('print_template_type'); ?></label>
                            <select name="type" class="selectpicker" id="type" data-width="100%"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <?php foreach ($types as $type) { ?>
                                    <option value="<?php echo $type['type']; ?>" <?php if (isset($template) && $template->type == $type['type']) {
                                           echo 'selected';
                                       } ?>><?php echo (strpos($type['type'], 'print_template_type_') !== false ? _l($type['type']) : $type['type']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="is_default" id="is_default" value="1" <?php if (isset($template) && $template->is_default == 1) {
                                    echo 'checked';
                                } ?>>
                                <label for="is_default"><?php echo _l('print_template_is_default'); ?></label>
                            </div>
                        </div>

                        <p class="bold"><?php echo _l('print_template_content'); ?></p>
                        <?php $value = (isset($template) ? $template->content : ''); ?>
                        <?php echo render_textarea('content', '', $value, array(), array(), '', 'tinymce'); ?>

                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin bold">Available Tags</h4>
                        <p class="text-muted small">Click to insert into editor</p>
                        <hr class="hr-panel-heading" />

                        <div class="tags-sidebar">

                            <!-- 1. Invoice -->
                            <h5 class="bold underline">Invoice Details</h5>
                            <div class="tags-group">
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{invoice_number}')">Invoice No</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{visit_date}')">Date</span>
                                <span class="label label-default tag-item" onclick="insertTag('{doctor_name}')">Doctor
                                    Name</span>
                                <span class="label label-default tag-item" onclick="insertTag('{received_by}')">Received
                                    By</span>
                                <span class="label label-default tag-item" onclick="insertTag('{total_amount}')">Total
                                    Amount</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{Discount}')">Discount</span>
                                <span class="label label-default tag-item" onclick="insertTag('{Paid}')">Paid
                                    Amount</span>
                                <span class="label label-default tag-item" onclick="insertTag('{Balance}')">Balance
                                    Due</span>
                            </div>
                            <hr />

                            <!-- 2. Receipt -->
                            <h5 class="bold underline">Receipt Details</h5>
                            <div class="tags-group">
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{receipt_number}')">Receipt No</span>
                                <span class="label label-default tag-item" onclick="insertTag('{payment_date}')">Payment
                                    Date</span>
                                <span class="label label-default tag-item" onclick="insertTag('{payment_mode}')">Payment
                                    Mode</span>
                                <span class="label label-default tag-item" onclick="insertTag('{amount_paid}')">Amount
                                    Paid</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{TransactionID}')">Transaction ID</span>
                                <span class="label label-default tag-item" onclick="insertTag('{Note}')">Note</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{amount_in_words}')">Amount in Words</span>
                            </div>
                            <hr />

                            <!-- 3. Patient -->
                            <h5 class="bold underline">Patient Details</h5>
                            <div class="tags-group">
                                <span class="label label-default tag-item" onclick="insertTag('{patient_name}')">Full
                                    Name</span>
                                <span class="label label-default tag-item" onclick="insertTag('{mr_number}')">MR
                                    Number</span>
                                <span class="label label-default tag-item" onclick="insertTag('{uid_no}')">UID No</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{patient_title}')">Title</span>
                                <span class="label label-default tag-item" onclick="insertTag('{Age}')">Age</span>
                                <span class="label label-default tag-item" onclick="insertTag('{age_unit}')">Age
                                    Unit</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{patient_dob}')">DOB</span>
                                <span class="label label-default tag-item" onclick="insertTag('{Gender}')">Gender</span>
                                <span class="label label-default tag-item" onclick="insertTag('{PhoneNo}')">Phone
                                    No</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{patient_email}')">Email</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{patient_address}')">Address</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{attender_name}')">Attender Name</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{attender_title}')">Attender Title</span>
                            </div>
                            <hr />

                            <!-- 4. Visit/Doctor -->
                            <h5 class="bold underline">Visit / Doctor Details</h5>
                            <div class="tags-group">
                                <span class="label label-default tag-item" onclick="insertTag('{visit_id}')">Visit
                                    ID</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{primary_doctor_name}')">Primary Doctor</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{referral_doctor_name}')">Referral Doctor</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{patient_referral_lab}')">Referral Lab</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{patient_company}')">Patient Company</span>
                            </div>
                            <hr />

                            <!-- 5. Patient Items -->
                            <h5 class="bold underline">Patient Items</h5>
                            <div class="tags-group">
                                <span class="label label-default tag-item" onclick="insertTag('{items_table}')">Items
                                    Table</span>
                            </div>
                            <hr />

                            <!-- 6. Company -->
                            <h5 class="bold underline">Company Details</h5>
                            <div class="tags-group">
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{company_name}')">Name</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{company_address}')">Address</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{company_city}')">City</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{company_state}')">State</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{company_country}')">Country</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{company_country_code}')">Country Code</span>
                                <span class="label label-default tag-item" onclick="insertTag('{company_zip_code}')">Zip
                                    Code</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{company_phone}')">Phone</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{company_main_domain}')">Web Domain</span>
                                <span class="label label-default tag-item" onclick="insertTag('{logo}')">Logo</span>
                            </div>
                            <hr />

                            <!-- 7. System Details -->
                            <h5 class="bold underline">System Details</h5>
                            <div class="tags-group">
                                <span class="label label-default tag-item" onclick="insertTag('{todays_date}')">Todays
                                    Date</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{todays_date_time}')">Todays Date & Time</span>
                                <span class="label label-default tag-item"
                                    onclick="insertTag('{print_user_name}')">Print User Name</span>
                            </div>
                            <hr />

                            <!-- 8. Lab Report Tags -->
                            <h5 class="bold underline">Lab Report Tags</h5>
                            <div class="tags-group">
                                <span class="label label-default tag-item" onclick="insertTag('{report_content}')">Report Content</span>
                                <span class="label label-default tag-item" onclick="insertTag('{patient_name}')">Patient Name</span>
                                <span class="label label-default tag-item" onclick="insertTag('{age}')">Age</span>
                                <span class="label label-default tag-item" onclick="insertTag('{gender}')">Gender</span>
                                <span class="label label-default tag-item" onclick="insertTag('{test_name}')">Test Name</span>
                                <span class="label label-default tag-item" onclick="insertTag('{ref_doc_name}')">Ref. Doctor</span>
                                <span class="label label-default tag-item" onclick="insertTag('{test_date}')">Test Date</span>
                                <span class="label label-default tag-item" onclick="insertTag('{generated_at}')">Generated At</span>
                            </div>
                            <hr />

                            <!-- 9. Print Templates -->
                            <h5 class="bold underline">Print Templates Tags</h5>
                            <div class="tags-group">
                                <?php foreach ($print_templates as $pt) { ?>
                                    <span class="label label-default tag-item"
                                        onclick="insertTag('{print_template_<?php echo $pt['id']; ?>}')"><?php echo $pt['name']; ?></span>
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
        function insertTag(tag) {
            if (typeof (tinymce) != "undefined" && tinymce.activeEditor) {
                tinymce.activeEditor.execCommand('mceInsertContent', false, tag);
            } else {
                // Fallback for normal textarea if tinymce not loaded yet
                var $txt = $('textarea[name="content"]');
                var caretPos = $txt[0].selectionStart;
                var textAreaTxt = $txt.val();
                $txt.val(textAreaTxt.substring(0, caretPos) + tag + textAreaTxt.substring(caretPos));
            }
        }
    </script>
    <style>
        .tag-item {
            cursor: pointer;
            display: inline-block;
            margin-bottom: 5px;
            margin-right: 3px;
            font-size: 100%;
            transition: all 0.2s;
        }

        .tag-item:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .tags-sidebar {
            max-height: 800px;
            overflow-y: auto;
        }
    </style>
    </body>

    </html>