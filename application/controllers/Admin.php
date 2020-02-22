<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Controleur de l'espace administrateur de DAPHNE
*
* @author Medhi Boulnemour <boulnemour.medhi@live.fr>
*/
class Admin extends MY_Controller {

	private $page_title = 'Administration';
	private $page_description =	'Cette page est réservée à l\'administration
																		de la base de données (DAPHNE)';

	/**
	* Contructeur du controleur
	* Il est éxécuté avant d'appler les pages de du controleur 'Admin'
	*/
	public function __construct() {
		parent::__construct();
		$this->load->model('Users_model');
		//$this->load->model('Group_model');
		$this->load->library('form_validation');
		$this->load->helper('html');

		//Verifie si l'utilisateur est un Administrateur
		if(!$this->session->userdata('admin'))
		{
			show_error("Vous n'êtes pas autorisé(e) à lire cette page!", 401);
			return;
		}
	}
	/**
	* Controleur de la page Administrateur
	*/
	public function index() {
		$this->view('admin/index', $this->page_title, $this->page_description);
	}
	/**
	* Controleur de la page Administrateur TABLES
	* L'Explorateur de tables de Daphne
	*
	* @param String $select_table Table à afficher ('root' par defaut)
	* @param Integer $debut_table Index du première élément à afficher
	*/
	public function tables($select_table='root', $debut_table=0){
		//Definit le nombre d'element à afficher par page

		$post_nb_elements = $this->input->post('nb_elements'); //recup de l entree nb_elements
		$user_nb_elements = $this->session->userdata('nb_elements_pagination');

		if(isset($post_nb_elements)) //si nb elements rempli
		{
			$nb_elements = $this->input->post('nb_elements');
			if($nb_elements <= 0) $nb_elements = 1;
			$this->session->set_userdata('nb_elements_pagination', $nb_elements);
		}
		elseif(isset($user_nb_elements))
		{
			$nb_elements = $this->session->userdata('nb_elements_pagination');
		}
		else {
			$nb_elements = 15;
			$this->session->set_userdata('nb_elements_pagination', $nb_elements);
		}
		$data['nb_elements'] = $nb_elements;


		//--- CREATION DE LA TABLE ---

		$this->load->library('table'); //charg. biblio table
		$this->load->model('Database_model'); //charg. le model DB

		if($select_table != 'root') //si on veut une table en particulier
		$tables = $this->Database_model->get_table($select_table); //le model retourne la table en fonction de la chaine select_table
		else
		$tables = $this->db->list_tables(); //sinon on retourne la liste de toutes les tables


		//GO TO : SEARCH:

		$go_to = $this->input->post('go_to'); //recupere la saisie goto
		if(isset($go_to) && !empty($go_to) && is_numeric($go_to)) //interet de !empty
		{
			$nb_elements = 1;
			$debut_table = $go_to - 1;
			$data['go_to'] = $go_to;
		}

		$display_tables = array_slice($tables, $debut_table, $nb_elements);

		// LE HEADER
		$header = array('#');

		if($select_table != 'root') $table_fields = $this->db->list_fields($select_table); //si table est != root alors on liste les champs
		else $table_fields = array('table_name');

		foreach ($table_fields as $field)
		{
			array_push($header, $field);
		}
		$this->table->set_heading($header); //fixe l en tete de la table

		$i = $debut_table + 1;
		foreach($display_tables as $table_row)
		{

			if($select_table == 'root')
			$table_row = '<a href="'.site_url('admin/tables').'/'.$table_row.'">'
			.$table_row.'</a>';

			$row = array($i);
			if(is_array($table_row)) //si table_row est un tableau
			{
				foreach ($table_row as $element) //parcours de chaque element
				{
					array_push($row, $element); //ajoute element au tab row
				}
			}
			else $row = array($i, $table_row); //tableau contenant l indice et la valeur de l element

			$this->table->add_row($row);
			$i++;
		}

		$template = array(
			'table_open' => '<table class="table">'
		);
		$this->table->set_template($template);

		$data['tables_html'] = $this->table->generate(); //génere le tableau html

		//--- GESTION DE LA PAGINATION ---

		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/tables').'/'.$select_table;
		$config['total_rows'] = count($tables);
		$config['per_page'] = $nb_elements;

		$this->pagination->initialize($config);
		$data['select_table'] =  $select_table;

		// Appel de la vue
		$this->view('admin/tables', $this->page_title, $this->page_description, $data);
	}
	/**
	*	Controleur de la page d'importation des données CSV
	*/
	public function import_csv() {
		$this->form_validation->set_rules('table', '"Table"', 'trim|encode_php_tags|required');

		if($this->form_validation->run()){
			// Configure
			// set the path where the files uploaded will be copied.
			// NOTE if using linux, set the folder to permission 777
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'csv|txt';

			//load the upload library
			$this->load->library('upload', $config);

			$data['upload_data'] = '';

			// if not successful, set the error message
			if (!$this->upload->do_upload('userfile')) {
				$data = array('error' => $this->upload->display_errors());
			}
			else { // else, set the success message
				$data['upload_data'] = $this->upload->data();

				$this->session->set_userdata('uploaded_file', $data['upload_data']);

				//traitement du fichier et importation dans la base
				$table = $this->input->post('table');
				$delimiter = $this->input->post('delimiter');

				if($this->input->post('header') == 'TRUE') $header = TRUE;
				else $header = FALSE;

				$file_path = $data['upload_data']["full_path"];

				$this->load->model('CSV_model');

				if($this->CSV_model->import_csv($table, $file_path, $delimiter, $header)){
					$data = array('success' => TRUE);
				}

				$this->session->unset_userdata('uploaded_file');
				//remove file
				unlink($file_path);
			}

		}
		$data['list_tables'] = $this->db->list_tables();

		$scripts = array('selectize', 'selectize_import_csv');
		$this->view('admin/import_csv', $this->page_title, $this->page_description, $data, $scripts);
	}
	/**	Controleur de la page members
	*		Cette page gère les utilisateurs de DAPHNE,
	*		ainsi que les demandes d'inscription
	*/
	public function members() {
		$this->load->model('Partner_model');
		$users_list = $this->Users_model->get_all_users_data();

		$data['figure_list'] = array();
		foreach ($users_list as $user) {
			//On renome les variables pour utiliser le template
			$tmp['member_status'] = $user['admin'];
			$tmp['member_username'] = $user['username'];
			$tmp['member_first_name'] = $user['first_name'];
			$tmp['member_last_name'] = $user['last_name'];
			$tmp['member_organization'] = ($user['partner']) ? $this->Partner_model->find(array('partner_code' => $user['partner']))[0]['partner_name'] : null;
			$tmp['member_email'] = $user['email'];
			$tmp['creation_date'] = $user['created_on_readable'];

			//On charge toute les figures de tout les utilisateurs dans le tableau 'figure_list'
			array_push($data['figure_list'], $this->load->view('admin/member_figure', $tmp, TRUE));
		}

		$scripts = array('jquery.shuffle.modernizr', 'members', 'selectize', );
		$this->view('admin/members', $this->page_title, $this->page_description, $data, $scripts);
	}
	/**
	* Supprimer un membre de la base de données.
	* @param String $login Pseudo du membre.
	*/
	public function remove_user($login)
	{
		if ($this->Users_model->remove_user($login)) echo TRUE;
		else echo FALSE;
	}
	/**
	* Supprimer une demande d'inscription de la base de données.
	* @param String $login Pseudo de la demande.
	*/
	public function remove_subscriber($login)
	{
		if ($this->Users_model->remove_subscriber($login)) echo TRUE;
		else echo FALSE;
	}
	/**
	* Met à jour le status d'un membre (admin/user).
	* @param String $login Pseudo du membre.
	*/
	public function change_admin_status($login)
	{
		$value = $this->input->get('new_status');
		if ($this->Users_model->change_status($login, $value))	echo TRUE;
		else	echo FALSE;
	}
}
