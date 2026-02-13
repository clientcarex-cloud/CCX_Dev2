<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#b2b_labs" aria-controls="b2b_labs" role="tab" data-toggle="tab">
                                            <?php echo _l('b2b_labs'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#b2b_payments" aria-controls="b2b_payments" role="tab"
                                            data-toggle="tab">
                                            B2B Payments
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#b2b_reports" aria-controls="b2b_reports" role="tab" data-toggle="tab">
                                            B2B Reports
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content mtop15">
                            <div role="tabpanel" class="tab-pane active" id="b2b_labs">
                                <a href="#" class="btn btn-info mbot15" data-toggle="modal"
                                    data-target="#b2b_lab_user_modal">
                                    <?php echo _l('b2b_labs_create_user'); ?>
                                </a>
                                
                                <div class="clearfix"></div>
                                <hr class="hr-panel-heading" />
                                
                                <div class="table-responsive">
                                    <table class="table dt-table" data-order-col="0" data-order-type="asc">
                                        <thead>
                                            <tr>
                                                <th><?php echo _l('b2b_labs_staff_name'); ?></th>
                                                <th><?php echo _l('b2b_labs_staff_email'); ?></th>
                                                <th>Pricing</th>
                                                <th><?php echo _l('b2b_labs_staff_last_login'); ?></th>
                                                <th><?php echo _l('b2b_labs_staff_active'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($staff_members as $member) { ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo admin_url('staff/member/' . $member['staffid']); ?>">
                                                        <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="mailto:<?php echo $member['email']; ?>"><?php echo $member['email']; ?></a>
                                                </td>
                                                <td>
                                                    <a href="<?php echo admin_url('b2b_labs/pricing/' . $member['staffid']); ?>" class="btn btn-info btn-xs">Pricing</a>
                                                </td>
                                                <td>
                                                    <?php echo $member['last_login'] ? _dt($member['last_login']) : _l('never'); ?>
                                                </td>
                                                <td>
                                                    <div class="onoffswitch">
                                                        <input type="checkbox"
                                                            data-switch-url="<?php echo admin_url('staff/change_staff_status'); ?>"
                                                            name="onoffswitch" class="onoffswitch-checkbox"
                                                            id="c_<?php echo $member['staffid']; ?>"
                                                            data-id="<?php echo $member['staffid']; ?>"
                                                            <?php echo $member['active'] == 1 ? 'checked' : ''; ?>>
                                                        <label class="onoffswitch-label"
                                                            for="c_<?php echo $member['staffid']; ?>"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- User Creation Modal -->
                            <div class="modal fade" id="b2b_lab_user_modal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <?php echo form_open(admin_url('b2b_labs/create_user')); ?>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><?php echo _l('b2b_labs_create_title'); ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="firstname" class="control-label"><?php echo _l('b2b_labs_firstname'); ?></label>
                                                <input type="text" class="form-control" name="firstname" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="lastname" class="control-label"><?php echo _l('b2b_labs_lastname'); ?></label>
                                                <input type="text" class="form-control" name="lastname" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email" class="control-label"><?php echo _l('b2b_labs_email'); ?></label>
                                                <input type="email" class="form-control" name="email" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password" class="control-label"><?php echo _l('b2b_labs_password'); ?></label>
                                                <input type="password" class="form-control" name="password" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                data-dismiss="modal"><?php echo _l('close'); ?></button>
                                            <button type="submit" class="btn btn-primary"><?php echo _l('b2b_labs_create_user'); ?></button>
                                        </div>
                                    </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="b2b_payments">
                                <p>B2B Payments Content</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="b2b_reports">
                                <p>B2B Reports Content</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Settings Button -->
            <!-- Floating Settings Button -->
            <a href="<?php echo admin_url('b2b_labs/settings'); ?>" class="btn btn-info"
                style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <i class="fa fa-cog fa-lg"></i>
            </a>
        </div>
    </div>
</div>
<?php init_tail(); ?>