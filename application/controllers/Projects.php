<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page.", 401);
            return;
        }

        $this->load->library('form_validation');
        $this->load->model(array('Project_model','DupRules_model', 'Trial_model', 'Users_model','Partner_model','Dataset_model','Notification_model','ProjectPartner_model','ProjectMember_model','TrialProject_model'));
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>', '</div>');
    }

    /**
     * Affiche la liste des projets
     */
    function index($offset = 0)
    {
        // Titre de la page
        $page['title'] = 'Projets';
        $page['subtitle'] = 'Liste des projets répertoriés dans DAPHNE';

        $data['limit'] = $this->input->get('limit');                     // Nombre d'élements à afficher
        $data['offset'] = $offset;                                       // Numero de l'élement à partir duquel l'affichage commence
        $data['order_field'] = $this->input->get('order_field');         // Champ par lequel ranger le tableau
        $data['order_direction'] = $this->input->get('order_direction'); // Direction par laquel ranger le tableau

        if (!$data['limit']) $data['limit'] = 10;
        //$data['total_rows'] = $this->Project_model->count(array('is_validated' => PGSQL_TRUE)); // Compte uniquement les projets validés
        $data['total_rows'] = $this->Project_model->count();


        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('limit', 'limit', 'trim|is_natural_no_zero|less_than_equal_to[1000]|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|is_natural|less_than[' . $data['total_rows'] . ']|xss_clean');
        $this->form_validation->set_rules('order_field', 'order_field', 'trim|alpha_dash|in_list[' . implode(",", $this->Project_model->fields()) . ']|xss_clean');
        $this->form_validation->set_rules('order_direction', 'order_direction', 'trim|alpha|in_list[ASC,DESC,RANDOM]|xss_clean');

        if ($this->form_validation->run()) {
            $this->load->library('table');
            $this->load->library('pagination');

            $config['base_url'] = site_url('projects/index');
            $config['total_rows'] = $data['total_rows'];
            $config['per_page'] = $data['limit'];
            $this->pagination->initialize($config);

            /*$query_filter = array();
            if ($this->session->userdata('admin') != PGSQL_TRUE) {
                $query_filter = array('is_validated' => PGSQL_TRUE);
            }*/
            $data['projects'] = $this->Project_model->find(array(), $data['limit'], $data['offset'], $data['order_field'], $data['order_direction']);
            $this->view('project/projects', $page['title'], $page['subtitle'], $data);
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Affiche le résumé d'un projet
     */
    public function display($project_code)
    {
        $this->form_validation->set_data(array('project_code' => $project_code));
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {

            $data = $this->Project_model->find(array('project_code' => $project_code))[0];


            $data['nb_members'] = $this->ProjectMember_model->count(array('project_code' => $project_code));
            $data['nb_partners'] = $this->ProjectPartner_model->count(array('project_code' => $project_code));
            $data['nb_trials'] = $this->TrialProject_model->count(array('project_code' => $project_code));
            $data['nb_datasets'] = $this->Dataset_model->count_all_handy_project_datasets($project_code);

            $currentUserStatus = $this->getUserProjectStatus($project_code, $this->session->userdata('username'));
            $data['is_leader'] = ($currentUserStatus == 'leader');
            $data['is_member'] = ($currentUserStatus == 'member');

            // Titre de la page
            $page['title'] = 'Projet ' . $project_code;
            $page['subtitle'] = $data['project_name'];

           // Appel de la vue
            $scripts = array('bootstrap-treeview.min', 'bootstrap-select-ajax-plugin', 'display_project_scripts');
            $this->view('project/display_project', $page['title'], $page['subtitle'], $data, $scripts);
        } else {
            show_error(validation_errors());
        }
    }

    /* fonction utilitaire pour déterminer le statut d'un utilisateur dans un projet ('leader' | 'member' | 'none' ) */
    public function getUserProjectStatus($project_code, $username) {
      $status = 'none';
      $userInfos = $this->ProjectMember_model->find(array('project_code' => $project_code, 'username' => $username));

      if ($userInfos) { // On vérifie si l'utilisateur appartient à ce projet
          if ($userInfos[0]['is_leader'] == PGSQL_TRUE) $status = 'leader';  // Définit si l'utilisateur est administrateur du projet ou non
          else                                          $status = 'member';
      }

      return $status;
    }

    /**
     * Fonction appelée en AJAX générant les élements HTML et la pagination pour
     * chaque onglet de la vue display_project
     */
    public function loadTabElements($num_page)
    {
        $ajaxData = array();
        $nbPerPage = 5;
        $project_code = $this->input->post('project_code');
        $tab_id = $this->input->post('tab_id');
        $offsetPage = ($num_page - 1) * $nbPerPage;

        $this->load->library('table');
        $this->load->library('pagination');

        $config['base_url'] = site_url('projects/loadTabElements');
        $config['per_page'] = $nbPerPage;
        $config['use_page_numbers'] = TRUE;

        $currentUserStatus = $this->getUserProjectStatus($project_code, $this->session->userdata('username'));
        $data['is_leader'] = ($currentUserStatus == 'leader');
        $data['is_member'] = ($currentUserStatus == 'member');
        $data['project_code'] = $project_code;


      switch ($tab_id) {

          case 'members':

              $config['total_rows'] = $this->ProjectMember_model->count(array('project_code' => $project_code));
              //Génération du html
              $data['members'] = $this->ProjectMember_model->get_project_members($project_code, null, $config['per_page'], $offsetPage);
              $this->pagination->initialize($config);
              $data['pagination'] = $this->pagination->create_links();
              $ajaxData['generatedHtml'] = $this->load->view('project/tabsViews/membersTab', $data, TRUE);
              break;

          case 'partners':

              $config['total_rows'] = $this->ProjectPartner_model->count(array('project_code' => $project_code));
              //Génération du html
              $data['partners'] = $this->ProjectPartner_model->get_project_partners($project_code, $config['per_page'], $offsetPage);
              $this->pagination->initialize($config);
              $data['pagination'] = $this->pagination->create_links();
              $ajaxData['generatedHtml'] = $this->load->view('project/tabsViews/partnersTab', $data, TRUE);
              break;

          case 'trials':

              $config['total_rows'] = $this->TrialProject_model->count(array('project_code' => $project_code));
              //Génération du html
              $data['trials'] = $this->TrialProject_model->get_project_trials($project_code, $config['per_page'], $offsetPage);
              $this->pagination->initialize($config);
              $data['pagination'] = $this->pagination->create_links();
              $ajaxData['generatedHtml'] = $this->load->view('project/tabsViews/trialsTab', $data, TRUE);
              break;

          case 'datasets':

              $config['total_rows'] = $this->Dataset_model->count_all_handy_project_datasets($project_code);
              //Génération du html
              $data['datasets'] = $this->Dataset_model->get_handy_project_datasets($project_code, $config['per_page'], $offsetPage);
              $this->pagination->initialize($config);
              $data['pagination'] = $this->pagination->create_links();
              $ajaxData['generatedHtml'] = $this->load->view('project/tabsViews/datasetsTab', $data, TRUE);
              break;

          default: return null;
      }

      echo json_encode($ajaxData);
    }

    /**
     * Page de création d'un projet
     */
    public function create()
    {
        // Titre de la page
        $page['title'] = 'Demande de création d\'un nouveau projet';
        $page['subtitle'] = 'Formulaire de saisie d\'une demande création de projet';

        // Récupération des données du formulaire
        $data['project_code'] = $this->input->post('project_code');
        $data['project_name'] = $this->input->post('project_name');
        $data['project_resume'] = $this->input->post('project_description');
        $data['coordinator'] = $this->input->post('coordinator');
        $data['coord_company'] = $this->input->post('company');
        $data['selected_partners'] = $this->input->post('selected_partners');


        // Règles de validation du formulaire
        $this->form_validation->set_rules('project_code', 'Code projet', 'required|trim|min_length[2]|max_length[255]|alpha_dash|is_unique[project.project_code]|xss_clean');
        $this->form_validation->set_rules('project_name', 'Nom du projet', 'required|trim|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('project_description', 'Description', 'trim|min_length[3]|xss_clean');
        $this->form_validation->set_rules('coordinator', 'Responsable', 'required|trim|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('company', 'Société', 'trim|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('selected_partners[]', 'Partenaires', 'callback_partner_valid|xss_clean');

        // Test de validation du formulaire
        if ($this->form_validation->run()) {
            $this->load->helper('postgre');
            $data = nullify_array($data); // Remplace les chaines de carctères vide par la valeur NULL

            //$this->load->model(array(  'ProjectUser_model', 'ProjectPartner_model'));
            // Enregistre le projet
            $this->Project_model->create(
                array(
                    'project_code' => $data['project_code'],
                    'project_name' => $data['project_name'],
                    'project_resume' => $data['project_resume'],
                    'coordinator' => $data['coordinator'],
                    'coord_company' => $data['coord_company']
                )
            );

            $this->ProjectMember_model->create(
                array(
                    'username' => $this->session->userdata('username'),
                    'project_code' => $data['project_code'],
                    'is_leader' => PGSQL_TRUE
                ));

// Enregistre les partenaires du projet
            $project_partners = $data['selected_partners'];
            if (count($project_partners)) {
                foreach ($project_partners as $partner_code) {
                    $this->ProjectPartner_model->create(array(
                        'project_code' => $data['project_code'],
                        'partner_code' => $partner_code
                    ));
                }
            }

            $data['msg'] = "Votre projet <strong>" . $data['project_code'] . "</strong> a été créé avec succès ! ";
            $this->view('success', $page['title'], $page['subtitle'], $data);
        } else {
            //$this->load->model(array('Partner_model'));
            $data['partners'] = $this->Partner_model->find();

            if (!$data['selected_partners']) {
                $data['selected_partners'] = array();
            }

// Affichage du formulaire de création d'un projet et retour des erreurs
            $scripts = array('bootstrap-select-ajax-plugin', 'new_project_app');
            $this->view('project/new_project', $page['title'], $page['subtitle'], $data, $scripts);
        }
    }

    /**
     * Page de mise à jour d'un projet
     */
    public function update($project_code = null)
    {
        //$this->load->model(array('DupRules_model'));
        try {
            $project_code = xss_clean($project_code); // Sécurise la valeur de la variable
//if (empty($project_code)) throw new Exception("Missing argument project_code in URL", 1);
            $query_result = $this->Project_model->find(array('project_code' => $project_code));
//if (!$query_result)       throw new Exception("Project_code doesn't exist", 2);
            $data = $query_result[0];

            if (!$this->ProjectMember_model->find(array('project_code' => $project_code, 'username' => $this->session->userdata('username'), 'is_leader' => PGSQL_TRUE)) && !$this->session->userdata('admin')) {
                throw new Exception("vous n'êtes pas autorisé à lire cette page.", 3);
            }

        } catch (Exception $e) {
            show_error($e->getMessage());
        }

// Régle de validation du formulaire
        $this->form_validation->set_rules('project_name', 'Nom du projet', 'required|trim|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('project_description', 'Description', 'trim|min_length[3]|xss_clean');
        $this->form_validation->set_rules('coordinator', 'Responsable', 'required|trim|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('coord_company', 'Organisation', 'trim|min_length[2]|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {

            $this->Project_model->update(
                array('project_code' => $project_code),
                array(
                    'project_name' => $this->input->post('project_name'),
                    'project_resume' => $this->input->post('project_description'),
                    'coordinator' => $this->input->post('coordinator'),
                    'coord_company' => $this->input->post('coord_company')
                )
            );
            redirect('projects/display/' . $project_code, 'location');

        } else {
            $page['title'] = 'Projet ' . $project_code;
            $page['subtitle'] = 'Formulaire de modification du projet ' . $project_code;
            $this->view('project/update_project', $page['title'], $page['subtitle'], $data);
        }
    }

    /**
     * [ADMIN] Supprime un projet (Implémentation future pour les administrateurs)
     */
    public function delete($project_code)
    {
        if (!$this->session->userdata('admin')) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $this->form_validation->set_data(array('project_code' => $project_code));
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {

            $this->Project_model->delete(array('project_code' => $project_code));  //Cela va "cascade delete" les membres etc
            //$this->load->model(array('DupRules_model'));
            //$this->DupRules_model->delete(array('project_code' => $project_code));
            redirect('projects', 'location');
        }
    }

    /**
     * Affiche les projets recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $username = $this->session->userdata('username');

            if ($this->session->userdata('admin')) {
                $results = $this->Project_model->like($searched_term, 'project_code');
            } else {
                $results = $this->Project_model->search_available_projects($username, $is_admin, $searched_term, 'project_code');
            }

            $projects = array_map(function ($result) {
                if ($result['is_validated'] == PGSQL_FALSE) {
                    return array(
                        'name' => $result['project_code'],
                        'value' => $result['project_code'],
                        'tag' => array(
                            'type' => "warning",
                            'label' => "Non vérifié"
                        )
                    );
                } else {
                    return array(
                        'name' => $result['project_code'],
                        'value' => $result['project_code']
                    );
                }
            }, $results);

            echo json_encode($projects);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }


	/**
     * Affiche les projets recherchés auxquels l utilisateur prend part ou à des droits sous forme de liste d'options (Format JSON)
     */
    public function searched_options_user_projects()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $username = $this->session->userdata('username');

            if ($this->session->userdata('admin')) {
                $results = $this->Project_model->like($searched_term, 'project_code');
            } else {
                //$results = $this->Project_model->search_available_projects($username, $is_admin, $searched_term, 'project_code');
				$results = $this->ProjectMember_model->get_user_projects($username, $searched_term);
            }

            $projects = array_map(function ($result) {
                return array(
                    'name' => $result['project_code'],
                    'value' => $result['project_code']
                );
              }, $results);
            /*$projects = array_map(function ($result) {
                if ($result['is_validated'] == PGSQL_FALSE) {
                    return array(
                        'name' => $result['project_code'],
                        'value' => $result['project_code'],
                        'tag' => array(
                            'type' => "warning",
                            'label' => "Non vérifié"
                        )
                    );
                } else {
                    return array(
                        'name' => $result['project_code'],
                        'value' => $result['project_code']
                    );
                }
            }, $results);*/

            echo json_encode($projects);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

    /**
     * Affiche les utilisateurs recherchés qui ne sont pas encore membre d'un projet donnée sous forme de liste d'options (Format JSON)
     */
    public function searched_not_project_members($project_code)
    {
        $this->form_validation->set_data(array('term' => $this->input->post('term'), 'project_code' => $project_code));
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            //$this->load->model(array('DupRules_model'));

            $searched_term = $this->input->post('term');
            $results = $this->ProjectMember_model->like_not_project_members($searched_term, $project_code);

            $not_members = array_map(function ($result) {
                return array(
                    'name' => $result['username'],
                    'value' => $result['username'],
                    'subtext' => $result['first_name'] . ' ' . $result['last_name']
                );
            }, $results);

            echo json_encode($not_members);
        } else {
            $this->form_validation->set_error_delimiters('', '');
            echo json_encode(array('type' => 'error', 'message' => validation_errors()));
        }
    }


    /**
     * Affiche les organismes recherchés qui ne sont pas encore partenaires d'un projet donnée sous forme de liste d'options (Format JSON)
     */
    public function searched_not_project_partners($project_code)
    {
        $this->form_validation->set_data(array('term' => $this->input->post('term'), 'project_code' => $project_code));
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            //$this->load->model(array('ProjectPartner_model'));

            $searched_term = $this->input->post('term');
            $results = $this->ProjectPartner_model->like_not_project_partners($searched_term, $project_code);

            $not_partners = array_map(function ($result) {
                return array(
                    'name' => $result['partner_name'],
                    'value' => $result['partner_code']
                );
            }, $results);

            echo json_encode($not_partners);
        } else {
            $this->form_validation->set_error_delimiters('', '');
            echo json_encode(array('type' => 'error', 'message' => validation_errors()));
        }
    }

    /**
     * Affiche les datsetss recherchés qui ne sont pas encore datasets d'un projet donnée sous forme de liste d'options (Format JSON)
     */
    public function searched_not_project_datasets($project_code)
    {

        $this->form_validation->set_data(array('term' => $this->input->post('term'), 'project_code' => $project_code));
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            //$this->load->model(array('DupRules_model'));

            $searched_term = $this->input->post('term');


            //$results = $this->DupRules_model->like_not_project_datasets($searched_term, $project_code);
            $results = $this->Dataset_model->like_not_project_datasets($searched_term, $project_code);

            //var_dump($results);
            $not_datasets = array_map(function ($result) {
                return array(
                    'name' => $result['dataset_name'],
                    'value' => $result['dataset_id']
                );
            }, $results);

            echo json_encode($not_datasets);
        } else {
            $this->form_validation->set_error_delimiters('', '');
            echo json_encode(array('type' => 'error', 'message' => validation_errors()));
        }
    }


    /**
     *  Petite fonction utilitaire
     **/

    protected function isCurrentUser_admin_or_leader($project_code) {
      $isCurrentUserAdmin = $this->session->userdata('admin');
      $isCurrentUserLeader = $this->ProjectMember_model->find(array('username' => $this->session->userdata('username'), 'project_code' => $project_code, 'is_leader' => PGSQL_TRUE));
      return ($isCurrentUserAdmin || $isCurrentUserLeader);
    }

    /**
     * Invite un utilisateur à rejoindre les membres du projet
     */
    public function invit($project_code)
    {
        //$this->load->model(array('Users_model', 'DupRules_model'));


        //Si ni admin ni leader
        if ( ! $this->isCurrentUser_admin_or_leader($project_code)) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $this->form_validation->set_data(array('user' => $this->input->post('user'), 'project_code' => $project_code));

        $this->form_validation->set_rules('user', 'identifiant',
            array(
                'trim', 'required', 'max_length[255]', 'xss_clean', 'callback_not_already_member[' . $project_code . ']',
                array('user_exist_callable', array($this->Users_model, 'exist'))
            )
        );
        $this->form_validation->set_message('user_exist_callable', 'L\'{field} n\'éxiste pas.');

        $this->form_validation->set_rules('project_code', 'code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $target_username = $this->input->post('user');
            $sender_username = $this->session->userdata('username');

            $this->ProjectMember_model->create(array('username' => $target_username, 'project_code' => $project_code, 'is_leader' => PGSQL_FALSE));

            // Alerte l'utilisateur invité (Notifications & Email)
            $target_user = $this->Users_model->find(array('username' => $target_username))[0];

            $this->load->library('email');

            if ($target_user['sending_email_enabled'] == PGSQL_TRUE) {
                $this->email->from(AEGIS_MAIL, "DAPHNE Web Application");
                $this->email->to($target_user['email']);

                $this->email->subject($sender_username . ' vous invite à rejoindre le projet ' . $project_code);
                $this->email->message($sender_username . ' vous invite à rejoindre les membres du projet ' . $project_code . ' via l\'application Web de DAPHNE');

                $this->email->send();
            }

            $this->Notification_model->create(array(
                'target_login' => $target_user['username'],
                'notification_type' => PROJECT_INVIT,
                'sender_login' => $sender_username,
                'created_time' => 'NOW()',
                'ressource' => $project_code
            ));

            redirect('projects/display/' . $project_code, 'location');

        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Supprime un membre du projet
     */
    public function remove_member($project_code)
    {

        //Si ni admin ni leader
        if ( ! $this->isCurrentUser_admin_or_leader($project_code)) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $username = $this->input->post('username');

        $this->form_validation->set_data(array('project_code' => $project_code, 'username' => $username));
        $this->form_validation->set_rules('username', 'Identifiant', 'trim|required|max_length[255]|xss_clean|callback_is_member[' . $project_code . ']');
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');
        if ($this->form_validation->run()) {
            $this->ProjectMember_model->delete(array('username' => $username, 'project_code' => $project_code));
            redirect('projects/display/' . $project_code, 'location');
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Change le statut du membre dans le projet
     */
    public function change_member_statut($project_code)
    {
// Vérifie si l'utilisateur est admin ou gestionnaire de projet
        //$this->load->model('DupRules_model');
        if ( ! $this->isCurrentUser_admin_or_leader($project_code)) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $username = $this->input->post('username');

        $this->form_validation->set_data(array('project_code' => $project_code, 'username' => $username));
        $this->form_validation->set_rules('username', 'Identifiant', 'trim|required|max_length[255]|xss_clean|callback_is_member[' . $project_code . ']');
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');
        if ($this->form_validation->run()) {
            if ($this->ProjectMember_model->find(array('username' => $username, 'project_code' => $project_code, 'is_leader' => PGSQL_FALSE))) {
                $this->ProjectMember_model->update(array('username' => $username, 'project_code' => $project_code), array('is_leader' => PGSQL_TRUE));
            } else {
                $this->ProjectMember_model->update(array('username' => $username, 'project_code' => $project_code), array('is_leader' => PGSQL_FALSE));
            }
            redirect('projects/display/' . $project_code, 'location');
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Ajoute un partenaire à un projet
     */
    public function add_partner($project_code)
    {

        //$this->load->model(array('Partner_model', 'ProjectUser_model'));

        $this->load->model(array('Partner_model', 'DupRules_model'));

// Vérifie si l'utilisateur est admin ou gestionnaire de projet
        if ( ! $this->isCurrentUser_admin_or_leader($project_code)) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $this->form_validation->set_data(array('project_code' => $project_code, 'partner' => $this->input->post('partner')));

        $this->form_validation->set_rules('partner', 'Partenaire', array('required', 'is_natural', 'callback_partner_valid', 'xss_clean', 'callback_not_already_partner[' . $project_code . ']',
                array('partner_exist_callable', array($this->Partner_model, 'exist'))
            )
        );
        $this->form_validation->set_message('partner_exist_callable', 'Le {field} n\'éxiste pas.');
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $partner = $this->input->post('partner');
            //$this->load->model(array('ProjectPartner_model'));
            $this->ProjectPartner_model->create(array('project_code' => $project_code, 'partner_code' => $partner));
            redirect('projects/display/' . $project_code, 'location');
        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Supprime un partenaire à un projet
     */
    public function remove_partner($project_code)
    {
// Vérifie si l'utilisateur est admin ou gestionnaire de projet
        if ( ! $this->isCurrentUser_admin_or_leader($project_code)) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $this->form_validation->set_data(array('project_code' => $project_code, 'partner' => $this->input->post('partner')));

        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');
        $this->form_validation->set_rules('partner', 'Partenaire',
            array('required', 'is_natural', 'callback_is_project_partner[' . $project_code . ']', 'xss_clean'));

        if ($this->form_validation->run()) {
            $partner = $this->input->post('partner');
            //$this->load->model(array('ProjectPartner_model'));
            $this->ProjectPartner_model->delete(array('project_code' => $project_code, 'partner_code' => $partner));
            redirect('projects/display/' . $project_code, 'location');
        } else {
            show_error(validation_errors());
        }
    }


    /**
     * Supprime un dataset d'un projet
     */
    public function remove_dataset($project_code)
    {
// Vérifie si l'utilisateur est admin ou gestionnaire de projet
        if ( ! $this->isCurrentUser_admin_or_leader($project_code)) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $this->form_validation->set_data(array('project_code' => $project_code, 'dataset_id' => $this->input->post('dataset_id')));

        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        $this->form_validation->set_rules('dataset_id', 'Jeu de données',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('dataset_exist_callable', array($this->Dataset_model, 'exist'))
            )
        );
        $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');



        if ($this->form_validation->run()) {
            $dataset_id = $this->input->post('dataset_id');
            $this->Project_model->removeLinkedDataset($dataset_id, $project_code);
            redirect('projects/display/' . $project_code, 'location');
        } else {
            show_error(validation_errors());
        }
    }



    public function quit($project_code)
    {
        $username = $this->session->userdata('username');

        $this->form_validation->set_data(array('project_code' => $project_code, 'username' => $username));
        $this->form_validation->set_rules('username', 'Identifiant', 'trim|required|max_length[255]|xss_clean|callback_is_member[' . $project_code . ']');
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {

            $this->ProjectMember_model->delete(array('username' => $username, 'project_code' => $project_code));

            redirect('projects/display/' . $project_code, 'location');

        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Régle de validation, Renvoie true si l'utilisateur a été invité à rejoindre
     * le projet.
     */
    public function is_invited($str, $project_code)
    {
        //$this->load->model('ProjectUser_model');
        $query_result = $this->ProjectUser_model->find(array('project_code' => $project_code, 'login' => $str));
        if ($query_result) {
            $user = $query_result[0];
            if ($user['is_validated'] == PGSQL_FALSE) {
                return TRUE;
            } else {
                $this->form_validation->set_message('is_invited', 'Cet utilisateur est déja membre du projet');
                return FALSE;
            }
        } else {
            $this->form_validation->set_message('is_invited', ' Cet utilisateur n\'a pas été invité à rejoindre les membres de ce projet');
            return FALSE;
        }
    }

    /**
     * Régle de validation de formulaire: Verifie si tout les partenaires sont bien définit dans DAPHNE.
     */
    public function partner_valid($input)
    {
        //$this->load->model(array('Partner_model'));
        if (is_array($input)) {
            foreach ($input as $value) {
                if (!$this->Partner_model->find(array('partner_code' => $value))) {
                    $this->form_validation->set_message('partner_valid', 'Le champ {field} contient des partenaires non-définis dans DAPHNE');
                    return FALSE;
                }
            }
            return TRUE;
        } elseif (is_string($input)) {
            if (!$this->Partner_model->find(array('partner_code' => $input))) {
                $this->form_validation->set_message('partner_valid', 'Le champ {field} contient des partenaires non-définis dans DAPHNE');
                return FALSE;
            }
            return TRUE;
        } elseif (empty($input)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Retourne Vrai si l'utilisateur n'est ni membre, ni invité du projet
     */
    public function not_already_member($username, $project_code)
    {
        //$this->load->model(array('DupRules_model'));
        $query_result = $this->ProjectMember_model->find(array('username' => $username, 'project_code' => $project_code));
        if ($query_result) { //Si on a trouvé un utilisateur appernant au groupe avec cet username
            $this->form_validation->set_message('not_already_member', "Cet utilisateur est déja membre du projet");
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Retourne Vrai si un organisme ne fait pas partie de la liste des partenaires
     */
    public function not_already_partner($str, $project_code)
    {
        //$this->load->model(array('ProjectPartner_model'));
        if ($this->ProjectPartner_model->find(array('partner_code' => $str, 'project_code' => $project_code))) {
            $this->form_validation->set_message('not_already_partner', "Cet organisme est déja partenaire du projet");
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Retourne Vrai si l'utilisateur est membre du projet
     */
    public function is_member($username, $project_code)
    {
        //$this->load->model(array('DupRules_model'));
        if ($this->ProjectMember_model->find(array('username' => $username, 'project_code' => $project_code))) {
            return TRUE;
        } else {
            $this->form_validation->set_message('is_member', "Cet utilisateur ne fait pas partie du projet");
            return FALSE;
        }
    }

    /**
     * Retourne Vrai si l'organisme est un partenaire du projet
     */
    public function is_project_partner($str, $project_code)
    {
        //$this->load->model(array('ProjectPartner_model'));
        if ($this->ProjectPartner_model->find(array('partner_code' => $str, 'project_code' => $project_code))) {
            return TRUE;
        } else {
            $this->form_validation->set_message('is_project_partner', "Cet organisme ne fait pas partie du projet");
            return FALSE;
        }
    }

    /**
     * Affiche les essais recherchés qui ne sont pas encore essais d'un projet donnée sous forme de liste d'options (Format JSON)
     */
    public function searched_not_project_trials($project_code)
    {
        $this->form_validation->set_data(array('term' => $this->input->post('term'), 'project_code' => $project_code));
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            //$this->load->model(array('TrialProject_model'));

            $searched_term = $this->input->post('term');
            $results = $this->TrialProject_model->like_not_project_trials($searched_term, $project_code);

            $not_trials = array_map(function ($result) {
                return array(
                    'name' => $result['trial_code'],
                    'value' => $result['trial_code']
                );
            }, $results);

            echo json_encode($not_trials);
        } else {
            $this->form_validation->set_error_delimiters('', '');
            echo json_encode(array('type' => 'error', 'message' => validation_errors()));
        }
    }

    /**
     * Retourne Vrai si un essai ne fait pas partie de la liste des essais
     */
    public function not_already_trial($str, $project_code)
    {
        //$this->load->model(array('TrialProject_model'));
        if ($this->TrialProject_model->find(array('trial_code' => $str, 'project_code' => $project_code))) {
            $this->form_validation->set_message('not_already_trial', "Cet selection est déja essai du projet");
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * Ajoute un essai à un projet
     */
    public function add_trial($project_code)
    {
        //$this->load->model(array('Trial_model', 'ProjectUser_model'));
		// Vérifie si l'utilisateur est admin ou gestionnaire de projet
        //if (!$this->session->userdata('admin') && !$this->ProjectUser_model->find(array('login' => $this->session->userdata('username'), 'project_code' => $project_code, 'is_leader' => PGSQL_TRUE))) {
		if (!$this->session->userdata('admin') && !$this->DupRules_model->find(array('login' => $this->session->userdata('username'), 'project_code' => $project_code, 'is_leader' => PGSQL_TRUE))) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $this->form_validation->set_data(array('project_code' => $project_code, 'trial' => $this->input->post('trial')));

        $this->form_validation->set_rules('trial', 'Essais', array('trim','required', 'alpha_dash','min_length[1]','max_length[50]', 'callback_trial_valid', 'xss_clean', 'callback_not_already_trial[' . $project_code . ']',
                array('trial_exist_callable', array($this->Trial_model, 'exist'))
            )
        );
        $this->form_validation->set_message('trial_exist_callable', 'Le {field} n\'éxiste pas.');
        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $trial = $this->input->post('trial');
            //$this->load->model(array('TrialProject_model'));
            $this->TrialProject_model->create(array('project_code' => $project_code, 'trial_code' => $trial));
            redirect('projects/display/' . $project_code, 'location');
        } else {
            show_error(validation_errors());
        }
    }

    public function trial_valid($input)
    {
        //$this->load->model(array('Trial_model'));
        if (is_array($input)) {
            foreach ($input as $value) {
                if (!$this->Trial_model->find(array('trial_code' => $value))) {
                    $this->form_validation->set_message('trial_valid', 'Le champ {field} contient des essais non-définis dans DAPHNE');
                    return FALSE;
                }
            }
            return TRUE;
        } elseif (is_string($input)) {
            if (!$this->Trial_model->find(array('trial_code' => $input))) {
                $this->form_validation->set_message('trial_valid', 'Le champ {field} contient des essais non-définis dans DAPHNE');
                return FALSE;
            }
            return TRUE;
        } elseif (empty($input)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Supprime un essai d'un projet
     */
    public function remove_trial($project_code)
    {
// Vérifie si l'utilisateur est admin ou gestionnaire de projet
        //$this->load->model(array('DupRules_model'));
        if (!$this->session->userdata('admin') && !$this->DupRules_model->find(array('login' => $this->session->userdata('username'), 'project_code' => $project_code, 'is_leader' => PGSQL_TRUE))) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $this->form_validation->set_data(array('project_code' => $project_code, 'trial' => $this->input->post('trial')));

        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');
        $this->form_validation->set_rules('trial', 'Essais',
            array('required', 'is_natural', 'callback_is_project_trial[' . $project_code . ']', 'xss_clean'));

        if ($this->form_validation->run()) {
            $trial = $this->input->post('trial');
            //$this->load->model(array('TrialProject_model'));
            $this->TrialProject_model->delete(array('project_code' => $project_code, 'trial_code' => $trial));
            redirect('projects/display/' . $project_code, 'location');
        } else {
            show_error(validation_errors());
        }
    }

	/**
     * Ajoute un dataset à un projet
     */
    public function add_dataset($project_code)
    {
		// Vérifie si l'utilisateur est admin ou gestionnaire de projet
		if (!$this->session->userdata('admin') && !$this->DupRules_model->find(array('login' => $this->session->userdata('username'), 'project_code' => $project_code, 'is_leader' => PGSQL_TRUE))) {
            set_status_header(401);
            show_error("vous n'êtes pas autorisé à lire cette page.", 401);
            return;
        }

        $this->form_validation->set_data(array('project_code' => $project_code, 'dataset_id' => $this->input->post('dataset')));

        $this->form_validation->set_rules('dataset_id', 'Jeu de données',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('dataset_exist_callable', array($this->Dataset_model, 'exist'))
            )
        );
        $this->form_validation->set_message('dataset_exist_callable', 'Le {field} n\'éxiste pas.');

        $this->form_validation->set_rules('project_code', 'Code du projet',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('project_exist_callable', array($this->Project_model, 'exist'))
            )
        );
        $this->form_validation->set_message('project_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $dataset_id = $this->input->post('dataset');
      			$login = $this->session->userdata('username');

      			//$duprules_id = $this->DupRules_model->find(array('project_code' => $project_code, 'login' => $login, 'dataset_id' => NULL))[0]['dup_id'];


            $this->Project_model->addLinkedDataset($dataset_id, $project_code);
            redirect('projects/display/' . $project_code, 'location');

            /*
      			if ($duprules_id) {
      				$new_data = array(
      					'dataset_id' => $dataset_id
      				);

      				$this->DupRules_model->update(array('dup_id' => $duprules_id), $new_data);
      				redirect('projects/display/' . $project_code, 'location');
      			}
      			else {
      				//$this->DupRules_model->create();
      			}
            */
        } else {
            show_error(validation_errors());
        }
    }

}
