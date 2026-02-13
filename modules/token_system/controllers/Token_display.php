<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Token_display extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('token_system_model');
    }

    public function index()
    {
        // Default View (No Ads or default)
        $data['title'] = _l('token_system');
        $this->load->view('token_system/display', $data);
    }

    public function view($id)
    {
        $display = $this->db->where('id', $id)->get(db_prefix() . 'token_displays')->row_array();

        if (!$display) {
            show_404();
        }

        // Gatekeeper Logic
        if (!empty($display['passcode'])) {
            $access_key = 'token_display_access_' . $id;
            if (!$this->session->has_userdata($access_key) || $this->session->userdata($access_key) !== true) {
                // Show Login Screen
                $this->load->view('token_system/login', ['display_id' => $id]);
                return;
            }
        }

        $data['title'] = $display['name'];
        $data['display'] = $display;

        $layout = isset($display['layout']) && !empty($display['layout']) ? $display['layout'] : 'modern';
        $view_file = 'token_system/layouts/layout_' . $layout;

        // Check if view exists, else fallback
        $layout_path = FCPATH . 'modules/token_system/views/layouts/layout_' . $layout . '.php';
        if (!file_exists($layout_path)) {
            $view_file = 'token_system/display'; // Fallback to original
        }

        $this->load->view($view_file, $data);
    }

    public function login($id)
    {
        if ($this->input->post()) {
            $passcode = $this->input->post('passcode');
            $display = $this->db->where('id', $id)->get(db_prefix() . 'token_displays')->row();

            if ($display && $display->passcode === $passcode) {
                $this->session->set_userdata('token_display_access_' . $id, true);
                redirect(site_url('token_system/token_display/view/' . $id));
            } else {
                $this->session->set_flashdata('error', 'Invalid Passcode');
                redirect(site_url('token_system/token_display/view/' . $id));
            }
        }
        redirect(site_url('token_system/token_display/view/' . $id));
    }



    public function get_queue_data()
    {
        $display_id = $this->input->get('display_id');
        $doctor_id = 0;

        if ($display_id) {
            $display = $this->db->where('id', $display_id)->get(db_prefix() . 'token_displays')->row();
            if ($display && $display->doctor_id) {
                $doctor_id = $display->doctor_id;
            }
        }

        // JSON endpoint for the display to poll
        $tokens = $this->token_system_model->get_todays_tokens('', $doctor_id);

        // Filter for "Now Serving" (Status 1)
        $serving = array_values(array_filter($tokens, function ($t) {
            return $t['status'] == 1;
        }));

        // Filter for "Waiting" (Status 0)
        $waiting = array_values(array_filter($tokens, function ($t) {
            return $t['status'] == 0;
        }));

        echo json_encode([
            'serving' => $serving,
            'waiting' => array_slice($waiting, 0, 10)
        ]);
        exit;
    }
}
