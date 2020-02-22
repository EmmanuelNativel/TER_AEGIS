<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->table = 'product';
	}
  
	/**
	* Retourne vrai si le produit existe
	*/
	public function exist($str)
    {
        if ($this->find(array('trading_name' => $str))) return TRUE;
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
	* Importe les données de produits depuis un tableau de données bidimensionnel
	*/
	public function import($data) {
		
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
				'trading_name' => $row['trading_name'],
				'firm' => $row['firm'],
				'approval' => $row['approval'],
				'recommended_amount' => $row['recommended_amount']
				));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
}
