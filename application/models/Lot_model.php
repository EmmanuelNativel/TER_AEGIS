<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lot_model extends MY_Model 
{

	protected $seedlot_unit_table = 'lot_unit';
	protected $accession_table = 'accession';
	protected $exp_unit_table = 'exp_unit';
	
	public function __construct()
	{
		parent::__construct();
		$this->table = 'lot';
	}

	public function create($values)
	{
		if ($this->db->set($values)->insert($this->table)) return TRUE;
		return null;
	}

		/**
		 * Retourne vrai si le code lot existe
		 */
	public function exist($str)
	{
		if ($this->find(array('lot_id' => $str)) || $str == NULL) return TRUE;
		else                                                            return FALSE;
	}
	
	public function addLot($feuilleLot, $nbreDeLignesLot)
	{
		$lineErrors=array();
        $lineSucess =array();
		$messageErrors = array();
		

        if(!empty($feuilleLot)){
            for($i=1; $i<$nbreDeLignesLot; $i++){
				$lot_code_ok = FALSE;
				$accession_code_ok = FALSE;
				
                $ligne = array_values($feuilleLot[$i]);
				
				$accession_code = $ligne[0];
				$lot_partner_code = $ligne[1];
				$lot_code = $ligne[2];
				$lot_description = $ligne[3];
				
				//lot_code unique constraint
				if (strlen($lot_code)>0){
					$this->db->select('lot_code');
					$this->db->where('lot_code', $lot_code);
					$q = $this->db->get($this->table);
					$lot= $q->result_array();

					if(count($lot) >0){
						array_push($messageErrors, "Lot, ligne ".$i." onglet Seedlot, déjà présent dans la base");
						array_push($lineErrors, $i+1);
					}
					else {
						$lot_code_ok = TRUE;
					}
				}
				else {
					$lot = array();
					array_push($messageErrors, "Lot, ligne ".$i." onglet Seedlot, absent");
				}
				//test existence accession
				if (strlen($accession_code)>0){
					$this->db->select('accession_id');
					$this->db->where('accession_code', $accession_code);
					$q = $this->db->get($this->accession_table);
					$accession= $q->result_array();

					if(count($accession) >0){
						$accession_code_ok = TRUE;
						$accession_id = $accession[0]['accession_id'];
					}
					else {
						array_push($messageErrors, "Accession, ligne ".$i." onglet Seedlot, non présente dans la base");
						array_push($lineErrors, $i+1);
						$accession_id = NULL;
					}
				}
				else {
					$accession = array();
					$accession_id = NULL;
					array_push($messageErrors, "Accession, ligne ".$i." onglet Seedlot, absent");
				}
				
				if ($accession_code_ok && $lot_code_ok) {
					$data = array(
						'lot_code' => $lot_code,
						'lot_code_partner' => $lot_partner_code,
						'accession_id' => $accession_id,
						'lot_description' => $lot_description
                        );

						$this->db->insert($this->table, $data);
						array_push($lineSucess, $i+1);
                }
                else{
					array_push($lineErrors, $i+1);
                }
			}

        }
        else{
			array_push($messageErrors, "Abscence de données dans la feuille SeedLot du fichier d'importation");
        }

        $tabResultSuccessAndErrorsLignes[0]= $lineSucess;
        $tabResultSuccessAndErrorsLignes[1]= $lineErrors;
		$tabResultSuccessAndErrorsLignes[2] = $messageErrors;

        return $tabResultSuccessAndErrorsLignes;
	}
	
	public function addLot_unit($feuilleLot_unit, $nbreDeLignesLot_unit){
		$lineErrors=array();
        $lineSucess =array();
		$messageErrors = array();
		

        if(!empty($feuilleLot_unit)){
            for($i=1; $i<$nbreDeLignesLot_unit; $i++){
				$lot_code_ok = FALSE;
				$unit_code_ok = FALSE;
				$format_start_date_ok = FALSE;
				$format_end_date_ok = FALSE;
				
                $ligne = array_values($feuilleLot_unit[$i]);
				
				$unit_code = $ligne[0];
				$lot_code = $ligne[1];
				$lot_unit_start_date = $ligne[2];
				$lot_unit_end_date = $ligne[3];
				
				//lot_code test existence
				if (strlen($lot_code)>0){
					$this->db->select('lot_id');
					$this->db->where('lot_code', $lot_code);
					$q = $this->db->get($this->table);
					$lot= $q->result_array();

					if(count($lot) >0){
						$lot_code_ok = TRUE;
						$lot_id = $lot[0]['lot_id'];
					}
					else {
						array_push($messageErrors, "Lot, ligne ".$i." onglet Seedlot_Unit, non présent dans la base");
						array_push($lineErrors, $i+1);
						$lot_id = NULL;
					}
				}
				else {
					$lot_id = NULL;
					array_push($messageErrors, "Lot, ligne ".$i." onglet Seedlot_Unit, absent");
				}
				//test existence unit_code
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
						array_push($messageErrors, "Exp_unit, ligne ".$i." onglet Seedlot_Unit, non présente dans la base");
						array_push($lineErrors, $i+1);
						$exp_unit_id = NULL;
					}
				}
				else {
					$exp_unit_id = NULL;
					array_push($messageErrors, "Exp_unit, ligne ".$i." onglet Seedlot_Unit, absent");
				}
				
				if( !(empty($lot_unit_start_date)) && strlen($lot_unit_start_date) == 10 ){
					
						if (strpos($lot_unit_start_date,"/") !== FALSE) 
						{
							$dateExploded = explode("/", $lot_unit_start_date);
							$year = $dateExploded[2];
							$month = $dateExploded[1];
							$day = $dateExploded[0];
						} else 
						{
							$dateExploded = explode("-", $lot_unit_start_date);
							$year = $dateExploded[0];
							$month = $dateExploded[1];
							$day = $dateExploded[2];
						}
						
						
						if (checkdate($month,$day,$year)){
							$lot_unit_start_date = $year."-".$month."-".$day;
							$format_start_date_ok = TRUE;
						}
						else {
							array_push($messageErrors, "Lot_unit_start_date, ligne ".$i." onglet Seedlot_Unit, n'existe pas dans le calendrier");
						}
                    }
                    else{
						array_push($messageErrors, "Lot_unit_start_date, ligne ".$i." onglet Seedlot_Unit, absente");
                    }
				
				if( !(empty($lot_unit_end_date)) && strlen($lot_unit_end_date) == 10 ){
					
					
						if (strpos($lot_unit_end_date,"/") !== FALSE) 
						{
							$dateExploded = explode("/", $lot_unit_end_date);
							$year = $dateExploded[2];
							$month = $dateExploded[1];
							$day = $dateExploded[0];
						} else 
						{
							$dateExploded = explode("-", $lot_unit_end_date);
							$year = $dateExploded[0];
							$month = $dateExploded[1];
							$day = $dateExploded[2];
						}
						
						
						if (checkdate($month,$day,$year)){
							$lot_unit_end_date = $year."-".$month."-".$day;
							$format_end_date_ok = TRUE;
						}
						else {
							array_push($messageErrors, "Lot_unit_end_date, ligne ".$i." onglet Seedlot_Unit, n'existe pas dans le calendrier");
						}
                    }
                    else{
						array_push($messageErrors, "Lot_unit_end_date, ligne ".$i." onglet Seedlot_Unit, absente");
                    }
				
				
				if ($unit_code_ok && $lot_code_ok && $format_start_date_ok && $format_end_date_ok) {
					$data = array(
						'exp_unit_id' => $exp_unit_id,
						'lot_id' => $lot_id,
						'sowing_date' => $lot_unit_start_date,
						'ending_date' => $lot_unit_end_date
                        );

						$this->db->insert($this->seedlot_unit_table, $data);
						array_push($lineSucess, $i+1);
                }
                else{
					array_push($lineErrors, $i+1);
                }
			}

        }
        else{
			array_push($messageErrors, "Abscence de données dans la feuille SeedLot_unit du fichier d'importation");
        }

        $tabResultSuccessAndErrorsLignes[0]= $lineSucess;
        $tabResultSuccessAndErrorsLignes[1]= $lineErrors;
		$tabResultSuccessAndErrorsLignes[2] = $messageErrors;

        return $tabResultSuccessAndErrorsLignes;
	}
}
