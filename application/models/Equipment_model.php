<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Equipment_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->table = 'equipment';
	}
  
	/**
	* Retourne vrai si l'equipement existe
	*/
	public function exist($str)
    {
        if ($this->find(array('equipment_name' => $str))) return TRUE;
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
	* Importe les données d equipements depuis un tableau de données bidimensionnel
	*/
	public function import($data) {
		
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
				'equipment_name' => $row['equipment_name'],
				'class' => $row['equipment_class'],
				'features' => $row['equipment_feature']
				));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
}
