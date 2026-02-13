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

        /* Active Appointment Styles */
        .active-appt {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .appt-icon {
            width: 60px;
            height: 60px;
            background-color: #2563eb;
            color: white;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .appt-details h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.125rem;
            color: #1e3a8a;
        }

        .appt-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
            color: #60a5fa;
            flex-wrap: wrap;
        }

        .appt-meta span {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            background: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            border: 1px solid #dbeafe;
            color: #3b82f6;
            font-weight: 500;
        }

        /* Booking Wizard Styles */
        .wizard-step {
            margin-bottom: 2rem;
        }

        .wizard-step h4 {
            margin: 0 0 1rem 0;
            font-size: 1rem;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .doctor-select-wrapper {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .doctor-dropdown {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            font-size: 1rem;
            font-weight: 500;
            color: #111827;
            background-color: #f9fafb;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            background-size: 1.25rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .doctor-dropdown:focus {
            outline: none;
            border-color: #2563eb;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.75rem;
        }

        .slot-btn {
            border: 1px solid #e5e7eb;
            background: white;
            padding: 0.75rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .slot-btn:hover:not(:disabled) {
            border-color: #2563eb;
            color: #2563eb;
        }

        .slot-btn.selected {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .slot-btn:disabled {
            background-color: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .btn-submit {
            width: 100%;
            background-color: #2563eb;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1.125rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1rem;
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* History Styles */
        .history-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .history-item {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-date {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .history-info h5 {
            margin: 0;
            font-size: 0.9375rem;
        }

        .history-info p {
            margin: 0;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-confirmed,
        .status-visited {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .status-cancelled,
        .status-missed {
            background: #fee2e2;
            color: #991b1b;
        }

        .hidden {
            display: none;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 640px) {
            .active-appt {
                flex-direction: column;
                text-align: center;
            }

            .history-item {
                grid-template-columns: 1fr auto;
            }

            .history-date {
                grid-column: span 2;
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
        <!-- Active Appointment -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <?php echo _l('active_appointment'); ?>
                </h2>
                <a href="<?php echo site_url('self_kiosk/kiosk/dashboard'); ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <?php echo _l('close'); ?>
                </a>
            </div>

            <?php if (!empty($active_appointments)): ?>
                <?php foreach ($active_appointments as $appt): ?>
                    <div class="active-appt">
                        <div class="appt-icon"><i class="fas fa-calendar-check"></i></div>
                        <div class="appt-details">
                            <h3>Appointment with
                                <?php echo get_staff_full_name($appt['doctor_id']); ?>
                            </h3>
                            <div class="appt-meta">
                                <span><i class="far fa-calendar"></i>
                                    <?php echo _d($appt['appointment_date']); ?>
                                </span>
                                <span><i class="far fa-clock"></i>
                                    <?php echo date('h:i A', strtotime($appt['start_time'])); ?>
                                </span>
                                <span class="status-badge status-<?php echo $appt['status']; ?>">
                                    <?php echo $appt['status']; ?>
                                </span>
                                <?php
                                $appt_time = strtotime($appt['appointment_date'] . ' ' . $appt['start_time']);
                                $now = time();
                                $diff = $appt_time - $now;
                                if ($diff > 0) {
                                    $days = floor($diff / (60 * 60 * 24));
                                    $hours = floor(($diff % (60 * 60 * 24)) / (60 * 60));
                                    $minutes = floor(($diff % (60 * 60)) / 60);

                                    $time_left = '';
                                    if ($days > 0)
                                        $time_left .= $days . 'd ';
                                    if ($hours > 0)
                                        $time_left .= $hours . 'h ';
                                    if ($minutes > 0)
                                        $time_left .= $minutes . 'm';

                                    if (!empty($time_left)) {
                                        echo '<span class="status-badge status-pending"><i class="fas fa-hourglass-half"></i> ' . trim($time_left) . ' left</span>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem 0; color: #6b7280;">
                    <i class="far fa-calendar-times" style="font-size: 2.5rem; margin-bottom: 1rem; display: block;"></i>
                    <p>
                        <?php echo _l('no_active_appointments'); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Booking Wizard (Only if no active confirmed appt) -->
        <?php if (empty($active_appointments)): ?>
            <div class="card">
                <div class="card-header">
                    <h2>
                        <?php echo _l('book_now'); ?>
                    </h2>
                </div>

                <?php if ($this->session->flashdata('message-danger')): ?>
                    <div class="alert alert-danger">
                        <?php echo $this->session->flashdata('message-danger'); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('message-success')): ?>
                    <div class="alert alert-success">
                        <?php echo $this->session->flashdata('message-success'); ?>
                    </div>
                <?php endif; ?>

                <?php echo form_open('self_kiosk/kiosk/book_appointment', ['id' => 'bookingForm']); ?>
                <input type="hidden" name="doctor_id" id="selected_doctor">
                <input type="hidden" name="start_time" id="selected_start_time">
                <input type="hidden" name="end_time" id="selected_end_time">

                <!-- Step 1: Select Doctor -->
                <div class="wizard-step">
                    <h4><i class="fas fa-user-md"></i>
                        <?php echo _l('select_doctor'); ?>
                    </h4>
                    <div class="doctor-select-wrapper">
                        <select class="doctor-dropdown" onchange="selectDoctor(this.value)" id="doctor_dropdown_select">
                            <option value=""><?php echo _l('select_doctor'); ?>...</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo $doctor['staffid']; ?>">
                                    <?php echo $doctor['firstname'] . ' ' . $doctor['lastname']; ?> 
                                    (<?php echo $doctor['anytime_appointment'] ? 'Instant' : 'Scheduled'; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Step 2: Select Date -->
                <div class="wizard-step hidden" id="dateStep">
                    <h4><i class="far fa-calendar-alt"></i>
                        <?php echo _l('select_date'); ?>
                    </h4>
                    <input type="text" name="appointment_date" id="calendar" class="form-input" placeholder="Select a date">
                </div>

                <!-- Step 3: Select Slot -->
                <div class="wizard-step hidden" id="slotStep">
                    <h4><i class="far fa-clock"></i>
                        <?php echo _l('select_slot'); ?>
                    </h4>
                    <div class="slots-grid" id="slotsContainer">
                        <!-- Slots will be loaded here -->
                    </div>
                </div>

                <button type="submit" class="btn-submit hidden" id="submitBtn">
                    <?php echo _l('submit'); ?>
                </button>
                <?php echo form_close(); ?>
            </div>
        <?php endif; ?>

        <!-- History -->
        <?php if (!empty($past_appointments)): ?>
            <div class="card">
                <div class="card-header">
                    <h2>
                        <?php echo _l('past_appointments'); ?>
                    </h2>
                </div>
                <div class="history-list">
                    <?php foreach ($past_appointments as $appt): ?>
                        <div class="history-item">
                            <div class="history-date">
                                <?php echo _d($appt['appointment_date']); ?>
                            </div>
                            <div class="history-info">
                                <h5>Appointment with
                                    <?php echo get_staff_full_name($appt['doctor_id']); ?>
                                </h5>
                                <p>
                                    <?php echo date('h:i A', strtotime($appt['start_time'])); ?>
                                    <?php if ($appt['status'] == 'visited' && !empty($appt['visit_confirmed_at'])): ?>
                                        <br><small class="text-success"><i class="fas fa-check-circle"></i> Visited at:
                                            <?php echo date('h:i A', strtotime($appt['visit_confirmed_at'])); ?></small>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <span class="status-badge status-<?php echo $appt['status']; ?>">
                                <?php echo $appt['status']; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let selectedDoctorId = null;
        let fp = null;

        function selectDoctor(id) {
            if (!id) {
                $('#dateStep').addClass('hidden');
                $('#slotStep').addClass('hidden');
                $('#submitBtn').addClass('hidden');
                return;
            }
            selectedDoctorId = id;
            $('#selected_doctor').val(id);
            $('#dateStep').removeClass('hidden');

            if (fp) {
                fp.clear();
            } else {
                fp = flatpickr("#calendar", {
                    minDate: "today",
                    dateFormat: "Y-m-d",
                    onChange: function (selectedDates, dateStr, instance) {
                        loadSlots(dateStr);
                    }
                });
            }

            // Hide submit btn if doctor changed
            $('#submitBtn').addClass('hidden');
            $('#slotStep').addClass('hidden');
        }

        function loadSlots(date) {
            if (!selectedDoctorId || !date) return;

            $('#slotsContainer').html('<p style="grid-column: span 3; text-align: center;">Loading slots...</p>');
            $('#slotStep').removeClass('hidden');

            $.get('<?php echo site_url('self_kiosk/kiosk/get_slots'); ?>', {
                doctor_id: selectedDoctorId,
                date: date
            }, function (slots) {
                slots = JSON.parse(slots);
                let html = '';
                if (slots.length > 0) {
                    slots.forEach(slot => {
                        html += `<button type="button" class="slot-btn" 
                                    ${!slot.available ? 'disabled' : ''} 
                                    onclick="selectSlot('${slot.start_time}', '${slot.end_time}', this)">
                                    ${slot.time.split(' - ')[0]}
                                 </button>`;
                    });
                } else {
                    html = '<p style="grid-column: span 3; color: #ef4444; font-size: 0.875rem;">No slots available for this date.</p>';
                }
                $('#slotsContainer').html(html);
            });
        }

        function selectSlot(start, end, btn) {
            $('.slot-btn').removeClass('selected');
            $(btn).addClass('selected');
            $('#selected_start_time').val(start);
            $('#selected_end_time').val(end);
            $('#submitBtn').removeClass('hidden');
        }
    </script>
</body>

</html>