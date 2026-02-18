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

            if ($id == '') {
                $id = $this->ccx_leads_model->add_lead($data);
                if ($id) {
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

        $data['title'] = _l('ccx_leads_settings');
        $this->load->view('settings', $data);
    }
}
