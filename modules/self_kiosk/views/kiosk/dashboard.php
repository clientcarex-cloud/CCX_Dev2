<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo _l('kiosk_dashboard'); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }

        .header {
            background-color: white;
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .logo img {
            max-height: 40px;
            width: auto;
        }

        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #111827;
        }

        .header .logout {
            color: #ef4444;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .header .logout:hover {
            color: #dc2626;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 1rem;
            padding: 2.5rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .welcome-banner h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .welcome-banner p {
            margin: 0.5rem 0 0;
            font-size: 1.125rem;
            opacity: 0.9;
        }

        .switch-member-link {
            display: inline-flex;
            align-items: center;
            margin-top: 1rem;
            color: white;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2rem;
            transition: background 0.2s;
        }

        .switch-member-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .switch-member-link i {
            margin-right: 0.5rem;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .service-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            text-decoration: none;
            color: inherit;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .icon-wrapper {
            width: 64px;
            height: 64px;
            background-color: #eff6ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: #2563eb;
            font-size: 1.5rem;
        }

        .service-card h3 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
        }

        .service-card p {
            margin: 0.5rem 0 0;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .footer {
            background-color: white;
            padding: 1.5rem 2rem;
            margin-top: 3rem;
            box-shadow: 0 -1px 3px 0 rgba(0, 0, 0, 0.1), 0 -1px 2px 0 rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .branding-container {
            position: relative;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .branding-container span {
            font-size: 11px;
            color: #7e8c9d;
            font-weight: 500;
            margin-right: 6px;
            letter-spacing: 0.3px;
        }

        .branding-container img {
            height: 20px;
            width: auto;
        }

        .healtho-card {
            display: none;
            position: absolute;
            bottom: 150%;
            right: 0;
            width: 280px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            padding: 20px;
            z-index: 1000;
            border: 1px solid #eee;
            text-align: left;
            color: #1f2937;
        }

        .healtho-card::after {
            content: '';
            position: absolute;
            top: 100%;
            right: 20px;
            border-width: 8px;
            border-style: solid;
            border-color: #fff transparent transparent transparent;
        }

        .healtho-card h5 {
            margin: 0 0 15px 0;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .healtho-card-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #4b5563;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .healtho-card-item:hover {
            color: #2563eb;
        }

        .healtho-card-item i {
            width: 25px;
            color: #2563eb;
            font-size: 14px;
        }

        @media (max-width: 640px) {
            .footer {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
                padding-bottom: 2rem;
            }

            .branding-container {
                justify-content: center;
            }

            .healtho-card {
                left: 50%;
                transform: translateX(-50%);
                right: auto;
                width: calc(100vw - 40px);
                max-width: 280px;
                bottom: 120%;
            }

            .healtho-card::after {
                left: 50%;
                margin-left: -8px;
                right: auto;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <?php get_company_logo('self_kiosk/kiosk/dashboard'); ?>
        <a href="<?php echo site_url('self_kiosk/kiosk/logout'); ?>" class="logout">
            <i class="fas fa-sign-out-alt"></i> <?php echo _l('logout'); ?>
        </a>
    </div>

    <div class="container">
        <?php if ($this->session->flashdata('message-success')): ?>
            <div class="alert alert-success mbot15"
                style="padding: 1rem; border-radius: 0.75rem; background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
                <?php echo $this->session->flashdata('message-success'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('message-danger')): ?>
            <div class="alert alert-danger mbot15"
                style="padding: 1rem; border-radius: 0.75rem; background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">
                <?php echo $this->session->flashdata('message-danger'); ?>
            </div>
        <?php endif; ?>

        <div class="welcome-banner">
            <h2><?php echo _l('welcome_patient', (isset($patient->full_name) ? $patient->full_name : 'Patient')); ?>
            </h2>
            <p><?php echo _l('how_can_we_help'); ?></p>

            <?php if (!empty($family_members)): ?>
                <?php
                // Save family members to session again just in case they were lost, 
                // but select_patient needs them in session.
                $this->session->set_userdata('kiosk_found_patients', $family_members);
                ?>
                <a href="<?php echo site_url('self_kiosk/kiosk/select_patient'); ?>" class="switch-member-link">
                    <i class="fas fa-users-cog"></i> <?php echo _l('switch_member'); ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="services-grid">
            <!-- 1. Update Your Info -->
            <?php if (!isset($settings['update_info']) || (isset($settings['update_info']['view']) && $settings['update_info']['view'] == "1") || $settings['update_info'] == "1") { ?>
                <a href="<?php echo site_url('self_kiosk/kiosk/profile'); ?>" class="service-card">
                    <div class="icon-wrapper"><i class="fas fa-user-edit"></i></div>
                    <h3><?php echo _l('update_info'); ?></h3>
                    <p><?php echo _l('update_info_desc'); ?></p>
                </a>
            <?php } ?>

            <!-- 2. View All Visits -->
            <?php if (!isset($settings['visits']) || (isset($settings['visits']['view']) && $settings['visits']['view'] == "1") || $settings['visits'] == "1") { ?>
                <a href="<?php echo site_url('self_kiosk/kiosk/visits'); ?>" class="service-card">
                    <div class="icon-wrapper"><i class="fas fa-history"></i></div>
                    <h3><?php echo _l('view_visits'); ?></h3>
                    <p><?php echo _l('view_visits_desc'); ?></p>
                </a>
            <?php } ?>

            <!-- 3. Your Appointments -->
            <?php if (!isset($settings['appointments']) || (isset($settings['appointments']['view']) && $settings['appointments']['view'] == "1") || $settings['appointments'] == "1") { ?>
                <a href="<?php echo site_url('self_kiosk/kiosk/appointments'); ?>" class="service-card">
                    <div class="icon-wrapper"><i class="fas fa-calendar-check"></i></div>
                    <h3><?php echo _l('your_appointments'); ?></h3>
                    <p><?php echo _l('manage_appointments'); ?></p>
                </a>
            <?php } ?>


            <!-- 8. Feedback/Suggestions -->
            <?php if (!isset($settings['feedback']) || (isset($settings['feedback']['view']) && $settings['feedback']['view'] == "1") || $settings['feedback'] == "1") { ?>
                <a href="<?php echo site_url('self_kiosk/kiosk/feedback'); ?>" class="service-card">
                    <div class="icon-wrapper"><i class="fas fa-comment-dots"></i></div>
                    <h3><?php echo _l('feedback_suggestions'); ?></h3>
                    <p><?php echo _l('feedback_suggestions_desc'); ?></p>
                </a>
            <?php } ?>

            <!-- 9. Google Map Review -->
            <?php if (!isset($settings['google_review']) || (isset($settings['google_review']['view']) && $settings['google_review']['view'] == "1") || $settings['google_review'] == "1") { ?>
                <?php $google_review_link = (isset($settings['google_review']['link']) && !empty($settings['google_review']['link'])) ? $settings['google_review']['link'] : '#'; ?>
                <a href="<?php echo $google_review_link; ?>" class="service-card" <?php echo $google_review_link != '#' ? 'target="_blank"' : ''; ?>>
                    <div class="icon-wrapper"><i class="fab fa-google"></i></div>
                    <h3><?php echo _l('google_review'); ?></h3>
                    <p><?php echo _l('google_review_desc'); ?></p>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="footer">
        <div class="copyright">
            Copyright &copy; <?php echo date('Y'); ?> Healthocare Private Limited. All rights reserved.
        </div>
        <div class="branding-container" id="brandingTrigger">
            <span>Powered by</span>
            <img src="<?php echo base_url('modules/web_integration/assets/images/healtho_logo.png'); ?>" alt="HealthO">

            <div class="healtho-card" id="healthoCard">
                <h5>Get our service for your Healthcare Brand</h5>
                <a href="tel:+919700730044" class="healtho-card-item">
                    <i class="fas fa-phone-alt"></i> +91 9700730044
                </a>
                <a href="mailto:Sales@healtho.in" class="healtho-card-item">
                    <i class="fas fa-envelope"></i> Sales@healtho.in
                </a>
                <a href="https://healtho.in" target="_blank" class="healtho-card-item">
                    <i class="fas fa-globe"></i> healtho.in
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#brandingTrigger').on('click', function (e) {
                e.stopPropagation();
                $('#healthoCard').fadeToggle(200);
            });

            $(document).on('click', function () {
                $('#healthoCard').fadeOut(200);
            });

            $('#healthoCard').on('click', function (e) {
                e.stopPropagation();
            });
        });
    </script>
</body>

</html>