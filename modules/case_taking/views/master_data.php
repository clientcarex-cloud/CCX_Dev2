<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>
                            <a href="<?php echo admin_url('case_taking/settings'); ?>"
                                class="btn btn-default pull-left mright10"><i class="fa fa-angle-left"></i> Back</a>
                            Manage
                            <?php echo ucfirst(str_replace('_', ' ', $type)); ?>
                        </h4>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <a href="#" class="btn btn-info mbottom15" onclick="new_item(); return false;">New Item</a>

                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="0" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item) { ?>
                                        <tr>
                                            <td>
                                                <?php echo $item['name']; ?>
                                            </td>
                                            <td>
                                                <a href="#"
                                                    onclick="edit_item(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>'); return false;"
                                                    class="btn btn-default btn-icon"><i
                                                        class="fa fa-pencil-square-o"></i></a>
                                                <a href="<?php echo admin_url('case_taking/delete_master_data/' . $item['id']); ?>"
                                                    class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
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

<!-- Modal -->
<div class="modal fade" id="item_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(current_url()); ?>
        <input type="hidden" name="id" id="item_id">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Item</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="control-label">Name</label>
                    <input type="text" class="form-control" name="name" id="item_name" required>
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
    function new_item() {
        $('#item_id').val('');
        $('#item_name').val('');
        $('#item_modal').modal('show');
    }

    function edit_item(id, name) {
        $('#item_id').val(id);
        $('#item_name').val(name);
        $('#item_modal').modal('show');
    }
</script>
</body>

</html>