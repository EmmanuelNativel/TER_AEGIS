<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sample_stage_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
		$this->table = 'sample_stage';
	}

  /**
  * Retourne vrai si le code stade existe
  */
  public function exist($str)
  {
      if ($this->find(array('code_st' => $str)) || $str == NULL) return TRUE;
      else return FALSE;
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
  * Modifie un enregistrement dont l'identifiant et les nouvelles valeurs sont passées en paramètre
  **/
  public function update($values,$id)
  {
    if ($this->db->update($this->table, $values, array('code_st' => $id))) return TRUE;
    return null;
  }

  /**
  * Modifie un enregistrement dont l'identifiant est passé en paramètre
  **/
  public function remove($id) {
    if($this->db->delete($this->table, array('code_st' => $id))) return TRUE;
    return null;
  }
	
	/**
	* Importe les données de stade echantillon depuis un tableau de données bidimensionnel
	*/
	public function import($data)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
			'trial_code' => $row['trial_code'],
			'st_name' => (isset($row['st_name']))? $row['st_name'] : NULL,
			'st_physio_stage' => (isset($row['st_physio_stage']))? $row['st_physio_stage'] : NULL));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}

  /**
  * Récupère toutes les données de la table entity
  */
  public function get_all_sample_stage_data(){
        return $this->db->select()
                    ->from($this->table)
                    ->get()
                    ->result_array();
    }
	
}
