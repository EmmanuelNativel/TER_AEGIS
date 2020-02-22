<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'notifications';
    }

    public function get_notifications($login, $limit = 0)
    {
        return $this->db->select()
            ->from($this->table)
            ->where('target_login', $login)
            ->order_by('created_time', 'desc')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    /*public function remove_notifications($id) {
        $this->db->delete('notifications', array('id' => $id));
        return $this->db->affected_rows() > 1 ? true:false;

    }*/

}