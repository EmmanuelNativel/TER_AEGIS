<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Controleur principal de l'application web DAPHNE
*
* @author Medhi Boulnemour <boulnemour.medhi@live.fr>
*/
class Welcome extends MY_Controller {

	/**
	* Contructeur du controleur
	* Il est éxécuté avant d'appler les pages de du controleur 'Welcome'
	*/
	public function __construct() {
		parent::__construct();
		$this->load->model('Users_model');
		$this->load->library('form_validation');
		//$this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');

		$this->load->add_package_path(APPPATH.'third_party/ion_auth/');
		$this->load->library('ion_auth');
	}

	/**
	* Index Page for this controller.
	*
	* Maps to the following URL
	* 		http://example.com/index.php/welcome
	*	- or -
	* 		http://example.com/index.php/welcome/index
	*	- or -
	* Since this controller is set as the default controller in
	* config/routes.php, it's displayed at http://example.com/
	*
	* So any other public methods not prefixed with an underscore will
	* map to /index.php/welcome/<method_name>
	* @see https://codeigniter.com/user_guide/general/urls.html
	*/
	public function index() {
		$this->view();
	}
	/**
	* Controleur de la page A propos
	*/
	public function about() {
		$this->view('welcome/about', 'A Propos...');
	}
	/**
	* Controleur de la page contact
	*/
	public function contact() {
		$this->view('welcome/contact', 'Nous Contacter');
	}
	/**
	* Controleur de la page de connexion
	*/
	public function gestiondata() {
		$this->view('welcome/gestiondata', 'Une bonne gestion des données');
	}
	/**
	* Controleur de la page de gestiondata
	*/
	public function acquisition() {
		$this->view('welcome/acquisition', 'Acquisition des données');
	}
	/**
	* Controleur de la page acquisition de données
	*/
	public function traitement() {
		$this->view('welcome/traitement', 'Traitement des données');
	}
	/**
	* Controleur de la page traitement de données
	*/
	public function partage() {
		$this->view('welcome/partage', 'Partage des données');
	}
	/**
	* Controleur de la page partage de données
	*/
	public function valorisation() {
		$this->view('welcome/valorisation', 'Valorisation des données');
	}
	/**
	* Controleur de la page valorisation de données
	*/
	
	public function mission() {
		$this->view('welcome/mission', 'Notre mission et notre vision');
	}
	/**
	* Controleur de la page valorisation de données
	*/
	
	public function login() {

		//Redirige l'utilisateur déja connecté
		//if ($this->session->userdata('connected') == TRUE) {
		if ($this->ion_auth->logged_in()) {
			//redirect("Welcome/index", "location");
			redirect("welcome/index", "location");
			return;
		}

		$data['pseudo'] = xss_clean(trim($this->input->post('pseudo')));
		$data['mdp'] = $this->input->post('mdp');

		//Règles du formulaire de connexion
		$this->form_validation->set_rules('pseudo', '"Identifiant"','trim|required|encode_php_tags|xss_clean');
		$this->form_validation->set_rules('mdp', '"Mot de passe"', 'required|encode_php_tags'
		.'|callback_ionAuthLoginVerification['.$data["pseudo"].']|xss_clean');

		if($this->form_validation->run()) {
		//if ($this->ion_auth->login($data['pseudo'], $data['mdp'], false)) {

			//On enregistre les données de l'utilisateur pour la session
			$user_data = $this->Users_model->get_user_data($data['pseudo']);

			$user_data['connected'] = TRUE;
			if ($user_data['admin'] == PGSQL_TRUE)	$user_data['admin'] = TRUE;
			else $user_data['admin'] = FALSE;

			$this->session->set_userdata($user_data);

			// Update de last_login dans la bdd
			$new_user_data = array(
				'last_login' => time()
			);
			$this->Users_model->set_user_data($data['pseudo'], $new_user_data);

			//Redirection
			//redirect("Welcome/index", "location");
			redirect("welcome/index", "location");
			return;
		}
		//Appel de la vue
		$this->view('welcome/login', 'connexion', 'Espace de connexion', $data);

	}


