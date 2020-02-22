<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_viewer_query_user_model extends MY_Model{

    public function __construct()
    {
        parent::__construct();
		$this->table = 'query_user';
    }

	public function exist($str)
	{
	  if ($this->find(array('query_tool_id' => $str)) || $str == NULL) return TRUE;
	  else                                                            return FALSE;
	}
	
	 public function update($values, $where)
    {
		$this->db->where('query_tool_id', $where);
        if ($this->db->set($values)->update($this->table)) return TRUE;
		return null;
    }
	
	 public function create($values)
    {
        if ($this->db->set($values)->insert($this->table)) return TRUE;
        return null;
    }
	
	public function get_all_query_user(){
        return $this->db->select()
                    ->from($this->table)
                    ->get()
                    ->result_array();
    }
	
}