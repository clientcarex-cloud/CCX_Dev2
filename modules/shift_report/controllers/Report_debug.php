<?php
// Save this as debug_report.php in the root or accessible folder
// Actually better as a controller method to access DB easily
defined('BASEPATH') or exit('No direct script access allowed');

class Report_debug extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shift_report/shift_report_model');
    }

    public function index()
    {
        $date = '2026-01-15';

        echo "<h1>Debug Data for $date</h1>";

        // 1. Check Visits
        $this->db->select('id, created_at, invoice_id, patient_id');
        $this->db->where('DATE(created_at)', $date);
        $visits = $this->db->get(db_prefix() . 'visits')->result_array();
        echo "<h2>Visits (Count: " . count($visits) . ")</h2>";
        echo "<pre>";
        print_r($visits);
        echo "</pre>";

        // 2. Check Invoices matching that date
        $this->db->select('id, date, sale_agent, total, status, number');
        $this->db->where('date', $date);
        $invoices = $this->db->get(db_prefix() . 'invoices')->result_array();
        echo "<h2>Invoices with Date $date (Count: " . count($invoices) . ")</h2>";
        // Show distribution of sale_agent
        $agents = [];
        foreach ($invoices as $inv) {
            $agents[$inv['sale_agent']][] = $inv['id'];
        }
        echo "<h3>Invoices by Sale Agent:</h3>";
        echo "<pre>";
        print_r($agents);
        echo "</pre>";

        // 3. Check Payments matching that date (if applicable, though report uses Invoice Date or Visit Date?)
        // The report logic I implemented uses Invoice Date.

        // 4. Check Payments on Invoices of that date
        $this->db->select('tblinvoicepaymentrecords.*, tblinvoices.sale_agent, tblinvoices.date as inv_date');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id=' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where(db_prefix() . 'invoices.date', $date);
        $payments = $this->db->get(db_prefix() . 'invoicepaymentrecords')->result_array();
        echo "<h2>Payments on Invoices of Date $date (Count: " . count($payments) . ")</h2>";
        echo "<pre>";
        print_r($payments);
        echo "</pre>";

    }
}
