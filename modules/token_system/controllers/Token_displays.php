<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Token_displays extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('token_system_model');

        // Auto-migration for new YouTube fields
        if (!$this->db->field_exists('yt_mute', db_prefix() . 'token_displays')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "token_displays` ADD `yt_mute` INT DEFAULT 1;");
        }
        if (!$this->db->field_exists('yt_loop', db_prefix() . 'token_displays')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "token_displays` ADD `yt_loop` INT DEFAULT 1;");
        }
        if (!$this->db->field_exists('passcode', db_prefix() . 'token_displays')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "token_displays` ADD `passcode` VARCHAR(50) DEFAULT NULL;");
        }
    }

    public function index($id = '')
    {
        if (!has_permission('token_system', '', 'view')) {
            access_denied(_l('token_system'));
        }

        if ($this->input->post()) {
            if (!has_permission('token_system', '', 'create') && !has_permission('token_system', '', 'edit')) {
                access_denied(_l('token_system'));
            }
            $data = $this->input->post();

            if ($id == '') {
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert(db_prefix() . 'token_displays', $data);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    set_alert('success', _l('added_successfully', _l('display_name')));
                }
            } else {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'token_displays', $data);
                set_alert('success', _l('updated_successfully', _l('display_name')));
            }

            redirect(admin_url('token_system/token_displays'));
        }

        if ($id != '') {
            $data['display'] = $this->db->where('id', $id)->get(db_prefix() . 'token_displays')->row_array();
            $data['title'] = _l('edit', _l('display_name'));
        } else {
            $data['title'] = _l('display_management');
        }

        // Fetch valid doctors for dropdown
        $this->db->select('staffid, firstname, lastname');
        $this->db->from(db_prefix() . 'staff');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role', 'left');
        $this->db->where_in('name', ['Doctor', 'Jr. Doctor', 'Sr. Doctor']);
        $this->db->where(db_prefix() . 'staff.active', 1);
        $data['doctors'] = $this->db->get()->result_array();

        $data['displays'] = $this->db->get(db_prefix() . 'token_displays')->result_array();

        // Helper to get doctor name in view
        $data['staff_map'] = [];
        foreach ($data['doctors'] as $d) {
            $data['staff_map'][$d['staffid']] = $d['firstname'] . ' ' . $d['lastname'];
        }

        $this->load->view('displays/manage', $data);
    }

    public function delete($id)
    {
        if (!has_permission('token_system', '', 'delete')) {
            access_denied(_l('token_system'));
        }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'token_displays');
        set_alert('success', _l('deleted', _l('display_name')));
        redirect(admin_url('token_system/token_displays'));
    }
}
