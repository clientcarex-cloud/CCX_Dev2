<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Schedules extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('patients/appointments_model'); // Re-use appointments model or create a new one
        // We will use appointments_model for schedule logic as it's closely related
    }

    public function index()
    {
        if (!has_permission('doctors', '', 'view')) {
            access_denied('Doctors Schedules');
        }

        $data['title'] = _l('doctor_schedule');
        $data['doctors'] = $this->appointments_model->get_doctors();

        $this->load->view('doctors/schedules', $data);
    }

    public function edit($doctor_id)
    {
        if (!has_permission('doctors', '', 'edit')) {
            access_denied('Doctors Schedules');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->appointments_model->update_schedule($doctor_id, $data);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('schedule')));
            }
            redirect(admin_url('doctors/schedules'));
        }

        $data['title'] = _l('schedule');
        $data['doctor'] = $this->staff_model->get($doctor_id);
        $data['schedule'] = $this->appointments_model->get_schedule($doctor_id);

        // Default schedule structure if empty
        if (empty($data['schedule'])) {
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $data['schedule'] = [];
            foreach ($days as $day) {
                $data['schedule'][] = [
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                    'slot_duration' => 30,
                    'is_available' => 1
                ];
            }
        }

        $this->load->view('doctors/schedule_form', $data);
    }
}
