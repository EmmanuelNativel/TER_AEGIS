<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operator_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->table = 'operator';
	}
  
	/**
	* Retourne vrai si l'operateur existe
	*/
	public function exist($l_name,$f_name)
    {
        if ($this->find(array('last_name' => $l_name,'first_name' =>$f_name))) return TRUE;
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
	* Importe les données d'operateurs depuis un tableau de données bidimensionnel
	*/
	public function import($data) {
		
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
				'last_name' => $row['last_name'],
				'first_name' => $row['first_name'],
				'status' => $row['status']
				));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
}
