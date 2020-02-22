<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sample_model extends MY_Model {
	
	protected $exp_unit_table = 'exp_unit';
	protected $stage_table = 'sample_stage';
	
	public function __construct()
	{
		parent::__construct();
		$this->table = 'sample';
	}
  
	/**
	* Retourne vrai si l'echantillon existe
	*/
	public function exist($str)
    {
        if ($this->find(array('sample_id' => $str)) || $str == NULL) return TRUE;
        else return FALSE;
    }
	
	/*
		retourne vrai si un echantillon existe en fonction du code
		(utile lors de la création d'un echantillon)
	*/
	public function exist_sample_code($str) {
		if ($this->find(array('sample_code' => $str))) return TRUE;
        else return FALSE;
	}
	
	/*
		Returne id sample a partir du sample code en parametre
	*/
	public function return_sample_id($sample_code) {
		$sample_id = $this->db->select('sample_id')
							->get_where($this->table, array('sample_code' => $sample_code), 1)
							->result_array()[0]['sample_id'];
		if (isset($sample_id)) return $sample_id;
		else return NULL;
	}
	
    /**
     * Créé un nouvel enregistrement avec les valeurs passées en paramètre
     **/
    public function create($values)
    {
        if ($this->db->set($values)->insert($this->table)) return TRUE;
        return null;
    }

    public function update($values,$code)
    {
      if ($this->db->update($this->table, $values, array('sample_code' => $code))) return TRUE;
      return null;
    }
	
	/*
		Returne id d'exp_unit a partir du sample id en parametre
	*/
	public function return_exp_unit_id($sample_id) {
		$exp_unit_id = $this->db->select('unit_id')
						   ->get_where($this->table, array('sample_id' => $sample_id), 1)
						   ->result_array()[0]['unit_id'];
		if (isset($exp_unit_id)) {
			return $exp_unit_id;
		}
		else return NULL;
	}
	
	
	/**
	* Importe les données d echantillon depuis un tableau de données bidimensionnel
	*/
	public function import($trial_code, $data) {
		
		$this->db->trans_start();
		foreach ($data as $row) {
			
			//recup exp_unit_id
			$exp_unit_id = $this->db->select('exp_unit_id')
								->get_where($this->exp_unit_table, array('trial_code' => $trial_code, 'unit_code' => $row['unit_code']), 1)
								->result_array()[0]['exp_unit_id'];
			
			//recup sample_stage_code
			$stage_code = $this->db->select('code_st')
							   ->get_where($this->stage_table, array('trial_code' => $trial_code, 'st_name' => $row['sample_stage_name']), 1)
							   ->result_array()[0]['code_st'];
			
			$this->create(array(
			'sample_code' => $row['sample_code'],
			'sample_type' => $row['sample_type'],
			'sample_nb_objects' => $row['sample_nb_objects'],
			'sample_plant_code' => $row['sample_plant_code'],
			'sample_entity' => $row['sample_entity_code'],
			'sample_entity_ref' => $row['sample_entity_ref'],
			'sample_entity_level' => $row['sample_entity_level'],
			'sample_st' => $stage_code,
			'unit_id' => $exp_unit_id,
			'harvest_date' => $row['harvest_date']
			));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

  /**
  * Récupère les Sample qui n'ont pas de date de récolte : harvest_date et que le champ sample_nb_objects = NULL
  */
  public function get_not_collected_sample(){
        return $this->db->select('s.sample_code, s.sample_type, s.sample_nb_objects, s.sample_plant_code, s.sample_entity, s.sample_entity_ref, s.sample_entity_level, s.sample_st, s.unit_id, s.harvest_date, t.st_name, s.sample_id,')
                    ->from($this->table.' s')
                    ->join('sample_stage t', 't.code_st = s.sample_st', 'left')
                    ->where(array('harvest_date' => NULL, 'sample_nb_objects' => NULL))
                    ->get()
                    ->result_array();

    }

  /**
  * Récupère le plus petit code disponible pour la prochaine insertion
  * Retourne un int
  */
  public function get_lowest_code_available(){
    $query = $this->db->query("SELECT min(a.sample_code::bigint) - 1 as nb
                              FROM sample a
                              LEFT OUTER JOIN sample b
                              ON b.sample_code::bigint = a.sample_code::bigint - 1
                              WHERE a.sample_code::bigint>1
                              AND b.sample_code::bigint is NULL");

    return $query->result_array()[0]['nb'];
  }

  /**
  * Récupère tous les samples
  */
  public function get_all_sample(){
        return $this->db->get($this->table)
                    ->result_array();
    }

  /**
  * Récupère le nombre d'échantillon dans la table sample
  */
  public function count_sample(){
    return $this->db->count_all($this->table);
  }

  /**
  * Récupère tous les échantillons avec le nombre actuel (après les opération)
  */
  public function get_all_sample_with_current_nb(){
        $query = $this->db->query("SELECT s.sample_code, s.sample_type, s.sample_plant_code, s.sample_entity, s.sample_entity_ref, s.sample_entity_level, s.sample_st, s.unit_id, s.harvest_date, t.st_name, s.sample_id, s.sample_nb_objects - (SELECT COALESCE(SUM(nb_object_remove), 0)
            FROM sample_operation_input o
            WHERE s.sample_id=o.sample_id) as sample_nb_objects
          FROM sample s
          LEFT JOIN sample_stage t
          ON s.sample_st = t.code_st");
        return $query->result_array();
  }

  /**
  * Récupère tous les échantillons récolté avec le nombre actuel et > 0
  */
  public function get_samples_with_current_nb_and_nb_exist(){
        $query = $this->db->query("SELECT s.sample_code, s.sample_type, s.sample_plant_code, s.sample_entity, s.sample_entity_ref, s.sample_entity_level, s.sample_st, s.unit_id, s.harvest_date, t.st_name, s.sample_id, s.sample_nb_objects - (SELECT COALESCE(SUM(nb_object_remove), 0)
            FROM sample_operation_input o
            WHERE s.sample_id=o.sample_id) as sample_nb_objects
          FROM sample s
          LEFT JOIN sample_stage t
          ON s.sample_st = t.code_st
          WHERE s.sample_nb_objects >= 0");
        return $query->result_array();
  }

  /**
  * Récupère tous les échantillons avec le nombre actuel ainsi que le code essai
  */
  public function get_all_sample_with_current_nb_and_trial(){
        $query = $this->db->query("SELECT s.sample_code, s.sample_type, s.sample_plant_code, s.sample_entity, s.sample_entity_ref, t.st_name, e.trial_code, s.sample_nb_objects - (SELECT COALESCE(SUM(nb_object_remove), 0)
            FROM sample_operation_input o
            WHERE s.sample_id=o.sample_id) as sample_nb_objects
          FROM sample s
          LEFT JOIN sample_stage t
          ON s.sample_st = t.code_st
          LEFT JOIN exp_unit e
          ON s.unit_id = e.exp_unit_id
          ");
        return $query->result_array();
  }

  /**
  * Récupère toutes les infos (avec le nombre actuel) d'un échantillon avec le code (pas l'id) passé en paramètre
  */
  public function get_sample_with_current_nb($code){
        $query = $this->db->query("SELECT s.sample_code, s.sample_type, s.sample_plant_code, s.sample_entity, s.sample_entity_ref, s.sample_entity_level, s.sample_st, s.unit_id, s.harvest_date, t.st_name, s.sample_id, e.trial_code, s.sample_nb_objects - (SELECT COALESCE(SUM(nb_object_remove), 0)
            FROM sample_operation_input o
            WHERE s.sample_id=o.sample_id) as sample_nb_objects
          FROM sample s
          LEFT JOIN sample_stage t
          ON s.sample_st = t.code_st
          LEFT JOIN exp_unit e
          ON s.unit_id = e.exp_unit_id
          WHERE s.sample_code = '".$code."'");
        return $query->result_array();
  }
	
}
