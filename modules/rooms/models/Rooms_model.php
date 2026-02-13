<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rooms_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all buildings
     */
    public function get_buildings($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'buildings')->row();
        }
        return $this->db->get(db_prefix() . 'buildings')->result();
    }

    /**
     * Add new building
     */
    public function add_building($data)
    {
        $this->db->insert(db_prefix() . 'buildings', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Building Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update building
     */
    public function update_building($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'buildings', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Building Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete building
     */
    public function delete_building($id)
    {
        // Check if building has floors
        $this->db->where('building_id', $id);
        $floors = $this->db->get(db_prefix() . 'floors')->result();
        if (count($floors) > 0) {
            return ['status' => false, 'message' => 'Cannot delete building with floors. Delete floors first.'];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'buildings');
        if ($this->db->affected_rows() > 0) {
            log_activity('Building Deleted [ID: ' . $id . ']');
            return ['status' => true, 'message' => 'Building deleted successfully'];
        }
        return ['status' => false, 'message' => 'Something went wrong'];
    }

    /**
     * Get floors by building ID
     */
    public function get_floors_by_building($building_id)
    {
        $this->db->where('building_id', $building_id);
        $this->db->order_by('floor_level', 'ASC');
        return $this->db->get(db_prefix() . 'floors')->result_array();
    }

    /**
     * Get single floor
     */
    public function get_floor($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'floors')->row();
    }

    /**
     * Add new floor
     */
    public function add_floor($data)
    {
        $this->db->insert(db_prefix() . 'floors', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Floor Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update floor
     */
    public function update_floor($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'floors', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Floor Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete floor
     */
    public function delete_floor($id)
    {
        // Check if floor has rooms mapped
        $this->db->where('floor_id', $id);
        $rooms = $this->db->get(db_prefix() . 'room_details')->result();
        if (count($rooms) > 0) {
            return ['status' => false, 'message' => 'Cannot delete floor with assigned rooms. Unassign rooms first.'];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'floors');
        if ($this->db->affected_rows() > 0) {
            log_activity('Floor Deleted [ID: ' . $id . ']');
            return ['status' => true, 'message' => 'Floor deleted successfully'];
        }
        return ['status' => false, 'message' => 'Something went wrong'];
    }

    /**
     * Get rooms on a floor
     */
    public function get_rooms_by_floor($floor_id)
    {
        $this->db->select(db_prefix() . 'items.*, ' . db_prefix() . 'room_details.id as assignment_id');
        $this->db->from(db_prefix() . 'room_details');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'room_details.item_id');
        $this->db->where(db_prefix() . 'room_details.floor_id', $floor_id);
        return $this->db->get()->result_array();
    }

    /**
     * Assign item (room) to floor
     */
    public function assign_room_to_floor($item_id, $floor_id)
    {
        // Check if already assigned
        $this->db->where('item_id', $item_id);
        $exists = $this->db->get(db_prefix() . 'room_details')->row();
        if ($exists) {
            return ['status' => false, 'message' => 'Room is already assigned to a floor.'];
        }

        $data = [
            'item_id' => $item_id,
            'floor_id' => $floor_id
        ];
        $this->db->insert(db_prefix() . 'room_details', $data);
        return ['status' => true, 'message' => 'Room assigned successfully'];
    }

    /**
     * Remove room assignment
     */
    public function remove_room_assignment($assignment_id)
    {
        $this->db->where('id', $assignment_id);
        $this->db->delete(db_prefix() . 'room_details');
        return ['status' => true, 'message' => 'Room unassigned successfully'];
    }
}
