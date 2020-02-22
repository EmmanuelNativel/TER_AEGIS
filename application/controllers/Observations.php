<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Observations extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page. <a href=\"" . site_url('welcome/login') . "\">Connexion</a>", 401);
            return;
        }

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
        $this->load->model('Obs_unit_model');
    }

    /**
     * Affiche la liste des observations disponibles pour l'utilisateur
     */
    function index()
    {
    }

    /**
     * Lien de téléchargement du formulaire d'importation des observations
     */
    public function download_form()
    {
        $this->load->helper('download');
        $file_path = 'download_forms/Import_observation.xlsx';//chemin absolu de mon fichier dans serveur
        force_download($file_path, NULL);
    }

    /**
     * Formulaire d'importation des observations
     */
    public function import()
    {
        $page['title'] = "Importation des observations";
        $page['subtitle'] = "Formulaire d'importation des observations";

        $config['upload_path'] = UPLOAD_PATH;//Le chemin d'accès au répertoire où le téléchargement doit être placé.
        $config['allowed_types'] = 'xls|xlsx'; // Autres extensions de la librairie phpexcel xml|ods|slk|gnumeric|csv|htm|html
        $config['encrypt_name'] = TRUE;

		
		$this->load->model(array('Exp_unit_model','Sample_stage_model','Dataset_model'));

        $this->load->library('excel');
        $this->load->library('table');
        $this->load->library('upload', $config);

        $this->form_validation->set_rules('trial_code', 'Essai', 'trim|required|alpha_dash|max_length[50]|xss_clean'); 
		$this->form_validation->set_rules('dataset_id', 'Jeu de données', 'trim|required|is_natural_no_zero|xss_clean');
		
		$scripts = array('bootstrap-select-ajax-plugin');
		$data = array();

        // if not successful, set the error message
        if (!$this->form_validation->run() || !$this->upload->do_upload('obs_file')) {
            $data['error'] = $this->upload->display_errors('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
            $this->view('observation/import', $page['title'], $page['subtitle'], $data, $scripts);
        } else {
            try {
                $trial = $this->input->post('trial_code');
				$dataset = $this->input->post('dataset_id');

                $upload_data = $this->upload->data();//return mon fichier de téléchargement

                PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
                PHPExcel_Settings:: setZipClass(PHPExcel_Settings :: ZIPARCHIVE);
                $objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']);//full_path est le chemin absolu vers le serveur y compris le nom de mon fichier excel

                //utilisation de l'objet excel pour obtenir un objet de feuille active(activeSheet)
                $objWorksheet = $objPHPExcel->getActiveSheet();
				
				$objPHPExcelCopyForCountLineNumber = PHPExcel_IOFactory::load($upload_data['full_path']);//full_path est le chemin absolu vers le serveur y compris le nom de mon fichier excel
				$objPHPExcelCopyForCountLineNumber->getActiveSheet()->toArray(null, true, true, true);//charger les données Excel en PHP.
				$nbreDeLignesObs = $objPHPExcelCopyForCountLineNumber->setActiveSheetIndex(0)->getHighestRow();
				
				//definition d'un tableau contenant les colonnes de base du fichier d'import
                $waiting_main_header = array('unit_code','obs_date','stage','phytorank','nb_object'); 
				
				if ($nbreDeLignesObs > 1) {
					$header = array(); // init du tableau qui contiendra les variables que l on souhaite observer
					$overallLineErrors = array();//tab qui contiendra les num de ligne du fichier dimport ou un probleme a ete rencontre
					$overallMessageErrors = array();//tab contenant les messages d erreur genere lors de l import

					foreach ($objWorksheet->getRowIterator() as $row) { //parcours de chaque ligne de la feuille
						$cellIterator = $row->getCellIterator(); //recupere un iterateur de ttes les cellules de la ligne actuelle
						$cellIterator->setIterateOnlyExistingCells(FALSE); // Permet à l'itérateur de ne pas occulter et prendre en compte 
																			//les cellules vides

						foreach ($cellIterator as $cell) { //parcours de chaque cellule de la ligne
							$cell_column = $cell->getColumn();
							$cell_row = $cell->getRow();
							$data_value = xss_clean($cell->getFormattedValue()); //recupere la valeur sans formatage de cellule appliquee

							if ($cell_row == 1) { //  si en-tete du fichier
								// Vérification du header, si var observee presente dans la base
								$field = $this->format_field($data_value, $waiting_main_header);

								if ($field) { //si la colonne du fichier d'import appartient à la base
									$header[$cell_column] = $field; //ajout du nom du champ dans le tableau header indexe par les num de col dans la feuille excel
								} elseif ($data_value) {
									//throw new Exception("La variable de la colonne : <b>" . $cell_column . "</b><br />n'existe pas dans la base", 3);
									array_push($overallMessageErrors, "La variable de la colonne ".$cell_column.", n'existe pas dans la base");
								}
							} else {
								// Vérification des valeurs
								if (isset($header[$cell_column]) && $data_value) {  // Si la colonne de la donnée fait réference à un champ défini dans le header
									switch ($header[$cell_column]) { //test sur le nom du champ
										case 'unit_code': //si unit_code
											if (!$this->Exp_unit_model->find(array('trial_code' => $trial, 'unit_code' => $data_value))) {
												//throw new Exception("Cellule : <b>" . $cell_column . $cell_row . "</b><br />Unit_code INCONNU<b>" . $data_value . "</b>", 1);
												array_push($overallMessageErrors, "Ensemble unit_code/Trial_code, ligne ".$cell_row.", n'existe pas dans la base");
												array_push($overallLineErrors, $cell_row);
												$import_data[$cell_row][$header[$cell_column]] = NULL;
											}
											else $import_data[$cell_row][$header[$cell_column]] = $data_value;
											break;
										case 'stage': //si code_st
											if (!$this->Sample_stage_model->find(array('trial_code' => $trial, 'st_name' => $data_value))){ 
												//throw new Exception("Cellule : <b>" . $cell_column . $cell_row . "</b><br />Sample_Stage INCONNU<b>" . $data_value . "</b>", 1);
												array_push($overallMessageErrors, "Stage_name, ligne ".$cell_row.", n'existe pas dans la base");
												array_push($overallLineErrors, $cell_row);
												$import_data[$cell_row][$cell_column] = NULL;
											}
											else $import_data[$cell_row][$header[$cell_column]] = $data_value;
											break;
										case 'obs_date':
											
											if (strlen($data_value) == 10 ){
												if (strpos($data_value,"/") !== FALSE) 
												{
													$dateExploded = explode("/", $data_value);
													$year = $dateExploded[2];
													$month = $dateExploded[1];
													$day = $dateExploded[0];
												} else 
												{
													$dateExploded = explode("-", $data_value);
													$year = $dateExploded[0];
													$month = $dateExploded[1];
													$day = $dateExploded[2];
												}
												
												if (checkdate($month,$day,$year)){
													$date = $year."-".$month."-".$day;
													$import_data[$cell_row][$header[$cell_column]] = $date;
												}
												else {
													array_push($overallMessageErrors, "obs_date, ligne ".$cell_row." n'existe pas dans le calendrier");
													array_push($overallLineErrors, $cell_row);
													$import_data[$cell_row][$header[$cell_column]] = NULL;
												}
											
											}
											else {
												array_push($overallMessageErrors, "obs_date, ligne ".$cell_row." n'existe pas dans le calendrier");
												array_push($overallLineErrors, $cell_row);
												$import_data[$cell_row][$header[$cell_column]] = NULL;
											}
											
											break;
										default:
											$import_data[$cell_row][$header[$cell_column]] = $data_value;
											break;
									}
									// matrice de donnees a importer indexe par numligne et nom de colonne
									//$import_data[$cell_row][$header[$cell_column]] = $data_value;
								} elseif (isset($header[$cell_column])) {
									switch ($header[$cell_column]) { //test sur le nom du champ
										case 'unit_code': //si unit_code
											//throw new Exception("Cellule : <b>" . $cell_column . $cell_row . "</b><br />Unit_code absent", 1);
											array_push($overallMessageErrors, "Ensemble unit_code/Trial_code, ligne ".$cell_row.", absent");
											array_push($overallLineErrors, $cell_row);
											break;
										case 'stage': //si code_st
											//throw new Exception("Cellule : <b>" . $cell_column . $cell_row . "</b><br />Sample_Stage absent", 1);
											array_push($overallMessageErrors, "Stage_name, ligne ".$cell_row.", absent");
											array_push($overallLineErrors, $cell_row);
											break;
									}
									$import_data[$cell_row][$header[$cell_column]] = NULL;
								}
							}
						}
					}
					
					//$import_result = $this->Obs_unit_model->import($import_data, $trial, $dataset, $waiting_main_header);
					
					$data['lignesErrors'] = $overallLineErrors;
					$data['messageErrors'] = $overallMessageErrors;
					
					if ( count($overallLineErrors)>0 ) {
						unlink($upload_data['full_path']); //remove file
						$this->view('observation/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
					else {	
						$import_result_obs_unit = $this->Obs_unit_model->import($import_data, $trial, $waiting_main_header); // Import des données
						
						$import_result_dataset_obs = $this->Dataset_model->import_dataset_obs($import_data, $trial, $dataset, $waiting_main_header);
						unlink($upload_data['full_path']); //remove file
						
						$this->view('observation/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
					
					
					//if ($import_result_obs_unit && $import_result_dataset_obs) {
						//$this->view('observation/import_success', $page['title'], $page['subtitle']);
					//} else {
					//    throw new Exception('Echec de l\'importation', 2);
					//}
				} else {
					unlink($upload_data['full_path']); //remove file
					$this->view('observation/import', $page['title'], $page['subtitle'], $data, $scripts);
				}
				
            } catch (Exception $e) {
                unlink($upload_data['full_path']); // remove file
				show_error($e->getmessage());
            }
        }
    }


    /**
     * Retourne le nom du champ (formaté), si une chaine de caractères est un nom de champ valide
     * pour le formulaire d'importation des données d'observation. Sinon retourne FALSE
     */
    public function format_field($field, $waiting_main_header)
    {
        $this->load->model('Variable_model');

        if (in_array(strtolower($field), $waiting_main_header)) {
            return strtolower($field);
        } elseif ($this->Variable_model->find(array('variable_code' => $field))) {
            return $field;
        } else {
            return FALSE;
        }
    }

    public function create()
    {

        //$this->load->model(array('Dataset_model', 'DatasetUser_model','users_model', 'Exp_unit_model', 'DatatsetType_model', 'Sample_stage_model'));
		$this->load->model(array('Dataset_model',  'Exp_unit_model', 'Sample_stage_model'));

        // Titre de la page
        $page['title'] = 'Création de nouvelle observation';
        $page['subtitle'] = 'Formulaire de saisie de création d\'une observation';

        // Récupération des données du formulaire
       // $data['obs_unit_code'] = $this->input->post('obs_unit_code');
        $data['unit_id'] = $this->input->post('unit_code');
        $data['obs_variable'] = $this->input->post('variable_code');
        $data['code_st'] = $this->input->post('st_name');
        $data['dataset_id'] = $this->input->post('dataset_name');
        $data['phytorank'] = $this->input->post('phytorank');
        $data['obs_date'] = $this->input->post('obs_date');
        $data['obs_value'] = $this->input->post('obs_value');
        $data['obs_nb_objects'] = $this->input->post('obs_nb_objects');
		$data['trial_code'] = $this->input->post('trial_code');


        // Règles de validation du formulaire
       // $this->form_validation->set_rules('obs_unit_code', 'Code observation', 'required|trim|min_length[1]|max_length[255]|alpha_dash|is_unique[obs_unit.obs_unit_code]|xss_clean');
        $this->form_validation->set_rules('unit_code', 'Unité expérimentale', 'required|alpha_dash|trim|max_length[25]|xss_clean');
        $this->form_validation->set_rules('dataset_name', 'Dataset', 'trim|required|alpha_dash|max_length[80]|xss_clean');
        $this->form_validation->set_rules('st_name', 'Nom du stade', 'trim|required|alpha_dash|max_length[10]|xss_clean');
        $this->form_validation->set_rules('obs_date', 'Date de l\'observation', 'trim|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');
        $this->form_validation->set_rules('phytorank', 'Rang de phytomer', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('variable_code', 'variable observée', 'trim|required|max_length[50]|xss_clean');
        $this->form_validation->set_rules('obs_nb_objects', 'Nombre d\'objets observés', 'trim|numeric|xss_clean');
		$this->form_validation->set_rules('trial_code', 'Essai', 'trim|required|alpha_dash|max_length[50]|xss_clean');
		$this->form_validation->set_rules('obs_value', 'Valeur Observation', 'trim|numeric|xss_clean');


        // Test de validation du formulaire
        if ($this->form_validation->run()) {
            $this->load->helper('postgre');
            $data = nullify_array($data); // Remplace les chaines de carctères vide par la valeur NULL
			if ($this->Obs_unit_model->exist_date_exp_unit_phyto_variable($data)) {
				$data['msg'] = "Une observation avec la même unité expérimentale, phytomer, date et variable observée existe déjà";
				$this->view('error', $page['title'], $page['subtitle'], $data);
			}
			else {
				if (!$this->Exp_unit_model->find(array('trial_code' => $data['trial_code'], 'exp_unit_id' => $data['unit_id']))) {
					$data['msg'] = "exp_unit_id et trial_code ne sont pas associés";
					$this->view('error', $page['title'], $page['subtitle'], $data);
				}
				else {
					if (!$this->Sample_stage_model->find(array('trial_code' => $data['trial_code'], 'code_st' => $data['code_st']))) {
						$data['msg'] = "trial_code et stade_échantillon ne sont pas associés";
						$this->view('error', $page['title'], $page['subtitle'], $data);
					}
					else {
						
						if ($this->Obs_unit_model->create(
							array(
							   // 'obs_unit_code' => $data['obs_unit_code'],
								'unit_id' => $data['unit_id'],
								'obs_variable' => $data['obs_variable'],
								'code_st' => $data['code_st'],
								'obs_value' => $data['obs_value'],
								'phytorank' => $data['phytorank'],
								'obs_date' => $data['obs_date'],
								'obs_nb_objects' => $data['obs_nb_objects']
							))) {
								if ($this->Dataset_model->create_dataset_obs($data)) {
									$data['msg'] = "L'Observation a été créée avec succés!";
									$this->view('success', $page['title'], $page['subtitle'], $data);
								}
								else {
									$data['msg'] = "Erreur lors de la création des données 'Jeu de données - Observations'";
									$this->view('error', $page['title'], $page['subtitle'], $data);
								}
							}
							else {
								$data['msg'] = "Erreur lors de la création des données d'Observations";
								$this->view('error', $page['title'], $page['subtitle'], $data);
							}
					}
				}
			}
        } else {
            // Affichage du formulaire de création d'observations et retour des erreurs
            $scripts = array('bootstrap-datepicker.min', 'bootstrap-datepicker.fr.min', 'bootstrap-select-ajax-plugin');
            $this->view('observation/new_observation', $page['title'], $page['subtitle'],$data,  $scripts );
        }
    }
}
