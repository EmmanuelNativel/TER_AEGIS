<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {
	protected $table = '';


	/**
	* Créé un nouvel enregistrement avec les valeurs passées en paramètre et retourne son id
	**/
	public function create($values) {
		if ($this->db->set($values)->insert($this->table)) return $this->db->insert_id();
		return null;
	}

	/**
	* Recherche les enregistrements correspondants aux condiditions passées en paramètres
	**/
	public function find($where = array(), $limit = null, $offset = null, $order_field = null, $order_direction = 'ASC') {
		$req = $this->db->order_by($order_field, $order_direction)->get_where($this->table, $where, $limit, $offset);
		return $req->result_array();
	}

	/**
	* Met à jour les enregistrements en fonction des condiditions passées en paramètres
	**/
	public function update($where, $value) {
		return $this->db->where($where)->update($this->table, $value);
	}

	/**
	* Supprime les enregistrements en fonction des condiditions passées en paramètres
	**/
	public function delete($where) {
		return $this->db->where($where)->delete($this->table);
	}

	/**
	* Retourne le nombre d'entrées de la table
	*/
	public function count($where=array(), $field = '*', $dist = FALSE)
	{
		return $this->db->select($this->table.'.'.$field)
		->from($this->table)
		->where($where)
		->distinct($dist)
		->count_all_results();
	}

	/**
	* Retourne la liste des champs de la table
	*/
	public function fields()
	{
		return $this->db->list_fields($this->table);
	}

	/**
	* Retourne les enregistrements contenant le terme recherché dans un champ de la table
	* Si le champ n'est pas spécifié on recherche le terme dans le premier champ de la table
	*/
	public function like($searched_term, $field=NULL, $limit=1000)
	{
		if (!$field)	$field = $this->fields()[0]; // Si le champ n'est pas spécifié on recherche le terme dans le premier champ de la table
		return $this->db->like('LOWER('.$field.')', strtolower($searched_term))
		->limit($limit)
		->from($this->table)
		->get()
		->result_array();
	}


}
