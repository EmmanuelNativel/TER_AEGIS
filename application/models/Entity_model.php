<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entity_model extends MY_Model
{
    protected $entity_ontology_table = 'entity_ontology';
    protected $ontology_table = 'ontology';
	
    public function __construct()
    {
        parent::__construct();
        $this->table = 'entity';
    }

    /**
     * Retourne vrai si le code entity existe
     */
    public function exist($str)
    {
        if ($this->find(array('entity_code' => $str)) || $str == NULL)  return TRUE;
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
	* Importe les données d entity depuis un tableau de données bidimensionnel
	*/
	public function import($data)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
			'entity_code' => $row['entity_code'],
			'entity_name' => (isset($row['entity_name']))? $row['entity_name'] : NULL,
			'entity_definition' => (isset($row['entity_definition']))? $row['entity_definition'] : NULL));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}

	/**
	* Récupère toutes les données de la table entity
	*/
	public function get_all_entity_data(){
        return $this->db->select()
                    ->from($this->table)
                    ->get()
                    ->result_array();
    }
	

    /*
     * Retourne l'ontology associé a l'entity
     */
    public function get_ontology($entity_code)
    {
        $this->db->select('*');
        $this->db->from($this->entity_ontology_table);
        $this->db->join($this->ontology_table, 'entity_ontology.ontology_id = ontology.ontology_id', 'left');
        $this->db->where('entity_ontology.entity_code',$entity_code);
        $result = $this->db->get()->result_array();
        if ($result != null) {
            return $result;
        }else{
            return null;
        }
    }

    public function search_like_code_name($term){
        $this->db->like('entity_name', $term);
        $this->db->or_like('entity_code', $term);
        return $this->db->get($this->table)->result_array();
    }
}