<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Medicines_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        require_once(module_dir_path('medicines', 'install.php'));
    }

    private function get_pharmacy_group_id()
    {
        $group = $this->db->where('name', 'Pharmacy')->get(db_prefix() . 'items_groups')->row();
        return $group ? $group->id : 0;
    }

    public function get($id = '')
    {
        $this->db->select(db_prefix() . 'items.*, ' . db_prefix() . 'items_groups.name as group_name, ' . db_prefix() . 'medicine_instructions.instruction, ' . db_prefix() . 'medicine_instructions.type');
        $this->db->from(db_prefix() . 'items');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'medicine_instructions', db_prefix() . 'medicine_instructions.medicine_id = ' . db_prefix() . 'items.id', 'left');

        // Filter by Pharmacy Group
        $this->db->where(db_prefix() . 'items_groups.name', 'Pharmacy');

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'items.id', $id);
            return $this->db->get()->row();
        }

        return $this->db->get()->result();
    }

    public function add($data)
    {
        // Force group_id to Pharmacy
        $data['group_id'] = $this->get_pharmacy_group_id();

        if (isset($data['csrf_token_name'])) {
            unset($data['csrf_token_name']);
        }

        $instruction = '';
        if (isset($data['instruction'])) {
            $instruction = $data['instruction'];
            unset($data['instruction']);
        }

        $type = '';
        if (isset($data['type'])) {
            $type = $data['type'];
            unset($data['type']);
        }

        // If getting "price" as single val, ensure standard items use 'rate'
        if (isset($data['price'])) {
            $data['rate'] = $data['price'];
            unset($data['price']);
        }

        // Ensure description is managed
        if (isset($data['name'])) {
            $data['description'] = $data['name'];
            unset($data['name']);
        }

        $this->db->insert(db_prefix() . 'items', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if ($instruction != '' || $type != '') {
                $this->db->insert(db_prefix() . 'medicine_instructions', [
                    'medicine_id' => $insert_id,
                    'instruction' => $instruction,
                    'type' => $type
                ]);
            }
            log_activity('New Medicine Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }

    public function update($data, $id)
    {
        $instruction = '';
        if (isset($data['instruction'])) {
            $instruction = $data['instruction'];
            unset($data['instruction']);
        }

        $type = '';
        if (isset($data['type'])) {
            $type = $data['type'];
            unset($data['type']);
        }

        // If getting "price" as single val, ensure standard items use 'rate'
        if (isset($data['price'])) {
            $data['rate'] = $data['price'];
            unset($data['price']);
        }

        if (isset($data['csrf_token_name'])) {
            unset($data['csrf_token_name']);
        }

        if (isset($data['name'])) {
            $data['description'] = $data['name'];
            unset($data['name']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'items', $data);

        // Update instruction and type
        $this->db->where('medicine_id', $id);
        $exists = $this->db->get(db_prefix() . 'medicine_instructions')->row();
        if ($exists) {
            $this->db->where('medicine_id', $id);
            $this->db->update(db_prefix() . 'medicine_instructions', [
                'instruction' => $instruction,
                'type' => $type
            ]);
        } else {
            $this->db->insert(db_prefix() . 'medicine_instructions', [
                'medicine_id' => $id,
                'instruction' => $instruction,
                'type' => $type
            ]);
        }

        if ($this->db->affected_rows() > 0 || $instruction != '' || $type != '') { // Check generic update
            log_activity('Medicine Updated [ID: ' . $id . ']');
            return true;
        }
        return true; // Return true anyway to handle case where only instruction updated or nothing changed
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'items');
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
}
