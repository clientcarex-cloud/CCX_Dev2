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

        $this->load->model('leads_model');
        $this->load->model('staff_model');
        $this->load->model('tickets_model');

        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['priorities'] = $this->tickets_model->get_priority();

        $data['title'] = _l('ccx_leads');
        $this->load->view('manage', $data);
    }

    public function lead($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('ccx_leads', '', 'create')) {
                    header('HTTP/1.0 401 Unauthorized');
                    echo json_encode(['success' => false, 'message' => _l('access_denied')]);
                    die;
                }
            } else {
                if (!has_permission('ccx_leads', '', 'edit')) {
                    header('HTTP/1.0 401 Unauthorized');
                    echo json_encode(['success' => false, 'message' => _l('access_denied')]);
                    die;
                }
            }

            $data = $this->input->post();

            // Auto-set company to name if not provided (or force it as per requirement)
            if (isset($data['name'])) {
                $data['company'] = $data['name'];
            }

            $custom_fields = [];
            if (isset($data['custom_fields'])) {
                $custom_fields = $data['custom_fields'];
                unset($data['custom_fields']);
            }

            if ($id == '') {
                $id = $this->ccx_leads_model->add_lead($data);
                if ($id) {
                    // Save Custom Fields
                    if (!empty($custom_fields)) {
                        foreach ($custom_fields as $field_id => $value) {
                            $this->db->insert(db_prefix() . 'ccx_leads_custom_values', [
                                'lead_id' => $id,
                                'field_id' => $field_id,
                                'value' => $value
                            ]);
                        }
                    }

                    $message = _l('added_successfully', _l('ccx_lead'));
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => true, 'message' => $message]);
                        die;
                    }
                    set_alert('success', $message);
                    redirect(admin_url('ccx_leads'));
                }
            } else {
                $success = $this->ccx_leads_model->update_lead($data, $id);

                // Save/Update Custom Fields
                if (!empty($custom_fields)) {
                    foreach ($custom_fields as $field_id => $value) {
                        $exists = $this->db->where('lead_id', $id)->where('field_id', $field_id)->get(db_prefix() . 'ccx_leads_custom_values')->row();
                        if ($exists) {
                            $this->db->where('id', $exists->id);
                            $this->db->update(db_prefix() . 'ccx_leads_custom_values', ['value' => $value]);
                        } else {
                            $this->db->insert(db_prefix() . 'ccx_leads_custom_values', [
                                'lead_id' => $id,
                                'field_id' => $field_id,
                                'value' => $value
                            ]);
                        }
                    }
                }

                $message = _l('updated_successfully', _l('ccx_lead'));
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => true, 'message' => $message]);
                    die;
                }
                if ($success) {
                    set_alert('success', $message);
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
                echo json_encode(['success' => true, 'message' => _l('added_successfully', _l('ccx_leads_call_log'))]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('problem_adding', _l('ccx_leads_call_log'))]);
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

    /* Check if phone number exists */
    public function check_duplicate_phone()
    {
        if ($this->input->is_ajax_request()) {
            $phone = $this->input->post('phone');
            $id = $this->input->post('id'); // ID to exclude (for edit mode)

            $this->db->where('phonenumber', $phone);
            if ($id) {
                $this->db->where('id !=', $id);
            }
            $exists = $this->db->count_all_results('tblccx_leads') > 0;

            echo json_encode(['exists' => $exists, 'message' => _l('ccx_leads_phone_exists')]);
        }
    }

    public function settings()
    {
        if (!is_admin()) {
            access_denied('CCX Leads Settings');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if (isset($data['ccx_leads_fields'])) {
                update_option('ccx_leads_field_settings', json_encode($data['ccx_leads_fields']));
                set_alert('success', _l('updated_successfully', _l('ccx_leads_settings')));
            }
            redirect(admin_url('ccx_leads/settings'));
        }

        $settings = get_option('ccx_leads_field_settings');
        if (empty($settings)) {
            $default_fields = [
                ['name' => 'Name', 'slug' => 'name', 'mandatory' => 1, 'status' => 1],
                ['name' => 'Phone', 'slug' => 'phonenumber', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Email', 'slug' => 'email', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Position', 'slug' => 'title', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Website', 'slug' => 'website', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Description', 'slug' => 'description', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Address', 'slug' => 'address', 'mandatory' => 0, 'status' => 1],
                ['name' => 'City', 'slug' => 'city', 'mandatory' => 0, 'status' => 1],
                ['name' => 'State', 'slug' => 'state', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Country', 'slug' => 'country', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Zip Code', 'slug' => 'zip', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Lead Value', 'slug' => 'lead_value', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Priority', 'slug' => 'priority', 'mandatory' => 0, 'status' => 1],
                ['name' => 'Status', 'slug' => 'status', 'mandatory' => 1, 'status' => 1],
                ['name' => 'Assigned', 'slug' => 'assigned', 'mandatory' => 0, 'status' => 1],
            ];
            $data['ccx_leads_fields'] = $default_fields;
        } else {
            $data['ccx_leads_fields'] = json_decode($settings, true);
        }

        // Fetch Custom Fields
        if ($this->db->table_exists(db_prefix() . 'ccx_leads_custom_fields')) {
            $data['custom_fields'] = $this->db->get(db_prefix() . 'ccx_leads_custom_fields')->result_array();
        }

        $data['title'] = _l('ccx_leads_settings');
        $this->load->view('settings', $data);
    }

    public function save_custom_field()
    {
        if (!is_admin()) {
            access_denied('CCX Leads Settings');
        }
        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$data['name'] || !$data['type']) {
                set_alert('danger', _l('problem_adding', 'Custom Field'));
                redirect(admin_url('ccx_leads/settings'));
            }

            $id = $data['id'];
            $insert_data = [
                'name' => $data['name'],
                'type' => $data['type'],
                'options' => trim($data['options']),
                'mandatory' => isset($data['mandatory']) ? 1 : 0,
                // 'status' => isset($data['status']) ? 1 : 0, // form doesn't send status, default is 1
            ];

            if ($id == '') {
                $slug = slug_it($data['name']);

                // Ensure unique slug
                $original_slug = $slug;
                $count = 1;
                while ($this->db->where('slug', $slug)->count_all_results(db_prefix() . 'ccx_leads_custom_fields') > 0) {
                    $slug = $original_slug . '_' . $count;
                    $count++;
                }

                $insert_data['slug'] = $slug;
                $insert_data['status'] = 1;
                $this->db->insert(db_prefix() . 'ccx_leads_custom_fields', $insert_data);
                set_alert('success', _l('added_successfully', 'Custom Field'));
            } else {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'ccx_leads_custom_fields', $insert_data);
                set_alert('success', _l('updated_successfully', 'Custom Field'));
            }
        }
        redirect(admin_url('ccx_leads/settings'));
    }

    public function get_custom_field($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }
        $field = $this->db->where('id', $id)->get(db_prefix() . 'ccx_leads_custom_fields')->row();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($field));
    }

    public function delete_custom_field($id)
    {
        if (!is_admin()) {
            access_denied('CCX Leads Settings');
        }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ccx_leads_custom_fields');
        // Also delete values
        $this->db->where('field_id', $id);
        $this->db->delete(db_prefix() . 'ccx_leads_custom_values');

        set_alert('success', _l('deleted', 'Custom Field'));
        redirect(admin_url('ccx_leads/settings'));
    }

    public function change_custom_field_status($id, $status)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ccx_leads_custom_fields', ['status' => $status]);
    }
}
