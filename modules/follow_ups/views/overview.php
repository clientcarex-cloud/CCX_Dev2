<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    .overview-section {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #f1f5f9;
        transition: transform 0.2s;
    }

    .overview-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
    }

    .overview-header {
        border-bottom: 2px solid #f8fafc;
        padding-bottom: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .overview-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.025em;
    }

    .overview-header i {
        color: #3b82f6;
        font-size: 20px;
        background: #eff6ff;
        padding: 8px;
        border-radius: 8px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 4px;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 15px;
        color: #334155;
        font-weight: 500;
        line-height: 1.5;
    }

    .info-value.empty {
        color: #94a3b8;
        font-style: italic;
        font-size: 14px;
    }

    .tag-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    .tag-blue {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .tag-green {
        background: #f0fdf4;
        color: #15803d;
    }

    .tag-purple {
        background: #faf5ff;
        color: #7e22ce;
    }
</style>

<div class="row">
    <!-- Patient Details -->
    <div class="col-md-12">
        <div class="overview-section">
            <div class="overview-header">
                <i class="fa fa-user-circle-o"></i>
                <h3>Patient Details</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Full Name</span>
                    <span class="info-value">
                        <?php
                        $title = !empty($patient->title) ? $patient->title . ' ' : '';
                        echo $title . (!empty($patient->full_name) ? $patient->full_name : '<span class="empty">N/A</span>');
                        ?>
                    </span>
                </div>
                <!-- Title merged into Name -->
                <div class="info-item">
                    <span class="info-label">Gender</span>
                    <span class="info-value">
                        <?php echo !empty($patient->gender) ? ucfirst($patient->gender) : '<span class="empty">N/A</span>'; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Age</span>
                    <span class="info-value">
                        <?php echo !empty($patient->age) ? $patient->age . ' ' . $patient->age_unit : '<span class="empty">N/A</span>'; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">MR Number</span>
                    <span class="info-value">
                        <span
                            class="tag-badge tag-blue"><?php echo !empty($patient->mr_number) ? $patient->mr_number : 'N/A'; ?></span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">UID / Aadhaar</span>
                    <span class="info-value">
                        <?php echo !empty($patient->uid_no) ? $patient->uid_no : '<span class="empty">Not Provided</span>'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Details -->
    <div class="col-md-6">
        <div class="overview-section" style="min-height: 220px;">
            <div class="overview-header">
                <i class="fa fa-address-card-o" style="color: #059669; background: #ecfdf5;"></i>
                <h3>Contact Information</h3>
            </div>
            <div class="info-grid" style="grid-template-columns: 1fr;">
                <div class="info-item">
                    <span class="info-label">Phone Number</span>
                    <span class="info-value">
                        <?php if (!empty($patient->phonenumber)) {
                            $len = strlen($patient->phonenumber);
                            $masked = $patient->phonenumber;
                            if ($len > 4) {
                                $masked = str_repeat('*', $len - 4) . substr($patient->phonenumber, -4);
                            }
                            ?>
                            <div style="display: flex; align-items: center;">
                                <i class="fa fa-phone text-success" style="margin-right: 8px;"></i>
                                <span class="masked-content" data-real="<?php echo $patient->phonenumber; ?>"
                                    style="font-family: monospace; font-size: 15px; letter-spacing: 1px;">
                                    <?php echo $masked; ?>
                                </span>
                                <a href="#" class="toggle-mask text-muted" style="margin-left: 10px; font-size: 14px;">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </div>
                        <?php } else {
                            echo '<span class="empty">N/A</span>';
                        } ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email Address</span>
                    <span class="info-value">
                        <?php if (!empty($patient->email)) { ?>
                            <a href="mailto:<?php echo $patient->email; ?>"><?php echo $patient->email; ?></a>
                        <?php } else {
                            echo '<span class="empty">N/A</span>';
                        } ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Address</span>
                    <span class="info-value">
                        <?php echo !empty($patient->address) ? nl2br($patient->address) : '<span class="empty">No address on file</span>'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Details -->
    <div class="col-md-6">
        <div class="overview-section" style="min-height: 220px;">
            <div class="overview-header">
                <i class="fa fa-info-circle" style="color: #7c3aed; background: #f5f3ff;"></i>
                <h3>Other Information</h3>
            </div>
            <div class="info-grid" style="grid-template-columns: 1fr;">
                <div class="info-item">
                    <span class="info-label">Reference (Referral Doctor)</span>
                    <span class="info-value">
                        <?php
                        if (!empty($referral_doctor_name)) {
                            echo $referral_doctor_name;
                        } else {
                            echo '<span class="empty">None</span>';
                        }
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Attender Name</span>
                    <span class="info-value">
                        <?php echo !empty($patient->attender_name) ? $patient->attender_name : '<span class="empty">-</span>'; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Registration Date</span>
                    <span class="info-value">
                        <?php echo _d($patient->datecreated); ?>
                    </span>
                </div>
                <!-- Visits -->
                <div class="info-item">
                    <span class="info-label">Total Visits</span>
                    <span class="info-value">
                        <span
                            class="tag-badge tag-green"><?php echo isset($patient->visits_count) ? $patient->visits_count : 0; ?>
                            Visits</span>
                        <?php if (!empty($patient->latest_visit_code)) { ?>
                            <span class="text-muted" style="font-size: 13px; margin-left: 5px;">(Latest:
                                <?php echo $patient->latest_visit_code; ?>)</span>
                        <?php } ?>
                    </span>
                </div>
                <!-- Groups -->
                <?php if (!empty($patient->customer_groups)) { ?>
                    <div class="info-item">
                        <span class="info-label">Groups</span>
                        <span class="info-value">
                            <?php
                            $groups = explode(',', $patient->customer_groups);
                            foreach ($groups as $group) {
                                echo '<span class="tag-badge tag-purple" style="margin-right: 5px; margin-bottom: 5px;">' . trim($group) . '</span>';
                            }
                            ?>
                        </span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>