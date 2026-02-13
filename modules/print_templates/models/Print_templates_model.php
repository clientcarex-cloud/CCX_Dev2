<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Print_templates_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_types()
    {
        $default_types = [
            'Invoice',
            'Bill',
            'Lab Test Report (Fixed)',
            'Lab Test Report (Word)',
            'PNDT',
            'Refund',
            'Barcode',
            'OP Bill',
            'Expense',
            'Pharmacy Bill',
            'Prescription',
            'IP Bill',
            'Refund Bill',
            'Report',
            'Discharge Summary',
            'IP Final Bill',
            'Report Header',
            'Report Footer',
            'Department Footer',
            'Department Header',
        ];

        if ($this->db->table_exists(db_prefix() . 'print_template_types')) {
            $types = $this->db->get(db_prefix() . 'print_template_types')->result_array();
            if (count($types) > 0) {
                return $types;
            }
        }

        // Fallback or "seeding" structure if table empty/missing logic preferred
        $types = [];
        foreach ($default_types as $t) {
            $types[] = ['type' => $t];
        }
        return $types;
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'print_templates')->row();
        }

        return $this->db->get(db_prefix() . 'print_templates')->result_array();
    }

    public function add($data)
    {
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->db->where('type', $data['type']);
            $this->db->update(db_prefix() . 'print_templates', ['is_default' => 0]);
        } else {
            $data['is_default'] = 0;
        }

        $this->db->insert(db_prefix() . 'print_templates', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Print Template Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }

        return false;
    }

    public function update($data, $id)
    {
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->db->where('type', $data['type']);
            $this->db->where('id !=', $id);
            $this->db->update(db_prefix() . 'print_templates', ['is_default' => 0]);
        } else {
            $data['is_default'] = 0;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'print_templates', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Print Template Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    public function get_all_simple()
    {
        $this->db->select('id, name');
        return $this->db->get(db_prefix() . 'print_templates')->result_array();
    }

    public function expand_template_tags($content, $depth = 0)
    {
        // Recursion limit
        if ($depth > 3) {
            return $content;
        }

        // Match {print_template_123}
        return preg_replace_callback('/\{print_template_(\d+)\}/', function ($matches) use ($depth) {
            $template_id = $matches[1];
            // Use existing get() method
            $template = $this->get($template_id);
            if ($template && isset($template->content)) {
                // Recursively expand included content
                return $this->expand_template_tags($template->content, $depth + 1);
            }
            return ''; // Template not found or empty
        }, $content);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'print_templates');

        if ($this->db->affected_rows() > 0) {
            log_activity('Print Template Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }
}
