<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Token_system extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('token_system_model');
    }

    public function index()
    {
        if (!has_permission('token_system', '', 'view')) {
            access_denied('Token System');
        }

        if ($this->input->post()) {
            if (!has_permission('token_system', '', 'create')) {
                access_denied(_l('token_system'));
            }
            $data = $this->input->post();

            // Validation or logic if doctor_id required

            $id = $this->token_system_model->add($data);
            if ($id) {
                set_alert('success', _l('token_created_successfully'));
            } else {
                set_alert('warning', _l('failed_to_create_token'));
            }
            redirect(admin_url('token_system'));
        }

        $doctor_filter = $this->input->get('doctor_id');

        $data['title'] = _l('token_system');

        // Fetch valid doctors for dropdown
        // Roles: Doctor (usually ID 1 or name based?), Jr. Doctor, Sr. Doctor.
        // We need to find the role IDs first or filter staff by role name.
        // Assuming Roles exist, let's look them up or fetch all staff and filter in PHP if dataset small.
        // Better: use direct query for performance.
        $this->db->select('staffid, firstname, lastname');
        $this->db->from(db_prefix() . 'staff');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role', 'left');
        $this->db->where_in(db_prefix() . 'roles.name', ['Doctor', 'Jr. Doctor', 'Sr. Doctor']); // Adjust role names if different
        $this->db->where(db_prefix() . 'staff.active', 1); // Only active
        $data['doctors'] = $this->db->get()->result_array();

        $data['tokens'] = $this->token_system_model->get_todays_tokens('', $doctor_filter);
        $data['doctor_filter'] = $doctor_filter;

        $this->load->view('manage', $data);
    }

    public function update_status($id, $status)
    {
        if (!has_permission('token_system', '', 'edit')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $success = $this->token_system_model->update_status($id, $status);
        echo json_encode(['success' => $success]);
    }

    public function delete($id)
    {
        if (!has_permission('token_system', '', 'delete')) {
            access_denied(_l('token_system'));
        }
        $success = $this->token_system_model->delete($id);
        if ($success) {
            set_alert('success', _l('token_deleted_successfully'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Token'));
        }
        redirect(admin_url('token_system'));
    }
}
