<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#fields" aria-controls="fields" role="tab" data-toggle="tab">
                                            Fields
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#ordering" aria-controls="ordering" role="tab" data-toggle="tab">
                                            Ordering
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#roller_coaster" aria-controls="roller_coaster" role="tab"
                                            data-toggle="tab">
                                            Roller Coaster
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#reports" aria-controls="reports" role="tab" data-toggle="tab">
                                            Reports
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#call_mgmt" aria-controls="call_mgmt" role="tab" data-toggle="tab">
                                            Call Mgmt
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#wa_web" aria-controls="wa_web" role="tab" data-toggle="tab">
                                            WA Web
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="fields">
                                <p>Fields settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="ordering">
                                <p>Ordering settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="roller_coaster">
                                <p>Roller Coaster settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="reports">
                                <p>Reports settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="call_mgmt">
                                <p>Call Mgmt settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="wa_web">
                                <p>WA Web settings coming soon...</p>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <a href="<?php echo admin_url('ccx_leads'); ?>" class="btn btn-default">
                            <?php echo _l('go_back'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>