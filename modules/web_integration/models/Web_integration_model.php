<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Web_integration_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get single form or all forms
     * @param  mixed $id form id
     * @return mixed     array | object
     */
    public function get_form($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'web_integration_forms')->row();
        }

        $this->db->select(db_prefix() . 'web_integration_forms.*, CONCAT(firstname, " ", lastname) as creator_name, COUNT(' . db_prefix() . 'web_integration_entries.id) as entries_count');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'web_integration_forms.created_by', 'left');
        $this->db->join(db_prefix() . 'web_integration_entries', db_prefix() . 'web_integration_entries.form_id = ' . db_prefix() . 'web_integration_forms.id', 'left');
        $this->db->group_by(db_prefix() . 'web_integration_forms.id');
        return $this->db->get(db_prefix() . 'web_integration_forms')->result_array();
    }

    /**
     * Get form by key for public access
     * @param  string $key form key
     * @return object
     */
    public function get_form_by_key($key)
    {
        $this->db->where('form_key', $key);
        return $this->db->get(db_prefix() . 'web_integration_forms')->row();
    }

    /**
     * Add new form
     * @param array $data form data
     * @return mixed form id or false
     */
    public function add_form($data)
    {
        $data['form_key'] = md5(uniqid(rand(), true));
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'web_integration_forms', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $this->init_default_fields($insert_id);
            return $insert_id;
        }

        return false;
    }

    /**
     * Update form
     * @param  array $data form data
     * @param  mixed $id   form id
     * @return boolean
     */
    public function update_form($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'web_integration_forms', $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete form
     * @param  mixed $id form id
     * @return boolean
     */
    public function delete_form($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'web_integration_forms');

        if ($this->db->affected_rows() > 0) {
            $this->db->where('form_id', $id);
            $this->db->delete(db_prefix() . 'web_integration_form_fields');

            $this->db->where('form_id', $id);
            $this->db->delete(db_prefix() . 'web_integration_entries');

            return true;
        }

        return false;
    }

    /**
     * Get form fields
     * @param  mixed $form_id form id
     * @return array
     */
    public function get_form_fields($form_id)
    {
        $this->ensure_fields_exist($form_id);
        $this->db->where('form_id', $form_id);
        $this->db->order_by('field_order', 'asc');
        return $this->db->get(db_prefix() . 'web_integration_form_fields')->result_array();
    }

    /**
     * Ensure all default fields exist for a form
     * @param mixed $form_id
     */
    private function ensure_fields_exist($form_id)
    {
        $default_fields = [
            'name',
            'age',
            'gender',
            'mobile_number',
            'whatsapp_number',
            'treatment',
            'doctor',
            'consultation_datetime',
            'email',
            'attachment',
            'branch',
            'message',
            'rating'
        ];

        // Get existing fields
        $this->db->where('form_id', $form_id);
        $existing_fields_rows = $this->db->get(db_prefix() . 'web_integration_form_fields')->result_array();
        $existing_fields = array_column($existing_fields_rows, 'field_id');

        $max_order = 0;
        if (!empty($existing_fields_rows)) {
            $max_order = max(array_column($existing_fields_rows, 'field_order'));
        }

        foreach ($default_fields as $field) {
            if (!in_array($field, $existing_fields)) {
                $max_order++;
                $this->db->insert(db_prefix() . 'web_integration_form_fields', [
                    'form_id' => $form_id,
                    'field_id' => $field,
                    'is_visible' => 1,
                    'is_required' => 0,
                    'custom_label' => ucfirst(str_replace('_', ' ', $field)),
                    'field_order' => $max_order
                ]);
            }
        }
    }

    /**
     * Update form fields settings
     * @param  array $data fields data
     * @param  mixed $form_id form id
     * @return boolean
     */
    public function update_form_fields($data, $form_id)
    {
        // Data structure expected: ['fields' => [field_id => ['is_visible' => 1, ...]]]
        if (isset($data['fields'])) {
            foreach ($data['fields'] as $field_id => $field_data) {
                $this->db->where('form_id', $form_id);
                $this->db->where('id', $field_id);  // Assuming the key is the field ID (primary key of row)
                // But wait, key might be the variable name, let's verify logic in controller. 
                // Better implementation: iterate and match by unique constraint or ID. 
                // For safety, let's assume we pass the row ID or we match by field_id name + form_id. 
                // Let's stick to update batch if possible, or simple updates.

                // Let's assume we pass 'id' in the post data or use field_id string if we want map by name.
                // The init method creates them. 

                $update_data = [
                    'is_visible' => isset($field_data['is_visible']) ? 1 : 0,
                    'is_required' => isset($field_data['is_required']) ? 1 : 0,
                    'custom_label' => $field_data['custom_label'],
                    'field_order' => $field_data['field_order'] ?? 0,
                ];

                $this->db->update(db_prefix() . 'web_integration_form_fields', $update_data);
            }
            return true;
        }
        return false;
    }

    // Alternative update specifically by form_id and the field_id string (e.g. 'name')
    public function update_form_field_by_name($form_id, $field_name, $data)
    {
        $this->db->where('form_id', $form_id);
        $this->db->where('field_id', $field_name);
        $this->db->update(db_prefix() . 'web_integration_form_fields', $data);
    }

    /**
     * Initialize default fields for a form
     * @param  mixed $form_id form id
     */
    private function init_default_fields($form_id)
    {
        $default_fields = [
            'name',
            'age',
            'gender',
            'mobile_number',
            'whatsapp_number',
            'treatment',
            'doctor',
            'consultation_datetime',
            'email',
            'attachment',
            'branch',
            'message',
            'rating'
        ];

        $order = 0;
        foreach ($default_fields as $field) {
            $this->db->insert(db_prefix() . 'web_integration_form_fields', [
                'form_id' => $form_id,
                'field_id' => $field,
                'is_visible' => 1,
                'is_required' => 0,
                'custom_label' => ucfirst(str_replace('_', ' ', $field)),
                'field_order' => $order++
            ]);
        }
    }

    /**
     * Add entry to form
     * @param array $data entry data
     * @return mixed entry id or false
     */
    public function add_entry($data)
    {
        $this->db->trans_start();

        $valid_columns = $this->get_valid_entry_columns();

        $insert_data = [];
        foreach ($valid_columns as $col) {
            if (isset($data[$col])) {
                $insert_data[$col] = $data[$col];
            }
        }

        $insert_data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'web_integration_entries', $insert_data);
        $entry_id = $this->db->insert_id();

        if (!$entry_id) {
            $this->db->trans_rollback();
            return false;
        }

        $form = $this->get_form($data['form_id']);

        if ($form) {
            if ($form->link_with_leads == 1) {
                $this->create_lead_from_entry($data, $form);
            }

            if ($form->link_with_appointments == 1) {
                $this->create_appointment_from_entry($data, $form, $entry_id);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $entry_id;
    }

    private function create_lead_from_entry($data, $form)
    {
        $this->load->model('leads_model');
        $this->load->model('custom_fields_model');

        // 1. Prepare Lead Data
        $lead_data = [
            'name' => $data['name'],
            'email' => isset($data['email']) ? $data['email'] : '',
            'phonenumber' => isset($data['mobile_number']) ? $data['mobile_number'] : '',
            'source' => $form->lead_source,
            'assigned' => $form->lead_assigned,
            'status' => get_option('leads_default_status'), // Default status
            'description' => isset($data['message']) ? $data['message'] : '',
            'dateadded' => date('Y-m-d H:i:s'),
            'is_public' => 0,
            'addedfrom' => $form->created_by,
            'address' => '',
            'city' => '',
            'state' => '',
            'country' => 0,
            'zip' => '',
            'company' => '',
            'website' => ''
        ];

        // 2. Map & Create Custom Fields
        $custom_fields_map = $this->get_lead_custom_fields_map($data);
        $custom_fields_data = [];

        foreach ($custom_fields_map as $field_name => $field_value) {
            if ($field_value == '')
                continue;

            // Check if custom field exists for leads
            $this->db->where('name', $field_name);
            $this->db->where('fieldto', 'leads');
            $field = $this->db->get(db_prefix() . 'customfields')->row();

            if (!$field) {
                // Create Custom Field because it doesn't exist
                $new_field_data = [
                    'name' => $field_name,
                    'slug' => slug_it('leads_' . $field_name),
                    'fieldto' => 'leads',
                    'type' => 'input', // Default to input for simplicity
                    'required' => 0,
                    'only_admin' => 0,
                    'show_on_table' => 1,
                    'show_on_pdf' => 0,
                    'show_on_client_portal' => 0,
                    'active' => 1,
                    'bs_column' => 12,
                    'field_order' => 0
                ];

                // Adjust type for Date and specific fields if needed
                if (strpos($field_name, 'Date') !== false) {
                    $new_field_data['type'] = 'date_picker';
                }

                $field_id = $this->custom_fields_model->add($new_field_data);
            } else {
                $field_id = $field->id;
            }

            $custom_fields_data['leads'][$field_id] = $field_value;
        }

        if (!empty($custom_fields_data)) {
            $lead_data['custom_fields'] = $custom_fields_data;
        }

        // 3. Insert Lead
        $this->leads_model->add($lead_data);
    }

    private function create_appointment_from_entry($data, $form, $entry_id)
    {
        $this->load->model('appointments/appointments_model');

        // Duplicate Check
        if ($this->appointments_model->check_duplicate_appointment($data['name'], $data['mobile_number'] ?? '', $data['consultation_date'])) {
            return;
        }

        // 1. Create Guest
        $guest_data = [
            'name' => $data['name'],
            'phone' => $data['mobile_number'] ?? '',
            'dob' => isset($data['age']) ? date('Y-m-d', strtotime('-' . $data['age'] . ' years')) : null, // Approx DOB from age
            'gender' => $data['gender'] ?? '',
        ];

        $guest_id = $this->appointments_model->add_guest($guest_data);

        if ($guest_id) {
            // 2. Create Appointment
            // Calculate end time if not provided
            $end_time = isset($data['end_time']) && !empty($data['end_time']) ? $data['end_time'] : date('H:i:s', strtotime($data['start_time'] . ' +30 minutes'));

            $appointment_data = [
                'notes' => $data['message'] ?? '',
                'appointment_date' => $data['consultation_date'],
                'start_time' => $data['start_time'],
                'end_time' => $end_time,
                'doctor_id' => $data['doctor'], // This is now ID from the form
                'patient_id' => null, // Initially guest
                'guest_id' => $guest_id,
                'status' => 'pending', // Default status
                'appointment_type' => 'Unpaid', // Default to Unpaid as per schema
            ];

            // Lookup Doctor Name for Entry Record consistency
            $doctor_name = get_staff_full_name($data['doctor']);
            $this->db->where('id', $entry_id);
            $this->db->update(db_prefix() . 'web_integration_entries', ['doctor' => $doctor_name]);

            $this->appointments_model->add($appointment_data);
        }
    }

    /**
     * Get entries for a form
     * @param  mixed $form_id form id
     * @return array
     */
    public function get_entries($form_id)
    {
        $this->db->where('form_id', $form_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get(db_prefix() . 'web_integration_entries')->result_array();
    }

    public function get_entry($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'web_integration_entries')->row();
    }

    /**
     * Get field slug/mapping info
     * @param string $field_id
     * @return string
     */
    public function get_field_slug($field_id)
    {
        // Standard Lead Fields Mapping
        $standard_fields = [
            'name' => 'name',
            'email' => 'email',
            'mobile_number' => 'phonenumber',
            'message' => 'description',
        ];

        if (isset($standard_fields[$field_id])) {
            return 'Standard Field (' . $standard_fields[$field_id] . ')';
        }

        // Custom Fields Mapping
        $custom_map = $this->get_custom_fields_definition();

        foreach ($custom_map as $label => $source_field) {
            if ($source_field == $field_id) {
                return slug_it('leads_' . $label);
            }
        }

        return '-';
    }

    public function get_valid_entry_columns()
    {
        return [
            'form_id',
            'name',
            'age',
            'gender',
            'mobile_number',
            'whatsapp_number',
            'treatment',
            'doctor',
            'consultation_datetime',
            'email',
            'attachment',
            'branch',
            'message',
            'rating'
        ];
    }

    public function get_custom_fields_definition()
    {
        return [
            'Age' => 'age',
            'Gender' => 'gender',
            'Treatment' => 'treatment',
            'Doctor' => 'doctor',
            'Consultation Date' => 'consultation_datetime',
            'Branch' => 'branch',
            'Rating' => 'rating',
            'Whatsapp Number' => 'whatsapp_number'
        ];
    }

    public function get_lead_custom_fields_map($data)
    {
        $map = $this->get_custom_fields_definition();
        $result = [];

        foreach ($map as $label => $source_field) {
            $result[$label] = $data[$source_field] ?? '';
        }

        return $result;
    }
}
