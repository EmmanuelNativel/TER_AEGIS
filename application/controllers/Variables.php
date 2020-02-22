<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Variables extends MY_Controller {

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
        $this->load->model('Variable_model');
    }

    /**
     * Ajoute a la variable de session 'array_variable' la variable passée en post
     * Si la variable de session n'existe pas, on la créer
     */
    public function add_var_in_session()
    {
        if (!$this->input->is_ajax_request()) {
           show_404();
        }
        else {
        	$array_var = array();
        	if ($this->session->userdata('array_variable') != null) {
        		/*$this->session->userdata('array_variable', array());*/
        		$array_var = $this->session->userdata('array_variable');
        	}
            $var_name = $this->input->post('var_name');
            $int_return = -1;
            if (in_array($var_name, $array_var)){
            	$int_return = 2;
            }else{
            	array_push($array_var, $var_name);
            	$int_return = 1;
            }

            $this->session->set_userdata('array_variable', $array_var);

            echo json_encode($int_return);
        }
    }
    /**
     * Enlebe a la variable de session 'array_variable' la variable passée en post
     */
    public function remove_var_in_session()
    {
        if (!$this->input->is_ajax_request()) {
           show_404();
        }
        else {
            $var_name = $this->input->post('var_name');
            $init_length = count($this->session->userdata('array_variable'));
        	$key = array_search($var_name, $this->session->userdata('array_variable'));
        	$arrayTemp = $this->session->userdata('array_variable');
        	array_splice($arrayTemp, $key, 1);

            $this->session->set_userdata('array_variable', $arrayTemp);

        	$int_return =-1;
        	if ($init_length = count($this->session->userdata('array_variable'))+1) {
        		$int_return = 1;
        	}
            echo json_encode($int_return);
        }
    }


    /**
     * Affiche la page principal permettant d'accedé au différentes fonctionnalités du dictionnaire des variables
     */
    public function index()
    {
        // Titre de la page
        $page['title'] = 'Dictionnaire des variables';
        $page['subtitle'] = '';

        $data['empty'] ="";

	    $scripts = array('bootstrap-select-ajax-plugin');
	        $this->view('variable/accueil', $page['title'], $page['subtitle'], $data, $scripts);

    }

    /**
     * Affiche la page de consultation du panier des variables
     */
    public function display_cart()
    {

		// Titre de la page
        $page['title'] = 'Panier des variables';
        $page['subtitle'] = 'Consultation';

        $array_data = array();
        if ($this->session->userdata('array_variable') != null && count($this->session->userdata('array_variable'))>0) {
	        foreach ($this->session->userdata('array_variable') as $key => $varaible) {
	        	$data_var = $this->Variable_model->get_var_trait_entity_method_scale($varaible);
	        	array_push($array_data, $data_var);
	        }
        }
        $data["data_var"] = $array_data;
	    $scripts = array('bootstrap-select-ajax-plugin', 'variable_session_consultation');
        $this->view('variable/variable_session_consultation', $page['title'], $page['subtitle'], $data, $scripts);
    }

    /**
     * Permet de générer un fichier Excel avec comme colonnes l'ensemble des variables dans le panier
     */
    public function create_excel_file(){
        if ($this->session->userdata('array_variable') != null && count($this->session->userdata('array_variable'))>0) {
        	$this->load->library('excel');
            $this->load->library('table');

        	$array_var = $this->session->userdata('array_variable');

        	$object = new PHPExcel();
            $object->setActiveSheetIndex(0);

            //set les nom des colonnes avec les noms des variabeles
            $column = 0;
            foreach ($array_var as $key => $var_name) {
                $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $var_name);
                $column++;
            }

            $object_writer= PHPExcel_IOFactory::createWriter($object, 'Excel5');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="variables.xls"');
            $object_writer->save('php://output');

        }else{
            redirect('variables/display_cart', 'location');
        }
    }

	/**
     * affiche la page de consultation des variables
     * Si aucun paramètre alors on affiche la page générale qui présente toutes les variables (dictionnaire des variables)
     * Si il y a un paramètre alors on affiche la page détaillé de la variables passée en paramètre
     */
    public function consultation($variable_code = '')
    {
    	if ($variable_code == null || $variable_code == '') {
		    /**
		     * affiche la page de consultation de toutes les variables "dictionnaire des variables"
		     */
    		// Titre de la page
	        $page['title'] = 'Catalogue des variables';
	        $page['subtitle'] = '';


	        $data['distinct_class'] = $this->Variable_model->select_distinct_class();
	        $data['distinct_subclass'] = $this->Variable_model->select_distinct_subclass();
	        $data['distinct_domain'] = $this->Variable_model->select_distinct_domain();
	        $data['class_subclass_domain'] = $this->Variable_model->select_distinct_class_subclass_domain();

	        $data['varesult'] =$this->Variable_model->get_all_from_varesult();

	        $scripts = array('bootstrap-select-ajax-plugin', 'variable_consultation');
	        $this->view('variable/consultation', $page['title'], $page['subtitle'], $data, $scripts);
    	}else{
    		/**
		     * affiche la page de consultation détaillée d'une variable passée en paramètre
		     */


    		//decodage du code variable passé en param problème avec les signes '%, /'
	        $uri_seg = $this->uri->uri_to_assoc(4);
	        $real_variable_code = urldecode($variable_code);
		    foreach($uri_seg as $key => $para){
		    	$real_variable_code .= "/".urldecode($key);
		    	if ($para != null && $para != '') {
		    		$real_variable_code .= "/".urldecode($para);
		    	}
		    }

		    // Titre de la page
	        $page['title'] = 'consultation des variables';
	        $page['subtitle'] = $real_variable_code;

	        $data['variable_code'] = $variable_code;
	        $data['real_variable_code'] = $real_variable_code;

	        $data['variable_varesult'] = $this->Variable_model->return_var_from_varesult($real_variable_code);
	        /*if ($data['variable_varesult'] == null) {

	        }else{

	        }*/
	        $this->load->model(array('Trait_model', 'Method_model', 'Scale_model', 'Entity_model'));

	        $data['info_without_onto'] = $this->Variable_model->get_var_trait_entity_method_scale($real_variable_code);
	        $data['var_onto'] = $this->Variable_model->get_ontology($real_variable_code);
	        $data['trait_onto'] = $this->Trait_model->get_ontology($data['info_without_onto']['trait_code']);
	        $data['entity_onto'] = $this->Entity_model->get_ontology($data['info_without_onto']['entity_code']);
	        $data['method_onto'] = $this->Method_model->get_ontology($data['info_without_onto']['method_code']);
	        $data['scale_onto'] = $this->Scale_model->get_ontology($data['info_without_onto']['scale_code']);


	        $scripts = array('bootstrap-select-ajax-plugin', 'detail_consultation_variable');
	        $this->view('variable/detail_consultation', $page['title'], $page['subtitle'], $data, $scripts);
    	}
    }

    public function saisie()
    {
        // Titre de la page
        $page['title'] = 'Création de nouvelle variable';
        $page['subtitle'] = 'Formulaire de saisie de création d\'une variable';

	    $this->load->model(array('Trait_model', 'Method_model', 'Scale_model', 'Entity_model'));

        $data['method_classes'] = $this->Method_model->get_distinct_classes();
        $data['method_subclasses'] = $this->Method_model->get_distinct_subclasses();

        // Récupération des données du formulaire
        $data['variable_code'] = $this->input->post('variable_code');
        $data['trait_code'] = $this->input->post('trait_code');
        $data['method_code'] = $this->input->post('method_code');
        $data['scale_code'] = $this->input->post('scale_code');
        $data['author'] = $this->input->post('author');
        $data['class'] = $this->input->post('class');
        $data['subclass'] = $this->input->post('subclass');
		$data['domain'] = $this->input->post('domain');

		$scripts = array('bootstrap-select-ajax-plugin', 'new_variable');
		$messageErrors = array();

        // Règles de validation du formulaire
        $this->form_validation->set_rules('variable_code', 'Code variable', 'required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('trait_code', 'Code trait', 'trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('method_code', 'Code méthode', 'trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('scale_code', 'Code échelle/unité', 'trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('author', 'Auteur', 'trim|max_length[100]|xss_clean');
        $this->form_validation->set_rules('class', 'Classe', 'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('subclass', 'Sous classe', 'trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('domain', 'Domaine', 'trim|max_length[255]|xss_clean');

        $this->load->library('session');
        // Test de validation du formulaire
        if ($this->form_validation->run()) {
            $this->load->helper('postgre');

            $data = nullify_array($data); // Remplace les chaines de caractères vide par la valeur NULL
			if (!($this->Variable_model->exist($data['variable_code']))) {
				if (!$this->Variable_model->create(
					array('variable_code' => $data['variable_code'],
						'trait_code' => $data['trait_code'],
						'method_code' => $data['method_code'],
						'scale_code' => $data['scale_code'],
						'author' => $data['author'],
						'class' => $data['class'],
						'subclass' => $data['subclass'],
						'domain' => $data['domain'])))
				{
					$this->session->set_flashdata('msg', 'Erreur lors de la création des données de Variable');
					$this->session->set_flashdata('msg_state', 'danger');
				}
				else {
					$this->session->set_flashdata('msg', 'La variable a été créée avec succés !');
					$this->session->set_flashdata('msg_state', 'success');
				}
			}
			else {
				$this->session->set_flashdata('msg', 'Une variable portant ce variable_code existe déjà');
				$this->session->set_flashdata('msg_state', 'danger');
			}
        }
        // Affichage du formulaire de création de variables et retour des erreurs
        $this->view('variable/saisie_variable', $page['title'], $page['subtitle'],$data,  $scripts);

    }




	public function create()
    {
        // Titre de la page
        $page['title'] = 'Création de nouvelle variable';
        $page['subtitle'] = 'Formulaire de saisie de création d\'une variable';

        // Récupération des données du formulaire
        $data['variable_code'] = $this->input->post('variable_code');
        $data['trait_code'] = $this->input->post('trait_code');
        $data['method_code'] = $this->input->post('method_code');
        $data['scale_code'] = $this->input->post('scale_code');
        $data['author'] = $this->input->post('author');
        $data['class'] = $this->input->post('class');
        $data['subclass'] = $this->input->post('subclass');
		$data['domain'] = $this->input->post('domain');

		$scripts = array('bootstrap-select-ajax-plugin');
		$messageErrors = array();

        // Règles de validation du formulaire
        $this->form_validation->set_rules('variable_code', 'Code variable', 'required|trim|alpha_dash|max_length[50]|xss_clean');
        $this->form_validation->set_rules('trait_code', 'Code trait', 'trim|required|alpha_dash|max_length[50]|xss_clean');
        $this->form_validation->set_rules('method_code', 'Code méthode', 'trim|required|alpha_dash|max_length[50]|xss_clean');
        $this->form_validation->set_rules('scale_code', 'Code échelle/unité', 'trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('author', 'Auteur', 'trim|alpha_numeric_spaces|max_length[100]|xss_clean');
        $this->form_validation->set_rules('class', 'Classe', 'trim|alpha_numeric_spaces|max_length[255]|xss_clean');
        $this->form_validation->set_rules('subclass', 'Sous classe', 'trim|alpha_numeric_spaces|max_length[255]|xss_clean');
		$this->form_validation->set_rules('domain', 'Domaine', 'trim|alpha_numeric_spaces|max_length[255]|xss_clean');

        // Test de validation du formulaire
        if ($this->form_validation->run()) {
            $this->load->helper('postgre');

            $data = nullify_array($data); // Remplace les chaines de caractères vide par la valeur NULL
			if (!($this->Variable_model->exist($data['variable_code']))) {
				if (!$this->Variable_model->create(
					array('variable_code' => $data['variable_code'],
						'trait_code' => $data['trait_code'],
						'method_code' => $data['method_code'],
						'scale_code' => $data['scale_code'],
						'author' => $data['author'],
						'class' => $data['class'],
						'subclass' => $data['subclass'],
						'domain' => $data['domain'])))
				{
					$data['msg'] = "Erreur lors de la création des données de Variable";
					$this->view('error', $page['title'], $page['subtitle'], $data);
				}
				else {
					$data['msg'] = "La variable a été créée avec succés!";
					$this->view('success', $page['title'], $page['subtitle'], $data);
				}
			}
			else {
				$data['msg'] = "Une variable portant ce variable_code existe déjà";
				$this->view('error', $page['title'], $page['subtitle'], $data);
			}
        } else {
            // Affichage du formulaire de création de variables et retour des erreurs
            $this->view('variable/new_variable', $page['title'], $page['subtitle'],$data,  $scripts);
        }
    }

    /**
     * Lien de téléchargement du formulaire d'importation des variables
     */
    public function download_form()
    {
        $this->load->helper('download');
        $file_path = 'download_forms/Import_variable.xlsx';//chemin absolu de mon fichier dans serveur
        force_download($file_path, NULL);
    }

	    /**
     * Appel de la vue import
     */
    public function mains_page(){
        $page['title'] = "Importation de variables";
        $page['subtitle'] = "Formulaire d'importation des variables";
        $scripts = array('bootstrap-select-ajax-plugin');
        $this->view('variable/import', $page['title'], $page['subtitle'], $scripts);
    }

    /**
     * Formulaire d'importation des variables
     */
    public function import()
    {
		$page['title'] = "Importation des variables";
        $page['subtitle'] = "Formulaire d'importation des variables";

        $config['upload_path'] = UPLOAD_PATH;//Le chemin d'accès au répertoire où le téléchargement doit être placé.
        $config['allowed_types'] = 'xls|xlsx'; // Autres extensions de la librairie phpexcel xml|ods|slk|gnumeric|csv|htm|html
        $config['encrypt_name'] = TRUE;

        $this->load->library('excel');
        $this->load->library('table');
        $this->load->library('upload', $config);

		    $scripts = array('bootstrap-select-ajax-plugin');

        $data = array();

        // if not successful, set the error message
        if (!$this->upload->do_upload('variable_file')) {
            $data['error'] = $this->upload->display_errors('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
            $this->view('variable/import', $page['title'], $page['subtitle'], $data, $scripts);
        } else {
            try {
				$this->load->model(array('Entity_model','Target_model','Trait_model','Method_model','Scale_model'));

                $upload_data = $this->upload->data();//return mon fichier de téléchargement

                PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
                PHPExcel_Settings:: setZipClass(PHPExcel_Settings :: ZIPARCHIVE);
                $objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']);//full_path est le chemin absolu vers le serveur y compris le nom de mon fichier excel

				// récupère tous les noms de feuilles du fichier
                $worksheetNames = $objPHPExcel->getSheetNames($upload_data['full_path']);
                $worksheetArray = array();

                foreach ($worksheetNames as $key => $sheetName) { //parcours de chaque feuille
					if ($sheetName != 'Lexicon') {
						//set the current active worksheet by name
						$objPHPExcel->setActiveSheetIndexByName($sheetName);
						// crée un tableau assoc avec le nom de la feuille en tant que clé et le tableau des contenus de la feuille comme valeur
						$worksheetArray[$sheetName] = $objPHPExcel->getActiveSheet();
					}
				}

				$nbreDeLignesVariable = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
				$nbreDeLignesEntity = $objPHPExcel->setActiveSheetIndex(1)->getHighestRow();
				$nbreDeLignesTarget = $objPHPExcel->setActiveSheetIndex(2)->getHighestRow();
				$nbreDeLignesTrait = $objPHPExcel->setActiveSheetIndex(3)->getHighestRow();
				$nbreDeLignesMethod = $objPHPExcel->setActiveSheetIndex(4)->getHighestRow();
				$nbreDeLignesScale = $objPHPExcel->setActiveSheetIndex(5)->getHighestRow();

				//definition des tableaux contenant les colonnes de base des feuilles du fichier d'import
				$waiting_variable_header = array('variable_code','trait_code','method_code','scale_code','author','class','subclass','domain');
				$waiting_entity_header = array('entity_code','entity_name','entity_definition');
				$waiting_target_header = array('target_name');
				$waiting_trait_header = array('trait_code','trait_name','trait_description','trait_entity_code','trait_target_name','trait_author');
				$waiting_method_header = array('method_code','method_name','method_class','method_subclass','method_description','method_formula','method_reference','method_type','content_type','author');
				$waiting_scale_header = array('scale_code','scale_name','scale_type','scale_level');


				$data['lignesErrors'] = array();
				$data['messageErrors'] = array();

				if ($nbreDeLignesVariable > 1) {
					if ( $nbreDeLignesScale > 1) {

						$tabResultCheckData['Scale'] = $this->format_worksheetdata($worksheetArray['Scale'], 'Scale', $waiting_scale_header);
						if (count($tabResultCheckData['Scale']['lignesErrors']) >0) {
							$data['lignesErrors'] = array_merge($data['lignesErrors'],$tabResultCheckData['Scale']['lignesErrors']);
							$data['messageErrors'] = array_merge($data['messageErrors'],$tabResultCheckData['Scale']['messageErrors']);
							throw new Exception("Problème de données avec l'onglet Scale", 1);
						} //si vérification données ok
						else { //alors import
							if (!$this->Scale_model->import($tabResultCheckData['Scale']['import_data'])){
								array_push($data['messageErrors'], "Erreur durant l'importation dans la base pour l'onglet Scale");
								throw new Exception("Erreur durant l'importation dans la base pour l'onglet Scale",1);
							}
						}
					}

					if ( $nbreDeLignesMethod > 1 ) {
						$tabResultCheckData['Method'] = $this->format_worksheetdata($worksheetArray['Method'], 'Method', $waiting_method_header);
						if (count($tabResultCheckData['Method']['lignesErrors']) >0) {
							$data['lignesErrors'] = array_merge($data['lignesErrors'],$tabResultCheckData['Method']['lignesErrors']);
							$data['messageErrors'] = array_merge($data['messageErrors'],$tabResultCheckData['Method']['messageErrors']);
							throw new Exception("Problème de données avec l'onglet Method", 1);
						}
						else {
							if (!$this->Method_model->import($tabResultCheckData['Method']['import_data'])){
								array_push($data['messageErrors'], "Erreur durant l'importation dans la base pour l'onglet Method");
								throw new Exception("Erreur durant l'importation dans la base pour l'onglet Method",1);
							}
						}
					}

					if ( $nbreDeLignesTarget > 1 ) {
						$tabResultCheckData['Target'] = $this->format_worksheetdata($worksheetArray['Target'], 'Target', $waiting_target_header);
						if (count($tabResultCheckData['Target']['lignesErrors']) >0) {
							$data['lignesErrors'] = array_merge($data['lignesErrors'],$tabResultCheckData['Target']['lignesErrors']);
							$data['messageErrors'] = array_merge($data['messageErrors'],$tabResultCheckData['Target']['messageErrors']);
							throw new Exception("Problème de données avec l'onglet Target", 1);
						} else {
							if (!$this->Target_model->import($tabResultCheckData['Target']['import_data'])) {
								array_push($data['messageErrors'], "Erreur durant l'importation dans la base pour l'onglet Target");
								throw new Exception("Erreur durant l'importation dans la base pour l'onglet Target",1);
							}
						}
					}

					if ( $nbreDeLignesEntity > 1 ) {
						$tabResultCheckData['Entity'] = $this->format_worksheetdata($worksheetArray['Entity'], 'Entity', $waiting_entity_header);
						if (count($tabResultCheckData['Entity']['lignesErrors']) >0) {
							$data['lignesErrors'] = array_merge($data['lignesErrors'],$tabResultCheckData['Entity']['lignesErrors']);
							$data['messageErrors'] = array_merge($data['messageErrors'],$tabResultCheckData['Entity']['messageErrors']);
							throw new Exception("Problème de données avec l'onglet Entity", 1);
						} else {
							if (!$this->Entity_model->import($tabResultCheckData['Entity']['import_data'])) {
								array_push($data['messageErrors'], "Erreur durant l'importation dans la base pour l'onglet Entity");
								throw new Exception("Erreur durant l'importation dans la base pour l'onglet Entity",1);
							}
						}
					}

					if ( $nbreDeLignesTrait > 1 ) {
						$tabResultCheckData['Trait'] = $this->format_worksheetdata($worksheetArray['Trait'], 'Trait', $waiting_trait_header);
						if (count($tabResultCheckData['Trait']['lignesErrors']) >0) {
							$data['lignesErrors'] = array_merge($data['lignesErrors'],$tabResultCheckData['Trait']['lignesErrors']);
							$data['messageErrors'] = array_merge($data['messageErrors'],$tabResultCheckData['Trait']['messageErrors']);
							throw new Exception("Problème de données avec l'onglet Trait", 1);
						} else {
							if (!$this->Trait_model->import($tabResultCheckData['Trait']['import_data'])) {
								array_push($data['messageErrors'], "Erreur durant l'importation dans la base pour l'onglet Trait");
								throw new Exception("Erreur durant l'importation dans la base pour l'onglet Trait",1);
							}
						}
					}

					$tabResultCheckData['Variable'] = $this->format_worksheetdata($worksheetArray['Variable'], 'Variable', $waiting_variable_header);
					if (count($tabResultCheckData['Variable']['lignesErrors']) >0) {
						$data['lignesErrors'] = array_merge($data['lignesErrors'],$tabResultCheckData['Variable']['lignesErrors']);
						$data['messageErrors'] = array_merge($data['messageErrors'],$tabResultCheckData['Variable']['messageErrors']);
						throw new Exception("Problème de données avec l'onglet Variable", 1);
					} else {
						if (!$this->Variable_model->import($tabResultCheckData['Variable']['import_data'])) {
							array_push($data['messageErrors'], "Erreur durant l'importation dans la base pour l'onglet Variable");
							throw new Exception("Erreur durant l'importation dans la base pour l'onglet Variable",1);
						}
					}
					unlink($upload_data['full_path']); //remove file
					$this->view('variable/import_success', $page['title'], $page['subtitle'], $data, $scripts);
				} else {
					unlink($upload_data['full_path']); //remove file
					$this->view('variable/import', $page['title'], $page['subtitle'], $data, $scripts);
				}

            } catch (Exception $e) {
				      $this->view('variable/import_success', $page['title'], $page['subtitle'], $data, $scripts);
              unlink($upload_data['full_path']); // remove file
				//show_error($e->getmessage());
            }
        }
	}

	/**
     * Retourne le nom du champ (formaté), si une chaine de caractères est un nom de champ valide
     * pour le formulaire d'importation des données liées à une var. Sinon retourne FALSE
     */
    public function format_field($field, $waiting_main_header)
    {
        if (in_array(strtolower($field), $waiting_main_header)) {
            return strtolower($field);
        } else {
            return FALSE;
        }
    }

	//fonction retournant la matrice de donnees verifiees et les lignes erreurs ainsi que leur message
	public function format_worksheetdata($worksheetdata, $worksheetname, $checking_header){

		$header = array(); // init du tableau qui contiendra les variables que l on souhaite importer
		$lineErrors = array();//tab qui contiendra les num de ligne du fichier dimport ou un probleme a ete rencontre
		$messageErrors = array();//tab contenant les messages d erreur genere lors de l import
		//$array_Line_Message_Data = array();

		foreach ($worksheetdata->getRowIterator() as $row) { //parcours de chaque ligne de la feuille
			$cellIterator = $row->getCellIterator(); //recupere un iterateur de ttes les cellules de la ligne actuelle
			$cellIterator->setIterateOnlyExistingCells(FALSE); //Permet à itérateur de pas occulter et prendre en compte les cellules vides

			foreach ($cellIterator as $cell) { //parcours de chaque cellule de la ligne
				$cell_column = $cell->getColumn();
				$cell_row = $cell->getRow();
				$data_value = xss_clean($cell->getFormattedValue()); //recupere la valeur sans formatage de cellule appliquee

				if ($cell_row == 1) { //  si en-tete du fichier
					// Vérification du header, si nom colonne present dans la base
					$field = $this->format_field($data_value, $checking_header);

					if ($field) { //si la colonne du fichier d'import appartient à la base
						$header[$cell_column] = $field; //ajout du nom du champ dans le tableau header indexe par les num de col dans la feuille excel
					} elseif ($data_value) {
						array_push($messageErrors, "La colonne ".$cell_column." de la feuille ".$worksheetname.", n'existe pas dans la base");
					}
				} else {
					// Vérification des valeurs
					if (isset($header[$cell_column]) && $data_value) {  // Si la colonne de la donnée fait réference à un champ défini dans le header
						switch ($worksheetname) { //test sur le nom de la feuille
							case 'Variable':
								switch($header[$cell_column]) { //test sur le nom de colonne
									case 'variable_code':
										if ($this->Variable_model->find(array('variable_code' => $data_value))) {
											array_push($messageErrors, "Variable_code, ligne ".$cell_row." feuille ".$worksheetname.", existe déjà dans la base");
											array_push($lineErrors, $cell_row);
											$import_data[$cell_row][$header[$cell_column]] = NULL;
										}
										else $import_data[$cell_row][$header[$cell_column]] = $data_value;
										break;
									case 'trait_code':
										if (!$this->Trait_model->find(array('trait_code' => $data_value))) {
											array_push($messageErrors, "Trait_code, ligne ".$cell_row." feuille ".$worksheetname.", n'existe pas dans la base");
											array_push($lineErrors, $cell_row);
											$import_data[$cell_row][$header[$cell_column]] = NULL;
										}
										else $import_data[$cell_row][$header[$cell_column]] = $data_value;
										break;
									case 'method_code':
										if (!$this->Method_model->find(array('method_code' => $data_value))) {
											array_push($messageErrors, "method_code, ligne ".$cell_row." feuille ".$worksheetname.", n'existe pas dans la base");
											array_push($lineErrors, $cell_row);
											$import_data[$cell_row][$header[$cell_column]] = NULL;
										}
										else $import_data[$cell_row][$header[$cell_column]] = $data_value;
										break;
									case 'scale_code':
										if (!$this->Scale_model->find(array('scale_code' => $data_value))) {
											array_push($messageErrors, "scale_code, ligne ".$cell_row." feuille ".$worksheetname.", n'existe pas dans la base");
											array_push($lineErrors, $cell_row);
											$import_data[$cell_row][$header[$cell_column]] = NULL;
										}
										else $import_data[$cell_row][$header[$cell_column]] = $data_value;
										break;
								}
								break;
							case 'Entity':
								if ($header[$cell_column] == 'entity_code'){
									if ($this->Entity_model->find(array('entity_code' => $data_value))){
										array_push($messageErrors, "entity_code, ligne ".$cell_row." feuille ".$worksheetname.", existe déjà dans la base");
										array_push($lineErrors, $cell_row);
										$import_data[$cell_row][$cell_column] = NULL;
									}
									else $import_data[$cell_row][$header[$cell_column]] = $data_value;
								}
								break;
							case 'Target':
								if ($header[$cell_column] == 'target_name') {
									if ($this->Target_model->find(array('target_name' => $data_value))){
										array_push($messageErrors, "target_name, ligne ".$cell_row." feuille ".$worksheetname.", existe déjà dans la base");
										array_push($lineErrors, $cell_row);
										$import_data[$cell_row][$cell_column] = NULL;
									}
									else $import_data[$cell_row][$header[$cell_column]] = $data_value;
								}
								break;
							case 'Trait':
								switch($header[$cell_column]) {
									case 'trait_code':
										if ($this->Trait_model->find(array('trait_code' => $data_value))){
											array_push($messageErrors, "trait_code, ligne ".$cell_row." feuille ".$worksheetname.", existe déjà dans la base");
											array_push($lineErrors, $cell_row);
											$import_data[$cell_row][$cell_column] = NULL;
										}
										else $import_data[$cell_row][$header[$cell_column]] = $data_value;
										break;

									case 'trait_entity_code':
										if (!$this->Entity_model->find(array('entity_code' => $data_value))){
											array_push($messageErrors, "entity_code, ligne ".$cell_row." feuille ".$worksheetname.", n'existe pas dans la base");
											array_push($lineErrors, $cell_row);
											$import_data[$cell_row][$cell_column] = NULL;
										}
										else $import_data[$cell_row][$header[$cell_column]] = $data_value;
										break;

									case 'trait_target_name':
										if (!$this->Target_model->find(array('target_name' => $data_value))){
											array_push($messageErrors, "target_name, ligne ".$cell_row." feuille ".$worksheetname.", n'existe pas dans la base");
											array_push($lineErrors, $cell_row);
											$import_data[$cell_row][$cell_column] = NULL;
										}
										else $import_data[$cell_row][$header[$cell_column]] = $data_value;
										break;
								}
								break;
							case 'Method':
								if ($header[$cell_column] == 'method_code') {
									if ($this->Method_model->find(array('method_code' => $data_value))){
										array_push($messageErrors, "method_code, ligne ".$cell_row." feuille ".$worksheetname.", existe déjà dans la base");
										array_push($lineErrors, $cell_row);
										$import_data[$cell_row][$cell_column] = NULL;
									}
									else $import_data[$cell_row][$header[$cell_column]] = $data_value;
								}
								break;
							case 'Scale':
								if ($header[$cell_column] == 'scale_code') {
									if ($this->Scale_model->find(array('scale_code' => $data_value))){
										array_push($messageErrors, "scale_code, ligne ".$cell_row." feuille ".$worksheetname.", existe déjà dans la base");
										array_push($lineErrors, $cell_row);
										$import_data[$cell_row][$cell_column] = NULL;
									}
									else $import_data[$cell_row][$header[$cell_column]] = $data_value;
								}
								break;
						}
					} elseif (isset($header[$cell_column])) {
						switch ($worksheetname) {
							case 'Variable':
								switch($header[$cell_column]) {
									case 'variable_code':
										array_push($messageErrors, "variable_code, ligne ".$cell_row." feuille ".$worksheetname.", absent");
										array_push($lineErrors, $cell_row);
										break;
									case 'trait_code':
										array_push($messageErrors, "trait_code, ligne ".$cell_row." feuille ".$worksheetname.", absent");
										array_push($lineErrors, $cell_row);
										break;
									case 'method_code':
										array_push($messageErrors, "method_code, ligne ".$cell_row." feuille ".$worksheetname.", absent");
										array_push($lineErrors, $cell_row);
										break;
									case 'scale_code':
										array_push($messageErrors, "scale_code, ligne ".$cell_row." feuille ".$worksheetname.", absent");
										array_push($lineErrors, $cell_row);
										break;
								}
								break;
							case 'Entity':
								if ($header[$cell_column] == 'entity_code'){
									array_push($messageErrors, "entity_code, ligne ".$cell_row." feuille ".$worksheetname.", absent");
									array_push($lineErrors, $cell_row);
								}
								break;
							case 'Target':
								if ($header[$cell_column] == 'target_name') {
									array_push($messageErrors, "target_name, ligne ".$cell_row." feuille ".$worksheetname.", absent");
									array_push($lineErrors, $cell_row);
								}
								break;
							case 'Trait':
								switch($header[$cell_column]) {
									case 'trait_code':
										array_push($messageErrors, "trait_code, ligne ".$cell_row." feuille ".$worksheetname.", absent");
										array_push($lineErrors, $cell_row);
										break;

									case 'trait_entity_code':
										array_push($messageErrors, "entity_code, ligne ".$cell_row." feuille ".$worksheetname.", absent");
										array_push($lineErrors, $cell_row);
										break;

									case 'trait_target_name':
										array_push($messageErrors, "target_name, ligne ".$cell_row." feuille ".$worksheetname.", absent");
										array_push($lineErrors, $cell_row);
										break;
								}
								break;
							case 'Method':
								if ($header[$cell_column] == 'method_code') {
									array_push($messageErrors, "method_code, ligne ".$cell_row." feuille ".$worksheetname.", absent");
									array_push($lineErrors, $cell_row);
								}
								break;
							case 'Scale':
								if ($header[$cell_column] == 'scale_code') {
									array_push($messageErrors, "scale_code, ligne ".$cell_row." feuille ".$worksheetname.", absent");
									array_push($lineErrors, $cell_row);
								}
								break;
						}
						$import_data[$cell_row][$header[$cell_column]] = NULL;
					}
				}
			}
		}

		$array_Line_Message_Data['lignesErrors'] = $lineErrors;
		$array_Line_Message_Data['messageErrors'] = $messageErrors;
		$array_Line_Message_Data['import_data'] = $import_data;

		return $array_Line_Message_Data;

	}

}
