<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Visits extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('patients_model');
        $this->load->model('invoice_items_model');
        $this->load->model('payment_modes_model');
        $this->load->model('currencies_model');
        $this->load->model('print_templates/print_templates_model');
    }

    public function index()
    {
        if (!has_permission('visits', '', 'view')) {
            access_denied('Visits');
        }

        $data['title'] = 'Patient Visits';

        // Filters
        $filters = [
            'search' => $this->input->get('search'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date'),
            'user_id' => $this->input->get('user_id'),
            'status' => $this->input->get('status'),
            'payment_status' => $this->input->get('payment_status')
        ];

        // Default Date Range Logic from Settings
        if ($this->input->get('from_date') === null && $this->input->get('to_date') === null) {
            $default_date_range = get_option('patients_visit_default_date_range');
            if ($default_date_range == 'today') {
                $filters['from_date'] = _d(date('Y-m-d'));
                $filters['to_date'] = _d(date('Y-m-d'));
            } elseif ($default_date_range == 'yesterday_today') {
                $filters['from_date'] = _d(date('Y-m-d', strtotime('-1 day')));
                $filters['to_date'] = _d(date('Y-m-d'));
            } elseif ($default_date_range == 'last_week') {
                $filters['from_date'] = _d(date('Y-m-d', strtotime('-7 days')));
                $filters['to_date'] = _d(date('Y-m-d'));
            }
        }

        // Fix: Use Query String Pagination - MANUAL IMPLEMENTATION
        // To completely bypass CI3 Pagination library PHP 8 issues.

        $per_page_offset = $this->input->get('per_page') ? (int) $this->input->get('per_page') : 0;
        $page = $per_page_offset; // Keep existing variable name for compatibility

        // LIMIT Logic
        $limit = $this->input->get('limit') ? (int) $this->input->get('limit') : 20;

        $total_rows = $this->patients_model->get_visits_count($filters);

        // Manual Pagination HTML Generation
        $base_url = admin_url('patients/visits/index');
        // Rebuild query string excluding per_page
        $query_params = $_GET;
        unset($query_params['per_page']);
        $query_string = http_build_query($query_params);
        $base_url_with_query = $base_url . '?' . ($query_string ? $query_string . '&' : '');

        $total_pages = ceil($total_rows / $limit);
        $current_page = ($page / $limit) + 1;

        $pagination_html = '<ul class="pagination">';

        // First & Prev
        if ($current_page > 1) {
            $prev_offset = $page - $limit;
            $pagination_html .= '<li><a href="' . $base_url_with_query . 'per_page=0">First</a></li>';
            $pagination_html .= '<li class="prev"><a href="' . $base_url_with_query . 'per_page=' . $prev_offset . '">&laquo;</a></li>';
        }

        // Number Links (Show surrounding 2 pages)
        $start_page = max(1, $current_page - 2);
        $end_page = min($total_pages, $current_page + 2);

        for ($i = $start_page; $i <= $end_page; $i++) {
            $offset = ($i - 1) * $limit;
            if ($i == $current_page) {
                $pagination_html .= '<li class="active"><a href="#">' . $i . '</a></li>';
            } else {
                $pagination_html .= '<li><a href="' . $base_url_with_query . 'per_page=' . $offset . '">' . $i . '</a></li>';
            }
        }

        // Next & Last
        if ($current_page < $total_pages) {
            $next_offset = $page + $limit;
            $last_offset = ($total_pages - 1) * $limit;
            $pagination_html .= '<li><a href="' . $base_url_with_query . 'per_page=' . $next_offset . '">&raquo;</a></li>';
            $pagination_html .= '<li><a href="' . $base_url_with_query . 'per_page=' . $last_offset . '">Last</a></li>';
        }

        $pagination_html .= '</ul>';

        // Fetch visits
        $data['visits'] = $this->patients_model->get_all_visits($limit, $page, $filters);
        $data['pagination_links'] = $pagination_html;
        $data['filters'] = $filters; // Pass back to view
        $data['selected_limit'] = $limit; // Pass limit back

        // Load Staff for Filters
        $this->load->model('staff_model');
        $data['staff_members'] = $this->staff_model->get();

        // Load Item Groups for Filter Tabs
        $data['item_groups'] = $this->invoice_items_model->get_groups();

        // Load Statuses for Filters
        $this->load->model('master_data/master_data_model');
        $data['item_statuses'] = $this->master_data_model->get_item_statuses();

        $this->load->model('invoices_model');
        $data['payment_statuses'] = $this->invoices_model->get_statuses();

        // Fetch Stats
        $stats = $this->patients_model->get_visits_stats($filters);
        $data['stats'] = $stats;

        if ($this->input->is_ajax_request()) {
            $view_html = $this->load->view('visits_list_template', $data, true);
            echo json_encode([
                'html' => $view_html,
                'stats' => $stats
            ]);
            die;
        } else {
            $this->load->view('visits', $data);
        }
    }



    /* Print OP Bill (based on Visit ID) */
    public function print_op_bill($visit_id)
    {
        if (!$visit_id) {
            show_404();
        }
        $this->db->where('id', $visit_id);
        $visit = $this->db->get(db_prefix() . 'visits')->row();
        if (!$visit) {
            show_404();
        }
        echo $this->_prepare_print_data_content($visit->invoice_id, null, 'OP Bill');
    }


    /* Add or edit visit */
    public function add($id = '')
    {
        if ($this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('full_name', 'Patient Name', 'required');
            $this->form_validation->set_rules('mobile_number', 'Mobile Number', 'required');

            if ($this->form_validation->run() === FALSE) {
                set_alert('danger', validation_errors());
                redirect(admin_url('patients/visits/add/' . $id));
            }

            $data = $this->input->post();

            // Handle file upload (Copy from Patients)
            if (isset($_FILES['prescription']) && !empty($_FILES['prescription']['name'])) {
                $path = FCPATH . 'uploads/patients_uploads/';
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                    $index_file = fopen($path . 'index.html', 'w'); // Create blank index.html
                    fclose($index_file);
                }

                $config['upload_path'] = $path;
                $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|doc|docx';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('prescription')) {
                    $upload_data = $this->upload->data();
                    $data['prescription_file'] = $upload_data['file_name'];
                } else {
                    set_alert('warning', 'Prescription upload failed: ' . $this->upload->display_errors());
                }
            }

            if ($id == '') {
                // For Visits, adding a visit usually implies adding a new patient OR selecting one.
                // Since this form clones Patient Add, it creates a Patient + Visit.
                if (!has_permission('visits', '', 'create')) {
                    access_denied('Visits');
                }

                // DUPLICATE CHECK: Mobile + Name
                $existing_patient_id = false;
                if (!empty($data['mobile_number']) && !empty($data['full_name'])) {
                    $this->db->select('userid');
                    $this->db->from(db_prefix() . 'clients');
                    $this->db->where('phonenumber', $data['mobile_number']);
                    $this->db->where('company', $data['full_name']); // Full Name is stored in 'company'
                    $patient_match = $this->db->get()->row();

                    if ($patient_match) {
                        $existing_patient_id = $patient_match->userid;
                    }
                }

                if ($existing_patient_id) {
                    // Match Found! Switch to Update/Add Visit mode for EXISTING patient
                    $id = $existing_patient_id;
                    $success = $this->patients_model->update($data, $id); // This will add the visit too via update()
                    // Set a different alert so user knows
                    set_alert('success', 'Existing patient found. Visit added successfully.');

                    // Proceed to print/redirect logic same as new creation
                    if ($success) {
                        if (get_option('patients_print_invoice_on_visit') == '1') {
                            $latest_visit = $this->patients_model->check_latest_visit_today($id);
                            if ($latest_visit) {
                                redirect(admin_url('patients/visits/add/' . $id . '?print_visit_id=' . $latest_visit->id));
                            }
                        }
                        redirect(admin_url('patients/visits'));
                    }
                } else {
                    // No match, create new
                    $id = $this->patients_model->add($data);
                }
                if ($id) {
                    set_alert('success', 'Visit (and Patient) added successfully');
                    if (get_option('patients_print_invoice_on_visit') == '1') {
                        // $id is patient_id. We need visit/invoice id.
                        // $this->patients_model->add returns patient_id but inside it creates visit.
                        // We need to fetch the latest visit for this patient to get the invoice ID?
                        // Ideally add() should return the visit_id or invoice_id too.
                        // Or we can fetch the latest visit for this patient.
                        $latest_visit = $this->patients_model->check_latest_visit_today($id);
                        if ($latest_visit) {
                            redirect(admin_url('patients/visits/add/' . $id . '?print_visit_id=' . $latest_visit->id));
                        }
                    }
                    redirect(admin_url('patients/visits'));
                }
            } else {
                // Editing... logic remains for patient update
                if (!has_permission('visits', '', 'edit')) {
                    access_denied('Visits');
                }
                $success = $this->patients_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('patient_updated_successfully'));

                    if (get_option('patients_print_invoice_on_visit') == '1') {
                        $latest_visit = $this->patients_model->check_latest_visit_today($id);
                        if ($latest_visit) {
                            redirect(admin_url('patients/visits/add/' . $id . '?print_visit_id=' . $latest_visit->id));
                        }
                    }
                }
                redirect(admin_url('patients/visits'));
            }
        }

        if ($id == '') {
            $data['title'] = 'New Visit';
            $data['locked_fields'] = [];
            $data['today_invoices'] = [];
        } else {
            // Ensure MR Number exists (Fix for old/external patients)
            $this->patients_model->ensure_mr_number($id);
            $data['patient'] = $this->patients_model->get($id);
            $data['today_invoices'] = $this->patients_model->get_today_invoices($id);

            // Check Time Locker Logic
            $locker_time = get_option('patients_visit_time_locker');
            $locked_fields_setting = get_option('patients_visit_locker_fields');
            $locked_fields_setting = !empty($locked_fields_setting) ? json_decode($locked_fields_setting, true) : [];

            $data['locked_fields'] = [];

            if (!empty($locker_time) && !empty($locked_fields_setting) && isset($data['patient']->datecreated)) {
                $created = $data['patient']->datecreated;
                $diff_minutes = (time() - strtotime($created)) / 60;

                if ($diff_minutes > $locker_time) {
                    $data['locked_fields'] = $locked_fields_setting;
                }
            }

            // Logic: If visit exists RECENTLY (Today/Yesterday), load it (Edit Mode).
            // If NOT recent, start fresh (New Visit Mode).
            // Note: $id here is PATIENT ID.

            // Use check_latest_active_visit to catch yesterday's visits too
            $today_visit = $this->patients_model->check_latest_active_visit($id);

            if ($today_visit && !$this->input->get('new_session')) {
                // EDIT MODE (Visit exists today)
                $data['tests'] = $this->patients_model->get_visit_tests($today_visit->invoice_id);
                $data['total_paid'] = $this->patients_model->get_invoice_paid($today_visit->invoice_id); // FIXED: Paid for THIS invoice only

                $data['title'] = 'Edit Visit / Patient';
                $data['visit_data'] = $today_visit; // Pass visit object

                // Fetch Invoice Data for Discount Persistence
                $this->load->model('invoices_model');
                $data['invoice'] = $this->invoices_model->get($today_visit->invoice_id);

                // Fetch Refund Total
                $this->load->model('refunds/refunds_model');
                $data['total_refunded'] = $this->refunds_model->get_invoice_refund_total($today_visit->invoice_id);
            } else {
                // NEW VISIT MODE (No visit today)
                $data['tests'] = []; // CLEAN SLATE
                $data['total_paid'] = 0; // Or historic? If typical CRM, unpaid from history?
                // User said "new invoice and item". So total_paid for THIS invoice is 0.
                // Historic total paid is for reference.
                // NOTE: We change this to 0 for "this invoice paid" context to fix negative due bug.
                $data['total_paid'] = 0;
                $data['title'] = 'New Visit';
                $data['visit_data'] = null;
            }
        }

        $data['name_titles'] = $this->patients_model->get_name_titles();
        $data['doctors'] = $this->patients_model->get_doctors();

        // OPTIMIZATION: Create Doctor -> Service Item Map
        $doctor_service_items = [];
        if (!empty($data['doctors'])) {
            $doctor_ids = array_column($data['doctors'], 'staffid');
            if (!empty($doctor_ids)) {
                $this->db->select('staffid, default_service_item');
                $this->db->where_in('staffid', $doctor_ids);
                $doc_items = $this->db->get(db_prefix() . 'staff')->result_array();

                foreach ($doc_items as $item) {
                    $doctor_service_items[$item['staffid']] = $item['default_service_item'];
                }
            }
        }
        $data['doctor_service_items'] = $doctor_service_items;

        $data['care_titles'] = $this->patients_model->get_name_care_titles();
        $data['referral_labs'] = $this->patients_model->get_staff_by_role(['Referral Lab']);
        $data['companies'] = $this->patients_model->get_staff_by_role(['Company']);

        $data['payment_modes'] = $this->payment_modes_model->get('', [], false);
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        // Load Item Groups for Filter Tabs
        $data['item_groups'] = $this->invoice_items_model->get_groups();

        // FILTER RESTRICTED GROUPS
        $restricted_groups = get_option('patients_restricted_groups');
        $restricted_groups = !empty($restricted_groups) ? json_decode($restricted_groups, true) : [];

        if (!empty($restricted_groups)) {
            $data['item_groups'] = array_filter($data['item_groups'], function ($group) use ($restricted_groups) {
                return !in_array($group['id'], $restricted_groups);
            });
        }

        // Load Lab Tests Statuses from Master Data
        $this->load->model('master_data/master_data_model');

        // New Grouped Statuses
        $all_statuses = $this->master_data_model->get_item_statuses();
        $grouped_statuses = [];
        foreach ($all_statuses as $status) {
            $grouped_statuses[$status['group_id']][] = $status;
        }
        $data['item_statuses_grouped'] = $grouped_statuses;

        // Legacy/Default status array for view safety
        $data['lab_test_statuses'] = [];

        // Toggle text logic variables for patient_details partial
        $data['show_optional'] = false;
        $data['toggle_text'] = '+ Show Optional Fields';

        $this->load->view('add_visit', $data);
    }
    public function search_patient_by_mobile()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $term = $this->input->get('term');

        $this->db->select('userid, company as full_name, phonenumber');
        $this->db->select(db_prefix() . 'patients_extra.mr_number, ' . db_prefix() . 'patients_extra.mobile_number as extra_mobile');

        // Optimize: Check today's visit in main query
        $today = date('Y-m-d');
        $this->db->select('(SELECT id FROM ' . db_prefix() . 'visits WHERE patient_id = ' . db_prefix() . 'clients.userid AND DATE(created_at) = "' . $today . '" ORDER BY created_at DESC LIMIT 1) as today_visit_id');

        $this->db->like('phonenumber', $term);
        $this->db->or_like('company', $term);

        $this->db->from(db_prefix() . 'clients');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'clients.userid', 'left');

        $results = $this->db->get()->result_array();

        $output = [];
        foreach ($results as $row) {
            $mobile = $row['extra_mobile'] ? $row['extra_mobile'] : $row['phonenumber'];

            $label = $row['full_name'] . ' (' . $mobile . ')';
            if ($row['mr_number']) {
                $label .= ' - ' . $row['mr_number'];
            }

            $has_visit_today = !empty($row['today_visit_id']);

            $output[] = [
                'label' => $label,
                'value' => $mobile, // Put mobile in input
                'id' => $row['userid'],
                'has_visit_today' => $has_visit_today
            ];
        }

        echo json_encode($output);
    }

    public function search_patient()
    {
        if (!has_permission('visits', '', 'view')) {
            ajax_access_denied();
        }
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $mobile = $this->input->post('mobile');
        $patients = $this->patients_model->get_patient_by_mobile($mobile);

        echo json_encode($patients);
    }

    public function get_patient_json($id)
    {
        if (!has_permission('visits', '', 'view')) {
            ajax_access_denied();
        }
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = $this->patients_model->get($id);
        echo json_encode($data);
    }

    public function get_visit_details($invoice_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $tests = $this->patients_model->get_visit_tests($invoice_id);
        echo json_encode($tests);
    }

    public function search_items()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $term = $this->input->post('q');

        // Robust Tests Group Lookup
        $tests_group_id = 0;
        $this->db->select('id');
        $this->db->group_start();
        $this->db->where('name', 'Tests');
        $this->db->or_where('name', 'tests');
        $this->db->or_where('name', 'TESTS');
        $this->db->group_end();
        $group_query = $this->db->get(db_prefix() . 'items_groups')->row();

        if ($group_query) {
            $tests_group_id = $group_query->id;
        }

        // Base Select
        $this->db->select('id, description, rate, unit, group_id, code');

        // Build Search Query
        $this->db->group_start();
        $this->db->like('description', $term);
        $this->db->or_like('long_description', $term);
        $this->db->or_like('code', $term);
        $this->db->group_end();

        // Filter Restricted Groups
        $restricted_groups = get_option('patients_restricted_groups');
        $restricted_groups = !empty($restricted_groups) ? json_decode($restricted_groups, true) : [];

        if (!empty($restricted_groups)) {
            $this->db->where_not_in('group_id', $restricted_groups);
        }

        // Feature: Filter by specific group (e.g. for Estimate Test Search)
        $group_id = $this->input->post('group_id');
        if ($group_id) {
            $this->db->where('group_id', $group_id);
        }

        // Apply is_active check for Tests group if it exists
        if ($tests_group_id) {
            $this->db->group_start();
            $this->db->where('group_id !=', $tests_group_id);
            $this->db->or_group_start();
            $this->db->where('group_id', $tests_group_id);
            $this->db->where('is_active', 1);
            $this->db->group_end();
            $this->db->group_end();
        }

        $items = $this->db->get(db_prefix() . 'items')->result_array();

        $result = [];
        foreach ($items as $item) {

            // Fetch group name for subtext
            $this->db->select('name');
            $this->db->where('id', $item['group_id']);
            $group = $this->db->get(db_prefix() . 'items_groups')->row();
            $group_name = $group ? $group->name : '';

            $item_name = $item['description'];
            if (!empty($item['code'])) {
                $item_name .= ' (' . $item['code'] . ')';
            }

            $result[] = [
                'id' => $item['id'],
                'name' => $item_name,
                'subtext' => $group_name
            ];
        }
        echo json_encode($result);
    }

    /* Print Invoice */
    public function print_invoice($visit_id)
    {
        if (!$visit_id) {
            show_404();
        }

        // Get Visit & Patient Data
        // Need to find patient_id from visit_id first.
        // Visits table: id, patient_id, invoice_id...
        $this->db->where('id', $visit_id);
        $visit = $this->db->get(db_prefix() . 'visits')->row();

        if (!$visit) {
            show_404();
        }

        $patient = $this->patients_model->get($visit->patient_id);
        $tests = $this->patients_model->get_visit_tests($visit->invoice_id);

        // Get Invoice Data
        $this->db->where('id', $visit->invoice_id);
        $invoice = $this->db->get(db_prefix() . 'invoices')->row();

        // Get Default Invoice Template
        $this->db->where('type', 'Invoice');
        $this->db->where('is_default', 1);
        $template = $this->db->get(db_prefix() . 'print_templates')->row();

        if (!$template) {
            echo "No default 'Invoice' print template found. Please configure one in Setup -> Print Templates.";
            die;
        }

        $content = $template->content;

        // Expand Nested Template Tags
        $content = $this->print_templates_model->expand_template_tags($content);

        // Calculate Paid & Due
        $paid = $this->patients_model->get_invoice_paid($visit->invoice_id);
        $due = $invoice->total - $paid;

        // Fetch Additional Names
        $attender_title = '';
        if (isset($patient->attender_title_id) && $patient->attender_title_id) {
            $at_row = $this->db->get_where(db_prefix() . 'name_care_titles', ['id' => $patient->attender_title_id])->row();
            if ($at_row)
                $attender_title = $at_row->name;
        }

        $primary_doctor_name = '';
        if ($visit->primary_doctor_id) {
            $primary_doctor_name = get_staff_full_name($visit->primary_doctor_id);
        }

        $referral_lab_name = '';
        if ($visit->referral_lab_id) {
            $referral_lab_name = get_staff_full_name($visit->referral_lab_id);
        }

        $company_name_patient = '';
        if ($visit->company_id) {
            $company_name_patient = get_staff_full_name($visit->company_id);
        }

        $dob_formatted = '';
        if ($patient->dob && $patient->dob != '0000-00-00') {
            $dob_formatted = _d($patient->dob);
        }

        $base_currency = $this->currencies_model->get_base_currency();

        // Company/System Info
        $company_name = get_option('invoice_company_name');
        $company_address = get_option('invoice_company_address');
        $company_city = get_option('invoice_company_city');
        $company_state = get_option('company_state');
        $company_zip = get_option('invoice_company_postal_code');
        $company_country_code = get_country_short_name(get_option('invoice_company_country_code'));
        $company_country = get_country_name(get_option('invoice_company_country_code'));
        $company_phone = get_option('invoice_company_phonenumber');
        $company_logo = '';
        $company_main_domain = get_option('main_domain');

        // Placeholders Replacement (Standard + User's Custom)
        $replacements = [
            // Standard
            '{patient_name}' => $patient->full_name,
            '{mr_number}' => $patient->mr_number,
            '{invoice_number}' => $invoice->number,
            '{visit_date}' => _d($visit->created_at),
            '{doctor_name}' => get_staff_full_name($visit->referral_doctor_id), // FIXED: Property name
            // '{total_amount}' => app_format_money($invoice->total, $this->currencies_model->get_base_currency()), // Commented out to avoid duplication with lower definition

            // User's Template Placeholders
            '{PatientID}' => $patient->mr_number, // Mapping MR No to PatientID as per output "MR No / ID"
            '{VisitID}' => $visit->visit_code,
            '{visit_id}' => $visit->visit_code, // Fixing visit_id tag
            '{BillDate}' => _d($invoice->date), // Or visit date
            '{BillTime}' => date('H:i', strtotime($visit->created_at)),
            '{PatientName}' => $patient->full_name,
            '{Age}' => $patient->age . ' ' . $patient->age_unit,
            '{Gender}' => $patient->gender,
            '{FamilyHead}' => $patient->attender_name, // S/D/W Of
            '{PhoneNo}' => $patient->phonenumber,
            '{ReceiptNo}' => $invoice->number,

            // New User Requested Tags
            '{todays_date}' => _d(date('Y-m-d')),
            '{todays_date_time}' => _dt(date('Y-m-d H:i:s')),
            '{print_user_name}' => get_staff_full_name(get_staff_user_id()),
            '{NetAmount}' => app_format_money($invoice->total, $base_currency),
            '{Total}' => app_format_money($invoice->total, $base_currency),
            '{total_amount}' => ((float) $invoice->total > 0) ? app_format_money($invoice->total, $base_currency) : '',
            '{Discount}' => app_format_money($invoice->discount_total, $base_currency),
            '{Paid}' => ((float) $paid > 0) ? app_format_money($paid, $base_currency) : '',
            '{Balance}' => ((float) $due > 0) ? app_format_money($due, $base_currency) : '',
            '{ReceivedBy}' => get_staff_full_name($invoice->sale_agent),
            '{PaymentRemarks}' => '', // Placeholder for backward compatibility
            '{payment_remark}' => '', // Invoice context usually has payment array, not single note. Default to empty.
            '{received_by}' => get_staff_full_name($invoice->sale_agent),

            // New Tags
            '{uid_no}' => $patient->uid_no,
            '{patient_title}' => isset($patient->title) ? $patient->title : '',
            '{age_unit}' => $patient->age_unit,
            '{patient_dob}' => $dob_formatted,
            '{primary_doctor_name}' => $primary_doctor_name,
            '{referral_doctor_name}' => get_staff_full_name($visit->referral_doctor_id),
            '{attender_name}' => $patient->attender_name,
            '{attender_title}' => $attender_title,
            '{patient_email}' => $patient->email,
            '{patient_address}' => $patient->address,
            '{patient_referral_lab}' => $referral_lab_name,
            '{patient_company}' => $company_name_patient,

            // Company Info
            '{company_name}' => $company_name,
            '{company_address}' => $company_address,
            '{company_city}' => $company_city,
            '{company_state}' => $company_state,
            '{company_zip}' => $company_zip,
            '{company_country}' => $company_country,
            '{company_country_code}' => $company_country_code,
            '{company_zip_code}' => $company_zip,
            '{company_main_domain}' => $company_main_domain,
            '{company_phone}' => $company_phone,
            '{company_logo}' => $company_logo,
            '{logo}' => $company_logo,
        ];

        // Items Table Generation
        $items_html = '<table width="100%" border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
        $items_html .= '<thead><tr><th style="width: 5%;">#</th><th>Test Name</th><th style="text-align: right;">Price</th></tr></thead>';
        $items_html .= '<tbody>';
        $i = 1;
        foreach ($tests as $test) {
            $items_html .= '<tr>';
            $items_html .= '<td>' . $i++ . '</td>';
            // FIXED: Keys now match model output
            $desc = isset($test['description']) ? $test['description'] : $test['test_name'];
            $items_html .= '<td>' . $desc . '</td>';
            $items_html .= '<td style="text-align: right;">' . app_format_money($test['rate'], $this->currencies_model->get_base_currency()) . '</td>';
            $items_html .= '</tr>';
        }
        $items_html .= '</tbody></table>';

        $replacements['{items_table}'] = $items_html;

        // Hint for unresolvable list items
        $replacements['{ItemName}'] = 'Please use {items_table} for list of items';

        foreach ($replacements as $key => $val) {
            $content = str_replace($key, (string) $val, $content);
        }

        $data['title'] = 'Print Invoice';
        $data['content'] = $content;
        $this->load->view('print_invoice', $data);
    } // End print_invoice

    /* Print Receipt */
    public function print_receipt($payment_id)
    {
        if (!$payment_id) {
            show_404();
        }
        $this->load->model('payments_model');
        $payment = $this->payments_model->get($payment_id);
        if (!$payment) {
            show_404();
        }
        echo $this->_prepare_print_data_content($payment->invoiceid, $payment_id, 'OP Bill'); // Default to OP Bill until user asks otherwise, handles refunds internally
    }


    public function get_doctor_consultation_fee_item($doctor_id)
    {
        if (!$doctor_id) {
            echo json_encode(['item_id' => null]);
            return;
        }
        $this->db->select('default_service_item');
        $this->db->where('staffid', $doctor_id);
        $doctor = $this->db->get(db_prefix() . 'staff')->row();

        echo json_encode(['item_id' => $doctor ? $doctor->default_service_item : null]);
    }

    /* Modals for AJAX Loading */
    public function get_refunds_modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model('refunds/refunds_model');
        $invoice_id = $this->input->get('invoice_id');
        $data['invoice_id'] = $invoice_id;
        $data['refunds'] = $this->refunds_model->get_refunds_by_invoice($invoice_id);
        $this->load->view('modals/refunds', $data);
    }



    /* AJAX Partial Loaders Removed - Reverted to Standard Load */

    public function get_item_details($item_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->model('invoice_items_model');
        $this->load->model('b2b_labs/B2b_labs_model');

        $item = $this->invoice_items_model->get($item_id);

        if (!$item) {
            echo json_encode(['error' => 'Item not found']);
            return;
        }

        $referral_lab_id = $this->input->get('referral_lab_id');
        $final_price = $item->rate;
        $price_source = 'standard';

        // 1. Check Referral Price
        if ($referral_lab_id) {
            $referral_price = $this->B2b_labs_model->get_test_price($referral_lab_id, $item_id);
            if ($referral_price !== null && $referral_price > 0) {
                $final_price = $referral_price;
                $price_source = 'referral';
            } else {
                // 2. Fallback to B2B Price
                // Check if item has b2b_price property. 
                // The get() method returns object with properties. Model doesn't explicitly Select *, so we need to valid if b2b_price is returned.
                // The model get() explicitly selects columns. We need to make sure b2b_price is selected OR we need to fetch it if missing.
                // Let's check `invoice_items_model->get()`. It selects specific columns: id, rate, taxes, desc, etc.
                // It DOES NOT select b2b_price by default in standard get(). 
                // We need to fetch it manually or use a direct DB call for this specific attribute if missing.

                $this->db->select('b2b_price');
                $this->db->where('id', $item_id);
                $b2b_item = $this->db->get(db_prefix() . 'items')->row();

                if ($b2b_item && isset($b2b_item->b2b_price) && $b2b_item->b2b_price > 0) {
                    $final_price = $b2b_item->b2b_price;
                    $price_source = 'b2b';
                }
            }
        } else {
            // Even if no referral lab selected, we might want to default to B2B price implies "B2B Labs" are just staff, but "B2B Price" might be a general price? 
            // Requirement says: "referral lab having "referral price" if the value is zero or not found then use B2B Price"
            // This implies B2B Price is a fallback for Referral Labs. 
            // If NO referral lab is selected, we should probably stick to Standard Price (Patient Price).
        }

        $item->rate = $final_price;
        $item->price_source = $price_source; // For debugging/UI indication

        echo json_encode($item);
    }
    public function print_estimate()
    {
        if (!$this->input->post()) {
            redirect(admin_url('patients/visits'));
        }

        $items = $this->input->post('items');
        $lab_name = $this->input->post('lab_name');
        $total = $this->input->post('total');

        if (empty($items))
            $items = [];

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Estimate</title>
            <style>
                body { font-family: Helvetica, Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .text-right { text-align: right; }
                .header { text-align: center; margin-bottom: 30px; }
            </style>
        </head>
        <body onload="window.print()">
            <div class="header">
                <h2>Estimate</h2>
            </div>
            
            <p><strong>Referral Lab:</strong> ' . ($lab_name ? $lab_name : 'N/A') . '</p>
            <p><strong>Date:</strong> ' . _d(date('Y-m-d')) . '</p>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Test items</th>
                        <th class="text-right">Price</th>
                    </tr>
                </thead>
                <tbody>';

        $i = 1;
        foreach ($items as $item) {
            $html .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . $item['description'] . '</td>
                        <td class="text-right">' . app_format_money($item['rate'], $this->currencies_model->get_base_currency()) . '</td>
                      </tr>';
        }

        $html .= '</tbody>
                  <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">Total</th>
                        <th class="text-right">' . app_format_money($total, $this->currencies_model->get_base_currency()) . '</th>
                    </tr>
                  </tfoot>
            </table>
        </body>
        </html>';

        echo $html;
    }
    public function get_estimate_modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->model('invoice_items_model');
        // Load Item Groups for Filter Tabs
        $item_groups = $this->invoice_items_model->get_groups();

        $data['referral_labs'] = $this->patients_model->get_staff_by_role(['Referral Lab']);

        // Identify Tests Group ID for Filtering
        $tests_group_id = 0;
        if (isset($item_groups)) {
            foreach ($item_groups as $grp) {
                if (strtolower($grp['name']) == 'tests') {
                    $tests_group_id = $grp['id'];
                    break;
                }
            }
        }
        $data['tests_group_id'] = $tests_group_id;

        $this->load->view('modals/estimate_modal', $data);
    }

    public function get_history_modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $invoice_id = $this->input->get('invoice_id');
        $this->load->model('invoices_model');
        $this->load->model('payments_model');

        $invoice = $this->invoices_model->get($invoice_id);
        $data['invoice'] = $invoice;
        $data['payments'] = $this->payments_model->get_invoice_payments($invoice_id);

        // Activity Logs
        $data['activity_log'] = [];
        if ($invoice) {
            // Filter logs related to this patient
            // Typically looking for Client ID in description or specific rel_id if available (custom)
            // Using loose match on ID pattern from Patients_model log_activity
            $term = '[ID: ' . $invoice->clientid;

            $this->db->select(db_prefix() . 'activity_log.*, CONCAT(firstname, " ", lastname) as staff_name');
            $this->db->from(db_prefix() . 'activity_log');
            $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'activity_log.staffid', 'left');
            $this->db->like('description', $term);
            $this->db->order_by('date', 'DESC');
            $data['activity_log'] = $this->db->get()->result_array();
        }

        $this->load->view('modals/history', $data);
    }
    public function get_sidebar_patients()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $search = $this->input->get('search');
        $date = $this->input->get('date');
        $this->load->model('patients_model');

        // Filters
        $filters = [];
        if (!empty($search)) {
            $filters['search'] = $search;
        }

        // Date Filter
        if (!empty($date)) {
            $filters['from_date'] = to_sql_date($date);
            $filters['to_date'] = to_sql_date($date);
        } else {
            // Default to Today if no date provided?
            // Actually, if date is empty from frontend, maybe show recent?
            // But frontend defaults to Today.
            // Let's safe fallback to Today if null/empty to avoid loading all history
            $filters['from_date'] = date('Y-m-d');
            $filters['to_date'] = date('Y-m-d');
        }

        // Fetch recent visits (limit 50)
        // Using get_all_visits with limit
        $visits = $this->patients_model->get_all_visits(50, 0, $filters);

        if (empty($visits)) {
            echo '<li class="list-group-item text-center">No patients found</li>';
            return;
        }

        foreach ($visits as $visit) {
            // Determine Status Color
            $status_color = 'text-warning'; // Default

            // Calculate Pay Status
            $paid = $visit['total_paid'] ? $visit['total_paid'] : 0;
            $total = $visit['invoice_amount'];
            $status_text = 'Unpaid';
            $status_class = 'text-danger';

            if ($paid >= $total && $total > 0) {
                $status_text = 'Paid';
                $status_class = 'text-success';
            } elseif ($paid > 0) {
                $status_text = 'Partial';
                $status_class = 'text-warning';
            }

            // Date Format: 26 Jan 2026, 06:36 PM
            $date = date('d M Y, h:i A', strtotime($visit['created_at']));
            $url = admin_url('patients/visits/add/' . $visit['patient_id']);

            echo '<li class="list-group-item visit-item" style="cursor: pointer;" onclick="window.location.href=\'' . $url . '\'">';
            echo '<div style="font-weight: bold; color: #337ab7;">' . $visit['patient_name'] . '</div>';
            echo '<div style="font-size: 12px; color: #777;">' . $date . '</div>';
            echo '<div style="font-size: 11px;"><span class="' . $status_class . '">Pay Status: ' . $status_text . '</span></div>';
            echo '</li>';
        }
    }

    private function _prepare_print_data_content($invoice_id, $payment_id = null, $template_type = 'OP Bill')
    {
        $this->load->model('payments_model');
        $this->load->model('invoices_model');
        $this->load->model('refunds/refunds_model');

        // Get Invoice Data
        $invoice = $this->invoices_model->get($invoice_id);
        if (!$invoice) {
            show_404();
        }

        // Get Visit Data
        $this->db->where('invoice_id', $invoice->id);
        $visit = $this->db->get(db_prefix() . 'visits')->row();

        // Get Patient Data
        $patient = $this->patients_model->get($invoice->clientid);

        // Refund Logic overrides template
        $refund_id = $this->input->get('refund_id');
        if ($refund_id) {
            $template_type = 'Refund Bill';
        }

        // Get Template
        $this->db->where('type', $template_type);
        $this->db->where('is_default', 1);
        $template = $this->db->get(db_prefix() . 'print_templates')->row();

        if (!$template) {
            return "No default '" . $template_type . "' print template found. Please configure one in Setup -> Print Templates.";
        }

        $content = $template->content;
        $content = $this->print_templates_model->expand_template_tags($content);

        // Refund Data
        $refund_data = false;
        if ($refund_id) {
            $refund_data = $this->refunds_model->get($refund_id);
        }

        // Payment Data
        $payment = null;
        if ($payment_id) {
            $payment = $this->payments_model->get($payment_id);
        } else {
            // Try to find a payment for context tags (e.g. PaymentMode)
            $payments = $this->payments_model->get_invoice_payments($invoice_id);
            if (!empty($payments)) {
                // Use latest payment
                $payment = (object) $payments[0];
            }
        }

        // Financials
        $paid = $this->patients_model->get_invoice_paid($invoice->id);
        $due = $invoice->total - $paid;

        // Items HTML
        $tests = $this->patients_model->get_visit_tests($invoice->id);
        $base_currency = $this->currencies_model->get_base_currency();

        $items_html = '<table width="100%" border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
        $items_html .= '<thead><tr><th style="width: 5%;">#</th><th>Item</th><th style="text-align: right;">Price</th></tr></thead>';
        $items_html .= '<tbody>';
        $i = 1;
        foreach ($tests as $test) {
            $items_html .= '<tr>';
            $items_html .= '<td>' . $i++ . '</td>';
            $desc = isset($test['description']) ? $test['description'] : $test['test_name'];
            $items_html .= '<td>' . $desc . '</td>';
            $items_html .= '<td style="text-align: right;">' . app_format_money($test['rate'], $base_currency) . '</td>';
            $items_html .= '</tr>';
        }
        $items_html .= '</tbody></table>';


        // Company Info
        $company_name = get_option('invoice_company_name');
        $company_address = get_option('invoice_company_address');
        $company_city = get_option('invoice_company_city');
        $company_state = get_option('company_state');
        $company_zip = get_option('invoice_company_postal_code');
        $company_phone = get_option('invoice_company_phonenumber');
        $company_country_code = get_country_short_name(get_option('invoice_company_country_code'));
        $company_country = get_country_name(get_option('invoice_company_country_code'));
        $company_main_domain = get_option('main_domain');
        $company_logo = get_option('company_logo') ? '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="img-responsive">' : '';


        // Additional Names
        $attender_title = '';
        if (isset($patient->attender_title_id) && $patient->attender_title_id) {
            $at_row = $this->db->get_where(db_prefix() . 'name_care_titles', ['id' => $patient->attender_title_id])->row();
            if ($at_row)
                $attender_title = $at_row->name;
        }

        $primary_doctor_name = '';
        $referral_doctor_name = '';
        $referral_lab_name = '';
        $company_name_patient = '';

        if ($visit) {
            if ($visit->primary_doctor_id)
                $primary_doctor_name = get_staff_full_name($visit->primary_doctor_id);
            if ($visit->referral_doctor_id)
                $referral_doctor_name = get_staff_full_name($visit->referral_doctor_id);
            if ($visit->referral_lab_id)
                $referral_lab_name = get_staff_full_name($visit->referral_lab_id);
            if ($visit->company_id)
                $company_name_patient = get_staff_full_name($visit->company_id);
        }

        $dob_formatted = ($patient->dob && $patient->dob != '0000-00-00') ? _d($patient->dob) : '';

        // Amount in Words
        $this->load->library('app_number_to_word', [], 'numberword');
        $amount_to_convert = $invoice->total; // Default to invoice total logic

        if ($payment) {
            $amount_to_convert = $payment->amount;
        }
        if ($refund_data) {
            $amount_to_convert = $refund_data->amount;
        }

        $amount_to_convert = (float) $amount_to_convert;
        $amount_in_words = $this->numberword->convert($amount_to_convert, $base_currency->name);

        // Replacements
        $replacements = [
            '{items_table}' => $items_html,
            '{patient_name}' => $patient->full_name,
            '{mr_number}' => $patient->mr_number,
            '{uid_no}' => $patient->uid_no,
            '{PatientID}' => $patient->mr_number,
            '{PatientName}' => $patient->full_name,
            '{Age}' => $patient->age . ' ' . $patient->age_unit,
            '{Gender}' => $patient->gender,
            '{PhoneNo}' => $patient->phonenumber,
            '{patient_address}' => $patient->address,
            '{patient_email}' => $patient->email,
            '{patient_title}' => isset($patient->title) ? $patient->title : '',
            '{patient_dob}' => $dob_formatted,

            '{VisitID}' => $visit ? $visit->visit_code : '',
            '{visit_id}' => $visit ? $visit->visit_code : '',
            '{visit_date}' => $visit ? _d($visit->created_at) : '',
            '{BillTime}' => $visit ? date('H:i', strtotime($visit->created_at)) : '',

            '{invoice_number}' => $invoice->number,
            '{BillNo}' => $invoice->number,
            '{Date}' => _d($invoice->date),
            '{BillDate}' => _d($invoice->date),

            '{TotalBill}' => app_format_money($invoice->total, $base_currency),
            '{Total}' => app_format_money($invoice->total, $base_currency),
            '{net_amount}' => app_format_money($invoice->total, $base_currency),
            '{Amount}' => app_format_money($invoice->total, $base_currency),
            '{Discount}' => app_format_money($invoice->discount_total, $base_currency),

            '{TotalPaid}' => app_format_money($paid, $base_currency),
            '{Paid}' => ((float) $paid > 0) ? app_format_money($paid, $base_currency) : '0.00',
            '{Balance}' => app_format_money($due, $base_currency),

            '{amount_in_words}' => $amount_in_words,

            // Payment/Refund Specifics
            '{ReceiptNo}' => $refund_data ? $refund_data->id : ($payment ? $payment->paymentid : '-'),
            '{payment_date}' => $refund_data ? _d($refund_data->refunded_on) : ($payment ? _d($payment->date) : ''),
            '{PaymentMode}' => $refund_data ? $refund_data->payment_mode : ($payment ? $payment->name : ''),
            '{payment_mode}' => $refund_data ? $refund_data->payment_mode : ($payment ? $payment->name : ''),
            '{amount_paid}' => app_format_money($refund_data ? $refund_data->amount : ($payment ? $payment->amount : 0), $base_currency),
            '{TransactionID}' => $payment ? $payment->transactionid : '',
            '{Note}' => $payment ? $payment->note : '',
            '{payment_remark}' => $payment ? $payment->note : '',

            '{primary_doctor_name}' => $primary_doctor_name,
            '{referral_doctor_name}' => $referral_doctor_name,
            '{patient_referral_lab}' => $referral_lab_name,
            '{patient_company}' => $company_name_patient,
            '{attender_name}' => $patient->attender_name,
            '{attender_title}' => $attender_title,

            '{print_user_name}' => get_staff_full_name(get_staff_user_id()),
            '{received_by}' => get_staff_full_name(),
            '{todays_date}' => _d(date('Y-m-d')),
            '{todays_date_time}' => _dt(date('Y-m-d H:i:s')),

            // Comp Info
            '{company_name}' => $company_name,
            '{company_address}' => $company_address,
            '{company_city}' => $company_city,
            '{company_state}' => $company_state,
            '{company_zip}' => $company_zip,
            '{company_phone}' => $company_phone,
            '{company_main_domain}' => $company_main_domain,
            '{company_country}' => $company_country,
            '{company_country_code}' => $company_country_code,
            '{company_zip_code}' => $company_zip,
            '{company_logo}' => $company_logo,
            '{logo}' => $company_logo,
        ];

        foreach ($replacements as $key => $val) {
            $content = str_replace($key, (string) $val, $content);
        }

        $html = '<!DOCTYPE html><html><head><title>Print</title>';
        $html .= '<style>body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; } @media print { @page { margin: 0; } body { margin: 1.6cm; } .no-print { display: none; } }</style>';
        $html .= '</head><body>';
        $html .= '<div class="no-print" style="margin-bottom: 20px;">
                <button onclick="window.print();">Print</button>
                <a href="' . admin_url('patients/visits') . '">Back to Visits</a>
            </div>';
        $html .= $content;
        $html .= '<script>window.onload = function () { window.print(); }</script>';
        $html .= '</body></html>';

        return $html;
    }
}
