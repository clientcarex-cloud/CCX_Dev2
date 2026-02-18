<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('ccx_leads', '', 'create')) { ?>
                                <a href="<?php echo admin_url('ccx_leads/lead'); ?>"
                                    class="btn btn-info pull-left display-block">
                                    <?php echo _l('new_ccx_lead'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('id'),
                            _l('name'),
                            _l('company'),
                            _l('phonenumber'),
                            _l('email'),
                            _l('status'),
                            _l('assigned'),
                            _l('dateadded'),
                            _l('options'),
                        ], 'ccx-leads'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-ccx-leads', window.location.href, [8], [8]);
    });
</script>
</body>

</html>