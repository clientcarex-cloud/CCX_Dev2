<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Surgeries extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('surgeries_model');
        $this->load->model('clients_model');
        $this->load->model('staff_model');
    }

    /* List all surgeries */
    public function index()
    {
        if (!has_permission('surgeries', '', 'view')) {
            access_denied('surgeries');
        }

        $data['title'] = 'Surgeries';
        $data['surgeries'] = $this->surgeries_model->get_surgeries();
        $this->load->view('manage', $data);
    }

    /* Settings page */
    public function settings()
    {
        if (!has_permission('surgeries', '', 'view')) {
            access_denied('surgeries');
        }

        $data['title'] = 'Surgery Settings';
        $this->load->view('settings', $data);
    }

    /* Table data generation */
    public function table($name = '')
    {
        if (!has_permission('surgeries', '', 'view')) {
            ajax_access_denied();
        }

        if ($name == 'surgeries') {
            $this->app->get_table_data(module_views_path('surgeries', 'tables/surgeries'));
        } elseif ($name == 'types') {
            $this->app->get_table_data(module_views_path('surgeries', 'tables/surgery_types'));
        }
    }

    /* Add or edit surgery record */
    public function surgery($id = '')
    {
        if (!has_permission('surgeries', '', 'view')) { // Adjust permission as needed, e.g., 'create'/'edit'
            access_denied('surgeries');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('surgeries', '', 'create')) {
                    access_denied('surgeries');
                }
                $id = $this->surgeries_model->add_surgery($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', 'Surgery'));
                    echo json_encode(['success' => true, 'message' => _l('added_successfully', 'Surgery')]);
                } else {
                    $db_error = $this->db->error();
                    echo json_encode([
                        'success' => false,
                        'message' => 'Database Error: ' . $db_error['message']
                    ]);
                }
            } else {
                if (!has_permission('surgeries', '', 'edit')) {
                    access_denied('surgeries');
                }
                $success = $this->surgeries_model->update_surgery($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', 'Surgery'));
                    echo json_encode(['success' => true, 'message' => _l('updated_successfully', 'Surgery')]);
                } else {
                    $db_error = $this->db->error();
                    echo json_encode([
                        'success' => false,
                        'message' => 'Database Error: ' . $db_error['message']
                    ]);
                }
            }
            die;
        }
    }

    /* Add or edit surgery type */
    // Renamed from 'type' to 'surgery_type' to avoid potential conflicts or for clarity, 
    // but sticking to plan 'type' is fine if usually safe. Let's use 'type' to match plan.
    public function type($id = '')
    {
        if (!has_permission('surgeries', '', 'view')) {
            access_denied('surgeries');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('surgeries', '', 'create')) {
                    access_denied('surgeries');
                }
                $id = $this->surgeries_model->add_surgery_type($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', 'Surgery Type'));
                    echo json_encode(['success' => true, 'message' => _l('added_successfully', 'Surgery Type')]);
                }
            } else {
                if (!has_permission('surgeries', '', 'edit')) {
                    access_denied('surgeries');
                }
                $success = $this->surgeries_model->update_surgery_type($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', 'Surgery Type'));
                    echo json_encode(['success' => true, 'message' => _l('updated_successfully', 'Surgery Type')]);
                }
            }
            die;
        }
    }

    /* Delete surgery */
    public function delete($id)
    {
        if (!has_permission('surgeries', '', 'delete')) {
            access_denied('surgeries');
        }

        if (!$id) {
            redirect(admin_url('surgeries'));
        }

        $response = $this->surgeries_model->delete_surgery($id);
        if ($response) {
            set_alert('success', _l('deleted', 'Surgery'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Surgery'));
        }
        redirect(admin_url('surgeries'));
    }

    /* Delete surgery type */
    public function delete_type($id)
    {
        if (!has_permission('surgeries', '', 'delete')) {
            access_denied('surgeries');
        }

        if (!$id) {
            redirect(admin_url('surgeries/settings'));
        }

        $response = $this->surgeries_model->delete_surgery_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('surgery_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('surgery_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('surgery_type')));
        }
        redirect(admin_url('surgeries/settings'));
    }
}
