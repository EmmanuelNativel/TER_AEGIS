<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group_Dataset_model extends MY_Model{

  protected $dataset_table = 'dataset';
  protected $group_table = 'group';
  protected $group_user_table = 'group_user';

  public function __construct()
  {
    parent::__construct();
    $this->table = 'group_dataset';
  }

  public function get_allowed_datasets($login, $permissions)
  {
    return $this->db->select($this->dataset_table.'.dataset_id, dataset_type, dataset_name, dataset_description, visibility')
                    ->from($this->dataset_table)
                    ->where(array('login' => $login, 'is_validated' => PGSQL_TRUE, 'permissions' => $permissions))
                    ->where('dataset_owner_login !=', $login)
                    ->join($this->table, $this->table.'.dataset_id = '.$this->dataset_table.'.dataset_id')
                    ->join($this->group_user_table, $this->table.'.group_id = '.$this->group_user_table.'.group_id')
                    ->get()
                    ->result_array();
  }

  public function get_group_datasets($group_id)
  {
    return $this->db->select()
                    ->from($this->dataset_table)
                    ->join($this->table, $this->table.'.dataset_id = '.$this->dataset_table.'.dataset_id')
                    ->where('group_id', $group_id)
                    ->get()
                    ->result_array();
  }

}
