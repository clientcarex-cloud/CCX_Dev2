<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" onclick="new_building(); return false;"
                                class="btn btn-info pull-left display-block">
                                <?php echo _l('new_building'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>

                        <div class="row">
                            <?php foreach ($buildings as $building) { ?>
                                <div class="col-md-4">
                                    <div class="panel_s">
                                        <div class="panel-body">
                                            <h4 class="no-margin">
                                                <a href="<?php echo admin_url('rooms/building/' . $building->id); ?>">
                                                    <?php echo $building->name; ?>
                                                </a>
                                            </h4>
                                            <p class="text-muted">
                                                <?php echo $building->description; ?>
                                            </p>
                                            <hr />
                                            <div class="text-right">
                                                <a href="#"
                                                    onclick="edit_building(<?php echo $building->id; ?>, '<?php echo $building->name; ?>', '<?php echo $building->description; ?>'); return false;"
                                                    class="btn btn-default btn-icon"><i
                                                        class="fa fa-pencil"></i></a>
                                                <a href="<?php echo admin_url('rooms/delete_building/' . $building->id); ?>"
                                                    class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (count($buildings) == 0) { ?>
                                <div class="col-md-12">
                                    <p class="text-muted text-center">
                                        <?php echo _l('no_buildings_found'); ?>
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

<!-- Building Modal -->
<div class="modal fade" id="building_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('new_building'); ?>
                </h4>
            </div>
            <?php echo form_open(admin_url('rooms/add_building'), array('id' => 'building-form')); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">
                        <?php echo _l('name'); ?>
                    </label>
                    <input type="text" class="form-control" id="name" name="name" required>
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
    function new_building() {
        $('#building_modal').modal('show');
        $('#building_modal .modal-title').text('<?php echo _l('new_building'); ?>');
        $('#building_modal form').attr('action', '<?php echo admin_url('rooms/add_building'); ?>');
        $('#building_modal input[name="name"]').val('');
        $('#building_modal textarea[name="description"]').val('');
    }

    function edit_building(id, name, description) {
        $('#building_modal').modal('show');
        $('#building_modal .modal-title').text('<?php echo _l('edit_building'); ?>');
        $('#building_modal form').attr('action', '<?php echo admin_url('rooms/update_building/'); ?>' + id);
        $('#building_modal input[name="name"]').val(name);
        $('#building_modal textarea[name="description"]').val(description);
    }
</script>
</body>

</html>