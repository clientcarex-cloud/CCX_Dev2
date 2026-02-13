<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <!-- Doctor Stats Cards -->
                        <div class="row mbot15">
                            <style>
                                .doctor-stats-card {
                                    background: #fff;
                                    border-radius: 12px;
                                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                                    /* Soft shadow */
                                    padding: 20px;
                                    display: flex;
                                    /* Flex layout */
                                    align-items: center;
                                    /* Center items vertically */
                                    justify-content: space-between;
                                    /* Space out icon/text */
                                    height: 100%;
                                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                                    border: 1px solid rgba(0, 0, 0, 0.03);
                                }

                                .doctor-stats-card:hover {
                                    transform: translateY(-5px);
                                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                                }

                                .doctor-stats-icon {
                                    width: 50px;
                                    height: 50px;
                                    border-radius: 12px;
                                    /* Soft square */
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 24px;
                                    margin-right: 15px;
                                    /* Spacing between icon and text */
                                }

                                .doctor-stats-info {
                                    flex-grow: 1;
                                }

                                .doctor-stats-title {
                                    font-size: 14px;
                                    font-weight: 600;
                                    color: #64748b;
                                    /* Slate 500 */
                                    margin-bottom: 5px;
                                    text-transform: uppercase;
                                    letter-spacing: 0.5px;
                                }

                                .doctor-stats-number {
                                    font-size: 28px;
                                    font-weight: 800;
                                    color: #1e293b;
                                    /* Slate 800 */
                                    line-height: 1;
                                }

                                .doctor-stats-subtext {
                                    font-size: 12px;
                                    color: #94a3b8;
                                    /* Slate 400 */
                                    margin-top: 5px;
                                    font-weight: 500;
                                }

                                /* Stats Colors */
                                .stat-total .doctor-stats-icon {
                                    background: #eff6ff;
                                    color: #2563eb;
                                }

                                /* Blue */
                                .stat-consultant .doctor-stats-icon {
                                    background: #f0fdf4;
                                    color: #16a34a;
                                }

                                /* Green */
                                .stat-referral .doctor-stats-icon {
                                    background: #fefce8;
                                    color: #ca8a04;
                                }

                                /* Yellow */
                                .stat-oncall .doctor-stats-icon {
                                    background: #fdf2f8;
                                    color: #db2777;
                                }

                                /* Pink */

                                /* Adjust grid for cards */
                                .stats-grid-col {
                                    margin-bottom: 20px;
                                }
                            </style>

                            <!-- Total Doctors -->
                            <div class="col-md-3 stats-grid-col">
                                <div class="doctor-stats-card stat-total">
                                    <div class="doctor-stats-icon">
                                        <i class="fa fa-user-md"></i>
                                    </div>
                                    <div class="doctor-stats-info">
                                        <div class="doctor-stats-title">Total Doctors</div>
                                        <div class="doctor-stats-number"><?php echo $stats['total']['count']; ?></div>
                                        <div class="doctor-stats-subtext">
                                            <span class="text-success"><?php echo $stats['total']['active']; ?>
                                                Active</span> &bull;
                                            <span class="text-danger"><?php echo $stats['total']['inactive']; ?>
                                                Inactive</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Consultant Doctors -->
                            <div class="col-md-3 stats-grid-col">
                                <div class="doctor-stats-card stat-consultant">
                                    <div class="doctor-stats-icon">
                                        <i class="fa fa-stethoscope"></i>
                                    </div>
                                    <div class="doctor-stats-info">
                                        <div class="doctor-stats-title">Consultant</div>
                                        <div class="doctor-stats-number"><?php echo $stats['consultant']['count']; ?>
                                        </div>
                                        <div class="doctor-stats-subtext">
                                            <?php echo $stats['consultant']['new_this_month']; ?> New This Month
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Referral Doctors -->
                            <div class="col-md-3 stats-grid-col">
                                <div class="doctor-stats-card stat-referral">
                                    <div class="doctor-stats-icon">
                                        <i class="fa fa-share-alt"></i>
                                    </div>
                                    <div class="doctor-stats-info">
                                        <div class="doctor-stats-title">Referral</div>
                                        <div class="doctor-stats-number"><?php echo $stats['referral']['count']; ?>
                                        </div>
                                        <div class="doctor-stats-subtext">
                                            <?php echo $stats['referral']['new_this_month']; ?> New This Month
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- On-Call Doctors -->
                            <div class="col-md-3 stats-grid-col">
                                <div class="doctor-stats-card stat-oncall">
                                    <div class="doctor-stats-icon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <div class="doctor-stats-info">
                                        <div class="doctor-stats-title">On-Call</div>
                                        <div class="doctor-stats-number"><?php echo $stats['on_call']['count']; ?></div>
                                        <div class="doctor-stats-subtext">
                                            <?php echo $stats['on_call']['new_this_month']; ?> New This Month
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mbot15">
                            <div class="col-md-6">
                                <h3 class="no-margin">Doctor List</h3>
                                <p class="text-muted">Manage consultant doctor details and their status.</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default btn-xs active"
                                        onclick="dt_custom_view('1','.table-doctors','status'); return false;">
                                        <input type="radio" name="options" id="option1" autocomplete="off" checked>
                                        Active
                                    </label>
                                    <label class="btn btn-default btn-xs"
                                        onclick="dt_custom_view('0','.table-doctors','status'); return false;">
                                        <input type="radio" name="options" id="option2" autocomplete="off"> Inactive
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mbot15">
                            <div class="col-md-3">
                                <select class="form-control selectpicker" id="specialization_filter"
                                    data-live-search="true" title="All Specializations">
                                    <option value="">All Specializations</option>
                                    <?php
                                    // Fetch unique specializations for filter
                                    // This logic might be better in controller and passed here
                                    $CI =& get_instance();
                                    $specializations = $CI->db->select('DISTINCT(specialization)')->where('specialization !=', '')->where('specialization IS NOT NULL', null, false)->get(db_prefix() . 'staff')->result_array();
                                    foreach ($specializations as $spec) {
                                        echo '<option value="' . $spec['specialization'] . '">' . $spec['specialization'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control selectpicker" id="profile_type_filter"
                                    title="All Profile Types">
                                    <option value="">All Profile Types</option>
                                    <option value="Consultant">Consultant</option>
                                    <option value="Referral">Referral</option>
                                    <option value="On-Call">On-Call</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('from_date', '', '', ['placeholder' => 'From Date']); ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('to_date', '', '', ['placeholder' => 'To Date']); ?>
                            </div>

                            <!-- Hidden input to store active status filter state -->
                            <input type="hidden" name="status" value="1">

                            <div class="col-md-2 text-right">
                                <?php if (has_permission('doctors', '', 'create')) { ?>
                                    <a href="<?php echo admin_url('doctors/member'); ?>" class="btn btn-primary btn-block">
                                        <i class="fa fa-plus"></i> Add
                                    </a>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('Doctor Name'),
                            _l('Specialization'),
                            _l('Profile Type'),
                            _l('Contact'),
                            _l('Status'),
                            _l('Referral'),
                            _l('Actions'),
                        ], 'doctors'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        // Server Params for DataTables
        var DoctorsServerParams = {
            "status": "[name='status']",
            "profile_type": "[id='profile_type_filter']",
            "from_date": "[name='from_date']",
            "to_date": "[name='to_date']"
        };

        var DoctorsTable = initDataTable('.table-doctors', window.location.href, [6], [6], DoctorsServerParams, [0, 'asc']);

        // Specialization Filter
        $('#specialization_filter').on('change', function () {
            DoctorsTable.column(1).search(this.value).draw();
        });

        // Filters reloading
        $('#specialization_filter, #profile_type_filter').on('change', function () {
            DoctorsTable.ajax.reload();
        });

        $('input[name="from_date"], input[name="to_date"]').on('change', function () {
            DoctorsTable.ajax.reload();
        });

        // Toggle Status functionality
        $('input[name="options"]').on('change', function () {
            var val = $(this).attr('id') == 'option1' ? 1 : 0; // option1 is Active (1), option2 is Inactive (0)
            $('input[name="status"]').val(val);
            $('.table-doctors').DataTable().ajax.reload();
        });
    });
</script>
</body>

</html>