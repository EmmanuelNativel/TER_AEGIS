<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lab_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
		$this->table = 'lab';
	}

	/**
	* Récupère tous les lab
	*/
	public function get_all_lab(){
	    return $this->db->get($this->table)
	                ->result_array();
	}
}