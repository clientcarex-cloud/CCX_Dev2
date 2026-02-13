<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Patients extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('patients_model');
        $this->load->model('payment_modes_model');
        $this->load->model('currencies_model');
        // Load Follow Ups Model for statuses
        $this->load->model('follow_ups/follow_ups_model');
        $this->load->model('staff_model');
    }

    /* List all patients */
    public function index()
    {
        if (!has_permission('patients', '', 'view')) {
            access_denied('Patients');
        }

        $this->load->model('client_groups_model');

        $data['title'] = _l('patients');

        // Filters - Removed
        $filters = [];
        $data['filters'] = $filters;

        // Fetch Groups
        $data['customer_groups'] = $this->client_groups_model->get_groups();

        // Dashboard Stats
        $data['dashboard_stats'] = $this->patients_model->get_dashboard_stats();


        // Dashboard Stats
        $data['dashboard_stats'] = $this->patients_model->get_dashboard_stats();

        // Used for statuses in the filter (if we add them later) or modals
        // $data['statuses'] = $this->follow_ups_model->get_statuses();

        // Fetch Statuses for Pop-up
        $data['statuses'] = $this->follow_ups_model->get_statuses();

        // Fetch Members for Reminders
        $data['members'] = $this->staff_model->get('', ['active' => 1]);

        $this->load->view('manage', $data);
    }

    public function table()
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }

        $this->load->view('table');
    }

    /* Settings Page */
    public function settings()
    {
        if (!is_admin()) {
            access_denied('Patients Settings');
        }

        $data['title'] = _l('patient_settings');

        // Fetch Item Groups for Billing Rules
        $this->load->model('invoice_items_model');
        $data['item_groups'] = $this->invoice_items_model->get_groups();

        $this->load->view('settings', $data);
    }

    public function save_settings()
    {
        if (!is_admin()) {
            access_denied('Patients Settings');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            // List of all expected checkbox fields
            $fields = [
                'patients_req_uid',
                'patients_req_primary_doctor',
                'patients_req_age',
                'patients_req_referral_doctor',
                'patients_req_attender_name',
                'patients_req_email',
                'patients_req_address',
                'patients_req_referral_lab',
                'patients_req_company',
                'patients_req_prescription',
                'patients_print_invoice_on_visit',
                // Visit Filter Settings
                'patients_visit_show_search',
                'patients_visit_show_date_range',
                'patients_visit_show_user',
                'patients_visit_show_item_status',
                'patients_visit_show_payment_status',
                'patients_visit_show_limit',
                'patients_visit_show_reset'
            ];

            foreach ($fields as $field) {
                $val = isset($data[$field]) ? '1' : '0';
                // Check if option exists, update or add
                if (get_option($field) === null) {
                    add_option($field, $val);
                } else {
                    update_option($field, $val);
                }
            }

            // Handle non-checkbox settings
            if (isset($data['patients_name_format'])) {
                update_option('patients_name_format', $data['patients_name_format']);
            }

            if (isset($data['patients_visit_default_date_range'])) {
                update_option('patients_visit_default_date_range', $data['patients_visit_default_date_range']);
            }

            if (isset($data['patients_visit_sort_order'])) {
                update_option('patients_visit_sort_order', $data['patients_visit_sort_order']);
            }

            // Time Locker Settings
            if (isset($data['patients_visit_time_locker'])) {
                update_option('patients_visit_time_locker', $data['patients_visit_time_locker']);
            }
            if (isset($data['patients_visit_locker_fields'])) {
                update_option('patients_visit_locker_fields', json_encode($data['patients_visit_locker_fields']));
            } else {
                update_option('patients_visit_locker_fields', json_encode([]));
            }

            // Handle Billing Rules
            if (isset($data['billing_rules'])) {
                update_option('patients_billing_rules', json_encode($data['billing_rules']));
            } else {
                update_option('patients_billing_rules', json_encode([]));
            }

            // Handle Restricted Groups
            if (isset($data['restricted_groups'])) {
                update_option('patients_restricted_groups', json_encode($data['restricted_groups']));
            } else {
                update_option('patients_restricted_groups', json_encode([]));
            }

            set_alert('success', _l('settings_updated'));
        }
        redirect(admin_url('patients/settings'));
    }
    /* Add or edit patient - DEPRECATED / REMOVED */
    /* Use Visits controller instead */

    /* Delete patient */
    public function delete($id)
    {
        if (!has_permission('patients', '', 'delete')) {
            access_denied('Patients');
        }
        if (!$id) {
            redirect(admin_url('patients'));
        }
        $response = $this->patients_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('patient_deleted'));
        } else {
            set_alert('warning', _l('patient_problem_deleting'));
        }
        redirect(admin_url('patients'));
    }
    /**
     * Quick add referral doctor
     */
    public function add_referral_doctor()
    {
        if (!has_permission('patients', '', 'create')) {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            die;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('full_name', 'Full Name', 'required');
        $this->form_validation->set_rules('mobile_number', 'Mobile Number', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            die;
        }

        $data = $this->input->post();

        // Get Role ID for 'Doctor'
        $this->db->where('name', 'Doctor');
        $role = $this->db->get(db_prefix() . 'roles')->row();
        $role_id = $role ? $role->roleid : 0;

        if (!$role_id) {
            $this->db->where_in('name', ['Doctor', 'Jr. Doctor', 'Sr. Doctor']);
            $role = $this->db->get(db_prefix() . 'roles')->row();
            $role_id = $role ? $role->roleid : 0;
        }

        // Split name
        $names = explode(' ', $data['full_name'], 2);
        $firstname = $names[0];
        $lastname = isset($names[1]) ? $names[1] : '';

        // Generate a dummy unique email to satisfy Unique constraint if exists
        $email = strtolower($firstname . '.' . $lastname . '.' . time() . '@referral.com');
        // Clean email
        $email = preg_replace('/[^a-z0-9@.]/', '', $email);

        $staff_data = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'phonenumber' => $data['mobile_number'],
            'area' => $data['area'],
            'doctor_profile_type' => 'Referral',
            'role' => $role_id,
            'active' => 1,
            'email' => $email,
            'password' => '$2y$10$quickaddpasswordhashdummy', // Direct hash or whatever
            'datecreated' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'staff', $staff_data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            echo json_encode([
                'success' => true,
                'id' => $insert_id,
                'name' => $firstname . ' ' . $lastname
            ]);
        } else {
            // Log error
            log_activity('Failed to quick add referral doctor: ' . $this->db->error()['message']);
            echo json_encode(['success' => false, 'message' => 'Failed to add doctor']);
        }
    }

    /* Edit patient or add new patient */
    public function patient($id = '')
    {
        $this->load->model('clients_model');
        $this->load->model('contracts_model');
        $this->load->model('proposals_model');
        $this->load->model('invoices_model');
        $this->load->model('credit_notes_model');
        $this->load->model('estimates_model');
        $this->load->model('projects_model');
        $this->load->model('payment_modes_model');
        $this->load->model('misc_model');
        $this->load->model('gdpr_model');

        if (staff_cant('view', 'customers')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (staff_cant('create', 'customers')) {
                    access_denied('customers');
                }

                $data = $this->input->post();

                $save_and_add_contact = false;
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                $id = $this->clients_model->add($data);
                if (staff_cant('view', 'customers')) {
                    $assign['customer_admins'] = [];
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->clients_model->assign_admins($assign, $id);
                }
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('client')));
                    if ($save_and_add_contact == false) {
                        redirect(admin_url('patients/patient/' . $id));
                    } else {
                        redirect(admin_url('patients/patient/' . $id . '?group=contacts&new_contact=true'));
                    }
                }
            } else {
                if (staff_cant('edit', 'customers')) {
                    if (!is_customer_admin($id)) {
                        access_denied('customers');
                    }
                }
                $success = $this->clients_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfully', _l('client')));
                }
                redirect(admin_url('patients/patient/' . $id));
            }
        }

        $group = !$this->input->get('group') ? 'profile' : $this->input->get('group');
        $data['group'] = $group;

        if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {
            redirect(admin_url('patients/patient/' . $id . '?group=contacts&contactid=' . $contact_id));
        }

        // Customer groups
        $data['groups'] = $this->clients_model->get_groups();

        if ($id == '') {
            $title = _l('add_new', _l('client'));
        } else {
            $client = $this->clients_model->get($id);
            $data['customer_tabs'] = get_customer_profile_tabs($id);

            // Access keys to remove: contacts, proposals, credit_notes, estimates, subscriptions, projects
            $tabs_to_remove = ['contacts', 'proposals', 'credit_notes', 'estimates', 'subscriptions', 'projects'];
            foreach ($tabs_to_remove as $key) {
                if (isset($data['customer_tabs'][$key])) {
                    unset($data['customer_tabs'][$key]);
                }
            }

            if (!$client) {
                show_404();
            }

            $data['contacts'] = $this->clients_model->get_contacts($id);
            $data['tab'] = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;

            if (!$data['tab']) {
                show_404();
            }

            // Fetch data based on groups
            if ($group == 'profile') {
                $data['customer_groups'] = $this->clients_model->get_customer_groups($id);
                $data['customer_admins'] = $this->clients_model->get_admins($id);
            } elseif ($group == 'attachments') {
                $data['attachments'] = get_all_customer_attachments($id);
            } elseif ($group == 'vault') {
                $data['vault_entries'] = hooks()->apply_filters('check_vault_entries_visibility', $this->clients_model->get_vault_entries($id));

                if ($data['vault_entries'] === -1) {
                    $data['vault_entries'] = [];
                }
            } elseif ($group == 'estimates') {
                $data['estimate_statuses'] = $this->estimates_model->get_statuses();
            } elseif ($group == 'invoices') {
                $data['invoice_statuses'] = $this->invoices_model->get_statuses();
            } elseif ($group == 'credit_notes') {
                $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
                $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($id);
            } elseif ($group == 'payments') {
                $data['payment_modes'] = $this->payment_modes_model->get();
            } elseif ($group == 'notes') {
                $data['user_notes'] = $this->misc_model->get_notes($id, 'customer');
            } elseif ($group == 'projects') {
                $data['project_statuses'] = $this->projects_model->get_project_statuses();
            } elseif ($group == 'statement') {
                if (staff_cant('view', 'invoices') && staff_cant('view', 'payments')) {
                    set_alert('danger', _l('access_denied'));
                    redirect(admin_url('patients/patient/' . $id));
                }

                $data = array_merge($data, prepare_mail_preview_data('customer_statement', $id));
            } elseif ($group == 'map') {
                if (get_option('google_api_key') != '' && staff_can('edit', 'customers')) {
                    $this->load->library('google_map_js');
                    $data['google_map_js'] = $this->google_map_js->get_map();
                }
            }



            $data['staff'] = $this->staff_model->get('', ['active' => 1]);
            $data['client'] = $client;
            $title = $client->company;

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_customer_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        if ($id != '') {
            $customer_currency = $data['client']->default_currency;

            foreach ($data['currencies'] as $currency) {
                if ($customer_currency != 0) {
                    if ($currency['id'] == $customer_currency) {
                        $customer_currency = $currency;

                        break;
                    }
                } else {
                    if ($currency['isdefault'] == 1) {
                        $customer_currency = $currency;

                        break;
                    }
                }
            }

            if (is_array($customer_currency)) {
                $customer_currency = (object) $customer_currency;
            }

            $data['customer_currency'] = $customer_currency;

            $slug_zip_folder = (
                $client->company != ''
                ? $client->company
                : get_contact_full_name(get_primary_contact_user_id($client->userid))
            );

            $data['zip_in_folder'] = slug_it($slug_zip_folder);
        }

        $data['bodyclass'] = 'customer-profile dynamic-create-groups';

        $data['title'] = $title;
        // Load custom view
        $this->load->view('patients/patient', $data);
    }



}
