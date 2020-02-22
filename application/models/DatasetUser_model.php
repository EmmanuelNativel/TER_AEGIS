<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DatasetUser_model extends MY_Model{

    protected $user_table = 'public.user';
    protected $dataset_table = 'dataset';

    public function __construct()
    {
        parent::__construct();
        $this->table = 'dataset_user';
    }

    /**
     * Créé un nouvel enregistrement avec les valeurs passées en paramètre
     **/
    public function create($values)
    {
        if ($this->db->set($values)->insert($this->table)) return TRUE;
        return null;
    }


    /**
     * Retourne la liste des membres d'un jeu de données
     */

   public function get_dataset_members($dataset_id)
    {
        return $this->db->from($this->user_table)
            ->join($this->table, $this->table . '.login = ' . $this->user_table . '.login')
            ->where('dataset_id', $dataset_id)
            ->get()
            ->result_array();
    }

    /**
     * Retourne la liste des utilisateurs éxterieurs au jeux de données
     */
    public function like_not_dataset_members($searched_term, $dataset_id)
    {
        return $this->db->select()
            ->from($this->user_table)
            ->group_start()
            ->like('LOWER(login)', strtolower($searched_term))
            ->or_like('LOWER(first_name)', strtolower($searched_term))
            ->or_like('LOWER(last_name)', strtolower($searched_term))
            ->group_end()
            ->where('username NOT IN (SELECT login FROM '.$this->table.' WHERE dataset_id = \''.$dataset_id.'\')')
            ->get()
            ->result_array();
    }

    public function get_allowed_datasets($login, $permissions)
    {
        return $this->db->select($this->dataset_table.'.dataset_id, dataset_type, dataset_name, dataset_description, visibility')
            ->from($this->dataset_table)
            ->where(array('login' => $login, 'is_validated' => PGSQL_TRUE, 'permissions' => $permissions))
            ->where('dataset_owner_login !=', $login)
            ->join($this->table, $this->table.'.dataset_id = '.$this->dataset_table.'.dataset_id')
           ->join($this->dataset_user_table, $this->table.'.dataset_id = '.$this->dataset_user_table.'.datast_id')
            ->get()
            ->result_array();
    }


    public function dataName(){
        return $this->db->select($this->dataset_table.'.dataset_name')
            ->from($this->table)
            ->join($this->dataset_table, $this->dataset_table.'.dataset_id = '.$this->table.'.dataset_id', 'left')
            ->get()
            ->result_array();

    }


}




