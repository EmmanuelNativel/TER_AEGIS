<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group_user_model extends MY_Model{

  protected $user_table = 'public.user';

  public function __construct()
  {
    parent::__construct();
    $this->table = 'group_user';
  }

  /**
  * Créé un nouvel enregistrement avec les valeurs passées en paramètre
  **/
  public function create($values) {
    if ($this->db->set($values)->insert($this->table)) return TRUE;
    return null;
  }

  /**
   * Retorune la liste des utilisateurs liés à un groupe donné
   */
  public function get_members($group_id)
  {
    return $this->db->select()
                    ->from($this->user_table)
                    ->join($this->table, $this->table.'.login = '.$this->user_table.'.login', 'left')
                    ->where('group_id', $group_id)
                    ->get()
                    ->result_array();
  }

  /**
   * Retourne la liste des utilisateurs ne faisant pas partie d'un groupe donné (excepté l'utilisateur connecté)
   */
  public function get_not_members($searched_term, $group_id)
  {
    return $this->db->select()
                    ->from($this->user_table)
                    ->group_start()
                      ->like('LOWER(login)', strtolower($searched_term))
                      ->or_like('LOWER(first_name)', strtolower($searched_term))
                      ->or_like('LOWER(last_name)', strtolower($searched_term))
                    ->group_end()
                    ->where('username NOT IN (SELECT login FROM '.$this->table.' WHERE group_id = \''.$group_id.'\')')
                    ->where('username !=', $this->session->userdata('username'))
                    ->get()
                    ->result_array();
  }

}
