<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Doctors extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
    }

    private function get_doctor_stats()
    {
        $stats = [
            'total' => [
                'count' => 0,
                'active' => 0,
                'inactive' => 0
            ],
            'consultant' => [
                'count' => 0,
                'new_this_month' => 0
            ],
            'referral' => [
                'count' => 0,
                'new_this_month' => 0
            ],
            'on_call' => [
                'count' => 0,
                'new_this_month' => 0
            ]
        ];

        // Base query for all doctors
        $this->db->select(db_prefix() . 'staff.*, ' . db_prefix() . 'roles.name as role_name');
        $this->db->from(db_prefix() . 'staff');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role', 'left');
        $this->db->where_in(db_prefix() . 'roles.name', ['Doctor', 'Jr. Doctor', 'Sr. Doctor']);
        $doctors = $this->db->get()->result_array();

        $current_month = date('Y-m');

        foreach ($doctors as $doctor) {
            // Total Stats
            $stats['total']['count']++;
            if ($doctor['active'] == 1) {
                $stats['total']['active']++;
            } else {
                $stats['total']['inactive']++;
            }

            // Profile Type Stats
            $profile_type = $doctor['doctor_profile_type'];
            $created_month = date('Y-m', strtotime($doctor['datecreated']));

            if ($profile_type == 'Consultant') {
                $stats['consultant']['count']++;
                if ($created_month == $current_month) {
                    $stats['consultant']['new_this_month']++;
                }
            } elseif ($profile_type == 'Referral') {
                $stats['referral']['count']++;
                if ($created_month == $current_month) {
                    $stats['referral']['new_this_month']++;
                }
            } elseif ($profile_type == 'On-Call') {
                $stats['on_call']['count']++;
                if ($created_month == $current_month) {
                    $stats['on_call']['new_this_month']++;
                }
            }
        }

        return $stats;
    }



    /* List all doctors */
    public function index()
    {
        if (!has_permission('doctors', '', 'view')) {
            access_denied('doctors');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('doctors', 'table'));
        }

        $data['title'] = 'Doctors';
        $data['stats'] = $this->get_doctor_stats();
        $this->load->view('manage', $data);
    }

    /* Add or update doctor */
    public function member($id = '')
    {
        if (!has_permission('doctors', '', 'view')) {
            access_denied('doctors');
        }

        $this->load->model('appointments/appointments_model');

        if ($this->input->post()) {
            $data = $this->input->post();

            // Unset signature_image from data as it is a file input and shouldn't be in insert if it slipped in
            if (isset($data['signature_image'])) {
                unset($data['signature_image']);
            }

            $schedule_data = [];
            if (isset($data['schedule'])) {
                $schedule_data = $data['schedule'];
                unset($data['schedule']);
            }

            if (!isset($data['anytime_appointment'])) {
                $data['anytime_appointment'] = 0;
            }
            // If active checkbox is unchecked, it won't be sent, so set it to 0
            if (!isset($data['active'])) {
                $data['active'] = 0;
            }

            if ($id == '') {
                if (!has_permission('doctors', '', 'create')) {
                    access_denied('doctors');
                }
                $id = $this->staff_model->add($data);
                if ($id) {
                    // Save Schedule
                    if (!empty($schedule_data)) {
                        $this->appointments_model->update_schedule($id, ['schedule' => $schedule_data]);
                    }
                    set_alert('success', _l('added_successfully', _l('staff_member')));
                    redirect(admin_url('doctors/member/' . $id));
                }
            } else {
                if (!has_permission('doctors', '', 'edit')) {
                    access_denied('doctors');
                }
                $success = $this->staff_model->update($data, $id);

                // Save Schedule (always update if posted)
                if (!empty($schedule_data)) {
                    $this->appointments_model->update_schedule($id, ['schedule' => $schedule_data]);
                }

                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('staff_member')));
                }
                redirect(admin_url('doctors/member/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', 'Doctor');
            $data['schedule'] = []; // Default empty schedule
        } else {
            $member = $this->staff_model->get($id);
            if (!$member) {
                blank_page('Staff Member Not Found', 'danger');
            }
            $data['member'] = $member;
            $title = $member->firstname . ' ' . $member->lastname;

            // Fetch Schedule
            $data['schedule'] = $this->appointments_model->get_schedule($id);

            // Fetch Doctor Stats
            // 1. Total Appointments
            $this->db->where('doctor_id', $id);
            $data['total_appointments'] = $this->db->count_all_results(db_prefix() . 'appointments');

            // 2. Unique Patients (Count distinct patient_id)
            $this->db->distinct();
            $this->db->select('patient_id');
            $this->db->where('doctor_id', $id);
            $this->db->where('patient_id IS NOT NULL');
            $this->db->where('patient_id !=', 0);
            $data['unique_patients'] = $this->db->get(db_prefix() . 'appointments')->num_rows();

            // 3. Upcoming Appointments (Date >= Today, Status != cancelled)
            $this->db->where('doctor_id', $id);
            $this->db->where('appointment_date >=', date('Y-m-d'));
            $this->db->where('status !=', 'cancelled');
            $data['upcoming_appointments'] = $this->db->count_all_results(db_prefix() . 'appointments');

            // 4. Pending Appointments
            $this->db->where('doctor_id', $id);
            $this->db->where('status', 'pending');
            $data['pending_appointments'] = $this->db->count_all_results(db_prefix() . 'appointments');
        }

        $this->load->model('roles_model');
        // Filter roles to only show Doctor, Jr. Doctor, Sr. Doctor
        $all_roles = $this->roles_model->get();
        $doctor_roles = array_filter($all_roles, function ($role) {
            return in_array($role['name'], ['Doctor', 'Jr. Doctor', 'Sr. Doctor']);
        });
        $data['roles'] = $doctor_roles;

        // Filter items to only show those in group 'fee'
        $this->db->select(db_prefix() . 'items.*');
        $this->db->from(db_prefix() . 'items');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->where('LOWER(' . db_prefix() . 'items_groups.name)', 'fee');
        $data['items'] = $this->db->get()->result_array();

        $data['title'] = $title;
        $this->load->view('member', $data);
    }
    public function pricing($staff_id)
    {
        if (!has_permission('doctors', '', 'view')) {
            access_denied('doctors');
        }

        // Auto-create table if it doesn't exist
        if (!$this->db->table_exists(db_prefix() . 'doctor_pricing')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'doctor_pricing` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `staff_id` int(11) NOT NULL,
                `test_id` int(11) NOT NULL,
                `referral_amount` decimal(15,2) NOT NULL DEFAULT "0.00",
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');
        }

        $this->load->model('doctors/Doctors_model');
        $this->load->model('invoice_items_model');
        $this->load->model('staff_model');
        $this->load->model('currencies_model');

        // Validate Staff ID
        $member = $this->staff_model->get($staff_id);
        if (!$member) {
            show_404();
        }

        if ($this->input->post()) {
            $success = $this->Doctors_model->update_pricing($staff_id, $this->input->post());
            if ($success) {
                set_alert('success', _l('updated_successfully', 'Pricing'));
            }
            redirect(admin_url('doctors/pricing/' . $staff_id));
        }

        // Get all Item Groups
        $groups = $this->invoice_items_model->get_groups();
        $data['groups'] = $groups;

        // Get all items organized by group
        $this->db->select(db_prefix() . 'items.*, ' . db_prefix() . 'departments.name as department_name, ' . db_prefix() . 'items_groups.name as group_name');
        $this->db->join(db_prefix() . 'departments', db_prefix() . 'departments.departmentid = ' . db_prefix() . 'items.department_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $items = $this->db->get(db_prefix() . 'items')->result_array();

        $items_by_group = [];
        foreach ($groups as $group) {
            $items_by_group[$group['id']] = [];
        }
        // Also handle items without a group if any (optional, but good practice)
        $items_by_group[0] = [];

        foreach ($items as $item) {
            $group_id = $item['group_id'] ? $item['group_id'] : 0;
            if (!isset($items_by_group[$group_id])) {
                $items_by_group[$group_id] = [];
            }
            $items_by_group[$group_id][] = $item;
        }

        $data['items_by_group'] = $items_by_group;

        // Get existing pricing
        $data['pricing'] = $this->Doctors_model->get_pricing($staff_id);
        $data['currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = 'Pricing for ' . $member->firstname . ' ' . $member->lastname;

        $this->load->view('pricing', $data);
    }

    public function bulk_update_pricing($staff_id)
    {
        if (!has_permission('doctors', '', 'create') && !has_permission('doctors', '', 'edit')) {
            access_denied('doctors');
        }

        if ($this->input->post()) {
            $action = $this->input->post('bulk_action');
            $value = $this->input->post('bulk_value');

            $this->load->model('invoice_items_model');
            $this->load->model('doctors/Doctors_model');

            // Get all items for bulk update (regardless of group)
            $tests = $this->db->get(db_prefix() . 'items')->result_array();

            $pricing_data = ['referral_amount' => []];

            foreach ($tests as $test) {
                $new_price = 0;
                $standard_price = $test['rate'];
                // Assuming tests don't have separate 'doctor_price' in items table, using standard rate or existing logic

                switch ($action) {
                    case 'percent_inc':
                        if (is_numeric($value)) {
                            $new_price = $standard_price + ($standard_price * ($value / 100));
                        }
                        break;
                    case 'percent_dec':
                        if (is_numeric($value)) {
                            $new_price = $standard_price - ($standard_price * ($value / 100));
                        }
                        break;
                    case 'amount_inc':
                        if (is_numeric($value)) {
                            $new_price = $standard_price + $value;
                        }
                        break;
                    case 'amount_dec':
                        if (is_numeric($value)) {
                            $new_price = $standard_price - $value;
                        }
                        break;
                    case 'sync_standard':
                        $new_price = $standard_price;
                        break;
                }

                // Ensure price is not negative
                $new_price = $new_price < 0 ? 0 : $new_price;

                $pricing_data['referral_amount'][$test['id']] = $new_price;
            }

            $success = $this->Doctors_model->update_pricing($staff_id, $pricing_data);

            if ($success) {
                set_alert('success', _l('updated_successfully', 'Pricing'));
            } else {
                set_alert('warning', 'No changes made or error occurred.');
            }
        }
        redirect(admin_url('doctors/pricing/' . $staff_id));
    }
}
