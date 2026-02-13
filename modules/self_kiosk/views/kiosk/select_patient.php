<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php echo $title; ?>
    </title>
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
            min-height: 100vh;
            padding: 1rem;
        }

        .selection-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .selection-card h2 {
            margin-top: 0;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .selection-card p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .patient-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .patient-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            text-decoration: none;
            color: #111827;
            transition: all 0.2s;
            text-align: left;
        }

        .patient-item:hover {
            border-color: #2563eb;
            background-color: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .patient-info .name {
            display: block;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .patient-info .details {
            display: block;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .patient-item i {
            color: #9ca3af;
            font-size: 1.25rem;
        }

        .patient-item:hover i {
            color: #2563eb;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .btn-back:hover {
            color: #111827;
        }

        .btn-back i {
            margin-right: 0.5rem;
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
            display: flex;
            align-items: center;
        }

        .branding-container span {
            font-size: 11px;
            margin-right: 6px;
        }

        .branding-container img {
            height: 20px;
        }

        @media (max-width: 640px) {
            .footer {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="selection-card">
        <h2>
            <?php echo _l('select_family_member'); ?>
        </h2>
        <p>
            <?php echo _l('multiple_patients_found'); ?>
        </p>

        <div class="patient-list">
            <?php foreach ($patients as $patient): ?>
                <a href="<?php echo site_url('self_kiosk/kiosk/do_select_patient/' . $patient['userid']); ?>"
                    class="patient-item">
                    <div class="patient-info">
                        <span class="name">
                            <?php echo $patient['name']; ?>
                        </span>
                        <span class="details">
                            <?php if (!empty($patient['mr_number'])): ?>
                                <strong>
                                    <?php echo _l('mr_number'); ?>:
                                </strong>
                                <?php echo $patient['mr_number']; ?>
                            <?php endif; ?>
                            <?php if (!empty($patient['phone'])): ?>
                                <br><strong>
                                    <?php echo _l('phone'); ?>:
                                </strong>
                                <?php echo $patient['phone']; ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php endforeach; ?>
        </div>

        <a href="<?php echo site_url('self_kiosk/kiosk'); ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            <?php echo _l('back_to_login'); ?>
        </a>
    </div>

    <div class="footer">
        <div class="copyright">
            Copyright &copy;
            <?php echo date('Y'); ?> Healthocare Private Limited. All rights reserved.
        </div>
        <div class="branding-container">
            <span>Powered by</span>
            <img src="<?php echo base_url('modules/web_integration/assets/images/healtho_logo.png'); ?>" alt="HealthO">
        </div>
    </div>

</body>

</html>