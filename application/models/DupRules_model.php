<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DupRules_model extends MY_Model{

    protected $user_table = 'users';
    protected $dataset_table = 'dataset';
    protected $project_table = 'project';

    public function __construct()
    {
        parent::__construct();
        $this->table = 'dup_rules';
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
     * Insère plusieurs entrées d'un coup grâce à un $bidimensionalArray
     * qui est un tableau contenant des tableaux associatifs.
     * Exemple : array( array('username' => 'blabla','dataset_id' => 18) , ... )
     **/
    public function insertMultipleUsers($bidimensionalArray)
    {
      if ($this->db->insert_batch($this->table, $bidimensionalArray)) return TRUE;
      return null;
    }

    /**
     * Retourne la liste des membres d'un jeu de données
     */

    public function get_dataset_members($dataset_id, $limit=null, $offset=null)
    {
      $query = $this->db->select($this->table.'.username,'.$this->table.'.permissions,'.$this->user_table.'.first_name,'.$this->user_table.'.last_name')
            ->from($this->user_table)
            ->join($this->table, $this->table . '.username = ' . $this->user_table . '.username')
            ->where('dataset_id', $dataset_id)
            ->group_by($this->table.'.username,'.$this->table.'.permissions,'.$this->user_table.'.first_name,'.$this->user_table.'.last_name');

      if ($limit) $query->limit($limit, $offset);

      return  $query->get()
                    ->result_array();
    }

    /**
     * Retourne la liste des utilisateurs éxterieurs au jeux de données
     * Si dataset_id est null alors on retourne la liste de tous les utilisateurs sauf celui connecté
     */
    public function like_not_dataset_members($searched_term, $dataset_id = null)
    {
        $query = $this->db->select()
            ->from($this->user_table)
            ->group_start()
            ->like('LOWER(username)', strtolower($searched_term))
            ->or_like('LOWER(first_name)', strtolower($searched_term))
            ->or_like('LOWER(last_name)', strtolower($searched_term))
            ->group_end();

        if (!is_null($dataset_id)) {
            $query->where('username NOT IN (SELECT username FROM '.$this->table.' WHERE dataset_id = \''.$dataset_id.'\')');
        }
        else {
            $username = $this->session->userdata('username');
            $query->where("username <> '" . $username . "'");
        }


        return $query->get()->result_array();


    }

	/*
		Retourne la liste des jeux de données pour un utilisateur et un type de droit
	*/
    public function get_allowed_datasets($login, $permissions)
    {
        return $this->db->select($this->dataset_table.'.dataset_id, dataset_type, dataset_name, dataset_description, visibility')
            ->from($this->dataset_table)
            ->where(array('username' => $login, 'permissions' => $permissions))
            ->where('dataset_owner_login !=', $login)
            ->join($this->table, $this->table.'.dataset_id = '.$this->dataset_table.'.dataset_id')
            ->get()
            ->result_array();
    }

	/*
		Retourne la liste de tous les noms de jeux de données
	*/
    public function dataName(){
        return $this->db->select($this->dataset_table.'.dataset_name')
            ->from($this->table)
            ->join($this->dataset_table, $this->dataset_table.'.dataset_id = '.$this->table.'.dataset_id', 'left')
            ->get()
            ->result_array();

    }


	/*
		Retourne la liste des projets auxquels l'utilsateur logué prend part
	*/
	public function like_user_projects($searched_term, $login)
    {
		return $this->db->select()
					->from($this->project_table)
					->group_start()
						->like('LOWER(project_code)', strtolower($searched_term))
					->group_end()
					->where('project_code IN (SELECT project_code FROM '.$this->table .' WHERE username = \''.$login.'\')')
					->get()
					->result_array();
    }


	//Retourne la liste des datasets avec permission d écriture en fonction d'un user et d'un project
	public function get_datasets_by_project_by_user_for_permission($login, $project_code, $permission)
    {
        return $this->db->select()
            ->from($this->dataset_table)
            ->where('dataset_id IN (SELECT dataset_id FROM '.$this->table.' WHERE (project_code = \''.$project_code.'\' AND username =\''. $login .'\' AND permissions =\'' . $permission . '\'  AND dataset_id IS NOT NULL)')
            ->get()
            ->result_array();
    }


}
