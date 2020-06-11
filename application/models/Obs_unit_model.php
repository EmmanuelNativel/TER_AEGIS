<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Obs_unit_model extends MY_Model
{

	protected $exp_unit_table = 'exp_unit';
	protected $stage_table = 'sample_stage';

	public function __construct()
	{
		parent::__construct();
		$this->table = 'obs_unit';
	}

	/**
	 * Retourne vrai si la obs unit existe
	 */
	public function exist($str)
	{
		if ($this->find(array('obs_unit_code' => $str)) || $str == NULL) return TRUE;
		else return FALSE;
	}

	/*
		retourne vrai si une obs existe en fonction des param
		(utile lors de la création d'une observation)
	*/
	public function exist_date_exp_unit_phyto_variable($data)
	{
		if ($this->find(array('unit_id' => $data['unit_id'], 'obs_variable' => $data['obs_variable'], 'obs_date' => $data['obs_date'], 'phytorank' => $data['phytorank']))) return TRUE;
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
	 * Importe les données d'observations depuis un tableau de données bidimensionnel
	 */
	//public function import($data, $trial_code, $dataset_id, $constant_header)
	public function import($data, $trial_code, $constant_header)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			$exp_unit_id = $this->db->select('exp_unit_id')
				->get_where($this->exp_unit_table, array('trial_code' => $trial_code, 'unit_code' => $row['unit_code']), 1)
				->result_array()[0]['exp_unit_id'];
			$stage_code = $this->db->select('code_st')
				->get_where($this->stage_table, array('trial_code' => $trial_code, 'st_name' => $row['stage']), 1)
				->result_array()[0]['code_st'];

			foreach ($row as $field => $value) {
				if (!in_array($field, $constant_header)) {
					/* $this->create(array(
				  'phytorank' => (isset($row['phytorank']))? $row['phytorank'] : NULL,
				  'obs_date' => (isset($row['obs_date']))? $row['obs_date'] : NULL,
				  'obs_variable' => $field,
				  'obs_value' => $value,
				  'obs_nb_objects' => (isset($row['nb_object']))? $row['nb_object'] : NULL,
				  'dataset_id' => $dataset_id,
				  'unit_id' => $exp_unit_id,
				  'code_st' => $stage_code
				)); */
					$this->create(array(
						'phytorank' => (isset($row['phytorank'])) ? $row['phytorank'] : NULL,
						'obs_date' => (isset($row['obs_date'])) ? $row['obs_date'] : NULL,
						'obs_variable' => $field,
						'obs_value' => $value,
						'obs_nb_objects' => (isset($row['nb_object'])) ? $row['nb_object'] : NULL,
						'unit_id' => $exp_unit_id,
						'code_st' => $stage_code
					));
				}
			}
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function get_trial_obs($trial_code, $obs_variable, $num_level = 2)
	{
		return $this->db->select('obs_variable, obs_value, exp_unit_id')
			->from($this->table)
			->join($this->exp_unit_table, 'unit_id = exp_unit_id')
			->where('trial_code', $trial_code)
			->where('obs_variable', $obs_variable)
			//->where('num_level', $num_level)
			->get()
			->result_array();
	}

	public function get_trial_var($trial_code)
	{
		return $this->db->select('DISTINCT(obs_variable)')
			->from($this->table)
			->join($this->exp_unit_table, 'unit_id = exp_unit_id')
			->where('trial_code', $trial_code)
			->get()
			->result_array();
	}
}
