<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="team-header">
                    <h4>Team Members</h4>
                    <div style="display:flex; align-items:center; gap:10px;">
                         <?php if (has_permission('staff', '', 'create')) { ?>
                            <a href="<?php echo admin_url('team/member'); ?>" class="add-member-btn">
                                <i class="fa fa-plus"></i> Add Member
                            </a>
                        <?php } ?>
                        <?php if (staff_can('view', 'roles')) { ?>
                            <a href="<?php echo admin_url('team/roles'); ?>" class="add-member-btn" style="background: #fff; color: #333; border: 1px solid #d1d5db;">
                                Manage Roles
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php
                        $table_data = [
                            _l('staff_dt_name'),
                            _l('staff_dt_email'),
                            _l('role'),
                            _l('staff_dt_last_Login'),
                            _l('staff_dt_active'),
                        ];
                        $custom_fields = get_custom_fields('staff', ['show_on_table' => 1]);
                        foreach ($custom_fields as $field) {
                            array_push($table_data, [
                                'name' => $field['name'],
                                'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                            ]);
                        }
                        render_datatable($table_data, 'team');
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-team', window.location.href);
    });
</script>