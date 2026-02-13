<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tests_master extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tests_master_model');
    }

    /* List all tests */
    public function index()
    {
        if (!has_permission('items', '', 'view')) {
            access_denied('Tests Master');
        }

        $data['title'] = 'Tests Master';
        $this->load->model('invoice_items_model');

        // Ensure "Tests" group exists
        $groups = $this->invoice_items_model->get_groups();
        $tests_group_id = '';
        $found = false;
        foreach ($groups as $group) {
            if (strtolower($group['name']) == 'tests') {
                $tests_group_id = $group['id'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->load->db->insert(db_prefix() . 'items_groups', ['name' => 'Tests']);
            $tests_group_id = $this->load->db->insert_id();
            // Refresh groups
            $groups = $this->invoice_items_model->get_groups();
        }

        $data['groups'] = $groups;
        $data['tests_group_id'] = $tests_group_id;

        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();

        $this->load->model('master_data/master_data_model');
        $data['tests_methods'] = $this->master_data_model->get_tests_methods();

        $this->load->view('tests_master/manage', $data);
    }

    /* Table data */
    public function table()
    {
        if (!has_permission('items', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('tests_master', 'table'));
    }

    /* Add or update test */
    public function test()
    {
        if (!has_permission('items', '', 'view')) {
            access_denied('Tests Master');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if (!isset($data['is_blood_sample_required'])) {
                $data['is_blood_sample_required'] = 0;
            }

            // Map 'department' dropdown to 'group_id'
            if (isset($data['department'])) {
                $data['group_id'] = $data['department'];
                unset($data['department']);
            } else {
                // If department is disabled in form, it won't be posted.
                // Force it to 'Tests' group
                $this->db->where('name', 'Tests');
                $group = $this->db->get(db_prefix() . 'items_groups')->row();
                if ($group) {
                    $data['group_id'] = $group->id;
                }
            }

            if (!isset($data['id'])) {
                if (!has_permission('items', '', 'create')) {
                    access_denied('Tests Master');
                }
                $id = $this->tests_master_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', 'Test'));
                    echo json_encode(['success' => true, 'message' => _l('added_successfully', 'Test')]);
                }
            } else {
                if (!has_permission('items', '', 'edit')) {
                    access_denied('Tests Master');
                }
                $id = $data['id'];
                $data['itemid'] = $id; // Model expects itemid for edit
                unset($data['id']);

                $success = $this->tests_master_model->edit($data);
                if ($success) {
                    set_alert('success', _l('updated_successfully', 'Test'));
                    echo json_encode(['success' => true, 'message' => _l('updated_successfully', 'Test')]);
                }
            }
            die;
        }
    }

    /* Delete test */
    public function delete($id)
    {
        if (!has_permission('items', '', 'delete')) {
            access_denied('Tests Master');
        }

        if (!$id) {
            redirect(admin_url('tests_master'));
        }

        $response = $this->tests_master_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted_successfully', 'Test'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Test'));
        }
        redirect(admin_url('tests_master'));
    }
    /* Templates page */
    public function templates($id)
    {
        if (!has_permission('items', '', 'view')) {
            access_denied('Tests Master');
        }

        if (!$id) {
            redirect(admin_url('tests_master'));
        }

        $data['title'] = 'Test Templates';
        $test = $this->tests_master_model->get($id);

        if (!$test) {
            access_denied('Tests Master');
        }

        // Word Templates
        $data['templates'] = $this->tests_master_model->get_templates($id);

        // Fixed Templates
        $data['fixed_templates'] = $this->tests_master_model->get_fixed_templates($id);

        $fixed_id = $this->input->get('fixed_id');
        $data['current_fixed_template'] = null;
        $data['parameters'] = [];

        if ($fixed_id) {
            $data['current_fixed_template'] = $this->tests_master_model->get_fixed_template($fixed_id);
            if ($data['current_fixed_template']) {
                $data['parameters'] = $this->tests_master_model->get_parameters($fixed_id);
            }
        } else if (!empty($data['fixed_templates'])) {
            // Select default or first if exists
            foreach ($data['fixed_templates'] as $ft) {
                if ($ft['is_default'] == 1) {
                    $data['current_fixed_template'] = (object) $ft;
                    break;
                }
            }
            if (!$data['current_fixed_template']) {
                $data['current_fixed_template'] = (object) $data['fixed_templates'][0];
            }
            $data['parameters'] = $this->tests_master_model->get_parameters($data['current_fixed_template']->id);
        }

        $data['test'] = $test;
        $this->load->view('tests_master/templates', $data);
    }

    /* Save template */
    public function save_template()
    {
        if (!has_permission('items', '', 'create') && !has_permission('items', '', 'edit')) {
            access_denied('Tests Master');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$data['id']) {
                unset($data['id']);
                $id = $this->tests_master_model->add_template($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', 'Template'));
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->tests_master_model->edit_template($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', 'Template'));
                }
            }
            redirect(admin_url('tests_master/templates/' . $data['test_id']));
        }
    }

    /* Delete template */
    public function delete_template($id, $test_id)
    {
        if (!has_permission('items', '', 'delete')) {
            access_denied('Tests Master');
        }

        if (!$id) {
            redirect(admin_url('tests_master/templates/' . $test_id));
        }

        $response = $this->tests_master_model->delete_template($id);
        if ($response == true) {
            set_alert('success', _l('deleted_successfully', 'Template'));
        }
        redirect(admin_url('tests_master/templates/' . $test_id));
    }

    /* Set default template */
    public function set_default($id, $test_id)
    {
        if (!has_permission('items', '', 'edit')) {
            access_denied('Tests Master');
        }

        if (!$id) {
            redirect(admin_url('tests_master/templates/' . $test_id));
        }

        $this->tests_master_model->set_default_template($id, $test_id);
        set_alert('success', _l('updated_successfully', 'Default Template'));
        redirect(admin_url('tests_master/templates/' . $test_id));
    }

    /* Fixed Template Actions */
    public function save_parameter()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);

            if (!$id) {
                if (!has_permission('items', '', 'create')) {
                    header('HTTP/1.0 403 Forbidden');
                    die;
                }
                $insert_id = $this->tests_master_model->add_parameter($data);
                if ($insert_id) {
                    echo json_encode(['success' => true, 'message' => _l('added_successfully', 'Parameter')]);
                }
            } else {
                if (!has_permission('items', '', 'edit')) {
                    header('HTTP/1.0 403 Forbidden');
                    die;
                }
                $success = $this->tests_master_model->edit_parameter($data, $id);
                if ($success) {
                    echo json_encode(['success' => true, 'message' => _l('updated_successfully', 'Parameter')]);
                }
            }
        }
    }

    public function delete_parameter($id)
    {
        if (!has_permission('items', '', 'delete')) {
            header('HTTP/1.0 403 Forbidden');
            die;
        }
        $success = $this->tests_master_model->delete_parameter($id);
        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('deleted_successfully', 'Parameter')]);
        }
    }

    public function save_remarks()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->tests_master_model->update_remarks($data['test_id'], $data['fixed_template_remarks']);
            if ($success) {
                echo json_encode(['success' => true, 'message' => _l('updated_successfully', 'Remarks')]);
            }
        }
    }

    public function update_parameter_order()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $this->tests_master_model->update_parameter_order($data['order']);
        }
    }

    /* Multiple Fixed Template Actions */
    public function save_fixed_template()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);

            if (!$id) {
                $id = $this->tests_master_model->add_fixed_template($data);
                set_alert('success', _l('added_successfully', 'Fixed Template'));
            } else {
                $this->tests_master_model->edit_fixed_template($this->input->post());
                set_alert('success', _l('updated_successfully', 'Fixed Template'));
            }
            redirect(admin_url('tests_master/templates/' . $data['test_id'] . '?fixed_id=' . $id . '&tab=fixed_template'));
        }
    }

    public function delete_fixed_template($id, $test_id)
    {
        $this->tests_master_model->delete_fixed_template($id);
        set_alert('success', _l('deleted_successfully', 'Fixed Template'));
        redirect(admin_url('tests_master/templates/' . $test_id . '?tab=fixed_template'));
    }

    public function set_default_fixed($id, $test_id)
    {
        $this->tests_master_model->set_default_fixed_template($id, $test_id);
        set_alert('success', _l('updated_successfully', 'Default Fixed Template'));
        redirect(admin_url('tests_master/templates/' . $test_id . '?fixed_id=' . $id . '&tab=fixed_template'));
    }

    public function set_active_type($test_id, $type)
    {
        $this->tests_master_model->update_active_template_type($test_id, $type);
        echo json_encode(['success' => true, 'message' => _l('updated_successfully', 'Active Template Mode')]);
    }

    /* Check if code exists */
    public function check_code()
    {
        if ($this->input->post()) {
            $code = $this->input->post('code');
            $id = $this->input->post('id');
            $exists = $this->tests_master_model->check_code_exists($code, $id);
            if ($exists) {
                echo json_encode(['exists' => true, 'message' => _l('code_already_exists')]);
            } else {
                echo json_encode(['exists' => false, 'message' => _l('code_available')]);
            }
        }
    }
    public function change_status_active($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->tests_master_model->change_status_active($id, $status);
        }
    }
}
