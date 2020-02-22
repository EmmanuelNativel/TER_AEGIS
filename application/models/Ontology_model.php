<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ontology_model extends MY_Model
{
	
    public function __construct()
    {
        parent::__construct();
        $this->table = 'ontology';
    }

    /**
     * Retourne vrai si le id ontology existe
     */
    public function exist($str)
    {
        if ($this->find(array('ontology_id' => $str)) || $str == NULL)  return TRUE;
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
	
	
}

