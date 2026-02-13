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

                        <?php echo form_open(admin_url('patients/save_settings')); ?>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#visit_form_settings" aria-controls="visit_form_settings" role="tab"
                                            data-toggle="tab">
                                            Add New Visit Form
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#visits_tweaks" aria-controls="visits_tweaks" role="tab"
                                            data-toggle="tab">
                                            Visits Tweaks
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#billing_rules" aria-controls="billing_rules" role="tab"
                                            data-toggle="tab">
                                            Billing Rules
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#visit_table_filters" aria-controls="visit_table_filters" role="tab"
                                            data-toggle="tab">
                                            Visit Table Filters
                                        </a>
                                    </li>
                                    <!-- Tab Removed -->

                                    <li role="presentation">
                                        <a href="#item_group_settings" aria-controls="item_group_settings" role="tab"
                                            data-toggle="tab">
                                            Item Group
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content mtop15">
                            <div role="tabpanel" class="tab-pane active" id="visit_form_settings">
                                <p class="text-muted">Turn ON to make the field <b>Mandatory</b> (Required).</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- UID No -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_uid" id="patients_req_uid"
                                                    <?php echo (get_option('patients_req_uid') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_uid">UID No</label>
                                            </div>
                                        </div>

                                        <!-- Age / DOB -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_age" id="patients_req_age"
                                                    <?php echo (get_option('patients_req_age') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_age">Age / DOB</label>
                                            </div>
                                        </div>

                                        <!-- Referral Doctor -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_referral_doctor"
                                                    id="patients_req_referral_doctor" <?php echo (get_option('patients_req_referral_doctor') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_referral_doctor">Referral Doctor</label>
                                            </div>
                                        </div>

                                        <!-- Attender Name -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_attender_name"
                                                    id="patients_req_attender_name" <?php echo (get_option('patients_req_attender_name') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_attender_name">Attender Name</label>
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_email" id="patients_req_email"
                                                    <?php echo (get_option('patients_req_email') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_email">Email</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <!-- Primary Doctor -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_primary_doctor"
                                                    id="patients_req_primary_doctor" <?php echo (get_option('patients_req_primary_doctor') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_primary_doctor">Primary Doctor</label>
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_address"
                                                    id="patients_req_address" <?php echo (get_option('patients_req_address') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_address">Address</label>
                                            </div>
                                        </div>

                                        <!-- Referral Lab -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_referral_lab"
                                                    id="patients_req_referral_lab" <?php echo (get_option('patients_req_referral_lab') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_referral_lab">Referral Lab</label>
                                            </div>
                                        </div>

                                        <!-- Company -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_company"
                                                    id="patients_req_company" <?php echo (get_option('patients_req_company') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_company">Company</label>
                                            </div>
                                        </div>

                                        <!-- Prescription -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_req_prescription"
                                                    id="patients_req_prescription" <?php echo (get_option('patients_req_prescription') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_req_prescription">Prescription File</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="visits_tweaks">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Print Invoice Toggle -->
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="patients_print_invoice_on_visit"
                                                    id="patients_print_invoice_on_visit" <?php echo (get_option('patients_print_invoice_on_visit') == '1') ? 'checked' : ''; ?>>
                                                <label for="patients_print_invoice_on_visit">Print Invoice</label>
                                                <p class="text-muted">Print Invoice while adding new visit automatically
                                                    on Save</p>
                                            </div>
                                        </div>

                                        <!-- Edit Visit Time Locker -->
                                        <div class="form-group">
                                            <label for="patients_visit_time_locker">Edit Visit Time Locker</label>
                                            <select name="patients_visit_time_locker" id="patients_visit_time_locker" class="form-control">
                                                <option value="" <?php echo get_option('patients_visit_time_locker') == '' ? 'selected' : ''; ?>>Disable</option>
                                                <option value="5" <?php echo get_option('patients_visit_time_locker') == '5' ? 'selected' : ''; ?>>5 Minutes</option>
                                                <option value="10" <?php echo get_option('patients_visit_time_locker') == '10' ? 'selected' : ''; ?>>10 Minutes</option>
                                                <option value="15" <?php echo get_option('patients_visit_time_locker') == '15' ? 'selected' : ''; ?>>15 Minutes</option>
                                                <option value="30" <?php echo get_option('patients_visit_time_locker') == '30' ? 'selected' : ''; ?>>30 Minutes</option>
                                                <option value="60" <?php echo get_option('patients_visit_time_locker') == '60' ? 'selected' : ''; ?>>1 Hour</option>
                                                <option value="360" <?php echo get_option('patients_visit_time_locker') == '360' ? 'selected' : ''; ?>>6 Hours</option>
                                                <option value="1440" <?php echo get_option('patients_visit_time_locker') == '1440' ? 'selected' : ''; ?>>24 Hours</option>
                                                <option value="2880" <?php echo get_option('patients_visit_time_locker') == '2880' ? 'selected' : ''; ?>>48 Hours</option>
                                            </select>
                                            <p class="text-muted">Lock edit inputs after X minutes of visit creation.</p>
                                        </div>

                                        <!-- Disable Edit Inputs -->
                                        <div class="form-group">
                                            <label for="patients_visit_locker_fields">Disable Edit inputs on Time Locker</label>
                                            <select name="patients_visit_locker_fields[]" id="patients_visit_locker_fields" class="selectpicker" multiple data-width="100%">
                                                <?php 
                                                $selected_fields = get_option('patients_visit_locker_fields');
                                                $selected_fields = !empty($selected_fields) ? json_decode($selected_fields) : [];
                                                ?>
                                                <option value="mobile_number" <?php echo in_array('mobile_number', $selected_fields) ? 'selected' : ''; ?>>Mobile Number</option>
                                                <option value="patient_name" <?php echo in_array('patient_name', $selected_fields) ? 'selected' : ''; ?>>Patient Name</option>
                                                <option value="age" <?php echo in_array('age', $selected_fields) ? 'selected' : ''; ?>>Age / DOB</option>
                                                <option value="uid_no" <?php echo in_array('uid_no', $selected_fields) ? 'selected' : ''; ?>>UID No</option>
                                                <option value="primary_doctor" <?php echo in_array('primary_doctor', $selected_fields) ? 'selected' : ''; ?>>Primary Doctor</option>
                                                <option value="referral_doctor" <?php echo in_array('referral_doctor', $selected_fields) ? 'selected' : ''; ?>>Referral Doctor</option>
                                                <option value="attender" <?php echo in_array('attender', $selected_fields) ? 'selected' : ''; ?>>Attender Name</option>
                                                <option value="email" <?php echo in_array('email', $selected_fields) ? 'selected' : ''; ?>>Email</option>
                                                <option value="address" <?php echo in_array('address', $selected_fields) ? 'selected' : ''; ?>>Address</option>
                                                <option value="referral_lab" <?php echo in_array('referral_lab', $selected_fields) ? 'selected' : ''; ?>>Referral Lab</option>
                                                <option value="company" <?php echo in_array('company', $selected_fields) ? 'selected' : ''; ?>>Company</option>
                                                <option value="prescription" <?php echo in_array('prescription', $selected_fields) ? 'selected' : ''; ?>>Prescription File</option>
                                            </select>
                                        </div>

                                        <!-- Patient Name Standard -->
                                        <div class="form-group"
                                            style="padding: 10px; background: #fbfbfb; border: 1px solid #f0f0f0; border-radius: 4px;">
                                            <p class="bold">Patient Name Standard</p>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" name="patients_name_format" id="pn_full_caps"
                                                    value="full_caps" <?php echo (get_option('patients_name_format') == 'full_caps') ? 'checked' : ''; ?>>
                                                <label for="pn_full_caps">Full Caps (e.g. JOHN DOE)</label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" name="patients_name_format" id="pn_title_case"
                                                    value="title_case" <?php echo (get_option('patients_name_format') == 'title_case') ? 'checked' : ''; ?>>
                                                <label for="pn_title_case">Only First Letter Caps (e.g. John
                                                    Doe)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="billing_rules">
                                <p class="text-muted">Configure payment requirements for each Item Group.</p>
                                <hr />
                                <?php
                                $billing_rules = get_option('patients_billing_rules');
                                $billing_rules = !empty($billing_rules) ? json_decode($billing_rules, true) : [];
                                ?>
                                <table class="table dt-table">
                                    <thead>
                                        <tr>
                                            <th>Item Group</th>
                                            <th>Payment Requirement</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($item_groups as $group) {
                                            $rule = isset($billing_rules[$group['id']]) ? $billing_rules[$group['id']] : 'none';
                                            ?>
                                            <tr>
                                                <td><?php echo $group['name']; ?></td>
                                                <td>
                                                    <div class="radio radio-primary radio-inline">
                                                        <input type="radio"
                                                            name="billing_rules[<?php echo $group['id']; ?>]"
                                                            id="rule_full_<?php echo $group['id']; ?>" value="full" <?php echo $rule == 'full' ? 'checked' : ''; ?>>
                                                        <label for="rule_full_<?php echo $group['id']; ?>">Full Payment
                                                            (100%)</label>
                                                    </div>
                                                    <div class="radio radio-primary radio-inline">
                                                        <input type="radio"
                                                            name="billing_rules[<?php echo $group['id']; ?>]"
                                                            id="rule_half_<?php echo $group['id']; ?>" value="half" <?php echo $rule == 'half' ? 'checked' : ''; ?>>
                                                        <label for="rule_half_<?php echo $group['id']; ?>">50%
                                                            Payment</label>
                                                    </div>
                                                    <div class="radio radio-primary radio-inline">
                                                        <input type="radio"
                                                            name="billing_rules[<?php echo $group['id']; ?>]"
                                                            id="rule_none_<?php echo $group['id']; ?>" value="none" <?php echo $rule == 'none' ? 'checked' : ''; ?>>
                                                        <label for="rule_none_<?php echo $group['id']; ?>">None</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <hr />
                                <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="visit_table_filters">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Date Range -->
                                        <div class="form-group">
                                            <label for="patients_visit_default_date_range">Set Date Range</label>
                                            <select name="patients_visit_default_date_range"
                                                id="patients_visit_default_date_range" class="form-control">
                                                <option value="none"
                                                    <?php echo get_option('patients_visit_default_date_range') == 'none' ? 'selected' : ''; ?>>
                                                    None</option>
                                                <option value="today"
                                                    <?php echo get_option('patients_visit_default_date_range') == 'today' ? 'selected' : ''; ?>>
                                                    Today</option>
                                                <option value="yesterday_today"
                                                    <?php echo get_option('patients_visit_default_date_range') == 'yesterday_today' ? 'selected' : ''; ?>>
                                                    Yesterday and Today</option>
                                                <option value="last_week"
                                                    <?php echo get_option('patients_visit_default_date_range') == 'last_week' ? 'selected' : ''; ?>>
                                                    Last Week</option>
                                            </select>
                                        </div>

                                        <!-- Sort Order -->
                                        <div class="form-group">
                                            <p class="bold">Table data filter ordering option</p>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" name="patients_visit_sort_order" id="sort_asc"
                                                    value="asc"
                                                    <?php echo get_option('patients_visit_sort_order') == 'asc' ? 'checked' : ''; ?>>
                                                <label for="sort_asc">Asc</label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" name="patients_visit_sort_order" id="sort_desc"
                                                    value="desc"
                                                    <?php echo get_option('patients_visit_sort_order') == 'desc' ? 'checked' : ''; ?>>
                                                <label for="sort_desc">Desc</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="bold">Hide/show</p>
                                        <!-- Checkboxes -->
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="patients_visit_show_search"
                                                id="patients_visit_show_search"
                                                <?php echo get_option('patients_visit_show_search') == '1' ? 'checked' : ''; ?>>
                                            <label for="patients_visit_show_search">Search</label>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="patients_visit_show_date_range"
                                                id="patients_visit_show_date_range"
                                                <?php echo get_option('patients_visit_show_date_range') == '1' ? 'checked' : ''; ?>>
                                            <label for="patients_visit_show_date_range">Date Range</label>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="patients_visit_show_user"
                                                id="patients_visit_show_user"
                                                <?php echo get_option('patients_visit_show_user') == '1' ? 'checked' : ''; ?>>
                                            <label for="patients_visit_show_user">User</label>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="patients_visit_show_item_status"
                                                id="patients_visit_show_item_status"
                                                <?php echo get_option('patients_visit_show_item_status') == '1' ? 'checked' : ''; ?>>
                                            <label for="patients_visit_show_item_status">Item Status</label>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="patients_visit_show_payment_status"
                                                id="patients_visit_show_payment_status"
                                                <?php echo get_option('patients_visit_show_payment_status') == '1' ? 'checked' : ''; ?>>
                                            <label for="patients_visit_show_payment_status">Payment Status</label>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="patients_visit_show_limit"
                                                id="patients_visit_show_limit"
                                                <?php echo get_option('patients_visit_show_limit') == '1' ? 'checked' : ''; ?>>
                                            <label for="patients_visit_show_limit">Limit</label>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="patients_visit_show_reset"
                                                id="patients_visit_show_reset"
                                                <?php echo get_option('patients_visit_show_reset') == '1' ? 'checked' : ''; ?>>
                                            <label for="patients_visit_show_reset">Reset button</label>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="item_group_settings">
                                <p class="text-muted">Item Group Settings</p>
                                <hr />
                                <?php
                                $restricted_groups = get_option('patients_restricted_groups');
                                $restricted_groups = !empty($restricted_groups) ? json_decode($restricted_groups, true) : [];
                                ?>
                                <table class="table dt-table">
                                    <thead>
                                        <tr>
                                            <th>Item Group Name</th>
                                            <th>Restriction</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($item_groups as $group) { ?>
                                            <tr>
                                                <td><?php echo $group['name']; ?></td>
                                                <td>
                                                    <div class="checkbox checkbox-danger">
                                                        <input type="checkbox" name="restricted_groups[]"
                                                            id="restrict_<?php echo $group['id']; ?>"
                                                            value="<?php echo $group['id']; ?>"
                                                            <?php echo in_array($group['id'], $restricted_groups) ? 'checked' : ''; ?>>
                                                        <label for="restrict_<?php echo $group['id']; ?>">Hide from Visit Search & Tabs</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <hr />
                                <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
                            </div>

                        </div>
                        <?php echo form_close(); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>