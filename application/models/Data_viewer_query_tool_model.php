<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_viewer_query_tool_model extends MY_Model{

    public function __construct()
    {
        parent::__construct();
		$this->table = 'query_tool';
    }
	
	 public function create($values)
    {
        if ($this->db->set($values)->insert($this->table)) return TRUE;
        return null;
    }
	
	public function get_all_query_tool(){
        return $this->db->select()
                    ->from($this->table)
                    ->get()
                    ->result_array();
    }
	
	public function get_all_query_tool_where_creator($creator){
        return $this->db->select()
                    ->from($this->table)
					->where('creator', $creator)
                    ->get()
                    ->result_array();
    }
	
	public function get_last_id(){
		return $this->db->select()
                    ->from($this->table)
					->order_by('query_tool_id', 'DESC')
					->limit(1)
					->get()
                    ->result_array();
	}
	
}