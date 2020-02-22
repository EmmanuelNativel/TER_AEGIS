<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analysis extends MY_Controller {
	
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
        $this->load->model('Analysis_model');
    }

    /**
     * Affiche la liste des analyses disponibles pour l'utilisateur
     */
    public function index()
    {
        
    }
	
	public function create() {
		// Titre de la page
        $page['title'] = 'Création d\'une nouvelle analyse';
        $page['subtitle'] = 'Formulaire de saisie de création d\'une analyse';

        // Récupération des données du formulaire
        $data['lab_internal_code'] = $this->input->post('lab_internal_code');
        $data['sample_id'] = $this->input->post('sample_code');
        $data['analysis_variable'] = $this->input->post('variable_code');
        $data['analysis_type'] = $this->input->post('analysis_type');
        $data['analysis_date'] = $this->input->post('analysis_date');
		$data['analysis_value'] = $this->input->post('analysis_value');
		$data['analysis_status'] = $this->input->post('analysis_status');
		$data['dataset_id']	= $this->input->post('dataset_name');
		$data['trial_code'] = $this->input->post('trial_code');
		
		$scripts = array('bootstrap-select-ajax-plugin');
		
        // Règles de validation du formulaire
        $this->form_validation->set_rules('lab_internal_code', 'Code de l\'analyse', 'trim|required|alpha_dash|max_length[255]|xss_clean');
        $this->form_validation->set_rules('sample_code', 'Code de l\'échantillon', 'trim|required|alpha_dash|max_length[255]|xss_clean');
        $this->form_validation->set_rules('variable_code', 'Code de la variable', 'trim|required|max_length[50]|xss_clean');
        $this->form_validation->set_rules('analysis_type', 'Type d\'analyse', 'trim|alpha_numeric_spaces|max_length[255]|xss_clean');
        $this->form_validation->set_rules('analysis_date', 'Date de l\'analyse', 'trim|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');
		$this->form_validation->set_rules('analysis_value', 'Valeur', 'trim|numeric|xss_clean');
		$this->form_validation->set_rules('analysis_status', 'Statut', 'trim|alpha_numeric_spaces|max_length[255]|xss_clean');
		$this->form_validation->set_rules('dataset_name', 'Nom du jeu de données', 'trim|alpha_dash|required|max_length[80]|xss_clean');
		$this->form_validation->set_rules('trial_code', 'Code de l\'essai', 'trim|alpha_dash|required|max_length[50]|xss_clean');

        // Test de validation du formulaire
        if ($this->form_validation->run()) {
            $this->load->helper('postgre');
			$this->load->model(array('Sample_model','Exp_unit_model','Dataset_model'));
			
            $data = nullify_array($data); // Remplace les chaines de caractères vide par la valeur NULL
			
			if ($this->Analysis_model->exist_lab_analysis_code($data['lab_internal_code'])) {
				$data['msg'] = "Une analyse avec le même code existe déjà";
				$this->view('error', $page['title'], $page['subtitle'], $data);
			}
			else {
				
				$exp_unit_id = $this->Sample_model->return_exp_unit_id($data['sample_id']);
				
				if (!$exp_unit_id) {
					$data['msg'] = "exp_unit_id introuvable à partir du sample_id!";
					$this->view('error', $page['title'], $page['subtitle'], $data);
				}
				else {
					if (!$this->Exp_unit_model->find(array('trial_code' => $data['trial_code'], 'exp_unit_id' => $exp_unit_id))) {
						$data['msg'] = "epx_unit_id et trial_code ne sont pas associés";
						$this->view('error', $page['title'], $page['subtitle'], $data);
					}
					else {
						if (!$this->Analysis_model->create(
							array('lab_internal_code' => $data['lab_internal_code'],
								'sample_id' => $data['sample_id'],
								'analysis_variable' => $data['analysis_variable'],
								'analysis_type' => $data['analysis_type'],
								'analysis_date' => $data['analysis_date'],
								'analysis_value' => $data['analysis_value'],
								'analysis_status' => $data['analysis_status']
								)))
						{
							$data['msg'] = "Erreur lors de la création des données dans la table analyse";
							$this->view('error', $page['title'], $page['subtitle'], $data);
						}
						else {
							if ($this->Dataset_model->create_dataset_analysis($data)) {
								$data['msg'] = "L'analyse a été créé avec succes !";
								$this->view('success', $page['title'], $page['subtitle'], $data);
							}
							else {
								$data['msg'] = "Erreur lors de la création des données dans la table dataset_analysis";
								$this->view('error', $page['title'], $page['subtitle'], $data);
							}
						}
					}
				}
			}
        } else {
            // Affichage du formulaire de création d'analyse  et retour des erreurs
            $this->view('analysis/new_analysis', $page['title'], $page['subtitle'],$data, $scripts);
        }		
	}
	
	/**
     * Lien de téléchargement du formulaire d'importation des analyses
     */
    public function download_form()
    {
        $this->load->helper('download');
        $file_path = 'download_forms/Import_analysis.xlsx';//chemin absolu de mon fichier dans serveur
        force_download($file_path, NULL);
    }
	
	/**
     * Retourne le nom du champ (formaté), si une chaine de caractères est un nom de champ valide
     * pour le formulaire d'importation des données d'analyse. Sinon retourne FALSE
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
	
	/**
     * Formulaire d'importation des analyses
     */
	public function import(){
		$page['title'] = "Importation d'analyse";
		$page['subtitle'] = "Formulaire d'importation des analyses";

		$config['upload_path'] = UPLOAD_PATH;//Le chemin d'accès au répertoire où le téléchargement doit être placé.
		$config['allowed_types'] = 'xls|xlsx'; // Autres extensions de la librairie phpexcel xml|ods|slk|gnumeric|csv|htm|html
		$config['encrypt_name'] = TRUE;

		$this->load->model(array('Sample_model','Exp_unit_model','Dataset_model'));

		$this->load->library('excel');
		$this->load->library('table');
		$this->load->library('upload', $config);

		$this->form_validation->set_rules('trial_code', 'Essai', 'trim|required|alpha_dash|max_length[255]|xss_clean'); 
		$this->form_validation->set_rules('dataset_id', 'Jeu de données', 'trim|required|is_natural_no_zero|xss_clean');
		
		$scripts = array('bootstrap-select-ajax-plugin');
		$data = array();

		// if not successful, set the error message
		if (!$this->form_validation->run() || !$this->upload->do_upload('analysis_file')) {
			$data['error'] = $this->upload->display_errors('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
			$this->view('analysis/import', $page['title'], $page['subtitle'], $data, $scripts);
		} 
		else {
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
				$nbreDeLignesAnalys = $objPHPExcelCopyForCountLineNumber->setActiveSheetIndex(0)->getHighestRow();
				
				//definition d'un tableau contenant les colonnes de base du fichier d'import
                $waiting_main_header = array('lab_internal_analysis_code','analysis_type','analysis_date','analysis_status','sample_code');
				
				if ($nbreDeLignesAnalys > 1) {
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
									array_push($overallMessageErrors, "La variable de la colonne ".$cell_column.", n'existe pas dans la base");
								}
							} else {
								// Vérification des valeurs
								if (isset($header[$cell_column]) && $data_value) {  // Si la colonne de la donnée fait réference à un champ défini dans le header
									switch ($header[$cell_column]) { //test sur le nom du champ
										case 'lab_internal_analysis_code':
											if ($this->Analysis_model->exist_lab_analysis_code($data_value)) {
												array_push($overallMessageErrors, "lab_internal_analysis_code, ligne ".$cell_row.", existe déjà dans la base");
												array_push($overallLineErrors, $cell_row);
												$import_data[$cell_row][$header[$cell_column]] = NULL;
											}
											else $import_data[$cell_row][$header[$cell_column]] = $data_value;
											break;
										case 'analysis_date':
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
													array_push($overallMessageErrors, "date, ligne ".$cell_row." n'existe pas dans le calendrier");
													array_push($overallLineErrors, $cell_row);
													$import_data[$cell_row][$header[$cell_column]] = NULL;
												}
											
											}
											else {
												array_push($overallMessageErrors, "date, ligne ".$cell_row." n'existe pas dans le calendrier");
												array_push($overallLineErrors, $cell_row);
												$import_data[$cell_row][$header[$cell_column]] = NULL;
											}
											
											break;
										case 'sample_code':
											if (!$this->Sample_model->exist_sample_code($data_value)) {
												array_push($overallMessageErrors, "sample_code, ligne ".$cell_row.", n'existe pas dans la base");
												array_push($overallLineErrors, $cell_row);
												$import_data[$cell_row][$header[$cell_column]] = NULL;
											}
											else {
												$sample_id = $this->Sample_model->return_sample_id($data_value);
												$exp_unit_id = $this->Sample_model->return_exp_unit_id($sample_id);
												if ($this->Exp_unit_model->find(array('exp_unit_id' => $exp_unit_id, 'trial_code' => $trial)))
													$import_data[$cell_row][$header[$cell_column]] = $data_value;
												else {
													array_push($overallMessageErrors, "Ensemble exp_unit/trial_code, associé au sample_code ligne ".$cell_row.", n'existe pas dans la base");
													array_push($overallLineErrors, $cell_row);
													$import_data[$cell_row][$header[$cell_column]] = NULL;
												}
											}
											break;
										default:
											$import_data[$cell_row][$header[$cell_column]] = $data_value;
											break;
									}
								}
								elseif (isset($header[$cell_column])) {
									switch ($header[$cell_column]) { //test sur le nom du champ
										case 'lab_internal_analysis_code': //si lab_internal_analysis_code
											array_push($overallMessageErrors, "lab_internal_analysis_code, ligne ".$cell_row.", absent");
											array_push($overallLineErrors, $cell_row);
											break;
										case 'sample_code': //si sample_code
											array_push($overallMessageErrors, "sample_code, ligne ".$cell_row.", absent");
											array_push($overallLineErrors, $cell_row);
											break;
									}
									$import_data[$cell_row][$header[$cell_column]] = NULL;
								}
							}
						}
					}
					
					
					$data['lignesErrors'] = $overallLineErrors;
					$data['messageErrors'] = $overallMessageErrors;
					
					if ( count($overallLineErrors)>0 ) {
						unlink($upload_data['full_path']); //remove file
						$this->view('analysis/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
					else {
						$import_result_analysis = $this->Analysis_model->import($import_data, $trial, $waiting_main_header); // Import des données
						
						$import_result_dataset_itk = $this->Dataset_model->import_dataset_analysis($import_data, $trial, $dataset, $waiting_main_header);
						unlink($upload_data['full_path']); //remove file
						$this->view('analysis/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
				}
				else {
					unlink($upload_data['full_path']); //remove file
					$this->view('analysis/import', $page['title'], $page['subtitle'], $data, $scripts);
				}
				
            } catch (Exception $e) {
                unlink($upload_data['full_path']); // remove file
				show_error($e->getmessage());
			}
		}
	}
}