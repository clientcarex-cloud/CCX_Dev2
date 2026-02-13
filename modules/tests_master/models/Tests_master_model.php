<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tests_master_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
        if (!$this->db->field_exists('b2b_price', db_prefix() . 'items')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "items` ADD `b2b_price` DECIMAL(15,2) DEFAULT '0.00' NOT NULL;");
        }
        if (!$this->db->field_exists('test_method_id', db_prefix() . 'items')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "items` ADD `test_method_id` INT(11) DEFAULT 0;");
        }
        if (!$this->db->field_exists('is_active', db_prefix() . 'items')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "items` ADD `is_active` INT(11) DEFAULT 1;");
        }
    }

    /**
     * Get test (item) by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */
    public function get($id = '')
    {
        $this->db->select(db_prefix() . 'items.*, ' . db_prefix() . 'items_groups.name as group_name');
        $this->db->from(db_prefix() . 'items');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'items.id', $id);
            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    /**
     * Add new test
     * @param array $data
     * @return boolean
     */
    public function add($data)
    {
        // Custom fields handling
        $custom_fields = [
            'is_price_changable' => isset($data['is_price_changable']) ? 1 : 0,
            'is_authorization_required' => isset($data['is_authorization_required']) ? 1 : 0,
            'is_blood_sample_required' => isset($data['is_blood_sample_required']) ? 1 : 0,
            'test_method_id' => isset($data['test_method_id']) ? $data['test_method_id'] : 0,
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ];

        // Remove them from data if they might cause issues with standard model (though usually extra fields are ignored or cause DB error if not in valid columns list if model checks)
        // invoice_items_model might not check columns, so passing them might be fine if table has columns. 
        // BUT, to be safe and ensure they are saved, let's do it explicitly.

        // Pass data to standard model
        $id = $this->invoice_items_model->add($data);

        if ($id) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'items', $custom_fields);
            return $id;
        }
        return false;
    }

    /**
     * Edit test
     * @param array $data
     * @return boolean
     */
    public function edit($data)
    {
        $custom_fields = [
            'is_price_changable' => isset($data['is_price_changable']) ? 1 : 0,
            'is_authorization_required' => isset($data['is_authorization_required']) ? 1 : 0,
            'is_blood_sample_required' => isset($data['is_blood_sample_required']) ? 1 : 0,
            'test_method_id' => isset($data['test_method_id']) ? $data['test_method_id'] : 0,
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ];

        $success = $this->invoice_items_model->edit($data);

        // Always attempt to update custom fields, because if only these flags changed, the parent model returns false (no changes)
        $this->db->where('id', $data['itemid']);
        $this->db->update(db_prefix() . 'items', $custom_fields);

        if ($this->db->affected_rows() > 0) {
            $success = true;
        }

        return $success;
    }

    /**
     * Delete test
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
        return $this->invoice_items_model->delete($id);
    }

    /**
     * Add new template
     * @param array $data
     * @return mixed
     */
    public function add_template($data)
    {
        $this->db->insert(db_prefix() . 'tests_word_templates', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }
        return false;
    }

    /**
     * Edit template
     * @param array $data
     * @param mixed $id
     * @return boolean
     */
    public function edit_template($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tests_word_templates', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get templates for a test
     * @param mixed $test_id
     * @return array
     */
    public function get_templates($test_id)
    {
        $this->db->where('test_id', $test_id);
        return $this->db->get(db_prefix() . 'tests_word_templates')->result_array();
    }

    /**
     * Get single template
     * @param mixed $id
     * @return object
     */
    public function get_template($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'tests_word_templates')->row();
    }

    /**
     * Delete template
     * @param mixed $id
     * @return boolean
     */
    public function delete_template($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tests_word_templates');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Set default template
     * @param mixed $id
     * @param mixed $test_id
     * @return boolean
     */
    public function set_default_template($id, $test_id)
    {
        // First reset all defaults for this test
        $this->db->where('test_id', $test_id);
        $this->db->update(db_prefix() . 'tests_word_templates', ['is_default' => 0]);

        // Set the new default
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tests_word_templates', ['is_default' => 1]);

        return true;
    }

    /* Fixed Template Parameters CRUD */
    public function add_parameter($data)
    {
        $this->db->insert(db_prefix() . 'tests_params', $data);
        return $this->db->insert_id();
    }

    public function edit_parameter($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tests_params', $data);
        return true;
    }

    public function get_parameters($fixed_template_id)
    {
        $this->db->where('fixed_template_id', $fixed_template_id);
        $this->db->order_by('sort_order', 'asc');
        $this->db->order_by('id', 'asc');
        return $this->db->get(db_prefix() . 'tests_params')->result_array();
    }

    public function delete_parameter($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tests_params');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function update_remarks($test_id, $remarks)
    {
        $this->db->where('id', $test_id);
        $this->db->update(db_prefix() . 'items', ['fixed_template_remarks' => $remarks]);
        return true;
    }

    public function update_parameter_order($data)
    {
        foreach ($data as $order) {
            $this->db->where('id', $order[0]);
            $this->db->update(db_prefix() . 'tests_params', ['sort_order' => $order[1]]);
        }
        return true;
    }

    /* Multiple Fixed Templates CRUD */
    public function add_fixed_template($data)
    {
        $this->db->insert(db_prefix() . 'tests_fixed_templates', $data);
        return $this->db->insert_id();
    }

    public function edit_fixed_template($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update(db_prefix() . 'tests_fixed_templates', [
            'template_name' => $data['template_name'],
            'remarks' => $data['remarks']
        ]);
        return true;
    }

    public function delete_fixed_template($id)
    {
        // Delete params first
        $this->db->where('fixed_template_id', $id);
        $this->db->delete(db_prefix() . 'tests_params');

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tests_fixed_templates');

        return $this->db->affected_rows() > 0;
    }

    public function get_fixed_templates($test_id)
    {
        $this->db->where('test_id', $test_id);
        return $this->db->get(db_prefix() . 'tests_fixed_templates')->result_array();
    }

    public function get_fixed_template($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'tests_fixed_templates')->row();
    }

    public function set_default_fixed_template($id, $test_id)
    {
        // Reset all
        $this->db->where('test_id', $test_id);
        $this->db->update(db_prefix() . 'tests_fixed_templates', ['is_default' => 0]);

        // Set new
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tests_fixed_templates', ['is_default' => 1]);
        return true;
    }

    public function update_active_template_type($test_id, $type)
    {
        $this->db->where('id', $test_id);
        $this->db->update(db_prefix() . 'items', ['active_template_type' => $type]);
        return true;
    }

    /**
     * Check if code exists
     * @param string $code
     * @param mixed $exclude_id
     * @return boolean
     */
    public function check_code_exists($code, $exclude_id = '')
    {
        $this->db->where('code', $code);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        $count = $this->db->count_all_results(db_prefix() . 'items');
        return $count > 0;
    }
    public function change_status_active($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'items', [
            'is_active' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
}
