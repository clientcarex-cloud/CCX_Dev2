<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sms_wa_email extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Optional: Check permission
        // if (!has_permission('sms_wa_email', '', 'view')) {
        //     access_denied('sms_wa_email');
        // }

        $data['title'] = 'SMS/WA/Email';
        $this->load->view('manage', $data);
    }
}
