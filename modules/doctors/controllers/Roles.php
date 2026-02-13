<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Roles extends AdminController
{
    private $allowed_roles = ['Doctor', 'Jr. Doctor', 'Sr. Doctor'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('roles_model');
    }

    /* List all doctor roles */
    public function index()
    {
        if (!has_permission('doctors', '', 'view')) {
            access_denied('doctors');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('doctors', 'tables/roles'));
        }

        $data['title'] = 'Doctor Roles';
        $this->load->view('roles/manage', $data);
    }

    /* Edit existing role (Add disabled) */
    public function role($id = '')
    {
        if (!has_permission('doctors', '', 'view')) {
            access_denied('doctors');
        }

        // Prevent adding new roles
        if ($id == '') {
            access_denied('doctors (Creating roles disabled)');
        }

        if ($this->input->post()) {
            if (!has_permission('doctors', '', 'edit')) {
                access_denied('doctors');
            }

            // Security Check: Ensure we are editing an allowed role
            $role = $this->roles_model->get($id);
            if ($role && !in_array($role->name, $this->allowed_roles)) {
                access_denied('doctors (Restricted Role)');
            }

            // Update only permissions (name is readonly in view, but model might rewrite it if posted)
            $success = $this->roles_model->update($this->input->post(), $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('role')));
            }
            redirect(admin_url('doctors/roles/role/' . $id));
        }

        $role = $this->roles_model->get($id);

        // Security Check
        if ($role && !in_array($role->name, $this->allowed_roles)) {
            access_denied('doctors (Restricted Role)');
        }

        $data['role_staff'] = $this->roles_model->get_role_staff($id);
        $data['role'] = $role;
        $title = _l('edit', _l('role')) . ' ' . $role->name;

        $data['title'] = $title;
        $this->load->view('roles/role', $data);
    }

    /* Delete role - Disabled */
    public function delete($id)
    {
        access_denied('doctors (Deleting roles disabled)');
    }
}
