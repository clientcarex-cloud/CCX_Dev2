<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="_buttons">
                    <?php if (has_permission('coupons', '', 'create')) { ?>
                        <a href="<?php echo admin_url('coupons/coupon'); ?>"
                            class="btn btn-info pull-left display-block"><?php echo _l('new_coupon'); ?></a>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>

                <div class="clearfix"></div>
                <hr class="hr-panel-heading" />

                <div class="panel_s">
                    <div class="panel-body">
                        <?php render_datatable([
                            _l('coupon_code'),
                            _l('coupon_name'),
                            _l('coupon_type'),
                            _l('coupon_amount'),
                            _l('coupon_start_date'),
                            _l('coupon_end_date'),
                            _l('coupon_status'),
                            _l('options'),
                        ], 'coupons'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-coupons', window.location.href);
    });
</script>
</body>

</html>