<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operation_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
		$this->table = 'operation';
	}

	/**
     * Créé un nouveau type d'opération 'operation' avec les valeurs passées en paramètre
     * Retourne l'id de l'operation créé
     **/
    public function create_operation_type($values)
    {
        $this->db->set($values)->insert($this->table);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    /**
     * Modifie type d'opération 'operation' avec les valeurs passées en paramètre dont l'id est passé en paramètre
     **/
    public function update_operation_type($values, $id)
    {
        if ($this->db->update($this->table, $values, array('id' => $id))) return TRUE;
    	return null;
    }
    /**
     * Supprime type d'opération 'operation' dont l'id est passé en paramètre
     **/
    public function delete_operation_type($id)
    {
        if($this->db->delete($this->table, array('id' => $id))) return TRUE;
    	return null;
    }



	/**
     * Créé un nouvelle opération 'sample_operation' avec les valeurs passées en paramètre
     * Retourne l'id du sample_operation créé
     **/
    public function create_sample_operation($values)
    {
        $this->db->set($values)->insert("sample_operation");
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /**
     * Créé un nouvel 'sample_operation_input' avec les valeurs passées en paramètre
     **/
    public function create_sample_operation_input($values)
    {
        if ($this->db->set($values)->insert("sample_operation_input")) return TRUE;
        return null;
    }
    /**
     * Créé un nouvel 'sample_operation_output' avec les valeurs passées en paramètre
     **/
    public function create_sample_operation_output($values)
    {
        if ($this->db->set($values)->insert("sample_operation_output")) return TRUE;
        return null;
    }

	/**
	* Récupère tous les types d'opération
	*/
	public function get_all_operation(){
	    return $this->db->get($this->table)
	                ->result_array();
	}
}