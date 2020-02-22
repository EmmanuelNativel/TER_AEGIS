<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exp_unit_model extends MY_Model
{

	protected $trial_table = 'trial';

	public function __construct()
	{
		parent::__construct();
		$this->table = 'exp_unit';
	}

	/**
     * Créé un nouvel enregistrement avec les valeurs passées en paramètre
     **/
	public function create($values)
	{
		if ($this->db->set($values)->insert($this->table)) return TRUE;
		return NULL;
	}

	/**
	 * Retourne vrai si l' id unité exp. existe
	 */
	public function exist($str)
	{
		if ($this->find(array('exp_unit_id' => $str)) || $str == NULL) return TRUE;
		else return FALSE;
	}
	
	/**
	* Importe les données d exp_unit depuis un tableau de données bidimensionnel
	*/
	public function import($data) {
		
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
			'num_level' => $row['num_level'],
			'unit_code' => $row['unit_code'],
			'trial_code' => $row['trial_code'],
			'assigned_to' => $row['assigned_to'],
			'level_label' => $row['level_label'],
			'x_coord' => $row['x_coord'],
			'y_coord' => $row['y_coord'],
			'unit_lat' => $row['unit_lat'],
			'unit_long' => $row['unit_long'],
			'unit_alt' => $row['unit_alt'],
			'nb_plant' => $row['nb_plant'],
			'rowspace' => $row['rowspace'],
			'surface' => $row['surface'],
			'unit_depth' => $row['profondeur']
			));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
	
	public function addExp_unit($feuilleExp_unit, $nbreDeLignesExp_unit){
		
		$lineErrors=array();
        $lineSucess =array();
		$messageErrors = array();

        if(!empty($feuilleExp_unit)){
            for($i=1; $i<$nbreDeLignesExp_unit; $i++){
				$trial_code_ok = FALSE;
				$unit_code_trial_code_ok = FALSE;
				$assign_to_ok = FALSE;
				
                $ligne = array_values($feuilleExp_unit[$i]);
				
				$num_level = $ligne[0];
				$unit_code = $ligne[1];
				$trial_code = $ligne[2];
				$assigned_to = $ligne[3];
				$level_label = $ligne[4];
				$x_coord = $ligne[5];
				$y_coord = $ligne[6];
				$unit_lat = $ligne[7];
				$unit_long = $ligne[8];
				$unit_alt = $ligne[9];
				$nbplant = $ligne[10];
				$rowspace = $ligne[11];
				$surface = $ligne[12];
				$profondeur = $ligne[13];
				
				//unit_code / trial_code unique constraint
				if (strlen($unit_code)>0 && strlen($trial_code)>0){
					$this->db->select('trial_code, unit_code');
					$this->db->where('trial_code', $trial_code);
					$this->db->where('unit_code', $unit_code);
					$q = $this->db->get($this->table);
					$exp_unit= $q->result_array();

					if(count($exp_unit) >0){
						array_push($messageErrors, "Ensemble unit_code/Trial_code, ligne ".$i." onglet Design, déjà présent dans la base");
						array_push($lineErrors, $i+1);
					}
					else {
						$unit_code_trial_code_ok = TRUE;
					}
				}
				else {
					$exp_unit = array();
					array_push($messageErrors, "Ensemble unit_code/Trial_code, ligne ".$i." onglet Design, absent");
				}
				
				// test l'id de trial
				if(strlen($trial_code)>0){
					$this->db->select('trial_code');
					$this->db->where('trial_code', $trial_code);
					$q = $this->db->get($this->trial_table);
					$trial= $q->result_array();

					if(count($trial) >0){
						$trial_code_ok = TRUE;
					}
					else {
						array_push($messageErrors, "Trial_code, ligne ".$i." onglet Design, non présent dans la base");
						array_push($lineErrors, $i+1);
					}
				}
				else {
					$trial = array();
					array_push($messageErrors, "Trial_code, ligne ".$i." onglet Design, absent");
				}
				
				// test de l'existence de l'exp_unit assigned_to
				if(strlen($assigned_to)>0){
					$this->db->select('exp_unit_id');
					$this->db->where('unit_code', $assigned_to);
					$this->db->where('trial_code', $trial_code);
					$q = $this->db->get($this->table);
					$assigned_to_recherche= $q->result_array();

					if(count($assigned_to_recherche) >0){
						$assigned_to = $assigned_to_recherche[0]['exp_unit_id'];
						$assign_to_ok = TRUE;
						
					}
					else {
						array_push($messageErrors, "Exp_unit assigned_to, ligne ".$i." onglet Design non présent dans la base");
						array_push($lineErrors, $i+1);
						$assign_to_ok = FALSE;
						$assigned_to = NULL;
					}
				}
				else {
					$assign_to_ok = TRUE;
					$assigned_to = NULL;
					//array_push($messageErrors, "Exp_unit assigned_to, ligne ".$i." absent");
				}
				
				if ($unit_code_trial_code_ok && $trial_code_ok && $assign_to_ok) {
					$data = array(
                        'num_level' => $num_level,
						'unit_code' => $unit_code,
						'trial_code' => $trial_code,
						'assigned_to' => $assigned_to,
						'level_label' => $level_label,
						'x_coord' => $x_coord,
						'y_coord' => $y_coord,
						'unit_lat' => $unit_lat,
						'unit_long' => $unit_long,
						'unit_alt' => $unit_alt,
						'nb_plant' => $nbplant,
						'rowspace' => $rowspace,
						'surface' => $surface,
						'unit_depth' => $profondeur
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
			array_push($messageErrors, "Abscence de données dans la feuille Design du fichier d'importation");
        }

        $tabResultSuccessAndErrorsLignes[0]= $lineSucess;
        $tabResultSuccessAndErrorsLignes[1]= $lineErrors;
		$tabResultSuccessAndErrorsLignes[2] = $messageErrors;

        return $tabResultSuccessAndErrorsLignes;
	}

	/**
	* Récupère toutes les données de la table exp_unit
	*/
	public function get_all_exp_unit_data(){
        return $this->db->select()
                    ->from($this->table)
                    ->get()
                    ->result_array();
    }

    public function get_trial_exp_unit_coords($trial_code){

		return $this->db->select('unit_code, num_level, exp_unit_id, assigned_to, x_coord, y_coord')
				->from($this->table)
				->where('trial_code', $trial_code)
				->order_by('num_level', 'ASC')
				->order_by('exp_unit_id', 'ASC')
				->get()
	            ->result_array();
	}
	
}
