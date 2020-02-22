<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Taxo_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'taxo';
    }

    /**
     * Importe les données d'observations depuis un tableau de données bidimensionnel
     */
    function Add_Taxo($data_taxo)
    {
        $this->db->trans_start();
        $this->db->insert('taxo', $data_taxo);
        $this->db->insert_id();
        $this->db->trans_complete();
        return $this->db->trans_status();

    }
}
