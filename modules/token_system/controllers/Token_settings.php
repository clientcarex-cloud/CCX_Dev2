<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Token_settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('token_system_model');
    }

    public function index()
    {
        if (!has_permission('token_system', '', 'view')) {
            access_denied(_l('token_system'));
        }

        if ($this->input->post()) {
            if (!has_permission('token_system', '', 'edit')) {
                access_denied(_l('token_system'));
            }

            update_option('token_system_workflow', $this->input->post('token_system_workflow'));
            set_alert('success', _l('updated_successfully', _l('settings')));
            redirect(admin_url('token_system/token_settings'));
        }

        $data['title'] = _l('settings');
        $this->load->view('settings', $data);
    }
}
