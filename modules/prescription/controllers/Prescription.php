<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Prescription extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('prescription_model');

        $data['title'] = 'Prescriptions';

        $filters = [
            'status' => $this->input->get('status') ? $this->input->get('status') : 'all', // Default to all
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date'),
            'search' => $this->input->get('search')
        ];

        // Apply Default Date if NO date filter is present
        if (empty($filters['from_date']) && empty($filters['to_date'])) {
            $default_date = get_option('prescription_default_date_filter');
            if ($default_date == 'today') {
                $filters['from_date'] = date('Y-m-d');
                $filters['to_date'] = date('Y-m-d');
            } elseif ($default_date == 'yesterday') {
                $filters['from_date'] = date('Y-m-d', strtotime('-1 day'));
                $filters['to_date'] = date('Y-m-d', strtotime('-1 day'));
            } elseif ($default_date == 'yesterday_today') {
                $filters['from_date'] = date('Y-m-d', strtotime('-1 day'));
                $filters['to_date'] = date('Y-m-d');
            }
        }



        $data['filters'] = $filters;
        $data['consultations'] = $this->prescription_model->get_consultations($filters);
        $data['status_counts'] = $this->prescription_model->get_status_counts($filters);

        $this->load->view('prescription/manage', $data);
    }

    public function change_status($id, $status)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->model('prescription_model');
        $success = $this->prescription_model->update_status($id, $status);

        echo json_encode(['success' => $success]);
    }

    public function create($id = '')
    {
        if (!has_permission('prescription', '', 'create')) {
            access_denied('prescription');
        }

        $this->load->model('prescription_model');

        // Fetch consultation details
        $test = $this->db->where('id', $id)->get(db_prefix() . 'patient_tests')->row();

        if (!$test) {
            show_404();
        }

        $data['consultation'] = $test;
        $data['consultation_id'] = $id;

        // Fetch Patient Data
        $this->load->model('patients/patients_model');
        $data['patient'] = $this->patients_model->get($test->patient_id);

        // Fetch Visit Data
        // Joined by invoice_id in model, so let's try to get visit directly via invoice_id from test
        $data['visit'] = $this->db->where('invoice_id', $test->invoice_id)->get(db_prefix() . 'visits')->row();

        // Fetch Master Data
        $this->load->model('master_data/master_data_model');
        $data['medicine_types'] = $this->master_data_model->get_medicine_types();
        $data['medicine_doses'] = $this->master_data_model->get_medicine_doses();
        $data['medicine_frequencies'] = $this->master_data_model->get_medicine_frequencies();
        $data['medicine_durations'] = $this->master_data_model->get_medicine_durations();
        $data['medicine_whens'] = $this->master_data_model->get_medicine_whens();

        // Fetch Medicines
        $data['medicines'] = $this->prescription_model->get_medicines();

        // Fetch Test Items
        $data['test_items'] = $this->prescription_model->get_test_items();

        // Fetch Custom Fields (Active Only for New/Edit)
        $custom_fields = $this->prescription_model->get_custom_fields(true);
        $data['custom_fields'] = $custom_fields;

        // Fetch Master Data for Autocomplete & QA Questions
        $data['master_data'] = [];
        $data['qa_questions'] = [];

        foreach ($custom_fields as $field) {
            $slug = $field['slug'];

            // Master Data
            $data['master_data'][$slug] = $this->prescription_model->get_master_data($slug);

            // QA Questions
            if ($field['type'] == 'qas') {
                // For system fields, we used option.
                // For custom fields, we used option too in settings logic saving.
                $template_id = get_option('prescription_qa_template_' . $slug);
                if ($template_id) {
                    $template = $this->prescription_model->get_qa_templates($template_id);
                    if ($template) {
                        $data['qa_questions'][$slug] = json_decode($template->questions, true);
                    }
                }
            }
        }

        // Prepare Input Settings (Global Defaults for New)
        $input_settings = [];
        $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals', 'tests_requested', 'next_visit', 'medicine'];
        foreach ($fields as $f) {
            if (!in_array($f, ['vitals', 'tests_requested', 'next_visit', 'medicine'])) {
                $input_settings[$f . '_type'] = get_option('prescription_input_type_' . $f);
            } else {
                // For these extras, populate the show key to match what we expect in view
                $input_settings['prescription_show_' . $f] = get_option('prescription_show_' . $f);
            }
            $input_settings[$f . '_mandatory'] = get_option('prescription_mandatory_' . $f);
        }
        $data['input_settings'] = $input_settings;

        // Dynamic Title
        $visit_code = isset($data['visit']) ? $data['visit']->visit_code : 'N/A';
        $data['title'] = $data['patient']->title . ' ' . $data['patient']->full_name . ' | MR. No: ' . $data['patient']->mr_number . ' | Visit-ID: ' . $visit_code . ' | ' . $data['patient']->age . ' ' . $data['patient']->age_unit;

        $this->load->view('prescription/create', $data);
    }

    public function save()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            // Handle QA Inputs
            if (isset($data['qa_input']) && is_array($data['qa_input'])) {
                foreach ($data['qa_input'] as $field => $answers) {
                    $json_data = [];
                    foreach ($answers as $item) {
                        $json_data[] = ['q' => $item['q'], 'a' => isset($item['a']) ? $item['a'] : ''];
                    }
                    $data[$field] = json_encode($json_data);
                }
                unset($data['qa_input']);
            }

            // Capture and unset save_type
            $save_type = isset($data['save_type']) ? $data['save_type'] : 'completed';
            if (isset($data['save_type']))
                unset($data['save_type']);

            // Snapshot Input Config (Version 2)
            $this->load->model('prescription_model');
            $current_custom_fields = $this->prescription_model->get_custom_fields();

            $snapshot_settings = [];
            $extra_fields = ['prescription_show_vitals', 'prescription_show_tests_requested', 'prescription_show_next_visit', 'prescription_show_medicine'];
            foreach ($extra_fields as $f) {
                $snapshot_settings[$f] = get_option($f);
                $mandatory_key = str_replace('_show_', '_mandatory_', $f);
                $snapshot_settings[$mandatory_key] = get_option($mandatory_key);
            }
            // Vitals type special case if needed, or other globals
            $snapshot_settings['prescription_input_type_vitals'] = get_option('prescription_input_type_vitals');

            $snapshot = [
                'version' => 2,
                'fields' => $current_custom_fields,
                'settings' => $snapshot_settings
            ];
            $data['input_config'] = json_encode($snapshot);

            $this->load->model('prescription_model');
            $id = $this->prescription_model->add_prescription($data);

            if ($id) {
                // Determined Status based on button click
                $status = ($this->input->post('save_type') == 'draft') ? 'Processing' : 'Completed';

                // If completed, update the prescription with date_completed
                if ($status == 'Completed') {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'prescriptions', ['date_completed' => date('Y-m-d H:i:s')]);
                }

                // Update status 
                if (isset($data['consultation_id'])) {
                    $this->prescription_model->update_status($data['consultation_id'], $status);
                }

                set_alert('success', _l('added_successfully', 'Prescription'));
                redirect(admin_url('prescription'));
            }
        }
        redirect(admin_url('prescription'));
    }

    public function autosave()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data = $this->input->post();
        $this->load->model('prescription_model');

        // Handle QA Inputs - Same logic as save
        if (isset($data['qa_input']) && is_array($data['qa_input'])) {
            foreach ($data['qa_input'] as $field => $answers) {
                $json_data = [];
                foreach ($answers as $item) {
                    $json_data[] = ['q' => $item['q'], 'a' => isset($item['a']) ? $item['a'] : ''];
                }
                $data[$field] = json_encode($json_data);
            }
            unset($data['qa_input']);
        }

        // Clean up data
        if (isset($data['save_type']))
            unset($data['save_type']);
        if (isset($data['csrf_token_name']))
            unset($data['csrf_token_name']);

        $id = isset($data['id']) ? $data['id'] : '';
        if (isset($data['id']))
            unset($data['id']); // Remove ID from update data array

        $response = [
            'success' => false,
            'csrf_token_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ];

        if (!empty($id)) {
            // Update Existing
            $success = $this->prescription_model->update_prescription($id, $data);
            if ($success) {
                // Since we made a DB write, the hash might have regenerated again? 
                // Actually CI regenerates on OUTPUT or init. Calling get_csrf_hash() gets the CURRENT (new) hash to be sent.
                $response['success'] = true;
                $response['id'] = $id;
                $response['action'] = 'update';
            }
        } else {
            // Create New
            // Snapshot Input Config (Same as Save)
            $snapshot = [];
            $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals', 'tests_requested', 'next_visit', 'medicine'];
            foreach ($fields as $f) {
                if (!in_array($f, ['vitals', 'tests_requested', 'next_visit', 'medicine'])) {
                    $snapshot[$f . '_type'] = get_option('prescription_input_type_' . $f);
                }
                $snapshot[$f . '_mandatory'] = get_option('prescription_mandatory_' . $f);
            }
            $data['input_config'] = json_encode($snapshot);

            $new_id = $this->prescription_model->add_prescription($data);
            if ($new_id) {
                // Set Status to Processing (Draft)
                if (isset($data['consultation_id'])) {
                    $this->prescription_model->update_status($data['consultation_id'], 'Processing');
                }
                $response['success'] = true;
                $response['id'] = $new_id;
                $response['action'] = 'create';
            }
        }

        echo json_encode($response);
    }

    public function edit($id = '')
    {
        if (!has_permission('prescription', '', 'edit')) {
            access_denied('prescription');
        }

        $this->load->model('prescription_model');

        $prescription = $this->prescription_model->get_prescription($id);

        if (!$prescription) {
            show_404();
        }

        $data['prescription'] = $prescription;
        $data['consultation_id'] = '';

        // Fetch Patient Data
        $this->load->model('patients/patients_model');
        $data['patient'] = $this->patients_model->get($prescription->patient_id);

        // Fetch Visit Data
        $data['visit'] = $this->db->where('id', $prescription->visit_id)->get(db_prefix() . 'visits')->row();

        // 1. Snapshot / Default Loading
        $input_settings = [];
        $custom_fields = [];
        $snapshot_data = [];

        // Check for Snapshot (V2 >> V1)
        if (!empty($prescription->input_config)) {
            $snapshot_data = json_decode($prescription->input_config, true);
        }

        // Global Defaults (Fallback)
        $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals', 'tests_requested', 'next_visit', 'medicine'];
        // Note: For custom fields, we fetch from DB first, then override if snapshot exists.
        $db_custom_fields = $this->prescription_model->get_custom_fields();


        if (!empty($snapshot_data) && isset($snapshot_data['version']) && $snapshot_data['version'] == 2) {
            // --- Version 2 Snapshot: Full Restoration ---
            // "fields" contains the array of custom fields settings at that time
            // "settings" contains other toggles
            $custom_fields = $snapshot_data['fields'];
            $input_settings = $snapshot_data['settings'];
        } else {
            // --- Version 1 or No Snapshot: Legacy/Global Mix ---

            // A. Prepare Input Settings (Vitals, Tests, etc.)
            foreach ($fields as $f) {
                // Type
                if (!in_array($f, ['vitals', 'tests_requested', 'next_visit', 'medicine'])) {
                    $key_type = $f . '_type';
                    // Snapshot V1 priority or Global
                    if (isset($snapshot_data[$key_type])) {
                        $input_settings[$key_type] = $snapshot_data[$key_type];
                    } else {
                        $input_settings[$key_type] = get_option('prescription_input_type_' . $f);
                    }
                }

                // Mandatory
                $key_mandatory = $f . '_mandatory';
                if (isset($snapshot_data[$key_mandatory])) {
                    $input_settings[$key_mandatory] = $snapshot_data[$key_mandatory];
                } else {
                    $input_settings[$key_mandatory] = get_option('prescription_mandatory_' . $f);
                }
            }

            // Other settings (not in loop loop)
            $simple_extras = ['vitals', 'tests_requested', 'next_visit', 'medicine'];
            foreach ($simple_extras as $extra) {
                $key = 'prescription_show_' . $extra;
                if (isset($snapshot_data[$key])) {
                    $input_settings[$key] = $snapshot_data[$key];
                } else {
                    $input_settings[$key] = get_option($key);
                }

                // Also Mandatory Settings
                $m_key = 'prescription_mandatory_' . $extra; // Note: stored as prescription_mandatory_medicine etc.
                if (isset($snapshot_data[$m_key])) {
                    $input_settings[$extra . '_mandatory'] = $snapshot_data[$m_key];
                } else {
                    $input_settings[$extra . '_mandatory'] = get_option($m_key);
                }
            }
            // Vitals Type special handling
            if (isset($snapshot_data['prescription_input_type_vitals'])) {
                $input_settings['prescription_input_type_vitals'] = $snapshot_data['prescription_input_type_vitals'];
            } else {
                $input_settings['prescription_input_type_vitals'] = get_option('prescription_input_type_vitals');
            }


            // B. Hydrate Custom Fields from DB but Override with Calculated Settings
            // This ensures that if we are editing an old prescription (V1), we try to honor its specific settings (if they were saved flat).
            // But mostly V1 just saved "complaints_type" etc.
            // So we take DB fields, and inject the type/mandatory from our $input_settings we just resolved.

            $custom_fields = $db_custom_fields;
            foreach ($custom_fields as &$field) {
                $slug = $field['slug'];
                // Override Type
                if (isset($input_settings[$slug . '_type'])) {
                    $field['type'] = $input_settings[$slug . '_type'];
                }
                // Override Mandatory
                if (isset($input_settings[$slug . '_mandatory'])) {
                    $field['mandatory'] = $input_settings[$slug . '_mandatory'];
                }

                // Note: Is_Active? V1 didn't really snapshot is_active well, usually trusted global.
                // But let's trust DB for availability.
            }
        }

        $data['input_settings'] = $input_settings;
        $data['custom_fields'] = $custom_fields;

        // Check if consultation is completed
        $data['is_completed'] = false;
        if ($data['visit'] && $data['visit']->invoice_id) {
            $this->db->where('invoice_id', $data['visit']->invoice_id);
            $this->db->where('status', 'Completed');
            $completed_test = $this->db->get(db_prefix() . 'patient_tests')->row();
            if ($completed_test) {
                $data['is_completed'] = true;
            }
        }

        // Mock consultation object for view compatibility
        $data['consultation'] = (object) ['patient_id' => $prescription->patient_id];

        // Fetch Master Data
        $this->load->model('master_data/master_data_model');
        $data['medicine_types'] = $this->master_data_model->get_medicine_types();
        $data['medicine_doses'] = $this->master_data_model->get_medicine_doses();
        $data['medicine_frequencies'] = $this->master_data_model->get_medicine_frequencies();
        $data['medicine_durations'] = $this->master_data_model->get_medicine_durations();
        $data['medicine_whens'] = $this->master_data_model->get_medicine_whens();

        // Fetch Medicines
        $data['medicines'] = $this->prescription_model->get_medicines();

        // Fetch Test Items
        $data['test_items'] = $this->prescription_model->get_test_items();


        // Fetch QA Questions
        // We need to re-loop custom_fields to find QA types and load templates
        $data['qa_questions'] = [];
        foreach ($custom_fields as $field) {
            if ($field['type'] == 'qas') {
                $slug = $field['slug'];
                // Template logic:
                // For V2: The template questions themselves are NOT snapshotted in inputs (usually), 
                // but the template ID is usually global setting. 
                // Wait! If I change a template, old prescriptions might break if questions change?
                // The current system stores QA answers as JSON [q, a]. So it renders what was saved (lines 227 create.php).
                // BUT lines 240 create.php loop through `qa_questions` (the template questions).
                // So if template changed, we might see new questions or miss old ones.
                // This is a known limitation unless we snapshot the *Questions Template* too.
                // For now, we load current template.

                $template_id = get_option('prescription_qa_template_' . $slug);
                if ($template_id) {
                    $template = $this->prescription_model->get_qa_templates($template_id);
                    if ($template) {
                        $data['qa_questions'][$slug] = json_decode($template->questions, true);
                    }
                }
            }
        }

        // Dynamic Title
        $data['title'] = $data['patient']->title . ' ' . $data['patient']->full_name . ' | MR. No: ' . $data['patient']->mr_number . ' | Visit-ID: ' . $data['visit']->visit_code . ' | ' . $data['patient']->age . ' ' . $data['patient']->age_unit;

        $this->load->view('prescription/create', $data);
    }

    public function view($id = '')
    {
        if (!has_permission('prescription', '', 'view')) {
            access_denied('prescription');
        }

        $this->load->model('prescription_model');

        $prescription = $this->prescription_model->get_prescription($id);

        if (!$prescription) {
            show_404();
        }

        $data['prescription'] = $prescription;
        $data['consultation_id'] = '';
        $data['read_only'] = true; // Flag for View Only Mode

        // Fetch Patient Data
        $this->load->model('patients/patients_model');
        $data['patient'] = $this->patients_model->get($prescription->patient_id);

        // Fetch Visit Data
        $data['visit'] = $this->db->where('id', $prescription->visit_id)->get(db_prefix() . 'visits')->row();

        // 1. Snapshot / Default Loading
        $input_settings = [];
        $custom_fields = [];
        $snapshot_data = [];

        // Check for Snapshot (V2 >> V1)
        if (!empty($prescription->input_config)) {
            $snapshot_data = json_decode($prescription->input_config, true);
        }

        // Global Defaults (Fallback)
        $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals', 'tests_requested', 'next_visit', 'medicine'];
        // Note: For custom fields, we fetch from DB first, then override if snapshot exists.
        $db_custom_fields = $this->prescription_model->get_custom_fields();


        if (!empty($snapshot_data) && isset($snapshot_data['version']) && $snapshot_data['version'] == 2) {
            // --- Version 2 Snapshot: Full Restoration ---
            // "fields" contains the array of custom fields settings at that time
            // "settings" contains other toggles
            $custom_fields = $snapshot_data['fields'];
            $input_settings = $snapshot_data['settings'];
        } else {
            // --- Version 1 or No Snapshot: Legacy/Global Mix ---

            // A. Prepare Input Settings (Vitals, Tests, etc.)
            foreach ($fields as $f) {
                // Type
                if (!in_array($f, ['vitals', 'tests_requested', 'next_visit', 'medicine'])) {
                    $key_type = $f . '_type';
                    // Snapshot V1 priority or Global
                    if (isset($snapshot_data[$key_type])) {
                        $input_settings[$key_type] = $snapshot_data[$key_type];
                    } else {
                        $input_settings[$key_type] = get_option('prescription_input_type_' . $f);
                    }
                }

                // Mandatory
                $key_mandatory = $f . '_mandatory';
                if (isset($snapshot_data[$key_mandatory])) {
                    $input_settings[$key_mandatory] = $snapshot_data[$key_mandatory];
                } else {
                    $input_settings[$key_mandatory] = get_option('prescription_mandatory_' . $f);
                }
            }

            // Other settings
            $simple_extras = ['vitals', 'tests_requested', 'next_visit', 'medicine'];
            foreach ($simple_extras as $extra) {
                $key = 'prescription_show_' . $extra;
                if (isset($snapshot_data[$key])) {
                    $input_settings[$key] = $snapshot_data[$key];
                } else {
                    $input_settings[$key] = get_option($key);
                }

                // Also Mandatory Settings
                $m_key = 'prescription_mandatory_' . $extra;
                if (isset($snapshot_data[$m_key])) {
                    $input_settings[$extra . '_mandatory'] = $snapshot_data[$m_key];
                } else {
                    $input_settings[$extra . '_mandatory'] = get_option($m_key);
                }
            }
            // Vitals Type special handling
            if (isset($snapshot_data['prescription_input_type_vitals'])) {
                $input_settings['prescription_input_type_vitals'] = $snapshot_data['prescription_input_type_vitals'];
            } else {
                $input_settings['prescription_input_type_vitals'] = get_option('prescription_input_type_vitals');
            }


            // B. Hydrate Custom Fields from DB but Override with Calculated Settings
            $custom_fields = $db_custom_fields;
            foreach ($custom_fields as &$field) {
                $slug = $field['slug'];
                // Override Type
                if (isset($input_settings[$slug . '_type'])) {
                    $field['type'] = $input_settings[$slug . '_type'];
                }
                // Override Mandatory
                if (isset($input_settings[$slug . '_mandatory'])) {
                    $field['mandatory'] = $input_settings[$slug . '_mandatory'];
                }
            }
        }

        $data['input_settings'] = $input_settings;
        $data['custom_fields'] = $custom_fields;


        // Fetch QA Questions (Hydrated from Snapshot Settings)
        $data['qa_questions'] = [];
        foreach ($custom_fields as $field) {
            if ($field['type'] == 'qas') {
                $slug = $field['slug'];
                $template_id = get_option('prescription_qa_template_' . $slug);
                if ($template_id) {
                    $template = $this->prescription_model->get_qa_templates($template_id);
                    if ($template) {
                        $data['qa_questions'][$slug] = json_decode($template->questions, true);
                    }
                }
            }
        }

        // Mock consultation object for view compatibility
        $data['consultation'] = (object) ['patient_id' => $prescription->patient_id];

        // Fetch Master Data
        $this->load->model('master_data/master_data_model');
        $data['medicine_types'] = $this->master_data_model->get_medicine_types();
        $data['medicine_doses'] = $this->master_data_model->get_medicine_doses();
        $data['medicine_frequencies'] = $this->master_data_model->get_medicine_frequencies();
        $data['medicine_durations'] = $this->master_data_model->get_medicine_durations();
        $data['medicine_whens'] = $this->master_data_model->get_medicine_whens();

        // Fetch Medicines
        $data['medicines'] = $this->prescription_model->get_medicines();

        // Fetch Test Items
        $data['test_items'] = $this->prescription_model->get_test_items();

        // Dynamic Title
        $data['title'] = $data['patient']->title . ' ' . $data['patient']->full_name . ' | MR. No: ' . $data['patient']->mr_number . ' | Visit-ID: ' . $data['visit']->visit_code . ' | ' . $data['patient']->age . ' ' . $data['patient']->age_unit;

        $this->load->view('prescription/create', $data);
    }

    public function update($id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            // Handle QA Inputs
            if (isset($data['qa_input']) && is_array($data['qa_input'])) {
                foreach ($data['qa_input'] as $field => $answers) {
                    $json_data = [];
                    foreach ($answers as $item) {
                        $json_data[] = ['q' => $item['q'] ?? '', 'a' => $item['a'] ?? ''];
                    }
                    $data[$field] = json_encode($json_data);
                }
                unset($data['qa_input']);
            }

            // Capture and unset save_type
            $save_type = isset($data['save_type']) ? $data['save_type'] : 'completed';
            if (isset($data['save_type']))
                unset($data['save_type']);

            $this->load->model('prescription_model');

            // Legacy Migration: If input_config is empty, snapshot current globals to lock them in.
            $existing_prescription = $this->prescription_model->get_prescription($id);
            if ($existing_prescription && empty($existing_prescription->input_config)) {
                $snapshot = [];
                $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals', 'tests_requested', 'next_visit', 'medicine'];
                foreach ($fields as $f) {
                    if (!in_array($f, ['vitals', 'tests_requested', 'next_visit', 'medicine'])) {
                        $snapshot[$f . '_type'] = get_option('prescription_input_type_' . $f);
                    }
                    $snapshot[$f . '_mandatory'] = get_option('prescription_mandatory_' . $f);
                }
                $data['input_config'] = json_encode($snapshot);
            }

            $success = $this->prescription_model->update_prescription($id, $data);

            if ($success) {
                // Determined Status based on button click
                $status = ($save_type == 'draft') ? 'Processing' : 'Completed';

                // Ensure status is marked accordingly (retrieving via Visit -> Invoice -> Patient Tests)
                // 1. Get Prescription to find Visit ID
                $prescription = $existing_prescription; // Use cached object
                if ($prescription && $prescription->visit_id) {
                    // 2. Get Visit to find Invoice ID
                    $visit = $this->db->where('id', $prescription->visit_id)->get(db_prefix() . 'visits')->row();
                    if ($visit && $visit->invoice_id) {
                        // 3. Update Status of all tests in this invoice
                        $this->db->select('id');
                        $this->db->where('invoice_id', $visit->invoice_id);
                        $tests = $this->db->get(db_prefix() . 'patient_tests')->result();

                        foreach ($tests as $test) {
                            $this->prescription_model->update_status($test->id, $status);
                        }
                    }
                }

                set_alert('success', _l('updated_successfully', 'Prescription'));
            }
        }
        redirect(admin_url('prescription'));
    }

    public function settings()
    {
        $this->load->model('prescription_model');

        // Check permission if needed, for now just allow or check 'view'
        if (!has_permission('prescription', '', 'view')) {
            access_denied('Prescription Settings');
        }

        if ($this->input->post()) {
            $group = $this->input->post('settings_group');

            if ($group == 'filters') {
                update_option('prescription_default_date_filter', $this->input->post('prescription_default_date_filter'));
            } elseif ($group == 'hide_show') {
                $opts = [
                    'prescription_show_date_filter',
                ];
                foreach ($opts as $opt) {
                    $val = $this->input->post($opt) ? '1' : '0';
                    update_option($opt, $val);
                }
            } elseif ($group == 'digital_prescription') {
                // General Options
                $opts = [
                    'prescription_mode_type',
                    'prescription_edit_timeout',
                    'prescription_enable_autosave',
                ];
                foreach ($opts as $opt) {
                    if ($opt == 'prescription_edit_timeout' || $opt == 'prescription_mode_type') {
                        update_option($opt, $this->input->post($opt));
                    } else {
                        $val = $this->input->post($opt) ? '1' : '0';
                        update_option($opt, $val);
                    }
                }

                // Field Configs (Unified System & Custom)
                $custom_fields = $this->prescription_model->get_custom_fields();
                foreach ($custom_fields as $field) {
                    $slug = $field['slug'];
                    $id = $field['id'];

                    // Inputs (Type, QAs)
                    // Note: System fields stored types in options, custom fields in table.
                    // To unify, we should now store everything in table.
                    // But for backward compatibility/safety, let's update table ALWAYS.

                    // Capture Array Inputs
                    $field_orders = $this->input->post('field_order');
                    $types = $this->input->post('type');
                    $mandatories = $this->input->post('mandatory');
                    $is_actives = $this->input->post('is_active');

                    $update_data = [];

                    // Order
                    if (isset($field_orders[$id])) {
                        $update_data['field_order'] = $field_orders[$id];
                    }

                    // Visibility
                    $update_data['is_active'] = (isset($is_actives[$id]) && $is_actives[$id] == 1) ? 1 : 0;

                    // Mandatory
                    $update_data['mandatory'] = (isset($mandatories[$id]) && $mandatories[$id] == 1) ? 1 : 0;

                    // Type
                    if (isset($types[$id])) {
                        $update_data['type'] = $types[$id];
                    }

                    // QA Template
                    // We store QA template ID in comparison to the slug. 
                    // Since specific QA table doesn't exist linked to field ID, we can use options or add column.
                    // Original code: get_option('prescription_qa_template_' . $field)
                    // Let's keep using options for QA Template mapping for now as it's not strictly "field config" but "content config".
                    // OR add `qa_template_id` to `prescription_custom_fields`? 
                    // Let's stick to options for QA template to avoid schema drift, but it's cleaner to add it.
                    // BUT, the custom field is dynamic. We can't use static option names for dynamic fields efficiently (w/o IDs).
                    // Actually, using slug in option name is fine: `prescription_qa_template_custom_slug`.

                    $template_key = 'prescription_qa_template_' . $slug;
                    if (isset($update_data['type']) && $update_data['type'] == 'qas') {
                        update_option($template_key, $this->input->post($template_key));
                    }

                    $this->prescription_model->update_custom_field($id, $update_data);

                    // Also update legacy options for system fields to ensure other parts (e.g. detailed views if not updated) don't break?
                    // I will be updating Views to use the Table, so options are redundant for these fields. 
                    // But safe to keep them in sync for system fields.
                    if ($field['is_system'] == 1) {
                        update_option('prescription_show_' . $slug, $update_data['is_active']);
                        update_option('prescription_mandatory_' . $slug, $update_data['mandatory']);
                        if (isset($update_data['type'])) {
                            update_option('prescription_input_type_' . $slug, $update_data['type']);
                        }
                    }
                }

                // Other non-list settings (Vitals, Tests, Next Visit, Medicine)
                // These are NOT in custom_fields table yet as per my migration (I only added the 7 text fields).
                // Let's keep them as Options.
                $other_opts = [
                    'prescription_show_vitals',
                    'prescription_show_tests_requested',
                    'prescription_show_next_visit',
                    'prescription_show_medicine',
                ];
                foreach ($other_opts as $opt) {
                    $val = $this->input->post($opt) ? '1' : '0';
                    update_option($opt, $val);

                    $mandatory_key = str_replace('_show_', '_mandatory_', $opt);
                    $m_val = $this->input->post($mandatory_key) ? '1' : '0';
                    update_option($mandatory_key, $m_val);

                    // Vitals input type?
                    if ($opt == 'prescription_show_vitals') {
                        update_option('prescription_input_type_vitals', $this->input->post('prescription_input_type_vitals'));
                    }
                }
            } elseif ($group == 'prescriptions_table') {
                $opts = ['prescription_show_options_column'];
                foreach ($opts as $opt) {
                    $val = $this->input->post($opt) ? '1' : '0';
                    update_option($opt, $val);
                }
            } elseif ($group == 'prescription_qas_save') {
                $questions_input = $this->input->post('questions');

                // Validate Questions
                if (!empty($questions_input) && is_array($questions_input)) {
                    // Ensure indexes are re-arranged if disjointed (though php input usually handles this, JS removal might leave gaps if not careful)
                    // But strictly speaking, $this->input->post('questions') creates a new array based on submitted keys. 
                    // We just need the values.
                    $questions_input = array_values($questions_input);
                }

                $template_data = [
                    'name' => $this->input->post('name'),
                    'questions' => json_encode($questions_input) // Store as JSON [q1, q2]
                ];
                $id = $this->input->post('id');
                $this->load->model('prescription_model');

                if ($id) {
                    // Safety Check: Detect Question Deletion logic
                    $existing_template = $this->prescription_model->get_qa_templates($id);
                    if ($existing_template) {
                        $old_questions = json_decode($existing_template->questions, true);
                        if (!empty($old_questions)) {
                            // Extract just the text from new questions for comparison
                            $new_question_texts = [];
                            if (!empty($questions_input)) {
                                foreach ($questions_input as $q) {
                                    $new_question_texts[] = is_array($q) ? $q['text'] : $q;
                                }
                            }

                            foreach ($old_questions as $old_q) {
                                $old_text = is_array($old_q) ? $old_q['text'] : $old_q;

                                // scan if $old_text is present in $new_question_texts
                                if (!in_array($old_text, $new_question_texts)) {
                                    // Defines as "Deleted" from the list
                                    if ($this->prescription_model->is_question_used($old_text)) {
                                        set_alert('warning', 'Cannot delete question "' . $old_text . '" because it is used in existing prescriptions.');
                                        redirect(admin_url('prescription/settings?group=prescription_qas'));
                                    }
                                }
                            }
                        }
                    }

                    $this->prescription_model->update_qa_template($template_data, $id);
                    set_alert('success', 'Template Updated Successfully');
                } else {
                    $this->prescription_model->add_qa_template($template_data);
                    set_alert('success', 'Template Added Successfully');
                }
                redirect(admin_url('prescription/settings?group=prescription_qas')); // Redirect back to tab
            }

            set_alert('success', 'Settings Saved Successfully');
            redirect(admin_url('prescription/settings'));
        }

        // Model is already loaded at the top
        $qa_templates = $this->prescription_model->get_qa_templates();

        // Inject Usage Count
        foreach ($qa_templates as &$t) {
            $t['usage_count'] = $this->prescription_model->get_template_usage_count($t['id']);
        }
        $data['qa_templates'] = $qa_templates;

        // Custom Fields
        $data['custom_fields'] = $this->prescription_model->get_custom_fields();

        // Inject Usage Count for Custom Fields
        $usage_counts = [];
        foreach ($data['custom_fields'] as $field) {
            $usage_counts[$field['id']] = $this->prescription_model->get_field_usage_count($field['slug']);
        }
        $data['usage_counts'] = $usage_counts;


        $data['title'] = 'Prescription Settings';
        $this->load->view('prescription/settings', $data);
    }

    public function delete_qa_template($id)
    {
        if (!has_permission('prescription', '', 'delete')) {
            access_denied('prescription');
        }
        $this->load->model('prescription_model');

        // Safety Check
        $usage_reason = $this->prescription_model->is_template_in_use($id);
        if ($usage_reason !== false) {
            set_alert('warning', 'Cannot delete template: ' . $usage_reason);
            redirect(admin_url('prescription/settings?group=prescription_qas'));
        }

        $this->prescription_model->delete_qa_template($id);
        set_alert('success', 'Template Deleted Successfully');
        redirect(admin_url('prescription/settings?group=prescription_qas'));
    }

    public function recreate($id)
    {
        if (!has_permission('prescription', '', 'create') || !has_permission('prescription', '', 'delete')) {
            access_denied('prescription');
        }

        $this->load->model('prescription_model');

        // Delete Existing Prescription
        $visit_id = $this->prescription_model->delete_prescription($id);

        if ($visit_id) {
            // Find Consultation ID (Patient Test ID)
            $visit = $this->db->where('id', $visit_id)->get(db_prefix() . 'visits')->row();
            if ($visit && $visit->invoice_id) {
                // Get tests for this invoice and check for Fee group
                $this->db->select(db_prefix() . 'patient_tests.*, ' . db_prefix() . 'items_groups.name as group_name');
                $this->db->from(db_prefix() . 'patient_tests');
                $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id');
                $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id');
                $this->db->where(db_prefix() . 'patient_tests.invoice_id', $visit->invoice_id);
                $tests = $this->db->get()->result();

                $redirect_id = null;

                if ($tests) {
                    foreach ($tests as $test) {
                        // Only update status if it is a Fee item (Consultation)
                        if ($test->group_name == 'Fee') {
                            $this->prescription_model->update_status($test->id, 'Processing');
                            $redirect_id = $test->id;
                        }
                    }

                    if ($redirect_id) {
                        set_alert('success', 'Prescription reset. You can now create a new one.');
                        redirect(admin_url('prescription/create/' . $redirect_id));
                    }
                }
            }
        }

        set_alert('warning', 'Could not reset prescription.');
        redirect(admin_url('prescription'));
    }

    public function cancel($id)
    {
        if (!has_permission('prescription', '', 'create')) { // Assuming create permission allows managing workflow
            access_denied('prescription');
        }
        $this->load->model('prescription_model');
        if ($this->prescription_model->update_status($id, 'Cancel')) {
            set_alert('success', 'Prescription functionality cancelled.');
        } else {
            set_alert('warning', 'Failed to cancel.');
        }
        redirect(admin_url('prescription'));
    }

    public function uncancel($id)
    {
        if (!has_permission('prescription', '', 'create')) {
            access_denied('prescription');
        }
        $this->load->model('prescription_model');
        if ($this->prescription_model->update_status($id, 'Processing')) {
            set_alert('success', 'Prescription functionality restored.');
        } else {
            set_alert('warning', 'Failed to restore.');
        }
        redirect(admin_url('prescription'));
    }

    /* Custom Fields Management */
    public function save_custom_field()
    {
        if (!has_permission('prescription', '', 'create')) {
            echo json_encode(['success' => false, 'message' => 'Access Denied']);
            return;
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $this->load->model('prescription_model');

            if (empty($data['id'])) {
                // Add New
                $data['slug'] = slug_it($data['label'], ['separator' => '_']); // Helper to create slug
                // Check uniqueness of slug
                // For simplicity, we assume it's unique enough or user deals with collision. 
                // Ideally we should check db.

                // Append random string if short or common? No, clean slug is better.
                // Just ensure it doesn't conflict with system columns?

                $id = $this->prescription_model->add_custom_field($data);
                if ($id) {
                    echo json_encode(['success' => true, 'message' => 'Field Added Successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add field']);
                }
            } else {
                // Update
                $id = $data['id'];
                unset($data['id']);
                if ($this->prescription_model->update_custom_field($id, $data)) {
                    echo json_encode(['success' => true, 'message' => 'Field Updated Successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update field']);
                }
            }
        }
    }

    public function delete_custom_field($id)
    {
        if (!has_permission('prescription', '', 'delete')) {
            echo json_encode(['success' => false, 'message' => 'Access Denied']);
            return;
        }
        $this->load->model('prescription_model');

        if ($this->prescription_model->delete_custom_field($id)) {
            echo json_encode(['success' => true, 'message' => 'Field Deleted Successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cannot delete system fields or field not found.']);
        }
    }

    public function master_data($type = '')
    {
        if (!has_permission('prescription', '', 'view')) {
            access_denied('Prescription Settings');
        }

        if (empty($type)) {
            redirect(admin_url('prescription/settings'));
        }

        $this->load->model('prescription_model');

        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);

            $data['category'] = $type;

            if ($id) {
                $this->prescription_model->update_master_data($id, $data);
                set_alert('success', 'Item Updated Successfully');
            } else {
                $this->prescription_model->add_master_data($data);
                set_alert('success', 'Item Added Successfully');
            }
            redirect(admin_url('prescription/master_data/' . $type));
        }

        $data['title'] = 'Manage ' . ucfirst(str_replace('_', ' ', $type));
        $data['type'] = $type;
        $data['items'] = $this->prescription_model->get_master_data($type);

        $this->load->view('prescription/master_data', $data);
    }

    public function delete_master_data($id)
    {
        if (!has_permission('prescription', '', 'delete')) {
            access_denied('prescription');
        }

        $this->load->model('prescription_model');
        $item = $this->db->where('id', $id)->get(db_prefix() . 'prescription_master_data')->row();

        if ($item) {
            $this->prescription_model->delete_master_data($id);
            set_alert('success', 'Item Deleted Successfully');
            redirect(admin_url('prescription/master_data/' . $item->category));
        } else {
            redirect(admin_url('prescription/settings'));
        }
    }
}
