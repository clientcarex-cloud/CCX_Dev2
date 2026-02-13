<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('ccx_dashboard_model');
  }

  public function index()
  {
    // Settings view is no longer used; redirect to dashboards.
    redirect(admin_url('ccx_dashboard/dashboards/my_dashboard'));
  }
}
