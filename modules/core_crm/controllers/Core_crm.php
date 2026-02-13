<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Core_crm extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            access_denied('Core CRM Settings');
        }
    }

    public function index()
    {
        $data['title'] = 'Core CRM Settings';

        if ($this->input->post()) {
            $menus = [
                'estimate_request',
                'contracts',
                'projects',
                'sales',
                'knowledge-base',
                'utilities',
                'setup',
                'subscriptions'
            ];

            foreach ($menus as $menu) {
                $val = $this->input->post('hide_' . $menu) ? '1' : '0';
                update_option('core_crm_hide_' . $menu, $val);
            }

            set_alert('success', _l('updated_successfully', 'Settings'));
            redirect(admin_url('core_crm'));
        }

        $this->load->view('core_crm/settings', $data);
    }
}
