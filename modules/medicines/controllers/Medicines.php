<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Medicines extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('medicines_model');
    }

    public function index()
    {
        if (!has_permission('medicines', '', 'view')) {
            access_denied('medicines');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if (isset($data['id']) && $data['id'] != '') {
                if (!has_permission('medicines', '', 'edit')) {
                    access_denied('medicines');
                }
                $success = $this->medicines_model->update($data, $data['id']);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('medicine')));
                }
                redirect(admin_url('medicines'));
            } else {
                if (!has_permission('medicines', '', 'create')) {
                    access_denied('medicines');
                }
                $id = $this->medicines_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('medicine')));
                }
                redirect(admin_url('medicines'));
            }
        }

        $this->load->model('master_data/master_data_model');
        $data['medicine_types'] = $this->master_data_model->get_medicine_types();

        $data['medicines'] = $this->medicines_model->get();
        $data['title'] = _l('medicines');
        $this->load->view('manage', $data);
    }

    public function json($id)
    {
        if (!has_permission('medicines', '', 'view')) {
            ajax_access_denied();
        }
        $medicine = $this->medicines_model->get($id);
        echo json_encode($medicine);
    }
    public function delete($id)
    {
        if (!has_permission('medicines', '', 'delete')) {
            access_denied('medicines');
        }
        $response = $this->medicines_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('medicine')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('medicine')));
        }
        redirect(admin_url('medicines'));
    }
}
