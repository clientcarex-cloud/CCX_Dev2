<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Mis_reports_pdf extends App_pdf
{
    protected $report_data;
    protected $title;

    public function __construct($params)
    {
        $this->report_data = $params['report_data'];
        $this->title = $params['title'];

        parent::__construct();

        $this->SetTitle($this->title);
    }

    public function prepare()
    {
        $this->set_view_vars([
            'new_patients' => $this->report_data['new_patients'],
            'old_patients' => $this->report_data['old_patients'],
            'from_date' => $this->report_data['from_date'],
            'to_date' => $this->report_data['to_date']
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'mis_report';
    }

    protected function file_path()
    {
        return module_dir_path('mis_reports', 'views/patient_visit_overall_pdf.php');
    }
}
