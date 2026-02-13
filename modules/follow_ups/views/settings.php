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

                        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#status_master_data" aria-controls="status_master_data" role="tab"
                                            data-toggle="tab">
                                            Status Master Data
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content mtop15">
                            <div role="tabpanel" class="tab-pane active" id="status_master_data">
                                <div class="_buttons">
                                    <a href="#" class="btn btn-info pull-left display-block"
                                        onclick="new_status(); return false;">
                                        <?php echo _l('new_status'); ?>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                                <hr class="hr-panel-heading" />

                                <div class="table-responsive">
                                    <table class="table dt-table" data-order-col="2" data-order-type="asc">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Usage</th>
                                                <th>Color</th>
                                                <!-- <th>Status Tabs Filter</th> -->
                                                <th>Order</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($statuses as $status) { ?>
                                                <tr>
                                                    <td><?php echo $status['name']; ?></td>
                                                    <td>
                                                        <span class="badge"
                                                            style="background-color: #777;"><?php echo isset($status_counts[$status['id']]) ? $status_counts[$status['id']] : 0; ?></span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            style="display:inline-block; width: 20px; height: 20px; background-color: <?php echo $status['color']; ?>; vertical-align: middle; border-radius: 4px; margin-right: 5px;"></span>
                                                        <?php echo $status['color']; ?>
                                                    </td>
                                                    <!-- <td>
                                                        <div class="onoffswitch">
                                                            <input type="checkbox"
                                                                data-switch-url="<?php echo admin_url('follow_ups/change_status_filter_visibility'); ?>"
                                                                name="onoffswitch" class="onoffswitch-checkbox"
                                                                id="c_<?php echo $status['id']; ?>"
                                                                data-id="<?php echo $status['id']; ?>" <?php if (isset($status['filter_visible']) && $status['filter_visible'] == 1) {
                                                                       echo 'checked';
                                                                   } ?>>
                                                            <label class="onoffswitch-label"
                                                                for="c_<?php echo $status['id']; ?>"></label>
                                                        </div>
                                                    </td> -->
                                                    <td><?php echo $status['status_order']; ?></td>
                                                    <td>
                                                        <a href="#"
                                                            onclick="edit_status(<?php echo htmlspecialchars(json_encode($status)); ?>); return false;"
                                                            class="btn btn-default btn-icon"><i
                                                                class="fa fa-pencil-square-o"></i></a>
                                                        <a href="<?php echo admin_url('follow_ups/settings?delete_id=' . $status['id']); ?>"
                                                            class="btn btn-danger btn-icon _delete"
                                                            onclick="delete_status(<?php echo $status['id']; ?>); return false;"><i
                                                                class="fa fa-remove"></i></a>
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
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="status_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('follow_ups/settings')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_status'); ?></span>
                    <span class="add-title"><?php echo _l('new_status'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div> <!-- For ID hidden input -->
                        <input type="hidden" name="action" value="add_status">

                        <?php echo render_input('name', 'Name'); ?>
                        <?php echo render_color_picker('color', 'Color', '#757575'); ?>
                        <?php echo render_input('status_order', 'Order', '0', 'number'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- Virtual Delete Form for security/csrf handling best practice vs GET -->
<form id="delete_status_form" action="<?php echo admin_url('follow_ups/settings'); ?>" method="post"
    style="display:none;">
    <input type="hidden" name="action" value="delete_status">
    <input type="hidden" name="id" id="delete_input_id">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
        value="<?php echo $this->security->get_csrf_hash(); ?>">
</form>

<?php init_tail(); ?>
<script>
    function new_status() {
        $('#status_modal').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#additional').html('');

        $('input[name="name"]').val('');
        $('input[name="color"]').val('#757575');
        $('.input-group-addon i').css('background-color', '#757575'); // Reset color picker visual
        $('input[name="status_order"]').val('0');
        $('input[name="action"]').val('add_status');
    }

    function edit_status(status) {
        $('#status_modal').modal('show');
        $('.edit-title').removeClass('hide');
        $('.add-title').addClass('hide');
        $('#additional').html('<input type="hidden" name="id" value="' + status.id + '">');

        $('input[name="name"]').val(status.name);
        $('input[name="color"]').val(status.color);
        $('.input-group-addon i').css('background-color', status.color); // Update color picker visual
        $('input[name="status_order"]').val(status.status_order);
        $('input[name="action"]').val('update_status');
    }

    function delete_status(id) {
        if (confirm('Are you sure you want to delete this status?')) {
            $('#delete_input_id').val(id);
            $('#delete_status_form').submit();
        }
    }

</script>