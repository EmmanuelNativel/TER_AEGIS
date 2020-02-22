<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dataset_model extends MY_Model {

	protected $dataset_user_table = 'dataset_user';
	protected $access_rules_table = 'dup_rules';
	protected $dataset_wd_table = 'dataset_wd';
	protected $dataset_obs_table = 'dataset_obs';
	protected $obs_unit_table = 'obs_unit';
	protected $exp_unit_table = 'exp_unit';
	protected $weather_day_table = 'weather_day';
  protected $linked_project_table = 'project_linked_datasets';

	public function __construct()
	{
		parent::__construct();
		$this->table = 'dataset';
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
	 * Retourne vrai si le id de dataset existe
	 */
	public function exist($str)
	{
	  if ($this->find(array('dataset_id' => $str)) || $str == NULL) return TRUE;
	  else                                                            return FALSE;
	}


	/*
		Créer les données de dataset_analysis depuis un tableau
	*/
	public function create_dataset_analysis($data) {

		$analysis_id = $this->db->select('analysis_id')
							->get_where($this->analysis_table, array('lab_internal_code' => $data['lab_internal_code'], 'analysis_date' => $data['analysis_date'], 'analysis_variable' => $data['analysis_variable'], 'sample_id' => $data['sample_id']), 1)
							->result_array()[0]['analysis_id'];

		$data_to_import = array('dataset_id' => $data['dataset_id'], 'analysis_id' => $analysis_id);
		if ($this->db->insert($this->dataset_analysis_table, $data_to_import)) return TRUE;
		else return FALSE;
	}

	/**
	* Crée les données de dataset_observations depuis un tableau
	*/
	public function create_dataset_obs($data)
	{
		$obs_unit_code = $this->db->select('obs_unit_code')
							->get_where($this->obs_unit_table, array('obs_date' => $data['obs_date'], 'obs_variable' => $data['obs_variable'], 'unit_id' => $data['unit_id']), 1)
							->result_array()[0]['obs_unit_code'];

		$data_to_import = array('dataset_id' => $data['dataset_id'], 'obs_unit_id' => $obs_unit_code);
		if ($this->db->insert($this->dataset_obs_table, $data_to_import)) return TRUE;
		else return FALSE;
	}

	/**
	* Crée les données de dataset_itk depuis un tableau
	*/
	public function create_dataset_itk($data)
	{
		$itk_id = $this->db->select('itk_id')
							->get_where($this->itk_table, array('itk_date' => $data['itk_date'], 'itk_variable' => $data['itk_variable'], 'exp_unit_id' => $data['exp_unit_id']), 1)
							->result_array()[0]['itk_id'];

		$data_to_import = array('dataset_id' => $data['dataset_id'], 'itk_id' => $itk_id);
		if ($this->db->insert($this->dataset_itk_table, $data_to_import)) return TRUE;
		else return FALSE;
	}

	/*
		Importe les données d'analyse depuis un tableau de données bidimensionnel
	*/
	public function import_dataset_analysis($data, $trial_code, $dataset_id, $constant_header) {
		$this->db->trans_start();
		foreach ($data as $row) {
			foreach ($row as $field => $value) {
				if (!in_array($field, $constant_header)) {

					$sample_id =  $this->db->select('sample_id')
								->get_where($this->sample_table, array('sample_code' => $row['sample_code']), 1)
								->result_array()[0]['sample_id'];

					$analysis_id = $this->db->select('analysis_id')
										->get_where($this->analysis_table, array('lab_internal_code' => $row['lab_internal_analysis_code'],'analysis_date' => $row['analysis_date'], 'analysis_variable' => $field, 'sample_id' => $sample_id), 1)
										->result_array()[0]['analysis_id'];

					$data_to_import = array('dataset_id' => $dataset_id, 'analysis_id' => $analysis_id);
					$this->db->insert($this->dataset_analysis_table, $data_to_import);
				}
			}

		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	* Importe les données de dataset_observations depuis un tableau de données bidimensionnel
	*/
	public function import_dataset_obs($data, $trial_code, $dataset_id, $constant_header)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			foreach ($row as $field => $value) {
				if (!in_array($field, $constant_header)) {

					$exp_unit_id = $this->db->select('exp_unit_id')
								->get_where($this->exp_unit_table, array('trial_code' => $trial_code, 'unit_code' => $row['unit_code']), 1)
								->result_array()[0]['exp_unit_id'];

					$obs_unit_code = $this->db->select('obs_unit_code')
										->get_where($this->obs_unit_table, array('obs_date' => $row['obs_date'], 'obs_variable' => $field, 'unit_id' => $exp_unit_id), 1)
										->result_array()[0]['obs_unit_code'];

					$data_to_import = array('dataset_id' => $dataset_id, 'obs_unit_id' => $obs_unit_code);
					$this->db->insert($this->dataset_obs_table, $data_to_import);
				}
			}

		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}

	/**
	* Importe les données de dataset_itk depuis un tableau de données bidimensionnel
	*/
	public function import_dataset_itk($data, $trial_code, $dataset_id, $constant_header)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			foreach ($row as $field => $value) {
				if (!in_array($field, $constant_header)) {

					$exp_unit_id = $this->db->select('exp_unit_id')
								->get_where($this->exp_unit_table, array('trial_code' => $trial_code, 'unit_code' => $row['unit_code']), 1)
								->result_array()[0]['exp_unit_id'];

					$itk_id = $this->db->select('itk_id')
										->get_where($this->itk_table, array('itk_date' => $row['date'], 'itk_variable' => $field, 'exp_unit_id' => $exp_unit_id), 1)
										->result_array()[0]['itk_id'];

					$data_to_import = array('dataset_id' => $dataset_id, 'itk_id' => $itk_id);
					$this->db->insert($this->dataset_itk_table, $data_to_import);
				}
			}

		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}

	/**
	* Importe les données de dataset_wd depuis un tableau de données bidimensionnel
	*/
	public function import_dataset_wd($data, $dataset_id, $constant_header)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			foreach ($row as $field => $value) {
				if (!in_array($field, $constant_header)) {

					$wd_id = $this->db->select('wd_id')
										->get_where($this->weather_day_table, array('wscode' => $row['wscode'], 'weatherdate' => $row['weatherdate'], 'weather_variable' => $field), 1)
										->result_array()[0]['wd_id'];

					$data_to_import = array('dataset_id' => $dataset_id, 'wd_id' => $wd_id);
					$this->db->insert($this->dataset_wd_table, $data_to_import);
				}
			}

		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}

	public function get_handy_datasets($login, $permissions=NULL) // TODO: A revoir
	{
		if ($permissions) {
		  return $this->db->select()
						  ->from($this->table)
						  ->where('username', $login)
						  ->where('permissions', $permissions)
						  ->join($this->access_rules_table, $this->table.'.dataset_id = '.$this->access_rules_table.'.dataset_id', 'left')
						  ->get()
						  ->result_array();
		} else {
		  return $this->db->select()
						  ->from($this->table)
						  ->where('username', $login)
						  ->join($this->access_rules_table, $this->table.'.dataset_id = '.$this->access_rules_table.'.dataset_id', 'left')
						  ->get()
						  ->result_array();
		}
	}

	public function count_handy_datasets($login)// A MODIFIER
	{
		return $this->db->select()
						->from($this->table)
						->where('username', $login)
						->join($this->access_rules_table, $this->table.'.dataset_id = '.$this->access_rules_table.'.dataset_id', 'left')
						->where($this->access_rules_table.'.username', $login)
						->count_all_results();
	}

	public function get_user_datasets($login)// A MODIFIER
	{
		return $this->db->select($this->table.'.dataset_id')
			->from($this->table)
			->join($this->access_rules_table, $this->access_rules_table.'.dataset_id = '.$this->table.'.dataset_id', 'left')
			->where($this->access_rules_table.'.username', $login)
			->get()
			->result_array();
	}

	public function get_handy_project_datasets($project_code, $limit=null, $offset=null)
	{

		$query = $this->db->select($this->table.'.dataset_name,'.$this->linked_project_table.'.dataset_id')
											->from($this->table)
											->join($this->linked_project_table, $this->table.'.dataset_id = '.$this->linked_project_table.'.dataset_id', 'left')
											->where('project_code', $project_code);

		if ($limit) $query->limit($limit, $offset);

    return  $query->get()
                  ->result_array();

	}

	public function count_all_handy_project_datasets($project_code)
	{
		return	$this->db->select($this->table.'.dataset_name,'.$this->linked_project_table.'.dataset_id')
										 ->from($this->table)
										 ->join($this->linked_project_table, $this->table.'.dataset_id = '.$this->linked_project_table.'.dataset_id', 'left')
										 ->where('project_code', $project_code)
										 ->count_all_results();
	}

	/**
	* Retourne la liste (filtrée par un terme recherché) des datasets éxterieurs à un projet
	*/
	public function like_not_project_datasets($searched_term, $project_code)
	{
			return $this->db->select()
					->from($this->table)
					->group_start()
							->like('LOWER(dataset_name)', strtolower($searched_term))
					->group_end()
					->where('dataset_id NOT IN (SELECT dataset_id FROM '.$this->linked_project_table.' WHERE project_code = \''.$project_code.'\' )')
					->get()
					->result_array();
	}

}
