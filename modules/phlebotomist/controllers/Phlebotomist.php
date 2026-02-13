<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Phlebotomist extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('phlebotomist_model');
    }

    public function index()
    {
        $data['title'] = 'Phlebotomist';
        $status = $this->input->get('status');
        $date = $this->input->get('date');

        if (!$status)
            $status = 'All'; // Default

        if (!$date)
            $date = date('Y-m-d'); // Default to today

        $data['selected_status'] = $status;
        $data['selected_date'] = $date;

        $data['requests'] = $this->phlebotomist_model->get_blood_sample_requests($status, $date);
        $data['emergency_count'] = $this->phlebotomist_model->count_by_status('Emergency', $date);
        $data['pending_count'] = $this->phlebotomist_model->count_by_status('Pending', $date);
        $this->load->view('phlebotomist/manage', $data);
    }

    public function collect_sample()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data = $this->input->post();

        $log_data = [
            'staff_id' => get_staff_user_id(),
            'patient_id' => $data['patient_id'],
            'patient_test_id' => $data['patient_test_id'],
            'test_name' => $data['test_name'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->phlebotomist_model->add_collection_log($log_data);

        if ($insert_id) {
            // Update status to Sample Collected
            $this->phlebotomist_model->update_status($data['patient_test_id'], 'Sample Collected');

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function print_label($id)
    {
        $template = $this->phlebotomist_model->get_barcode_template();
        $test_data = $this->phlebotomist_model->get_test_details($id);

        if (!$template || !$test_data) {
            echo "Error: Template or Data not found.";
            return;
        }

        // Merge Fields
        // Merge Fields (Handle NULLs)
        $content = $template->content;
        $content = str_replace('{PatientName}', $test_data['patient_name'] ?? '', $content);
        $content = str_replace('{MrNo}', $test_data['mr_number'] ?? '', $content);
        $content = str_replace('{SampleNo}', $test_data['sample_id'] ?? '', $content); // Use sample_id
        $content = str_replace('{LabTestSampleType}', $test_data['test_name'] ?? '', $content);
        $content = str_replace('{Age}', $test_data['age'] ?? '', $content);
        $content = str_replace('{Gender}', $test_data['gender'] ?? '', $content);
        $content = str_replace('{SampleCollectedTime}', $test_data['collected_at'] ? _dt($test_data['collected_at']) : '', $content);

        // Barcode Image
        $barcode_url = admin_url('phlebotomist/get_barcode_image/' . $id);
        $content = str_replace('{CustomisedBarCodeImage}', '<img src="' . $barcode_url . '">', $content);

        // Output for popup
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Print Label</title>
            <style>
                body { margin: 0; padding: 0; }
                @media print {
                    @page { margin: 0; }
                    body { margin: 0; }
                }
            </style>
        </head>
        <body onload="window.print();">
            ' . $content . '
        </body>
        </html>';
    }

    public function get_barcode_image($id)
    {
        // Require TCPDF Barcode library if simple require works, or rely on autoloader?
        // In Perfex, TCPDF is usually loaded via library or composer.
        // Let's try to load the library if not defined.
        if (!class_exists('TCPDFBarcode')) {
            require_once(APPPATH . 'vendor/tecnickcom/tcpdf/tcpdf_barcodes_1d.php');
        }

        // Get code to encode (Sample No / Visit Code / ID)
        $test_data = $this->phlebotomist_model->get_test_details($id);
        $code = $test_data['sample_id'] ?? $test_data['visit_code'] ?? $id;

        $barcodeobj = new TCPDFBarcode($code, 'C128');

        // Output PNG
        // getBarcodePNG($w, $h, $color)
        $barcodeobj->getBarcodePNG(2, 30, array(0, 0, 0));
        exit;
    }
}
