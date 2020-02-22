<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectMember_model extends MY_Model{

  protected $user_table = 'public.users';
  protected $project_table = 'public.project';

  public function __construct()
  {
    parent::__construct();
    $this->table = 'project_members';
  }

  /**
  * Créé un nouvel enregistrement avec les valeurs passées en paramètre
  **/
  public function create($values) {
    if ($this->db->set($values)->insert($this->table)) return TRUE;
    return null;
  }

  /**
   * Retourne la liste des membres d'un projet
   */
  public function get_project_members($project_code, $fields=null, $limit=null, $offset=null)
  {

    if (!$fields) $fields = $this->table.'.username, is_leader, email, first_name, last_name, admin';

    $query = $this->db->select($fields)
                    ->from($this->table)
                    ->join($this->user_table, $this->table.'.username = '.$this->user_table.'.username')
                    ->where('project_code', $project_code)
                    ->order_by('is_leader', 'DESC');

    if ($limit) $query->limit($limit, $offset);

    return  $query->get()
                  ->result_array();
  }

  /**
   * Retourne un tableau bidimensionnel contenant tous les project_code et les liste de membres associées.
   */
  public function get_all_projects_members()
  {
    $this->db->select('project_code, username');
    $allMembers = $this->db->get($this->table);
    $return_array = array();

    //On rempli le tableau bidimensionnel avec les données de la requête.
    foreach ($allMembers->result() as $member)
    {
      //si la clé project_code est déjà enregistré, on ajoute simplement le membre en cours
      //sinon on créé une nouvelle entrée pour le project_code et on y ajoute le membre en cours
      if(array_key_exists($member->project_code, $return_array)) {
        $return_array[$member->project_code][] = $member->username;
      } else {
        $return_array[$member->project_code] = array($member->username);
      }
    }

    return $return_array;
  }

  /**
   * Retourne la liste des utilisateurs éxterieurs au projet
   */
  public function like_not_project_members($searched_term, $project_code)
  {
    return $this->db->select()
                    ->from($this->user_table)
                    ->group_start()
                      ->like('LOWER(username)', strtolower($searched_term))
                      ->or_like('LOWER(first_name)', strtolower($searched_term))
                      ->or_like('LOWER(last_name)', strtolower($searched_term))
                    ->group_end()
                    ->where('username NOT IN (SELECT username FROM '.$this->table.' WHERE project_code = \''.$project_code.'\')')
                    ->get()
                    ->result_array();
  }


  /*
    Retourne la liste des projets liés à un utilisateur
  */
    public function get_user_projects($username, $searched_term="")
    {

            return $this->db->select(/*$this->table.'.project_code'*/)
                        ->from($this->table)
                        ->join($this->project_table, $this->table.'.project_code = '.$this->project_table.'.project_code')
                        ->group_start()
                          ->like('LOWER('.$this->table.'.project_code)', strtolower($searched_term))
                          ->or_like('LOWER(project_name)', strtolower($searched_term))
                        ->group_end()
                        ->where($this->table.'.username', $username)
                        ->get()
                        ->result_array();

    }

}
