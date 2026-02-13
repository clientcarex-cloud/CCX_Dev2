<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Self_kiosk extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('self_kiosk_model');
    }

    public function index()
    {
        // Migration: Ensure settings column exists
        if (!$this->db->field_exists('settings', db_prefix() . 'self_kiosk_qrcodes')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'self_kiosk_qrcodes` ADD `settings` TEXT NULL AFTER `slug`;');
        }

        // Migration: Create feedback table
        if (!$this->db->table_exists(db_prefix() . 'self_kiosk_feedback')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'self_kiosk_feedback` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `patient_id` INT NOT NULL,
                `patient_name` VARCHAR(255) NOT NULL,
                `mobile_number` VARCHAR(50) NOT NULL,
                `rating` INT NOT NULL,
                `message` TEXT,
                `qr_code_id` INT NOT NULL,
                `date_created` DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        }

        // Migration: Shorten long slugs once
        $qrcodes = $this->self_kiosk_model->get();
        foreach ($qrcodes as $qr) {
            if (strlen($qr['slug']) > 10) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $new_slug = substr(str_shuffle(str_repeat($characters, 5)), 0, 10);
                $this->db->where('id', $qr['id'])->update(db_prefix() . 'self_kiosk_qrcodes', ['slug' => $new_slug]);
            }
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('self_kiosk', 'tables/qrcodes'));
        }

        $data['title'] = _l('self_kiosk_qrcodes');
        $this->load->view('self_kiosk/manage', $data);
    }

    public function code($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            $id = $id == '' ? ($data['id'] ?? '') : $id;

            if ($id == '') {
                $id = $this->self_kiosk_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('qr_code')));
                }
            } else {
                $success = $this->self_kiosk_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('qr_code')));
                }
            }
            redirect(admin_url('self_kiosk'));
        }
    }

    public function feedbacks($qr_code_id)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('self_kiosk', 'tables/feedbacks'), [
                'qr_code_id' => $qr_code_id
            ]);
        }

        $qr_code = $this->self_kiosk_model->get($qr_code_id);
        $data['title'] = 'Feedback Entries - ' . $qr_code->name;
        $data['qr_code'] = $qr_code;
        $this->load->view('self_kiosk/feedbacks', $data);
    }

    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('self_kiosk'));
        }

        $response = $this->self_kiosk_model->delete($id);
        if ($response) {
            set_alert('success', _l('deleted_successfully', _l('qr_code')));
        }
        redirect(admin_url('self_kiosk'));
    }
}
