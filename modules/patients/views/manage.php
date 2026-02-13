<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <style>
                .patient-stat-card {
                    background: #fff;
                    border-radius: 12px;
                    padding: 20px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    position: relative;
                    overflow: hidden;
                    border: none;
                    margin-bottom: 20px;
                    color: #fff;
                    /* Default text color for gradient cards */
                }

                .patient-stat-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                }

                .patient-stat-card .stat-icon {
                    position: absolute;
                    right: -10px;
                    bottom: -10px;
                    font-size: 80px;
                    opacity: 0.15;
                    transform: rotate(-15deg);
                }

                .patient-stat-card h3 {
                    font-size: 32px;
                    font-weight: 700;
                    margin: 0 0 5px 0;
                    color: inherit;
                }

                .patient-stat-card span {
                    font-size: 14px;
                    font-weight: 500;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    opacity: 0.9;
                }

                /* Gradient Themes */
                .stat-card-info {
                    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                    /* Greenish Teal */
                }

                .stat-card-success {
                    background: linear-gradient(135deg, #3a7bd5 0%, #3a6073 100%);
                    /* Blue */
                }

                .stat-card-warning {
                    background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
                    /* Orange/Yellow */
                }

                .stat-card-primary {
                    background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%);
                    /* Purple */
                }
            </style>

            <div class="col-md-3">
                <div class="patient-stat-card stat-card-info">
                    <div class="stat-content">
                        <h3 class="no-margin"><?php echo $dashboard_stats['total_patients']; ?></h3>
                        <span>Total Patients</span>
                    </div>
                    <i class="fa fa-users stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="patient-stat-card stat-card-success">
                    <div class="stat-content">
                        <h3 class="no-margin"><?php echo $dashboard_stats['total_visits']; ?></h3>
                        <span>Total Visits</span>
                    </div>
                    <i class="fa fa-calendar-check-o stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="patient-stat-card stat-card-warning">
                    <div class="stat-content">
                        <h3 class="no-margin"><?php echo $dashboard_stats['new_patients_month']; ?></h3>
                        <span>New (This Month)</span>
                    </div>
                    <i class="fa fa-user-plus stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="patient-stat-card stat-card-primary">
                    <div class="stat-content">
                        <h3 class="no-margin"><?php echo $dashboard_stats['avg_visits']; ?></h3>
                        <span>Avg. Visits / Patient</span>
                    </div>
                    <i class="fa fa-heartbeat stat-icon"></i>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">



                        <!-- Removed form tag to prevent submit on enter, inputs act as filters now -->
                        <!-- Filters removed as per request -->


                        <div class="clearfix"></div>
                        <div class="clearfix"></div>

                        <?php
                        $table_data = [
                            'S.no',
                            'Patient Name & Date',
                            'MR No & Visits',
                            'Contact Info',
                            'Age / DOB',
                            'Since Patient',
                            'Branch'
                        ];
                        render_datatable($table_data, 'patients');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (is_admin()) { ?>
        <a href="<?php echo admin_url('patients/settings'); ?>" class="btn btn-info btn-icon"
            style="position: fixed; bottom: 25px; right: 25px; z-index: 9999; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
            <i class="fa fa-cogs" style="font-size: 24px;"></i>
        </a>
    <?php } ?>


    <?php init_tail(); ?>
    <script>
        $(document).ready(function () {
            initDataTable('.table-patients', '<?php echo admin_url('patients/table'); ?>', undefined, undefined, 'undefined', [0, 'desc']);

            // Unmasking Logic using Event Delegation
            $(document).on('click', '.toggle-mask', function (e) {
                e.preventDefault();
                var btn = $(this);
                var container = btn.prev('.masked-content');
                var realValue = String(container.data('real')); // Ensure string for numeric phone numbers
                var currentValue = container.text().trim();

                // Determine if currently masked (contains *)
                if (currentValue.indexOf('*') !== -1) {
                    // Unmask
                    container.text(realValue);
                    btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    // Re-mask logic
                    var masked = '';
                    if (realValue.indexOf('@') !== -1) {
                        // Email masking
                        var parts = realValue.split('@');
                        if (parts.length == 2) {
                            masked = parts[0].substring(0, 2) + '****@' + parts[1];
                        } else {
                            masked = realValue;
                        }
                    } else {
                        // Phone masking
                        masked = (realValue.length > 4) ? realValue.substring(0, 2) + '******' + realValue.substring(realValue.length - 2) : realValue;
                    }
                    container.text(masked);
                    btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        });
    </script>
    <?php $this->load->view('patient_master_modal/modal_content'); ?>
    <script
        src="<?php echo module_dir_url('patient_master_modal', 'assets/js/script.js') . '?v=' . time(); ?>"></script>
    </body>

    </html>