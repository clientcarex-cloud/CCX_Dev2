<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" class="btn btn-info pull-left display-block" data-toggle="modal"
                                data-target="#type_modal">
                                <?php echo _l('New Card Plan'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <table class="table dt-table items" data-order-col="0" data-order-type="desc">
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo _l('ID'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('Plan Name'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('Price'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('Validity (Years)'); ?>
                                    </th>
                                    <th>
                                        <?php echo _l('options'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($types as $type) { ?>
                                    <tr>
                                        <td>
                                            <?php echo $type['id']; ?>
                                        </td>
                                        <td>
                                            <?php echo $type['name']; ?>
                                        </td>
                                        <td>
                                            <?php echo app_format_money($type['price'], get_base_currency()); ?>
                                        </td>
                                        <td>
                                            <?php echo $type['validity_years']; ?>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-default btn-icon" data-toggle="modal"
                                                data-target="#type_modal" data-id="<?php echo $type['id']; ?>"
                                                data-name="<?php echo $type['name']; ?>"
                                                data-price="<?php echo $type['price']; ?>"
                                                data-validity_years="<?php echo $type['validity_years']; ?>">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
                                            <a href="<?php echo admin_url('privilege_card/delete_type/' . $type['id']); ?>"
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
<?php $this->load->view('type_modal'); ?>
<?php init_tail(); ?>
<script>
    $(function () {
        $('#type_modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var modal = $(this);
            modal.find('.modal-title').text(id ? "<?php echo _l('Edit Card Plan'); ?>" : "<?php echo _l('New Card Plan'); ?>");
            modal.find('form').attr('action', id ? "<?php echo admin_url('privilege_card/type/'); ?>" + id : "<?php echo admin_url('privilege_card/type'); ?>");
            modal.find('#name').val(button.data('name'));
            modal.find('#price').val(button.data('price'));
            modal.find('#validity_years').val(button.data('validity_years'));
        });
    });
</script>
</body>

</html>