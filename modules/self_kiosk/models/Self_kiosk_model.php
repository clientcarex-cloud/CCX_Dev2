<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Self_kiosk_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get one or all self kiosk QR codes
     * @param  string $id
     * @return mixed
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'self_kiosk_qrcodes')->row();
        }

        return $this->db->get(db_prefix() . 'self_kiosk_qrcodes')->result_array();
    }

    /**
     * Add new self kiosk QR code
     * @param array $data
     */
    public function add($data)
    {
        $data['date_created'] = date('Y-m-d H:i:s');
        $data['staff_id'] = get_staff_user_id();
        $data['slug'] = $this->generate_slug();

        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (isset($data['services'])) {
            $data['settings'] = json_encode($data['services']);
            unset($data['services']);
        }

        $this->db->insert(db_prefix() . 'self_kiosk_qrcodes', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Self Kiosk QR Code Created [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update self kiosk QR code
     * @param  array $data
     * @param  mixed $id
     * @return boolean
     */
    public function update($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (isset($data['services'])) {
            $data['settings'] = json_encode($data['services']);
            unset($data['services']);
        } else {
            $data['settings'] = json_encode([]); // Default empty if none selected
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'self_kiosk_qrcodes', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Self Kiosk QR Code Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete self kiosk QR code
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'self_kiosk_qrcodes');

        if ($this->db->affected_rows() > 0) {
            log_activity('Self Kiosk QR Code Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Generate a unique slug
     * @return string
     */
    private function generate_slug()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $slug = substr(str_shuffle(str_repeat($characters, 5)), 0, 10);
        // Ensure uniqueness
        if ($this->db->where('slug', $slug)->count_all_results(db_prefix() . 'self_kiosk_qrcodes') > 0) {
            return $this->generate_slug();
        }
        return $slug;
    }

    /**
     * Get by slug
     * @param  string $slug
     * @return object
     */
    public function get_by_slug($slug)
    {
        $this->db->where('slug', $slug);
        return $this->db->get(db_prefix() . 'self_kiosk_qrcodes')->row();
    }
}
