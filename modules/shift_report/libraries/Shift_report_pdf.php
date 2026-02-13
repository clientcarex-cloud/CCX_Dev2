<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Shift_report_pdf extends App_pdf
{
    protected $shift_report;

    public function __construct($shift_report)
    {
        $this->load_language($shift_report['staff_id']);
        parent::__construct();

        $this->shift_report = $shift_report;
        $this->SetTitle('Shift Report - ' . $shift_report['date']);
    }

    public function prepare()
    {
        $this->set_view_vars([
            'report_data' => $this->shift_report['report_data'],
            'staff_details' => $this->shift_report['staff_details'],
            'date' => $this->shift_report['date'],
            'staff_id' => $this->shift_report['staff_id'],
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'shift_report';
    }

    protected function file_path()
    {
        if (isset($this->shift_report['type']) && $this->shift_report['type'] == 'shift_report_hospital') {
            return FCPATH . 'modules/shift_report/views/types/report_hospital_pdf.php';
        }
        if (isset($this->shift_report['type']) && $this->shift_report['type'] == 'shift_report_lab') {
            return FCPATH . 'modules/shift_report/views/types/report_lab_pdf.php';
        }
        if (isset($this->shift_report['type']) && $this->shift_report['type'] == 'overall_collection_hospital') {
            return FCPATH . 'modules/shift_report/views/types/report_hospital_overall_pdf.php';
        }
        if (isset($this->shift_report['type']) && $this->shift_report['type'] == 'overall_collection_lab') {
            return FCPATH . 'modules/shift_report/views/types/report_lab_overall_pdf.php';
        }
        if (isset($this->shift_report['type']) && $this->shift_report['type'] == 'consolidated_shift_hospital_lab') {
            return FCPATH . 'modules/shift_report/views/types/report_consolidated_shift_hospital_lab_pdf.php';
        }
        if (isset($this->shift_report['type']) && $this->shift_report['type'] == 'consolidated_overall_hospital_lab') {
            return FCPATH . 'modules/shift_report/views/types/report_consolidated_overall_hospital_lab_pdf.php';
        }
        if (isset($this->shift_report['type']) && $this->shift_report['type'] == 'consolidated_all_groups_shift') {
            return FCPATH . 'modules/shift_report/views/types/report_consolidated_all_groups_shift_pdf.php';
        }
        if (isset($this->shift_report['type']) && $this->shift_report['type'] == 'consolidated_overall_all_groups') {
            return FCPATH . 'modules/shift_report/views/types/report_consolidated_overall_all_groups_pdf.php';
        }
        return FCPATH . 'modules/shift_report/views/report_pdf.php';
    }
}
