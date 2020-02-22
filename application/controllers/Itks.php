<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Itks extends MY_Controller {

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
        $this->load->model('Itk_model');
    }

    /**
     * Affiche la liste des itks disponibles pour l'utilisateur
     */
    public function index()
    {
        
    }
	
	public function create()
    {
        // Titre de la page
        $page['title'] = 'Création d\'un nouvel itinéraire technique';
        $page['subtitle'] = 'Formulaire de saisie de création d\'un itinéraire technique';

        // Récupération des données du formulaire
        $data['itk_duration'] = $this->input->post('itk_duration');
        $data['itk_date'] = $this->input->post('itk_date');
        $data['itk_variable'] = $this->input->post('variable_code');
        $data['itk_value'] = $this->input->post('itk_value');
        $data['exp_unit_id'] = $this->input->post('unit_code');
		$data['dataset_id'] = $this->input->post('dataset_name');
		$data['trial_code'] = $this->input->post('trial_code');
		
		$scripts = array('bootstrap-select-ajax-plugin');
		
        // Règles de validation du formulaire
        $this->form_validation->set_rules('itk_duration', 'Durée itineraire technique', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('itk_date', 'Date itineraire technique', 'trim|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');
        $this->form_validation->set_rules('variable_code', ' Code Variable utilisée par itineraire technique', 'trim|required|max_length[50]|xss_clean');
        $this->form_validation->set_rules('itk_value', 'Valeur', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('unit_code', 'Code unité expérimentale', 'trim|required|alpha_dash|max_length[25]|xss_clean');
		$this->form_validation->set_rules('dataset_name', 'Dataset', 'trim|alpha_dash|required|max_length[80]|xss_clean');
		$this->form_validation->set_rules('trial_code', 'Essai', 'trim|required|alpha_dash|max_length[50]|xss_clean');

        // Test de validation du formulaire
        if ($this->form_validation->run()) {
            $this->load->helper('postgre');
			$this->load->model(array('Exp_unit_model','Dataset_model'));
			
            $data = nullify_array($data); // Remplace les chaines de caractères vide par la valeur NULL
			
			if ($this->Itk_model->exist_expunit_variable_date($data)) {
				$data['msg'] = "Un itinéraire technique avec la même unité expérimentale, date et variable observée existe déjà";
				$this->view('error', $page['title'], $page['subtitle'], $data);
			}
			else {
				
				if (!$this->Exp_unit_model->find(array('trial_code' => $data['trial_code'], 'exp_unit_id' => $data['exp_unit_id']))) {
					$data['msg'] = "epx_unit_id et trial_code ne sont pas associés";
					$this->view('error', $page['title'], $page['subtitle'], $data);
				}
				else {
					if (!$this->Itk_model->create(
						array('itk_duration' => $data['itk_duration'],
							'itk_date' => $data['itk_date'],
							'itk_variable' => $data['itk_variable'],
							'itk_value' => $data['itk_value'],
							'exp_unit_id' => $data['exp_unit_id'])))
					{
						$data['msg'] = "Erreur lors de la création des données d'itinéraire technique";
						$this->view('error', $page['title'], $page['subtitle'], $data);
					}
					else {
						if (!$this->Dataset_model->create_dataset_itk($data)){
							$data['msg'] = "Erreur lors de la création des données 'Jeu de données - Itinéraire technique'";
							$this->view('error', $page['title'], $page['subtitle'], $data);
						}
						else {
							$data['msg'] = "L'itinéraire technique a été créé avec succès !";
							$this->view('success', $page['title'], $page['subtitle'], $data);
						}
					}
				}
			}
        } else {
            // Affichage du formulaire de création de itks et retour des erreurs
            $this->view('itk/new_itk', $page['title'], $page['subtitle'],$data, $scripts);
        }
    }

    /**
     * Lien de téléchargement du formulaire d'importation des itks
     */
    public function download_form()
    {
        $this->load->helper('download');
        $file_path = 'download_forms/Import_itk.xlsx';//chemin absolu de mon fichier dans serveur
        force_download($file_path, NULL);
    }
	
	/**
     * Formulaire d'importation des itks
     */
	public function import(){
		$page['title'] = "Importation d'itinéraire technique";
        $page['subtitle'] = "Formulaire d'importation des itinéraires techniques";

        $config['upload_path'] = UPLOAD_PATH;//Le chemin d'accès au répertoire où le téléchargement doit être placé.
        $config['allowed_types'] = 'xls|xlsx'; // Autres extensions de la librairie phpexcel xml|ods|slk|gnumeric|csv|htm|html
        $config['encrypt_name'] = TRUE;

		$this->load->model(array('Exp_unit_model','Dataset_model'));

        $this->load->library('excel');
        $this->load->library('table');
        $this->load->library('upload', $config);

        $this->form_validation->set_rules('trial_code', 'Essai', 'trim|required|alpha_dash|max_length[255]|xss_clean'); 
		$this->form_validation->set_rules('dataset_id', 'Jeu de données', 'trim|required|is_natural_no_zero|xss_clean');
		
		$scripts = array('bootstrap-select-ajax-plugin');
		$data = array();

        // if not successful, set the error message
        if (!$this->form_validation->run() || !$this->upload->do_upload('itk_file')) {
            $data['error'] = $this->upload->display_errors('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
            $this->view('itk/import', $page['title'], $page['subtitle'], $data, $scripts);
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
				$nbreDeLignesItk = $objPHPExcelCopyForCountLineNumber->setActiveSheetIndex(0)->getHighestRow();
				
				//definition d'un tableau contenant les colonnes de base du fichier d'import
                $waiting_main_header = array('unit_code','date','duree');
				
				if ($nbreDeLignesItk > 1) {
					$header = array(); // init du tableau qui contiendra les colonnes valides que l on souhaite importer
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
								// Vérification du header, si var utilisee par itineraire tech. presente dans la base
								$field = $this->format_field($data_value, $waiting_main_header);

								if ($field) { //si la colonne du fichier d'import appartient à la base
									$header[$cell_column] = $field; //ajout du nom du champ dans le tableau header indexe par les num de col dans la feuille excel
								} elseif ($data_value) {
									array_push($overallMessageErrors, "La variable de la colonne ".$cell_column.", n'existe pas dans la base");
								}
							} else {
								// Vérification des valeurs
								if (isset($header[$cell_column]) && $data_value) {  // Si la colonne de la donnée fait réference à un champ défini dans le header
									if (strcmp($header[$cell_column],'unit_code')==0) { //test sur le nom du champ si unit_code
										if (!$this->Exp_unit_model->find(array('trial_code' => $trial, 'unit_code' => $data_value))) {
											array_push($overallMessageErrors, "Ensemble unit_code/Trial_code, ligne ".$cell_row.", n'existe pas dans la base");
											array_push($overallLineErrors, $cell_row);
											$import_data[$cell_row][$header[$cell_column]] = NULL;
										}
										else $import_data[$cell_row][$header[$cell_column]] = $data_value;
									} 
									elseif ($header[$cell_column] == 'date') {
										
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
									}
									else {
										// matrice de donnees a importer indexe par numligne et nom de colonne
										$import_data[$cell_row][$header[$cell_column]] = $data_value;
									}
								} elseif (isset($header[$cell_column])) {
									if (strcmp($header[$cell_column],'unit_code')==0) { //test sur le nom du champ si unit_code
										array_push($overallMessageErrors, "Ensemble unit_code/Trial_code, ligne ".$cell_row.", absent");
										array_push($overallLineErrors, $cell_row);
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
						$this->view('itk/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
					else {
						$import_result_itk = $this->Itk_model->import($import_data, $trial, $waiting_main_header); // Import des données
						
						$import_result_dataset_itk = $this->Dataset_model->import_dataset_itk($import_data, $trial, $dataset, $waiting_main_header);
						unlink($upload_data['full_path']); //remove file
						$this->view('itk/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
				}
				else {
					unlink($upload_data['full_path']); //remove file
					$this->view('itk/import', $page['title'], $page['subtitle'], $data, $scripts);
				}
				
            } catch (Exception $e) {
                unlink($upload_data['full_path']); // remove file
				show_error($e->getmessage());
			}
		}
	}
	
	/**
     * Retourne le nom du champ (formaté), si une chaine de caractères est un nom de champ valide
     * pour le formulaire d'importation des données d'itineraire technique. Sinon retourne FALSE
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
	
}