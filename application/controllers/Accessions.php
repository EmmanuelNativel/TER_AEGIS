<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accessions extends MY_Controller //heritage controller code ignit
{

    public function __construct() //constructeur de la classe
    {
        parent::__construct(); //appel du constructeur parent
        if (!$this->session->userdata('connected')) { //utilisation de la bibliot session pour savoir si utilisateur connecté ?
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page. <a href=\"" . site_url('welcome/login') . "\">Connexion</a>", 401);
            return;
        }

        $this->load->helper(array('form')); //chargement des differents helpers et biblio
        $this->load->library('form_validation');
        $this->load->helper('date');
		//balise html entourant les messages d erreurs
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
        $this->load->model('Accession_model'); //chargement du model accession

    }

    /**
     * Lien de téléchargement du formulaire d'importation des accessions
     */
    public function download_form()
    {
        $this->load->helper('download');//chargement du helper download
        $file_path = 'download_forms/Import_accession.xlsx';
        force_download($file_path, NULL); /* genere des en-tetes pour le serveur qui force les donnees a etre
											telecharger sur le bureau, le premier param est le chemin du fichier a telecharger
											le deuxieme param est null le contenu du fichier sera lu
											*/
	}
    /**
     * Appel de la vue import
     */
    public function main_page(){

        $page['title'] = "Importation des accessions";
        $page['subtitle'] = "Formulaire d'importation des accessions";

        $scripts = array('bootstrap-select-ajax-plugin');
        $this->view('accession/import', $page['title'], $page['subtitle'], $scripts);
    }


    /**
     * Appel de la fonction importation fichier
     */

    public function import()
    {
      $page['title'] = "Importation des accessions";
      $page['subtitle'] = "Formulaire d'importation des accessions";

      $config['upload_path'] = UPLOAD_PATH;//le dossier où les exeles seront chargés
      $config['allowed_types'] = 'xls|xlsx';
      $config['encrypt_name'] = true;

      $this->load->library('excel'); //charg. biblio excel
      $this->load->library('table'); //charg. biblio genere auto. tableau html a partir de tableau ou un ensemble de results de BDD
      $this->load->library('upload', $config); //charg. biblio upload avec passage d'option

      //if not successful, set the error message
      if (!$this->upload->do_upload('access_file')) {
          $data['error'] = $this->upload->display_errors('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
          $scripts = array('bootstrap-select-ajax-plugin');
          $this->view('accession/import', $page['title'], $page['subtitle'], $data, $scripts);
      } else {
          try {
              $data = array();
              $upload_data = $this->upload->data();//return un tableau contenant ttes les infos relatives au fichier uploade

              PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP); /*defini l encodage zip à utiliser PCLZip ou ZipArchive
                                                                          necessaire pour utiliser excel 2007 ou fichier xlsx	*/
              $objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']); //chargement du fichier ds l objet phpexcel

              //recupere la feuille active du classeur et la converti en tableau php afin de faciliter la manip des données
              $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

              //setActiveSheetIndex = fixe l index de feuille active a 0 ainsi le classeur sera ouvert sur la premiere feuille
              $nbreDeLignesAccession = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

              $nbreDeLignesAccessionUse = $objPHPExcel->setActiveSheetIndex(1)->getHighestRow();

              if ($nbreDeLignesAccession > 1) {
                  // récupère tous les noms de feuilles du fichier
                  $worksheetNames = $objPHPExcel->getSheetNames($upload_data['full_path']);
                  $return = array();

                  foreach ($worksheetNames as $key => $sheetName) { //parcours de chaque feuille
                      //set the current active worksheet by name
                      $objPHPExcel->setActiveSheetIndexByName($sheetName);
                      // crée un tableau assoc avec le nom de la feuille en tant que clé et le tableau des contenus de la feuille comme valeur
                      $return[$sheetName] = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                  }

                  $feuilleAccession = $return['Accession'];
                  $feuilleAccession = array_values($feuilleAccession); //transforme les indexs correspondant au lettre des colonnes en nombre

                  $resultInsertionAccession = $this->Accession_model->addAccessions($feuilleAccession, $nbreDeLignesAccession); //,$nbreDeColonnesAccession

                  if ($nbreDeLignesAccessionUse > 1) {
                      $feuilleAccesionUse = $return['Accession_use'];
                      $feuilleAccesionUse = array_values($feuilleAccesionUse);

                      $resultInsertionAccessionUse = $this->Accession_model->addAccession_Use($feuilleAccesionUse, $nbreDeLignesAccessionUse);
                  }


                  if (count($resultInsertionAccession[1]) > 0) { // if errors
                      if ($nbreDeLignesAccessionUse > 1) {
                          if (count($resultInsertionAccessionUse[1]) > 0) { // if errors
                              $resultInsertionAccession[1] = array_merge($resultInsertionAccession[1], $resultInsertionAccessionUse[1]);
                          }
                      }
                      $data['lignesErrors'] = $resultInsertionAccession[1];
                  }

                  if (count($resultInsertionAccession[2]) > 0) {
                      if ($nbreDeLignesAccessionUse > 1) {
                          if (count($resultInsertionAccessionUse[2]) > 0) { // if errors
                              $resultInsertionAccession[2] = array_merge($resultInsertionAccession[2], $resultInsertionAccessionUse[2]);
                          }
                      }
                      $data['messageErrors'] = $resultInsertionAccession[2];
                  }
                  $scripts = array('bootstrap-select-ajax-plugin');
                  unlink($upload_data['full_path']); //remove file
                  $this->view('accession/import_success', $page['title'], $page['subtitle'], $data, $scripts);
              } else {
                  $scripts = array('bootstrap-select-ajax-plugin');
                  unlink($upload_data['full_path']); // remove file
                  $this->view('accession/import', $page['title'], $page['subtitle'], $data, $scripts);
              }
          } catch (Exception $e) {
              unlink($upload_data['full_path']); // remove file
              show_error($e->getmessage());
          }
      }
    }

    /**
     * Affiche la liste des accessions disponible pour l'utilisateur
     */
    public function index($offset = 0)
    {
        // Titre de la page
        $page['title'] = 'Liste des accessions';
        $page['subtitle'] = 'Liste des accessions enregistrées dans DAPHNE';

        $data['limit'] = $this->input->get('limit'); // Nombre d'élements à afficher
        $data['offset'] = $offset; // Numero de l'élement à partir duquel l'affichage commence
        $data['order_field'] = $this->input->get('order_field'); // Champ par lequel ranger le tableau
        $data['order_direction'] = $this->input->get('order_direction'); // Direction par laquel ranger le tableau

        if (!$data['limit']) $data['limit'] = 10; //si pas de limite alors on fixe a 10
        $data['total_rows'] = $this->Accession_model->count();


        $this->form_validation->set_data($data);

		//fixe les regles de validation du formulaire
        $this->form_validation->set_rules('limit', 'limit', 'trim|is_natural_no_zero|less_than_equal_to[1000]|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|is_natural|less_than[' . $data['total_rows'] . ']|xss_clean');
        $this->form_validation->set_rules('order_field', 'order_field', 'trim|alpha_dash|in_list[' . implode(",", $this->Accession_model->fields()) . ']|xss_clean');
        $this->form_validation->set_rules('order_direction', 'order_direction', 'trim|alpha|in_list[ASC,DESC,RANDOM]|xss_clean');

        if ($this->form_validation->run()) { //si formulaire valide
            $this->load->library('table');
            $this->load->library('pagination');

            $config['base_url'] = site_url('accessions/index'); //fixe les parametres de la pagination
            $config['total_rows'] = $data['total_rows'];
            $config['per_page'] = $data['limit'];
            $this->pagination->initialize($config); //defini le type de pagination

            $data['accessions'] = $this->Accession_model->find(array(), $data['limit'], $data['offset'], $data['order_field'], $data['order_direction']
            );
            $this->view('accession/accessions', $page['title'], $page['subtitle'], $data);
        } else {
            show_error(validation_errors());
        }

    }

    /**
     * Affiche le résumé d'une accession
     */
    public function display($accession_code = Null)
    {
        $this->form_validation->set_data(array('accession_code' => $accession_code));
        $this->form_validation->set_rules('accession_code', 'Code accession',
            array(
                'trim', 'required','alpha_dash', 'max_length[255]', 'xss_clean',
                array('accession_exist_callable',
                    array($this->Accession_model, 'exist'))
            )
        );
        $this->form_validation->set_message('accession_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $this->load->library('table');
            $data = $this->Accession_model->find(array('accession_code' => $accession_code))[0];


            // Titre de la page
            $page['title'] = 'Accession ' . $data['accession_code'];
            $page['subtitle'] = 'Code : ' . $data['accession_code'];

            $this->view('accession/display_accession', $page['title'], $page['subtitle'], $data, array('bootstrap-treeview.min', 'bootstrap-select-ajax-plugin'));
        } else {
            show_error(validation_errors());
        }
    }


    public function create()
    {

        //$this->load->model(array('Partner_model', 'Country_model', 'Taxo_model')); //multi chargement des modeles

        // Titre de la page
        $page['title'] = 'Création de nouvelle accession';
        $page['subtitle'] = 'Formulaire de saisie de création d\'une accession';

        // Récupération des données du formulaire
        $data['taxo_name'] = $this->input->post('taxo_name');
        $data['accession_code'] = $this->input->post('accession_code');


        // Règles de validation du formulaire
        $this->form_validation->set_rules('accession_code', 'Code accession', 'required|trim|min_length[2]|max_length[50]|alpha_dash|is_unique[accession.accession_code]|xss_clean');
        $this->form_validation->set_rules('taxo_name', 'Taxo', 'required|trim|min_length[1]|max_length[255]|xss_clean');



        // Test de validation du formulaire
        if ($this->form_validation->run()) {
            $this->load->helper('postgre'); //charg. du helper postgre
            $data = nullify_array($data); // Remplace les chaines de caractères vide par la valeur NULL
      			if ($this->Accession_model->exist($data['accession_code'])) {
      				$data['msg'] = "Une accession avec ce code existe déjà";
      				$this->view('error', $page['title'], $page['subtitle'], $data);
      			}
      			else {
      				if ($this->Accession_model->create(
      					array(
      						'accession_code' => $data['accession_code'],
      						'taxo_id' => $data['taxo_name']
      					)))
      				{
      					$data['msg'] = "L'accession <strong>" . $data['accession_code'] . "</strong> a été créée avec succés!";
      					$this->view('success', $page['title'], $page['subtitle'], $data);
      				}
      				else {
      					$data['msg'] = "Erreur lors de la création des données d'Accessions";
      					$this->view('error', $page['title'], $page['subtitle'], $data);
      				}
      			}
        } else {
            // Affichage du formulaire de création d'accession et retour des erreurs
            $scripts = array('bootstrap-datepicker.min', 'bootstrap-datepicker.fr.min', 'bootstrap-select-ajax-plugin');
            $this->view('accession/new_accession', $page['title'], $page['subtitle'], $data, $scripts);
        }
    }

    /**
     * Page de mise à jour d'une accession
     */
    public function update($accession_code=null)
    {
        $this->load->model(array('Partner_model', 'Country_model', 'Taxo_model'));
        //$this->load->model(array('Accession_model')); //interet car deja present ds constructeur?
        try {
            $accession_code = xss_clean($accession_code); // Sécurise la valeur de la variable
            $query_result = $this->Accession_model->find(array('accession_code' => $accession_code));
            $data = $query_result[0];

        } catch (Exception $e) {
            show_error($e->getMessage());
        }

        // Régle de validation du formulaire
        //$this->form_validation->set_rules('accession_code', 'Code accession', 'required|trim|min_length[2]|max_length[255]|alpha_dash|is_unique[accession.accession_code]|xss_clean');
        $this->form_validation->set_rules('seed_production_site', 'Code site', 'trim|min_length[2]|max_length[255]|xss_clean');
        //$this->form_validation->set_rules('taxo_code', 'Code taxo', 'trim|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('seed_production_date', 'Date de production', 'trim|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');
        $this->form_validation->set_rules('partner', 'Institut producteur', 'alpha_dash|trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('accession_type', 'Type accession', 'trim|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('accession_mother', 'accession mère', 'trim|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('accession_father', 'accession père', 'trim|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('genetic_pool', 'Pool génique', 'trim|min_length[1]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('donor_code', 'Code donateur', 'trim|xss_clean');
        $this->form_validation->set_rules('country', 'Pays d\'origine', 'trim|xss_clean');
        $this->form_validation->set_rules('ecotype', 'Ecotype', 'trim|xss_clean');
        $this->form_validation->set_rules('agrosystem', 'Agrosystème', 'trim|xss_clean');
        $this->form_validation->set_rules('vernacular_name', 'Nom vernaculaire', 'trim|xss_clean');
        $this->form_validation->set_rules('biological_status', 'Statut biologique', 'trim|xss_clean');
        $this->form_validation->set_rules('lat_origin', 'Latitude', 'trim|xss_clean');
        $this->form_validation->set_rules('long_origin', 'Longitude', 'trim|xss_clean');
        $this->form_validation->set_rules('region', 'Région d\'origine', 'trim|xss_clean');
        $this->form_validation->set_rules('city', 'Ville d\'origine', 'trim|xss_clean');



        if ($this->form_validation->run()) {

            $this->Accession_model->update(
                array('accession_code' => $accession_code),
                array(
                    //'taxo_id' => $this->input->post('taxo_code'),
                    'seed_production_site' => $this->input->post('site_code'),
                    'seed_production_date'=> $this->input->post('seed_production_date'),
                    'seed_institut_producer' => $this->input->post('partner'),
                    'accession_type' =>$this->input->post('accession_type'),
                    'accession_mother'=>$this->input->post('accession_mother'),
                    'accession_father'=> $this->input->post('accession_father'),
                    'genetic_pool' => $this->input->post('genetic_pool'),
                    'donor_code' =>$this->input->post('donor_code'),
                    'seed_origin_country'=> $this->input->post('country'),
                    'ecotype' => $this->input->post('ecotype'),
                    'agrosystem' => $this->input->post ('agrosystem'),
                    'vernacular_name' => $this->input->post('vernacular_name'),
                    'biological_status' => $this->input->post('biological_status'),
                    'lat_origin' => $this->input->post('lat_origin'),
                    'long_origin' => $this->input->post('long_origin'),
                    'region' => $this->input->post('region'),
                    'city' => $this->input->post('city')
                )
            );
            redirect('accessions/display/'.$accession_code);

        } else {
            $page['title'] = 'Accession '.$accession_code;
            $page['subtitle'] = 'Formulaire de modification d\'une accession '.$accession_code;
            $this->view('accession/update_accession', $page['title'], $page['subtitle'], $data);
        }
    }
        /**
         * Page de suppression d'une accession
         */
        public function delete()
    {
        $this->form_validation->set_rules('accession_code', 'Code accession', array('trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
            array('accession_exist_callable', array($this->Accession_model, 'exist'))));
        $this->form_validation->set_message('accession_exist_callable', 'Le {field} n\'éxiste pas.');


        if ($this->form_validation->run()) {

            $accession_code = $this->input->post('accession_code');
            $this->Accession_model->find(array('accession_code' => $accession_code))[0];
            //$this->load->model(array('Accession_model'));

            // Vérifie si l'utilisateur est admin
            if (!$this->session->userdata('admin')) {
                set_status_header(401);
                show_error("vous n'êtes pas autorisé à lire cette page.", 401);
                return;
            }

            $this->Accession_model->delete(array('accession_code' => $this->input->post('accession_code')));
            redirect('accessions/index/');
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Affiche les accessions recherchées sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[50]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $results = $this->Accession_model->like($searched_term, 'accession_code');


            $accessions = array_map(function ($results) {
                return array(
                    'name' => $results['accession_code'],
                    'value' => $results['accession_id']
                );
            }, $results);


            echo json_encode($accessions);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }


}
