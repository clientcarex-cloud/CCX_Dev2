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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
        }

        .card-header h2 {
            margin: 0;
            font-size: 1.5rem;
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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
        }

        .form-input {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-input:disabled {
            background-color: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
        }

        .age-group {
            display: flex;
            gap: 0.5rem;
        }

        .age-group .form-input:first-child {
            flex: 2;
        }

        .age-group select {
            flex: 1;
        }

        .section-title {
            grid-column: span 2;
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #2563eb;
        }

        .btn-submit {
            grid-column: span 2;
            background-color: #2563eb;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .section-title {
                grid-column: span 1;
            }

            .btn-submit {
                grid-column: span 1;
            }
        }

        .mr-tag {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-left: 0.75rem;
            vertical-align: middle;
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
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <h2><?php echo $title; ?></h2>
                    <?php if (isset($patient->mr_number) && !empty($patient->mr_number)): ?>
                        <span class="mr-tag"><?php echo $patient->mr_number; ?></span>
                    <?php endif; ?>
                </div>
                <a href="<?php echo site_url('self_kiosk/kiosk/dashboard'); ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <?php echo _l('close'); ?>
                </a>
            </div>

            <?php if ($this->session->flashdata('message-success')): ?>
                <div class="alert alert-success">
                    <?php echo $this->session->flashdata('message-success'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('message-danger')): ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('message-danger'); ?>
                </div>
            <?php endif; ?>

            <?php
            $can_edit = !isset($settings['update_info']) || (isset($settings['update_info']['edit']) && $settings['update_info']['edit'] == "1");
            ?>

            <?php echo form_open('self_kiosk/kiosk/update_profile'); ?>
            <div class="form-grid">
                <!-- Name (Disabled) -->
                <div class="form-group">
                    <label>
                        <?php echo _l('name'); ?>
                    </label>
                    <input type="text" class="form-input" value="<?php echo e($patient->full_name); ?>" disabled>
                </div>

                <!-- Mobile (Disabled) -->
                <div class="form-group">
                    <label>
                        <?php echo _l('mobile_number'); ?>
                    </label>
                    <input type="text" class="form-input" value="<?php echo e($patient->phonenumber); ?>" disabled>
                </div>

                <!-- Age / DOB -->
                <div class="form-group">
                    <label>
                        <?php echo _l('age_dob'); ?>
                    </label>
                    <div class="age-group">
                        <input type="text" name="age" id="age_input" class="form-input"
                            value="<?php echo e($patient->age_unit == 'DOB' ? date('d-m-Y', strtotime($patient->dob)) : $patient->age); ?>"
                            placeholder="<?php echo _l('age'); ?>" <?php echo !$can_edit ? 'disabled' : ''; ?>>
                        <select name="age_unit" id="age_unit_select" class="form-input" <?php echo !$can_edit ? 'disabled' : ''; ?>>
                            <option value="Years" <?php echo $patient->age_unit == 'Years' ? 'selected' : ''; ?>>Years
                            </option>
                            <option value="Months" <?php echo $patient->age_unit == 'Months' ? 'selected' : ''; ?>>Months
                            </option>
                            <option value="Days" <?php echo $patient->age_unit == 'Days' ? 'selected' : ''; ?>>Days
                            </option>
                            <option value="DOB" <?php echo $patient->age_unit == 'DOB' ? 'selected' : ''; ?>>DOB
                                (DD-MM-YYYY)</option>
                        </select>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label>
                        <?php echo _l('email'); ?>
                    </label>
                    <input type="email" name="email" class="form-input" value="<?php echo e($patient->email); ?>"
                        placeholder="example@email.com" <?php echo !$can_edit ? 'disabled' : ''; ?>>
                </div>

                <!-- UID No -->
                <div class="form-group">
                    <label>
                        <?php echo _l('uid_no'); ?>
                    </label>
                    <input type="text" name="uid_no" class="form-input" value="<?php echo e($patient->uid_no); ?>"
                        placeholder="ID Card Number" <?php echo !$can_edit ? 'disabled' : ''; ?>>
                </div>

                <div class="section-title">
                    <i class="fas fa-user-friends"></i>
                    <?php echo _l('attender_details'); ?>
                </div>

                <!-- Attender Title -->
                <div class="form-group">
                    <label>
                        <?php echo _l('attender_title'); ?>
                    </label>
                    <select name="attender_title_id" class="form-input" <?php echo !$can_edit ? 'disabled' : ''; ?>>
                        <option value="">
                            <?php echo _l('dropdown_non_selected_tex'); ?>
                        </option>
                        <?php foreach ($attender_titles as $title_item): ?>
                            <option value="<?php echo $title_item['id']; ?>" <?php echo $patient->attender_title_id == $title_item['id'] ? 'selected' : ''; ?>>
                                <?php echo $title_item['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Attender Name -->
                <div class="form-group">
                    <label>
                        <?php echo _l('attender_name'); ?>
                    </label>
                    <input type="text" name="attender_name" class="form-input"
                        value="<?php echo e($patient->attender_name); ?>" <?php echo !$can_edit ? 'disabled' : ''; ?>>
                </div>

                <!-- Address -->
                <div class="form-group full-width">
                    <label>
                        <?php echo _l('address'); ?>
                    </label>
                    <textarea name="address" class="form-input" rows="3" <?php echo !$can_edit ? 'disabled' : ''; ?>><?php echo e($patient->address); ?></textarea>
                </div>

                <?php if ($can_edit): ?>
                    <button type="submit" class="btn-submit">
                        <?php echo _l('submit'); ?>
                    </button>
                <?php endif; ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function () {
            const ageInput = document.getElementById('age_input');
            const ageUnit = document.getElementById('age_unit_select');
            let fp = null;

            function initPicker() {
                if (ageUnit.value === 'DOB') {
                    if (!fp) {
                        fp = flatpickr(ageInput, {
                            dateFormat: "d-m-Y",
                            allowInput: true,
                            maxDate: "today"
                        });
                    }
                    ageInput.placeholder = "DD-MM-YYYY";
                } else {
                    if (fp) {
                        fp.destroy();
                        fp = null;
                    }
                    ageInput.placeholder = "Age";
                    // If it was a date, clear it? Maybe not, keep current value
                }
            }

            $(ageUnit).on('change', function () {
                initPicker();
                if (this.value !== 'DOB') {
                    // Force numeric if not DOB
                    if (isNaN(ageInput.value)) {
                        ageInput.value = "";
                    }
                }
            });

            // Run on load
            initPicker();
        });
    </script>
</body>

</html>