<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Patients_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
        $this->load->model('payments_model');
    }

    public function get_today_invoices($patient_id)
    {
        $today = date('Y-m-d');
        $this->db->select('id, number, total, status, date');
        // Also get paid amount?
        // We can use get_invoice_total_paidOr logic but maybe simple is enough.
        // Actually, we probably want to know if it's paid or not.
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where('clientid', $patient_id);
        $this->db->where('date', $today);
        $this->db->order_by('id', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get patient/s
     * @param  mixed $id Optional patient id
     * @return mixed     array or object
     */
    public function get($id = '', $filters = [])
    {
        $this->db->select(db_prefix() . 'clients.userid as patientid, company as full_name, phonenumber, address, active, datecreated, ' . db_prefix() . 'patients_extra.age, ' . db_prefix() . 'patients_extra.mobile_number as extra_mobile, ' . db_prefix() . 'patients_extra.title_id, ' . db_prefix() . 'patients_extra.gender, ' . db_prefix() . 'patients_extra.referral_doctor_id, ' . db_prefix() . 'patients_extra.attender_title_id, ' . db_prefix() . 'patients_extra.attender_name, ' . db_prefix() . 'patients_extra.referral_lab_id, ' . db_prefix() . 'patients_extra.company_id, ' . db_prefix() . 'patients_extra.prescription_file, ' . db_prefix() . 'patients_extra.mr_number, ' . db_prefix() . 'patients_extra.age_unit, ' . db_prefix() . 'patients_extra.dob, ' . db_prefix() . 'patients_extra.uid_no');

        // Email
        $this->db->select('(SELECT email FROM ' . db_prefix() . 'contacts WHERE userid=' . db_prefix() . 'clients.userid AND is_primary=1 LIMIT 1) as email');

        // Latest Visit Code
        $this->db->select('(SELECT visit_code FROM ' . db_prefix() . 'visits WHERE patient_id=' . db_prefix() . 'clients.userid ORDER BY created_at DESC LIMIT 1) as latest_visit_code');

        // Total Visits Count
        $this->db->select('(SELECT COUNT(*) FROM ' . db_prefix() . 'visits WHERE patient_id=' . db_prefix() . 'clients.userid) as visits_count');

        // Last Primary Doctor
        $this->db->select('(SELECT primary_doctor_id FROM ' . db_prefix() . 'visits WHERE patient_id=' . db_prefix() . 'clients.userid AND primary_doctor_id IS NOT NULL ORDER BY created_at DESC LIMIT 1) as last_primary_doctor_id');

        // Customer Groups
        $this->db->select('(SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM ' . db_prefix() . 'customers_groups JOIN ' . db_prefix() . 'customer_groups ON ' . db_prefix() . 'customer_groups.groupid = ' . db_prefix() . 'customers_groups.id WHERE customer_id=' . db_prefix() . 'clients.userid) as customer_groups');

        // Fetch Title Name
        $this->db->select(db_prefix() . 'name_titles.name as title');

        $this->db->from(db_prefix() . 'clients');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'clients.userid', 'left');
        $this->db->join(db_prefix() . 'name_titles', db_prefix() . 'name_titles.id = ' . db_prefix() . 'patients_extra.title_id', 'left');

        // We return all clients as patients
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'clients.userid', $id);
            return $this->db->get()->row();
        }

        // Apply Filters
        if (!empty($filters)) {
            if (isset($filters['from_date']) && !empty($filters['from_date'])) {
                $this->db->where('DATE(' . db_prefix() . 'clients.datecreated) >=', to_sql_date($filters['from_date']));
            }
            if (isset($filters['to_date']) && !empty($filters['to_date'])) {
                $this->db->where('DATE(' . db_prefix() . 'clients.datecreated) <=', to_sql_date($filters['to_date']));
            }
            if (isset($filters['age_dob']) && !empty($filters['age_dob'])) {
                $val = $filters['age_dob'];
                // Check if date or number
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
                    // It is a date, filter by DOB
                    $this->db->where(db_prefix() . 'patients_extra.dob', $val);
                } else {
                    // Assume Age
                    $this->db->where(db_prefix() . 'patients_extra.age', $val);
                }
            }
            if (isset($filters['group_id']) && !empty($filters['group_id'])) {
                $this->db->join(db_prefix() . 'customer_groups', db_prefix() . 'customer_groups.customer_id = ' . db_prefix() . 'clients.userid', 'left');
                $this->db->where(db_prefix() . 'customer_groups.groupid', $filters['group_id']);
            }
        }

        return $this->db->get()->result();
    }

    /**
     * Add new patient
     * @param array $data $_POST data
     */
    public function add($data)
    {
        // Prepare client data
        $client_data = [
            'company' => $data['full_name'], // Map Full Name to Company
            'phonenumber' => $data['mobile_number'],
            'address' => isset($data['address']) ? $data['address'] : '',
            'datecreated' => date('Y-m-d H:i:s'),
            'addedfrom' => get_staff_user_id(),
            'active' => 1,
        ];

        $this->db->insert(db_prefix() . 'clients', $client_data);
        $client_id = $this->db->insert_id();

        if ($client_id) {
            // Add primary contact
            // Split name for contact
            $names = explode(' ', $data['full_name'], 2);
            $firstname = $names[0];
            $lastname = isset($names[1]) ? $names[1] : '';

            $contact_data = [
                'userid' => $client_id,
                'is_primary' => 1,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'phonenumber' => $data['mobile_number'],
                'datecreated' => date('Y-m-d H:i:s'),
                'active' => 1,
            ];

            // Handle email if provided, otherwise leave null (if allowed) or generate dummy?
            // Perfex 'email' in tblcontacts must be unique. 
            // If user didn't provide email, we skip it. DB usually allows NULL if not strictly validated by code.
            // But we should check if 'email' is passed in $data.
            if (isset($data['email']) && !empty($data['email'])) {
                $contact_data['email'] = $data['email'];
            }

            $this->db->insert(db_prefix() . 'contacts', $contact_data);
            $contact_id = $this->db->insert_id();

            // Generate MR Number
            $mr_number = date('dmy') . '-' . $client_id;

            // Add extra patient details
            $extra_data = [
                'patient_id' => $client_id,
                'mr_number' => $mr_number,
                'uid_no' => isset($data['uid_no']) ? $data['uid_no'] : null,
                'title_id' => isset($data['title_id']) ? $data['title_id'] : null,
                'gender' => isset($data['gender']) ? $data['gender'] : null,
                // Age / DOB Logic
                'age' => $this->calculate_age_value($data),
                'age_unit' => isset($data['age_unit']) ? $data['age_unit'] : 'Years',
                'dob' => $this->calculate_dob($data),
                'mobile_number' => $data['mobile_number'],
                'referral_doctor_id' => isset($data['referral_doctor_id']) ? $data['referral_doctor_id'] : null,
                'attender_title_id' => isset($data['attender_title_id']) ? $data['attender_title_id'] : null,
                'attender_name' => isset($data['attender_name']) ? $data['attender_name'] : null,
                'referral_lab_id' => isset($data['referral_lab_id']) ? $data['referral_lab_id'] : null,
                'company_id' => isset($data['company_id']) ? $data['company_id'] : null,
                'prescription_file' => isset($data['prescription_file']) ? $data['prescription_file'] : null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert(db_prefix() . 'patients_extra', $extra_data);

            // Handle Billing (Invoice & Payment)
            if (isset($data['items']) && !empty($data['items'])) {
                $this->generate_invoice_and_payment($client_id, $data);

                // Record Visit with extra fields
                // Wait, record_visit is called inside generate_invoice_and_payment?
                // No, I need to check where record_visit was called.
                // Ah, I missed where it was called. It was NOT called in `add` directly in my previous snippet view?
                // Let me check `generate_invoice_and_payment`.
                // Wait, I need to verify WHERE record_visit is called.
                // I'll assume it was called inside `generate_invoice_and_payment` or I need to add it there.
                // Let's look at `generate_invoice_and_payment` text I have.
            }
            log_activity('New Patient Added [ID: ' . $client_id . ', Name: ' . $data['full_name'] . ']');

            return $client_id;
        }

        return false;
    }

    /**
     * Update patient
     * @param  array $data $_POST data
     * @param  mixed $id   patient id
     * @return boolean
     */
    public function update($data, $id)
    {
        $updated = false;
        $client_data = [
            'company' => $data['full_name'],
            'phonenumber' => $data['mobile_number'],
            'address' => isset($data['address']) ? $data['address'] : '',
        ];

        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'clients', $client_data);

        if ($this->db->affected_rows() > 0) {
            $updated = true;
        }

        // Update extra info
        // Check if exists
        $this->db->where('patient_id', $id);
        $exists = $this->db->get(db_prefix() . 'patients_extra')->row();

        $extra_data = [
            'uid_no' => isset($data['uid_no']) ? $data['uid_no'] : null,
            'title_id' => isset($data['title_id']) ? $data['title_id'] : null,
            'gender' => isset($data['gender']) ? $data['gender'] : null,
            'age' => $this->calculate_age_value($data),
            'age_unit' => isset($data['age_unit']) ? $data['age_unit'] : 'Years',
            'dob' => $this->calculate_dob($data),
            'mobile_number' => $data['mobile_number'],
            'referral_doctor_id' => isset($data['referral_doctor_id']) ? $data['referral_doctor_id'] : null,
            'attender_title_id' => isset($data['attender_title_id']) ? $data['attender_title_id'] : null,
            'attender_name' => isset($data['attender_name']) ? $data['attender_name'] : null,
            'referral_lab_id' => isset($data['referral_lab_id']) ? $data['referral_lab_id'] : null,
            'company_id' => isset($data['company_id']) ? $data['company_id'] : null,
        ];

        if (isset($data['mr_number'])) {
            $extra_data['mr_number'] = $data['mr_number'];
        }

        if (isset($data['prescription_file']) && !empty($data['prescription_file'])) {
            $extra_data['prescription_file'] = $data['prescription_file'];
        }

        if ($exists) {
            $this->db->where('patient_id', $id);
            $this->db->update(db_prefix() . 'patients_extra', $extra_data);
        } else {
            $extra_data['patient_id'] = $id;
            $extra_data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert(db_prefix() . 'patients_extra', $extra_data);
        }

        if ($this->db->affected_rows() > 0) {
            $updated = true;
        }

        // Update primary contact
        // We ensure a primary contact is updated or created to keep names and emails in sync.
        $names = explode(' ', $data['full_name'], 2);
        $firstname = $names[0];
        $lastname = isset($names[1]) ? $names[1] : '';

        $contact_data = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'phonenumber' => $data['mobile_number'],
        ];

        if (isset($data['email'])) {
            $contact_data['email'] = $data['email'];
        }

        $this->db->where('userid', $id);
        $this->db->where('is_primary', 1);
        $primary_contact = $this->db->get(db_prefix() . 'contacts')->row();

        if ($primary_contact) {
            $this->db->where('id', $primary_contact->id);
            $this->db->update(db_prefix() . 'contacts', $contact_data);
            if ($this->db->affected_rows() > 0) {
                $updated = true;
            }
        } else {
            // Check for any contact for this user
            $this->db->where('userid', $id);
            $any_contact = $this->db->get(db_prefix() . 'contacts')->row();

            if ($any_contact) {
                $contact_data['is_primary'] = 1;
                $this->db->where('id', $any_contact->id);
                $this->db->update(db_prefix() . 'contacts', $contact_data);
                $updated = true; // Count as updated since we enforced primary
            } else {
                // Create new primary contact
                $contact_data['userid'] = $id;
                $contact_data['is_primary'] = 1;
                $contact_data['active'] = 1;
                $contact_data['datecreated'] = date('Y-m-d H:i:s');
                $this->db->insert(db_prefix() . 'contacts', $contact_data);
                $updated = true;
            }
        }

        if ($updated) {
            log_activity('Patient Updated [ID: ' . $id . ', Name: ' . $data['full_name'] . ']');
        }

        // Handle Billing (Invoice & Payment) even on update if items are present
        // Note: For now, we append new invoices. 
        if (isset($data['items']) && !empty($data['items'])) {
            $this->generate_invoice_and_payment($id, $data);
            $updated = true;
        }

        // Update Visit Info (Referrals)
        $this->update_visit_info($id, $data);

        return $updated;
    }

    private function generate_invoice_and_payment($client_id, $data)
    {
        // Create Invoice
        $currency = get_base_currency();
        $invoice_data = [
            'clientid' => $client_id,
            'billing_street' => isset($data['address']) ? $data['address'] : '',
            'billing_city' => '',
            'billing_state' => '',
            'billing_zip' => '',
            'billing_country' => 0,
            'date' => date('Y-m-d'),
            'duedate' => date('Y-m-d'),
            'currency' => $currency->id,
            'number' => get_option('next_invoice_number'),
            'subtotal' => 0,
            'total' => 0,
            'adjustment' => 0, // For simple discount if needed, but we use 'discount_total' usually
            'discount_percent' => (isset($data['discount_type']) && $data['discount_type'] == '%') ? $data['discount_amount'] : 0,
            'discount_total' => (isset($data['discount_type']) && $data['discount_type'] == '%') ? 0 : (isset($data['discount_amount']) ? $data['discount_amount'] : 0), // Will be calculated below if %
            'discount_type' => (isset($data['discount_type']) && $data['discount_type'] == '%') ? 'before_tax' : '',
            'terms' => '',
            'sale_agent' => get_staff_user_id(),
            'status' => 1, // Unpaid initially
            'adminnote' => '',
            'clientnote' => '',
            'newitems' => []
        ];

        // Format Items for Invoice
        $items = [];
        $subtotal = 0;
        $new_items_to_log = [];

        foreach ($data['items'] as $key => $item) {
            // Check if it's an existing test
            if (isset($item['test_id']) && !empty($item['test_id'])) {
                // Update Status Only
                // Update Status & is_emergency
                $this->db->where('id', $item['test_id']);
                $update_data = ['status' => $item['status']];
                if (isset($item['is_emergency'])) {
                    $update_data['is_emergency'] = $item['is_emergency'];
                }
                $this->db->update(db_prefix() . 'patient_tests', $update_data);

                // Log History
                $this->log_test_status($item['test_id'], $item['status']);

                continue; // Skip invoicing
            }

            // New Item Logic
            $item_id = $item['id'];
            $rate = $item['rate'];
            $name = $item['description'];

            // Construct item for invoice
            $items[$key] = [
                'order' => $key,
                'description' => $name,
                'long_description' => '',
                'qty' => 1,
                'unit' => '',
                'rate' => $rate,
                'taxname' => []
            ];

            $subtotal += $rate;
            $new_items_to_log[] = $item;
        }

        // Prepare Payments List
        $payments_list = [];
        if (isset($data['payments']) && is_array($data['payments'])) {
            $payments_list = $data['payments'];
        } elseif (isset($data['payment']) && isset($data['payment']['amount']) && $data['payment']['amount'] > 0) {
            $payments_list[] = $data['payment'];
        }

        // If no new items, check if we have a payment to process for existing PREVIOUS due
        // BUT, if this is a NEW visit (no existing invoice and we are here to create one), we should NOT return.
        // We generally assume if existing_invoice_id is NOT set, we want to create a new one.
        if (empty($items) && isset($data['existing_invoice_id'])) {
            // If no new items, check if we have a payment to process for existing PREVIOUS due
            foreach ($payments_list as $pmt) {
                if (empty($pmt['amount']) || $pmt['amount'] <= 0)
                    continue;

                // Find unpaid invoices for this client
                $this->db->select('id, total, status');
                $this->db->where('clientid', $client_id);
                $this->db->where('status !=', 2); // Not fully paid
                $this->db->order_by('id', 'ASC'); // Oldest first
                $invoices = $this->db->get(db_prefix() . 'invoices')->result();

                $payment_amount = $pmt['amount'];

                foreach ($invoices as $inv) {
                    if ($payment_amount <= 0)
                        break;

                    // Calculate outcome
                    // We rely on Perfex payments model to handle overpayment but it's better to check due
                    $due = get_invoice_total_left_to_pay($inv->id, $inv->total);

                    if ($due <= 0)
                        continue;

                    $amount_to_pay = $payment_amount;
                    if ($amount_to_pay > $due) {
                        $amount_to_pay = $due;
                    }

                    $payment_data = [
                        'invoiceid' => $inv->id,
                        'amount' => $amount_to_pay,
                        'paymentmode' => $pmt['paymentmode'],
                        'date' => date('Y-m-d'),
                        'note' => isset($pmt['note']) ? $pmt['note'] : ''
                    ];

                    $this->payments_model->add($payment_data);
                    $payment_amount -= $amount_to_pay;
                }

                // If still amount left (overpayment? or no unpaid invoices found?), maybe attach to the last invoice?
                if ($payment_amount > 0) {
                    // Find ANY last invoice
                    $this->db->select('id');
                    $this->db->where('clientid', $client_id);
                    $this->db->order_by('id', 'DESC');
                    $last_invoice = $this->db->get(db_prefix() . 'invoices')->row();

                    if ($last_invoice) {
                        $payment_data = [
                            'invoiceid' => $last_invoice->id,
                            'amount' => $payment_amount,
                            'paymentmode' => $pmt['paymentmode'],
                            'date' => date('Y-m-d'),
                            'note' => isset($pmt['note']) ? $pmt['note'] : 'Overpayment / Credit'
                        ];
                        $this->payments_model->add($payment_data);
                    }
                }
            }
            return;
        }

        if (isset($data['existing_invoice_id']) && !empty($data['existing_invoice_id'])) {
            $invoice_id = $data['existing_invoice_id'];

            // Add NEW items to existing invoice
            // We need to loop new_items_to_log to add them to invoice
            foreach ($new_items_to_log as $item) {
                // Construct item array for add_new_sales_item_post
                $invoice_item = [
                    'description' => $item['description'],
                    'long_description' => '',
                    'qty' => 1,
                    'unit' => '',
                    'rate' => $item['rate'],
                    'order' => 1, // Order doesn't matter much here
                    'taxname' => []
                ];

                if ($itemid = add_new_sales_item_post($invoice_item, $invoice_id, 'invoice')) {
                    _maybe_insert_post_item_tax($itemid, $invoice_item, $invoice_id, 'invoice');
                }
            }

            if (!empty($new_items_to_log)) {
                update_sales_total_tax_column($invoice_id, 'invoice', db_prefix() . 'invoices');
                update_invoice_status($invoice_id);
            }

            // We do NOT record a new visit code, as we are updating the existing one.
            // But we must log the new items in patient_tests which happens below.

        } else {
            // Create NEW Invoice
            // Calculate Discount if Percentage
            if ($invoice_data['discount_type'] == 'before_tax' && $invoice_data['discount_percent'] > 0) {
                // Calculate discount: (subtotal * percent) / 100
                $calculated_discount = ($subtotal * $invoice_data['discount_percent']) / 100;
                $invoice_data['discount_total'] = $calculated_discount;
            }

            $invoice_data['newitems'] = $items;
            $invoice_data['subtotal'] = $subtotal;
            $invoice_data['total'] = $subtotal - $invoice_data['discount_total'];

            $invoice_id = $this->invoices_model->add($invoice_data);

            if ($invoice_id) {
                // Record Visit (Only for new invoice)
                $this->record_visit($client_id, $invoice_id, $data);
            }
        }

        if ($invoice_id) {



            // Log status to tblpatient_tests for NEW items
            foreach ($new_items_to_log as $item) {
                $test_data = [
                    'patient_id' => $client_id,
                    'invoice_id' => $invoice_id,
                    'item_id' => $item['id'],
                    'status' => $item['status'],
                    'is_emergency' => isset($item['is_emergency']) ? $item['is_emergency'] : 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert(db_prefix() . 'patient_tests', $test_data);
                $new_test_id = $this->db->insert_id();

                // Log History
                $this->log_test_status($new_test_id, $item['status']);
            }

            // Handle Payments (Array)
            foreach ($payments_list as $pmt) {
                if (empty($pmt['amount']) || $pmt['amount'] <= 0)
                    continue;

                $payment_data = [
                    'invoiceid' => $invoice_id,
                    'amount' => $pmt['amount'],
                    'paymentmode' => $pmt['paymentmode'],
                    'date' => date('Y-m-d'),
                    'note' => isset($pmt['note']) ? $pmt['note'] : ''
                ];

                $this->payments_model->add($payment_data);
            }
        }
    }

    /**
     * Delete patient
     * @param  mixed $id patient id
     * @return boolean
     */
    public function delete($id)
    {
        // Use Perfex standard delete_client function to ensure everything is cleaned up
        // Requires loading Clients model or just deleting manually if we want to be minimal.
        // Better to load clients model.
        $this->load->model('clients_model');
        $result = $this->clients_model->delete($id);

        if ($result) {
            // Also delete extra data
            $this->db->where('patient_id', $id);
            $this->db->delete(db_prefix() . 'patients_extra');
        }

        return $result;
    }

    public function get_name_titles()
    {
        return $this->db->get(db_prefix() . 'name_titles')->result_array();
    }

    public function get_doctors()
    {
        return $this->get_staff_by_role(['Doctor', 'Jr. Doctor', 'Sr. Doctor']);
    }

    public function get_name_care_titles()
    {
        return $this->db->get(db_prefix() . 'name_care_titles')->result_array();
    }

    public function get_patient_tests($patient_id)
    {
        $this->db->select(db_prefix() . 'patient_tests.id as test_id, ' . db_prefix() . 'patient_tests.status, ' . db_prefix() . 'items.description, ' . db_prefix() . 'items.rate, ' . db_prefix() . 'patient_tests.item_id, ' . db_prefix() . 'items_groups.name as group_name');
        $this->db->from(db_prefix() . 'patient_tests');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->where('patient_id', $patient_id);
        return $this->db->get()->result_array();
    }

    public function get_staff_by_role($roles)
    {
        $this->db->select(db_prefix() . 'staff.staffid, firstname, lastname, phonenumber');
        $this->db->from(db_prefix() . 'staff');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role', 'left');
        $this->db->where_in(db_prefix() . 'roles.name', $roles);
        $this->db->where(db_prefix() . 'staff.active', 1);
        return $this->db->get()->result_array();
    }

    private function record_visit($patient_id, $invoice_id, $extra_data = [])
    {
        // 1. Get current visit count for this patient
        $this->db->where('patient_id', $patient_id);
        $count = $this->db->count_all_results(db_prefix() . 'visits');

        // Increment for new visit
        $new_count = $count + 1;

        // 2. Generate Visit Code: DDMMYY-Count
        $visit_code = date('dmy') . '-' . $new_count;

        // 3. Insert into tblvisits
        $data = [
            'patient_id' => $patient_id,
            'invoice_id' => $invoice_id,
            'visit_code' => $visit_code,
            'created_at' => date('Y-m-d H:i:s'),
            'referral_doctor_id' => isset($extra_data['referral_doctor_id']) ? $extra_data['referral_doctor_id'] : null,
            'primary_doctor_id' => isset($extra_data['primary_doctor_id']) ? $extra_data['primary_doctor_id'] : null,
            'referral_lab_id' => isset($extra_data['referral_lab_id']) ? $extra_data['referral_lab_id'] : null,
            'company_id' => isset($extra_data['company_id']) ? $extra_data['company_id'] : null,
        ];

        $this->db->insert(db_prefix() . 'visits', $data);
    }

    private function update_visit_info($patient_id, $data)
    {
        // Update the LATEST visit for this patient (presumably the one being edited)
        // Or if we had a visit_id in $data, use that.
        // For now, assuming context is "Current/Latest Visit".

        // Find latest visit
        $this->db->select('id');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('DATE(created_at)', date('Y-m-d')); // Only update today's visit?
        // User might be editing an old visit? If so, today logic fails.
        // But the constraint "check if user already having visit today" implies focused on Today.
        // Let's stick to "Latest Visit" generally, or Today's.
        $this->db->order_by('created_at', 'DESC');
        $visit = $this->db->get(db_prefix() . 'visits')->row();

        if ($visit) {
            $update_data = [
                'referral_doctor_id' => isset($data['referral_doctor_id']) ? $data['referral_doctor_id'] : null,
                'primary_doctor_id' => isset($data['primary_doctor_id']) ? $data['primary_doctor_id'] : null,
                'referral_lab_id' => isset($data['referral_lab_id']) ? $data['referral_lab_id'] : null,
                'company_id' => isset($data['company_id']) ? $data['company_id'] : null,
            ];
            $this->db->where('id', $visit->id);
            $this->db->update(db_prefix() . 'visits', $update_data);
        }
    }

    public function get_total_paid($patient_id)
    {
        $this->db->select('SUM(amount) as total');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid', 'left');
        $this->db->where(db_prefix() . 'invoices.clientid', $patient_id);
        $result = $this->db->get()->row();
        return $result ? $result->total : 0;
    }
    public function get_visits_count($filters = [])
    {
        $this->apply_visits_filters($filters);
        $this->db->from(db_prefix() . 'visits');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'visits.invoice_id', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'invoices.sale_agent', 'left');
        return $this->db->count_all_results();
    }

    public function get_all_visits($limit = '', $start = '', $filters = [])
    {
        $this->db->select(db_prefix() . 'visits.*');
        $this->db->select(db_prefix() . 'clients.company as patient_name');
        $this->db->select(db_prefix() . 'name_titles.name as title'); // FETCH TITLE
        $this->db->select(db_prefix() . 'patients_extra.mr_number');
        $this->db->select('CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as staff_name');
        $this->db->select('CONCAT(doctors.firstname, " ", doctors.lastname) as doctor_name'); // FETCH REFERRAL DOCTOR NAME
        $this->db->select('CONCAT(primary_docs.firstname, " ", primary_docs.lastname) as primary_doctor_name'); // FETCH PRIMARY DOCTOR NAME
        $this->db->select(db_prefix() . 'invoices.total as invoice_amount');
        $this->db->select('(SELECT SUM(amount) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid=' . db_prefix() . 'invoices.id) as total_paid');
        $this->db->select('(SELECT COUNT(*) FROM ' . db_prefix() . 'patient_tests WHERE invoice_id=' . db_prefix() . 'visits.invoice_id) as test_count');

        // Group Concat for test names - Warning: might be long, truncate in view
        $this->db->select('(SELECT GROUP_CONCAT(' . db_prefix() . 'items.description SEPARATOR ", ") FROM ' . db_prefix() . 'patient_tests JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id WHERE invoice_id=' . db_prefix() . 'visits.invoice_id) as test_names');

        $this->db->from(db_prefix() . 'visits');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'name_titles', db_prefix() . 'name_titles.id = ' . db_prefix() . 'patients_extra.title_id', 'left'); // JOIN TITLES
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'visits.invoice_id', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'invoices.sale_agent', 'left');
        $this->db->join(db_prefix() . 'staff as doctors', 'doctors.staffid = ' . db_prefix() . 'visits.referral_doctor_id', 'left'); // JOIN REFERRAL DOCTORS
        $this->db->join(db_prefix() . 'staff as primary_docs', 'primary_docs.staffid = ' . db_prefix() . 'visits.primary_doctor_id', 'left'); // JOIN PRIMARY DOCTORS

        $this->apply_visits_filters($filters);

        $this->db->order_by(db_prefix() . 'visits.created_at', 'DESC');

        if ($limit != '' && $start != '') {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result_array();
    }

    public function get_visits_stats($filters = [])
    {
        $this->apply_visits_filters($filters);

        // 1. Total Visits
        $this->db->select('COUNT(' . db_prefix() . 'visits.id) as total_visits');

        // 2. Unique Patients
        $this->db->select('COUNT(DISTINCT ' . db_prefix() . 'visits.patient_id) as unique_patients');

        // 3. Unique Doctors/Staff (using invoices.sale_agent as per table "User" column)
        $this->db->select('COUNT(DISTINCT ' . db_prefix() . 'invoices.sale_agent) as unique_doctors');

        $this->db->from(db_prefix() . 'visits');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'visits.invoice_id', 'left');

        $result = $this->db->get()->row_array();

        // 4. Total Tests - Separate efficient query
        // Re-apply filters for second query
        // Note: apply_visits_filters modifies the DB object state, so we need to be careful. 
        // CodeIgniter 3 usually resets builder after get().

        $this->apply_visits_filters($filters);
        $this->db->select('COUNT(*) as total_tests');
        $this->db->from(db_prefix() . 'patient_tests');
        $this->db->join(db_prefix() . 'visits', db_prefix() . 'visits.invoice_id = ' . db_prefix() . 'patient_tests.invoice_id', 'join');
        // We still need joins for filters (client name, doctor etc)
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'visits.patient_id', 'left');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'visits.invoice_id', 'left');

        $tests_result = $this->db->get()->row();

        $result['total_tests'] = $tests_result ? $tests_result->total_tests : 0;

        return $result;
    }

    private function apply_visits_filters($filters)
    {
        if (isset($filters['search']) && !empty($filters['search'])) {
            $term = $filters['search'];
            $this->db->group_start();
            $this->db->like(db_prefix() . 'clients.company', $term);
            $this->db->or_like(db_prefix() . 'visits.visit_code', $term);
            $this->db->or_like(db_prefix() . 'patients_extra.mr_number', $term);
            $this->db->or_like(db_prefix() . 'clients.phonenumber', $term);
            $this->db->or_like(db_prefix() . 'patients_extra.mobile_number', $term);
            $this->db->group_end();
        }

        if (isset($filters['from_date']) && !empty($filters['from_date'])) {
            $this->db->where('DATE(' . db_prefix() . 'visits.created_at) >=', to_sql_date($filters['from_date']));
        }
        if (isset($filters['to_date']) && !empty($filters['to_date'])) {
            $this->db->where('DATE(' . db_prefix() . 'visits.created_at) <=', to_sql_date($filters['to_date']));
        }

        if (isset($filters['user_id']) && !empty($filters['user_id'])) {
            $this->db->where(db_prefix() . 'invoices.sale_agent', $filters['user_id']);
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            // Check if any test in the visit's invoice has this status
            $this->db->where('EXISTS (SELECT 1 FROM ' . db_prefix() . 'patient_tests WHERE ' . db_prefix() . 'patient_tests.invoice_id = ' . db_prefix() . 'visits.invoice_id AND status = ' . $this->db->escape_str($filters['status']) . ')');
        }

        if (isset($filters['payment_status']) && !empty($filters['payment_status'])) {
            $this->db->where(db_prefix() . 'invoices.status', $filters['payment_status']);
        }

        if (isset($filters['patient_id']) && !empty($filters['patient_id'])) {
            $this->db->where(db_prefix() . 'visits.patient_id', $filters['patient_id']);
        }
    }

    public function get_visit_tests($invoice_id)
    {
        $this->db->select(db_prefix() . 'items.description as test_name');
        $this->db->select(db_prefix() . 'items.description'); // Add description key
        $this->db->select(db_prefix() . 'items.rate'); // Add rate
        $this->db->select(db_prefix() . 'patient_tests.status');
        $this->db->select(db_prefix() . 'patient_tests.id as test_id');
        $this->db->select(db_prefix() . 'patient_tests.item_id');
        $this->db->select(db_prefix() . 'items_groups.name as group_name');
        $this->db->select(db_prefix() . 'items_groups.id as group_id');
        $this->db->select(db_prefix() . 'items.is_price_changable');
        $this->db->select(db_prefix() . 'items.is_authorization_required');
        $this->db->select(db_prefix() . 'patient_tests.is_emergency'); // Add is_emergency

        // Staff? patient_tests doesn't track *who* added the specific test, usually invoice sale_agent covers the batch.
        // But if we need row level user... we might fallback to invoice creator.
        $this->db->select('CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as staff_name');

        $this->db->from(db_prefix() . 'patient_tests');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'patient_tests.item_id', 'left');
        $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'patient_tests.invoice_id', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'invoices.sale_agent', 'left');

        $this->db->where(db_prefix() . 'patient_tests.invoice_id', $invoice_id);
        return $this->db->get()->result_array();
    }
    public function check_latest_visit_today($patient_id)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . 'visits');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->order_by('created_at', 'DESC');
        $visit = $this->db->get()->row();

        return $visit;
    }

    private function log_test_status($test_id, $status)
    {
        if ($this->db->table_exists(db_prefix() . 'patient_test_history')) {
            $this->db->insert(db_prefix() . 'patient_test_history', [
                'test_id' => $test_id,
                'status' => $status,
                'staff_id' => get_staff_user_id(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function check_latest_active_visit($patient_id)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . 'visits');
        $this->db->where('patient_id', $patient_id);
        // Look back 1 day (Yesterday and Today)
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $this->db->where('DATE(created_at) >=', $yesterday);
        $this->db->order_by('created_at', 'DESC');
        $visit = $this->db->get()->row();

        return $visit;
    }
    public function get_invoice_paid($invoice_id)
    {
        $this->db->select('SUM(amount) as total');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->where('invoiceid', $invoice_id);
        $result = $this->db->get()->row();
        return $result ? $result->total : 0;
    }

    private function calculate_age_value($data)
    {
        if (isset($data['age_unit']) && $data['age_unit'] == 'DOB' && !empty($data['age'])) {
            $dob_val = $data['age'];
            $date = DateTime::createFromFormat('d-m-Y', $dob_val);
            if (!$date) {
                $date = DateTime::createFromFormat('d/m/Y', $dob_val);
            }
            if (!$date) {
                // Try standard format
                try {
                    $date = new DateTime($dob_val);
                } catch (Exception $e) {
                    $date = false;
                }
            }

            if ($date) {
                $now = new DateTime();
                $diff = $now->diff($date);
                return $diff->y;
            }
            return 0;
        }

        // For Years, Months, Days - return the value
        return isset($data['age']) ? $data['age'] : 0;
    }

    private function calculate_dob($data)
    {
        if (isset($data['age_unit']) && $data['age_unit'] == 'DOB') {
            // Convert DD-MM-YYYY to YYYY-MM-DD
            if (isset($data['age']) && !empty($data['age'])) {
                $dob = $data['age'];
                // Check if already Y-m-d (rare, but safety)
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
                    return $dob;
                }
                // Convert d-m-Y (or d/m/Y)
                $date = DateTime::createFromFormat('d-m-Y', $dob);
                if (!$date) {
                    $date = DateTime::createFromFormat('d/m/Y', $dob); // Fallback
                }

                if ($date) {
                    return $date->format('Y-m-d');
                }
            }
            return null;
        }

        // Calculate from Age + Unit
        if (isset($data['age']) && !empty($data['age'])) {
            $unit = isset($data['age_unit']) ? $data['age_unit'] : 'Years';
            $val = intval($data['age']);
            if ($val > 0) {
                try {
                    return date('Y-m-d', strtotime("-{$val} {$unit}"));
                } catch (Exception $e) {
                    return null;
                }
            }
        }
        return null;
    }

    public function get_dashboard_stats()
    {
        $stats = [];

        // Total Patients
        $this->db->select('count(userid) as total');
        $stats['total_patients'] = $this->db->get(db_prefix() . 'clients')->row()->total;

        // Total Visits
        $this->db->select('count(id) as total');
        $stats['total_visits'] = $this->db->get(db_prefix() . 'visits')->row()->total;

        // New Patients This Month
        $this->db->select('count(userid) as total');
        $this->db->where('MONTH(datecreated)', date('m'));
        $this->db->where('YEAR(datecreated)', date('Y'));
        $stats['new_patients_month'] = $this->db->get(db_prefix() . 'clients')->row()->total;

        // Avg Visits per Patient
        if ($stats['total_patients'] > 0) {
            $stats['avg_visits'] = number_format($stats['total_visits'] / $stats['total_patients'], 1);
        } else {
            $stats['avg_visits'] = 0;
        }

        return $stats;
    }

    public function ensure_mr_number($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $extra = $this->db->get(db_prefix() . 'patients_extra')->row();

        if ($extra && !empty($extra->mr_number)) {
            return; // Already has MR Number
        }

        // Generate MR Number
        // We need client created date for consistency with add() logic
        $this->db->select('datecreated');
        $this->db->where('userid', $patient_id);
        $client = $this->db->get(db_prefix() . 'clients')->row();

        if (!$client)
            return; // Should not happen

        $date_part = date('dmy', strtotime($client->datecreated));
        $mr_number = $date_part . '-' . $patient_id;

        if ($extra) {
            // Update
            $this->db->where('patient_id', $patient_id);
            $this->db->update(db_prefix() . 'patients_extra', ['mr_number' => $mr_number]);
        } else {
            // Insert
            $this->db->insert(db_prefix() . 'patients_extra', [
                'patient_id' => $patient_id,
                'mr_number' => $mr_number,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function get_patient_by_mobile($mobile)
    {
        $this->db->select('userid, company as name, phonenumber as phone');
        $this->db->select(db_prefix() . 'patients_extra.mr_number');
        $this->db->from(db_prefix() . 'clients');
        $this->db->join(db_prefix() . 'patients_extra', db_prefix() . 'patients_extra.patient_id = ' . db_prefix() . 'clients.userid', 'left');
        $this->db->like('phonenumber', $mobile);
        $this->db->or_like(db_prefix() . 'patients_extra.mobile_number', $mobile);
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }
}
