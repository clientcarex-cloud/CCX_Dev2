<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Patients_review extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('manage_payments/manage_payments_model');
        $this->load->model('staff_model');

        $filters = [
            'report_from' => $this->input->get('report_from'),
            'report_to' => $this->input->get('report_to'),
            'user_id' => $this->input->get('user_id'),
            'status' => $this->input->get('status') ? $this->input->get('status') : 'all'
        ];

        if ($this->input->get('report_from') && $this->input->get('report_to')) {
            $data['payments'] = $this->manage_payments_model->get_payments($filters);
        } else {
            $data['payments'] = [];
        }
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['filters'] = $filters;

        $data['title'] = 'Patients Review';
        $this->load->view('patients_review', $data);
    }

    public function settings()
    {
        $data['title'] = 'Patients Review Settings';

        // Load Item Groups
        $this->load->model('invoice_items_model');
        $data['item_groups'] = $this->invoice_items_model->get_groups();

        // Load Staff
        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);

        $this->load->view('settings', $data);
    }

    public function save_settings()
    {
        if (!has_permission('patients_review', '', 'edit')) {
            access_denied('patients_review');
        }

        if ($this->input->post()) {

            // Explicitly define fields to save to avoid saving CSRF or other garbage
            $fields = [
                'patients_records',
                'delete_system',
                'archive_system',
                'enable_archive_password',
                'enable_unarchive_password',
                // 'unarchive_password', // Handled separately
                'enable_delete_password',
                // 'delete_password', // Handled separately
                'patients_review_item_groups',
                'patients_review_authorized_user',
                'show_archived_tab',
            ];

            // Check if 'settings' array exists in POST (Perfex default often wraps settings)
            $posted_settings = $this->input->post('settings');

            foreach ($fields as $field) {
                $val = $this->input->post($field);

                // If null, check if it's in the 'settings' array
                if ($val === null && isset($posted_settings[$field])) {
                    $val = $posted_settings[$field];
                }

                // Handle arrays (multi-selects)
                if (is_array($val)) {
                    $val = implode(',', $val);
                }

                if ($val === null) {
                    $val = '';
                }

                update_option($field, $val);
            }

            // Handle Passwords Separately
            // Archive Password
            $new_archive_password = $this->input->post('archive_password');
            if ($new_archive_password === null && isset($posted_settings['archive_password'])) {
                $new_archive_password = $posted_settings['archive_password'];
            }
            if (!empty($new_archive_password)) {
                update_option('archive_password', $new_archive_password);
                update_option('archive_password_last_changed', date('Y-m-d H:i:s'));
            }

            // Unarchive Password
            $new_unarchive_password = $this->input->post('unarchive_password');
            if ($new_unarchive_password === null && isset($posted_settings['unarchive_password'])) {
                $new_unarchive_password = $posted_settings['unarchive_password'];
            }
            if (!empty($new_unarchive_password)) {
                update_option('unarchive_password', $new_unarchive_password);
                update_option('unarchive_password_last_changed', date('Y-m-d H:i:s'));
            }

            // Delete Password
            $new_delete_password = $this->input->post('delete_password');
            if ($new_delete_password === null && isset($posted_settings['delete_password'])) {
                $new_delete_password = $posted_settings['delete_password'];
            }
            if (!empty($new_delete_password)) {
                update_option('delete_password', $new_delete_password);
                update_option('delete_password_last_changed', date('Y-m-d H:i:s'));
            }


            set_alert('success', 'Settings Saved Successfully');
            redirect(admin_url('patients_review/settings'));
        }
    }
    public function bulk_action()
    {
        if (!has_permission('patients_review', '', 'delete')) {
            access_denied('patients_review');
        }

        if ($this->input->post()) {
            $ids = $this->input->post('ids');
            $action = $this->input->post('action');

            if (empty($ids)) {
                echo json_encode(['success' => false, 'message' => 'No items selected']);
                return;
            }

            // Check Delete Password
            if (get_option('enable_delete_password') == 1) {
                $submitted_password = $this->input->post('delete_password');
                $actual_password = get_option('delete_password');
                if ($submitted_password != $actual_password) {
                    echo json_encode(['success' => false, 'message' => 'Incorrect Password']);
                    return;
                }
            }

            $delete_option = $this->input->post('delete_option');

            $affected_rows = 0;
            $deleted_visits = 0;
            $deleted_invoices = 0;
            $deleted_payments = 0;

            // Get associated Invoice IDs
            $this->db->select('id, invoice_id');
            $this->db->where_in('id', $ids);
            $visits = $this->db->get(db_prefix() . 'visits')->result();

            $invoice_ids = [];
            foreach ($visits as $visit) {
                if ($visit->invoice_id) {
                    $invoice_ids[] = $visit->invoice_id;
                }
            }

            if ($action == 'delete') {
                if ($delete_option == 'whole_invoices' && !empty($invoice_ids)) {
                    // 1. Delete Payments
                    $this->db->where_in('invoiceid', $invoice_ids);
                    $this->db->delete(db_prefix() . 'invoicepaymentrecords');
                    $deleted_payments = $this->db->affected_rows();

                    // 2. Delete Items
                    $this->db->where('rel_type', 'invoice');
                    $this->db->where_in('rel_id', $invoice_ids);
                    $this->db->delete(db_prefix() . 'itemable');

                    // 3. Delete Invoices
                    $this->db->where_in('id', $invoice_ids);
                    $this->db->delete(db_prefix() . 'invoices');
                    $deleted_invoices = $this->db->affected_rows();
                }

                if ($delete_option == 'whole_invoices') {
                    $this->db->where_in('id', $ids);
                    $this->db->delete(db_prefix() . 'visits');
                    $deleted_visits = $this->db->affected_rows();
                    $affected_rows = $deleted_visits;
                } elseif ($delete_option == 'paid_receipts' && !empty($invoice_ids)) {
                    // 1. Delete Payments
                    $this->db->where_in('invoiceid', $invoice_ids);
                    $this->db->delete(db_prefix() . 'invoicepaymentrecords');
                    $deleted_payments = $this->db->affected_rows();

                    // 2. Set Invoice Status to Unpaid (1) or whatever appropriate status
                    // Since specific payments are deleted, status should probably be checked or forced to unpaid/partial.
                    // For bulk simplicity, setting to Unpaid (1) is safest if all receipts are gone.
                    $this->db->where_in('id', $invoice_ids);
                    $this->db->update(db_prefix() . 'invoices', ['status' => 1]); // 1 = Unpaid
                }

            } elseif ($action == 'archive') {
                if (!empty($invoice_ids)) {
                    // Move Invoices
                    $this->db->query('INSERT IGNORE INTO ' . db_prefix() . 'invoices_archive SELECT * FROM ' . db_prefix() . 'invoices WHERE id IN (' . implode(',', array_map('intval', $invoice_ids)) . ')');

                    // Move Payments
                    $this->db->query('INSERT IGNORE INTO ' . db_prefix() . 'invoicepaymentrecords_archive SELECT * FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid IN (' . implode(',', array_map('intval', $invoice_ids)) . ')');

                    // Move Items
                    $this->db->query("INSERT IGNORE INTO " . db_prefix() . "itemable_archive SELECT * FROM " . db_prefix() . "itemable WHERE rel_type = 'invoice' AND rel_id IN (" . implode(',', array_map('intval', $invoice_ids)) . ")");

                    // Delete Originals
                    $this->db->where_in('id', $invoice_ids)->delete(db_prefix() . 'invoices');
                    $this->db->where_in('invoiceid', $invoice_ids)->delete(db_prefix() . 'invoicepaymentrecords');
                    $this->db->where('rel_type', 'invoice')->where_in('rel_id', $invoice_ids)->delete(db_prefix() . 'itemable');
                }

                // Update Visits
                $this->db->where_in('id', $ids);
                $this->db->update(db_prefix() . 'visits', ['is_archived' => 1]);
                $affected_rows = $this->db->affected_rows();

            } elseif ($action == 'unarchive') {
                if (!empty($invoice_ids)) {
                    // Move Back Invoices
                    $this->db->query('INSERT IGNORE INTO ' . db_prefix() . 'invoices SELECT * FROM ' . db_prefix() . 'invoices_archive WHERE id IN (' . implode(',', array_map('intval', $invoice_ids)) . ')');

                    // Move Back Payments
                    $this->db->query('INSERT IGNORE INTO ' . db_prefix() . 'invoicepaymentrecords SELECT * FROM ' . db_prefix() . 'invoicepaymentrecords_archive WHERE invoiceid IN (' . implode(',', array_map('intval', $invoice_ids)) . ')');

                    // Move Back Items
                    $this->db->query("INSERT IGNORE INTO " . db_prefix() . "itemable SELECT * FROM " . db_prefix() . "itemable_archive WHERE rel_type = 'invoice' AND rel_id IN (" . implode(',', array_map('intval', $invoice_ids)) . ")");

                    // Delete from Archive
                    $this->db->where_in('id', $invoice_ids)->delete(db_prefix() . 'invoices_archive');
                    $this->db->where_in('invoiceid', $invoice_ids)->delete(db_prefix() . 'invoicepaymentrecords_archive');
                    $this->db->where('rel_type', 'invoice')->where_in('rel_id', $invoice_ids)->delete(db_prefix() . 'itemable_archive');
                }

                // Update Visits
                $this->db->where_in('id', $ids);
                $this->db->update(db_prefix() . 'visits', ['is_archived' => 0]);
                $affected_rows = $this->db->affected_rows();
            }

            if ($affected_rows > 0 || $deleted_payments > 0 || $deleted_invoices > 0) {
                $msg = '';
                if ($deleted_visits > 0)
                    $msg .= $deleted_visits . ' Visits Deleted. ';
                if ($deleted_invoices > 0)
                    $msg .= $deleted_invoices . ' Invoices Deleted. ';
                if ($deleted_payments > 0)
                    $msg .= $deleted_payments . ' Payments Deleted. ';
                if ($action == 'archive')
                    $msg .= $affected_rows . ' Visits Archived. ';
                if ($action == 'unarchive')
                    $msg .= $affected_rows . ' Visits Unarchived. ';

                echo json_encode(['success' => true, 'message' => $msg]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('problem_deleting', _l('patients_review'))]);
            }
        }
    }


}
