<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partner_model extends MY_Model{

  public function __construct()
  {
    parent::__construct();
    $this->table = 'partner';
  }

  /**
   * Retourne vrai si le partner_code existe
   */
  public function exist($str)
  {
    if ($this->find(array('partner_code' => $str)) || $str == NULL) return TRUE;
    else                                                            return FALSE;
  }

}
