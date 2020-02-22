<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exp_unit extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page. <a href=\"" . site_url('welcome/login') . "\">Connexion</a>", 401);
            return;
        }

        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->helper('date');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>', '</div>');
        $this->load->model('Exp_unit_model');
    }

    /**
     * Lien de téléchargement du formulaire d'importation de dispositif
     */

    public function download_form()
    {
        $this->load->helper('download');
        $file_path = 'download_forms/Import_design.xlsx';
        force_download($file_path, NULL);
    }

    /**
     * Appel de la vue import
     */
    public function mains_page(){
        $page['title'] = "Importation de dispositif";
        $page['subtitle'] = "Formulaire d'importation des dispositifs";
        $scripts = array('bootstrap-select-ajax-plugin');
        $this->view('exp_unit/import', $page['title'], $page['subtitle'], $scripts);
    }



    public function import()
    {
		$page['title'] = "Importation des dispositifs";
        $page['subtitle'] = "Formulaire d'importation des dispositifs";
		
        $config['upload_path'] = UPLOAD_PATH;//Le chemin d'accès au répertoire où le téléchargement doit être placé.
        $config['allowed_types'] = 'xls|xlsx'; // Autres extensions de la librairie phpexcel xml|ods|slk|gnumeric|csv|htm|html
        $config['encrypt_name'] = TRUE;


        $this->load->library('excel');
        $this->load->library('table');
        $this->load->library('upload', $config);


        // if not successful, set the error message
        if (!$this->upload->do_upload('exp_unit_file')) {
            $data['error'] = $this->upload->display_errors('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
            $scripts = array('bootstrap-select-ajax-plugin');
           $this->view('exp_unit/import', $page['title'], $page['subtitle'], $data, $scripts);
        } else {
            try {
				$data = array();
                $upload_data = $this->upload->data();
				
                PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
                $objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']);// détecte automatiquement le lecteur correct à charger pour ce type de fichier

                $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);//charger les données Excel en PHP.
				
				//setActiveSheetIndex = fixe l index de feuille active a 0 ainsi le classeur sera ouvert sur la premiere feuille
                $nbreDeLignesExp_unit = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
				$nbreDeLignesSeedlot = $objPHPExcel->setActiveSheetIndex(1)->getHighestRow();
				$nbreDeLignesSeedlot_unit = $objPHPExcel->setActiveSheetIndex(2)->getHighestRow();
				$nbreDeLignesFactor = $objPHPExcel->setActiveSheetIndex(3)->getHighestRow();
				$nbreDeLignesFactor_level = $objPHPExcel->setActiveSheetIndex(4)->getHighestRow();
				$nbreDeLignesFactor_trial = $objPHPExcel->setActiveSheetIndex(5)->getHighestRow();
				$nbreDeLignesFactor_unit = $objPHPExcel->setActiveSheetIndex(6)->getHighestRow();
				
				if ($nbreDeLignesExp_unit > 1) {
					$worksheetNames = $objPHPExcel->getSheetNames($upload_data['full_path']);
					$return = array();

					foreach ($worksheetNames as $key => $sheetName) {
						//set the current active worksheet by name
						$objPHPExcel->setActiveSheetIndexByName($sheetName);
						// crée un tableau assoc avec le nom de la feuille en tant que clé et le tableau des contenus de la feuille comme valeur
						$return[$sheetName] = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
					}
					
					$feuilleDesign = $return['Design'];
					$feuilleDesign = array_values($feuilleDesign); //transforme les indexs correspondant au lettre des colonnes en nombre
					
					$resultInsertionDesign = $this->Exp_unit_model->addExp_unit($feuilleDesign, $nbreDeLignesExp_unit );
					
					if ($nbreDeLignesSeedlot > 1) {
						$feuilleSeedlot = $return['Seedlot'];
						$feuilleSeedlot = array_values($feuilleSeedlot );
						
						$this->load->model('Lot_model');
						
						$resultInsertionSeedlot = $this->Lot_model->addLot($feuilleSeedlot ,$nbreDeLignesSeedlot );
					}
					if ($nbreDeLignesFactor > 1) {
						$feuilleFactor = $return['Treatment'];
						$feuilleFactor = array_values($feuilleFactor );
						
						$this->load->model('Factor_model');
						
						$resultInsertionFactor = $this->Factor_model->addFactor($feuilleFactor ,$nbreDeLignesFactor );
					}
					if ($nbreDeLignesFactor_level > 1) {
						$feuilleFactor_level = $return['Treatment_Level'];
						$feuilleFactor_level = array_values($feuilleFactor_level );
						
						$resultInsertionFactor_Level = $this->Factor_model->addFactor_level($feuilleFactor_level ,$nbreDeLignesFactor_level );
					}
					if ($nbreDeLignesFactor_trial > 1) {
						$feuilleFactor_trial = $return['Treatment_Trial'];
						$feuilleFactor_trial = array_values($feuilleFactor_trial );
						
						$resultInsertionFactor_trial = $this->Factor_model->addFactor_trial($feuilleFactor_trial ,$nbreDeLignesFactor_trial );
					}
					if ($nbreDeLignesSeedlot_unit > 1) {
						$this->load->model('Lot_model');
						
						$feuilleSeedlot_unit = $return['Seedlot_Unit'];
						$feuilleSeedlot_unit = array_values($feuilleSeedlot_unit );
						
						$resultInsertionSeedlot_unit = $this->Lot_model->addLot_unit($feuilleSeedlot_unit ,$nbreDeLignesSeedlot_unit );
					}
					if ($nbreDeLignesFactor_unit > 1) {
						$feuilleFactor_unit = $return['Treatment_Unit'];
						$feuilleFactor_unit = array_values($feuilleFactor_unit );
						
						$resultInsertionFactor_unit = $this->Factor_model->addFactor_unit($feuilleFactor_unit ,$nbreDeLignesFactor_unit );
					}
					$data = array();
					if (count($resultInsertionDesign[1]) > 0){ // if errors
						if ($nbreDeLignesSeedlot > 1) {
							if (count($resultInsertionSeedlot[1]) > 0){ // if errors
								$resultInsertionDesign[1] = array_merge($resultInsertionDesign[1],$resultInsertionSeedlot[1]);
							}
						}
						if ($nbreDeLignesFactor > 1) {
							if (count($resultInsertionFactor[1]) > 0){
								$resultInsertionDesign[1] = array_merge($resultInsertionDesign[1],$resultInsertionFactor[1]);
							}
						}
						if ($nbreDeLignesFactor > 1 ) {
							if (count($resultInsertionFactor_Level[1]) > 0){
								$resultInsertionDesign[1] = array_merge($resultInsertionDesign[1],$resultInsertionFactor_Level[1]);
							}
						}
						if ($nbreDeLignesFactor_trial > 1) {
							if (count($resultInsertionFactor_trial[1]) > 0){
								$resultInsertionDesign[1] = array_merge($resultInsertionDesign[1],$resultInsertionFactor_trial[1]);
							}
						}
						if ($nbreDeLignesSeedlot_unit > 1) {
							if (count($resultInsertionSeedlot_unit[1]) > 0){
								$resultInsertionDesign[1] = array_merge($resultInsertionDesign[1],$resultInsertionSeedlot_unit[1]);
							}
						}
						if ($nbreDeLignesFactor_unit > 1) {
							if (count($resultInsertionFactor_unit[1]) > 0){
								$resultInsertionDesign[1] = array_merge($resultInsertionDesign[1],$resultInsertionFactor_unit[1]);	
							}
						}
						
						$data['lignesErrors'] = $resultInsertionDesign[1];
					}
					if (count($resultInsertionDesign[2]) > 0){
						if ($nbreDeLignesSeedlot > 1) {
							if (count($resultInsertionSeedlot[2]) > 0){ // if errors
								$resultInsertionDesign[2] = array_merge($resultInsertionDesign[2],$resultInsertionSeedlot[2]);
							}
						}
						if ($nbreDeLignesFactor > 1) { 
							if (count($resultInsertionFactor[2]) > 0){
								$resultInsertionDesign[2] = array_merge($resultInsertionDesign[2],$resultInsertionFactor[2]);
							}
						}
						if ($nbreDeLignesFactor_level > 1) {
							if (count($resultInsertionFactor_Level[2]) > 0){
								$resultInsertionDesign[2] = array_merge($resultInsertionDesign[2],$resultInsertionFactor_Level[2]);
							}
						}
						if ($nbreDeLignesFactor_trial > 1) {
							if (count($resultInsertionFactor_trial[2]) > 0){
								$resultInsertionDesign[2] = array_merge($resultInsertionDesign[2],$resultInsertionFactor_trial[2]);
							}
						}
						if ($nbreDeLignesSeedlot_unit > 1) {
							if (count($resultInsertionSeedlot_unit[2]) > 0){
								$resultInsertionDesign[2] = array_merge($resultInsertionDesign[2],$resultInsertionSeedlot_unit[2]);
							}
						}
						if ($nbreDeLignesFactor_unit > 1) {
							if (count($resultInsertionFactor_unit[2]) > 0){
								$resultInsertionDesign[2] = array_merge($resultInsertionDesign[2],$resultInsertionFactor_unit[2]);	
							}
						}
						
						$data['messageErrors'] = $resultInsertionDesign[2];
					}
					$scripts = array('bootstrap-select-ajax-plugin');
					unlink($upload_data['full_path']); //remove file
					$this->view('exp_unit/import_success', $page['title'], $page['subtitle'], $data, $scripts);
				}
				else {
					$scripts = array('bootstrap-select-ajax-plugin');
					unlink($upload_data['full_path']); // remove file
					$this->view('exp_unit/import', $page['title'], $page['subtitle'], $data, $scripts);
				}
				
            } catch (Exception $e) {
                unlink($upload_data['full_path']); // remove file
                show_error($e->getmessage());
            }
        }
    }
	
	/**
     * Affiche les unites experimentales recherchées sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[25]|xss_clean');

        if( $this->form_validation->run() )
        {
            $searched_term = $this->input->post('term');
            $results = $this->Exp_unit_model->like($searched_term, 'unit_code');

            $unites = array_map(function($results) {
                return array(
                    'name' => $results['unit_code'],
                    'value' => $results['exp_unit_id']
                );
            }, $results);

            echo json_encode($unites);
        }
        else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }
}







