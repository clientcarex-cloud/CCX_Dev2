<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tats extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Optional: Check permission
        // if (!has_permission('tats', '', 'view')) {
        //     access_denied('tats');
        // }

        $data['title'] = 'TATs Item Group Wise Report';
        $this->load->model('tats_model');
        $data['groups'] = $this->tats_model->get_tat_report();
        $this->load->view('manage', $data);
    }
}
