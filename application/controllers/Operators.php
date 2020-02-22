<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operators extends MY_Controller {
	
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
        $this->load->model('Operator_model');
    }

    /**
     * Affiche la liste des operateurs disponibles pour l'utilisateur
     */
    public function index()
    {
        
    }
	
	public function create() {
		// Titre de la page
        $page['title'] = 'Création d\'un nouvel opérateur';
        $page['subtitle'] = 'Formulaire de saisie de création d\'un opérateur';

        // Récupération des données du formulaire
        $data['last_name'] = $this->input->post('last_name');
        $data['first_name'] = $this->input->post('first_name');
        $data['status'] = $this->input->post('status');
        
		$scripts = array('bootstrap-select-ajax-plugin');
		
        // Règles de validation du formulaire
        $this->form_validation->set_rules('last_name', 'Nom', 'trim|required|max_length[50]|xss_clean');
        $this->form_validation->set_rules('first_name', 'Prénom', 'trim|required|max_length[50]|xss_clean');
        $this->form_validation->set_rules('status', 'Statut', 'trim|required|max_length[50]|xss_clean');
        
        // Test de validation du formulaire
        if ($this->form_validation->run()) {
            $this->load->helper('postgre');
			
            $data = nullify_array($data); // Remplace les chaines de caractères vide par la valeur NULL
			if (!$this->Operator_model->exist($data['last_name'])) {
			
				if (!$this->Operator_model->create(
					array('last_name' => $data['last_name'],
						'first_name' => $data['first_name'],
						'status' => $data['status'])))
				{
					$data['msg'] = "Erreur lors de la création des données dans la table operator";
					$this->view('error', $page['title'], $page['subtitle'], $data);
				} else {
					$data['msg'] = "L'opérateur a été créé avec succès !";
					$this->view('success', $page['title'], $page['subtitle'], $data);
				}
			}
			else {
				$data['msg'] = "Erreur, un opérateur portant le même nom existe déjà";
				$this->view('error', $page['title'], $page['subtitle'], $data);
			}
        } else {
            // Affichage du formulaire de création d'operateur  et retour des erreurs
            $this->view('operator/new_operator', $page['title'], $page['subtitle'],$data, $scripts);
        }		
	}
	
	/**
     * Lien de téléchargement du formulaire d'importation des operateurs
     */
    public function download_form()
    {
        $this->load->helper('download');
        $file_path = 'download_forms/Import_operator.xlsx';//chemin absolu de mon fichier dans serveur
        force_download($file_path, NULL);
    }
	
	
	public function import(){
		$page['title'] = "Importation d'opérateur";
		$page['subtitle'] = "Formulaire d'importation des opérateurs";

		$config['upload_path'] = UPLOAD_PATH;//Le chemin d'accès au répertoire où le téléchargement doit être placé.
		$config['allowed_types'] = 'xls|xlsx'; // Autres extensions de la librairie phpexcel xml|ods|slk|gnumeric|csv|htm|html
		$config['encrypt_name'] = TRUE;

		$this->load->library('excel');
		$this->load->library('table');
		$this->load->library('upload', $config);
		
		$scripts = array('bootstrap-select-ajax-plugin');
		$data = array();

		// if not successful, set the error message
		if (!$this->upload->do_upload('operator_file')) {
			$data['error'] = $this->upload->display_errors('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
			$this->view('operator/import', $page['title'], $page['subtitle'], $data, $scripts);
		}
		else {
			try {
				
				$upload_data = $this->upload->data();//return mon fichier de téléchargement

				PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
				PHPExcel_Settings:: setZipClass(PHPExcel_Settings :: ZIPARCHIVE);
				$objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']);//full_path est le chemin absolu vers le serveur y compris le nom de mon fichier excel
				//utilisation de l'objet excel pour obtenir un objet de feuille active(activeSheet)
				$objWorksheet = $objPHPExcel->getActiveSheet();
				
				$objPHPExcelCopyForCountLineNumber = PHPExcel_IOFactory::load($upload_data['full_path']);//full_path est le chemin absolu vers le serveur y compris le nom de mon fichier excel
				$objPHPExcelCopyForCountLineNumber->getActiveSheet()->toArray(null, true, true, true);//charger les données Excel en PHP.
				$nbreDeLignesOperator = $objPHPExcelCopyForCountLineNumber->setActiveSheetIndex(0)->getHighestRow();
				
				//definition d'un tableau contenant les colonnes de base du fichier d'import
                $waiting_main_header = array('last_name','first_name','status');
				
				if ($nbreDeLignesOperator > 1) {
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
								if (in_array(strtolower($data_value), $waiting_main_header))
									$field = strtolower($data_value);
								else $field = FALSE;
								
								if ($field) { //si la colonne du fichier d'import appartient à la base
									$header[$cell_column] = $field; //ajout du nom du champ dans le tableau header indexe par les num de col dans la feuille excel
								} elseif ($data_value) {
									array_push($overallMessageErrors, "La variable de la colonne ".$cell_column.", n'existe pas dans la base");
								}
							} else {
								// Vérification des valeurs
								if (isset($header[$cell_column]) && $data_value) {  // Si la colonne de la donnée fait réference à un champ défini dans le header
									switch ($header[$cell_column]) { //test sur le nom du champ
										case 'last_name':
											$l_name = $data_value;
											$import_data[$cell_row][$header[$cell_column]] = $data_value;
											break;
										case 'first_name':
											
											if ($this->Operator_model->exist($l_name,$data_value)) {
												array_push($overallMessageErrors, "Opérateur, ligne ".$cell_row.", existe déjà dans la base");
												array_push($overallLineErrors, $cell_row);
												$import_data[$cell_row][$header[$cell_column]] = NULL;
											}
											else $import_data[$cell_row][$header[$cell_column]] = $data_value;
											break;
										default:
											$import_data[$cell_row][$header[$cell_column]] = $data_value;
											break;
									}
								}
								elseif (isset($header[$cell_column])) {
									switch ($header[$cell_column]) { //test sur le nom du champ
										case 'last_name': //si last_name
											array_push($overallMessageErrors, "last_name, ligne ".$cell_row.", absent");
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
						$this->view('operator/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
					else {
						$import_result_operator = $this->Operator_model->import($import_data); // Import des données
						
						unlink($upload_data['full_path']); //remove file
						$this->view('operator/import_success', $page['title'], $page['subtitle'], $data, $scripts);
					}
				}
				else {
					unlink($upload_data['full_path']); //remove file
					$this->view('operator/import', $page['title'], $page['subtitle'], $data, $scripts);
				}
				
            } catch (Exception $e) {
                unlink($upload_data['full_path']); // remove file
				show_error($e->getmessage());
			}
		}
	}
}