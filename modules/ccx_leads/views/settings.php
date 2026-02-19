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

                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#fields" aria-controls="fields" role="tab" data-toggle="tab">
                                            <?php echo _l('Fields'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#ordering" aria-controls="ordering" role="tab" data-toggle="tab">
                                            <?php echo _l('Ordering'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#statuses" aria-controls="statuses" role="tab" data-toggle="tab">
                                            <?php echo _l('Statuses'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#roller_coaster" aria-controls="roller_coaster" role="tab"
                                            data-toggle="tab">
                                            <?php echo _l('Roller Coaster'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#wa_web" aria-controls="wa_web" role="tab" data-toggle="tab">
                                            <?php echo _l('WA Web'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#call_mgmt" aria-controls="call_mgmt" role="tab" data-toggle="tab">
                                            <?php echo _l('Call Mgmt'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#reporting" aria-controls="reporting" role="tab" data-toggle="tab">
                                            <?php echo _l('Reporting'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="fields">
                                <p class="text-muted">Fields settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="ordering">
                                <p class="text-muted">Ordering settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="statuses">
                                <p class="text-muted">Statuses settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="roller_coaster">
                                <p class="text-muted">Roller Coaster settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="wa_web">
                                <p class="text-muted">WA Web settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="call_mgmt">
                                <p class="text-muted">Call Management settings coming soon...</p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="reporting">
                                <p class="text-muted">Reporting settings coming soon...</p>
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