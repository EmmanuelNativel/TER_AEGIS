<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Method_model extends MY_Model
{
	protected $method_ontology_table = 'method_ontology';
    protected $ontology_table = 'ontology';

    public function __construct()
    {
        parent::__construct();
        $this->table = 'method';
    }

    /**
     * Retourne vrai si le code methode existe
     */
    public function exist($str)
    {
        if ($this->find(array('method_code' => $str)) || $str == NULL)  return TRUE;
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
	* Importe les données de method depuis un tableau de données bidimensionnel
	*/
	public function import($data)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
			'method_code' => $row['method_code'],
			'method_name' => (isset($row['method_name']))? $row['method_name'] : NULL,
			'method_class' => (isset($row['method_class']))? $row['method_class'] : NULL,
			'method_subclass' => (isset($row['method_subclass']))? $row['method_subclass'] : NULL,
			'method_description' => (isset($row['method_description']))? $row['method_description'] : NULL,
			'method_formula' => (isset($row['method_formula']))? $row['method_formula'] : NULL,
			'method_reference' => (isset($row['method_reference']))? $row['method_reference'] : NULL,
			'method_type' => (isset($row['method_type']))? $row['method_type'] : NULL,
			'content_type' => (isset($row['content_type']))? $row['content_type'] : NULL,
			'author' => (isset($row['author']))? $row['author'] : NULL
			));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}

	/*
     * Retourne l'ontology associé au methode code
     */
    public function get_ontology($method_code)
    {
        $this->db->select('*');
        $this->db->from($this->method_ontology_table);
        $this->db->join($this->ontology_table, 'method_ontology.ontology_id = ontology.ontology_id', 'left');
        $this->db->where('method_ontology.method_code',$method_code);
        $result = $this->db->get()->result_array();
        if ($result != null) {
            return $result;
        }else{
            return null;
        }
    }

    public function get_distinct_classes(){
        $this->db->distinct();
        $this->db->select('method_class');
        $this->db->where('method_class is NOT NULL', NULL, FALSE);        
        return $this->db->get($this->table)->result_array();
    }
    public function get_distinct_subclasses(){
        $this->db->distinct();
        $this->db->select('method_subclass');
        $this->db->where('method_subclass is NOT NULL', NULL, FALSE);        
        return $this->db->get($this->table)->result_array();
    }
	
}