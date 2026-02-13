<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facelink_model extends App_Model
{
    public function create($staff_id, $name, $created_by = null, $is_global = 0)
    {
        $token = bin2hex(random_bytes(16));
        $data = [
            'staff_id'    => $staff_id,
            'name'        => $name,
            'token'       => $token,
            'is_active'   => 1,
            'is_global'   => $is_global ? 1 : 0,
            'created_by'  => $created_by,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'face_links', $data);
        $data['id'] = $this->db->insert_id();
        return $data;
    }

    public function all()
    {
        return $this->db->select('fl.*, s.firstname, s.lastname, s.email, s.active as staff_active, r.name as role_name')
            ->from(db_prefix() . 'face_links as fl')
            ->join(db_prefix() . 'staff as s', 's.staffid = fl.staff_id', 'left')
            ->join(db_prefix() . 'roles as r', 'r.roleid = s.role', 'left')
            ->order_by('fl.id', 'desc')
            ->get()
            ->result_array();
    }

    public function toggle($id, $active)
    {
        $this->db->where('id', $id)->update(db_prefix() . 'face_links', [
            'is_active' => $active ? 1 : 0,
        ]);
    }

    public function delete($id)
    {
        $this->db->where('id', $id)->delete(db_prefix() . 'face_links');
    }

    public function by_token($token)
    {
        return $this->db->select('*')
            ->from(db_prefix() . 'face_links')
            ->where('token', $token)
            ->where('is_active', 1)
            ->limit(1)
            ->get()
            ->row();
    }

    public function upsert_global($name, $created_by = null)
    {
        $existing = $this->db->select('*')
            ->from(db_prefix() . 'face_links')
            ->where('is_global', 1)
            ->limit(1)
            ->get()
            ->row_array();

        if ($existing) {
            $token = bin2hex(random_bytes(16));
            $this->db->where('id', $existing['id'])->update(db_prefix() . 'face_links', [
                'name'        => $name,
                'token'       => $token,
                'is_active'   => 1,
                'created_by'  => $created_by,
                'last_used_at'=> null,
            ]);
            $existing['token'] = $token;
            $existing['name']  = $name;
            $existing['is_active'] = 1;
            return (object) $existing;
        }

        return (object) $this->create(0, $name, $created_by, 1);
    }

    public function global_link()
    {
        return $this->db->select('*')
            ->from(db_prefix() . 'face_links')
            ->where('is_global', 1)
            ->limit(1)
            ->get()
            ->row();
    }

    public function touch($id)
    {
        $this->db->where('id', $id)->update(db_prefix() . 'face_links', [
            'last_used_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
