<?php

class Database_model extends MY_Model
{
  protected $table;
    /**
     *  Retourne les lignes de tout les champs
     *  d'une table dans la base de données.
     *
     *  @param String $table Le nom de la table dans la base
     *  @return Array Le tableau des valeurs de la table
     */
    public function get_table($table, $limit=NULL){
		return $this->db->from($table)
						->limit($limit)
						->get()
						->result_array();
    }

	public function get_where($table, $where, $limit=NULL){
		return $this->db->from($table)
						->where($where)
						->limit($limit)
						->get()
						->result_array();
    }

  public function get_header($table)
  {
    return $this->db->query('select column_name from information_schema.columns where table_name=\''.$table.'\';')
							->result_array();
  }

  /**
   * Définit la table considerée par la classe
   */
  public function set_table($table_name)
  {
    $this->table = $table_name;
  }


}
