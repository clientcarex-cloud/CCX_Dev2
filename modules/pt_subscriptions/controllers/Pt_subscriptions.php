<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pt_subscriptions extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        /*
        if (!has_permission('pt_subscriptions', '', 'view')) {
            access_denied('pt_subscriptions');
        }
        */

        $data['title'] = 'Pt. Subscriptions';

        $this->load->model('pt_subscriptions_model');
        $this->load->model('staff_model');
        $this->load->model('invoice_items_model');

        // Filters
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');

        // Fetch All Data (SSR) with Filters
        // $data['subscriptions'] = $this->pt_subscriptions_model->get_subscriptions_table_data($status, $from, $to); // Removed for SSR
        $data['tab_counts'] = $this->pt_subscriptions_model->get_subscription_counts($from, $to);
        $data['active_status'] = $status ? $status : 'all';
        $data['from_date'] = $from;
        $data['to_date'] = $to;

        // Fetch Items for Modal
        $all_items = $this->invoice_items_model->get();
        // Filter for "Packages" group
        $this->db->where('name', 'Packages');
        $package_group = $this->db->get(db_prefix() . 'items_groups')->row();

        if ($package_group) {
            $data['available_items'] = array_filter($all_items, function ($item) use ($package_group) {
                return $item['group_id'] == $package_group->id;
            });
        } else {
            $data['available_items'] = $all_items;
        }

        // Fetch Doctors
        $data['doctors'] = $this->pt_subscriptions_model->get_doctors();

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $this->load->view('manage', $data);
    }

    public function table()
    {
        $this->load->view('table');
    }

    public function create()
    {
        if ($this->input->post()) {
            $this->load->model('invoices_model');
            $this->load->model('currencies_model');
            $this->load->model('pt_subscriptions_model');
            $data = $this->input->post();

            // Fetch Client Requesting Subscription for Billing Info
            $this->load->model('clients_model');
            $client = $this->clients_model->get($data['clientid']);

            // Basic Invoice Data Structure for Perfex
            $invoice_data = [
                'clientid' => $data['clientid'],
                'number' => get_option('next_invoice_number'),
                'date' => date('Y-m-d'),
                'duedate' => date('Y-m-d', strtotime('+' . get_option('predefined_terms_invoice') . ' days')),
                'currency' => $this->currencies_model->get_base_currency()->id,
                'recurring' => 1,
                'recurring_type' => $data['recurring_type'],
                'cycles' => $data['recurring'],
                'custom_recurring' => 0,
                'show_quantity_as' => 1,
                'is_pt_subscription' => 1,
                'pt_subscription_status' => 'active',
                'status' => 1, // Unpaid
                'terms' => get_option('predefined_terms_invoice'),
                'sale_agent' => $data['doctor_id'] ?? get_staff_user_id(), // Use selected doctor or current user
                'adminnote' => $data['adminnote'] ?? '', // Add notes
                'subtotal' => 0, // Will be calc
                'total' => 0,    // Will be calc
                // Billing Details
                'billing_street' => $client->billing_street ?? '',
                'billing_city' => $client->billing_city ?? '',
                'billing_state' => $client->billing_state ?? '',
                'billing_zip' => $client->billing_zip ?? '',
                'billing_country' => $client->billing_country ?? '',
                'shipping_street' => $client->shipping_street ?? '',
                'shipping_city' => $client->shipping_city ?? '',
                'shipping_state' => $client->shipping_state ?? '',
                'shipping_zip' => $client->shipping_zip ?? '',
                'shipping_country' => $client->shipping_country ?? '',
            ];

            // Reconstruct Items for invoices_model->add
            $this->load->model('invoice_items_model');
            $item = $this->invoice_items_model->get($data['itemid']);

            if ($item) {
                // Item structure for invoices_model->add
                $item_key = 'new-1';
                $invoice_data['newitems'][$item_key] = [
                    'description' => $item->description,
                    'long_description' => $item->long_description,
                    'qty' => 1,
                    'rate' => $item->rate,
                    'order' => 1,
                    'unit' => $item->unit
                ];

                $invoice_data['subtotal'] = $item->rate;
                $invoice_data['total'] = $item->rate;
            } else {
                set_alert('warning', 'Item not found');
                redirect('admin/pt_subscriptions');
            }

            $id = $this->invoices_model->add($invoice_data);

            if ($id) {
                // Log Activity
                $this->pt_subscriptions_model->log_activity('Created new subscription for Patient ID: ' . $data['clientid']);
                set_alert('success', 'Subscription Created Successfully');
            } else {
                set_alert('danger', 'Failed to create subscription');
            }
            redirect('admin/pt_subscriptions');
        }
    }

    public function update()
    {
        if ($this->input->post()) {
            $this->load->model('pt_subscriptions_model');
            $this->load->model('invoices_model');
            $data = $this->input->post();
            $id = $data['id'];

            $update_data = [
                'recurring' => $data['recurring'],
                'recurring_type' => $data['recurring_type'],
                'cycles' => $data['recurring'], // Often kept in sync for subscription definition
                'sale_agent' => $data['doctor_id'],
                'adminnote' => $data['adminnote']
            ];

            // Handle Status Change
            if (isset($data['status'])) {
                $this->pt_subscriptions_model->change_subscription_status($id, $data['status']);
            }

            $success = $this->pt_subscriptions_model->update_subscription($id, $update_data);

            if ($success) {
                $this->pt_subscriptions_model->log_activity('Updated subscription ID: ' . $id);
                set_alert('success', 'Subscription Updated Successfully');
            } else {
                set_alert('danger', 'Failed to update subscription');
            }
            redirect('admin/pt_subscriptions');
        }
    }

    public function change_status($id, $status)
    {
        $this->load->model('pt_subscriptions_model');
        $success = $this->pt_subscriptions_model->change_subscription_status($id, $status);
        if ($success) {
            $this->pt_subscriptions_model->log_activity('Changed status of subscription ID: ' . $id . ' to ' . $status);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function get_subscription_json($id)
    {
        $this->load->model('pt_subscriptions_model');
        $subscription = $this->pt_subscriptions_model->get_subscription($id);
        echo json_encode($subscription);
    }
}
