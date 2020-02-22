<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Factor_model extends MY_Model
{

	protected $treatment_level_table = 'factor_level';
	protected $treatment_trial_table = 'factor_trial';
	protected $treatment_unit_table = 'factor_unit';
	protected $trial_table = 'trial';
	protected $exp_unit_table = 'exp_unit';

	public function __construct()
	{
		parent::__construct();
		$this->table = 'factor';
	}
  /**
	* Créé un nouvel enregistrement avec les valeurs passées en paramètre
	**/
	public function create($values) {
		if ($this->db->set($values)->insert($this->table)) return TRUE;
		return null;
	}

		/**
		 * Retourne vrai si le factor_id existe
		 */
	public function exist($str)
	{
		if ($this->find(array('factor_id' => $str)) || $str == NULL) return TRUE;
		else														return FALSE;
	}
	
	public function addFactor($feuilleFactor, $nbreDeLignesFactor){
		$lineErrors=array();
        $lineSucess =array();
		$messageErrors = array();
		
		
        if(!empty($feuilleFactor)){
            for($i=1; $i<$nbreDeLignesFactor; $i++){
				$factor_ok = FALSE;
				
                $ligne = array_values($feuilleFactor[$i]);
				
				$factor = $ligne[0];
				
				//factor unique constraint
				if (strlen($factor)>0){
					$this->db->select('factor_id');
					$this->db->where('factor', $factor);
					$q = $this->db->get($this->table);
					$factor_id= $q->result_array();

					if(count($factor_id) >0){
						array_push($messageErrors, "Factor, ligne ".$i." onglet Treatment, déjà présent dans la base");
						array_push($lineErrors, $i+1);
					}
					else {
						$factor_ok = TRUE;
					}
				}
				else {
					$factor_id = array();
					array_push($messageErrors, "Factor, ligne ".$i." onglet Treatment, absent");
				}
				
				if ($factor_ok ) {
					$data = array('factor' => $factor);

					$this->db->insert($this->table, $data);
					array_push($lineSucess, $i+1);
                }
                else{
					array_push($lineErrors, $i+1);
                }
			}
        }
        else{
			array_push($messageErrors, "Abscence de données dans la feuille Treatment du fichier d'importation");
        }

        $tabResultSuccessAndErrorsLignes[0]= $lineSucess;
        $tabResultSuccessAndErrorsLignes[1]= $lineErrors;
		$tabResultSuccessAndErrorsLignes[2] = $messageErrors;

        return $tabResultSuccessAndErrorsLignes;
	}
	
	public function addFactor_level($feuilleFactorLevel, $nbreDeLignesFactor_Level) {
		$lineErrors=array();
        $lineSucess =array();
		$messageErrors = array();
		

        if(!empty($feuilleFactorLevel)){
            for($i=1; $i<$nbreDeLignesFactor_Level; $i++){
				$factor_ok = FALSE;
				$factor_level_ok = FALSE;
				
                $ligne = array_values($feuilleFactorLevel[$i]);
				
				$factor = $ligne[0];
				$factor_level = $ligne[1];
				
				//factor test existence
				if (strlen($factor)>0){
					$this->db->select('factor_id');
					$this->db->where('factor', $factor);
					$q = $this->db->get($this->table);
					$factor_id= $q->result_array();

					if(count($factor_id) >0){
						$factor_ok = TRUE;
						$factor_id = $factor_id[0]['factor_id'];
					}
					else {
						$factor_id = NULL;
						array_push($messageErrors, "Factor, ligne ".$i." onglet Treatment_Level, non présent dans la base");
						array_push($lineErrors, $i+1);
					}
				}
				else {
					$factor_id = NULL;
					array_push($messageErrors, "Factor, ligne ".$i." onglet Treatment_Level, absent");
				}
				
				if (strlen($factor_level)>0){
					$factor_level_ok = TRUE;
				}
				else {
					array_push($messageErrors, "Factor_level, ligne ".$i." onglet Treatment_Level, absent");
				}
				
				if ($factor_ok && $factor_level_ok) {
					$data = array('factor_id' => $factor_id,
								'factor_level' => $factor_level);

					$this->db->insert($this->treatment_level_table, $data);
					
					array_push($lineSucess, $i+1);
                }
                else{
					array_push($lineErrors, $i+1);
                }
			}
        }
        else{
			array_push($messageErrors, "Abscence de données dans la feuille Treatment_Level du fichier d'importation");
        }

        $tabResultSuccessAndErrorsLignes[0]= $lineSucess;
        $tabResultSuccessAndErrorsLignes[1]= $lineErrors;
		$tabResultSuccessAndErrorsLignes[2] = $messageErrors;

        return $tabResultSuccessAndErrorsLignes;
	}
	
	
	public function addFactor_trial($feuilleFactor_trial ,$nbreDeLignesFactor_trial ) {
		
		$lineErrors=array();
        $lineSucess =array();
		$messageErrors = array();
		

        if(!empty($feuilleFactor_trial)){
            for($i=1; $i<$nbreDeLignesFactor_trial; $i++){
				$factor_ok = FALSE;
				$factor_level_ok = FALSE;
				$trial_code_ok = FALSE;
				
                $ligne = array_values($feuilleFactor_trial[$i]);
				
				$trial_code = $ligne[0];
				$factor = $ligne[1];
				$factor_level = $ligne[2];
				$factor_description = $ligne[3];
				
				//factor test existence
				if (strlen($factor)>0){
					$this->db->select('factor_id');
					$this->db->where('factor', $factor);
					$q = $this->db->get($this->table);
					$factor_id= $q->result_array();

					if(count($factor_id) >0){
						$factor_ok = TRUE;
						$factor_id = $factor_id[0]['factor_id'];
					}
					else {
						array_push($messageErrors, "Factor, ligne ".$i." onglet Treatment_Trial, non présent dans la base");
						array_push($lineErrors, $i+1);
						$factor_id = NULL;
					}
				}
				else {
					$factor_id = NULL;
					array_push($messageErrors, "Factor, ligne ".$i." onglet Treatment_Trial, absent");
				}
				
				//factor_level test existence
				if (strlen($factor_level)>0){
					$this->db->select('factor_level_id');
					$this->db->where('factor_id', $factor_id);
					$this->db->where('factor_level', $factor_level);
					$q = $this->db->get($this->treatment_level_table);
					$factor_level_id= $q->result_array();

					if(count($factor_level_id) >0){
						$factor_level_ok = TRUE;
						$factor_level_id = $factor_level_id[0]['factor_level_id'];
					}
					else {
						array_push($messageErrors, "Factor level, ligne ".$i." onglet Treatment_Trial, non présent dans la base");
						$factor_level_id = NULL;
						array_push($lineErrors, $i+1);
					}
				}
				else {
					$factor_level_id = NULL;
					array_push($messageErrors, "Factor level, ligne ".$i." onglet Treatment_Trial, absent");
				}
				
				//trial_code test existence
				if (strlen($trial_code)>0){
					$this->db->select('trial_code');
					$this->db->where('trial_code', $trial_code);
					$q = $this->db->get($this->trial_table);
					$trial_code_recherche= $q->result_array();

					if(count($trial_code_recherche) >0){
						$trial_code_ok = TRUE;
					}
					else {
						array_push($messageErrors, "Trial, ligne ".$i." onglet Treatment_Trial, non présent dans la base");
						array_push($lineErrors, $i+1);
					}
				}
				else {
					$trial_code_recherche = array();
					array_push($messageErrors, "Trial, ligne ".$i." onglet Treatment_Trial, absent");
				}
				
				
				if ($factor_ok && $factor_level_ok && $trial_code_ok) {
					$data = array('trial_code' =>  $trial_code ,
								'description' => $factor_description,
								'factor_level_id' => $factor_level_id);

					$this->db->insert($this->treatment_trial_table, $data);
					
					array_push($lineSucess, $i+1);
					
                }
                else{
					array_push($lineErrors, $i+1);
                }
			}
        }
        else{
			array_push($messageErrors, "Abscence de données dans la feuille Treatment_Trial du fichier d'importation");
        }

        $tabResultSuccessAndErrorsLignes[0]= $lineSucess;
        $tabResultSuccessAndErrorsLignes[1]= $lineErrors;
		$tabResultSuccessAndErrorsLignes[2] = $messageErrors;

        return $tabResultSuccessAndErrorsLignes;
		
	}
	
	public function addFactor_unit($feuilleFactor_unit, $nbreDeLignesFactor_unit) {
		$lineErrors=array();
        $lineSucess =array();
		$messageErrors = array();
		

        if(!empty($feuilleFactor_unit)){
            for($i=1; $i<$nbreDeLignesFactor_unit; $i++){
				$factor_ok = FALSE;
				$factor_level_ok = FALSE;
				$unit_code_ok = FALSE;
				$format_from_date_ok = FALSE;
				$format_to_date_ok = FALSE;
				
                $ligne = array_values($feuilleFactor_unit[$i]);
				
				$unit_code = $ligne[0];
				$factor = $ligne[1];
				$factor_level = $ligne[2];
				$from_date = $ligne[3];
				$to_date = $ligne[4];
				
				//factor test existence
				if (strlen($factor)>0){
					$this->db->select('factor_id');
					$this->db->where('factor', $factor);
					$q = $this->db->get($this->table);
					$factor_id= $q->result_array();

					if(count($factor_id) >0){
						$factor_ok = TRUE;
						$factor_id = $factor_id[0]['factor_id'];
					}
					else {
						$factor_id = NULL;
						array_push($messageErrors, "Factor, ligne ".$i." onglet Treatment_unit, non présent dans la base");
						array_push($lineErrors, $i+1);
					}
				}
				else {
					$factor_id = NULL;
					array_push($messageErrors, "Factor, ligne ".$i." onglet Treatment_unit, absent");
				}
				
				//factor_level test existence
				if (strlen($factor_level)>0){
					$this->db->select('factor_level_id');
					$this->db->where('factor_id', $factor_id);
					$this->db->where('factor_level', $factor_level);
					$q = $this->db->get($this->treatment_level_table);
					$factor_level_id= $q->result_array();

					if(count($factor_level_id) >0){
						$factor_level_ok = TRUE;
						$factor_level_id = $factor_level_id[0]['factor_level_id'];
					}
					else {
						$factor_level_id = NULL;
						array_push($messageErrors, "Factor level, ligne ".$i." onglet Treatment_unit, non présent dans la base");
						array_push($lineErrors, $i+1);
					}
				}
				else {
					$factor_level_id = NULL;
					array_push($messageErrors, "Factor level, ligne ".$i." onglet Treatment_unit, absent");
				}
				
				//exp_unit test existence
				if (strlen($unit_code)>0){
					$this->db->select('exp_unit_id');
					$this->db->where('unit_code', $unit_code);
					$q = $this->db->get($this->exp_unit_table);
					$exp_unit= $q->result_array();

					if(count($exp_unit) >0){
						$unit_code_ok = TRUE;
						$exp_unit_id = $exp_unit[0]['exp_unit_id'];
					}
					else {
						array_push($messageErrors, "Exp_unit, ligne ".$i." onglet Treatment_unit, non présent dans la base");
						array_push($lineErrors, $i+1);
						$exp_unit_id = NULL;
					}
				}
				else {
					$exp_unit_id = NULL;
					array_push($messageErrors, "Exp_unit, ligne ".$i." onglet Treatment_unit, absent");
				}
				
				if( !(empty($from_date)) && strlen($from_date) == 10 ){
					
					if (strpos($from_date,"/") !== FALSE) 
					{
						$dateExploded = explode("/", $from_date);
						$year = $dateExploded[2];
						$month = $dateExploded[1];
						$day = $dateExploded[0];
					} else 
					{
						$dateExploded = explode("-", $from_date);
						$year = $dateExploded[0];
						$month = $dateExploded[1];
						$day = $dateExploded[2];
					}
					
					
					if (checkdate($month,$day,$year)){
						$from_date = $year."-".$month."-".$day;
						$format_from_date_ok = TRUE;
					}
					else {
						array_push($messageErrors, "Factor_unit_from_date, ligne ".$i." onglet Treatment_unit, n'existe pas dans le calendrier");
					}
				}
				else{
					array_push($messageErrors, "Factor_unit_from_date, ligne ".$i." onglet Treatment_unit, absente");
				}
				
				if( !(empty($to_date)) && strlen($to_date) == 10 ){
					
					if (strpos($to_date,"/") !== FALSE) 
					{
						$dateExploded = explode("/", $to_date);
						$year = $dateExploded[2];
						$month = $dateExploded[1];
						$day = $dateExploded[0];
					} else 
					{
						$dateExploded = explode("-", $to_date);
						$year = $dateExploded[0];
						$month = $dateExploded[1];
						$day = $dateExploded[2];
					}
					
					
					if (checkdate($month,$day,$year)){
						$to_date = $year."-".$month."-".$day;
						$format_to_date_ok = TRUE;
					}
					else {
						array_push($messageErrors, "Factor_unit_to_date, ligne ".$i." onglet Treatment_unit, n'existe pas dans le calendrier");
					}
				}
				else{
					array_push($messageErrors, "Factor_unit_to_date, ligne ".$i." onglet Treatment_unit, absente");
				}
				
				
				if ($unit_code_ok && $factor_level_ok && $factor_ok && $format_from_date_ok && $format_to_date_ok) {
					$data = array('exp_unit_id' => $exp_unit_id,
								'factor_level_id' => $factor_level_id,
								'from_date' => $from_date,
								'to_date' => $to_date);

					$this->db->insert($this->treatment_unit_table, $data);
					
					array_push($lineSucess, $i+1);
                }
                else{
					array_push($lineErrors, $i+1);
                }
			}
        }
        else{
			array_push($messageErrors, "Abscence de données dans la feuille Treatment_unit du fichier d'importation");
        }

        $tabResultSuccessAndErrorsLignes[0]= $lineSucess;
        $tabResultSuccessAndErrorsLignes[1]= $lineErrors;
		$tabResultSuccessAndErrorsLignes[2] = $messageErrors;

        return $tabResultSuccessAndErrorsLignes;
	}
}
