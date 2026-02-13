<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Insurance extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Optional: Check permission
        // if (!has_permission('insurance', '', 'view')) {
        //     access_denied('insurance');
        // }

        $data['title'] = 'Insurance';
        $this->load->view('manage', $data);
    }
}
