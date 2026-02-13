<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Coupons extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('coupons_model');
    }

    /* List all coupons */
    public function index()
    {
        if (!has_permission('coupons', '', 'view')) {
            access_denied('coupons');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('coupons', 'table'));
        }

        $data['title'] = _l('coupons');
        $this->load->view('manage', $data);
    }

    /* Add or edit coupon */
    public function coupon($id = '')
    {
        if (!has_permission('coupons', '', 'view')) {
            access_denied('coupons');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('coupons', '', 'create')) {
                    access_denied('coupons');
                }

                // Pack price_cap settings
                if ($data['type'] == 3) { // Price Cap
                    $data['type_settings'] = [
                        'min_total' => $data['min_total'],
                        'max_total' => $data['max_total'],
                        'discount_mode' => $data['discount_mode']
                    ];
                    // Remove individual fields so they don't break insert if column doesn't exist
                    unset($data['min_total'], $data['max_total'], $data['discount_mode']);
                }

                $id = $this->coupons_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('coupon')));
                    redirect(admin_url('coupons'));
                }
            } else {
                if (!has_permission('coupons', '', 'edit')) {
                    access_denied('coupons');
                }

                // Pack price_cap settings
                if ($data['type'] == 3) { // Price Cap
                    $data['type_settings'] = [
                        'min_total' => $data['min_total'],
                        'max_total' => $data['max_total'],
                        'discount_mode' => $data['discount_mode']
                    ];
                    // Remove individual fields so they don't break insert if column doesn't exist
                    unset($data['min_total'], $data['max_total'], $data['discount_mode']);
                }

                $success = $this->coupons_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('coupon')));
                }
                redirect(admin_url('coupons'));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('coupon_lowercase'));
        } else {
            $data['coupon'] = $this->coupons_model->get($id);
            $title = _l('edit', _l('coupon_lowercase'));
        }

        $data['title'] = $title;
        $this->load->view('coupon', $data);
    }

    /* Delete coupon */
    public function delete($id)
    {
        if (!has_permission('coupons', '', 'delete')) {
            access_denied('coupons');
        }

        if (!$id) {
            redirect(admin_url('coupons'));
        }

        $response = $this->coupons_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('coupon')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('coupon_lowercase')));
        }
        redirect(admin_url('coupons'));
    }
}
