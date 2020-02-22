<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_model extends MY_Model{

  public function __construct()
  {
    parent::__construct();
    $this->table = 'site';
  }

    public function create($values)
    {
        if ($this->db->set($values)->insert($this->table)) return TRUE;
        return null;
    }

  /**
   * Retourne vrai si le code site existe
   */
  public function valid_site($str)
  {
    if ($this->find(array('site_code' => $str)) || $str == NULL)  return TRUE;
    else                                          return FALSE;
  }

}
