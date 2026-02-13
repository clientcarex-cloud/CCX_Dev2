<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Refunds_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->check_database_integrity();
    }

    private function check_database_integrity()
    {
        // 1. Check for 'refund_items' table
        if (!$this->db->table_exists(db_prefix() . 'refund_items')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . 'refund_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `refund_id` int(11) NOT NULL,
                `patient_test_id` int(11) NOT NULL,
                `refunded_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
                PRIMARY KEY (`id`),
                KEY `refund_id` (`refund_id`),
                KEY `patient_test_id` (`patient_test_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $this->db->char_set . ';');
        }

        // 2. Check for 'refund_type' column in 'refunds' table
        if ($this->db->table_exists(db_prefix() . 'refunds')) {
            if (!$this->db->field_exists('refund_type', db_prefix() . 'refunds')) {
                $this->db->query('ALTER TABLE `' . db_prefix() . 'refunds` ADD `refund_type` VARCHAR(50) DEFAULT NULL AFTER `note`;');
            }
        }
    }

    public function get($id = '')
    {
        $this->db->select(db_prefix() . 'refunds.*, ' . db_prefix() . 'clients.company as patient_name, ' . db_prefix() . 'invoices.number as invoice_number');
        $this->db->from(db_prefix() . 'refunds');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'refunds.invoice_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid', 'left');

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'refunds.id', $id);
            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = get_staff_user_id();
        $data['refunded_on'] = to_sql_date($data['refunded_on']);



        // Extract items for later use (NOT for insertion into refunds table)
        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
        }

        // Filter Data for Insertion
        $allowed_columns = [
            'invoice_id',
            'amount',
            'refund_type',
            'payment_mode',
            'note',
            'refunded_on',
            'created_at',
            'created_by'
        ];

        $insert_data = [];
        foreach ($allowed_columns as $col) {
            if (isset($data[$col])) {
                $insert_data[$col] = $data[$col];
            }
        }

        $this->db->insert(db_prefix() . 'refunds', $insert_data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Refund Added [ID: ' . $insert_id . ', Type: ' . $data['refund_type'] . ', Amount: ' . $data['amount'] . ']');

            // Handle Refund Items & Status Updates
            foreach ($items as $item) {
                // $item = ['test_id' => ..., 'amount' => ...]
                if (!empty($item['test_id'])) {
                    // 1. Insert into refund_items
                    $this->db->insert(db_prefix() . 'refund_items', [
                        'refund_id' => $insert_id,
                        'patient_test_id' => $item['test_id'],
                        'refunded_amount' => isset($item['amount']) ? $item['amount'] : 0
                    ]);

                    // 2. Check Logic for Status Update
                    // Types: 'refund_cancellation', 'only_refund', 'only_cancellation'
                    // If 'only_refund', we DO NOT cancel status.
                    // If 'refund_cancellation' or 'only_cancellation', we SET status to Cancelled.

                    if ($data['refund_type'] == 'refund_cancellation' || $data['refund_type'] == 'only_cancellation') {
                        $this->db->where('id', $item['test_id']);
                        $this->db->update(db_prefix() . 'patient_tests', ['status' => 'Cancelled']);
                    }
                }
            }

            return $insert_id;
        }

        return false;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'refunds');
        if ($this->db->affected_rows() > 0) {
            log_activity('Refund Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
    public function get_invoice_refund_total($invoice_id)
    {
        $this->db->select('SUM(amount) as total');
        $this->db->from(db_prefix() . 'refunds');
        $this->db->where('invoice_id', $invoice_id);
        $result = $this->db->get()->row();
        return $result ? (float) $result->total : 0.00;
    }

    public function get_refunds_by_invoice($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get(db_prefix() . 'refunds')->result_array();
    }
}
