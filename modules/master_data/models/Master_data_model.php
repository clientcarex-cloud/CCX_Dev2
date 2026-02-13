<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Master_data_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();

        // Auto-create item_statuses table
        if (!$this->db->table_exists(db_prefix() . 'item_statuses')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'item_statuses` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(150) NOT NULL,
                `group_id` int(11) NOT NULL DEFAULT 0,
                `color` varchar(20) DEFAULT NULL,
                `order` int(11) DEFAULT 0,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');

            // Migration: Copy existing Lab Statuses to 'Tests' group
            // 1. Find 'Tests' group ID
            $this->db->where('name', 'Tests');
            $group = $this->db->get(db_prefix() . 'items_groups')->row();

            if ($group) {
                // Get existing lab statuses
                $old_statuses = $this->db->get(db_prefix() . 'lab_tests_statuses')->result_array();
                foreach ($old_statuses as $status) {
                    $this->db->insert(db_prefix() . 'item_statuses', [
                        'name' => $status['name'],
                        'group_id' => $group->id
                    ]);
                }
            } else {
                // Try to find if user renamed it? Or just use ID from commonly known setup? 
                // If not found, we can't migrate specific tests statuses perfectly.
                // But let's assume standard names as per request.
                // User listed: Fee, Packages, Pharmacy, Rooms, Services, Tests

                // If specific groups not found, we skip automatic seeding or seed generic.
            }
        }

        // Auto-create tests_methods table
        if (!$this->db->table_exists(db_prefix() . 'tests_methods')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'tests_methods` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(150) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');
        }
    }

    /* Name Titles */
    public function get_name_titles($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'name_titles')->row();
        }
        return $this->db->get(db_prefix() . 'name_titles')->result_array();
    }

    public function add_name_title($data)
    {
        $this->db->insert(db_prefix() . 'name_titles', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Name Title Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_name_title($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'name_titles', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Name Title Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_name_title($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'name_titles');
        if ($this->db->affected_rows() > 0) {
            log_activity('Name Title Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Name Care Titles */
    public function get_name_care_titles($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'name_care_titles')->row();
        }
        return $this->db->get(db_prefix() . 'name_care_titles')->result_array();
    }

    public function add_name_care_title($data)
    {
        $this->db->insert(db_prefix() . 'name_care_titles', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Name Care Title Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_name_care_title($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'name_care_titles', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Name Care Title Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_name_care_title($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'name_care_titles');
        if ($this->db->affected_rows() > 0) {
            log_activity('Name Care Title Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Lab Tests Statuses */
    public function get_lab_tests_statuses($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'lab_tests_statuses')->row();
        }
        return $this->db->get(db_prefix() . 'lab_tests_statuses')->result_array();
    }

    public function add_lab_tests_status($data)
    {
        $this->db->insert(db_prefix() . 'lab_tests_statuses', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Lab Tests Status Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_lab_tests_status($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'lab_tests_statuses', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Lab Tests Status Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_lab_tests_status($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'lab_tests_statuses');
        if ($this->db->affected_rows() > 0) {
            log_activity('Lab Tests Status Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Item Statuses (Group Specific) */
    public function get_item_statuses($group_id = '', $id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'item_statuses')->row();
        }

        if (is_numeric($group_id)) {
            $this->db->where('group_id', $group_id);
        }

        $this->db->order_by('order', 'asc');
        $this->db->order_by('name', 'asc');
        return $this->db->get(db_prefix() . 'item_statuses')->result_array();
    }

    public function add_item_status($data)
    {
        $this->db->insert(db_prefix() . 'item_statuses', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Item Status Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ', Group: ' . $data['group_id'] . ']');
        }
        return $insert_id;
    }

    public function update_item_status($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'item_statuses', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Item Status Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_item_status($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'item_statuses');
        if ($this->db->affected_rows() > 0) {
            log_activity('Item Status Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Medicine Types */
    public function get_medicine_types($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'medicine_types')->row();
        }
        return $this->db->get(db_prefix() . 'medicine_types')->result_array();
    }

    public function add_medicine_type($data)
    {
        $this->db->insert(db_prefix() . 'medicine_types', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Medicine Type Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_medicine_type($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'medicine_types', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine Type Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_medicine_type($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'medicine_types');
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine Type Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Medicine Doses */
    public function get_medicine_doses($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'medicine_doses')->row();
        }
        return $this->db->get(db_prefix() . 'medicine_doses')->result_array();
    }

    public function add_medicine_dose($data)
    {
        $this->db->insert(db_prefix() . 'medicine_doses', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Medicine Dose Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_medicine_dose($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'medicine_doses', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine Dose Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_medicine_dose($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'medicine_doses');
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine Dose Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Medicine Frequencies */
    public function get_medicine_frequencies($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'medicine_frequencies')->row();
        }
        return $this->db->get(db_prefix() . 'medicine_frequencies')->result_array();
    }

    public function add_medicine_frequency($data)
    {
        $this->db->insert(db_prefix() . 'medicine_frequencies', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Medicine Frequency Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_medicine_frequency($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'medicine_frequencies', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine Frequency Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_medicine_frequency($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'medicine_frequencies');
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine Frequency Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Medicine Durations */
    public function get_medicine_durations($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'medicine_durations')->row();
        }
        return $this->db->get(db_prefix() . 'medicine_durations')->result_array();
    }

    public function add_medicine_duration($data)
    {
        $this->db->insert(db_prefix() . 'medicine_durations', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Medicine Duration Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_medicine_duration($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'medicine_durations', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine Duration Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_medicine_duration($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'medicine_durations');
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine Duration Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Medicine When */
    public function get_medicine_whens($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'medicine_whens')->row();
        }
        return $this->db->get(db_prefix() . 'medicine_whens')->result_array();
    }

    public function add_medicine_when($data)
    {
        $this->db->insert(db_prefix() . 'medicine_whens', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Medicine When Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_medicine_when($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'medicine_whens', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine When Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_medicine_when($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'medicine_whens');
        if ($this->db->affected_rows() > 0) {
            log_activity('Medicine When Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Treatments */
    public function get_treatments($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'treatments')->row();
        }
        return $this->db->get(db_prefix() . 'treatments')->result_array();
    }

    public function add_treatment($data)
    {
        $this->db->insert(db_prefix() . 'treatments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Treatment Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_treatment($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'treatments', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Treatment Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_treatment($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'treatments');
        if ($this->db->affected_rows() > 0) {
            log_activity('Treatment Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Department Groups */
    public function get_department_groups($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $group = $this->db->get(db_prefix() . 'department_groups')->row();

            if ($group) {
                $group->departments = $this->get_department_group_items($group->id);
            }
            return $group;
        }
        return $this->db->get(db_prefix() . 'department_groups')->result_array();
    }

    public function get_department_group_items($group_id)
    {
        $this->db->select('department_id');
        $this->db->where('group_id', $group_id);
        $items = $this->db->get(db_prefix() . 'department_group_items')->result_array();

        $department_ids = [];
        foreach ($items as $item) {
            $department_ids[] = $item['department_id'];
        }
        return $department_ids;
    }

    public function add_department_group($data)
    {
        $departments = [];
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        $this->db->insert(db_prefix() . 'department_groups', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            foreach ($departments as $department_id) {
                $this->db->insert(db_prefix() . 'department_group_items', [
                    'group_id' => $insert_id,
                    'department_id' => $department_id
                ]);
            }
            log_activity('New Department Group Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_department_group($data, $id)
    {
        $departments = [];
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'department_groups', $data);

        if ($this->db->affected_rows() > 0 || !empty($departments)) {
            // Update items
            $this->db->where('group_id', $id);
            $this->db->delete(db_prefix() . 'department_group_items');

            foreach ($departments as $department_id) {
                $this->db->insert(db_prefix() . 'department_group_items', [
                    'group_id' => $id,
                    'department_id' => $department_id
                ]);
            }

            log_activity('Department Group Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_department_group($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'department_groups');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('group_id', $id);
            $this->db->delete(db_prefix() . 'department_group_items');

            log_activity('Department Group Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /* Tests Methods */
    public function get_tests_methods($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'tests_methods')->row();
        }
        return $this->db->get(db_prefix() . 'tests_methods')->result_array();
    }

    public function add_tests_method($data)
    {
        $this->db->insert(db_prefix() . 'tests_methods', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Tests Method Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }
        return $insert_id;
    }

    public function update_tests_method($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tests_methods', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Tests Method Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function delete_tests_method($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tests_methods');
        if ($this->db->affected_rows() > 0) {
            log_activity('Tests Method Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
}
