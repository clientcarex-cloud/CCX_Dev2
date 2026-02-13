<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <style>
                            .pndt-filters {
                                display: inline-flex;
                                background-color: #f1f5f9;
                                padding: 5px;
                                border-radius: 50px;
                                margin-bottom: 15px;
                            }

                            .pndt-filter-item {
                                padding: 5px 15px;
                                border-radius: 50px;
                                text-decoration: none !important;
                                color: #475569;
                                font-weight: 500;
                                font-size: 13px;
                                margin-right: 2px;
                                transition: all 0.2s;
                            }

                            .pndt-filter-item:hover {
                                color: #1e293b;
                            }

                            .pndt-filter-item.active {
                                background-color: #ffffff;
                                color: #0f172a;
                                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                                font-weight: 600;
                            }

                            .pndt-controls-wrapper .form-group {
                                margin-bottom: 0 !important;
                            }
                        </style>
                        <div class="pndt-controls-wrapper"
                            style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">

                            <!-- Tabs -->
                            <div class="pndt-filters" style="margin-bottom: 0;">
                                <?php
                                $statuses = ['All', 'Regular', 'Emergency', 'Processing', 'Completed'];
                                foreach ($statuses as $s) {
                                    $active = (isset($selected_status) && $selected_status == $s) ? 'active' : '';
                                    if (!isset($selected_status) && $s == 'All')
                                        $active = 'active';

                                    $params = $_GET;
                                    $params['status'] = $s;
                                    $url = admin_url('pndt?' . http_build_query($params));

                                    echo '<a href="' . $url . '" class="pndt-filter-item ' . $active . '">' . $s . '</a>';
                                }
                                ?>
                            </div>

                            <!-- Filters Form -->
                            <form method="get" action="<?php echo admin_url('pndt'); ?>">
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <div style="width: 140px;">
                                        <?php echo render_date_input('from_date', '', $filters['from_date'], ['placeholder' => 'From Date', 'onchange' => 'this.form.submit()']); ?>
                                    </div>
                                    <div style="width: 140px;">
                                        <?php echo render_date_input('to_date', '', $filters['to_date'], ['placeholder' => 'To Date', 'onchange' => 'this.form.submit()']); ?>
                                    </div>

                                    <?php
                                    $has_filters = !empty($filters['from_date']) || !empty($filters['to_date']);
                                    if ($has_filters) {
                                        ?>
                                        <div>
                                            <a href="<?php echo admin_url('pndt'); ?>" class="btn btn-default btn-icon"
                                                style="border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"><i
                                                    class="fa fa-remove"></i></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                        <hr class="hr-panel-heading" />

                        <table class="table dt-table" data-order-col="0" data-order-type="asc">
                            <thead>
                                <tr>
                                    <th>Sno.</th>
                                    <th>Patients</th>
                                    <th>Age & Gender</th>
                                    <th>Lab Test</th>
                                    <th>Ref. Doc</th>
                                    <th>Patient IDs</th>
                                    <th>Status</th>
                                    <th>PNDT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $key => $r) { ?>
                                    <tr>
                                        <td><?php echo $key + 1; ?>.</td>
                                        <td>
                                            <b>
                                                <?php
                                                if (isset($r['is_emergency']) && $r['is_emergency'] == 1) {
                                                    echo '🚨 ';
                                                }
                                                echo $r['patient_name'];
                                                ?>
                                            </b><br>
                                            <small class="text-muted">
                                                <?php echo _dt($r['created_at']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php echo $r['age']; ?><br>
                                            <?php echo $r['gender']; ?>
                                        </td>
                                        <td><b><?php echo $r['test_name']; ?></b></td>
                                        <td>
                                            <?php echo $r['ref_doc_name'] ? strtoupper($r['ref_doc_name']) : '-'; ?>
                                        </td>
                                        <td>
                                            <b>MR NO : <?php echo $r['mr_number']; ?></b><br>
                                            <b>VISIT ID : <?php echo $r['visit_code']; ?></b>
                                        </td>
                                        <td>
                                            <?php echo $r['status']; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-xs pndt-action-btn"
                                                onclick="pndt_form(<?php echo $r['id']; ?>)">PNDT</button>
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
<!-- Floating Settings Button -->
<a href="<?php echo admin_url('pndt/settings'); ?>" class="btn btn-default" style="
    position: fixed;
    bottom: 30px;
    right: 30px;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 1000;
    font-size: 24px;
    background: #fff;
    color: #475569;
">
    <i class="fa fa-cog"></i>
</a>
<div id="modal_wrapper"></div>
<?php init_tail(); ?>
<script>
    function pndt_form(id) {
        var url = admin_url + 'pndt/form';
        if (typeof (id) !== 'undefined') {
            url += '/' + id;
        }
        $.get(url, function (response) {
            $('#modal_wrapper').html(response);
            $('#pndt_form_modal').modal('show');
        });
    }
</script>