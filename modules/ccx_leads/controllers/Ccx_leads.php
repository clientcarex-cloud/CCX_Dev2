<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ccx_leads extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ccx_leads_model');
        $this->load->model('leads_model');
        $this->load->model('staff_model');
        $this->load->model('misc_model');
    }

    /* List all leads */
    public function index($id = '')
    {
        close_setup_menu();

        if (!has_permission('leads', '', 'view')) {
            access_denied('leads');
        }

        // If ID is passed, we might want to open the slide-over directly (logic in JS)
        $data['leadid'] = $id;

        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['members'] = $this->staff_model->get('', ['active' => 1]);
        $data['summary'] = $this->ccx_leads_model->get_status_summary();
        $data['field_settings_map'] = $this->ccx_leads_model->get_field_settings_map();
        $data['title'] = 'CCX Leads';

        // Kanban logic
        $data['kanban_content'] = $this->kanban();

        $this->load->view('ccx_leads/main', $data);
    }

    public function kanban()
    {
        $data['statuses'] = $this->leads_model->get_status();

        return $this->load->view('ccx_leads/kanban', $data, true);
    }

    public function kanban_load_more()
    {
        $status = $this->input->get('status');
        $page = $this->input->get('page');

        $this->load->model('ccx_leads_model');
        $leads = $this->ccx_leads_model->do_kanban_query($status, '', $page);

        foreach ($leads as $lead) {
            $this->load->view('ccx_leads/_kan_ban_card', ['lead' => $lead, 'status' => $status]);
        }
    }

    /* Table view data */
    public function table()
    {
        $this->load->model('ccx_leads_model');
        $this->load->model('leads_model');
        $this->load->model('staff_model');
        $this->load->model('misc_model');

        if (!has_permission('leads', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('ccx_leads', 'table'));
    }

    /* Get lead data for slide-over */
    public function get_lead_data($id)
    {
        $lead = $this->leads_model->get($id);

        if (!$lead) {
            header('HTTP/1.0 404 Not Found');
            echo 'Lead not found';
            die;
        }

        $data['lead'] = $lead;
        $data['check_permission'] = true; // For activity log
        $data['activity_log'] = $this->leads_model->get_lead_activity_log($id);
        $data['notes'] = $this->misc_model->get_notes($id, 'lead');
        $data['attachments'] = $this->leads_model->get_lead_attachments($id);

        $this->load->view('ccx_leads/lead_panel', $data);
    }

    /* Module's own lead modal — independent from core */
    public function lead_modal($id)
    {
        if (!has_permission('leads', '', 'view')) {
            ajax_access_denied();
        }

        $lead = $this->leads_model->get($id);
        if (!$lead) {
            show_404();
        }

        $this->load->model('currencies_model');

        $data['lead'] = $lead;
        $data['activity_log'] = $this->leads_model->get_lead_activity_log($id);
        $data['notes'] = $this->misc_model->get_notes($id, 'lead');
        $data['mail_activity'] = $this->leads_model->get_mail_activity($id);
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['members'] = $this->staff_model->get('', ['active' => 1]);
        $data['total_notes'] = count($data['notes']);
        $data['total_reminders'] = total_rows(db_prefix() . 'reminders', ['rel_id' => $id, 'rel_type' => 'lead']);
        $data['total_attachments'] = count($lead->attachments);
        $data['openEdit'] = false;
        $data['lead_locked'] = false;
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['field_settings'] = $this->ccx_leads_model->get_field_settings_map();

        $this->load->view('ccx_leads/lead_modal', $data);
    }

    /* AJAX: Serve independent New Lead form */
    public function new_lead()
    {
        if (!has_permission('leads', '', 'view')) {
            ajax_access_denied();
        }

        $this->load->model('currencies_model');

        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['members'] = $this->staff_model->get('', ['active' => 1]);
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['field_settings'] = $this->ccx_leads_model->get_field_settings_map();

        $this->load->view('ccx_leads/new_lead_form', $data);
    }

    /* AJAX: Handle new lead form submission */
    public function save_lead()
    {
        if (!has_permission('leads', '', 'view')) {
            ajax_access_denied();
        }

        if (!$this->input->post()) {
            echo json_encode(['success' => false, 'message' => 'No data received']);
            die;
        }

        $id = $this->leads_model->add($this->input->post());

        echo json_encode([
            'success' => $id ? true : false,
            'id' => $id,
            'message' => $id ? _l('added_successfully', _l('lead')) : '',
        ]);
        die;
    }

    /* Settings page */
    public function settings()
    {
        if (!has_permission('leads', '', 'view')) {
            access_denied('leads');
        }

        $data['title'] = _l('ccx_leads_settings');
        $this->load->model('leads_model');
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['field_settings'] = $this->ccx_leads_model->get_field_settings();
        $this->load->view('ccx_leads/settings', $data);
    }

    /* AJAX: Save field settings */
    public function save_field_settings()
    {
        if (!has_permission('leads', '', 'view')) {
            ajax_access_denied();
        }

        $fields = $this->input->post('fields');
        if (!$fields || !is_array($fields)) {
            echo json_encode(['success' => false, 'message' => 'No data received']);
            die;
        }

        foreach ($fields as $field) {
            $id = intval($field['id']);
            if ($id <= 0)
                continue;

            $update = [
                'label' => trim($field['label']),
                'active' => isset($field['active']) ? 1 : 0,
                'required' => isset($field['required']) ? 1 : 0,
            ];

            $this->ccx_leads_model->update_field_setting($id, $update);
        }

        echo json_encode(['success' => true, 'message' => 'Field settings saved successfully']);
        die;
    }
}
