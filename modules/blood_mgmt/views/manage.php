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
                                <h4 class="no-margin"><?php echo _l('blood_mgmt'); ?> - Dashboard</h4>
                                <hr class="hr-panel-heading" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="widget">
                                    <div class="row">
                                        <div class="col-xs-12 text-center">
                                            <a href="<?php echo admin_url('blood_mgmt/donors'); ?>"
                                                class="text-uppercase col-md-12"
                                                style="font-size: 20px; padding: 20px; background: #f0f0f0; border-radius: 5px;">
                                                <i class="fa fa-user fa-3x"></i><br />
                                                <?php echo _l('blood_donors'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="widget">
                                    <div class="row">
                                        <div class="col-xs-12 text-center">
                                            <a href="<?php echo admin_url('blood_mgmt/inventory'); ?>"
                                                class="text-uppercase col-md-12"
                                                style="font-size: 20px; padding: 20px; background: #f0f0f0; border-radius: 5px;">
                                                <i class="fa fa-tint fa-3x"></i><br />
                                                <?php echo _l('blood_inventory'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="widget">
                                    <div class="row">
                                        <div class="col-xs-12 text-center">
                                            <a href="<?php echo admin_url('blood_mgmt/issues'); ?>"
                                                class="text-uppercase col-md-12"
                                                style="font-size: 20px; padding: 20px; background: #f0f0f0; border-radius: 5px;">
                                                <i class="fa fa-heartbeat fa-3x"></i><br />
                                                <?php echo _l('blood_issues'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>