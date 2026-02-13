<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string()); ?>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <?php $value = (isset($coupon) ? $coupon->name : ''); ?>
                                <?php echo render_input('name', 'coupon_name', $value); ?>
                                
                                <?php $value = (isset($coupon) ? $coupon->code : ''); ?>
                                <?php echo render_input('code', 'coupon_code', $value); ?>
                                
                                <div class="form-group">
                                    <label for="type"><?php echo _l('coupon_type'); ?></label>
                                    <select name="type" id="type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="1" <?php if (isset($coupon) && $coupon->type == 1) { echo 'selected'; } ?>>Percentage</option>
                                        <option value="2" <?php if (isset($coupon) && $coupon->type == 2) { echo 'selected'; } ?>>Fixed Amount</option>
                                        <option value="3" <?php if (isset($coupon) && $coupon->type == 3) { echo 'selected'; } ?>>Price Cap</option>
                                    </select>
                                </div>
                                
                                <!-- Price Cap Settings -->
                                <?php 
                                    $min_total = '';
                                    $max_total = '';
                                    $discount_mode = '1'; // Default Percentage
                                    if(isset($coupon) && $coupon->type == 3 && !empty($coupon->type_settings)) {
                                        $settings = json_decode($coupon->type_settings, true);
                                        $min_total = $settings['min_total'] ?? '';
                                        $max_total = $settings['max_total'] ?? '';
                                        $discount_mode = $settings['discount_mode'] ?? '1';
                                    }
                                    
                                    $hide_price_cap = (isset($coupon) && $coupon->type == 3) ? '' : 'hide';
                                ?>
                                <div id="price_cap_wrapper" class="<?php echo $hide_price_cap; ?>">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php echo render_input('min_total', 'coupon_min_total', $min_total, 'number'); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php echo render_input('max_total', 'coupon_max_total', $max_total, 'number'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="discount_mode"><?php echo _l('coupon_discount_mode'); ?></label>
                                        <select name="discount_mode" id="discount_mode" class="selectpicker" data-width="100%">
                                            <option value="1" <?php if ($discount_mode == 1) { echo 'selected'; } ?>>Percentage</option>
                                            <option value="2" <?php if ($discount_mode == 2) { echo 'selected'; } ?>>Fixed Amount</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="amount_wrapper">
                                    <?php $value = (isset($coupon) ? $coupon->amount : ''); ?>
                                    <?php echo render_input('amount', 'coupon_amount', $value, 'number'); ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $value = (isset($coupon) ? _d($coupon->start_date) : ''); ?>
                                        <?php echo render_date_input('start_date', 'coupon_start_date', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $value = (isset($coupon) ? _d($coupon->end_date) : ''); ?>
                                        <?php echo render_date_input('end_date', 'coupon_end_date', $value); ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $value = (isset($coupon) ? $coupon->max_uses : ''); ?>
                                        <?php echo render_input('max_uses', 'coupon_max_uses', $value, 'number'); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $value = (isset($coupon) ? $coupon->max_uses_per_client : ''); ?>
                                        <?php echo render_input('max_uses_per_client', 'coupon_max_uses_per_client', $value, 'number'); ?>
                                    </div>
                                </div>

                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="active" id="active" <?php if (isset($coupon) && $coupon->active == 1) { echo 'checked'; } elseif (!isset($coupon)) { echo 'checked'; } ?>>
                                    <label for="active"><?php echo _l('coupon_active'); ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        $('#type').on('change', function() {
            var type = $(this).val();
            if (type == 3) {
                $('#price_cap_wrapper').removeClass('hide');
            } else {
                $('#price_cap_wrapper').addClass('hide');
            }
            updateAmountLabel();
        });

        $('#discount_mode').on('change', function() {
            updateAmountLabel();
        });

        function updateAmountLabel() {
            var type = $('#type').val();
            var label = "<?php echo _l('coupon_amount'); ?>";
            
            if (type == 1) { // Percentage
                label = "<?php echo _l('coupon_amount_percentage'); ?>";
            } else if (type == 2) { // Fixed
                label = "<?php echo _l('coupon_amount_fixed'); ?>";
            } else if (type == 3) { // Price Cap
                // depends on sub-mode
                var subMode = $('#discount_mode').val();
                if (subMode == 1) {
                    label = "<?php echo _l('coupon_amount_percentage'); ?>";
                } else {
                    label = "<?php echo _l('coupon_amount_fixed'); ?>";
                }
            }
            
            $('label[for="amount"]').text(label);
        }
        
        // Init label
        updateAmountLabel();
    });
</script>
</body>
</html>