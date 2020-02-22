<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Variable_ontology_model extends MY_Model
{
	
    public function __construct()
    {
        parent::__construct();
        $this->table = 'variable_ontology';
    }

    /**
     * Retourne vrai si la paire variable/ontology existe
     */
    public function exist($strvariable,$strontology)
    {
        if ($this->find(array('variable_code' => $strvariable,'ontology_id' => $strontology)) || $str == NULL)  return TRUE;
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

