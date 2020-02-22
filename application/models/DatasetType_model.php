<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DatasetType_model extends MY_Model{

  public function __construct()
  {
    parent::__construct();
    $this->table = 'dataset_type';
  }

}
