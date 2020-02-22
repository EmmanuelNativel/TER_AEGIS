<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Itk_model extends MY_Model
{
	protected $exp_unit_table = 'exp_unit';
	
    public function __construct()
    {
        parent::__construct();
        $this->table = 'itk';
    }

    /**
     * Retourne vrai si le code itk existe
     */
    public function exist($str)
    {
        if ($this->find(array('itk_id' => $str)) || $str == NULL)  return TRUE;
        else return FALSE;
    }
	
	/*
		retourne vrai si un itk existe en fonction des param
		(utile lors de la création d'un itk)
	*/
	public function exist_expunit_variable_date($data) {
		if ($this->find(array('exp_unit_id' => $data['exp_unit_id'], 'itk_variable' => $data['itk_variable'], 'itk_date' => $data['itk_date']))) return TRUE;
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
	* Importe les données d'itk depuis un tableau de données bidimensionnel
	*/
	public function import($data, $trial_code, $constant_header)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			
			$exp_unit_id = $this->db->select('exp_unit_id')
								->get_where($this->exp_unit_table, array('trial_code' => $trial_code, 'unit_code' => $row['unit_code']), 1)
								->result_array()[0]['exp_unit_id'];
			
			foreach ($row as $field => $value) {
				if (!in_array($field, $constant_header)) {
					$this->create(array(
					'itk_duration' => (isset($row['duree']))? $row['duree'] : NULL,
					'itk_date' => (isset($row['date']))? $row['date'] : NULL,
					'itk_variable' => $field,
					'itk_value' => $value,
					'exp_unit_id' => $exp_unit_id
					));
				}
			}
		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}
	
}