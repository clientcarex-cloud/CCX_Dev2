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
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1.5rem;
            text-align: left;
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

        .rating-container {
            margin-bottom: 2.5rem;
        }

        .rating-label {
            display: block;
            font-size: 1.125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1.5rem;
        }

        .stars {
            display: flex;
            justify-content: center;
            flex-direction: row-reverse;
            gap: 0.5rem;
        }

        .stars input {
            display: none;
        }

        .stars label {
            font-size: 3rem;
            color: #d1d5db;
            cursor: pointer;
            transition: color 0.2s;
        }

        .stars label:hover,
        .stars label:hover~label,
        .stars:not(.disabled) input:checked~label {
            color: #fbbf24;
        }

        .stars.disabled label {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .stars.disabled input:checked~label {
            color: #fbbf24;
        }

        .message-container {
            text-align: left;
            margin-bottom: 2rem;
        }

        .message-container label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input {
            padding: 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            font-size: 1rem;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.2s;
            background-color: #f9fafb;
            resize: vertical;
            min-height: 120px;
        }

        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-submit {
            display: block;
            width: 100%;
            background-color: #2563eb;
            color: white;
            padding: 1.25rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1.125rem;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            text-align: left;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 640px) {
            .stars label {
                font-size: 2.5rem;
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
                    <?php echo _l('feedback_suggestions'); ?>
                </h2>
                <a href="<?php echo site_url('self_kiosk/kiosk/dashboard'); ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <?php echo _l('close'); ?>
                </a>
            </div>

            <?php if ($this->session->flashdata('message-danger')): ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('message-danger'); ?>
                </div>
            <?php endif; ?>

            <?php if (!$can_submit): ?>
                <div class="alert alert-danger" style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-exclamation-circle" style="font-size: 1.25rem;"></i>
                    <span>Daily limit reached. You have already submitted feedback <?php echo $submission_count; ?> times today.</span>
                </div>
            <?php endif; ?>

            <p style="color: #6b7280; margin-bottom: 2rem;">We value your feedback. Please let us know how we can
                improve our services.</p>

            <?php echo form_open('self_kiosk/kiosk/submit_feedback'); ?>

            <div class="rating-container">
                <span class="rating-label">Rate your experience</span>
                <div class="stars <?php echo !$can_submit ? 'disabled' : ''; ?>">
                    <input type="radio" name="rating" id="star5" value="5" <?php echo !$can_submit ? 'disabled' : ''; ?>><label for="star5"
                        class="fas fa-star"></label>
                    <input type="radio" name="rating" id="star4" value="4" <?php echo !$can_submit ? 'disabled' : ''; ?>><label for="star4"
                        class="fas fa-star"></label>
                    <input type="radio" name="rating" id="star3" value="3" <?php echo !$can_submit ? 'disabled' : ''; ?>><label for="star3"
                        class="fas fa-star"></label>
                    <input type="radio" name="rating" id="star2" value="2" <?php echo !$can_submit ? 'disabled' : ''; ?>><label for="star2"
                        class="fas fa-star"></label>
                    <input type="radio" name="rating" id="star1" value="1" <?php echo !$can_submit ? 'disabled' : ''; ?>><label for="star1"
                        class="fas fa-star"></label>
                </div>
            </div>

            <div class="message-container">
                <label for="message">Your Message (Optional)</label>
                <textarea name="message" id="message" class="form-input"
                    placeholder="Tell us more about your experience..." <?php echo !$can_submit ? 'disabled' : ''; ?>></textarea>
            </div>

            <button type="submit" class="btn-submit" <?php echo !$can_submit ? 'disabled style="opacity:0.6; cursor:not-allowed;"' : ''; ?>>
                <?php echo !$can_submit ? 'Daily Limit Reached' : 'Submit Feedback'; ?>
            </button>
            <?php echo form_close(); ?>
        </div>
    </div>

</body>

</html>