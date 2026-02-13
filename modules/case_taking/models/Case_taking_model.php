<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Case_taking_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();

        // Auto-fix schema if table exists but is wrong (backup check)
        if ($this->db->table_exists(db_prefix() . 'case_taking')) {
            if (!$this->db->field_exists('patient_id', db_prefix() . 'case_taking')) {
                // $this->db->query('DROP TABLE ' . db_prefix() . 'case_taking'); // Be careful with drop
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
        return $this->db->get(db_prefix() . 'case_taking_master_data')->result_array();
    }

    /**
     * Add Master Data
     * @param array $data
     */
    public function add_master_data($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'case_taking_master_data', $data);
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
        $this->db->update(db_prefix() . 'case_taking_master_data', $data);
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
        $this->db->delete(db_prefix() . 'case_taking_master_data');
        return $this->db->affected_rows() > 0;
    }

    public function add_case_taking($data)
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



        // Case Taking Data
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

        $this->db->insert(db_prefix() . 'case_taking', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {

            // Log Activity
            log_activity('New Case Taking Created [ID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    public function get_case_taking($id)
    {
        $this->db->where('id', $id);
        $case_taking = $this->db->get(db_prefix() . 'case_taking')->row();

        if ($case_taking) {

            // Decode Custom Data
            if (!empty($case_taking->custom_data)) {
                $case_taking->custom_data = json_decode($case_taking->custom_data, true);
            } else {
                $case_taking->custom_data = [];
            }
        }

        return $case_taking;
    }

    public function update_case_taking($id, $data)
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



        // Separate Custom Fields
        $custom_fields = $this->get_custom_fields();
        $custom_data = [];
        foreach ($custom_fields as $field) {
            if ($field['is_system'] == 0 && isset($data[$field['slug']])) {
                $custom_data[$field['slug']] = $data[$field['slug']];
                unset($data[$field['slug']]);
            }
        }

        if (!empty($custom_data)) {
            $existing = $this->get_case_taking($id);
            $existing_custom = (array) $existing->custom_data;
            $final_custom = array_merge($existing_custom, $custom_data);
            $data['custom_data'] = json_encode($final_custom);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'case_taking', $data);

        // Update Items - Simple strategy: Delete all and recreate

        log_activity('Case Taking Updated [ID: ' . $id . ']');
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
            db_prefix() . 'case_taking.id as case_taking_id, ' .
            db_prefix() . 'case_taking.datecreated as case_taking_date, ' .
            db_prefix() . 'case_taking.date_completed as case_taking_completed_date, ' .
            db_prefix() . 'case_taking.consultation_duration'
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

        // Join Case Taking
        $this->db->join(db_prefix() . 'case_taking', db_prefix() . 'case_taking.visit_id = ' . db_prefix() . 'visits.id', 'left');

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
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'patient_tests.patient_id', 'left');

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




    /**
     * Get QA Templates
     * @param  string $id (optional)
     * @return mixed
     */
    public function get_qa_templates($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'case_taking_qa_templates')->row();
        }
        return $this->db->get(db_prefix() . 'case_taking_qa_templates')->result_array();
    }

    /**
     * Add QA Template
     * @param array $data
     */
    public function add_qa_template($data)
    {
        $this->db->insert(db_prefix() . 'case_taking_qa_templates', $data);
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
        $this->db->update(db_prefix() . 'case_taking_qa_templates', $data);
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
        $this->db->delete(db_prefix() . 'case_taking_qa_templates');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Check if a specific question text is used in any case taking
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
        $result = $this->db->get(db_prefix() . 'case_taking')->row();

        return ($result) ? true : false;
    }

    /**
     * Check if a QA Template is in use
     * Usage defined as: 
     * 1. Assigned in Settings
     * 2. Any of its CURRENT questions are used in case taking
     * @param  int  $id
     * @return boolean|string True if used (or string reason), False if safe
     */
    public function is_template_in_use($id)
    {
        // 1. Check Settings
        $settings_types = [
            'case_taking_qa_template_complaints',
            'case_taking_qa_template_diagnosis',
            'case_taking_qa_template_advice',
            'case_taking_qa_template_personal_history',
            'case_taking_qa_template_family_history',
            'case_taking_qa_template_allergies',
            'case_taking_qa_template_vaccination'
        ];

        $this->db->where_in('name', $settings_types);
        $this->db->where('value', $id);
        $setting = $this->db->get(db_prefix() . 'options')->row();

        if ($setting) {
            return "This template is currently assigned in Case Taking Settings.";
        }

        // 2. Check if questions in this template are used
        $template = $this->get_qa_templates($id);
        if ($template) {
            $questions = json_decode($template->questions, true);
            if (!empty($questions)) {
                foreach ($questions as $q) {
                    $q_text = is_array($q) ? $q['text'] : $q;
                    if ($this->is_question_used($q_text)) {
                        return "Questions from this template are used in existing case takings.";
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get usage count of a template (Number of case takings using its questions)
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

        $result = $this->db->get(db_prefix() . 'case_taking')->row();
        return $result ? (int) $result->count : 0;
    }

    /**
     * Delete Case Taking and return Visit ID
     * @param  int $id Case Taking ID
     * @return int|boolean Visit ID or false
     */
    public function delete_case_taking($id)
    {
        $this->db->where('id', $id);
        $case_taking = $this->db->get(db_prefix() . 'case_taking')->row();

        if ($case_taking) {
            $visit_id = $case_taking->visit_id;

            // Delete Items

            // Delete Case Taking
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'case_taking');

            // Log Activity
            log_activity('Case Taking Deleted [ID: ' . $id . ']');

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
        return $this->db->get(db_prefix() . 'case_taking_custom_fields')->result_array();
    }

    /**
     * Add Custom Field
     * @param array $data
     */
    public function add_custom_field($data)
    {
        $this->db->insert(db_prefix() . 'case_taking_custom_fields', $data);
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
        $this->db->update(db_prefix() . 'case_taking_custom_fields', $data);
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
        $field = $this->db->get(db_prefix() . 'case_taking_custom_fields')->row();
        if ($field && $field->is_system == 1) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'case_taking_custom_fields');
        return $this->db->affected_rows() > 0;
    }
    public function get_field_usage_count($slug)
    {
        // Check if the slug exists in the custom_data JSON string
        // We look for "slug": pattern
        $this->db->like('custom_data', '"' . $slug . '":');
        // We also check for non-empty value if possible, but LIKE pattern implies key existence which is "usage" enough for now.
        // A stricter check would be tough with just LIKE on JSON text.
        return $this->db->count_all_results(db_prefix() . 'case_taking');
    }
}
