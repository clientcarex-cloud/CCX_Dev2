<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo _l('patient_check_in'); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card h2 {
            margin-top: 0;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .login-card p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-submit {
            width: 100%;
            background-color: #2563eb;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
        }

        .alert {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            padding: 1rem 2rem;
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
                padding: 1rem;
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
                bottom: 110%;
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

    <div class="login-card">
        <h2><?php echo _l('welcome'); ?></h2>
        <p><?php echo _l('enter_mobile_to_check_in'); ?></p>

        <?php if ($this->session->flashdata('message-danger')): ?>
            <div class="alert alert-danger">
                <?php echo $this->session->flashdata('message-danger'); ?>
            </div>
        <?php endif; ?>

        <?php echo form_open('self_kiosk/kiosk/login'); ?>
        <div class="form-group">
            <label for="mobile"><?php echo _l('mobile_number'); ?></label>
            <input type="tel" name="mobile" id="mobile" class="form-input" placeholder="e.g. 9876543210" required
                autofocus>
        </div>
        <button type="submit" class="btn-submit"><?php echo _l('check_in'); ?></button>
        <?php echo form_close(); ?>
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