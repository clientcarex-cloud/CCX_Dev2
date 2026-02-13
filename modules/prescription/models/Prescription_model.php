<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Prescription_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();

        // Auto-fix schema if table exists but is wrong (from old install.php)
        if ($this->db->table_exists(db_prefix() . 'prescriptions')) {
            if (!$this->db->field_exists('patient_id', db_prefix() . 'prescriptions')) {
                $this->db->query('DROP TABLE ' . db_prefix() . 'prescriptions');
            }
        }

        // Auto-create tables
        if (!$this->db->table_exists(db_prefix() . 'prescriptions')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'prescriptions` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `patient_id` int(11) NOT NULL,
                `visit_id` int(11) NOT NULL,
                `staff_id` int(11) NOT NULL,
                `datecreated` datetime NOT NULL,
                `bp_sys` varchar(10) DEFAULT NULL,
                `bp_dia` varchar(10) DEFAULT NULL,
                `pulse` varchar(10) DEFAULT NULL,
                `height` varchar(10) DEFAULT NULL,
                `weight` varchar(10) DEFAULT NULL,
                `temperature` varchar(10) DEFAULT NULL,
                `spo2` varchar(10) DEFAULT NULL,
                `bmi` varchar(10) DEFAULT NULL,
                `complaints` text,
                `diagnosis` text,
                `advice` text,
                `personal_history` text,
                `family_history` text,
                `allergies` text,
                `vaccination` text,
                `tests_requested` text,
                `next_visit_qty` int(11) DEFAULT NULL,
                `next_visit_unit` varchar(20) DEFAULT NULL,
                `next_visit_date` date DEFAULT NULL,
                `date_completed` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');
        } else {
            // Check for new columns and add them if they don't exist
            if (!$this->db->field_exists('personal_history', db_prefix() . 'prescriptions')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions` ADD `personal_history` TEXT AFTER `advice`');
            }
            if (!$this->db->field_exists('input_config', db_prefix() . 'prescriptions')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions` ADD `input_config` TEXT NULL AFTER `next_visit_date`');
            }
            if (!$this->db->field_exists('family_history', db_prefix() . 'prescriptions')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions` ADD `family_history` TEXT AFTER `personal_history`');
            }
            if (!$this->db->field_exists('allergies', db_prefix() . 'prescriptions')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions` ADD `allergies` TEXT AFTER `family_history`');
            }
            if (!$this->db->field_exists('vaccination', db_prefix() . 'prescriptions')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions` ADD `vaccination` TEXT AFTER `allergies`');
            }
            if (!$this->db->field_exists('date_completed', db_prefix() . 'prescriptions')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions` ADD `date_completed` datetime DEFAULT NULL AFTER `next_visit_date`');
            }
            if (!$this->db->field_exists('consultation_duration', db_prefix() . 'prescriptions')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions` ADD `consultation_duration` int(11) DEFAULT 0 AFTER `date_completed`');
            }
        }


        if (!$this->db->table_exists(db_prefix() . 'prescription_qa_templates')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'prescription_qa_templates` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `questions` text,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');
        }

        if (!$this->db->table_exists(db_prefix() . 'prescription_items')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'prescription_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `prescription_id` int(11) NOT NULL,
                `type` varchar(50) DEFAULT NULL,
                `medicine_name` varchar(255) DEFAULT NULL,
                `dose` varchar(50) DEFAULT NULL,
                `when_f` varchar(50) DEFAULT NULL,
                `frequency` varchar(50) DEFAULT NULL,
                `duration` varchar(50) DEFAULT NULL,
                `instruction` text,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');
        }

        if (!$this->db->table_exists(db_prefix() . 'prescription_master_data')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'prescription_master_data` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `category` varchar(100) NOT NULL,
              `name` varchar(255) NOT NULL,
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');
        }

        // New Custom Fields Table
        if (!$this->db->table_exists(db_prefix() . 'prescription_custom_fields')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'prescription_custom_fields` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `slug` varchar(100) NOT NULL,
                `label` varchar(255) NOT NULL,
                `type` varchar(50) DEFAULT "smart_editor",
                `field_order` int(11) DEFAULT 0,
                `mandatory` tinyint(1) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `is_system` tinyint(1) DEFAULT 0,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');

            // Populate Default System Fields
            $default_fields = [
                ['slug' => 'complaints', 'label' => 'Complaints', 'field_order' => 1],
                ['slug' => 'diagnosis', 'label' => 'Diagnosis', 'field_order' => 2],
                ['slug' => 'advice', 'label' => 'Advice', 'field_order' => 3],
                ['slug' => 'personal_history', 'label' => 'Personal History', 'field_order' => 4],
                ['slug' => 'family_history', 'label' => 'Family History', 'field_order' => 5],
                ['slug' => 'allergies', 'label' => 'Allergies', 'field_order' => 6],
                ['slug' => 'vaccination', 'label' => 'Vaccination', 'field_order' => 7]
            ];

            foreach ($default_fields as $field) {
                $this->db->insert(db_prefix() . 'prescription_custom_fields', [
                    'slug' => $field['slug'],
                    'label' => $field['label'],
                    'type' => get_option('prescription_input_type_' . $field['slug']) ? get_option('prescription_input_type_' . $field['slug']) : 'smart_editor',
                    'field_order' => $field['field_order'],
                    'mandatory' => get_option('prescription_mandatory_' . $field['slug']),
                    'is_active' => get_option('prescription_show_' . $field['slug']),
                    'is_system' => 1
                ]);
            }
        } else {
            // Check for new columns and add them if they don't exist
            if (!$this->db->field_exists('custom_data', db_prefix() . 'prescriptions')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions` ADD `custom_data` LONGTEXT NULL AFTER `consultation_duration`');
            }
        }
    }

    /**
     * Get Master Data
     * @param  string $category
     * @return array
     */
    public function get_master_data($category)
    {
        $this->db->where('category', $category);
        $this->db->order_by('name', 'ASC');
        return $this->db->get(db_prefix() . 'prescription_master_data')->result_array();
    }

    /**
     * Add Master Data
     * @param array $data
     */
    public function add_master_data($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'prescription_master_data', $data);
        return $this->db->insert_id();
    }

    /**
     * Update Master Data
     * @param  int $id
     * @param  array $data
     * @return boolean
     */
    public function update_master_data($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'prescription_master_data', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete Master Data
     * @param  int $id
     * @return boolean
     */
    public function delete_master_data($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'prescription_master_data');
        return $this->db->affected_rows() > 0;
    }

    public function add_prescription($data)
    {
        // Extract Items
        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        if (isset($data['consultation_id'])) {
            unset($data['consultation_id']);
        }

        // Handle Multiselect Tests
        if (isset($data['tests_requested']) && is_array($data['tests_requested'])) {
            $data['tests_requested'] = implode(',', $data['tests_requested']);
        }

        // Prescriptions Data
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['staff_id'] = get_staff_user_id();

        // Separate Custom Fields
        $custom_fields = $this->get_custom_fields();
        $custom_data = [];
        foreach ($custom_fields as $field) {
            if ($field['is_system'] == 0 && isset($data[$field['slug']])) {
                $custom_data[$field['slug']] = $data[$field['slug']];
                unset($data[$field['slug']]);
            }
        }
        $data['custom_data'] = json_encode($custom_data);

        $this->db->insert(db_prefix() . 'prescriptions', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            foreach ($items as $item) {
                if (empty($item['medicine_name']))
                    continue;

                $item['prescription_id'] = $insert_id;
                $this->db->insert(db_prefix() . 'prescription_items', $item);
            }

            // Log Activity
            log_activity('New Prescription Created [ID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    public function get_prescription($id)
    {
        $this->db->where('id', $id);
        $prescription = $this->db->get(db_prefix() . 'prescriptions')->row();

        if ($prescription) {
            $this->db->where('prescription_id', $id);
            $prescription->items = $this->db->get(db_prefix() . 'prescription_items')->result_array();

            // Decode Custom Data
            if (!empty($prescription->custom_data)) {
                $prescription->custom_data = json_decode($prescription->custom_data, true);
            } else {
                $prescription->custom_data = [];
            }
        }

        return $prescription;
    }

    public function update_prescription($id, $data)
    {
        // Extract Items
        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        if (isset($data['consultation_id'])) {
            unset($data['consultation_id']);
        }

        // Handle Multiselect Tests
        if (isset($data['tests_requested']) && is_array($data['tests_requested'])) {
            $data['tests_requested'] = implode(',', $data['tests_requested']);
        }

        // Separate Custom Fields
        $custom_fields = $this->get_custom_fields();
        // We need to fetch existing custom data to merge incase we are only updating partials (though controller usually sends all)
        // But for safety, let's just rewrite what we get.
        $custom_data = [];
        foreach ($custom_fields as $field) {
            if ($field['is_system'] == 0 && isset($data[$field['slug']])) {
                $custom_data[$field['slug']] = $data[$field['slug']];
                unset($data[$field['slug']]);
            }
        }

        // If we are updating, we should probably merge with existing custom_data if we want to be safe, 
        // but typically the edit form submits everything.
        // Let's check if there is existing data to keep if not present in post? 
        // Actually, for HTML forms, unchecked checkboxes might be missing, so we must be careful.
        // But here we are dealing with smart boxes (textarea/input), so they should be present as empty strings if empty.

        // HOWEVER, if we are just updating status or something else, we might not have these fields.
        // So we should only update custom_data if we actually found any custom fields in the $data.
        if (!empty($custom_data)) {
            // Retrieve existing to merge? Or just Overwrite? 
            // Better to fetch existing, decode, merge, and encode.
            $existing = $this->get_prescription($id);
            $existing_custom = (array) $existing->custom_data;
            $final_custom = array_merge($existing_custom, $custom_data);
            $data['custom_data'] = json_encode($final_custom);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'prescriptions', $data);

        // Update Items - Simple strategy: Delete all and recreate
        $this->db->where('prescription_id', $id);
        $this->db->delete(db_prefix() . 'prescription_items');

        foreach ($items as $item) {
            if (empty($item['medicine_name']))
                continue;

            $item['prescription_id'] = $id;
            $this->db->insert(db_prefix() . 'prescription_items', $item);
        }

        log_activity('Prescription Updated [ID: ' . $id . ']');
        return true;
    }

    public function get_consultations($filters = [])
    {
        $this->db->select(
            db_prefix() . 'patient_tests.id, ' .
            db_prefix() . 'patient_tests.status, ' .
            db_prefix() . 'clients.company as patient_name, ' .
            db_prefix() . 'clients.userid as patient_id, ' .
            db_prefix() . 'patients_extra.age, ' .
            db_prefix() . 'patients_extra.age_unit, ' .
            db_prefix() . 'patients_extra.gender, ' .
            db_prefix() . 'patients_extra.mr_number, ' .
            db_prefix() . 'visits.visit_code, ' .
            db_prefix() . 'visits.id as visit_id, ' .
            'CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as doctor_name, ' .
            db_prefix() . 'items.description as consultation_name, ' .
            db_prefix() . 'visits.created_at as visit_date, ' .
            db_prefix() . 'prescriptions.id as prescription_id, ' .
            db_prefix() . 'prescriptions.datecreated as prescription_date, ' .
            db_prefix() . 'prescriptions.date_completed as prescription_completed_date, ' .
            db_prefix() . 'prescriptions.consultation_duration'
        );

        $this->db->from(db_prefix() . 'patient_tests');

        // Joins
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'visits.primary_doctor_id', 'left');

        // Join Prescriptions
        $this->db->join(db_prefix() . 'prescriptions', db_prefix() . 'prescriptions.visit_id = ' . db_prefix() . 'visits.id', 'left');

        // Filter by Consultation Group (Fee)
        $this->db->group_start();
        $this->db->where(db_prefix() . 'items_groups.name', 'Fee');
        $this->db->group_end();


        // Additional Filters
        if (isset($filters['status']) && $filters['status'] != 'all') {
            if ($filters['status'] == 'Emergency') {
                $this->db->where(db_prefix() . 'patient_tests.is_emergency', 1);
            } else {
                $this->db->where(db_prefix() . 'patient_tests.status', $filters['status']);
            }
        }

        if (isset($filters['from_date']) && !empty($filters['from_date'])) {
            $this->db->where('DATE(' . db_prefix() . 'visits.created_at) >=', to_sql_date($filters['from_date']));
        }

        if (isset($filters['to_date']) && !empty($filters['to_date'])) {
            $this->db->where('DATE(' . db_prefix() . 'visits.created_at) <=', to_sql_date($filters['to_date']));
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left'); // Needed for MR number search
            $this->db->group_start();
            $this->db->like(db_prefix() . 'clients.company', $filters['search']);
            $this->db->or_like(db_prefix() . 'patients_extra.mr_number', $filters['search']);
            $this->db->or_like(db_prefix() . 'visits.visit_code', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by(db_prefix() . 'visits.created_at', 'DESC');

        return $this->db->get()->result_array();
    }

    public function update_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'patient_tests', [
            'status' => $status
        ]);
        return $this->db->affected_rows() > 0;
    }

    public function get_status_counts($filters = [])
    {
        $this->db->select(db_prefix() . 'patient_tests.status, COUNT(*) as count, SUM(CASE WHEN ' . db_prefix() . 'patient_tests.is_emergency = 1 THEN 1 ELSE 0 END) as emergency_count');
        $this->db->from(db_prefix() . 'patient_tests');

        // Joins needed for filters
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left'); // For search filter ideally, but mostly we need group 'Fee'

        // Filter by Consultation Group (Fee)
        $this->db->group_start();
        $this->db->where(db_prefix() . 'items_groups.name', 'Fee');
        $this->db->group_end();

        // Apply Date Filter if exists
        if (isset($filters['from_date']) && !empty($filters['from_date'])) {
            $this->db->where('DATE(' . db_prefix() . 'visits.created_at) >=', to_sql_date($filters['from_date']));
        }

        if (isset($filters['to_date']) && !empty($filters['to_date'])) {
            $this->db->where('DATE(' . db_prefix() . 'visits.created_at) <=', to_sql_date($filters['to_date']));
        }

        // Apply Search Filter if exists (to match list counts)
        if (isset($filters['search']) && !empty($filters['search'])) {
            $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'patient_tests.patient_id', 'left'); // Needed for MR number search
            $this->db->group_start();
            $this->db->like(db_prefix() . 'clients.company', $filters['search']);
            $this->db->or_like(db_prefix() . 'patients_extra.mr_number', $filters['search']);
            $this->db->or_like(db_prefix() . 'visits.visit_code', $filters['search']);
            $this->db->group_end();
        }

        $this->db->group_by(db_prefix() . 'patient_tests.status');

        $query = $this->db->get()->result_array();

        $counts = [
            'all' => 0,
            'Pending' => 0,
            'Processing' => 0,
            'Emergency' => 0,
            'Completed' => 0
            // Add other statuses if necessary
        ];

        foreach ($query as $row) {
            $status = $row['status'];
            $count = (int) $row['count'];
            $emergency_count = (int) $row['emergency_count'];

            if (isset($counts[$status])) {
                $counts[$status] += $count;
            } else {
                $counts[$status] = $count;
            }
            $counts['all'] += $count;
            $counts['Emergency'] += $emergency_count;
        }

        return $counts;
    }

    public function get_test_items()
    {
        $this->db->select(db_prefix() . 'items.id, ' . db_prefix() . 'items.description as name'); // perfex items use description as name usually
        $this->db->from(db_prefix() . 'items');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id');
        $this->db->where(db_prefix() . 'items_groups.name', 'Tests');

        return $this->db->get()->result_array();
    }

    public function get_medicines()
    {
        $this->db->select(
            db_prefix() . 'items.id, ' .
            db_prefix() . 'items.description as name, ' .
            db_prefix() . 'medicine_instructions.instruction, ' .
            db_prefix() . 'medicine_instructions.type as type_id'
        );
        $this->db->from(db_prefix() . 'items');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'medicine_instructions', db_prefix() . 'medicine_instructions.medicine_id = ' . db_prefix() . 'items.id', 'left');

        $this->db->where(db_prefix() . 'items_groups.name', 'Pharmacy');

        return $this->db->get()->result_array();
    }

    /**
     * Get QA Templates
     * @param  string $id (optional)
     * @return mixed
     */
    public function get_qa_templates($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'prescription_qa_templates')->row();
        }
        return $this->db->get(db_prefix() . 'prescription_qa_templates')->result_array();
    }

    /**
     * Add QA Template
     * @param array $data
     */
    public function add_qa_template($data)
    {
        $this->db->insert(db_prefix() . 'prescription_qa_templates', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }
        return false;
    }

    /**
     * Update QA Template
     * @param  array $data
     * @param  mixed $id
     * @return boolean
     */
    public function update_qa_template($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'prescription_qa_templates', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Delete QA Template
     * @param  mixed $id
     * @return boolean
     */
    public function delete_qa_template($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'prescription_qa_templates');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Check if a specific question text is used in any prescription
     * @param  string  $question_text
     * @return boolean
     */
    public function is_question_used($question_text)
    {
        // Columns to search in
        $columns = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination'];

        $this->db->group_start();
        foreach ($columns as $col) {
            // Searching for JSON pattern "q":"<question_text>"
            $this->db->or_like($col, '"q":"' . $this->db->escape_like_str($question_text) . '"');
        }
        $this->db->group_end();

        // We only care if at least one exists
        $this->db->limit(1);
        $result = $this->db->get(db_prefix() . 'prescriptions')->row();

        return ($result) ? true : false;
    }

    /**
     * Check if a QA Template is in use
     * Usage defined as: 
     * 1. Assigned in Settings
     * 2. Any of its CURRENT questions are used in prescriptions
     * @param  int  $id
     * @return boolean|string True if used (or string reason), False if safe
     */
    public function is_template_in_use($id)
    {
        // 1. Check Settings
        $settings_types = [
            'prescription_qa_template_complaints',
            'prescription_qa_template_diagnosis',
            'prescription_qa_template_advice',
            'prescription_qa_template_personal_history',
            'prescription_qa_template_family_history',
            'prescription_qa_template_allergies',
            'prescription_qa_template_vaccination'
        ];

        $this->db->where_in('name', $settings_types);
        $this->db->where('value', $id);
        $setting = $this->db->get(db_prefix() . 'options')->row();

        if ($setting) {
            return "This template is currently assigned in Prescription Settings.";
        }

        // 2. Check if questions in this template are used
        $template = $this->get_qa_templates($id);
        if ($template) {
            $questions = json_decode($template->questions, true);
            if (!empty($questions)) {
                foreach ($questions as $q) {
                    $q_text = is_array($q) ? $q['text'] : $q;
                    if ($this->is_question_used($q_text)) {
                        return "Questions from this template are used in existing prescriptions.";
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get usage count of a template (Number of prescriptions using its questions)
     * @param  int $id Template ID
     * @return int
     */
    public function get_template_usage_count($id)
    {
        $template = $this->get_qa_templates($id);
        if (!$template || empty($template->questions)) {
            return 0;
        }

        $questions = json_decode($template->questions, true);
        if (empty($questions)) {
            return 0;
        }

        $question_texts = [];
        foreach ($questions as $q) {
            $question_texts[] = is_array($q) ? $q['text'] : $q;
        }

        if (empty($question_texts)) {
            return 0;
        }

        // Columns to search in
        $columns = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination'];

        $this->db->select('COUNT(DISTINCT id) as count');
        $this->db->group_start();
        foreach ($columns as $col) {
            foreach ($question_texts as $text) {
                $this->db->or_like($col, '"q":"' . $this->db->escape_like_str($text) . '"');
            }
        }
        $this->db->group_end();

        $result = $this->db->get(db_prefix() . 'prescriptions')->row();
        return $result ? (int) $result->count : 0;
    }

    /**
     * Delete Prescription and return Visit ID
     * @param  int $id Prescription ID
     * @return int|boolean Visit ID or false
     */
    public function delete_prescription($id)
    {
        $this->db->where('id', $id);
        $prescription = $this->db->get(db_prefix() . 'prescriptions')->row();

        if ($prescription) {
            $visit_id = $prescription->visit_id;

            // Delete Items
            $this->db->where('prescription_id', $id);
            $this->db->delete(db_prefix() . 'prescription_items');

            // Delete Prescription
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'prescriptions');

            // Log Activity
            log_activity('Prescription Deleted [ID: ' . $id . ']');

            return $visit_id;
        }

        return false;
    }

    /**
     * Get Custom Fields
     * @param  boolean $only_active
     * @return array
     */
    public function get_custom_fields($only_active = false)
    {
        if ($only_active) {
            $this->db->where('is_active', 1);
        }
        $this->db->order_by('field_order', 'ASC');
        return $this->db->get(db_prefix() . 'prescription_custom_fields')->result_array();
    }

    /**
     * Add Custom Field
     * @param array $data
     */
    public function add_custom_field($data)
    {
        $this->db->insert(db_prefix() . 'prescription_custom_fields', $data);
        return $this->db->insert_id();
    }

    /**
     * Update Custom Field
     * @param  int $id
     * @param  array $data
     * @return boolean
     */
    public function update_custom_field($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'prescription_custom_fields', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete Custom Field
     * @param  int $id
     * @return boolean
     */
    public function delete_custom_field($id)
    {
        // Prevent deleting system fields
        $this->db->where('id', $id);
        $field = $this->db->get(db_prefix() . 'prescription_custom_fields')->row();
        if ($field && $field->is_system == 1) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'prescription_custom_fields');
        return $this->db->affected_rows() > 0;
    }
    public function get_field_usage_count($slug)
    {
        // Check if the slug exists in the custom_data JSON string
        // We look for "slug": pattern
        $this->db->like('custom_data', '"' . $slug . '":');
        // We also check for non-empty value if possible, but LIKE pattern implies key existence which is "usage" enough for now.
        // A stricter check would be tough with just LIKE on JSON text.
        return $this->db->count_all_results(db_prefix() . 'prescriptions');
    }
}

