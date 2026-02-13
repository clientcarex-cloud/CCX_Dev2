<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Core CRM - Menu Visibility Settings</h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('core_crm')); ?>

                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-muted">Check the menus you want to <b>HIDE</b> from the admin sidebar.
                                </p>
                                <br>
                                <?php
                                $menus = [
                                    'estimate_request' => 'Estimate Requests',
                                    'contracts' => 'Contracts',
                                    'projects' => 'Projects',
                                    'sales' => 'Sales',
                                    'knowledge-base' => 'Knowledge Base',
                                    'utilities' => 'Utilities',
                                    'setup' => 'Setup',
                                    'subscriptions' => 'Subscriptions'
                                ];

                                foreach ($menus as $slug => $label) {
                                    $checked = (get_option('core_crm_hide_' . $slug) == '1') ? 'checked' : '';
                                    ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="hide_<?php echo $slug; ?>"
                                            id="hide_<?php echo $slug; ?>" <?php echo $checked; ?>>
                                        <label for="hide_<?php echo $slug; ?>">
                                            <?php echo $label; ?>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <hr />
                        <button type="submit" class="btn btn-info">Save Settings</button>
                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>