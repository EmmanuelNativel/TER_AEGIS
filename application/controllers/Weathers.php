<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Weathers extends MY_Controller {
	
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
        $this->load->model('Weather_model');
    }

    /**
     * Affiche la liste des meteos disponibles pour l'utilisateur
     */
    public function index()
    {
        
    }
	
	/**
     * Lien de téléchargement du formulaire d'importation des meteos
     */
    public function download_form()
    {
        $this->load->helper('download');
        $file_path = 'download_forms/Import_weather.xlsx';//chemin absolu de mon fichier dans serveur
        force_download($file_path, NULL);
    }
	
	/**
     * Retourne le nom du champ (formaté), si une chaine de caractères est un nom de champ valide
     * pour le formulaire d'importation des données meteos. Sinon retourne FALSE
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
     * Formulaire d'importation des meteos
     */
	public function import(){
		$page['title'] = "Importation d'donnée météorologique";
		$page['subtitle'] = "Formulaire d'importation des donnée météorologiques";

		$config['upload_path'] = UPLOAD_PATH;//Le chemin d'accès au répertoire où le téléchargement doit être placé.
		$config['allowed_types'] = 'xls|xlsx'; // Autres extensions de la librairie phpexcel xml|ods|slk|gnumeric|csv|htm|html
		$config['encrypt_name'] = TRUE;

		$this->load->library('excel');
		$this->load->library('table');
		$this->load->library('upload', $config);
		
		$this->load->model(array('Dataset_model','Weather_station_model','Weather_station_trial_model'));
		
		$scripts = array('bootstrap-select-ajax-plugin');
		$data = array();
		
		$this->form_validation->set_rules('dataset_id', 'Jeu de données', 'trim|required|is_natural_no_zero|xss_clean');
		$this->form_validation->set_rules('trial_code', 'Essai', 'trim|required|alpha_dash|max_length[255]|xss_clean');
		
		// if not successful, set the error message
		if (!$this->form_validation->run() || !$this->upload->do_upload('weather_file')) {
			$data['error'] = $this->upload->display_errors('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
			$this->view('weather/import', $page['title'], $page['subtitle'], $data, $scripts);
		}
		else {
			try {
				$dataset = $this->input->post('dataset_id');
				$trial = $this->input->post('trial_code');
				
				$upload_data = $this->upload->data();//return mon fichier de téléchargement

				PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
				PHPExcel_Settings:: setZipClass(PHPExcel_Settings :: ZIPARCHIVE);
				$objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']);//full_path est le chemin absolu vers le serveur y compris le nom de mon fichier excel
				//utilisation de l'objet excel pour obtenir un objet de feuille active(activeSheet)
				$objWorksheet = $objPHPExcel->getActiveSheet();
				
				$objPHPExcelCopyForCountLineNumber = PHPExcel_IOFactory::load($upload_data['full_path']);//full_path est le chemin absolu vers le serveur y compris le nom de mon fichier excel
				$objPHPExcelCopyForCountLineNumber->getActiveSheet()->toArray(null, true, true, true);//charger les données Excel en PHP.
				$nbreDeLignesWeather = $objPHPExcelCopyForCountLineNumber->setActiveSheetIndex(0)->getHighestRow();
				
				//definition d'un tableau contenant les colonnes de base du fichier d'import
                $waiting_main_header = array('wscode','weather_date');
				
				if ($nbreDeLignesWeather > 1) {
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
										case 'wscode':
											if (!$this->Weather_station_model->exist($data_value)){
												array_push($overallMessageErrors, "Station météorologique, ligne ".$cell_row.", n'existe pas dans la base");
												array_push($overallLineErrors, $cell_row);
												$import_data[$cell_row][$header[$cell_column]] = NULL;
											}
											else {
												if (!$this->Weather_station_trial_model->exist($data_value,$trial)) {
													array_push($overallMessageErrors, "Ensemble Station météorologique / Trial code, ligne ".$cell_row.", n'existe pas dans la base");
													array_push($overallLineErrors, $cell_row);
													$import_data[$cell_row][$header[$cell_column]] = NULL;
												}
												else
												{
													$import_data[$cell_row][$header[$cell_column]] = $data_value;
													$ws_code = $data_value;
												}
											}
											break;
										case 'weather_date':
											
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
											
											if ($this->table->find(array('wscode' => $import_data[$cell_row]['wscode'], 'weatherdate' => $import_data[$cell_row]['weatherdate'], 'weather_variable' => $header[$cell_column]))) {
												//message dialog ecraser données	?
											}
											else $import_data[$cell_row][$header[$cell_column]] = $data_value;
											
											//$import_data[$cell_row][$header[$cell_column]] = $data_value;
											break;
									}
								}
								elseif (isset($header[$cell_column])) {
									switch ($header[$cell_column]) { //test sur le nom du champ
										case 'wscode' :
											array_push($overallMessageErrors, $header[$cell_column].", ligne ".$cell_row.", absent");
											array_push($overallLineErrors, $cell_row);
											break;
										case 'weather_date': //si last_name
											array_push($overallMessageErrors, $header[$cell_column].", ligne ".$cell_row.", absent");
											array_push($overallLineErrors, $cell_row);
											break;
										default :
											$import_data[$cell_row][$header[$cell_column]] = NULL;
											break;
									}
								}
							}
						}
					}
					
					
					$data['lignesErrors'] = $overallLineErrors;
					$data['messageErrors'] = $overallMessageErrors;
					
					if ( count($overallLineErrors)>0 ) {
						unlink($upload_data['full_path']); //remove file
						$this->view('weather/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
					else {
						$import_result_weather = $this->Weather_model->import($import_data,$waiting_main_header); // Import des données
						
						$import_result_dataset_wd = $this->Dataset_model->import_dataset_wd($import_data, $dataset, $waiting_main_header);
						
						unlink($upload_data['full_path']); //remove file
						$this->view('weather/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
				}
				else {
					unlink($upload_data['full_path']); //remove file
					$this->view('weather/import', $page['title'], $page['subtitle'], $data, $scripts);
				}
				
            } catch (Exception $e) {
                unlink($upload_data['full_path']); // remove file
				show_error($e->getmessage());
			}
		}
	}
}