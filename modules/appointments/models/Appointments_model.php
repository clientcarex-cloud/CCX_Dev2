<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointments_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->db->field_exists('invoice_id', db_prefix() . 'appointments')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "appointments` ADD `invoice_id` INT(11) NULL DEFAULT NULL AFTER `appointment_type`;");
        }
    }

    public function get($id = '', $filters = [])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $appointment = $this->db->get(db_prefix() . 'appointments')->row();
            return $appointment;
        }

        // For list view
        $this->db->select(db_prefix() . 'appointments.*, ' . db_prefix() . 'invoices.status as invoice_status, ' . db_prefix() . 'invoices.total as invoice_total, (SELECT SUM(amount) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'appointments.invoice_id) as total_payment');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'appointments.invoice_id', 'left');

        // Apply Filters
        if (!empty($filters['doctor_id'])) {
            $this->db->where(db_prefix() . 'appointments.doctor_id', $filters['doctor_id']);
        }
        if (!empty($filters['date'])) {
            $this->db->where(db_prefix() . 'appointments.appointment_date', to_sql_date($filters['date']));
        }
        if (!empty($filters['status'])) {
            $this->db->where(db_prefix() . 'appointments.status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start();

            // Search in Appointments table (e.g., date, status - limited use but consistent)
            // But main target is Patient Name (Client/Guest)

            // Join Clients to search in them
            // Since we left join invoices, let's also join clients and guests temporarily for search? 
            // OR use a subquery/exists or complex join.
            // Simpler: Left Join Clients and Guests to the main query to filter

            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'appointments.patient_id', 'left');
            $this->db->join(db_prefix() . 'patient_guests', db_prefix() . 'patient_guests.id = ' . db_prefix() . 'appointments.guest_id', 'left');

            $this->db->like(db_prefix() . 'clients.company', $search);
            $this->db->or_like(db_prefix() . 'clients.phonenumber', $search);
            $this->db->or_like(db_prefix() . 'clients.mr_no', $search);

            $this->db->or_like(db_prefix() . 'patient_guests.name', $search);
            $this->db->or_like(db_prefix() . 'patient_guests.phone', $search);

            $this->db->group_end();
        }

        // HIMS Branch Logic
        $CI =& get_instance();
        if (!function_exists('get_staff_hims_branch_id')) {
            // $CI->load->helper('hims/hims');
        }

        if (function_exists('get_staff_hims_branch_id') && !is_hims_unrestricted()) {
            $branch_id = get_staff_hims_branch_id();
            if ($branch_id) {
                // Disambiguate column
                $this->db->where(db_prefix() . 'appointments.hims_branch_id', $branch_id);
            }
        }

        // Sorting? Default by ID desc usually
        $this->db->order_by(db_prefix() . 'appointments.id', 'desc');

        return $this->db->get(db_prefix() . 'appointments')->result_array();
    }

    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        // HIMS Branch Logic
        if (!isset($data['hims_branch_id'])) {
            $CI =& get_instance();
            if (!function_exists('get_staff_hims_branch_id')) {
                // $CI->load->helper('hims/hims');
            }
            if (function_exists('get_staff_hims_branch_id')) {
                $branch_id = get_staff_hims_branch_id();
                if ($branch_id) {
                    $data['hims_branch_id'] = $branch_id;
                }
            }
        }
        // Check if invoice generation is needed
        $create_invoice = false;
        $invoice_data = [];
        if (isset($data['create_invoice']) && $data['create_invoice'] == 1 && $data['appointment_type'] == 'Paid') {
            $create_invoice = true;
            $invoice_data = [
                'service_name' => $data['service_name'],
                'amount' => $data['amount'],
                'received_amount' => isset($data['received_amount']) ? $data['received_amount'] : $data['amount'],
                'payment_mode' => isset($data['payment_mode']) ? $data['payment_mode'] : '', // Add payment mode
                'clientid' => $data['patient_id']
            ];

            // Validate received amount
            if ($invoice_data['received_amount'] > $invoice_data['amount']) {
                $invoice_data['received_amount'] = $invoice_data['amount'];
            }
            // Auto-confirm Paid appointments
            $data['status'] = 'confirmed';

            // Auto-convert Guest to Patient if Paid
            if (!empty($data['guest_id'])) {
                $this->load->model('patients/patients_model');
                $guest = $this->get_guest($data['guest_id']);

                if ($guest) {
                    $patient_data = [
                        'firstname' => $guest->name, // Assuming name is just firstname or full name
                        'lastname' => '', // You might want to split name if possible, or just put all in firstname
                        'phonenumber' => $guest->phone,
                        'dob' => $guest->dob,
                        'gender' => $guest->gender,
                        'patient_title' => isset($guest->title) ? $guest->title : '',
                        'email' => '', // Default empty email to prevent undefined index error
                    ];

                    $new_patient_id = $this->patients_model->add_patient($patient_data);

                    if ($new_patient_id) {
                        $data['patient_id'] = $new_patient_id;
                        $data['guest_id'] = null; // Unset guest_id

                        // Update invoice data with new clientid
                        if ($create_invoice) {
                            $invoice_data['clientid'] = $new_patient_id;
                        }
                    }
                }
            }
        }

        // Always unset non-db fields to prevent "Unknown column" error
        if (isset($data['create_invoice']))
            unset($data['create_invoice']);
        if (isset($data['service_name']))
            unset($data['service_name']);
        if (isset($data['amount']))
            unset($data['amount']);
        if (isset($data['received_amount']))
            unset($data['received_amount']);
        if (isset($data['payment_mode']))
            unset($data['payment_mode']);
        if (isset($data['appointment_id']))
            unset($data['appointment_id']);

        // Anytime Appointment Logic
        $anytime_appointment = false;
        if (empty($data['start_time']) && !empty($data['doctor_id'])) {
            $doctor = $this->staff_model->get($data['doctor_id']);
            if ($doctor && $doctor->anytime_appointment == 1) {
                $anytime_appointment = true;
                $data['appointment_date'] = date('Y-m-d');
                $data['start_time'] = date('H:i:s');
                $data['end_time'] = date('H:i:s', strtotime('+30 minutes')); // Default 30 mins
            }
        }

        // Validate Availability only if NOT anytime appointment
        if (!$anytime_appointment) {
            if (empty($data['start_time'])) {
                return 'slot_required';
            } else {
                if (!$this->check_availability($data['doctor_id'], $data['appointment_date'], $data['start_time'])) {
                    return 'slot_unavailable';
                }
            }
        }


        $this->db->insert(db_prefix() . 'appointments', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Appointment Added [ID: ' . $insert_id . ']');

            if ($create_invoice && !empty($invoice_data['clientid'])) {
                $this->create_invoice($insert_id, $invoice_data);
            }

            return $insert_id;
        }
        return false;
    }

    public function create_invoice($appointment_id, $data)
    {
        $this->load->model('invoices_model');
        $new_invoice_data = [
            'clientid' => $data['clientid'],
            'number' => get_option('next_invoice_number'),
            'date' => date('Y-m-d'),
            'duedate' => date('Y-m-d'),
            'currency' => get_base_currency()->id,
            'newitems' => [
                [
                    'description' => $data['service_name'],
                    'long_description' => 'Appointment Charge',
                    'qty' => 1,
                    'rate' => $data['amount'],
                    'unit' => '',
                    'order' => 1
                ]
            ],
            'subtotal' => $data['amount'],
            'total' => $data['amount'],
            'billing_street' => '', // fetch from client if needed
            // Add other required fields defaults
        ];

        // This is a simplified invoice creation. Real one might need more data.
        $id = $this->invoices_model->add($new_invoice_data);
        if ($id) {
            // Link invoice to appointment
            $this->db->where('id', $appointment_id);
            $this->db->update(db_prefix() . 'appointments', ['invoice_id' => $id]);

            // Link invoice to appointment if needed, or just log it
            log_activity('Invoice Created for Appointment [ApptID: ' . $appointment_id . ', InvID: ' . $id . ']');

            // Record Payment
            if (!empty($data['payment_mode']) && $data['received_amount'] > 0) {
                $payment_data = [
                    'amount' => $data['received_amount'],
                    'invoiceid' => $id,
                    'paymentmode' => $data['payment_mode'],
                    'date' => date('Y-m-d'),
                    'transactionid' => 'Appt-' . $appointment_id
                ];
                $this->load->model('payments_model');
                $this->payments_model->add($payment_data);
            }
        }
    }

    public function add_guest($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'patient_guests', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $original = $this->get($id);

        // Check for reschedule
        $is_reschedule = false;
        if (
            (isset($data['appointment_date']) && $data['appointment_date'] != $original->appointment_date) ||
            (isset($data['start_time']) && $data['start_time'] != $original->start_time)
        ) {
            $is_reschedule = true;

            // Validate Availability
            $doctor_id = isset($data['doctor_id']) ? $data['doctor_id'] : $original->doctor_id;
            $date = isset($data['appointment_date']) ? $data['appointment_date'] : $original->appointment_date;
            $start_time = isset($data['start_time']) ? $data['start_time'] : $original->start_time;

            if (!$this->check_availability($doctor_id, $date, $start_time, $id)) {
                return 'slot_unavailable';
            }
        }

        // Check if invoice generation is needed
        $create_invoice = false;
        $invoice_data = [];
        if (isset($data['create_invoice']) && $data['create_invoice'] == 1 && isset($data['appointment_type']) && $data['appointment_type'] == 'Paid') {
            $create_invoice = true;
            $invoice_data = [
                'service_name' => isset($data['service_name']) ? $data['service_name'] : '',
                'amount' => isset($data['amount']) ? $data['amount'] : 0,
                'received_amount' => isset($data['received_amount']) ? $data['received_amount'] : (isset($data['amount']) ? $data['amount'] : 0),
                'payment_mode' => isset($data['payment_mode']) ? $data['payment_mode'] : '',
                'clientid' => isset($data['patient_id']) ? $data['patient_id'] : $original->patient_id
            ];

            // Validate received amount
            if ($invoice_data['received_amount'] > $invoice_data['amount']) {
                $invoice_data['received_amount'] = $invoice_data['amount'];
            }
            // Auto-confirm Paid appointments
            $data['status'] = 'confirmed';

            // Auto-convert Guest to Patient if Paid (if not already patient)
            if (empty($invoice_data['clientid']) && !empty($original->guest_id)) {
                $this->load->model('patients/patients_model');
                $guest = $this->get_guest($original->guest_id);
                if ($guest) {
                    $patient_data = [
                        'firstname' => $guest->name,
                        'lastname' => '',
                        'phonenumber' => $guest->phone,
                        'dob' => $guest->dob,
                        'gender' => $guest->gender,
                        'patient_title' => isset($guest->title) ? $guest->title : '',
                        'email' => '',
                    ];
                    $new_patient_id = $this->patients_model->add_patient($patient_data);
                    if ($new_patient_id) {
                        $data['patient_id'] = $new_patient_id;
                        $data['guest_id'] = null;
                        $invoice_data['clientid'] = $new_patient_id;
                    }
                }
            }
        }

        // Cleanup non-db fields
        if (isset($data['create_invoice']))
            unset($data['create_invoice']);
        if (isset($data['service_name']))
            unset($data['service_name']);
        if (isset($data['amount']))
            unset($data['amount']);
        if (isset($data['received_amount']))
            unset($data['received_amount']);
        if (isset($data['payment_mode']))
            unset($data['payment_mode']);
        if (isset($data['appointment_id']))
            unset($data['appointment_id']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appointments', $data);

        if ($this->db->affected_rows() > 0 || $create_invoice) { // If invoice created, return true even if no update rows (e.g. only converting to paid)
            log_activity('Appointment Updated [ID: ' . $id . ']');

            if ($create_invoice && !empty($invoice_data['clientid'])) {
                $this->create_invoice($id, $invoice_data);
            }

            if ($is_reschedule) {
                $reschedule_data = [
                    'appointment_id' => $id,
                    'prev_date' => $original->appointment_date,
                    'prev_start_time' => $original->start_time,
                    'prev_end_time' => $original->end_time,
                    'new_date' => $data['appointment_date'],
                    'new_start_time' => $data['start_time'],
                    'new_end_time' => $data['end_time'],
                    'rescheduled_by' => get_staff_user_id()
                ];
                $this->db->insert(db_prefix() . 'appointment_reschedules', $reschedule_data);
                log_activity('Appointment Rescheduled [ID: ' . $id . ']');
            }

            return true;
        }
        return false;
    }

    public function check_availability($doctor_id, $date, $start_time, $exclude_id = null)
    {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('appointment_date', $date);
        $this->db->where('start_time', $start_time);
        $this->db->where('status !=', 'cancelled');
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results(db_prefix() . 'appointments') == 0;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'appointments');
        if ($this->db->affected_rows() > 0) {
            log_activity('Appointment Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    public function get_doctors()
    {
        $this->db->select('staffid, firstname, lastname, email, default_service_item, anytime_appointment');
        $this->db->from(db_prefix() . 'staff');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role');
        $this->db->where_in(db_prefix() . 'roles.name', ['Doctor', 'Jr. Doctor', 'Sr. Doctor']);
        $this->db->where(db_prefix() . 'staff.active', 1);
        $this->db->where(db_prefix() . 'staff.doctor_profile_type', 'Consultant');
        return $this->db->get()->result_array();
    }

    public function get_doctor_slots($doctor_id, $date, $exclude_appointment_id = null)
    {
        $day_of_week = date('l', strtotime($date));

        // Fetch schedule for this doctor and day
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('day_of_week', $day_of_week);
        $schedule = $this->db->get(db_prefix() . 'doctor_schedule')->row();

        if (!$schedule || !$schedule->is_available) {
            return [];
        }

        $start_time = strtotime($date . ' ' . $schedule->start_time);
        $end_time = strtotime($date . ' ' . $schedule->end_time);
        $slot_duration = $schedule->slot_duration * 60; // Convert to seconds

        $slots = [];
        $current_time = $start_time;

        while ($current_time < $end_time) {
            $slot_start = date('H:i:s', $current_time);
            $slot_end = date('H:i:s', $current_time + $slot_duration);

            // Ensure slot doesn't exceed end time
            if ($current_time + $slot_duration > $end_time) {
                break;
            }

            // Check if slot is booked
            $is_booked = $this->is_slot_booked($doctor_id, $date, $slot_start, $exclude_appointment_id);

            $slots[] = [
                'time' => date('h:i A', $current_time) . ' - ' . date('h:i A', $current_time + $slot_duration),
                'start_time' => $slot_start,
                'end_time' => $slot_end,
                'available' => !$is_booked
            ];

            // Filter out past slots if date is today
            if ($date == date('Y-m-d')) {
                $now_time = date('H:i:s');
                if ($slot_start < $now_time) {
                    $slots[count($slots) - 1]['available'] = false;
                    $slots[count($slots) - 1]['past'] = true; // Optional flag
                }
            }


            $current_time += $slot_duration;
        }

        return $slots;
    }

    public function get_schedule($doctor_id)
    {
        $this->db->where('doctor_id', $doctor_id);
        return $this->db->get(db_prefix() . 'doctor_schedule')->result_array();
    }

    public function update_schedule($doctor_id, $data)
    {
        // Clear existing schedule for this doctor (or update/insert logic)
        // Simplest is to delete all and re-insert or update by day
        // Let's iterate and update/insert

        foreach ($data['schedule'] as $day_schedule) {
            $day = $day_schedule['day_of_week'];

            $update_data = [
                'doctor_id' => $doctor_id,
                'day_of_week' => $day,
                'start_time' => $day_schedule['start_time'],
                'end_time' => $day_schedule['end_time'],
                'slot_duration' => $day_schedule['slot_duration'],
                'is_available' => isset($day_schedule['is_available']) ? 1 : 0
            ];

            $this->db->where('doctor_id', $doctor_id);
            $this->db->where('day_of_week', $day);
            $exists = $this->db->count_all_results(db_prefix() . 'doctor_schedule');

            if ($exists > 0) {
                $this->db->where('doctor_id', $doctor_id);
                $this->db->where('day_of_week', $day);
                $this->db->update(db_prefix() . 'doctor_schedule', $update_data);
            } else {
                $this->db->insert(db_prefix() . 'doctor_schedule', $update_data);
            }
        }
        return true;
    }

    public function is_slot_booked($doctor_id, $date, $start_time, $exclude_appointment_id = null)
    {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('appointment_date', $date);
        $this->db->where('start_time', $start_time);
        $this->db->where('status !=', 'cancelled');
        if ($exclude_appointment_id) {
            $this->db->where('id !=', $exclude_appointment_id);
        }
        return $this->db->count_all_results(db_prefix() . 'appointments') > 0;
    }

    public function get_guest($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'patient_guests')->row();
    }

    public function get_patient_by_mobile($mobile)
    {
        $results = [];

        // Search in Clients
        $this->db->select('userid, company, phonenumber, mr_no, datecreated as date_registered, dob'); // Added datecreated
        $this->db->from(db_prefix() . 'clients');
        $this->db->like('phonenumber', $mobile); // Changed to LIKE
        $this->db->where('is_patient', 1);
        $clients = $this->db->get()->result_array();

        foreach ($clients as $client) {
            $client['type'] = 'client';
            $client['name'] = $client['company'];
            $client['phone'] = $client['phonenumber'];
            // Calculate Age if DOB exists
            if (!empty($client['dob'])) {
                $client['age'] = date_diff(date_create($client['dob']), date_create('today'))->y;
            } else {
                $client['age'] = '';
            }
            // Format date
            $client['date_registered'] = _dt($client['date_registered']);
            $results[] = $client;
        }

        // Search in Guests
        $this->db->select('id, name, phone, dob, gender, created_at as date_registered'); // Assuming created_at exists or we use current time if not
        $this->db->from(db_prefix() . 'patient_guests');
        $this->db->like('phone', $mobile); // Changed to LIKE
        $guests = $this->db->get()->result_array();

        foreach ($guests as $guest) {
            $guest['type'] = 'guest';
            $guest['userid'] = $guest['id']; // Map id to userid for consistency in JS
            $guest['mr_no'] = 'Guest';
            // Calculate Age if DOB exists
            if (!empty($guest['dob'])) {
                $guest['age'] = date_diff(date_create($guest['dob']), date_create('today'))->y;
            } else {
                $guest['age'] = '';
            }
            // Format date
            $guest['date_registered'] = _dt($guest['date_registered']);
            $results[] = $guest;
        }

        return $results;
    }
    public function get_reschedule_history($appointment_id)
    {
        $this->db->where('appointment_id', $appointment_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get(db_prefix() . 'appointment_reschedules')->result_array();
    }

    public function has_reschedule_history($appointment_id)
    {
        $this->db->where('appointment_id', $appointment_id);
        return $this->db->count_all_results(db_prefix() . 'appointment_reschedules') > 0;
    }

    public function check_duplicate_appointment($name, $mobile, $date, $exclude_appointment_id = null)
    {
        // Check against Appointments linked to Clients
        $this->db->select('a.id');
        $this->db->from(db_prefix() . 'appointments a');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = a.patient_id', 'left');
        $this->db->where('a.status !=', 'cancelled');
        $this->db->where('a.status !=', 'visited'); // Assuming visited means done, but user said "active" usually means pending/confirmed. Let's stick to pending/confirmed as per request "pending or confirmed status"
        $this->db->where_in('a.status', ['pending', 'confirmed']);
        $this->db->where('a.appointment_date', $date);

        $this->db->group_start();
        $this->db->where('c.company', $name);
        $this->db->where('c.phonenumber', $mobile);
        $this->db->group_end();

        if ($exclude_appointment_id && is_numeric($exclude_appointment_id)) {
            $this->db->where('a.id !=', $exclude_appointment_id);
        }

        $client_match = $this->db->get()->num_rows();

        if ($client_match > 0) {
            return true;
        }

        // Check against Appointments linked to Guests
        $this->db->select('a.id');
        $this->db->from(db_prefix() . 'appointments a');
        $this->db->join(db_prefix() . 'patient_guests g', 'g.id = a.guest_id', 'left');
        $this->db->where_in('a.status', ['pending', 'confirmed']);
        $this->db->where('a.appointment_date', $date);

        $this->db->group_start();
        $this->db->where('g.name', $name);
        $this->db->where('g.phone', $mobile);
        $this->db->group_end();

        if ($exclude_appointment_id && is_numeric($exclude_appointment_id)) {
            $this->db->where('a.id !=', $exclude_appointment_id);
        }

        $guest_match = $this->db->get()->num_rows();

        return $guest_match > 0;
    }

    public function mark_missed_appointments()
    {
        $buffer_minutes = get_option('appointments_buffer_time');
        if (!$buffer_minutes) {
            $buffer_minutes = 15; // Default fallback
        }

        $this->db->where_in('status', ['pending', 'confirmed']);
        $this->db->where('appointment_date', date('Y-m-d'));
        $appointments = $this->db->get(db_prefix() . 'appointments')->result_array();

        $now = time();

        foreach ($appointments as $appointment) {
            // Logic: if current_time > (appointment_time + buffer)
            // appointment_time = date + start_time
            $appointment_time_str = $appointment['appointment_date'] . ' ' . $appointment['start_time'];
            $appointment_time = strtotime($appointment_time_str);

            // Add buffer to appointment time
            $time_limit = $appointment_time + ($buffer_minutes * 60);

            if ($now > $time_limit) {
                // Mark as Missed
                $this->db->where('id', $appointment['id']);
                $this->db->update(db_prefix() . 'appointments', ['status' => 'missed']);

                if ($this->db->affected_rows() > 0) {
                    log_activity('Appointment Auto-Marked as Missed [ID: ' . $appointment['id'] . ']');
                }
            }
        }
    }
}
