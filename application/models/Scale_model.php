<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scale_model extends MY_Model
{
	protected $scale_ontology_table = 'scale_ontology';
    protected $ontology_table = 'ontology';
	
    public function __construct()
    {
        parent::__construct();
        $this->table = 'scale';
    }

    /**
     * Retourne vrai si le code echelle /unite existe
     */
    public function exist($str)
    {
        if ($this->find(array('scale_code' => $str)) || $str == NULL)  return TRUE;
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
	* Importe les données de scale depuis un tableau de données bidimensionnel
	*/
	public function import($data)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
			'scale_code' => $row['scale_code'],
			'scale_name' => (isset($row['scale_name']))? $row['scale_name'] : NULL,
			'scale_type' => (isset($row['scale_type']))? $row['scale_type'] : NULL,
			'scale_level' => (isset($row['scale_level']))? $row['scale_level'] : NULL
			));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}

	/*
     * Retourne l'ontology associé au scale
     */
    public function get_ontology($scale_code)
    {
        $this->db->select('*');
        $this->db->from($this->scale_ontology_table);
        $this->db->join($this->ontology_table, 'scale_ontology.ontology_id = ontology.ontology_id', 'left');
        $this->db->where('scale_ontology.scale_code',$scale_code);
        $result = $this->db->get()->result_array();
        if ($result != null) {
            return $result;
        }else{
            return null;
        }
    }
	
}