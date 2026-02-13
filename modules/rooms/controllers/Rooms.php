<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rooms extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('rooms_model');
    }

    public function index()
    {
        if (!has_permission('rooms', '', 'view')) {
            access_denied('rooms');
        }

        $data['title'] = _l('Buildings');
        $data['buildings'] = $this->rooms_model->get_buildings();
        $this->load->view('manage_buildings', $data);
    }

    /* Building Management */
    public function building($id)
    {
        if (!has_permission('rooms', '', 'view')) {
            access_denied('rooms');
        }

        $building = $this->rooms_model->get_buildings($id);
        if (!$building) {
            show_404();
        }

        $data['title'] = $building->name . ' - ' . _l('Floors');
        $data['building'] = $building;
        $data['floors'] = $this->rooms_model->get_floors_by_building($id);

        $this->load->view('building_details', $data);
    }

    public function add_building()
    {
        if (!has_permission('rooms', '', 'create')) {
            access_denied('rooms');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $this->rooms_model->add_building($data);
            if ($id) {
                set_alert('success', _l('added_successfully', _l('building')));
                redirect(admin_url('rooms'));
            }
        }
    }

    public function update_building($id)
    {
        if (!has_permission('rooms', '', 'edit')) {
            access_denied('rooms');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->rooms_model->update_building($data, $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('building')));
            }
            redirect(admin_url('rooms'));
        }
    }

    public function delete_building($id)
    {
        if (!has_permission('rooms', '', 'delete')) {
            access_denied('rooms');
        }
        $response = $this->rooms_model->delete_building($id);
        set_alert($response['status'] ? 'success' : 'danger', $response['message']);
        redirect(admin_url('rooms'));
    }

    /* Floor Management */
    public function floor($id)
    {
        if (!has_permission('rooms', '', 'view')) {
            access_denied('rooms');
        }

        $floor = $this->rooms_model->get_floor($id);
        if (!$floor) {
            show_404();
        }

        $data['title'] = $floor->name . ' - ' . _l('Rooms');
        $data['floor'] = $floor;
        $data['building'] = $this->rooms_model->get_buildings($floor->building_id);
        $data['rooms'] = $this->rooms_model->get_rooms_by_floor($id);

        // Get items in 'Rooms' group for assignment dropdown
        $this->load->model('invoice_items_model');
        // Fetch group ID for 'Rooms'
        $group = $this->db->where('name', 'Rooms')->get(db_prefix() . 'items_groups')->row();
        if ($group) {
            $data['room_items'] = $this->db->where('group_id', $group->id)->get(db_prefix() . 'items')->result();
        } else {
            $data['room_items'] = [];
        }

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();


        $this->load->view('floor_details', $data);
    }

    public function add_floor()
    {
        if (!has_permission('rooms', '', 'create')) {
            access_denied('rooms');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $building_id = $data['building_id'];
            $id = $this->rooms_model->add_floor($data);
            if ($id) {
                set_alert('success', _l('added_successfully', _l('floor')));
                redirect(admin_url('rooms/building/' . $building_id));
            }
        }
    }

    public function update_floor($id)
    {
        if (!has_permission('rooms', '', 'edit')) {
            access_denied('rooms');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->rooms_model->update_floor($data, $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('floor')));
            }
            // Get building id to redirect back
            $floor = $this->rooms_model->get_floor($id);
            redirect(admin_url('rooms/building/' . $floor->building_id));
        }
    }

    public function delete_floor($id)
    {
        if (!has_permission('rooms', '', 'delete')) {
            access_denied('rooms');
        }

        $floor = $this->rooms_model->get_floor($id);
        $building_id = $floor->building_id;

        $response = $this->rooms_model->delete_floor($id);
        set_alert($response['status'] ? 'success' : 'danger', $response['message']);
        redirect(admin_url('rooms/building/' . $building_id));
    }

    /* Room Assignment */
    public function assign_room()
    {
        if (!has_permission('rooms', '', 'create')) {
            access_denied('rooms');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $response = $this->rooms_model->assign_room_to_floor($data['item_id'], $data['floor_id']);
            set_alert($response['status'] ? 'success' : 'danger', $response['message']);
            redirect(admin_url('rooms/floor/' . $data['floor_id']));
        }
    }

    public function unassign_room($assignment_id, $floor_id)
    {
        if (!has_permission('rooms', '', 'delete')) {
            access_denied('rooms');
        }
        $response = $this->rooms_model->remove_room_assignment($assignment_id);
        set_alert($response['status'] ? 'success' : 'danger', $response['message']);
        redirect(admin_url('rooms/floor/' . $floor_id));
    }
    public function add_room_item()
    {
        if (!has_permission('items', '', 'create')) {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            die;
        }

        if ($this->input->post()) {
            $this->load->model('invoice_items_model');
            $data = $this->input->post();

            // validation
            if (empty($data['description']) || empty($data['rate'])) {
                echo json_encode(['success' => false, 'message' => _l('all_fields_required')]);
                die;
            }

            // Get 'Rooms' group id
            $group = $this->db->where('name', 'Rooms')->get(db_prefix() . 'items_groups')->row();
            if (!$group) {
                // Create group if it doesn't exist? Or just fail? For now, let's try to create or fail.
                // Better to fail and tell user to setup
                echo json_encode(['success' => false, 'message' => 'Rooms item group not found.']);
                die;
            }

            $item_data = [
                'description' => $data['description'],
                'rate' => $data['rate'],
                'group_id' => $group->id,
                'unit' => '', // Optional
                'tax' => '', // Optional
                'tax2' => '' // Optional
            ];

            $id = $this->invoice_items_model->add($item_data);

            if ($id) {
                echo json_encode([
                    'success' => true,
                    'message' => _l('added_successfully', _l('item')),
                    'id' => $id,
                    'name' => $data['description'] . ' - ' . $data['rate']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('problem_adding', _l('item'))]);
            }
        }
    }
}
