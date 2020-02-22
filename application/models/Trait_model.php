<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trait_model extends MY_Model
{

    protected $method_table = 'method';
    protected $scale_table = 'scale';
    protected $trait_ontology_table = 'trait_ontology';
    protected $ontology_table = 'ontology';

    public function __construct()
    {
        parent::__construct();
        $this->table = 'trait';
    }

	/**
     * Retourne vrai si le code trait existe
     */
    public function exist($str)
    {
        if ($this->find(array('trait_code' => $str)) || $str == NULL)  return TRUE;
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
	* Importe les données de traits depuis un tableau de données bidimensionnel
	*/
	public function import($data)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
			'trait_code' => $row['trait_code'],
			'trait_name' => (isset($row['trait_name']))? $row['trait_name'] : NULL,
			'trait_description' => (isset($row['trait_description']))? $row['trait_description'] : NULL,
			'trait_entity' => $row['trait_entity_code'],
			'trait_target' => $row['trait_target_name'],
			'trait_author' => (isset($row['trait_author']))? $row['trait_author'] : NULL
			));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}
	
    public function trait_all($trait_code)
    {
        $query = $this->db->select('trait_name, trait_description')
            ->from($this->table)
            ->where('trait_code', $trait_code)
            ->get()
            ->result_array();
        return $query;
    }

    public function Methods_all($method_code)
    {
        $query = $this->db->select('method_name, method_class, method_description, method_formula ')
            ->from($this->method_table)
            ->where('method_code', $method_code)
            ->get()
            ->result_array();
        return $query;
    }

    public function Scale_all($scale_code)
    {
        $query = $this->db->select('scale_name, scale_type, scale_level')
            ->from($this->scale_table)
            ->where('scale_code', $scale_code)
            ->get()
            ->result_array();
        return $query;
    }

    
    /*
     * Retourne l'ontology associé au trait code
     */
    public function get_ontology($trait_code)
    {
        $this->db->select('*');
        $this->db->from($this->trait_ontology_table);
        $this->db->join($this->ontology_table, 'trait_ontology.ontology_id = ontology.ontology_id', 'left');
        $this->db->where('trait_ontology.trait_code',$trait_code);
        $result = $this->db->get()->result_array();
        if ($result != null) {
            return $result;
        }else{
            return null;
        }
    }

}