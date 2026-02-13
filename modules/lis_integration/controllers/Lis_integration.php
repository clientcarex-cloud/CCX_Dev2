<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Lis_integration extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Optional: Check permission
        // if (!has_permission('lis_integration', '', 'view')) {
        //     access_denied('lis_integration');
        // }

        $data['title'] = 'LIS Integration';
        $this->load->view('manage', $data);
    }
}
