<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        <div class="table-responsive">
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('id'); ?></th>
                                        <th><?php echo _l('name'); ?></th>
                                        <th><?php echo _l('email'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($doctors as $doctor) { ?>
                                        <tr>
                                            <td><?php echo $doctor['staffid']; ?></td>
                                            <td><a
                                                    href="<?php echo admin_url('doctors/schedules/edit/' . $doctor['staffid']); ?>"><?php echo $doctor['firstname'] . ' ' . $doctor['lastname']; ?></a>
                                            </td>
                                            <td><?php echo $doctor['email']; ?></td>
                                            <!-- Assuming email is available in get_doctors result, if not, might need to fetch -->
                                            <td>
                                                <a href="<?php echo admin_url('doctors/schedules/edit/' . $doctor['staffid']); ?>"
                                                    class="btn btn-default btn-icon"><i class="fa fa-calendar"></i>
                                                    <?php echo _l('schedule'); ?></a>
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
<?php init_tail(); ?>
</body>

</html>