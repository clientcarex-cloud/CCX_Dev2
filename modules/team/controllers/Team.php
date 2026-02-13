<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Team extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
        $this->load->model('roles_model');
    }

    public function index()
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }

        if ($this->input->is_ajax_request()) {
            $this->load->view('table_data');
        }

        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['title'] = 'Team Management';
        $this->load->view('manage', $data);
    }

    public function member($id = '')
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }

        $this->load->model('departments_model');

        if ($this->input->post()) {
            $data = $this->input->post();
            // Basic handling similar to Staff controller
            // ... (We will implement the detailed save logic later or reuse Staff model)
            if ($id == '') {
                if (!has_permission('staff', '', 'create')) {
                    access_denied('staff');
                }
                $id = $this->staff_model->add($data);
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('staff_member')));
                    redirect(admin_url('team/member/' . $id));
                }
            } else {
                if (!has_permission('staff', '', 'edit')) {
                    access_denied('staff');
                }
                handle_staff_profile_image_upload($id);
                $response = $this->staff_model->update($data, $id);
                if ($response) {
                    set_alert('success', _l('updated_successfully', _l('staff_member')));
                }
                redirect(admin_url('team/member/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('staff_member'));
        } else {
            $member = $this->staff_model->get($id);
            if (!$member) {
                blank_page('Staff Member Not Found', 'danger');
            }
            $data['member'] = $member;
            $title = $member->firstname . ' ' . $member->lastname;
            $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);
        }

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['roles'] = $this->roles_model->get();
        $data['user_notes'] = $this->misc_model->get_notes($id, 'staff');
        $data['departments'] = $this->departments_model->get();
        $data['title'] = $title;

        $this->load->view('member', $data);
    }

    public function delete($id)
    {
        if (!has_permission('staff', '', 'delete')) {
            access_denied('Delete Staff');
        }

        // Check if we are trying to delete the current user or admin
        if ($id == get_staff_user_id() || $id == 1) { // Assuming ID 1 is main admin, or check is_admin($id) logic from staff controller
            set_alert('warning', _l('staff_cant_remove_main_admin'));
            redirect(admin_url('team'));
        }

        $success = $this->staff_model->delete($id, $this->input->post('transfer_data_to'));
        if ($success) {
            set_alert('success', _l('deleted', _l('staff_member')));
        }
        redirect(admin_url('team'));
    }
    public function roles()
    {
        if (staff_cant('view', 'roles')) {
            access_denied('roles');
        }
        if ($this->input->is_ajax_request()) {
            $this->load->view('role_table_data');
        }
        $data['title'] = _l('all_roles');
        $this->load->view('roles', $data);
    }

    public function role($id = '')
    {
        if (staff_cant('view', 'roles')) {
            access_denied('roles');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (staff_cant('create', 'roles')) {
                    access_denied('roles');
                }
                $id = $this->roles_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('role')));
                    redirect(admin_url('team/role/' . $id));
                }
            } else {
                if (staff_cant('edit', 'roles')) {
                    access_denied('roles');
                }
                $success = $this->roles_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('role')));
                }
                redirect(admin_url('team/role/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('role'));
        } else {
            $data['role_staff'] = $this->roles_model->get_role_staff($id);
            $role = $this->roles_model->get($id);
            $data['role'] = $role;
            $title = _l('edit', _l('role')) . ' ' . $role->name;
        }
        $data['title'] = $title;
        $this->load->view('role', $data);
    }

    public function delete_role($id)
    {
        if (staff_cant('delete', 'roles')) {
            access_denied('roles');
        }
        if (!$id) {
            redirect(admin_url('team/roles'));
        }
        $response = $this->roles_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('role_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('role')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('role_lowercase')));
        }
        redirect(admin_url('team/roles'));
    }
}
