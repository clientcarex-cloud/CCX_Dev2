<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
$report_types = [
    ['id' => 'lab', 'name' => 'Lab'],
    ['id' => 'hospital', 'name' => 'Hospital'],
    ['id' => 'lab_hospital', 'name' => 'Lab + Hospital'],
    ['id' => 'pharmacy', 'name' => 'Pharmacy'],
    ['id' => 'clinic', 'name' => 'Clinic'],
    ['id' => 'clinic_lab', 'name' => 'Clinic + Lab'],
    ['id' => 'clinic_pharmacy', 'name' => 'Clinic + Pharmacy'],
    ['id' => 'all_combine', 'name' => 'All Combine'],
];
$type_map = array_column($report_types, 'name', 'id');
$role_map = [];
foreach ($roles as $role) {
    $role_map[$role['roleid']] = $role['name'];
}
$staff_map = [];
foreach ($staff as $s) {
    $staff_map[$s['staffid']] = $s['firstname'] . ' ' . $s['lastname'];
}

?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" class="btn btn-info pull-left display-block"
                                onclick="new_report(); return false;">New Report</a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="table-responsive">
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Roles</th>
                                        <th>Staff</th>
                                        <th>Description</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports as $report) { ?>
                                        <tr>
                                            <td><?php echo $report['id']; ?></td>
                                            <td><?php echo $report['name']; ?></td>
                                            <td><?php echo isset($type_map[$report['type']]) ? $type_map[$report['type']] : $report['type']; ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (!empty($report['allowed_roles'])) {
                                                    $role_ids = explode(',', $report['allowed_roles']);
                                                    foreach ($role_ids as $role_id) {
                                                        if (isset($role_map[$role_id])) {
                                                            echo '<span class="label label-default mright5 inline-block">' . $role_map[$role_id] . '</span>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (!empty($report['allowed_staff'])) {
                                                    $staff_ids = explode(',', $report['allowed_staff']);
                                                    foreach ($staff_ids as $staff_id) {
                                                        if (isset($staff_map[$staff_id])) {
                                                            echo '<span class="label label-default mright5 inline-block">' . $staff_map[$staff_id] . '</span>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $report['description']; ?></td>
                                            <td>
                                                <a href="#"
                                                    onclick="edit_report(<?php echo htmlspecialchars(json_encode($report)); ?>); return false;"
                                                    class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>
                                            </td>
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

<div class="modal fade" id="report_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('mis_reports/report')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Edit Report</span>
                    <span class="add-title">Add New Report</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="id" id="id">
                        <?php echo render_input('name', 'Report Name'); ?>
                        <?php echo render_select('type', $report_types, ['id', 'name'], 'Report Type'); ?>
                        <?php echo render_select('allowed_roles[]', $roles, ['roleid', 'name'], 'Allowed Roles', '', ['multiple' => true, 'data-actions-box' => true], [], '', '', false); ?>
                        <?php echo render_select('allowed_staff[]', $staff, ['staffid', ['firstname', 'lastname']], 'Allowed Staff', '', ['multiple' => true, 'data-actions-box' => true], [], '', '', false); ?>
                        <?php echo render_textarea('description', 'Description'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info">Save</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php init_tail(); ?>
<script>
    function new_report() {
        $('#report_modal').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#report_modal input[name="id"]').val('');
        $('#report_modal input[name="name"]').val('');
        $('#report_modal select[name="type"]').val('').change();
        $('#report_modal select[name="allowed_roles[]"]').val('').change();
        $('#report_modal select[name="allowed_staff[]"]').val('').change();
        $('#report_modal textarea[name="description"]').val('');
    }

    function edit_report(report) {
        $('#report_modal').modal('show');
        $('.edit-title').removeClass('hide');
        $('.add-title').addClass('hide');
        $('#report_modal input[name="id"]').val(report.id);
        $('#report_modal input[name="name"]').val(report.name);
        $('#report_modal select[name="type"]').val(report.type).change();

        var roles = [];
        if (report.allowed_roles) {
            roles = report.allowed_roles.split(',');
        }
        $('#report_modal select[name="allowed_roles[]"]').val(roles).change();

        var staff = [];
        if (report.allowed_staff) {
            staff = report.allowed_staff.split(',');
        }
        $('#report_modal select[name="allowed_staff[]"]').val(staff).change();

        $('#report_modal textarea[name="description"]').val(report.description);
    }
</script>
</body>

</html>