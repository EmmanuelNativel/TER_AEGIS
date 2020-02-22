<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Weather_station_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->table = 'ws';
	}
  
	/**
	* Retourne vrai si la station meteo existe
	*/
	public function exist($data)
    {
        if ($this->find(array('wscode' => $data))) return TRUE;
        else return FALSE;
    }
	
}
