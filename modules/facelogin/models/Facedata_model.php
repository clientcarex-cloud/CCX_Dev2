<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facedata_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Retrieve hosting_account data.
     *
     * @param int|string $id Optional ID of the hosting_account.
     * @return array|object Returns all records if no ID is provided, otherwise returns a single record.
     */
    public function get($id = ''){
        if($id == ''){
            return  $this->db->get(db_prefix().'face_data')->result_array();
        }else{
            $this->db->where('id',$id);
            return $this->db->get(db_prefix().'face_data')->row();
        }
    }
    public function get_by_user($user_id, $user_type = 'staff') {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $query = $this->db->get(db_prefix() . 'face_data');
    
        return $query->row(); // returns null if not found
    }
    /**
     * Add a new face_data record.
     *
     * @param array $data Array of face_data data.
     * @return int The ID of the newly inserted face_data.
     */
    public function add($data){
        $this->db->insert(db_prefix() . 'face_data', $data);
        return $this->db->insert_id();
    }
   
    public function get_by_user_id($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', 'staff'); // optional: if you're storing staff
        $this->db->where('is_active', 1);
        return $this->db->get(db_prefix() . 'face_data')->row();
    }
}









