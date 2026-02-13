<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('medicines', '', 'create')) { ?>
                            <a href="#" onclick="new_medicine(); return false;"
                                class="btn btn-info pull-left display-block">
                                <?php echo _l('new_medicine'); ?>
                            </a>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <table class="table dt-table" data-order-col="1" data-order-type="desc">
                            <thead>
                                <tr>
                                    <th><?php echo _l('id'); ?></th>
                                    <th><?php echo _l('medicine_name'); ?></th>
                                    <th><?php echo _l('unit'); ?></th>
                                    <th><?php echo _l('medicine_price'); ?></th>
                                    <th><?php echo _l('medicine_description'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($medicines as $medicine) { ?>
                                    <tr>
                                        <td><?php echo $medicine->id; ?></td>
                                        <td><a href="#"
                                                onclick="edit_medicine(<?php echo $medicine->id; ?>); return false;"><?php echo $medicine->description; ?></a>
                                        </td>
                                        <td><?php echo $medicine->unit; ?></td>
                                        <!-- Using rate as Quantity or Price? Usually rate is Price. Custom field for Stock? -->
                                        <!-- Standard items table has: description(name), long_description, rate(price), unit... no stock quantity usually unless inventory module.
                                         The user asked basically for a "Medicines" list. I'll stick to displaying what I have. 
                                         Correction: Standard items don't have Quantity column. I will just show Rate for now as Price.
                                    -->
                                        <td><?php echo app_format_money($medicine->rate, get_base_currency()); ?></td>
                                        <td><?php echo $medicine->long_description; ?></td>
                                        <td>
                                            <a href="#" onclick="edit_medicine(<?php echo $medicine->id; ?>); return false;"
                                                class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                                            <a href="<?php echo admin_url('medicines/delete/' . $medicine->id); ?>"
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

<!-- Modal -->
<div class="modal fade" id="medicine_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('medicines')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('new_medicine'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="id" value="">
                        <?php echo render_input('name', 'medicine_name'); ?>
                        <?php echo render_input('price', 'medicine_price', '', 'number'); ?>
                        <?php echo render_input('unit', 'unit'); ?>
                        <?php echo render_select('type', $medicine_types, ['id', 'name'], 'medicine_type'); ?>

                        <?php echo render_textarea('instruction', 'instruction'); ?>

                        <?php echo render_textarea('long_description', 'medicine_description'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php init_tail(); ?>
<script>
    function new_medicine() {
        $('#medicine_modal').modal('show');
        $('.modal-title').text('<?php echo _l('new_medicine'); ?>');
        $('#medicine_modal input').val('');
        $('#medicine_modal textarea').val('');
        $('#medicine_modal select').val('').change();
    }

    function edit_medicine(id) {
        $.get(admin_url + 'medicines/json/' + id, function (response) {
            response = JSON.parse(response);
            $('#medicine_modal input[name="name"]').val(response.description);
            $('#medicine_modal input[name="price"]').val(response.rate);
            $('#medicine_modal input[name="unit"]').val(response.unit);
            $('#medicine_modal select[name="type"]').val(response.type).change();
            $('#medicine_modal textarea[name="instruction"]').val(response.instruction);
            $('#medicine_modal textarea[name="long_description"]').val(response.long_description);
            $('#medicine_modal input[name="id"]').val(response.id);

            $('.modal-title').text('<?php echo _l('edit_medicine'); ?>');
            $('#medicine_modal').modal('show');
        });
    }
</script>
</body>

</html>