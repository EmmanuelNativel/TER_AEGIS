<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trials extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page. <a href='".site_url('welcome/login')."'>Connexion</a>", 401);
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-info col-lg-3"><span class="glyphicon glyphicon-exclamation-sign"></span>', '</div>');
        $this->load->helper('date');
        $this->load->model(array('Trial_model','TrialProject_model','Obs_unit_model'));

        $this->load->library('user_agent');
    }

    /**
     * Affiche la liste des essais d'un projet
     */
    function index($offset = 0)
    {
        // Titre de la page
        $page['title'] = 'Essais agronomiques';
        $page['subtitle'] = 'Liste des essais répertoriés dans DAPHNE';

        $data['limit'] = $this->input->get('limit'); // Nombre d'élements à afficher
        $data['offset'] = $offset; // Numero de l'élement à partir duquel l'affichage commence
        $data['order_field'] = $this->input->get('order_field'); // Champ par lequel ranger le tableau
        $data['order_direction'] = $this->input->get('order_direction'); // Direction par laquel ranger le tableau

        if (!$data['limit']) $data['limit'] = 10;
        $data['total_rows'] = $this->Trial_model->count();//tous les essais

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('limit', 'limit', 'trim|is_natural_no_zero|less_than_equal_to[1000]|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|is_natural|less_than[' . $data['total_rows'] . ']|xss_clean');
        $this->form_validation->set_rules('order_field', 'order_field', 'trim|alpha_dash|in_list[' . implode(",", $this->Trial_model->fields()) . ']|xss_clean');
        $this->form_validation->set_rules('order_direction', 'order_direction', 'trim|alpha|in_list[ASC,DESC,RANDOM]|xss_clean');

        if ($this->form_validation->run()) {
            $this->load->library('table');
            $this->load->library('pagination');

            $config['base_url'] = site_url('trials/index');
            $config['total_rows'] = $data['total_rows'];
            $config['per_page'] = $data['limit'];
            $this->pagination->initialize($config);

            $data['trials'] = $this->Trial_model->find(array(), $data['limit'], $data['offset'], $data['order_field'], $data['order_direction']);
            $this->view('trial/trials', $page['title'], $page['subtitle'], $data);
        } else {
            show_error(validation_errors());
        }
    }
    /**
     * Affiche le résumé d'un essai --> Permet de récupérer les l'arbre (blocs et parcelles associées)
     */
    public function display($trial_code=null)
    {
        $this->form_validation->set_data(array('trial_code' => $trial_code));
        $this->form_validation->set_rules('trial_code', 'Essai',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('trial_exist_callable',
                    array($this->Trial_model, 'exist'))
            )
        );
        $this->form_validation->set_message('trial_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $this->load->library('table');
            $this->load->model('DupRules_model');
            $data = $this->Trial_model->find(array('trial_code' => $trial_code))[0];
            $data['is_leader'] = ($this->DupRules_model->find(array('username' => $this->session->userdata('username'), 'is_leader' => PGSQL_TRUE)))? TRUE : FALSE;

            // Titre de la page
            $page['title'] = 'Essai ' . $data['trial_code'];
            $page['subtitle'] = 'Propriétés d\'un essai' . $data['trial_code']/* . ' du projet ' . $data['project_code']*/;

            $data['previousUrl'] = $this->agent->referrer();
            $data['project_code'] = $this->input->get('project_code');

            //Récupération des données pour le dispositif expérimental
            $expData = $this->Trial_model->get_trial_experimental_data($trial_code);


            // ---------------
            // Génération des données qui seront affichées dans le datatable Dispositif Expérimental
            // ---------------
            // (Restructuration afin de faciliter l'affichage)

            $tableHeaders = array('unit_code' => 'Unité exp.', 'level_label' => 'Niveau', 'parent_label' => 'Niveau parent');
            $tableRows = array();
            $factorsLevelsDescription = array();
            foreach ($expData as $key => $dbEntry) {
              $exp_unit_id = $dbEntry['exp_unit_id'];
              $unit_code = $dbEntry['unit_code'];
              $level_label = $dbEntry['level_label'];
              $parent_unit_code = $dbEntry['parent_unit_code'];
              $parent_level_label = $dbEntry['parent_level_label'];
              $factor_name = $dbEntry['factor'];
              $factor_level =  $dbEntry['factor_level'];
              $factor_level_description =  $dbEntry['factor_level_description'];

              //=== Remplissage du tableau de descriptions pour les facteurs levels
              if ( ! array_key_exists($factor_level, $factorsLevelsDescription))
                  $factorsLevelsDescription[$factor_level] = $factor_level_description;

              //=== Remplissage tableHeader
              if( ! in_array($factor_name,$tableHeaders))
                  $tableHeaders[$factor_name] = $factor_name;

              //=== Remplissage tableRows
              //On ajoute les valeurs dans le bon tableau s'il existe.
              //Sinon on créé un nouveau tableau à chaque fois.
              //L'utilisation des tableaux à chaque fois permet dans le html
              //d'afficher 0, 1 ou n valeurs sans vérifications supplémentaires.
              if (array_key_exists($unit_code, $tableRows)) {
                if (array_key_exists($factor_name, $tableRows[$unit_code])) {
                  array_push($tableRows[$unit_code][$factor_name], $factor_level);
                } else {
                  $tableRows[$unit_code][$factor_name] = array($factor_level);
                }
              } else {
                $tableRows[$unit_code] = array(
                  "unit_code" => array($unit_code),
                  "level_label" => array($level_label),
                  "parent_label" => array($parent_level_label." ".$parent_unit_code),
                   $factor_name => array($factor_level)
                );
              }
            }

            // Passage de 3 tableaux au html pour le dispositif expérimental :
            // les titres des colonnes ($tableHaders) ,
            // le contenu de chaque ligne ($tableRows),
            // et les descriptions qui serviront à afficher des tooltips ($factorsLevelsDescription).
            $data['dispExp_tableHeaders'] = $tableHeaders;
            $data['dispExp_tableRows'] = $tableRows;
            $data['dispExp_FLDescription'] = $factorsLevelsDescription;


            $scripts = array('jquery.dataTables', 'dataTables.bootstrap', 'bootstrap-select-ajax-plugin', 'display_trial/display_trial_script');
            $stylesheets = array('display_trial_style');

            $this->view('trial/display_trial', $page['title'], $page['subtitle'], $data, $scripts, $stylesheets);

        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Fonction appelée en AJAX qui permet de récupérer la vue HTML d'un dataviz
     * (les paramètres sont passés via HTTP POST)
     */
    public function ajaxLoadDataviz()
    {
        $ajaxData = array(); //return array
        $trial_code = $this->input->post('trial_code');
        $datavizCode = $this->input->post('datavizCode');
        $datavizTitle = $this->input->post('datavizTitle');

        $viewData = array("datavizTitle" => $datavizTitle);

        //$ajaxData['datavizHtml'] = $this->load->view('trial/dataviz/'.$datavizCode, $viewData, TRUE);

        switch ($datavizCode) {

            case 'expUnitGraph':
                // Données pour les selectPicker
                $viewData['all_exp_unit'] = $this->Trial_model->get_trial_experimental_data(
                    $trial_code,
                    $selectedFields=array("e.exp_unit_id",  "e.unit_code")
                );
                $viewData['all_variablesName'] = array_column($this->Obs_unit_model->get_trial_var($trial_code), 'obs_variable');
                $ajaxData['datavizHtml'] = $this->load->view('trial/dataviz/expUnitGraph', $viewData, TRUE);
                break;

            case 'expUnit2D':
                // Données pour les selectPicker
                $viewData['all_exp_unit'] = $this->Trial_model->get_trial_experimental_data(
                    $trial_code,
                    $selectedFields=array("e.exp_unit_id",  "e.unit_code")
                );
                $viewData['all_variablesName'] = array_column($this->Obs_unit_model->get_trial_var($trial_code), 'obs_variable');
                $ajaxData['datavizHtml'] = $this->load->view('trial/dataviz/expUnit2D', $viewData, TRUE);
                break;
            
            case 'animatedMap':
                // Données pour les selectPicker
                $viewData['all_exp_unit'] = $this->Trial_model->get_trial_experimental_data(
                    $trial_code,
                    $selectedFields=array("e.exp_unit_id",  "e.unit_code")
                );
                $viewData['all_variablesName'] = array_column($this->Obs_unit_model->get_trial_var($trial_code), 'obs_variable');
                $ajaxData['datavizHtml'] = $this->load->view('trial/dataviz/animatedMap', $viewData, TRUE);
                break;

            default: return null;
        }

        echo json_encode($ajaxData);
    }

    /**
     * Fonction appelée dans la vue Trial (section Data Visualization) pour récupérer les données d'observations
     * associées à plusieurs unités expérimentales et plusieurs variables dans le but de réaliser des graphiques
     */
    public function ajaxLoadExpUnitData() {
      $ajaxData = array(); //tableau qui sera retourné
      $selectedUnitExp =  json_decode($this->input->post('selectedUnitExp'));
      $selectedVariables = json_decode($this->input->post('selectedVariables'));
      $ajaxData['exp_unit_data'] = $this->Trial_model->get_exp_unit_data($selectedVariables, $selectedUnitExp);
      echo json_encode($ajaxData);
    }

    /**
    * Fonction appelée lors de la data visualization pour récupérer des données sur le dispositif expérimental
    * d'un essai (infos sur les facteurs, modalités etc.)
    */
    public function ajaxLoadDispExp($trial_code) {
      $ajaxData = array(); //tableau qui sera retourné
      //Récupération des données pour le dispositif expérimental
      $ajaxData['dispExp'] = $this->Trial_model->get_trial_experimental_data($trial_code);
      echo json_encode($ajaxData);
    }



    /** (Pour le datatable Observations)
     * Fonction appelée dans la vue Trial pour récupérer les données d'observations d'un essai
     * (appelée à chaque draw. i.e search, order, change page etc... du datatable Observations)
     */
    public function ajaxLoadDatatableObservations($trial_code) {
      $ajaxData = array(); //tableau qui sera retourné
      //Input post parameters
      $draw = $this->input->post('draw');
      $start =  $this->input->post('start');
      $length = $this->input->post('length');
      $searched_term = $this->input->post('search')['value'];
      $order_array = $this->input->post('order');
      $columns_array = $this->input->post('columns');
      $fixedColumnsName = array_column($this->input->post('fixedColumns'), 'data');

      //Génération du tableau des orderBy. (tableau contenant toutes les informations
      // concernant les colonnes à ordonner et dans quel sens (ASC/DESC))
      //Ce tableau sera passé au model pour faire la requête en ordonnant les colonnes.
      $orderByArray = array();
      foreach ($order_array as $key => $order) {
          $colIndice = $order['column'];
          $direction = strtoupper($order['dir']);
          $columnName = $columns_array[$colIndice]['data'];
          //Si la colonne est une variable (non fixé au départ) alors elle fait partie
          // du champ json_variables de la table. Il faut donc ajuster le orderBy en conséquence.
          if(! in_array($columnName, $fixedColumnsName)) {
            $orderByArray["json_variables->'" . $columnName . "'"] = $direction;
          } else {
            $orderByArray[$columnName] = $direction;
          }
      }
      //Réalisation de la requête permettant de filtrer, ordonner et limiter les observations.
      //Tout ce travail est délégué à PostgreSQL (cela permettra de passer à l'echelle).
      $obsData = $this->Trial_model->get_trial_observations_data($trial_code, $length, $start, $searched_term, $orderByArray);
      $totalFiltered = $obsData['totalFiltered'];

      //Restructuration des élements retournés par la requête afin de respecter
      //les exigences du DataTable.
      $dataTableArray = array();
      foreach($obsData['result'] as $key => $obs) {
        $variablesValuesArray = json_decode($obs['json_variables'], true);
        //Suppression du champ json_variables
        unset($obs['json_variables']);
        //Ajout des variables au même niveau que les autres champs (on applati le tableau)
        array_push($dataTableArray, array_merge($obs, $variablesValuesArray));
      }

      //Retourne les valeurs requise par le datatable (voir documentation Datatable server-side processing)
      $ajaxData['draw'] = $draw;
      $ajaxData['recordsTotal'] = $this->Trial_model->count_trial_observations_data($trial_code);
      $ajaxData['recordsFiltered'] = $totalFiltered;
      $ajaxData['data'] = $dataTableArray;

      echo json_encode($ajaxData);
    }

    /**
     * Fonction ajax retournant la liste de toutes les variables mesurées dans un essai.
     */
    public function ajaxLoadTrialVariablesName($trial_code) {
      $ajaxData = array();
      //Chargement des variables utilisées dans l'essai
      $ajaxData['columns'] = array_column($this->Obs_unit_model->get_trial_var($trial_code), 'obs_variable');
      echo json_encode($ajaxData);
    }

    /**
     * Page de création d'un essai
     */
    public function create()
    {
        // Chargement des models
        //$this->load->model(array('Site_model', 'TrialProject_model'));
		$this->load->model('Site_model');

        // Titre de la page
        $page['title'] = 'Nouvel essai agronomique';
        $page['subtitle'] = 'Formulaire de saisie d\'un nouvel essais agronomiques';

        // Récupération des données du formulaire
        $data['trial_code'] = $this->input->post('trial_code');
        $data['site_code'] = $this->input->post('site_code');
        $data['trial_description'] = $this->input->post('description');
        $data['starting_date'] = $this->input->post('starting_date');
        $data['ending_date'] = $this->input->post('ending_date');
        $data['commentary'] = $this->input->post('commentary');
       // $data['project_code'] = $this->input->post('project_code');
        $data['controlled_environment'] = $this->input->post('checkbox_env');

        // Regles de validations
        $this->form_validation->set_rules('trial_code', 'Code de l\'essai', 'trim|required|alpha_dash|min_length[1]|max_length[50]|xss_clean');
        $this->form_validation->set_rules('site_code', 'Code du lieu',
            array(
                'trim', 'alpha_dash', 'max_length[50]', 'xss_clean',
                array('site_callable', array($this->Site_model, 'valid_site'))
            )
        );
        $this->form_validation->set_message('site_callable', 'Le {field} n\'éxiste pas.');
        //$this->form_validation->set_message('project_is_valid_callable', 'Le {field} est en attente de validation par un administrateur.');
        //$this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');
        $this->form_validation->set_rules('commentary', 'Commentaire', 'trim|max_length[2000]|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('starting_date', 'Date de début de l\'essai', 'trim|required|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');
        $this->form_validation->set_rules('ending_date', 'Date de fin de l\'essai', 'trim|required|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');
        $this->form_validation->set_rules('checkbox_env', 'Environnemnt controllé', 'trim|in_list[NULL,TRUE]|xss_clean');

        // Test de validation du formulaire
        if ($this->form_validation->run()) {
            $this->load->helper('postgre');
            $data = nullify_array($data); // Remplace les chaines de carctères vide par la valeur NULL
            if (!$data['controlled_environment']) {
                $data['controlled_environment'] = PGSQL_FALSE; // Remplace la valeur NULL de la checkbox par FALSE
            }
            $this->Trial_model->create($data);


            $data['msg'] = "L'essai agronomique <strong>" . $data['trial_code'] . "</strong> a été créé avec succés!";
            $this->view('success', $page['title'], $page['subtitle'], $data);

        } else {
            // Affichage du formulaire de création d'un essai et retour des erreurs
            $scripts = array('bootstrap-datepicker.min', 'bootstrap-datepicker.fr.min', 'bootstrap-select-ajax-plugin');
            $this->view('trial/new_trial', $page['title'], $page['subtitle'], $data, $scripts);
        }

    }


    /**
     * Page de mise à jour d'un essai
     */
    public function update($trial_code=null, $project_code=null)
    {
        //$this->load->model(array('ProjectUser_model'));
        try {
            $trial_code = xss_clean($trial_code); // Sécurise la valeur de la variable
            $query_result = $this->Trial_model->find(array('trial_code' => $trial_code));
            $data = $query_result[0];

        } catch (Exception $e) {
            show_error($e->getMessage());
        }

        // Régle de validation du formulaire
        $this->form_validation->set_rules('site_code', 'Localisation', 'trim|required|min_length[3]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('commentary', 'Commentaire', 'trim|max_length[2000]|xss_clean');
        $this->form_validation->set_rules('starting_date', 'Date de début de l\'essai', 'trim|required|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');
        $this->form_validation->set_rules('ending_date', 'Date de fin de l\'essai', 'trim|required|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');


        if ($this->form_validation->run()) {

            $this->Trial_model->update(
                array('trial_code' => $trial_code),
                array(
                    'site_code' => $this->input->post('site_code'),
                    'trial_description' => $this->input->post('description'),
                    'commentary' => $this->input->post('commentary'),
                    'starting_date' => $this->input->post('starting_date'),
                    'ending_date' => $this->input->post('ending_date')
                )
            );
            redirect('trials/display/'.$trial_code);

        } else {
            $page['title'] = 'Esaai '.$trial_code;
            $page['subtitle'] = 'Formulaire de modification d\'un essai '.$trial_code;
            $this->view('trial/update_trial', $page['title'], $page['subtitle'], $data);
        }
    }

    /**
     * Page de suppression d'un essai
     */
    public function delete()
    {
        $this->form_validation->set_rules('trial_code', 'Essai', array('trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
            array('trial_exist_callable', array($this->Trial_model, 'exist'))));
        $this->form_validation->set_message('trial_exist_callable', 'Le {field} n\'éxiste pas.');


        if ($this->form_validation->run()) {

            $trial_code = $this->input->post('trial_code');
            $project_code = $this->Trial_model->find(array('trial_code' => $trial_code))[0]['project_code'];
            $this->load->model(array('ProjectUser_model'));
            // Vérifie si l'utilisateur est admin ou gestionnaire de projet
            if (!$this->session->userdata('admin') && !$this->ProjectUser_model->find(array('login' => $this->session->userdata('username'), 'project_code' => $project_code, 'is_leader' => PGSQL_TRUE))) {
                set_status_header(401);
                show_error("vous n'êtes pas autorisé à lire cette page.", 401);
                return;
            }

            $this->Trial_model->delete(array('trial_code' => $this->input->post('trial_code')));
            redirect('trials/index/');
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Affiche les essais recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options($project_code = NULL)
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[50]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');

            if (!$project_code) {
                $results = $this->Trial_model->like($searched_term, 'trial_code');
            } else {
                $results = $this->TrialProject_model->find(array('project_code' => xss_clean($project_code)));
            }

            $trials = array_map(function ($results) {
                return array(
                    'name' => $results['trial_code'],
                    'value' => $results['trial_code']
                );
            }, $results);

            echo json_encode($trials);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

    public function get_trial_coords($trial_code){

        $page['title'] = 'LOREM';
        $page['subtitle'] = 'Lorem Ipsum';

        $this->load->model('Exp_unit_model');

        $result = $this->Exp_unit_model->get_trial_exp_unit_coords($trial_code);

        $expunit_coord = array();

        foreach ($result as $key) {
            switch ($key['num_level']) {
                case 1:
                    $expunit_coord[] = array(
                        'code' => $key['unit_code'],
                        'id' => floatval($key['exp_unit_id']),
                        'level' => floatval($key['num_level'])
                    );

                    break;
                case 2:
                    $expunit_coord[] = array(
                        'id' => floatval($key['exp_unit_id']),
                        'level' => floatval($key['num_level']),
                        'father' => $key['assigned_to'],
                        'x' => floatval($key['x_coord']),
                        'y' => floatval($key['y_coord'])
                    );
                    break;
                default:
                    $expunit_coord[] = array(
                        'id' => floatval($key['exp_unit_id']),
                        'level' => floatval($key['num_level']),
                        'father' => $key['assigned_to']
                    );
                    break;
            }
        }

        echo json_encode($expunit_coord);
    }

     public function get_trial_obs($trial_code){

        $page['title'] = 'LOREM';
        $page['subtitle'] = 'Lorem Ipsum';

        $this->load->model('Obs_unit_model');

        $obs_variable = $this->input->post('choosed_var');

        $result = $this->Obs_unit_model->get_trial_obs($trial_code, $obs_variable);

        $expunit_coord = array_map(function ($result) {
            return array(
                'id' => floatval($result['exp_unit_id']),
                'variable' => $result['obs_variable'],
                'value' => floatval($result['obs_value'])
            );
        },$result);

        echo json_encode($expunit_coord);
    }

    public function disposiplay($trial_code = '2016_WP4_Parents_G2MARS_BCNAM_Diaphen'){

        $page['title'] = 'DATAVIZ';
        $page['subtitle'] = 'Espace de datavisualisation';


        $this->load->model('Obs_unit_model');

        $result = $this->Obs_unit_model->get_trial_var($trial_code);
        $data['variables'] = $result;
        $scripts = array('data_visualisation_compute', 'data_visualisation_display');
        $this->view('trial/visualisation', $page['title'], $page['subtitle'], $data, $scripts);
    }

    /**
     * Récupère les différents blocs de l'essai ainsi que les parcelles qui y sont associées.
     */
    public function getHierarchyData($trial_code=null)
    {
        // Vérifie si le code de l'essai est valide
        $this->form_validation->set_data(array('trial_code' => $trial_code));
        $this->form_validation->set_rules('trial_code', 'Essai',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('trial_exist_callable',
                    array($this->Trial_model, 'exist'))
            )
        );
        $this->form_validation->set_message('trial_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $this->load->library('table');
            $this->load->model('DupRules_model');
            $data = $this->Trial_model->find(array('trial_code' => $trial_code))[0]; // récupère les infos de l'essai
            $data['is_leader'] = ($this->DupRules_model->find(array('username' => $this->session->userdata('username'), 'is_leader' => PGSQL_TRUE)))? TRUE : FALSE;

            // Titre de la page
            $page['title'] = 'Essai ' . $data['trial_code'];
            $page['subtitle'] = 'Propriétés d\'un essai' . $data['trial_code']/* . ' du projet ' . $data['project_code']*/;

            $data['previousUrl'] = $this->agent->referrer();
            $data['project_code'] = $this->input->get('project_code');

            //Récupération des données pour le dispositif expérimental *****
            $expData = $this->Trial_model->get_trial_experimental_data($trial_code);


            // ---------------
            // Génération des données qui seront affichées dans le datatable Dispositif Expérimental
            // ---------------
            // (Restructuration afin de faciliter l'affichage)

            $tableHeaders = array('unit_code' => 'Unité exp.', 'level_label' => 'Niveau', 'parent_label' => 'Niveau parent');
            $tableRows = array();
            $factorsLevelsDescription = array();
            foreach ($expData as $key => $dbEntry) {
              $exp_unit_id = $dbEntry['exp_unit_id'];
              $unit_code = $dbEntry['unit_code'];
              $level_label = $dbEntry['level_label'];
              $parent_unit_code = $dbEntry['parent_unit_code'];
              $parent_level_label = $dbEntry['parent_level_label'];
              $factor_name = $dbEntry['factor'];
              $factor_level =  $dbEntry['factor_level'];
              $factor_level_description =  $dbEntry['factor_level_description'];

              //=== Remplissage du tableau de descriptions pour les facteurs levels
              if ( ! array_key_exists($factor_level, $factorsLevelsDescription))
                  $factorsLevelsDescription[$factor_level] = $factor_level_description;

              //=== Remplissage tableHeader
              if( ! in_array($factor_name,$tableHeaders))
                  $tableHeaders[$factor_name] = $factor_name;

              //=== Remplissage tableRows
              //On ajoute les valeurs dans le bon tableau s'il existe.
              //Sinon on créé un nouveau tableau à chaque fois.
              //L'utilisation des tableaux à chaque fois permet dans le html
              //d'afficher 0, 1 ou n valeurs sans vérifications supplémentaires.
              if (array_key_exists($unit_code, $tableRows)) {
                if (array_key_exists($factor_name, $tableRows[$unit_code])) {
                  array_push($tableRows[$unit_code][$factor_name], $factor_level);
                } else {
                  $tableRows[$unit_code][$factor_name] = array($factor_level);
                }
              } else {
                $tableRows[$unit_code] = array(
                  "unit_code" => array($unit_code),
                  "level_label" => array($level_label),
                  "parent_label" => array($parent_level_label." ".$parent_unit_code),
                   $factor_name => array($factor_level)
                );
              }
            }

            // Passage de 3 tableaux au html pour le dispositif expérimental :
            // les titres des colonnes ($tableHaders) ,
            // le contenu de chaque ligne ($tableRows),
            // et les descriptions qui serviront à afficher des tooltips ($factorsLevelsDescription).
            $data['dispExp_tableHeaders'] = $tableHeaders;
            $data['dispExp_tableRows'] = $tableRows;
            $data['dispExp_FLDescription'] = $factorsLevelsDescription;


            $scripts = array('jquery.dataTables', 'dataTables.bootstrap', 'bootstrap-select-ajax-plugin', 'display_trial/display_trial_script');
            $stylesheets = array('display_trial_style');

            $this->view('trial/display_trial', $page['title'], $page['subtitle'], $data, $scripts, $stylesheets);

        } else {
            show_error(validation_errors());
        }
    }

    //modèle envoi JSON
    public function ajaxLoadExpUnitData2() {
        $ajaxData = array(); //tableau qui sera retourné
        $trialCode =  json_decode($this->input->post('trialCode'));
        //$ajaxData['trialCode'] = $trialCode;
        //$selectedUnitExp =  json_decode($this->input->post('selectedUnitExp'));
        //$selectedVariables = json_decode($this->input->post('selectedVariables'));
        //$ajaxData['exp_unit_data'] = $this->Trial_model->get_exp_unit_data($selectedVariables, $selectedUnitExp);

        //Récupération des données pour le dispositif expérimental
        //$expData = $this->Trial_model->get_trial_hierarchy_data($trialCode);
        $expData = $this->Trial_model->get_trial_hierarchy_data($trialCode);
        $ajaxData['expData'] = $expData;

        echo json_encode($ajaxData);
    }
}
