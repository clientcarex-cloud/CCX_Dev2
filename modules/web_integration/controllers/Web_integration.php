<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Web_integration extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('web_integration_model');
    }

    public function index()
    {
        if (!has_permission('web_integration', '', 'view')) {
            access_denied('web_integration');
        }

        $data['title'] = 'Web Integration Forms';
        $data['forms'] = $this->web_integration_model->get_form();
        $this->load->view('manage', $data);
    }

    public function form($id = '')
    {
        if (!has_permission('web_integration', '', 'view')) {
            access_denied('web_integration');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('web_integration', '', 'create')) {
                    access_denied('web_integration');
                }
                $id = $this->web_integration_model->add_form($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', 'Form'));
                    redirect(admin_url('web_integration/form/' . $id));
                }
            } else {
                if (!has_permission('web_integration', '', 'edit')) {
                    access_denied('web_integration');
                }
                // Check if we are updating fields or form details
                if (isset($data['name'])) {
                    $success = $this->web_integration_model->update_form($data, $id);
                    if ($success) {
                        set_alert('success', _l('updated_successfully', 'Form'));
                    }
                } else {
                    // Updating fields
                    $success = $this->web_integration_model->update_form_fields($data, $id);
                    if ($success) {
                        set_alert('success', _l('updated_successfully', 'Fields'));
                    }
                }
                redirect(admin_url('web_integration/form/' . $id));
            }
        }

        if ($id == '') {
            $data['title'] = 'Add New Form';
        } else {
            $data['form'] = $this->web_integration_model->get_form($id);
            $data['fields'] = $this->web_integration_model->get_form_fields($id);
            $data['title'] = 'Edit Form - ' . $data['form']->name;
        }

        $this->load->model('leads_model');
        $data['sources'] = $this->leads_model->get_source();
        $this->load->model('staff_model');
        $data['members'] = $this->staff_model->get('', ['active' => 1]);

        $this->load->view('form_builder', $data);
    }

    public function delete_form($id)
    {
        if (!has_permission('web_integration', '', 'delete')) {
            access_denied('web_integration');
        }

        if (!$id) {
            redirect(admin_url('web_integration'));
        }

        $response = $this->web_integration_model->delete_form($id);
        if ($response) {
            set_alert('success', _l('deleted', 'Form'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Form'));
        }
        redirect(admin_url('web_integration'));
    }

    public function entries($form_id)
    {
        if (!has_permission('web_integration', '', 'view')) {
            access_denied('web_integration');
        }

        $data['form'] = $this->web_integration_model->get_form($form_id);
        if (!$data['form']) {
            show_404();
        }

        $data['title'] = 'Entries - ' . $data['form']->name;
        $data['entries'] = $this->web_integration_model->get_entries($form_id);
        $this->load->view('entries', $data);
    }

    public function get_iframe_code($form_id)
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->web_integration_model->get_form($form_id);
            $url = site_url('web_integration/forms/index/' . $form->form_key);
            $iframe_code = '<iframe src="' . $url . '" width="100%" height="800" frameborder="0"></iframe>';
            echo json_encode(['iframe_code' => $iframe_code]);
        }
    }
    public function get_api_details($form_id)
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->web_integration_model->get_form($form_id);
            $fields = $this->web_integration_model->get_form_fields($form_id);

            $appointment_info = [];
            if ($form->link_with_appointments == 1) {
                $appointment_info = [
                    'slots_url' => site_url('web_integration/forms/get_doctor_slots'),
                    'is_active' => true
                ];
            }

            echo json_encode(['form' => $form, 'fields' => $fields, 'appointment_info' => $appointment_info]);
        }
    }
}
