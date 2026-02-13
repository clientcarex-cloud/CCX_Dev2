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
            font-size: 1.25rem;
            color: #111827;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
        }

        .card-header h2 {
            margin: 0;
            font-size: 1.25rem;
            color: #111827;
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

        .visits-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .visit-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            padding: 1.25rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .visit-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .visit-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .visit-date-badge {
            background: #eff6ff;
            color: #2563eb;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .visit-code {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        .visit-body {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .info-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            font-weight: 600;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 500;
            color: #111827;
        }

        .tests-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .test-tag {
            background: #f3f4f6;
            color: #374151;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 500;
            border: 1px solid #e5e7eb;
        }

        .no-data {
            text-align: center;
            padding: 3rem 0;
            color: #6b7280;
        }

        .no-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 640px) {
            .visit-header {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="logo">
            <?php get_company_logo('self_kiosk/kiosk/dashboard'); ?>
        </div>
        <h1>
            <?php echo _l('patient_portal'); ?>
        </h1>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>
                    <?php echo _l('your_visits'); ?>
                </h2>
                <a href="<?php echo site_url('self_kiosk/kiosk/dashboard'); ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <?php echo _l('close'); ?>
                </a>
            </div>

            <?php if (!empty($visits)): ?>
                <div class="visits-list">
                    <?php foreach ($visits as $visit): ?>
                        <div class="visit-card">
                            <div class="visit-header">
                                <div class="visit-date-badge">
                                    <i class="far fa-calendar-alt"></i>
                                    <?php echo _d($visit['created_at']); ?>
                                </div>
                                <div class="visit-code">
                                    <?php echo _l('visit_code'); ?>:
                                    <?php echo $visit['visit_code']; ?>
                                </div>
                            </div>

                            <div class="visit-body">
                                <div class="info-group">
                                    <span class="info-label">
                                        <?php echo _l('doctor'); ?>
                                    </span>
                                    <span class="info-value">
                                        <i class="fas fa-user-md" style="color: #2563eb; margin-right: 0.5rem;"></i>
                                        <?php echo !empty($visit['doctor_name']) ? $visit['doctor_name'] : _l('not_assigned'); ?>
                                    </span>
                                </div>

                                <div class="info-group">
                                    <span class="info-label">
                                        <?php echo _l('tests_services'); ?>
                                    </span>
                                    <div class="tests-list">
                                        <?php
                                        if (!empty($visit['test_names'])) {
                                            $tests = explode(', ', $visit['test_names']);
                                            foreach ($tests as $test) {
                                                echo '<span class="test-tag">' . $test . '</span>';
                                            }
                                        } else {
                                            echo '<span style="color: #9ca3af; font-style: italic; font-size: 0.875rem;">' . _l('no_tests_found') . '</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-history"></i>
                    <p>
                        <?php echo _l('no_visits_found'); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>