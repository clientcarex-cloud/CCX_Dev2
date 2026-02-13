<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Patient_master_modal extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('patients/patients_model');
        $this->load->model('follow_ups/follow_ups_model');
        $this->load->model('staff_model');
    }

    /* Notes Functions */
    public function notes_table($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('patient_master_modal', 'tables/notes'), ['patient_id' => $patient_id]);
    }

    public function add_note()
    {
        if (!has_permission('patients', '', 'create') && !has_permission('patients', '', 'edit')) {
            ajax_access_denied();
        }

        $data = $this->input->post();
        if ($data) {
            $this->load->model('misc_model');
            $note_data = [
                'description' => $data['description'],
                'rel_type' => 'customer',
                'rel_id' => $data['patient_id']
            ];

            $id = $this->misc_model->add_note($note_data, 'customer', $data['patient_id']);

            if ($id) {
                // Update Follow Up Status if provided
                if (isset($data['status_id']) && !empty($data['status_id']) && isset($data['prescription_id'])) {
                    $this->follow_ups_model->update_prescription_status($data['prescription_id'], $data['status_id']);
                }

                echo json_encode(['success' => true, 'message' => _l('added_successfully', 'Call Log')]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add note']);
            }
        }
    }

    public function delete_note($id)
    {
        $this->load->model('misc_model');
        $note = $this->db->where('id', $id)->get(db_prefix() . 'notes')->row();

        if (!$note) {
            echo json_encode(['success' => false]);
            die;
        }

        if ($note->addedfrom != get_staff_user_id() && !is_admin()) {
            ajax_access_denied();
        }

        $success = $this->misc_model->delete_note($id);
        echo json_encode(['success' => $success]);
    }

    /* Reminders Functions */
    public function reminders_table($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('patient_master_modal', 'tables/reminders'), ['patient_id' => $patient_id]);
    }

    public function add_reminder()
    {
        if (!has_permission('patients', '', 'create') && !has_permission('patients', '', 'edit')) {
            ajax_access_denied();
        }

        $data = $this->input->post();
        if ($data) {
            $reminder_data = [
                'date' => $data['date'],
                'staff' => $data['staff'],
                'description' => $data['description'],
                'rel_id' => $data['patient_id'],
                'rel_type' => 'customer',
                'notify_by_email' => isset($data['notify_by_email']) ? 1 : 0
            ];

            $this->load->model('misc_model');
            $id = $this->misc_model->add_reminder($reminder_data, $data['patient_id']);

            if ($id) {
                echo json_encode(['success' => true, 'message' => _l('added_successfully', _l('reminder'))]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add reminder']);
            }
        }
    }

    public function delete_reminder($id)
    {
        $this->load->model('misc_model');
        $reminder = $this->db->where('id', $id)->get(db_prefix() . 'reminders')->row();

        if (!$reminder) {
            echo json_encode(['success' => false]);
            die;
        }

        if ($reminder->creator != get_staff_user_id() && !is_admin()) {
            ajax_access_denied();
        }

        $this->misc_model->delete_reminder($id);
        echo json_encode(['success' => true, 'message' => _l('deleted', _l('reminder'))]);
    }

    /* Tasks Functions */
    public function tasks_table($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('patient_master_modal', 'tables/tasks'), ['patient_id' => $patient_id]);
    }

    public function add_task()
    {
        if (!has_permission('patients', '', 'create') && !has_permission('patients', '', 'edit')) {
            ajax_access_denied();
        }

        $data = $this->input->post();
        if ($data) {
            $this->load->model('tasks_model');

            $task_data = [
                'name' => $data['name'],
                'startdate' => $data['startdate'],
                'duedate' => $data['duedate'],
                'priority' => $data['priority'],
                'rel_type' => 'customer',
                'rel_id' => $data['patient_id'],
                'status' => 1, // Not Started
            ];

            $id = $this->tasks_model->add($task_data);

            if ($id) {
                echo json_encode(['success' => true, 'message' => _l('added_successfully', _l('task'))]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add task']);
            }
        }
    }

    public function delete_task($id)
    {
        if (!has_permission('tasks', '', 'delete')) {
            ajax_access_denied();
        }
        $this->load->model('tasks_model');
        $success = $this->tasks_model->delete_task($id);
        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('deleted', _l('task'))]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('problem_deleting', _l('task_lowercase'))]);
        }
    }

    /* Invoices, Payments, Expenses */
    public function invoices_table($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }
        $_POST['clientid'] = $patient_id;
        $this->app->get_table_data(module_views_path('patient_master_modal', 'tables/invoices'));
    }

    public function payments_table($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }
        $_POST['clientid'] = $patient_id;
        $this->app->get_table_data(module_views_path('patient_master_modal', 'tables/payments'));
    }

    public function expenses_table($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }
        $_POST['clientid'] = $patient_id;
        $this->app->get_table_data(module_views_path('patient_master_modal', 'tables/expenses'));
    }

    public function prescriptions_table($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('patient_master_modal', 'tables/prescriptions'), ['patient_id' => $patient_id]);
    }

    /* Files Functions */
    public function files_table($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('patient_master_modal', 'tables/files'), ['patient_id' => $patient_id]);
    }

    public function add_file()
    {
        if (!has_permission('patients', '', 'create') && !has_permission('patients', '', 'edit')) {
            ajax_access_denied();
        }

        $patient_id = $this->input->post('patient_id');
        if (!$patient_id) {
            echo json_encode(['success' => false, 'message' => 'No patient ID provided']);
            return;
        }

        $this->load->model('clients_model');
        $uploaded_files = handle_client_attachments_upload($patient_id); // Returns array of saved files or false

        if ($uploaded_files) {
            echo json_encode(['success' => true, 'message' => _l('added_successfully', _l('file'))]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        }
    }

    public function delete_file($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('clients_model');
        $success = $this->clients_model->delete_attachment($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => _l('deleted', _l('file'))]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('problem_deleting', _l('file'))]);
        }
    }

    public function get_patient_overview($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }

        $patient = $this->patients_model->get($patient_id);

        if (!$patient) {
            echo '<div class="alert alert-danger">Patient not found</div>';
            return;
        }

        $data['patient'] = $patient;
        $data['referral_doctor_name'] = "";

        if (!empty($patient->referral_doctor_id)) {
            $this->load->model('staff_model');
            $doctor = $this->staff_model->get($patient->referral_doctor_id);
            if ($doctor) {
                $data['referral_doctor_name'] = $doctor->firstname . " " . $doctor->lastname;
            }
        }

        // Use follow_ups overview view if available, or we might need to copy it too if we want full isolation
        // Implementation plan said "Copy all table definitions", but overview isn't a table.
        // For now, let's continue reusing follow_ups/overview since we didn't copy it in the plan.
        $this->load->view('follow_ups/overview', $data);
    }

    public function get_patient_tickets($patient_id)
    {
        if (!has_permission('patients', '', 'view')) {
            ajax_access_denied();
        }

        $this->load->model('clients_model');
        $this->load->helper('tickets');

        $client = $this->clients_model->get($patient_id);
        if (!$client) {
            echo '';
            return;
        }

        $data['client'] = $client;
        $data['contacts'] = $this->clients_model->get_contacts($patient_id);

        $this->load->view('follow_ups/tickets', $data);
    }
}
