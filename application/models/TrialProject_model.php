<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: mokdad
 * Date: 08/03/2017
 * Time: 15:11
 */
class TrialProject_model extends MY_Model
{

    protected $trial_table = 'trial';

    public function __construct()
    {
        parent::__construct();
        $this->table = 'trial_project';
    }
    /**
     * Créé un nouvel enregistrement
     **/
    public function create($values)
    {
        if ($this->db->set($values)->insert($this->table)) return TRUE;
        return null;
    }

    public function get_project_trials($project_code, $limit=null, $offset=null)
    {
      $query = $this->db->from($this->trial_table)
                        ->join($this->table, $this->table.'.trial_code = '.$this->trial_table.'.trial_code')
                        ->where('project_code', $project_code);

      if ($limit) $query->limit($limit, $offset);

      return  $query->get()
                    ->result_array();
    }

    /**
     * Retourne la liste des essai éxterieurs au projet
     */
    public function like_not_project_trials($searched_term, $project_code)
    {
        return $this->db->select('trial_code')
            ->from($this->trial_table)
            ->group_start()
            ->like('LOWER(trial_code)', strtolower($searched_term))
            ->group_end()
            ->where('trial_code NOT IN (SELECT trial_code FROM '.$this->table.' WHERE project_code = \''.$project_code.'\')')
            ->get()
            ->result_array();
    }


}
