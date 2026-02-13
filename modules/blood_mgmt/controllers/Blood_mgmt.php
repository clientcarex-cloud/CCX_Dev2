<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Blood_mgmt extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('blood_mgmt_model');
    }

    /* List all donors */
    public function index()
    {
        if (!has_permission('blood_mgmt', '', 'view')) {
            access_denied('blood_mgmt');
        }

        $this->load->view('manage');
    }

    public function donors()
    {
        if (!has_permission('blood_mgmt', '', 'view')) {
            access_denied('blood_mgmt');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('blood_mgmt', 'tables/donors'));
        }
        $data['title'] = _l('blood_donors');
        $this->load->view('donors/manage', $data);
    }

    public function donor($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('blood_mgmt', '', 'create')) {
                    access_denied('blood_mgmt');
                }
                $id = $this->blood_mgmt_model->add_donor($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('blood_donor')));
                    echo json_encode([
                        'success' => true,
                        'message' => _l('added_successfully', _l('blood_donor'))
                    ]);
                }
            } else {
                if (!has_permission('blood_mgmt', '', 'edit')) {
                    access_denied('blood_mgmt');
                }
                $success = $this->blood_mgmt_model->update_donor($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('blood_donor')));
                    echo json_encode([
                        'success' => true,
                        'message' => _l('updated_successfully', _l('blood_donor'))
                    ]);
                }
            }
            die;
        }
    }

    public function delete_donor($id)
    {
        if (!has_permission('blood_mgmt', '', 'delete')) {
            access_denied('blood_mgmt');
        }
        if (!$id) {
            redirect(admin_url('blood_mgmt/donors'));
        }
        $response = $this->blood_mgmt_model->delete_donor($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('blood_donor')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('blood_donor')));
        }
        redirect(admin_url('blood_mgmt/donors'));
    }

    public function inventory()
    {
        if (!has_permission('blood_mgmt', '', 'view')) {
            access_denied('blood_mgmt');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('blood_mgmt', 'tables/blood_bags'));
        }
        $data['donors'] = $this->blood_mgmt_model->get_donors();
        $data['title'] = _l('blood_inventory');
        $this->load->view('inventory/manage', $data);
    }

    public function blood_bag($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('blood_mgmt', '', 'create')) {
                    access_denied('blood_mgmt');
                }
                $id = $this->blood_mgmt_model->add_blood_bag($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('blood_bag')));
                    echo json_encode([
                        'success' => true,
                        'message' => _l('added_successfully', _l('blood_bag'))
                    ]);
                }
            } else {
                if (!has_permission('blood_mgmt', '', 'edit')) {
                    access_denied('blood_mgmt');
                }
                $success = $this->blood_mgmt_model->update_blood_bag($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('blood_bag')));
                    echo json_encode([
                        'success' => true,
                        'message' => _l('updated_successfully', _l('blood_bag'))
                    ]);
                }
            }
            die;
        }
    }

    public function delete_blood_bag($id)
    {
        if (!has_permission('blood_mgmt', '', 'delete')) {
            access_denied('blood_mgmt');
        }
        if (!$id) {
            redirect(admin_url('blood_mgmt/inventory'));
        }
        $response = $this->blood_mgmt_model->delete_blood_bag($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('blood_bag')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('blood_bag')));
        }
        redirect(admin_url('blood_mgmt/inventory'));
    }

    public function issues()
    {
        if (!has_permission('blood_mgmt', '', 'view')) {
            access_denied('blood_mgmt');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('blood_mgmt', 'tables/issues'));
        }

        // For modal or form:
        $data['available_bags'] = $this->blood_mgmt_model->get_available_blood_bags();
        $data['title'] = _l('blood_issues');
        $this->load->view('issues/manage', $data);
    }

    public function issue($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            // Issues are generally created, editing complex due to bag status. For now allow create.
            // If edit is needed, logic should handle bag restoration.
            if ($id == '') {
                if (!has_permission('blood_mgmt', '', 'create')) {
                    access_denied('blood_mgmt');
                }
                $id = $this->blood_mgmt_model->add_issue($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('blood_issue')));
                    echo json_encode([
                        'success' => true,
                        'message' => _l('added_successfully', _l('blood_issue'))
                    ]);
                }
            }
            die;
        }
    }

    public function delete_issue($id)
    {
        if (!has_permission('blood_mgmt', '', 'delete')) {
            access_denied('blood_mgmt');
        }
        if (!$id) {
            redirect(admin_url('blood_mgmt/issues'));
        }
        $response = $this->blood_mgmt_model->delete_issue($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('blood_issue')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('blood_issue')));
        }
        redirect(admin_url('blood_mgmt/issues'));
    }

    public function get_donor_data($id)
    {
        if (!has_permission('blood_mgmt', '', 'view')) {
            access_denied('blood_mgmt');
        }
        $donor = $this->blood_mgmt_model->get_donors($id);
        echo json_encode($donor);
    }

    public function get_bag_data($id)
    {
        if (!has_permission('blood_mgmt', '', 'view')) {
            access_denied('blood_mgmt');
        }
        $bag = $this->blood_mgmt_model->get_blood_bags($id);
        echo json_encode($bag);
    }
}
