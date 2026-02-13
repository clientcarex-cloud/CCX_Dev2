<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Master_data extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('master_data_model');
    }

    /* Name Titles CRUD */
    public function name_titles()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/name_titles'));
        }
        $data['title'] = 'Name Titles';
        $this->load->view('name_titles', $data);
    }

    public function save_name_title()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_name_title($data);
                if ($id) {
                    $message = _l('added_successfully', 'Name Title');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_name_title($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Name Title');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_name_title($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/name_titles'));
        }
        $response = $this->master_data_model->delete_name_title($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Name Title'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Name Title'));
        }
        redirect(admin_url('master_data/name_titles'));
    }

    /* Name Care Titles CRUD */
    public function name_care_titles()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/name_care_titles'));
        }
        $data['title'] = 'Name Care Titles';
        $this->load->view('name_care_titles', $data);
    }

    public function save_name_care_title()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_name_care_title($data);
                if ($id) {
                    $message = _l('added_successfully', 'Name Care Title');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_name_care_title($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Name Care Title');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_name_care_title($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/name_care_titles'));
        }
        $response = $this->master_data_model->delete_name_care_title($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Name Care Title'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Name Care Title'));
        }
        redirect(admin_url('master_data/name_care_titles'));
    }

    /* Lab Tests Statuses CRUD */
    public function lab_tests_statuses()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/lab_tests_statuses'));
        }
        $data['title'] = _l('lab_tests_statuses');
        $this->load->view('lab_tests_statuses', $data);
    }

    public function save_lab_tests_status()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_lab_tests_status($data);
                if ($id) {
                    $message = _l('added_successfully', _l('lab_tests_status'));
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_lab_tests_status($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', _l('lab_tests_status'));
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_lab_tests_status($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/lab_tests_statuses'));
        }
        $response = $this->master_data_model->delete_lab_tests_status($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('lab_tests_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lab_tests_status')));
        }
        redirect(admin_url('master_data/lab_tests_statuses'));
    }

    /* Item Statuses (Group Specific) */
    public function item_statuses($group_id)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/item_statuses'), ['group_id' => $group_id]);
        }

        // Global 'Items_groups' fetch (using Query or Invoice Items Model if loaded)
        // Let's us direct query for simplicity as we don't want to load big models unless needed
        $this->db->where('id', $group_id);
        $group = $this->db->get(db_prefix() . 'items_groups')->row();

        if (!$group) {
            show_404();
        }

        $data['group'] = $group;
        $data['title'] = $group->name . ' Statuses';
        $data['group_id'] = $group_id;

        $this->load->view('item_statuses', $data);
    }

    public function save_item_status()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_item_status($data);
                if ($id) {
                    $message = _l('added_successfully', 'Item Status');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_item_status($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Item Status');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_item_status($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }

        // Get group id before delete to redirect back correctly
        $this->db->where('id', $id);
        $status = $this->db->get(db_prefix() . 'item_statuses')->row();
        $group_id = $status ? $status->group_id : 0;

        if (!$id) {
            redirect(admin_url('master_data'));
        }
        $response = $this->master_data_model->delete_item_status($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Item Status'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Item Status'));
        }

        if ($group_id) {
            redirect(admin_url('master_data/item_statuses/' . $group_id));
        } else {
            redirect(admin_url('master_data'));
        }
    }

    /* Medicine Types CRUD */
    public function medicine_types()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/medicine_types'));
        }
        $data['title'] = 'Medicine Types';
        $this->load->view('medicine_types', $data);
    }

    public function save_medicine_type()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_medicine_type($data);
                if ($id) {
                    $message = _l('added_successfully', 'Medicine Type');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_medicine_type($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Medicine Type');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_medicine_type($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/medicine_types'));
        }
        $response = $this->master_data_model->delete_medicine_type($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Medicine Type'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Medicine Type'));
        }
        redirect(admin_url('master_data/medicine_types'));
    }

    /* Medicine Doses CRUD */
    public function medicine_doses()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/medicine_doses'));
        }
        $data['title'] = 'Medicine Doses';
        $this->load->view('medicine_doses', $data);
    }

    public function save_medicine_dose()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_medicine_dose($data);
                if ($id) {
                    $message = _l('added_successfully', 'Medicine Dose');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_medicine_dose($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Medicine Dose');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_medicine_dose($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/medicine_doses'));
        }
        $response = $this->master_data_model->delete_medicine_dose($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Medicine Dose'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Medicine Dose'));
        }
        redirect(admin_url('master_data/medicine_doses'));
    }

    /* Medicine Frequencies CRUD */
    public function medicine_frequencies()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/medicine_frequencies'));
        }
        $data['title'] = 'Medicine Frequencies';
        $this->load->view('medicine_frequencies', $data);
    }

    public function save_medicine_frequency()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_medicine_frequency($data);
                if ($id) {
                    $message = _l('added_successfully', 'Medicine Frequency');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_medicine_frequency($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Medicine Frequency');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_medicine_frequency($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/medicine_frequencies'));
        }
        $response = $this->master_data_model->delete_medicine_frequency($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Medicine Frequency'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Medicine Frequency'));
        }
        redirect(admin_url('master_data/medicine_frequencies'));
    }

    /* Medicine Durations CRUD */
    public function medicine_durations()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/medicine_durations'));
        }
        $data['title'] = 'Medicine Durations';
        $this->load->view('medicine_durations', $data);
    }

    public function save_medicine_duration()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_medicine_duration($data);
                if ($id) {
                    $message = _l('added_successfully', 'Medicine Duration');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_medicine_duration($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Medicine Duration');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_medicine_duration($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/medicine_durations'));
        }
        $response = $this->master_data_model->delete_medicine_duration($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Medicine Duration'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Medicine Duration'));
        }
        redirect(admin_url('master_data/medicine_durations'));
    }

    /* Medicine When CRUD */
    public function medicine_whens()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/medicine_whens'));
        }
        $data['title'] = 'Medicine When';
        $this->load->view('medicine_whens', $data);
    }

    public function save_medicine_when()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_medicine_when($data);
                if ($id) {
                    $message = _l('added_successfully', 'Medicine When');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_medicine_when($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Medicine When');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_medicine_when($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/medicine_whens'));
        }
        $response = $this->master_data_model->delete_medicine_when($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Medicine When'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Medicine When'));
        }
        redirect(admin_url('master_data/medicine_whens'));
    }

    /* Treatments CRUD */
    public function treatments()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/treatments'));
        }
        $data['title'] = 'Treatments';
        $this->load->view('treatments', $data);
    }

    public function save_treatment()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_treatment($data);
                if ($id) {
                    $message = _l('added_successfully', 'Treatment');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_treatment($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Treatment');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_treatment($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/treatments'));
        }
        $response = $this->master_data_model->delete_treatment($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Treatment'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Treatment'));
        }
        redirect(admin_url('master_data/treatments'));
    }

    /* Department Groups CRUD */
    public function department_groups()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/department_groups'));
        }
        $data['title'] = 'Department Groups';
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        $this->load->view('department_groups', $data);
    }

    public function save_department_group()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_department_group($data);
                if ($id) {
                    $message = _l('added_successfully', 'Department Group');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_department_group($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', 'Department Group');
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_department_group($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/department_groups'));
        }
        $response = $this->master_data_model->delete_department_group($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Department Group'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Department Group'));
        }
        redirect(admin_url('master_data/department_groups'));
    }

    /* Tests Methods CRUD */
    public function tests_methods()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('master_data', 'tables/tests_methods'));
        }
        $data['title'] = _l('tests_methods');
        $this->load->view('tests_methods', $data);
    }

    public function save_tests_method()
    {
        if (!has_permission('master_data', '', 'create') && !has_permission('master_data', '', 'edit')) {
            access_denied('Master Data');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('master_data', '', 'create')) {
                    access_denied('Master Data');
                }
                $id = $this->master_data_model->add_tests_method($data);
                if ($id) {
                    $message = _l('added_successfully', _l('tests_method'));
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            } else {
                if (!has_permission('master_data', '', 'edit')) {
                    access_denied('Master Data');
                }
                $success = $this->master_data_model->update_tests_method($data, $data['id']);
                if ($success) {
                    $message = _l('updated_successfully', _l('tests_method'));
                    echo json_encode(['success' => true, 'message' => $message]);
                }
            }
        }
    }

    public function delete_tests_method($id)
    {
        if (!has_permission('master_data', '', 'delete')) {
            access_denied('Master Data');
        }
        if (!$id) {
            redirect(admin_url('master_data/tests_methods'));
        }
        $response = $this->master_data_model->delete_tests_method($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('tests_method')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('tests_method')));
        }
        redirect(admin_url('master_data/tests_methods'));
    }
}
