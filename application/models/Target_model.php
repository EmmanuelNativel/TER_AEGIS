<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Target_model extends MY_Model
{
	
    public function __construct()
    {
        parent::__construct();
        $this->table = 'target';
    }

    /**
     * Retourne vrai si le name target existe
     */
    public function exist($str)
    {
        if ($this->find(array('target_name' => $str)) || $str == NULL)  return TRUE;
        else                                          return FALSE;
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
	* Importe les données de target depuis un tableau de données bidimensionnel
	*/
	public function import($data)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
			'target_name' => $row['target_name']));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}
	
}