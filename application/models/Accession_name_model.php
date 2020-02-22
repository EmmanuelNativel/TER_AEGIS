<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accession_name_model extends MY_Model{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'accession_name';
    }

    public function create($values)
    {
        if ($this->db->set($values)->insert($this->table)) return TRUE;
        return null;
    }



}