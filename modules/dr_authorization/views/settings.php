<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Dr Authorization Settings</h4>
                        <hr class="hr-panel-heading" />
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#filters" aria-controls="filters" role="tab"
                                            data-toggle="tab">Filters</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#hide_show" aria-controls="hide_show" role="tab"
                                            data-toggle="tab">Hide/Show</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="filters">
                                <?php echo form_open(admin_url('dr_authorization/settings')); ?>
                                <input type="hidden" name="settings_group" value="filters">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="dr_authorization_default_date_filter">Default Date Filter</label>
                                        <select name="dr_authorization_default_date_filter"
                                            id="dr_authorization_default_date_filter" class="form-control selectpicker">
                                            <option value="" <?php echo get_option('dr_authorization_default_date_filter') == '' ? 'selected' : ''; ?>>None</option>
                                            <option value="today" <?php echo get_option('dr_authorization_default_date_filter') == 'today' ? 'selected' : ''; ?>>Today</option>
                                            <option value="yesterday" <?php echo get_option('dr_authorization_default_date_filter') == 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                                            <option value="yesterday_today" <?php echo get_option('dr_authorization_default_date_filter') == 'yesterday_today' ? 'selected' : ''; ?>>Yesterday & Today</option>
                                        </select>
                                    </div>
                                </div>
                                <hr />
                                <button type="submit" class="btn btn-info">Save Settings</button>
                                <?php echo form_close(); ?>
                            </div>

                            <!-- Hide/Show Tab -->
                            <div role="tabpanel" class="tab-pane" id="hide_show">
                                <?php echo form_open(admin_url('dr_authorization/settings')); ?>
                                <input type="hidden" name="settings_group" value="hide_show">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="text-info bold">Toggle visibility of filters on the Dr Authorization
                                            list
                                            page.</p>
                                    </div>
                                    <div class="col-md-12">
                                        <!-- Date Range Filter -->
                                        <div class="form-group">
                                            <label for="dr_authorization_show_date_filter">Show Date Range
                                                Filter</label>
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="dr_authorization_show_date_filter"
                                                    class="onoffswitch-checkbox" id="dr_authorization_show_date_filter"
                                                    value="1" <?php echo get_option('dr_authorization_show_date_filter') == '1' || get_option('dr_authorization_show_date_filter') === '' ? 'checked' : ''; ?>>
                                                <label class="onoffswitch-label"
                                                    for="dr_authorization_show_date_filter">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- User Filter -->
                                        <div class="form-group">
                                            <label for="dr_authorization_show_user_filter">Show User Filter</label>
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="dr_authorization_show_user_filter"
                                                    class="onoffswitch-checkbox" id="dr_authorization_show_user_filter"
                                                    value="1" <?php echo get_option('dr_authorization_show_user_filter') == '1' || get_option('dr_authorization_show_user_filter') === '' ? 'checked' : ''; ?>>
                                                <label class="onoffswitch-label"
                                                    for="dr_authorization_show_user_filter">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Ref Doctor Filter -->
                                        <div class="form-group">
                                            <label for="dr_authorization_show_ref_doctor_filter">Show Ref. Doctor
                                                Filter</label>
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="dr_authorization_show_ref_doctor_filter"
                                                    class="onoffswitch-checkbox"
                                                    id="dr_authorization_show_ref_doctor_filter" value="1" <?php echo get_option('dr_authorization_show_ref_doctor_filter') == '1' || get_option('dr_authorization_show_ref_doctor_filter') === '' ? 'checked' : ''; ?>>
                                                <label class="onoffswitch-label"
                                                    for="dr_authorization_show_ref_doctor_filter">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Department Filter -->
                                        <div class="form-group">
                                            <label for="dr_authorization_show_department_filter">Show Department
                                                Filter</label>
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="dr_authorization_show_department_filter"
                                                    class="onoffswitch-checkbox"
                                                    id="dr_authorization_show_department_filter" value="1" <?php echo get_option('dr_authorization_show_department_filter') == '1' || get_option('dr_authorization_show_department_filter') === '' ? 'checked' : ''; ?>>
                                                <label class="onoffswitch-label"
                                                    for="dr_authorization_show_department_filter">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Payment Info Toggle -->
                                        <div class="form-group">
                                            <label for="dr_authorization_show_payment_info">Show Paid & Due Info</label>
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="dr_authorization_show_payment_info"
                                                    class="onoffswitch-checkbox" id="dr_authorization_show_payment_info"
                                                    value="1" <?php echo get_option('dr_authorization_show_payment_info') == '1' || get_option('dr_authorization_show_payment_info') === '' ? 'checked' : ''; ?>>
                                                <label class="onoffswitch-label"
                                                    for="dr_authorization_show_payment_info">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <button type="submit" class="btn btn-info">Save Settings</button>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>

</html>