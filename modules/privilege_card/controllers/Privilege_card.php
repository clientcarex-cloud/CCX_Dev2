<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Privilege_card extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('privilege_card_model');
    }

    /* List all privilege card members */
    public function index()
    {
        if (!has_permission('privilege_card', '', 'view')) {
            access_denied('privilege_card');
        }

        // Redirect to members since that is our main view
        redirect(admin_url('privilege_card/members'));
    }

    /* List all privilege card members */
    public function members()
    {
        if (!has_permission('privilege_card', '', 'view')) {
            access_denied('privilege_card');
        }

        $data['members'] = $this->privilege_card_model->get_members();
        $data['types'] = $this->privilege_card_model->get_types();
        $data['title'] = _l('Privilege Card Members');
        $this->load->view('members', $data);
    }

    /* Add or edit member */
    public function member($id = '')
    {
        if (!has_permission('privilege_card', '', 'view')) {
            access_denied('privilege_card');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('privilege_card', '', 'create')) {
                    access_denied('privilege_card');
                }
                $id = $this->privilege_card_model->add_member($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('privilege_card_member')));
                }
            } else {
                if (!has_permission('privilege_card', '', 'edit')) {
                    access_denied('privilege_card');
                }
                $success = $this->privilege_card_model->update_member($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('privilege_card_member')));
                }
            }
            redirect(admin_url('privilege_card/members'));
        }
    }

    /* Delete member */
    public function delete_member($id)
    {
        if (!has_permission('privilege_card', '', 'delete')) {
            access_denied('privilege_card');
        }
        if (!$id) {
            redirect(admin_url('privilege_card/members'));
        }
        $response = $this->privilege_card_model->delete_member($id);
        if ($response == true) {
            set_alert('success', _l('deleted_successfully', _l('privilege_card_member')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('privilege_card_member')));
        }
        redirect(admin_url('privilege_card/members'));
    }

    /* List all privilege card types (plans) */
    public function types()
    {
        if (!has_permission('privilege_card', '', 'view')) {
            access_denied('privilege_card');
        }

        $data['types'] = $this->privilege_card_model->get_types();
        $data['title'] = _l('Privilege Card Plans');
        $this->load->view('types', $data);
    }

    /* Add or edit type */
    public function type($id = '')
    {
        if (!has_permission('privilege_card', '', 'view')) {
            access_denied('privilege_card');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('privilege_card', '', 'create')) {
                    access_denied('privilege_card');
                }
                $id = $this->privilege_card_model->add_type($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('privilege_card_type')));
                }
            } else {
                if (!has_permission('privilege_card', '', 'edit')) {
                    access_denied('privilege_card');
                }
                $success = $this->privilege_card_model->update_type($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('privilege_card_type')));
                }
            }
            redirect(admin_url('privilege_card/types'));
        }
    }

    /* Delete type */
    public function delete_type($id)
    {
        if (!has_permission('privilege_card', '', 'delete')) {
            access_denied('privilege_card');
        }
        if (!$id) {
            redirect(admin_url('privilege_card/types'));
        }
        $response = $this->privilege_card_model->delete_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('privilege_card_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted_successfully', _l('privilege_card_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('privilege_card_type')));
        }
        redirect(admin_url('privilege_card/types'));
    }
}
