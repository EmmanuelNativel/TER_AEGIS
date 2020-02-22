<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Weather_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->table = 'weather_day';
	}
  
	/**
	* Retourne vrai si la meteo existe
	*/
	public function exist($data)
    {
        if ($this->find(array('' => $data))) return TRUE;  //wscode , date , variable ou wd_id ?
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
	* Importe les données meteos depuis un tableau de données bidimensionnel
	*/
	public function import($data,$constant_header) {
		
		$this->db->trans_start();
		foreach ($data as $row) {
			foreach ($row as $field => $value) {
				if (!in_array($field, $constant_header)) {
			
					$this->create(array(
					'wscode' => $row['wscode'],
					'weatherdate' => $row['weatherdate'],
					'weather_variable' => $field,
					'weather_value' => $value
					));
				}
			}
		}
		

		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
}
