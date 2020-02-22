<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datasets extends MY_Controller
{
    protected $nbGroupsPerPage = 5; //Nombre de groupes pour la pagination lors de la séléction des droits d'accès

	 //Constructeur du controler
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page. <a href='".site_url('welcome/login')."'>Connexion</a>", 401);
            return;
        }

        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('user_agent');

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
        $this->load->model(array('Dataset_model', 'DupRules_model','ProjectMember_model', 'Project_model'));

    }


    /**
     * Liste des jeux de données de l'utilisateur
     */
    function index($offset = 0, $personal = FALSE)
    {
        // Titre de la page
        $page['title'] = 'Mes Données';
        $page['subtitle'] = 'Liste de mes jeux de données';

        $data['limit'] = $this->input->get('limit'); //  recupere le nombre d'élements à afficher
        $data['offset'] = $offset; // Numero de l'élement à partir duquel l'affichage commence
        $data['order_field'] = $this->input->get('order_field'); // recup. Champ par lequel ranger le tableau
        $data['order_direction'] = $this->input->get('order_direction'); // recup. Direction par laquelle ranger le tableau

        if (!$data['limit']) $data['limit'] = 10;
        $data['total_rows'] = $this->DupRules_model->count(array('username' => $this->session->userdata('username'),'dataset_id >=' => 0)); //

        $this->form_validation->set_data($data);
		//fixe regle de validation du formulaire
        $this->form_validation->set_rules('limit', 'limit', 'trim|is_natural_no_zero|less_than_equal_to[1000]|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|is_natural|less_than_equal_to[' . $data['total_rows'] . ']|xss_clean');

		/*	regle sur le champ a partir duquel le tab va etre ordonne
			implode  chaîne de caractères de tous les éléments des tab, dans le même ordre, avec le caractere ",", placée entre deux éléments
		*/
        $this->form_validation->set_rules('order_field', 'order_field', 'trim|alpha_dash|in_list[' . implode(",", array_merge($this->Dataset_model->fields())) . ']|xss_clean');
        $this->form_validation->set_rules('order_direction', 'order_direction', 'trim|alpha|in_list[ASC,DESC,RANDOM]|xss_clean');

        if ($this->form_validation->run()) {
            $this->load->library('table');
            $this->load->library('pagination');
            $config['base_url'] = site_url('datasets/index');
            $config['total_rows'] = $data['total_rows'];
            $config['per_page'] = $data['limit'];
            $this->pagination->initialize($config);
//
            $data['datasets'] = array_merge($this->Dataset_model->get_handy_datasets($this->session->userdata('username')));

            $this->view('dataset/datasets', $page['title'], $page['subtitle'], $data);
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Affiche le résumé d'un jeu de données
     */

    public function display($dataset_id = Null)
    {
        $this->form_validation->set_data(array('dataset_id' => $dataset_id));
        $this->form_validation->set_rules('dataset_id', 'Jeu de données',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('dataset_exist_callable', array($this->Dataset_model, 'exist'))
            )
        );
        $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');



        if ($this->form_validation->run()) {

            $username = $this->session->userdata('username');

            $data = $this->Dataset_model->find(array('dataset_id' => $dataset_id))[0];

            $this->load->model('DatasetType_model');
            $data['dataset_types'] = $this->DatasetType_model->find();

            $data['nb_members'] = count($this->DupRules_model->get_dataset_members($dataset_id));
            $data['nb_linkedProjects'] = count($this->Project_model->get_dataset_linked_projects($dataset_id));

            $data['permissions'] = FALSE;
            $data['invited'] = FALSE;
            $data['is_member'] = FALSE;

            $query_data = $this->DupRules_model->find(array('dataset_id' => $dataset_id, 'username' => $username));

            if ($query_data) { // On vérifie si l'utilisateur appartient à ce jeu de données
                $dataset_user_info = $query_data[0];
                if ($dataset_user_info['permissions'] == ACCESS_WRITE) $data['permissions'] = TRUE;
                if ($dataset_user_info['permissions'] == ACCESS_READ) $data['invited'] = TRUE;    // Définit si l'utilisateur est invité à rejoindre le jeu de données ou non
                else                                                    $data['is_member'] = TRUE;
            }


            // Titre de la page
            $page['title'] = 'Jeu de données ' . $data['dataset_name'];
            $page['subtitle'] = 'Membres de jeu de données ' . $data['dataset_name'] . ' : Créé par ' . $data['dataset_owner_login'];

            $data['previousUrl'] = $this->agent->referrer();
            $data['project_code'] = $this->input->get('project_code');

            // Appel de la vue
            $scripts = array('bootstrap-treeview.min', 'bootstrap-select-ajax-plugin', 'display_dataset_scripts');
            $this->view('dataset/display_dataset', $page['title'], $page['subtitle'], $data, $scripts);
        } else {
            show_error(validation_errors());
        }
    }


    /**
     * Fonction appelée en AJAX générant les élements HTML et la pagination pour
     * chaque onglet de la vue display_dataset
     */
    public function loadTabElements($num_page)
    {
        $ajaxData = array();
        $nbPerPage = 5;
        $tab_id = $this->input->post('tab_id');
        $dataset_id = $this->input->post('dataset_id');
        $dataset_owner_login = $this->input->post('dataset_owner_login');
        $offsetPage = ($num_page - 1) * $nbPerPage;

        $this->load->library('table');
        $this->load->library('pagination');

        $config['base_url'] = site_url('datasets/loadTabElements');
        $config['per_page'] = $nbPerPage;
        $config['use_page_numbers'] = TRUE;


      switch ($tab_id) {

          case 'members':
              $config['total_rows'] = count($this->DupRules_model->get_dataset_members($dataset_id));
              //Génération du html
              $data['members'] = $this->DupRules_model->get_dataset_members($dataset_id, $config['per_page'], $offsetPage);
              $data['dataset_owner_login'] = $dataset_owner_login;
              $this->pagination->initialize($config);
              $data['pagination'] = $this->pagination->create_links();
              $ajaxData['generatedHtml'] = $this->load->view('dataset/tabsViews/membersTab', $data, TRUE);
              break;

          case 'linkedProjects':
              $config['total_rows'] = count($this->Project_model->get_dataset_linked_projects($dataset_id));
              //Génération du html
              $data['linkedProjects'] = $this->Project_model->get_dataset_linked_projects($dataset_id,  $config['per_page'], $offsetPage);
              $data['dataset_owner_login'] = $dataset_owner_login;
              $this->pagination->initialize($config);
              $data['pagination'] = $this->pagination->create_links();
              $ajaxData['generatedHtml'] = $this->load->view('dataset/tabsViews/linkedProjectsTab', $data, TRUE);
              break;

          default: return null;
      }

      echo json_encode($ajaxData);
    }




    /**
     * Créer un nouveau jeu de données
     */
    public function create($project_code=null)
    {
        // Titre de la page
        $page['title'] = 'Nouveau jeu de données';
        $page['subtitle'] = 'Formulaire de création d\'un jeu de données';

        $this->load->model('DatasetType_model');
        $data['dataset_types'] = $this->DatasetType_model->find();


        // Récupération des données du formulaire
        $dataset_name = $this->input->post('datasetName');
        $dataset_description = $this->input->post('datasetDescription');
        $dataset_type = $this->input->post('datasetType');
        $visibility = $this->input->post('visibility');
        $username = $this->session->userdata('username');

          //Si la visibilité est égale à 1 (Partagé) alors on récupère la liste des membres, sinon ce tableau doit être vide.
        $selectedMembers = ($visibility == VISIBILITY_CUSTOM) ? json_decode($this->input->post('selectedMembers'),true) : [];



        // Règles de validation du formulaire
        $this->form_validation->set_rules('datasetName', 'Nom', 'trim|required|min_length[1]|max_length[80]|is_unique[dataset.dataset_name]|xss_clean');
        $this->form_validation->set_rules('datasetDescription', 'Description', 'trim|max_length[255]|xss_clean');
        //$this->form_validation->set_rules('datasetType', 'Type du jeu de données', 'trim|alpha_numeric_spaces|max_length[50]|xss_clean');
        $this->form_validation->set_rules('visibility', 'Visibilité', 'trim|required|is_natural|max_length[1]|xss_clean|in_list[0,1,2]');
        $this->form_validation->set_message('datasetName', 'Error Message');


        if ($this->form_validation->run()) {
            if (!$dataset_description) $dataset_description = null;
            if (!$dataset_type) $dataset_type = null;

            $this->load->model(array('Dataset_model', 'DupRules_model'));
            $this->Dataset_model->create(array(
                'dataset_name' => $dataset_name,
                'dataset_description' => $dataset_description,
                'dataset_type' => $dataset_type,
                'visibility' => $visibility,
                'dataset_owner_login' => $username
            ));

            $new_dataset_id = $this->Dataset_model->find(array('dataset_name' => $dataset_name))[0]['dataset_id'];

            //Création du tableau d'utilisateurs final qui sera inséré dans la bdd
            $insertArray = [];

            //On ajoute dans un premier temps l'utilisateur actuel avec l'indicateur is_leader=true
            array_push($insertArray,
              array(
                'username' => $username,
                'dataset_id' => $new_dataset_id,
                'permissions' => ACCESS_WRITE,
                'is_leader' => true
              )
            );
            //Si l'accès est partagé on ajoute la liste des membres sélectionnés
            if ($visibility == VISIBILITY_CUSTOM) {
              foreach($selectedMembers as $indice => $member) {
                //Evite de rajouter une 2eme fois l'utilisateur courant même s'il a été sélectionné dans un groupe
                if ($member['username'] == $this->session->userdata('username')) continue;

                array_push($insertArray,
                  array(
                    'username' => $member['username'],
                    'dataset_id' => $new_dataset_id,
                    'permissions' => $member['permission'],
                    'is_leader' => false
                  )
                );
              }
            }

            //log_message('error', print_r($insertArray, true));

            $this->DupRules_model->insertMultipleUsers($insertArray);

            // Si le dataset est ajouté depuis la partie projet on lie le dataset au projet
            // dans la table project_linked_datasets
            if($project_code){
              $this->Project_model->addLinkedDataset($new_dataset_id, $project_code);
            }

            $data['msg'] = "Le jeu de données <strong>".$dataset_name."</strong> a été créé avec succés!";

            $this->view('success', $page['title'], $page['subtitle'], $data);

        } else {
            $data['from_project'] = FALSE;
            $data['project_code'] = null;
            if($project_code){
                $data['from_project'] = TRUE;
                $data['project_code'] = $project_code;
            }

            //Lorsque l'on charge la page on initialise les données pour la recherche de groupe


            $allGroups = $this->ProjectMember_model->get_all_projects_members();

            //Génération de fausse données pour le test de pagination etc
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for ($i=0; $i < 500 ; $i++) {
              $group_name = 'Groupe_' . substr(str_shuffle($permitted_chars), 0, 5);
              $allGroups[$group_name] = array();
              for ($j=0; $j < 100 ; $j++) {
                $user_name = 'User_' . substr(str_shuffle($permitted_chars), 0, 5);
                array_push($allGroups[$group_name], $user_name);
              }
            }



            // Si le dataset est ajouté depuis un projet alors on met en valeur le
            // projet courant en le mettant en premier dans la liste
            if ($project_code) $allGroups = array($project_code => $allGroups[$project_code]) + $allGroups;

            $searchGroupData = array();
            $searchGroupData['allGroups'] = $allGroups;
            $searchGroupData['currentGroups'] = $allGroups; //groupes actuellement filtrés
            $searchGroupData['project_code'] = $project_code;
            $searchGroupData['allowHighlight'] = true;

            //Stockage dans les données de session pour y avoir accès plus tard
            $this->session->set_userdata('searchGroupData',   $searchGroupData);

            $scripts = array('bootstrap-datepicker.min', 'bootstrap-datepicker.fr.min', 'bootstrap-select-ajax-plugin', 'new_datasets_scripts');
            $stylesheets = array('new_datasets');
            $this->view('dataset/new_dataset', $page['title'], $page['subtitle'], $data, $scripts, $stylesheets);
        }
    }


    /**
     * Page de suppression d'un jeu de données
     */
    public function delete()
    {
        $this->form_validation->set_rules('dataset_id', 'jeu de données', array('trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
            array('dataset_exist_callable', array($this->Dataset_model, 'exist'))));
        $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');


        if ($this->form_validation->run()) {

            $dataset_id = $this->input->post('dataset_id');
            $dataset_id = $this->Dataset_model->find(array('dataset_id' => $dataset_id))[0]['dataset_id'];
            $this->load->model(array('DupRules_model'));
            // Vérifie si l'utilisateur est admin ou gestionnaire de jeu de données
            if (!$this->session->userdata('username') && !$this->DupRules_model->find(array('username' => $this->session->userdata('username'), 'dataset_id' => $dataset_id, 'permissions' => ACCESS_WRITE))) {
                set_status_header(401);
                show_error("vous n'êtes pas autorisé à lire cette page.", 401);
                return;
            }

            $this->Dataset_model->delete(array('dataset_id' => $this->input->post('dataset_id')));
            $this->DupRules_model->delete(array('dataset_id' => $this->input->post('dataset_id')));

            redirect('datasets/index/');
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Page de mise à jour d'un jeu de données
     */
    public function update()
    {
        $this->load->model('DatasetType_model');
        $data['dataset_types'] = $this->DatasetType_model->find();

        $this->form_validation->set_rules('dataset_id', 'jeu de données', array('trim', 'required', 'is_natural_no_zero', 'xss_clean',
            array('datasets_exist_callable', array($this->Dataset_model, 'exist'))));
        $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');

        $this->form_validation->set_rules('datasetName', 'Name', 'trim|alpha_dash|required|min_length[5]|max_length[80]|xss_clean');
        //$this->form_validation->set_rules('datasetType', 'Type du jeu de données', 'trim|alpha_dash|max_length[50]|xss_clean');
        $this->form_validation->set_rules('datasetDescription', 'Description', 'trim|max_length[255]|xss_clean');


        if ($this->form_validation->run()) {
            $dataset_id = $this->input->post('dataset_id');
            $dataset_id = $this->Dataset_model->find(array('dataset_id' => $dataset_id))[0]['dataset_id'];

            $new_data = array(
                'dataset_name' => $this->input->post('datasetName'),
                'dataset_type' => $this->input->post('datasetType'),
                'dataset_description' => $this->input->post('datasetDescription'),

            );
            $this->Dataset_model->update(array('dataset_id' => $dataset_id), $new_data, $data);
            redirect('datasets/display/' . $dataset_id, 'location');
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Invite un utilisateur à rejoindre les membres du jeu de données
     */
    public function invit($dataset_id)
    {
        $this->load->model(array('Users_model', 'DupRules_model', 'Dataset_model'));
        $this->form_validation->set_data(array('user' => $this->input->post('user'), 'dataset_id' => $dataset_id)); // 'user' => $this->input->post('user') utilite?
        $this->form_validation->set_rules('user', 'identifiant',
            array(
                'trim', 'required', 'max_length[255]', 'xss_clean', 'callback_not_already_member[' . $dataset_id . ']',
                array('user_exist_callable', array($this->Users_model, 'exist')) //custom error message mal place ?
            )
        );
        $this->form_validation->set_message('user_exist_callable', 'L\'{field} n\'éxiste pas.');
        $this->form_validation->set_rules('dataset_id', 'jeu de données',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('dataset_exist_callable', array($this->Dataset_model, 'exist')) //error message mal place ?
            )
        );
        $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $target_username = $this->input->post('user');
            $sender_username = $this->session->userdata('username');

            $this->DupRules_model->create(array('username' => $target_username, 'dataset_id' => $dataset_id, 'permissions' => ACCESS_READ));

            // Alerte l'utilisateur invité (Notifications & Email)

            $target_user = $this->Users_model->find(array('username' => $target_username))[0];
            $this->load->library('email');

            if ($target_user['sending_email_enabled'] == PGSQL_TRUE) {
                $this->email->from(AEGIS_MAIL, "DAPHNE Web Application");
                $this->email->to($target_user['email']);

                $this->email->subject($sender_username . ' vous a ajouté à son Jeu de données ' . $dataset_id);
                $this->email->message($sender_username . ' vous a ajouté aux membres du jeu de données ' . $dataset_id . ' via l\'application Web de DAPHNE');

                $this->email->send();
            }
            $this->Notification_model->create(array(
                'target_login' => $target_user['username'],
                'notification_type' => DATASET_INVIT,
                'sender_login' => $sender_username,
                'created_time' => 'NOW()',
                'ressource' => $dataset_id
            ));

            redirect('datasets/display/' . $dataset_id, 'location');

        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Change le statut du membre dans le projet
     */
    public function change_member_statut($dataset_id)
    {
        $username = $this->input->post('username');

        $this->form_validation->set_data(array('dataset_id' => $dataset_id, 'username' => $username));
        $this->form_validation->set_rules('username', 'Identifiant', 'trim|required|max_length[255]|xss_clean|callback_is_member[' . $dataset_id . ']');
        $this->form_validation->set_rules('dataset_id', 'jeu de données',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('dataset_exist_callable', array($this->Dataset_model, 'exist'))
            )
        );
        $this->form_validation->set_message('Dataset_exist_callable', 'Le {field} n\'éxiste pas.');
        if ($this->form_validation->run()) {
            if ($this->DupRules_model->find(array('username' => $username, 'dataset_id' => $dataset_id, 'permissions' => ACCESS_READ))) {
                $this->DupRules_model->update(array('username' => $username, 'dataset_id' => $dataset_id), array('permissions' => ACCESS_WRITE));
            } else {
                $this->DupRules_model->update(array('username' => $username, 'dataset_id' => $dataset_id), array('permissions' => ACCESS_READ));
            }
            redirect('datasets/display/' . $dataset_id, 'location');
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Supprime un membre du jeu de données
     */
    public function remove_member($dataset_id)
    {
        $username = $this->input->post('username');

        $this->form_validation->set_data(array('dataset_id' => $dataset_id, 'username' => $username));
        $this->form_validation->set_rules('username', 'Identifiant', 'trim|required|max_length[255]|xss_clean|callback_is_member[' . $dataset_id . ']');
        $this->form_validation->set_rules('dataset_id', 'jeu de données',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('dataset_exist_callable', array($this->Dataset_model, 'exist'))
            )
        );
        $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');
        if ($this->form_validation->run()) {
            $this->DupRules_model->delete(array('username' => $username, 'dataset_id' => $dataset_id));
            redirect('datasets/display/' . $dataset_id, 'location');
        } else {
            show_error(validation_errors());
        }
    }


    public function quit($dataset_id)
    {
        $username = $this->session->userdata('username');

        $this->form_validation->set_data(array('dataset_id' => $dataset_id, 'username' => $username));
        $this->form_validation->set_rules('username', 'Identifiant', 'trim|required|max_length[255]|xss_clean|callback_is_member[' . $dataset_id . ']');
        $this->form_validation->set_rules('dataset_id', 'jeu de données',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('dataset_exist_callable', array($this->Dataset_model, 'exist'))
            )
        );
        $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {

            $this->DupRules_model->delete(array('username' => $username, 'dataset_id' => $dataset_id));

            redirect('datasets/index/');

        } else {
            show_error(validation_errors());
        }
    }


    /**
     * Retourne Vrai si l'utilisateur n'est ni membre, ni invité du jeu de données
     */
    public function not_already_member($username, $dataset_id)
    {
        $this->load->model(array('DupRules_model'));
        $query_data = $this->DupRules_model->find(array('username' => $username, 'dataset_id' => $dataset_id));
        if ($query_data) {
            $this->form_validation->set_message('not_already_member', "Cet utilisateur a déjà été invité");
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function is_member($username, $dataset_id)
    {
        $this->load->model(array('DupRules_model'));
        if ($this->DupRules_model->find(array('username' => $username, 'dataset_id' => $dataset_id))) {
            return TRUE;
        } else {
            $this->form_validation->set_message('is_member', "Cet utilisateur ne fait pas partie du jeu de données");
            return FALSE;
        }
    }


    /**
    * Fonction appelée (AJAX) lors de la recherche de groupe. Elle permet de filtrer les groupes
    * et de retourner le html associé afin d'avoir un "live search".
    **/
    public function search_members_groups()
    {
      //  Filtrage des groupes avec le terme recherché

      $searched_term = strtolower($this->input->post('searched_term'));

      if ($searched_term !== '') {
        $foundGroups = array_filter($this->session->searchGroupData['allGroups'],
          function($key) use ($searched_term) {
            return strpos(strtolower($key), $searched_term) !== FALSE;
          },
          ARRAY_FILTER_USE_KEY);
      } else {
        $foundGroups = $this->session->searchGroupData['allGroups'];
      }

      //Mise à jour de la variable de session avec le nouveau tableau filtré
      $this->updateSearchGroupData('currentGroups', $foundGroups);

      // Génération du html (première page seulement) qui représentera les groupes
      // et des données de pagination

      $pageData = $this->loadGroupsHtmlForPage(1, false); //page=1 - ajax=false;

      //Après la première recherche il n'est plus nécéssaire de mettre en avant
      // le projet actuel (notamment le fait d'ouvrir le panel par défaut)
      if ($this->session->searchGroupData['allowHighlight']) $this->updateSearchGroupData('allowHighlight', false);

      // Retourne les données à la requête Ajax
      $ajaxData['pagination'] = $pageData['pagination'];
      $ajaxData['groupsGeneratedHtml'] = $pageData['generatedHtml'];

      echo json_encode($ajaxData);
    }


    public function loadGroupsHtmlForPage($num_page, $ajax=true) {
      $currentGroups = $this->session->searchGroupData['currentGroups'];
      $debut = ($num_page - 1) * $this->nbGroupsPerPage;

      //Selection des groupes à afficher pour ce numéro de page
      $thisPageGroups = array_slice($currentGroups, $debut, $this->nbGroupsPerPage);

      //Génération du html qui représentera les groupes
      $data['projectsMembers'] = $thisPageGroups;
      $data['project_code'] = $this->session->searchGroupData['project_code'];
      $data['allowHighlight'] = $this->session->searchGroupData['allowHighlight'];
      // grâce au troisième paramètre de la méthode view(), il est possible d'obtenir
      // le rendu html d'une vue. Ainsi il sera possible de le passer en ajax et
      // de l'afficher avec jQuery
      $generatedHtml = $this->load->view('dataset/groupsMembers', $data, TRUE);


      //Chargement des données de pagination

      // Pagination Configuration
      $config['base_url'] = base_url().'index.php/datasets/loadGroupsHtmlForPage';
      $config['use_page_numbers'] = TRUE;
      $config['total_rows'] = count($currentGroups);
      $config['per_page'] = $this->nbGroupsPerPage;

      // Initialize
      $this->pagination->initialize($config);

      $pagination_links = $this->pagination->create_links();

      if ($ajax)
      {
        $ajaxData['pagination'] = $pagination_links;
        $ajaxData['groupsHtml'] = $generatedHtml;
        echo json_encode($ajaxData);
      }
      else {
        return array('generatedHtml' => $generatedHtml, 'pagination' => $pagination_links);
      }
    }

    public function updateSearchGroupData($key, $val) {
      $searchGroupData = $this->session->searchGroupData;
      $searchGroupData[$key] = $val;
      $this->session->set_userdata('searchGroupData',   $searchGroupData);
    }










    /**
     * Affiche les utilisateurs recherchés qui ne sont pas encore membre d'un jeu de données sous forme de liste d'options (Format JSON)
     * si le dataset_id n'est pas spécifié alors le modèle retournera la liste de tous les utilisateurs du site (sauf celui connecté).
     */
    public function searched_not_dataset_members($dataset_id=null)
    {
        $this->form_validation->set_data(array('term' => $this->input->post('term'), 'dataset_id' => $dataset_id));
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if (!is_null($dataset_id)) {
          $this->form_validation->set_rules('dataset_id', 'jeu de connées',
              array(
                  'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                  array('dataset_exist_callable', array($this->Dataset_model, 'exist'))
              )
          );
          $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');
        }

        if ($this->form_validation->run()) {
            $this->load->model(array('DupRules_model'));

            $searched_term = $this->input->post('term');
            $datas = $this->DupRules_model->like_not_dataset_members($searched_term, $dataset_id);

            $found_members = array_map(function ($data) {
                return array(
                    'name' => $data['username'],
                    'value' => $data['username'],
                    'subtext' => $data['first_name'] . ' ' . $data['last_name']
                );
            }, $datas);

            echo json_encode($found_members);
        } else {
            $this->form_validation->set_error_delimiters('', '');
            echo json_encode(array('type' => 'error', 'message' => validation_errors()));
        }
    }

    /**
     * Affiche les utilisateurs recherchés qui ne sont pas encore membre d'un jeu de données sous forme de liste d'options (Format JSON)
     */
    public function OLD_searched_not_dataset_members($dataset_id)
    {
        $this->form_validation->set_data(array('term' => $this->input->post('term'), 'dataset_id' => $dataset_id));
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('dataset_id', 'jeu de connées',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('dataset_exist_callable', array($this->Dataset_model, 'exist'))
            )
        );
        $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $this->load->model(array('DupRules_model'));

            $searched_term = $this->input->post('term');
            $datas = $this->DupRules_model->like_not_dataset_members($searched_term, $dataset_id);

            $not_members = array_map(function ($data) {
                return array(
                    'name' => $data['username'],
                    'value' => $data['username'],
                    'subtext' => $data['first_name'] . ' ' . $data['last_name']
                );
            }, $datas);

            echo json_encode($not_members);
        } else {
            $this->form_validation->set_error_delimiters('', '');
            echo json_encode(array('type' => 'error', 'message' => validation_errors()));
        }
    }


	/**
     * Affiche les jeux de données recherchés pour un projet sur lequels l'utilisateur peut écrire.
     * Ces jeux de données sont renvoyés sous forme de liste d'options (Format JSON)
     */
    public function searched_user_datasets_project($project_code)
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $username = $this->session->userdata('username');

            if ($this->session->userdata('admin')) {
                $datas = $this->Dataset_model->like($searched_term, 'dataset_name');
            } else {
				if ($project_code) {
					$datas = $this->DupRules_model->get_datasets_by_project_by_user_for_permission($username, $project_code, 'w');
				}
            }

            $dataset = array_map(function ($data) {

                switch ($data['visibility']) {
                    case VISIBILITY_PRIVATE:  //0
                        $type = "danger";
                        $label = "Privé";
                        break;

                    case VISIBILITY_CUSTOM:  //1
                        $type = "warning";
                        $label = "Personnalisé";
                        break;

                    case VISIBILITY_PUBLIC: //2
                        $type = "info";
                        $label = "Public";
                        break;
                }

                return array(
                    'name' => $data['dataset_name'] . ' (Crée par ' . $data['dataset_owner_login'] . ')',
                    'value' => $data['dataset_id'],
                    'tag' => array(
                        'type' => $type,
                        'label' => $label
                    )
                );
            }, $datas);

            echo json_encode($dataset);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

    /**
     * Affiche les jeux de données recherchés sur lesquels l'utilisateur peut écrire.
     * Ces jeux de données sont renvoyés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $username = $this->session->userdata('username');

            if ($this->session->userdata('admin')) {
                $datas = $this->Dataset_model->like($searched_term, 'dataset_name');
            } else {
				$datas = array_merge($this->Dataset_model->find(array('dataset_owner_login' => $username, 'visibility' => 2), $data['limit'], $data['offset'], $data['order_field'], $data['order_direction'])
				, $this->Dataset_model->get_handy_datasets($username, ACCESS_WRITE));
            }

            $dataset = array_map(function ($data) {

                switch ($data['visibility']) {
                    case VISIBILITY_PRIVATE:  //0
                        $type = "danger";
                        $label = "Privé";
                        break;

                    case VISIBILITY_CUSTOM:  //1
                        $type = "warning";
                        $label = "Personnalisé";
                        break;

                    case VISIBILITY_PUBLIC: //2
                        $type = "info";
                        $label = "Public";
                        break;
                }

                return array(
                    'name' => $data['dataset_name'] . ' (Crée par ' . $data['dataset_owner_login'] . ')',
                    'value' => $data['dataset_id'],
                    'tag' => array(
                        'type' => $type,
                        'label' => $label
                    )
                );
            }, $datas);

            echo json_encode($dataset);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }


    public function request()
    {
        $dataset_id='35';
        $this->load->model('DatasetUser_model');
        $datas = $this->DatasetUser_model->dataname($dataset_id);
        print_r($datas);

    }

}
