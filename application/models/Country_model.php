<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Country_model extends MY_Model{

  public function __construct()
  {
    parent::__construct();
    $this->table = 'country';
  }
  /**
   * Retourne la liste des pays codés sur 2 caractères
   */
  public function get_alpha2()
  {
    return $this->db->select('country_code')
                    ->get($this->table)
                    ->result_array();
  }

}
