<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Referral_dispatch extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Optional: Check permission
        // if (!has_permission('referral_dispatch', '', 'view')) {
        //     access_denied('referral_dispatch');
        // }

        $this->load->model('referral_dispatch_model');

        $data['doctor_stats'] = $this->referral_dispatch_model->get_referral_doctor_stats();
        $data['lab_stats'] = $this->referral_dispatch_model->get_referral_lab_stats();

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = 'Referral Dispatch';
        $this->load->view('manage', $data);
    }
}
