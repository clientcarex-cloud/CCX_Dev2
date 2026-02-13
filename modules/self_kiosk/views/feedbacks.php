<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('self_kiosk'); ?>"
                                class="btn btn-default pull-left display-block">
                                <i class="fa fa-arrow-left"></i>
                                <?php echo _l('back'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <h4 class="mbot20">
                            <?php echo $title; ?>
                        </h4>
                        <?php render_datatable(array(
                            'Patient Name',
                            'Mobile Number',
                            'Rating',
                            'Message',
                            'Date'
                        ), 'feedback-entries'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-feedback-entries', window.location.href);
    });
</script>