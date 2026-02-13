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
                                    <?php echo $building->name; ?> <span class="text-muted small">-
                                        <?php echo _l('manage_floors'); ?>
                                    </span>
                                </h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="#" onclick="new_floor(); return false;" class="btn btn-info">
                                    <?php echo _l('add_floor'); ?>
                                </a>
                                <a href="<?php echo admin_url('rooms'); ?>" class="btn btn-default">
                                    <?php echo _l('back_to_buildings'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <!-- Visual Floor Representation -->
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="building-structure"
                                    style="border: 2px solid #e5e7eb; padding: 20px; border-radius: 8px; background: #f9fafb;">
                                    <?php if (count($floors) > 0) {
                                        // Reverse floors to show higher floors on top visually
                                        $display_floors = array_reverse($floors);
                                        foreach ($display_floors as $floor) {
                                            ?>
                                            <div class="floor-row"
                                                style="background: white; border: 1px solid #d1d5db; margin-bottom: 10px; padding: 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
                                                <div>
                                                    <h4 style="margin: 0; font-weight: bold;">
                                                        <a href="<?php echo admin_url('rooms/floor/' . $floor['id']); ?>">
                                                            <?php echo $floor['name']; ?>
                                                        </a>
                                                        <small class="text-muted">(Level
                                                            <?php echo $floor['floor_level']; ?>)
                                                        </small>
                                                    </h4>
                                                    <small>
                                                        <?php echo $floor['description']; ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <a href="<?php echo admin_url('rooms/floor/' . $floor['id']); ?>"
                                                        class="btn btn-success btn-xs">
                                                        <?php echo _l('view_rooms'); ?>
                                                    </a>
                                                    <button
                                                        onclick="edit_floor(<?php echo $floor['id']; ?>, '<?php echo $floor['name']; ?>', <?php echo $floor['floor_level']; ?>, '<?php echo $floor['description']; ?>')"
                                                        class="btn btn-default btn-xs">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    <a href="<?php echo admin_url('rooms/delete_floor/' . $floor['id']); ?>"
                                                        class="btn btn-danger btn-xs _delete">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php }
                                    } else { ?>
                                        <div class="text-center text-muted" style="padding: 40px;">
                                            <i class="fa fa-building-o fa-3x"></i>
                                            <br /><br />
                                            <?php echo _l('no_floors_added'); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floor Modal -->
<div class="modal fade" id="floor_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('new_floor'); ?>
                </h4>
            </div>
            <?php echo form_open(admin_url('rooms/add_floor'), array('id' => 'floor-form')); ?>
            <input type="hidden" name="building_id" value="<?php echo $building->id; ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">
                        <?php echo _l('name'); ?>
                    </label>
                    <input type="text" class="form-control" id="name" name="name" required
                        placeholder="e.g. Ground Floor">
                </div>
                <div class="form-group">
                    <label for="floor_level">
                        <?php echo _l('floor_level'); ?>
                    </label>
                    <input type="number" class="form-control" id="floor_level" name="floor_level" required value="0"
                        help="0 for Ground, 1 for 1st Floor, etc.">
                </div>
                <div class="form-group">
                    <label for="description">
                        <?php echo _l('description'); ?>
                    </label>
                    <textarea class="form-control" id="description" name="description"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-info">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    function new_floor() {
        $('#floor_modal').modal('show');
        $('#floor_modal .modal-title').text('<?php echo _l('new_floor'); ?>');
        $('#floor_modal form').attr('action', '<?php echo admin_url('rooms/add_floor'); ?>');
        $('#floor_modal input[name="name"]').val('');
        $('#floor_modal input[name="floor_level"]').val('0');
        $('#floor_modal textarea[name="description"]').val('');
    }

    function edit_floor(id, name, level, description) {
        $('#floor_modal').modal('show');
        $('#floor_modal .modal-title').text('<?php echo _l('edit_floor'); ?>');
        $('#floor_modal form').attr('action', '<?php echo admin_url('rooms/update_floor/'); ?>' + id);
        $('#floor_modal input[name="name"]').val(name);
        $('#floor_modal input[name="floor_level"]').val(level);
        $('#floor_modal textarea[name="description"]').val(description);
    }
</script>
</body>

</html>