<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Follow_ups extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('follow_ups_model');
    }

    public function index()
    {
        if (!has_permission('follow_ups', '', 'view')) {
            access_denied('follow_ups');
        }

        $status_id = $this->input->get('status_id');
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');

        $data['title'] = 'Follow Ups';
        $data['follow_ups'] = $this->follow_ups_model->get_follow_ups($status_id, $from_date, $to_date);

        // Fetch Statuses and Counts
        $data['statuses'] = $this->follow_ups_model->get_statuses();
        $data['status_counts'] = $this->follow_ups_model->get_status_counts();

        // Add "All" count
        $all_count = 0;
        foreach ($data['status_counts'] as $c) {
            $all_count += $c;
        }
        $data['all_count'] = $all_count;

        // Pass filter values back to view
        $data['selected_status'] = $status_id;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        // Fetch Staff for Reminders
        $this->load->model('staff_model');
        $data['members'] = $this->staff_model->get('', ['active' => 1]);

        // Load staff members for reminder modal
        $this->load->model('staff_model');
        $data['members'] = $this->staff_model->get('', ['active' => 1]);

        // Enrich data with visit counts? Or do it in the view?
        // Doing it here is cleaner to avoid model calls in view loop (though small scale might be fine)
        // Let's loop and add visits count.
        foreach ($data['follow_ups'] as &$row) {
            $row['visits_count'] = $this->follow_ups_model->get_visits_count($row['patient_id']);
        }

        // Fetch default 'New' status for fallback coloring
        $data['new_status_default'] = $this->follow_ups_model->get_status_by_name('New');
        if (!$data['new_status_default']) {
            // Fallback hardcoded if DB is somehow empty, though it shouldn't be
            $data['new_status_default'] = ['name' => 'New', 'color' => '#2196F3'];
        }

        $this->load->view('manage', $data);
    }

    public function settings()
    {
        if (!has_permission('follow_ups', '', 'view')) {
            access_denied('follow_ups');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if (isset($data['action'])) {
                if ($data['action'] == 'add_status') {
                    $this->follow_ups_model->add_status([
                        'name' => $data['name'],
                        'color' => $data['color'],
                        'status_order' => $data['status_order']
                    ]);
                    set_alert('success', 'Status added successfully');
                } elseif ($data['action'] == 'update_status') {
                    $this->follow_ups_model->update_status([
                        'name' => $data['name'],
                        'color' => $data['color'],
                        'status_order' => $data['status_order']
                    ], $data['id']);
                    set_alert('success', 'Status updated successfully');
                } elseif ($data['action'] == 'delete_status') {
                    $response = $this->follow_ups_model->delete_status($data['id']);
                    if (is_array($response) && isset($response['status']) && $response['status'] == false) {
                        set_alert('warning', $response['message']);
                    } elseif ($response == true) {
                        set_alert('success', 'Status deleted successfully');
                    } else {
                        set_alert('warning', 'Problem deleting status');
                    }
                }
            }
            redirect(admin_url('follow_ups/settings'));
        }

        $data['statuses'] = $this->follow_ups_model->get_statuses();
        $data['status_counts'] = $this->follow_ups_model->get_status_counts();
        $data['title'] = 'Follow Ups Settings';
        $this->load->view('settings', $data);
    }





}
