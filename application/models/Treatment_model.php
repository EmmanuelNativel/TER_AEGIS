<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Treatment_model extends MY_Model{

  public function __construct()
  {
    parent::__construct();
    $this->table = 'factor';
  }
  /**
	* Créé un nouvel enregistrement avec les valeurs passées en paramètre
	**/
	public function create($values) {
		if ($this->db->set($values)->insert($this->table)) return TRUE;
		return null;
	}
	
}
