<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analysis_model extends MY_Model {
	
	protected $sample_table = 'sample';
	protected $variable_table = 'variable';
	
	public function __construct()
	{
		parent::__construct();
		$this->table = 'analysis';
	}
  
	/**
	* Retourne vrai si l'analyse existe
	*/
	public function exist($str)
    {
        if ($this->find(array('analysis_id' => $str)) || $str == NULL) return TRUE;
        else return FALSE;
    }
	
	/*
		retourne vrai si une analyse existe en fonction des param
		(utile lors de la création d'une analyse)
	*/
	public function exist_lab_analysis_code($str) {
		if ($this->find(array('lab_internal_code' => $str))) return TRUE;
        else return FALSE;
	}
	
	/*
		retourne vrai si une analyse existe en fonction de la date, de la variable et
		de l'id echantillon (utile lors de la création d'une analyse)
	*/
	public function exist_date_variable_sampleid($data) {
		if ($this->find(array('analysis_date' => $data['analysis_date'], 'analysis_variable' => $data['analysis_variable'], 'sample_id' => $data['sample_id']))) return TRUE;
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
	* Importe les données d analyses depuis un tableau de données bidimensionnel
	*/
	public function import($data, $trial_code, $constant_header) {
		
		$this->db->trans_start();
		foreach ($data as $row) {
			$sample_id =  $this->db->select('sample_id')
								->get_where($this->sample_table, array('sample_code' => $row['sample_code']), 1)
								->result_array()[0]['sample_id'];
			if (isset($sample_id)) {
				foreach ($row as $field => $value) {
					if (!in_array($field, $constant_header)) {
						$this->create(array(
							'lab_internal_code' => $row['lab_internal_analysis_code'],
							'sample_id' => $sample_id,
							'analysis_variable' => $field,
							'analysis_type' => $row['analysis_type'],
							'analysis_date' => $row['analysis_date'],
							'analysis_value' => $value,
							'analysis_status' => $row['analysis_status']
							));
					}
				}
			}
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
}
