<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectPartner_model extends MY_Model
{

    protected $partner_table = 'partner';

    public function __construct()
    {
        parent::__construct();
        $this->table = 'project_partner';
    }

    /**
     * Créé un nouvel enregistrement
     **/
    public function create($values)
    {
        if ($this->db->set($values)->insert($this->table)) return TRUE;
        return null;
    }


  public function get_project_partners($project_code, $limit=null, $offset=null)
  {
    $query = $this->db->from($this->partner_table)
                    ->join($this->table, $this->table.'.partner_code = '.$this->partner_table.'.partner_code')
                    ->where('project_code', $project_code);

    if ($limit) $query->limit($limit, $offset);

    return  $query->get()
                  ->result_array();
  }

  /**
   * Retourne la liste des organismes éxterieurs au projet
   */
  public function like_not_project_partners($searched_term, $project_code)
  {
    return $this->db->select('partner_code, partner_name')
                    ->from($this->partner_table)
                    ->group_start()
                      ->like('LOWER(partner_name)', strtolower($searched_term))
                      ->or_like('LOWER(city)', strtolower($searched_term))
                    ->group_end()
                    ->where('partner_code NOT IN (SELECT partner_code FROM '.$this->table.' WHERE project_code = \''.$project_code.'\')')
                    ->get()
                    ->result_array();
  }

}
