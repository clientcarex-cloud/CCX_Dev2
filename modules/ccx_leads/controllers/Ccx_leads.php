<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ccx_leads extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ccx_leads_model');
    }

    public function index()
    {
        if (!has_permission('ccx_leads', '', 'view')) {
            access_denied('CCX Leads');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('ccx_leads', 'table'));
        }

        $data['title'] = _l('ccx_leads');
        $this->load->view('manage', $data);
    }

    public function lead($id = '')
    {
        if (!has_permission('ccx_leads', '', 'view')) {
            access_denied('CCX Leads');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('ccx_leads', '', 'create')) {
                    access_denied('CCX Leads');
                }
                $id = $this->ccx_leads_model->add_lead($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('ccx_lead')));
                    redirect(admin_url('ccx_leads'));
                }
            } else {
                if (!has_permission('ccx_leads', '', 'edit')) {
                    access_denied('CCX Leads');
                }
                $success = $this->ccx_leads_model->update_lead($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('ccx_lead')));
                }
                redirect(admin_url('ccx_leads/lead/' . $id));
            }
        }

        if ($id == '') {
            $data['title'] = _l('add_new', _l('ccx_lead'));
        } else {
            $data['lead'] = $this->ccx_leads_model->get_lead($id);
            $data['call_logs'] = $this->ccx_leads_model->get_call_logs($id);
            $data['title'] = _l('edit', _l('ccx_lead'));
        }

        $this->load->view('lead', $data);
    }

    public function save_call_log()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $insert_id = $this->ccx_leads_model->add_call_log($data);
            if ($insert_id) {
                echo json_encode(['success' => true, 'message' => _l('added_successfully', 'Call Log')]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('problem_adding', 'Call Log')]);
            }
        }
    }

    public function delete($id)
    {
        if (!has_permission('ccx_leads', '', 'delete')) {
            access_denied('CCX Leads');
        }

        if (!$id) {
            redirect(admin_url('ccx_leads'));
        }

        $response = $this->ccx_leads_model->delete_lead($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('ccx_lead')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ccx_lead')));
        }
        redirect(admin_url('ccx_leads'));
    }
}
