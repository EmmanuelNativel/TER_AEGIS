<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends MY_Model
{
    public function __construct()
    {
      parent::__construct();
      $this->table = 'users';
    }

/**
     * Retourne TRUE si l'utilisateurs données existe dans la table des utilisateurs de DAPHNE.
     */
    public function exist($input)
    {

      if ($this->find(array('username' => $input))) return TRUE;
      else                                       return FALSE;
    }

    public function add_user($pseudo, $mdp, $email, $first_name, $last_name, $organization = Null) {
        return $this->db->set('login', $pseudo)
                        ->set('password', $mdp)
                        ->set('email', $email)
                        ->set('first_name', $first_name)
                        ->set('last_name', $last_name)
                        ->set('partner', $organization)
                        ->set('creation_date', 'NOW()', false)
                        ->insert($this->table);

    }
    public function remove_user($pseudo) {
      $this->db->where('username', $pseudo);
      return $this->db->delete($this->table);
    }

    public function count_members($where = array()) {
        return (int) $this->db->where($where)
                        ->count_all_results($this->table);
    }

    public function get_user_data($pseudo) {
        return $this->db->select('username, email, first_name, last_name, partner, admin, created_on_readable')
                    ->from($this->table)
                    ->where('username', $pseudo)
                    ->get()
                    ->result_array()[0];
    }

    public function get_all_users_data(){
        return $this->db->select('username, email, first_name, last_name, partner, admin, created_on_readable')
                    ->from($this->table)
                    ->get()
                    ->result_array();
    }


    public function get_user_hash($pseudo)
    {
        return $this->db->select('password')
                        ->from($this->table)
                        ->where('username', $pseudo)
                        ->limit(1)
                        ->get()
                        ->result_array()[0]["password"];
    }

    public function change_status($pseudo, $value)
    {
      $this->db->where('username', $pseudo);
      return $this->db->update($this->table, array('admin' => $value));
    }

    public function set_user_data($pseudo, $data)
    {
      $this->db->trans_start();
      $this->db->where('username', $pseudo);
      $this->db->update($this->table, $data);
      $this->db->trans_complete();

      return $this->db->trans_status();
    }
}
