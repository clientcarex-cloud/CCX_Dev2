<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        
                        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-chevron-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-chevron-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#referral_doctors" aria-controls="referral_doctors" role="tab" data-toggle="tab">
                                            Referral Doctors
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#referral_labs" aria-controls="referral_labs" role="tab" data-toggle="tab">
                                            Referral Labs
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            <!-- Referral Doctors Tab -->
                            <div role="tabpanel" class="tab-pane active" id="referral_doctors">
                                <h4 class="no-margin font-bold">Referral Doctors Collection</h4>
                                <hr />
                                <div class="table-responsive">
                                    <table class="table dt-table" data-order-col="2" data-order-type="desc">
                                        <thead>
                                            <tr>
                                                <th>Referral Name</th>
                                                <th>Total Visits</th>
                                                <th>Total Billed</th>
                                                <th>Total Collected</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($doctor_stats as $stat) { ?>
                                                <tr>
                                                    <td><?php echo $stat['name']; ?></td>
                                                    <td><?php echo $stat['total_visits']; ?></td>
                                                    <td><?php echo app_format_money($stat['total_billed'], $base_currency); ?></td>
                                                    <td><?php echo app_format_money($stat['total_collected'], $base_currency); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Referral Labs Tab -->
                            <div role="tabpanel" class="tab-pane" id="referral_labs">
                                <h4 class="no-margin font-bold">Referral Labs Collection</h4>
                                <hr />
                                <div class="table-responsive">
                                    <table class="table dt-table" data-order-col="2" data-order-type="desc">
                                        <thead>
                                            <tr>
                                                <th>Referral Name</th>
                                                <th>Total Visits</th>
                                                <th>Total Billed</th>
                                                <th>Total Collected</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($lab_stats as $stat) { ?>
                                                <tr>
                                                    <td><?php echo $stat['name']; ?></td>
                                                    <td><?php echo $stat['total_visits']; ?></td>
                                                    <td><?php echo app_format_money($stat['total_billed'], $base_currency); ?></td>
                                                    <td><?php echo app_format_money($stat['total_collected'], $base_currency); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
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