	/**
	* Controleur de la page d'inscription
	*/
	public function signin() {

		//Redirige l'utilisateur déja connecté
		if ($this->ion_auth->logged_in()) {
			redirect("Welcome/index", "location");
			return;
		}

		//Règles du formulaire d'inscription
		$this->form_validation->set_rules('pseudo', '"Identifiant"','trim|required|min_length[3]|xss_clean'
		.'|max_length[30]|alpha_dash'
		.'|encode_php_tags'
		.'|is_unique[users.username]');
		$this->form_validation->set_rules('mdp', '"Mot de passe"',	'required|min_length[5]|xss_clean'
		.'|max_length[52]|alpha_dash'
		.'|encode_php_tags'
		.'|matches[mdp_conf]');
		$this->form_validation->set_rules('mdp_conf', '"Confirmation du mot de passe"','required|xss_clean'
		.'|min_length[5]|max_length[52]'
		.'|alpha_dash|encode_php_tags');
		$this->form_validation->set_rules('email', '"E-mail"', 'required|valid_email|xss_clean'
		.'|is_unique[users.email]');
		$this->form_validation->set_rules('first_name', '"Prénom"', 'required|trim|min_length[2]|xss_clean'
		.'|max_length[50]|alpha_dash'
		.'|encode_php_tags');
		$this->form_validation->set_rules('last_name', '"Nom"', 'required|trim|min_length[2]|max_length[50]|xss_clean'
		.'|alpha_dash|encode_php_tags');
		$this->form_validation->set_rules('organization', '"Organisation"', 'trim|is_natural|xss_clean'
		.'|encode_php_tags');

		$data['username'] = $this->input->post('pseudo');
		$data['mdp'] = $this->input->post('mdp');
		$data['mdp_conf'] = $this->input->post('mdp_conf');
		$data['email'] = $this->input->post('email');
		$data['first_name'] = $this->input->post('first_name');
		$data['last_name'] = $this->input->post('last_name');
		$data['organization'] = $this->input->post('organization');

		//On recupère la liste des organisations
		$this->load->model('Partner_model');
		$data['list_partner'] = $this->Partner_model->find();

		if($this->form_validation->run()){


			$additional_data = array(
				 'ip_address' => $this->input->ip_address(),
				 'first_name' => $data['first_name'],
				 'last_name' => $data['last_name'],
				 'created_on' => time(),
				 'created_on_readable' => date('Y-m-d H:i:s'),
				 'last_login' => time(),
				 'active' => 1
			);

			//L'organisation est facultative
			if($data['organization']) $additional_data += array('partner' => $data['organization']);

			$this->ion_auth->register($data['username'], $data['mdp'], $data['email'], $additional_data);

			//On enregistre les données de l'utilisateur pour la session
			$user_data = $data;

			//On retire le mot de passe des futur variables de session
			unset($user_data['mdp']);
			unset($user_data['mdp_conf']);

			$user_data['connected'] = TRUE;
			$this->session->set_userdata($user_data);

			// Alerte les administrateurs (Notifications & Email)
			$this->load->library('email');
			$this->load->model('Notification_model');

			$this->email->from($user_data['email'], $user_data['username']);
			$this->email->to(AEGIS_MAIL);

			$this->email->subject("[Inscription] Un nouveau membre vient de s'inscrire sur DAPHNE ".$user_data['username']);
			$this->email->message(
				"Un nouvel utilisateur de DAPHNE s'est inscrit.\n\n
				login : ".$data['username']."\n
				email : ".$data['email']."\n
				first_name : ".$data['first_name']."\n
				last_name : ".$data['last_name']."\n
				organization : ".$data['organization']
			);

			//TODO: N'envoie plus de mail pour l'instant ni ne notifie les admin
			// ===============================================================================

			//$this->email->send();

			/*
			$admins = $this->Users_model->find(array('admin' => PGSQL_TRUE));
			foreach ($admins as $admin) {
				$this->Notification_model->create(array(
					'target_login' => $admin['login'],
					'notification_type' => NEW_USER,
					'sender_login' => $user_data['login'],
					'created_time' => 'NOW()',
					'ressource' => $user_data['login']
				));
			}
			*/

			//Redirection
			redirect("Welcome/index", "location");
			return;
		}
		$scripts = array(	'selectize',
											'verifie_pseudo',
											'init_organization_select'
										);
		$this->view('welcome/signin', 'Inscription', 'Formulaire d\'inscription', $data, $scripts);
	}
	/**
	* Controleur de la page de deconnexion
	*/
	public function logout() {

		$this->session->sess_destroy();
		$this->ion_auth->logout();

		//Redirection
		redirect("Welcome/index", "location");
		return;
	}
	/**
	 * Cette fonction affiche une chaine de carcatère qui est l'entête
	 * d'une table donnée de DAPHNE au format CSV.
	 * S'utilise via le navigateur "url-du-site/(index.php)/welcome/header_csv/nom-de-la-table"
	 * @param String $table nom de la table dans la base de données DAPHNE
	 */
	// public function header_csv($table){
	// 	$this->load->model('database_model');
	// 	$result = $this->database_model->get_header($table);
	// 	// var_dump($result);
	// 	$test = array();
	// 	foreach ($result as $value) {
	// 		array_push($test, $value['column_name']);
	// 	}
	// 	echo implode(';', $test);
	// 	return;
	// }
	/**
	 * Page de téléchargement des tables de DAPHNE au format CSV
	 */
	// public function table_csv($table=NULL)
	// {
	// 	if ($table == NULL) {
	// 		$tables = $this->db->list_tables();
	// 		echo "<h4>Usage:</h4>\".../table_csv/nom-de-la-table\" <br />";
	// 		echo "<h4>Tables de daphne:</h4>";
	// 		foreach ($tables as $el) {
	// 			echo "<a href=".site_url("Welcome/table_csv/".$el).">".$el."</a><br />";
	// 		}
	// 		return;
	// 	}
	// 	$this->load->helper('download');
	// 	$this->load->dbutil();
	//
	// 	$query = $this->db->query("SELECT * FROM ".$table);
	//
	// 	$delimiter = ";";
	// 	$newline = "\r\n";
	// 	$enclosure = '"';
	//
	// 	$data = $this->dbutil->csv_from_result($query, $delimiter, $newline, $enclosure);
	//
	// 	$name = $table.'.csv';
	//
	// 	force_download($name, $data);
	// }
}
