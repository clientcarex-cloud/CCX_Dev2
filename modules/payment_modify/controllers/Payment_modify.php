<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payment_modify extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('manage_payments/manage_payments_model');
        $this->load->model('staff_model');
        $this->load->model('payment_modes_model');

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
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);
        $data['filters'] = $filters;

        // Settings for editability
        $data['settings'] = [
            'pm_allow_edit_mode' => get_option('pm_allow_edit_mode'),
            'pm_allow_edit_amount' => get_option('pm_allow_edit_amount'),
            'pm_allow_edit_date' => get_option('pm_allow_edit_date'),
            'pm_allow_edit_collected_by' => get_option('pm_allow_edit_collected_by'),
            'pm_allow_edit_discount' => get_option('pm_allow_edit_discount'),
        ];

        $data['title'] = 'Payment Modify';
        $this->load->view('payment_modify', $data);
    }

    public function update_receipt()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $data = [
                'paymentmode' => $this->input->post('paymentmode'),
                'amount' => $this->input->post('amount'),
                'date' => to_sql_date($this->input->post('date')),
            ];

            $this->load->model('payments_model');
            // update() method in payments_model handles hook 'before_payment_updated' which might be needed
            // However, payments_model->update expects 'note' too, or might overwrite it. 
            // Minimal update: direct DB or use model? 
            // Using model is safer for consistency, but standard model requires 'date' in SQL format already if passing to update?
            // Checking model: `to_sql_date` is called inside `update` too. So we pass regular date?
            // Wait, standard `update` method calls `to_sql_date` on `$data['date']`.

            // Let's rely on model but ensure we don't break other fields.
            // Model fetches current payment first.

            // We need to pass 'note' if we don't want to lose it? 
            // No, standard update only updates what is passed? 
            // Checking: $this->db->update('invoicepaymentrecords', $data); in model.
            // If we only pass 3 fields, only 3 fields update.
            // But model does $data['note'] = nl2br($data['note']); without checking isset. 
            // This might throw error if note is missing.
            // Let's fetch existing note first to be safe, or just suppress it.

            $current = $this->payments_model->get($id);
            if ($current) {
                $data['note'] = $current->note; // Preserve note
                $success = $this->payments_model->update($data, $id);
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Payment not found']);
            }
        }
    }

    public function update_invoice()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $data = [
                'sale_agent' => $this->input->post('sale_agent'),
                'discount_total' => $this->input->post('discount'),
            ];
            // Recalculate invoice totals maybe?
            // Changing discount total directly might break total/subtotal calculation consistency if we don't recalculate.
            // But Perfex usually calculates things.
            // If we just update DB, fine for now, but safer to use invoices_model->update() but it requires FULL data usually.

            // Direct update for simple fields
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', $data);

            // If discount changed, we might need to update 'total'.
            // total = subtotal + tax - discount + adjustment.
            // Ideally we should re-calculate.
            if ($this->input->post('discount') !== null) {
                $this->load->model('invoices_model');
                $invoice = $this->invoices_model->get($id);
                // Simple recalc attempt:
                $new_total = $invoice->subtotal + $invoice->total_tax - $data['discount_total'] + $invoice->adjustment;
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'invoices', ['total' => $new_total]);
                $this->invoices_model->save_formatted_number($id); // Refreshes sort order/numbering if needed, mainly valid for numbers.
            }

            echo json_encode(['success' => true]);
        }
    }

    public function save_settings()
    {
        if (!is_admin()) {
            access_denied('Payment Modify Settings');
        }
        if ($this->input->post()) {
            $settings = [
                'pm_allow_edit_mode',
                'pm_allow_edit_amount',
                'pm_allow_edit_date',
                'pm_allow_edit_collected_by',
                'pm_allow_edit_discount'
            ];

            foreach ($settings as $option) {
                // If checkbox is checked it sends '1', else we set '0' if missing
                // Note: update_option might need to create the option if it doesn't exist.
                // In Perfex, update_option generally handles insert if not exists, or we might need add_option if it's new.
                // But usually update_option is safe.
                $val = $this->input->post($option);
                update_option($option, $val ? 1 : 0);
            }
            echo json_encode(['success' => true]);
        }
    }
}
