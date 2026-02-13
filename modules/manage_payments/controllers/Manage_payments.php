<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Manage_payments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('manage_payments_model');
        $this->load->model('staff_model');

        $filters = [
            'date' => $this->input->get('date'),
            'user_id' => $this->input->get('user_id'),
            'status' => $this->input->get('status') ? $this->input->get('status') : 'all'
        ];

        $data['payments'] = $this->manage_payments_model->get_payments($filters);
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['filters'] = $filters;

        $data['title'] = 'Manage Payments';
        $this->load->view('manage_payments/manage', $data);
    }
}
