<?php

defined('BASEPATH') or exit('No direct script access allowed');

class B2b_labs extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Check permission - implementing standard check
        if (!has_permission('b2b_labs', '', 'view')) {
            access_denied('b2b_labs');
        }

        $this->load->model('staff_model');

        // Get "Referral Lab" role ID
        $this->db->where('name', 'Referral Lab');
        $role = $this->db->get(db_prefix() . 'roles')->row();

        $data['staff_members'] = [];
        if ($role) {
            $data['staff_members'] = $this->staff_model->get('', ['role' => $role->roleid]);
        }

        $data['title'] = 'Referral Lab';
        $this->load->view('manage', $data);
    }

    public function settings()
    {
        $data['title'] = 'Referral Lab Settings';
        $this->load->view('settings', $data);
    }

    public function create_user()
    {
        if (!has_permission('b2b_labs', '', 'create')) {
            access_denied('b2b_labs');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $this->load->model('staff_model');

            // Get "Referral Lab" role ID
            $this->db->where('name', 'Referral Lab');
            $role = $this->db->get(db_prefix() . 'roles')->row();

            if ($role) {
                $data['role'] = $role->roleid;
            }

            $id = $this->staff_model->add($data);

            if ($id) {
                set_alert('success', _l('added_successfully', _l('staff_member')));
            }

            redirect(admin_url('b2b_labs'));
        }
    }
    public function pricing($staff_id)
    {
        if (!has_permission('b2b_labs', '', 'view')) {
            access_denied('b2b_labs');
        }

        // Auto-create table if it doesn't exist (Fix for "page not working" if table missing)
        if (!$this->db->table_exists(db_prefix() . 'referral_lab_pricing')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'referral_lab_pricing` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `staff_id` int(11) NOT NULL,
                `test_id` int(11) NOT NULL,
                `referral_price` decimal(15,2) NOT NULL DEFAULT "0.00",
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');
        }

        $this->load->model('b2b_labs/B2b_labs_model');
        $this->load->model('invoice_items_model');
        $this->load->model('staff_model');
        $this->load->model('currencies_model');

        // Validate Staff ID
        $member = $this->staff_model->get($staff_id);
        if (!$member) {
            show_404();
        }

        if ($this->input->post()) {
            $success = $this->B2b_labs_model->update_pricing($staff_id, $this->input->post());
            if ($success) {
                set_alert('success', _l('updated_successfully', 'Pricing'));
            }
            redirect(admin_url('b2b_labs/pricing/' . $staff_id));
        }

        // Get all "Tests"
        // First get Tests group id
        $groups = $this->invoice_items_model->get_groups();
        $tests_group_id = '';
        foreach ($groups as $group) {
            if (strtolower($group['name']) == 'tests') {
                $tests_group_id = $group['id'];
                break;
            }
        }

        // Get items for this group
        if ($tests_group_id) {
            $this->db->select(db_prefix() . 'items.*, ' . db_prefix() . 'departments.name as department_name');
            $this->db->join(db_prefix() . 'departments', db_prefix() . 'departments.departmentid = ' . db_prefix() . 'items.department_id', 'left');
            $this->db->where('group_id', $tests_group_id);
            $data['tests'] = $this->db->get(db_prefix() . 'items')->result_array();
        } else {
            $data['tests'] = [];
        }

        // Get existing pricing
        $data['pricing'] = $this->B2b_labs_model->get_pricing($staff_id);
        $data['currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = 'Pricing for ' . $member->firstname . ' ' . $member->lastname;

        $this->load->view('pricing', $data);
    }


    public function bulk_update_pricing($staff_id)
    {
        if (!has_permission('b2b_labs', '', 'create') && !has_permission('b2b_labs', '', 'edit')) {
            access_denied('b2b_labs');
        }

        if ($this->input->post()) {
            $action = $this->input->post('bulk_action');
            $value = $this->input->post('bulk_value');

            $this->load->model('invoice_items_model');
            $this->load->model('b2b_labs/B2b_labs_model');

            // Get Tests Group ID
            $groups = $this->invoice_items_model->get_groups();
            $tests_group_id = '';
            foreach ($groups as $group) {
                if (strtolower($group['name']) == 'tests') {
                    $tests_group_id = $group['id'];
                    break;
                }
            }

            if (!$tests_group_id) {
                set_alert('warning', 'Tests group not found.');
                redirect(admin_url('b2b_labs/pricing/' . $staff_id));
            }

            // Get all tests with B2B price and Standard Rate
            $this->db->where('group_id', $tests_group_id);
            $tests = $this->db->get(db_prefix() . 'items')->result_array();

            $pricing_data = ['referral_price' => []];

            foreach ($tests as $test) {
                $new_price = 0;
                $standard_price = $test['rate'];
                $b2b_price = isset($test['b2b_price']) ? $test['b2b_price'] : 0;

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
                    case 'sync_b2b':
                        $new_price = $b2b_price;
                        break;
                    case 'sync_standard':
                        $new_price = $standard_price;
                        break;
                }

                // Ensure price is not negative
                $new_price = $new_price < 0 ? 0 : $new_price;

                $pricing_data['referral_price'][$test['id']] = $new_price;
            }

            $success = $this->B2b_labs_model->update_pricing($staff_id, $pricing_data);

            if ($success) {
                set_alert('success', _l('updated_successfully', 'Pricing'));
            } else {
                set_alert('warning', 'No changes made or error occurred.');
            }
        }
        redirect(admin_url('b2b_labs/pricing/' . $staff_id));
    }
}
