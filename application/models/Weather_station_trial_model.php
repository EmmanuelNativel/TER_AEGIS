<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Weather_station_trial_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->table = 'ws_trial';
	}
  
	/**
	* Retourne vrai si la station meteo existe
	*/
	public function exist($station_code, $trial_code)
    {
        if ($this->find(array('wscode' => $station_code, 'trial_code' => $trial_code))) return TRUE;
        else return FALSE;
    }
	
}