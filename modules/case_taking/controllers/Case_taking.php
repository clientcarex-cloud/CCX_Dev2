<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Case_taking extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('case_taking_model');

        $data['title'] = 'Case Taking';

        $filters = [
            'status' => $this->input->get('status') ? $this->input->get('status') : 'all',
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date'),
            'search' => $this->input->get('search')
        ];

        // Apply Default Date if NO date filter is present
        if (empty($filters['from_date']) && empty($filters['to_date'])) {
            $default_date = get_option('case_taking_default_date_filter');
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
        $data['consultations'] = $this->case_taking_model->get_consultations($filters);
        $data['status_counts'] = $this->case_taking_model->get_status_counts($filters);

        $this->load->view('case_taking/manage', $data);
    }

    public function change_status($id, $status)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->model('case_taking_model');
        $success = $this->case_taking_model->update_status($id, $status);

        echo json_encode(['success' => $success]);
    }

    public function create($id = '')
    {
        if (!has_permission('case_taking', '', 'create')) {
            access_denied('case_taking');
        }

        $this->load->model('case_taking_model');

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
        $data['visit'] = $this->db->where('invoice_id', $test->invoice_id)->get(db_prefix() . 'visits')->row();

        // Fetch Master Data
        $this->load->model('master_data/master_data_model');


        // Fetch Custom Fields (Active Only for New/Edit)
        $custom_fields = $this->case_taking_model->get_custom_fields(true);
        $data['custom_fields'] = $custom_fields;

        // Fetch Master Data for Autocomplete & QA Questions
        $data['master_data'] = [];
        $data['qa_questions'] = [];

        foreach ($custom_fields as $field) {
            $slug = $field['slug'];

            // Master Data
            $data['master_data'][$slug] = $this->case_taking_model->get_master_data($slug);

            // QA Questions
            if ($field['type'] == 'qas') {
                $template_id = get_option('case_taking_qa_template_' . $slug);
                if ($template_id) {
                    $template = $this->case_taking_model->get_qa_templates($template_id);
                    if ($template) {
                        $data['qa_questions'][$slug] = json_decode($template->questions, true);
                    }
                }
            }
        }

        // Prepare Input Settings (Global Defaults for New)
        $input_settings = [];
        $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals', 'medicine'];
        foreach ($fields as $f) {
            if (!in_array($f, ['vitals'])) {
                $input_settings[$f . '_type'] = get_option('case_taking_input_type_' . $f);
            } else {
                $input_settings['case_taking_show_' . $f] = get_option('case_taking_show_' . $f);
            }
            $input_settings[$f . '_mandatory'] = get_option('case_taking_mandatory_' . $f);
        }
        $data['input_settings'] = $input_settings;

        // Dynamic Title
        $visit_code = isset($data['visit']) ? $data['visit']->visit_code : 'N/A';
        $data['title'] = $data['patient']->title . ' ' . $data['patient']->full_name . ' | MR. No: ' . $data['patient']->mr_number . ' | Visit-ID: ' . $visit_code . ' | ' . $data['patient']->age . ' ' . $data['patient']->age_unit;

        $this->load->view('case_taking/create', $data);
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
            $this->load->model('case_taking_model');
            $current_custom_fields = $this->case_taking_model->get_custom_fields();

            $snapshot_settings = [];
            $extra_fields = ['case_taking_show_vitals'];
            foreach ($extra_fields as $f) {
                $snapshot_settings[$f] = get_option($f);
                $mandatory_key = str_replace('_show_', '_mandatory_', $f);
                $snapshot_settings[$mandatory_key] = get_option($mandatory_key);
            }
            $snapshot_settings['case_taking_input_type_vitals'] = get_option('case_taking_input_type_vitals');

            $snapshot = [
                'version' => 2,
                'fields' => $current_custom_fields,
                'settings' => $snapshot_settings
            ];
            $data['input_config'] = json_encode($snapshot);

            $id = $this->case_taking_model->add_case_taking($data);

            if ($id) {
                // Determined Status based on button click
                $status = ($this->input->post('save_type') == 'draft') ? 'Processing' : 'Completed';

                // If completed, update with date_completed
                if ($status == 'Completed') {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'case_taking', ['date_completed' => date('Y-m-d H:i:s')]);
                }

                // Update status 
                if (isset($data['consultation_id'])) {
                    $this->case_taking_model->update_status($data['consultation_id'], $status);
                }

                set_alert('success', _l('added_successfully', 'Case Taking'));
                redirect(admin_url('case_taking'));
            }
        }
        redirect(admin_url('case_taking'));
    }

    public function autosave()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data = $this->input->post();
        $this->load->model('case_taking_model');

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

        if (isset($data['save_type']))
            unset($data['save_type']);
        if (isset($data['csrf_token_name']))
            unset($data['csrf_token_name']);

        $id = isset($data['id']) ? $data['id'] : '';
        if (isset($data['id']))
            unset($data['id']);

        $response = [
            'success' => false,
            'csrf_token_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ];

        if (!empty($id)) {
            // Update Existing
            $success = $this->case_taking_model->update_case_taking($id, $data);
            if ($success) {
                $response['success'] = true;
                $response['id'] = $id;
                $response['action'] = 'update';
            }
        } else {
            // Create New
            $snapshot = [];
            $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals'];
            foreach ($fields as $f) {
                if (!in_array($f, ['vitals'])) {
                    $snapshot[$f . '_type'] = get_option('case_taking_input_type_' . $f);
                }
                $snapshot[$f . '_mandatory'] = get_option('case_taking_mandatory_' . $f);
            }
            $data['input_config'] = json_encode($snapshot);

            $new_id = $this->case_taking_model->add_case_taking($data);
            if ($new_id) {
                if (isset($data['consultation_id'])) {
                    $this->case_taking_model->update_status($data['consultation_id'], 'Processing');
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
        if (!has_permission('case_taking', '', 'edit')) {
            access_denied('case_taking');
        }

        $this->load->model('case_taking_model');

        $case_taking = $this->case_taking_model->get_case_taking($id);

        if (!$case_taking) {
            show_404();
        }

        $data['prescription'] = $case_taking; // Keeping variable name 'prescription' for view compatibility or mass replace to 'case_taking' in view
        // Actually, if I replace 'prescription' in view, I should pass 'case_taking' here.
        // I will assume I will bulk replace $prescription -> $case_taking in views.
        $data['case_taking'] = $case_taking;

        $data['consultation_id'] = '';

        // Fetch Patient Data
        $this->load->model('patients/patients_model');
        $data['patient'] = $this->patients_model->get($case_taking->patient_id);

        // Fetch Visit Data
        $data['visit'] = $this->db->where('id', $case_taking->visit_id)->get(db_prefix() . 'visits')->row();

        // 1. Snapshot / Default Loading
        $input_settings = [];
        $custom_fields = [];
        $snapshot_data = [];

        // Check for Snapshot
        if (!empty($case_taking->input_config)) {
            $snapshot_data = json_decode($case_taking->input_config, true);
        }

        // Global Defaults (Fallback)
        $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals', 'medicine'];
        $db_custom_fields = $this->case_taking_model->get_custom_fields();


        if (!empty($snapshot_data) && isset($snapshot_data['version']) && $snapshot_data['version'] == 2) {
            $custom_fields = $snapshot_data['fields'];
            $input_settings = $snapshot_data['settings'];
        } else {
            // Version 1 or No Snapshot
            foreach ($fields as $f) {
                if (!in_array($f, ['vitals', 'medicine'])) {
                    $key_type = $f . '_type';
                    if (isset($snapshot_data[$key_type])) {
                        $input_settings[$key_type] = $snapshot_data[$key_type];
                    } else {
                        $input_settings[$key_type] = get_option('case_taking_input_type_' . $f);
                    }
                }

                $key_mandatory = $f . '_mandatory';
                if (isset($snapshot_data[$key_mandatory])) {
                    $input_settings[$key_mandatory] = $snapshot_data[$key_mandatory];
                } else {
                    $input_settings[$key_mandatory] = get_option('case_taking_mandatory_' . $f);
                }
            }

            $simple_extras = ['vitals', 'medicine'];
            foreach ($simple_extras as $extra) {
                $key = 'case_taking_show_' . $extra;
                if (isset($snapshot_data[$key])) {
                    $input_settings[$key] = $snapshot_data[$key];
                } else {
                    $input_settings[$key] = get_option($key);
                }

                $m_key = 'case_taking_mandatory_' . $extra;
                if (isset($snapshot_data[$m_key])) {
                    $input_settings[$extra . '_mandatory'] = $snapshot_data[$m_key];
                } else {
                    $input_settings[$extra . '_mandatory'] = get_option($m_key);
                }
            }
            if (isset($snapshot_data['case_taking_input_type_vitals'])) {
                $input_settings['case_taking_input_type_vitals'] = $snapshot_data['case_taking_input_type_vitals'];
            } else {
                $input_settings['case_taking_input_type_vitals'] = get_option('case_taking_input_type_vitals');
            }

            $custom_fields = $db_custom_fields;
            foreach ($custom_fields as &$field) {
                $slug = $field['slug'];
                if (isset($input_settings[$slug . '_type'])) {
                    $field['type'] = $input_settings[$slug . '_type'];
                }
                if (isset($input_settings[$slug . '_mandatory'])) {
                    $field['mandatory'] = $input_settings[$slug . '_mandatory'];
                }
            }
        }

        $data['input_settings'] = $input_settings;
        $data['custom_fields'] = $custom_fields;

        // Check if completed
        $data['is_completed'] = false;
        if ($data['visit'] && $data['visit']->invoice_id) {
            $this->db->where('invoice_id', $data['visit']->invoice_id);
            $this->db->where('status', 'Completed');
            $completed_test = $this->db->get(db_prefix() . 'patient_tests')->row();
            if ($completed_test) {
                $data['is_completed'] = true;
            }
        }

        $data['consultation'] = (object) ['patient_id' => $case_taking->patient_id];

        // Fetch Master Data
        $this->load->model('master_data/master_data_model');
        $data['medicine_types'] = $this->master_data_model->get_medicine_types();
        $data['medicine_doses'] = $this->master_data_model->get_medicine_doses();
        $data['medicine_frequencies'] = $this->master_data_model->get_medicine_frequencies();
        $data['medicine_durations'] = $this->master_data_model->get_medicine_durations();
        $data['medicine_whens'] = $this->master_data_model->get_medicine_whens();

        $data['medicines'] = $this->case_taking_model->get_medicines();



        $data['qa_questions'] = [];
        foreach ($custom_fields as $field) {
            if ($field['type'] == 'qas') {
                $slug = $field['slug'];
                $template_id = get_option('case_taking_qa_template_' . $slug);
                if ($template_id) {
                    $template = $this->case_taking_model->get_qa_templates($template_id);
                    if ($template) {
                        $data['qa_questions'][$slug] = json_decode($template->questions, true);
                    }
                }
            }
        }

        $data['title'] = $data['patient']->title . ' ' . $data['patient']->full_name . ' | MR. No: ' . $data['patient']->mr_number . ' | Visit-ID: ' . $data['visit']->visit_code . ' | ' . $data['patient']->age . ' ' . $data['patient']->age_unit;

        $this->load->view('case_taking/create', $data);
    }

    public function view($id = '')
    {
        if (!has_permission('case_taking', '', 'view')) {
            access_denied('case_taking');
        }

        $this->load->model('case_taking_model');

        $case_taking = $this->case_taking_model->get_case_taking($id);

        if (!$case_taking) {
            show_404();
        }

        $data['case_taking'] = $case_taking; // For updated view
        $data['consultation_id'] = '';
        $data['read_only'] = true;

        $this->load->model('patients/patients_model');
        $data['patient'] = $this->patients_model->get($case_taking->patient_id);
        $data['visit'] = $this->db->where('id', $case_taking->visit_id)->get(db_prefix() . 'visits')->row();

        // Snapshot Logic (Same as Edit)
        $input_settings = [];
        $custom_fields = [];
        $snapshot_data = [];

        if (!empty($case_taking->input_config)) {
            $snapshot_data = json_decode($case_taking->input_config, true);
        }

        $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals', 'medicine'];
        $db_custom_fields = $this->case_taking_model->get_custom_fields();

        if (!empty($snapshot_data) && isset($snapshot_data['version']) && $snapshot_data['version'] == 2) {
            $custom_fields = $snapshot_data['fields'];
            $input_settings = $snapshot_data['settings'];
        } else {
            foreach ($fields as $f) {
                if (!in_array($f, ['vitals', 'medicine'])) {
                    $key_type = $f . '_type';
                    if (isset($snapshot_data[$key_type])) {
                        $input_settings[$key_type] = $snapshot_data[$key_type];
                    } else {
                        $input_settings[$key_type] = get_option('case_taking_input_type_' . $f);
                    }
                }
                $key_mandatory = $f . '_mandatory';
                if (isset($snapshot_data[$key_mandatory])) {
                    $input_settings[$key_mandatory] = $snapshot_data[$key_mandatory];
                } else {
                    $input_settings[$key_mandatory] = get_option('case_taking_mandatory_' . $f);
                }
            }

            $simple_extras = ['vitals', 'tests_requested', 'next_visit', 'medicine'];
            foreach ($simple_extras as $extra) {
                $key = 'case_taking_show_' . $extra;
                if (isset($snapshot_data[$key])) {
                    $input_settings[$key] = $snapshot_data[$key];
                } else {
                    $input_settings[$key] = get_option($key);
                }

                $m_key = 'case_taking_mandatory_' . $extra;
                if (isset($snapshot_data[$m_key])) {
                    $input_settings[$extra . '_mandatory'] = $snapshot_data[$m_key];
                } else {
                    $input_settings[$extra . '_mandatory'] = get_option($m_key);
                }
            }
            if (isset($snapshot_data['case_taking_input_type_vitals'])) {
                $input_settings['case_taking_input_type_vitals'] = $snapshot_data['case_taking_input_type_vitals'];
            } else {
                $input_settings['case_taking_input_type_vitals'] = get_option('case_taking_input_type_vitals');
            }

            $custom_fields = $db_custom_fields;
            foreach ($custom_fields as &$field) {
                $slug = $field['slug'];
                if (isset($input_settings[$slug . '_type'])) {
                    $field['type'] = $input_settings[$slug . '_type'];
                }
                if (isset($input_settings[$slug . '_mandatory'])) {
                    $field['mandatory'] = $input_settings[$slug . '_mandatory'];
                }
            }
        }

        $data['input_settings'] = $input_settings;
        $data['custom_fields'] = $custom_fields;

        $data['qa_questions'] = [];
        foreach ($custom_fields as $field) {
            if ($field['type'] == 'qas') {
                $slug = $field['slug'];
                $template_id = get_option('case_taking_qa_template_' . $slug);
                if ($template_id) {
                    $template = $this->case_taking_model->get_qa_templates($template_id);
                    if ($template) {
                        $data['qa_questions'][$slug] = json_decode($template->questions, true);
                    }
                }
            }
        }

        $data['consultation'] = (object) ['patient_id' => $case_taking->patient_id];

        $this->load->model('master_data/master_data_model');
        $data['medicine_types'] = $this->master_data_model->get_medicine_types();
        $data['medicine_doses'] = $this->master_data_model->get_medicine_doses();
        $data['medicine_frequencies'] = $this->master_data_model->get_medicine_frequencies();
        $data['medicine_durations'] = $this->master_data_model->get_medicine_durations();
        $data['medicine_whens'] = $this->master_data_model->get_medicine_whens();

        $data['medicines'] = $this->case_taking_model->get_medicines();


        $data['title'] = $data['patient']->title . ' ' . $data['patient']->full_name . ' | MR. No: ' . $data['patient']->mr_number . ' | Visit-ID: ' . $data['visit']->visit_code . ' | ' . $data['patient']->age . ' ' . $data['patient']->age_unit;

        $this->load->view('case_taking/create', $data);
    }

    public function update($id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();

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

            $save_type = isset($data['save_type']) ? $data['save_type'] : 'completed';
            if (isset($data['save_type']))
                unset($data['save_type']);

            $this->load->model('case_taking_model');

            $existing_case_taking = $this->case_taking_model->get_case_taking($id);
            if ($existing_case_taking && empty($existing_case_taking->input_config)) {
                $snapshot = [];
                $fields = ['complaints', 'diagnosis', 'advice', 'personal_history', 'family_history', 'allergies', 'vaccination', 'vitals'];
                foreach ($fields as $f) {
                    if (!in_array($f, ['vitals'])) {
                        $snapshot[$f . '_type'] = get_option('case_taking_input_type_' . $f);
                    }
                    $snapshot[$f . '_mandatory'] = get_option('case_taking_mandatory_' . $f);
                }
                $data['input_config'] = json_encode($snapshot);
            }

            $success = $this->case_taking_model->update_case_taking($id, $data);

            if ($success) {
                $status = ($save_type == 'draft') ? 'Processing' : 'Completed';

                $case_taking = $existing_case_taking;
                if ($case_taking && $case_taking->visit_id) {
                    $visit = $this->db->where('id', $case_taking->visit_id)->get(db_prefix() . 'visits')->row();
                    if ($visit && $visit->invoice_id) {
                        $this->db->select('id');
                        $this->db->where('invoice_id', $visit->invoice_id);
                        $tests = $this->db->get(db_prefix() . 'patient_tests')->result();

                        foreach ($tests as $test) {
                            $this->case_taking_model->update_status($test->id, $status);
                        }
                    }
                }

                set_alert('success', _l('updated_successfully', 'Case Taking'));
            }
        }
        redirect(admin_url('case_taking'));
    }

    public function settings()
    {
        $this->load->model('case_taking_model');

        if (!has_permission('case_taking', '', 'view')) {
            access_denied('Case Taking Settings');
        }

        if ($this->input->post()) {
            $group = $this->input->post('settings_group');

            if ($group == 'filters') {
                update_option('case_taking_default_date_filter', $this->input->post('case_taking_default_date_filter'));
            } elseif ($group == 'hide_show') {
                $opts = [
                    'case_taking_show_date_filter',
                ];
                foreach ($opts as $opt) {
                    $val = $this->input->post($opt) ? '1' : '0';
                    update_option($opt, $val);
                }
            } elseif ($group == 'digital_case_taking') {
                $opts = [
                    'case_taking_mode_type',
                    'case_taking_edit_timeout',
                    'case_taking_enable_autosave',
                ];
                foreach ($opts as $opt) {
                    if ($opt == 'case_taking_edit_timeout' || $opt == 'case_taking_mode_type') {
                        update_option($opt, $this->input->post($opt));
                    } else {
                        $val = $this->input->post($opt) ? '1' : '0';
                        update_option($opt, $val);
                    }
                }

                $custom_fields = $this->case_taking_model->get_custom_fields();
                foreach ($custom_fields as $field) {
                    $slug = $field['slug'];
                    $id = $field['id'];

                    $field_orders = $this->input->post('field_order');
                    $types = $this->input->post('type');
                    $mandatories = $this->input->post('mandatory');
                    $is_actives = $this->input->post('is_active');

                    $update_data = [];

                    if (isset($field_orders[$id])) {
                        $update_data['field_order'] = $field_orders[$id];
                    }

                    $update_data['is_active'] = (isset($is_actives[$id]) && $is_actives[$id] == 1) ? 1 : 0;
                    $update_data['mandatory'] = (isset($mandatories[$id]) && $mandatories[$id] == 1) ? 1 : 0;

                    if (isset($types[$id])) {
                        $update_data['type'] = $types[$id];
                    }

                    $template_key = 'case_taking_qa_template_' . $slug;
                    if (isset($update_data['type']) && $update_data['type'] == 'qas') {
                        update_option($template_key, $this->input->post($template_key));
                    }

                    $this->case_taking_model->update_custom_field($id, $update_data);

                    if ($field['is_system'] == 1) {
                        update_option('case_taking_show_' . $slug, $update_data['is_active']);
                        update_option('case_taking_mandatory_' . $slug, $update_data['mandatory']);
                        if (isset($update_data['type'])) {
                            update_option('case_taking_input_type_' . $slug, $update_data['type']);
                        }
                    }
                }

                $other_opts = [
                    'case_taking_show_vitals',
                ];
                foreach ($other_opts as $opt) {
                    $val = $this->input->post($opt) ? '1' : '0';
                    update_option($opt, $val);

                    $mandatory_key = str_replace('_show_', '_mandatory_', $opt);
                    $m_val = $this->input->post($mandatory_key) ? '1' : '0';
                    update_option($mandatory_key, $m_val);


                }
            } elseif ($group == 'case_takings_table') {
                $opts = ['case_taking_show_options_column'];
                foreach ($opts as $opt) {
                    $val = $this->input->post($opt) ? '1' : '0';
                    update_option($opt, $val);
                }
            } elseif ($group == 'case_taking_qas_save') {
                $questions_input = $this->input->post('questions');

                if (!empty($questions_input) && is_array($questions_input)) {
                    $questions_input = array_values($questions_input);
                }

                $template_data = [
                    'name' => $this->input->post('name'),
                    'questions' => json_encode($questions_input)
                ];
                $id = $this->input->post('id');
                $this->load->model('case_taking_model');

                if ($id) {
                    $existing_template = $this->case_taking_model->get_qa_templates($id);
                    if ($existing_template) {
                        $old_questions = json_decode($existing_template->questions, true);
                        if (!empty($old_questions)) {
                            $new_question_texts = [];
                            if (!empty($questions_input)) {
                                foreach ($questions_input as $q) {
                                    $new_question_texts[] = is_array($q) ? $q['text'] : $q;
                                }
                            }

                            foreach ($old_questions as $old_q) {
                                $old_text = is_array($old_q) ? $old_q['text'] : $old_q;

                                if (!in_array($old_text, $new_question_texts)) {
                                    if ($this->case_taking_model->is_question_used($old_text)) {
                                        set_alert('warning', 'Cannot delete question "' . $old_text . '" because it is used in existing case takings.');
                                        redirect(admin_url('case_taking/settings?group=case_taking_qas'));
                                    }
                                }
                            }
                        }
                    }

                    $this->case_taking_model->update_qa_template($template_data, $id);
                    set_alert('success', 'Template Updated Successfully');
                } else {
                    $this->case_taking_model->add_qa_template($template_data);
                    set_alert('success', 'Template Added Successfully');
                }
                redirect(admin_url('case_taking/settings?group=case_taking_qas'));
            }

            set_alert('success', 'Settings Saved Successfully');
            redirect(admin_url('case_taking/settings'));
        }

        $qa_templates = $this->case_taking_model->get_qa_templates();
        foreach ($qa_templates as &$t) {
            $t['usage_count'] = $this->case_taking_model->get_template_usage_count($t['id']);
        }
        $data['qa_templates'] = $qa_templates;

        $data['custom_fields'] = $this->case_taking_model->get_custom_fields();

        $usage_counts = [];
        foreach ($data['custom_fields'] as $field) {
            $usage_counts[$field['id']] = $this->case_taking_model->get_field_usage_count($field['slug']);
        }
        $data['usage_counts'] = $usage_counts;


        $data['title'] = 'Case Taking Settings';
        $this->load->view('case_taking/settings', $data);
    }

    public function delete_qa_template($id)
    {
        if (!has_permission('case_taking', '', 'delete')) {
            access_denied('case_taking');
        }
        $this->load->model('case_taking_model');

        $usage_reason = $this->case_taking_model->is_template_in_use($id);
        if ($usage_reason !== false) {
            set_alert('warning', 'Cannot delete template: ' . $usage_reason);
            redirect(admin_url('case_taking/settings?group=case_taking_qas'));
        }

        $this->case_taking_model->delete_qa_template($id);
        set_alert('success', 'Template Deleted Successfully');
        redirect(admin_url('case_taking/settings?group=case_taking_qas'));
    }

    public function recreate($id)
    {
        if (!has_permission('case_taking', '', 'create') || !has_permission('case_taking', '', 'delete')) {
            access_denied('case_taking');
        }

        $this->load->model('case_taking_model');

        $visit_id = $this->case_taking_model->delete_case_taking($id);

        if ($visit_id) {
            $visit = $this->db->where('id', $visit_id)->get(db_prefix() . 'visits')->row();
            if ($visit && $visit->invoice_id) {
                $this->db->select(db_prefix() . 'patient_tests.*, ' . db_prefix() . 'items_groups.name as group_name');
                $this->db->from(db_prefix() . 'patient_tests');
                $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id');
                $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id');
                $this->db->where(db_prefix() . 'patient_tests.invoice_id', $visit->invoice_id);
                $tests = $this->db->get()->result();

                $redirect_id = null;

                if ($tests) {
                    foreach ($tests as $test) {
                        if ($test->group_name == 'Fee') {
                            $this->case_taking_model->update_status($test->id, 'Processing');
                            $redirect_id = $test->id;
                        }
                    }

                    if ($redirect_id) {
                        set_alert('success', 'Case Taking reset. You can now create a new one.');
                        redirect(admin_url('case_taking/create/' . $redirect_id));
                    }
                }
            }
        }

        set_alert('warning', 'Could not reset Case Taking.');
        redirect(admin_url('case_taking'));
    }

    public function cancel($id)
    {
        if (!has_permission('case_taking', '', 'create')) {
            access_denied('case_taking');
        }
        $this->load->model('case_taking_model');
        if ($this->case_taking_model->update_status($id, 'Cancel')) {
            set_alert('success', 'Case Taking functionality cancelled.');
        } else {
            set_alert('warning', 'Failed to cancel.');
        }
        redirect(admin_url('case_taking'));
    }

    public function uncancel($id)
    {
        if (!has_permission('case_taking', '', 'create')) {
            access_denied('case_taking');
        }
        $this->load->model('case_taking_model');
        if ($this->case_taking_model->update_status($id, 'Processing')) {
            set_alert('success', 'Case Taking functionality restored.');
        } else {
            set_alert('warning', 'Failed to restore.');
        }
        redirect(admin_url('case_taking'));
    }

    /* Custom Fields Management */
    public function save_custom_field()
    {
        if (!has_permission('case_taking', '', 'create')) {
            echo json_encode(['success' => false, 'message' => 'Access Denied']);
            return;
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $this->load->model('case_taking_model');

            if (empty($data['id'])) {
                // Add New
                $data['slug'] = slug_it($data['label'], ['separator' => '_']);

                $id = $this->case_taking_model->add_custom_field($data);
                if ($id) {
                    echo json_encode(['success' => true, 'message' => 'Field Added Successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add field']);
                }
            } else {
                // Update
                $id = $data['id'];
                unset($data['id']);
                if ($this->case_taking_model->update_custom_field($id, $data)) {
                    echo json_encode(['success' => true, 'message' => 'Field Updated Successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update field']);
                }
            }
        }
    }

    public function delete_custom_field($id)
    {
        if (!has_permission('case_taking', '', 'delete')) {
            echo json_encode(['success' => false, 'message' => 'Access Denied']);
            return;
        }
        $this->load->model('case_taking_model');

        if ($this->case_taking_model->delete_custom_field($id)) {
            echo json_encode(['success' => true, 'message' => 'Field Deleted Successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cannot delete system fields or field not found.']);
        }
    }

    public function master_data($type = '')
    {
        if (!has_permission('case_taking', '', 'view')) {
            access_denied('Case Taking Settings');
        }

        if (empty($type)) {
            redirect(admin_url('case_taking/settings'));
        }

        $this->load->model('case_taking_model');

        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);

            $data['category'] = $type;

            if ($id) {
                $this->case_taking_model->update_master_data($id, $data);
                set_alert('success', 'Item Updated Successfully');
            } else {
                $this->case_taking_model->add_master_data($data);
                set_alert('success', 'Item Added Successfully');
            }
            redirect(admin_url('case_taking/master_data/' . $type));
        }

        $data['title'] = 'Manage ' . ucfirst(str_replace('_', ' ', $type));
        $data['type'] = $type;
        $data['items'] = $this->case_taking_model->get_master_data($type);

        $this->load->view('case_taking/master_data', $data);
    }

    public function delete_master_data($id)
    {
        if (!has_permission('case_taking', '', 'delete')) {
            access_denied('case_taking');
        }

        $this->load->model('case_taking_model');
        $item = $this->db->where('id', $id)->get(db_prefix() . 'case_taking_master_data')->row();

        if ($item) {
            $this->case_taking_model->delete_master_data($id);
            set_alert('success', 'Item Deleted Successfully');
            redirect(admin_url('case_taking/master_data/' . $item->category));
        } else {
            redirect(admin_url('case_taking/settings'));
        }
    }
}
