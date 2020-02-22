<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends MY_Model{

  //Initialisation des noms des tables du model
  //var $access_rules_table = 'dup_rules';
  protected $access_rules_table = 'dup_rules';
  protected $partner_table = 'project_partner';
  protected $linkedDatasetsTable = 'project_linked_datasets';

  public function __construct()
  {
    parent::__construct();
    $this->table = 'project';
  }

  /**
  * Créé un nouvel enregistrement avec les valeurs passées en paramètre
  **/
  public function create($values) {
    if ($this->db->set($values)->insert($this->table)) return TRUE;
    return null;
  }

  /**
   * Retourne vrai si le code projet existe
   */
  public function exist($str)
  {
    if ($this->find(array('project_code' => $str)) || $str == NULL) return TRUE;
    else                                                            return FALSE;
  }

  /**
   * Retourne vrai si le projet est validé
   */
  public function is_valid($str)
  {
    if ($this->find(array('project_code' => $str, 'is_validated' => PGSQL_TRUE)) || $str == NULL) return TRUE;
    else                                                            return FALSE;
  }

  /**
   * Retourne la liste des projets validés contenant le terme recherché
   */
  public function search_valid_projects($term, $field=NULL)
  {
    $results = array();
    $query_results = $this->like($term, $field);
    foreach ($query_results as $project) {
      if ($project['is_validated'] == PGSQL_TRUE) {
        array_push($results, $project);
      }
    }
    return $results ? $results : NULL;
  }

  /**
   * Retourne la liste des projets validés, accessibles à un utilisateur.
   */
  public function search_available_projects($username, $searched_term, $field=NULL)
  {
    if (!$field)	$field = $this->fields()[0]; // Si le champ n'est pas spécifié on recherche le terme dans le premier champ de la table
    return $this->db->like('LOWER('.$field.')', strtolower($searched_term))
    ->from($this->table)
    ->join($project_user_table, $project_user_table.'.project_code = '.$this->table.'.project_code', 'left')
    ->where('username', $username)
    ->get()
    ->result_array();
  }

  /**
   * Retourne la liste de tous les projets. (en ne selectionnant que les champs donnés en paramètre)
   */
  public function get_all_projects($fields='*') {

    return $this->db->select($fields) //fields par défaut * donc selectionnera tous les champs
    ->from($this->table)
    ->get()
    ->result_array();
  }

  public function get_data()
  {
    return $this->db->select('*')
    ->from($this->table)
    ->join($this->partner_table, $this->partner_table.'.project_code = '.$this->table.'.project_code', 'left')
    ->get()
    ->result_array();
  }

  /**
  * Lie un nouveau dataset à un projet
  **/
  public function addLinkedDataset($dataset_id, $project_code) {

    $data = array(
        'project_code' => $project_code,
        'dataset_id' => $dataset_id
    );

    if ($this->db->insert($this->linkedDatasetsTable, $data)) return TRUE;
    return null;
  }

  /**
  * Retire un dataset d'un projet
  **/
  public function removeLinkedDataset($dataset_id, $project_code) {

    $data = array(
        'project_code' => $project_code,
        'dataset_id' => $dataset_id
    );

    if ($this->db->delete($this->linkedDatasetsTable, $data)) return TRUE;
    return null;
  }

  /**
	* Retourne la liste des projets associés à un $dataset_id (avec limitation pour une eventuelle pagination)
	*/
	public function get_dataset_linked_projects($dataset_id, $limit=null, $offset=null)
	{

		$query = $this->db->select()
											->from($this->linkedDatasetsTable)
											->join($this->table, $this->table.'.project_code = '.$this->linkedDatasetsTable.'.project_code')
											->where($this->linkedDatasetsTable.'.dataset_id', $dataset_id);

		if ($limit) $query->limit($limit, $offset);

    return  $query->get()
                  ->result_array();

	}

}
