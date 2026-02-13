<div class="modal fade" id="type_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo _l('New Card Plan'); ?>
                </h4>
            </div>
            <?php echo form_open(admin_url('privilege_card/type')); ?>
            <div class="modal-body">
                <?php echo render_input('name', 'Plan Name'); ?>
                <?php echo render_input('price', 'Price', '', 'number'); ?>
                <?php echo render_input('validity_years', 'Validity (Years)', '', 'number'); ?>
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