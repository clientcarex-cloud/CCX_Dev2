<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Refunds extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('refunds_model');
    }

    public function index()
    {
        if (!has_permission('refunds', '', 'view')) {
            access_denied('refunds');
        }

        if ($this->input->post()) {
            if (!has_permission('refunds', '', 'create')) {
                access_denied('refunds');
            }
            $data = $this->input->post();
            $id = $this->refunds_model->add($data);
            if ($id) {
                set_alert('success', 'Refund added successfully');
            }
            redirect(admin_url('refunds'));
        }

        $data['title'] = 'Refunds & Cancellations';
        $data['refunds'] = $this->refunds_model->get();
        // Load Payment Modes for dropdown
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

        $this->load->view('manage', $data);
    }

    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('refunds');
        }
        if (!$id) {
            redirect(admin_url('refunds'));
        }
        $response = $this->refunds_model->delete($id);
        if ($response) {
            set_alert('success', 'Refund deleted successfully');
        }
        redirect(admin_url('refunds'));
    }

    // AJAX helper to get patient invoices
    public function get_patient_invoices($patient_id)
    {
        if (!$patient_id)
            return;
        $this->db->select('id, number, total, status, (SELECT SUM(amount) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid=' . db_prefix() . 'invoices.id) as total_paid');
        $this->db->where('clientid', $patient_id);
        $this->db->where('status !=', 1); // Not Unpaid (Partial or Paid)
        $invoices = $this->db->get(db_prefix() . 'invoices')->result_array();
        echo json_encode($invoices);
    }

    /* Save Refund from Modal */
    public function save_refund()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
            }

            if (!has_permission('refunds', '', 'create')) {
                echo json_encode(['success' => false, 'message' => 'Access Denied']);
                die;
            }

            $data = $this->input->post();

            // Basic Validation
            if (!isset($data['amount']) || $data['amount'] <= 0) {
                if ($data['refund_type'] != 'only_cancellation') {
                    echo json_encode(['success' => false, 'message' => 'Invalid Amount']);
                    die;
                }
            }

            $id = $this->refunds_model->add($data);

            if ($id) {
                echo json_encode(['success' => true, 'message' => 'Refund processed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add refund (Model returned false)']);
            }
        } catch (Exception $e) {
            log_message('error', 'Refund Save Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        } catch (Error $e) {
            log_message('error', 'Refund Save Fatal Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Fatal Error: ' . $e->getMessage()]);
        }
    }

    public function get_visit_items($invoice_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model('patients/patients_model');
        $items = $this->patients_model->get_visit_tests($invoice_id);
        echo json_encode($items);
    }
}
