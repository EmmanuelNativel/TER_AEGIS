<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soil_model extends MY_Model{

  public function __construct()
  {
    parent::__construct();
    $this->table = 'soil_profile';
  }

  /**
   * Retourne vrai si le code sol existe
   */
  public function valid_soil($str)
  {
    if ($this->find(array('profile_code' => $str)) || $str == NULL)  return TRUE;
    else                                          return FALSE;
  }

}
