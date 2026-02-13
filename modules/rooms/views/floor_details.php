<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-margin font-bold">
                                    <span class="text-muted">
                                        <?php echo $building->name; ?> /
                                    </span>
                                    <?php echo $floor->name; ?>
                                </h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="#" onclick="assign_room(); return false;" class="btn btn-info">
                                    <?php echo _l('add_room'); ?>
                                </a>
                                <a href="<?php echo admin_url('rooms/building/' . $building->id); ?>"
                                    class="btn btn-default">
                                    <?php echo _l('back_to_floor_view'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <!-- Visual Room Grid -->
                        <div class="row">
                            <?php foreach ($rooms as $room) { ?>
                                <div class="col-md-3">
                                    <div class="panel_s"
                                        style="border: 1px solid #dce1ef; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <div class="panel-body text-center">
                                            <i class="fa fa-bed fa-3x text-info"></i>
                                            <h4 class="bold">
                                                <?php echo $room['description']; // Using description as Room Name usually stored there for items ?>
                                            </h4>

                                            <div class="text-muted mtop10">
                                                Rate:
                                                <?php echo app_format_money($room['rate'], $base_currency); ?>
                                            </div>

                                            <div class="mtop15">
                                                <a href="<?php echo admin_url('rooms/unassign_room/' . $room['assignment_id'] . '/' . $floor->id); ?>"
                                                    class="btn btn-danger btn-xs _delete">
                                                    <?php echo _l('remove'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (count($rooms) == 0) { ?>
                                <div class="col-md-12 text-center text-muted mtop20">
                                    <i class="fa fa-bed fa-2x"></i>
                                    <p class="mtop10">
                                        <?php echo _l('no_rooms_assigned'); ?>
                                    </p>
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Room Modal -->
<div class="modal fade" id="assign_room_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('add_room_to_floor'); ?>
                </h4>
            </div>
            <?php echo form_open(admin_url('rooms/assign_room'), array('id' => 'assign-room-form')); ?>
            <input type="hidden" name="floor_id" value="<?php echo $floor->id; ?>">
            <div class="modal-body">
                <!-- Info message removed as requested -->
                <div class="form-group">
                    <label for="item_id">
                        <?php echo _l('select_room'); ?>
                    </label>
                    <select class="form-control selectpicker" data-live-search="true" name="item_id" required>
                        <option value=""></option>
                        <?php foreach ($room_items as $item) { ?>
                            <option value="<?php echo $item->id; ?>">
                                <?php echo $item->description; ?> - <?php echo $item->rate; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-info btn-xs" onclick="toggle_new_room_form();">
                        <?php echo _l('new_room'); ?>
                    </button>
                    <div id="new_room_wrapper" class="text-left mtop10 hide">
                        <label><?php echo _l('room_name'); ?></label>
                        <input type="text" id="new_room_name" class="form-control" placeholder="Room Name/Description">
                        <label class="mtop5"><?php echo _l('rate'); ?></label>
                        <input type="number" id="new_room_rate" class="form-control" placeholder="Rate">
                        <div class="mtop10 text-right">
                            <button type="button" class="btn btn-default"
                                onclick="toggle_new_room_form();"><?php echo _l('cancel'); ?></button>
                            <button type="button" class="btn btn-success"
                                onclick="save_new_room_item();"><?php echo _l('save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-info">
                    <?php echo _l('assign'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    function assign_room() {
        $('#assign_room_modal').modal('show');
    }

    function toggle_new_room_form() {
        $('#new_room_wrapper').toggleClass('hide');
    }

    function save_new_room_item() {
        var name = $('#new_room_name').val();
        var rate = $('#new_room_rate').val();

        if (!name || !rate) {
            alert_float('warning', '<?php echo _l('all_fields_required'); ?>');
            return;
        }

        var data = {
            description: name,
            rate: rate
        };

        if (typeof csrfData !== 'undefined') {
            data[csrfData.token_name] = csrfData.hash;
        }

        $.post(admin_url + 'rooms/add_room_item', data).done(function (response) {
            response = JSON.parse(response);
            if (response.success) {
                // Add to select and select it
                var newOption = '<option value="' + response.id + '" selected>' + response.name + '</option>';
                $('#assign_room_modal select[name="item_id"]').append(newOption);
                $('#assign_room_modal select[name="item_id"]').selectpicker('refresh');

                // Clear and hide form
                $('#new_room_name').val('');
                $('#new_room_rate').val('');
                toggle_new_room_form();
                alert_float('success', response.message);
            } else {
                alert_float('warning', response.message);
            }
        });
    }
</script>
</body>

</html>