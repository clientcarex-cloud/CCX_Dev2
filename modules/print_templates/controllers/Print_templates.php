<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Print_templates extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('print_templates_model');
    }

    /* List all print templates */
    public function index()
    {
        if (!has_permission('print_templates', '', 'view')) {
            access_denied(_l('print_templates'));
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('print_templates', 'table'));
        }

        $data['title'] = _l('print_templates');
        $this->load->view('manage', $data);
    }

    /* Add or edit print template */
    public function template($id = '')
    {
        if (!has_permission('print_templates', '', 'view')) {
            access_denied(_l('print_templates'));
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('print_templates', '', 'create')) {
                    access_denied(_l('print_templates'));
                }
                $id = $this->print_templates_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('print_template_lowercase')));
                    redirect(admin_url('print_templates'));
                }
            } else {
                if (!has_permission('print_templates', '', 'edit')) {
                    access_denied(_l('print_templates'));
                }
                $success = $this->print_templates_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('print_template_lowercase')));
                }
                redirect(admin_url('print_templates'));
            }
        }

        if ($id == '') {
            $title = _l('new_print_template');
        } else {
            $data['template'] = $this->print_templates_model->get($id);
            $title = _l('edit_print_template');
        }

        $data['types'] = $this->print_templates_model->get_types();
        $data['print_templates'] = $this->print_templates_model->get_all_simple();

        $data['title'] = $title;
        $this->load->view('template', $data);
    }

    /* Delete print template */
    public function delete($id)
    {
        if (!has_permission('print_templates', '', 'delete')) {
            access_denied(_l('print_templates'));
        }

        if (!$id) {
            redirect(admin_url('print_templates'));
        }

        $response = $this->print_templates_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('print_template_lowercase')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('print_template_lowercase')));
        }
        redirect(admin_url('print_templates'));
    }
}
