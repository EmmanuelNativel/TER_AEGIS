<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_viewer extends MY_Controller
{
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
        $this->load->model(array('data_viewer_query_tool_model'));
        $this->load->model(array('data_viewer_query_user_model'));
        $this->load->helper('html');

    }


    /**
     *page de visualisation
     **/
	function index()
	{
		$page['title'] = "Visualisation les tables de données";
		$page['subtitle'] = "Visualisation graphique des projets DAPHNE";

		$this->view('data_viewer/visualize', $page['title'], $page['subtitle']);
	}

  public function visualize()
  {
		$page['title'] = "Visualisation des tables de données";
		$page['subtitle'] = "Visualisation graphique des projets DAPHNE";

		$scripts = array('bootstrap-datepicker.min', 'bootstrap-datepicker.fr.min', 'bootstrap-select-ajax-plugin',
		'cdn.datatables.netvbsdt-1.10.16datatables', 'query-builder.standalone', 'selectize');
		if (!isset($_SESSION['pseudo'])){
			$_SESSION['pseudo'] = $this->session->userdata('username');
			// $_SESSION['pseudo'] = 'sandrine.auzoux';
		}

		// chargement des requetes disponible
		if (!isset($this->data['sql'])){
			$this->initialize_sql_request();
		}

		// chargement des tables disponible
		if (!isset($this->data['table'])){
			$this->loading_table_available();
		}

		// chargement des projects disponible
		// charge les projects disponible si la la table n'est pas encore chargé ...
		if (!isset($this->data['project']['available'])){
			$this->loading_project_available($_SESSION['pseudo']);
			$this->initialize_loading_data($this->data['table']['available'],$_SESSION['project']['available'],null);
		}

		if (!isset($this->data['tab']['query_tool']['load'])){
			$this->initialize_query_tools();
		}
		$this->view('data_viewer/visualize_forms', $page['title'], $page['subtitle'], $this->data, $scripts);
  }

	/*
	*
	*
	* Fonctions de requetes et de recuperations de données de la base de données DAPHNE
	*
	*
	*/

	// charge les tables disponibles a la consultation
	public function loading_table_available(){
		$this->data['table'] = array();
		$this->data['table']['available'] = array("accession","taxo","lot","trial","device","observation","sample","analysis","route","equipment","product","weather");
	}

	// charge les projets affectés à un utilisateur,
	// l'utilisateur ne pourra visualiser des informations que sur les projets qui lui sont affectés
	public function loading_project_available($user)
	{
		$this->data['project'] = array();

		$data['sql']['request'] = "	SELECT dr.project_code
									FROM public.dup_rules dr
									JOIN public.users u ON u.username = dr.username
									WHERE u.username ='".$user."'";
		$this->data['project']['available'] = array();
		$data['query'] = array();
		//	On lance la requête
		$query = $this->db->query($data['sql']['request'], $data['query']);
		foreach($query->result() as $ligne){
			$this->data['project']['available'][] = $ligne->project_code;
		}
		$_SESSION['project']['available'] = $this->data['project']['available'];
	}

	public function initialize_query_tools(){

		$this->data['tab']['query_tool']['query']= array();
		$this->data['tab']['query_tool']['load']['globalJSON'] = array();
		$this->data['tab']['query_tool']['load']['userJSON'] = array();
		$this->data['tab']['query_tool']['load']['popularJSON'] = array();
		$this->data['query_tool']['query']['index'] = array();

	}

	// pre-chargement des requetes sql
	public function initialize_sql_request(){

		// modele
		/*
		// --------------------
		// requete table
		// --------------------
		$this->data['sql_table_name']['table'] = "table_name";
		$this->data['sql_new_index']['table'] = array();
		$this->data['sql']['table'] = array();
		$this->data['sql']['table']['select'] = array();
		$this->data['sql']['table']['from'] = array();
		$this->data['sql']['table']['where']['question'] = array();
		$this->data['sql']['table']['where']['answer'] = array();
		$this->data['sql']['table']['end'] = array(";");
		$this->data['sql']['table']['request'] = "";
		*/

		$this->data['sql_table_index_belong_to'] = array();

		// --------------------
		// requete accession
		// --------------------
		$this->data['sql_table_name']['accession'] = "accession";
		$this->data['sql_table_need_project']['accession'] = "true";
		// a remplir

		$this->data['sql_new_index']['accession'] = array(); //array("partner.partner_name", "country.country", "site.site_name", "taxo.taxo_name");
		$this->data['sql_table_index_belong_to']['accession']['partner_name'] = 'partner';
		$this->data['sql_table_index_belong_to']['accession']['country'] = 'country';
		$this->data['sql_table_index_belong_to']['accession']['site_name'] = 'site';
		$this->data['sql_table_index_belong_to']['accession']['taxo_name'] = 'taxo';

		$this->data['sql']['accession'] = array();
		$this->data['sql']['accession']['select'] = array("SELECT accession.* ");
		$this->data['sql']['accession']['from'] = array("FROM accession ",
			"NATURAL JOIN lot l ",
			"NATURAL JOIN lot_unit l_u ",
			"NATURAL JOIN exp_unit e_u ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");

		$this->data['sql']['accession']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['accession']['where']['answer'] = array();
		$this->data['sql']['accession']['end'] =	array(";");
		$this->data['sql']['accession']['request'] = "";

		// --------------------
		// requete taxo
		// --------------------
		$this->data['sql_table_name']['taxo'] = "taxo";
		$this->data['sql_table_need_project']['taxo'] = "true";

		$this->data['sql_new_index']['taxo'] = array();
		$this->data['sql_table_index_belong_to']['taxo'] = array();

		$this->data['sql']['taxo'] = array();
		$this->data['sql']['taxo']['select'] = array("SELECT taxo.* ");
		$this->data['sql']['taxo']['from'] = array("FROM taxo ",
			"NATURAL JOIN accession a ",
			"NATURAL JOIN lot l ",
			"NATURAL JOIN lot_unit l_u ",
			"NATURAL JOIN exp_unit e_u ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");
		$this->data['sql']['taxo']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['taxo']['where']['answer'] = array();
		$this->data['sql']['taxo']['end'] = array(";");
		$this->data['sql']['taxo']['request'] = "";

		// --------------------
		// requete lot
		// --------------------
		$this->data['sql_table_name']['lot'] = "lot";
		$this->data['sql_table_need_project']['lot'] = "true";

		$this->data['sql_new_index']['lot'] = array("accession.accession_code");
		$this->data['sql_table_index_belong_to']['lot']['accession_code'] = 'accession';

		$this->data['sql']['lot'] = array();
		$this->data['sql']['lot']['select'] = array("SELECT lot.* ");
		$this->data['sql']['lot']['from'] = array("FROM lot ",
			"NATURAL JOIN lot_unit l_u ",
			"NATURAL JOIN exp_unit e_u ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");
		$this->data['sql']['lot']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['lot']['where']['answer'] = array();
		$this->data['sql']['lot']['end'] = array(";");
		$this->data['sql']['lot']['request'] = "";

		// --------------------
		// requete trial
		// --------------------
		$this->data['sql_table_name']['trial'] = "trial";
		$this->data['sql_table_need_project']['trial'] = "true";

		$this->data['sql_new_index']['trial'] = array("site.site_name");
		$this->data['sql_table_index_belong_to']['trial']['site_name'] = 'site';

		$this->data['sql']['trial'] = array();
		$this->data['sql']['trial']['select'] = array("SELECT trial.* ");
		$this->data['sql']['trial']['from'] = array("FROM trial ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");
		$this->data['sql']['trial']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['trial']['where']['answer'] = array();
		$this->data['sql']['trial']['end'] = array(";");
		$this->data['sql']['trial']['request'] = "";

		// --------------------
		// requete device
		// --------------------
		$this->data['sql_table_name']['device'] = "wp";
		$this->data['sql_table_need_project']['device'] = "true";

		$this->data['sql_new_index']['device'] = array("project.project_name");
		$this->data['sql_table_index_belong_to']['device']['project_name'] = 'project';

		$this->data['sql']['device'] = array();
		$this->data['sql']['device']['select'] = array("SELECT wp.*");
		// t_p.trial_code
		$this->data['sql']['device']['from'] = array("FROM wp ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");

		$this->data['sql']['device']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['device']['where']['answer'] = array();
		$this->data['sql']['device']['end'] = array(";");
		$this->data['sql']['device']['request'] = "";

		// --------------------
		// requete observation
		// --------------------
		$this->data['sql_table_name']['observation'] = "obs_unit";
		$this->data['sql_table_need_project']['observation'] = "true";

		$this->data['sql_new_index']['observation'] = array("exp_unit.unit_code", "sample_stage.st_name", "variable.method_code");
		$this->data['sql_table_index_belong_to']['observation']['unit_code'] = 'exp_unit';
		$this->data['sql_table_index_belong_to']['observation']['st_name'] = 'sample_stage';
		$this->data['sql_table_index_belong_to']['observation']['method_code'] = 'variable';

		$this->data['sql']['observation'] = array();
		$this->data['sql']['observation']['select'] = array("SELECT obs_unit.* ");
		$this->data['sql']['observation']['from'] = array("FROM obs_unit ",
			"NATURAL JOIN sample_stage s_s ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");

		$this->data['sql']['observation']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['observation']['where']['answer'] = array();
		$this->data['sql']['observation']['end'] = array(";");
		$this->data['sql']['observation']['request'] = "";

		// --------------------
		// requete sample
		// --------------------
		$this->data['sql_table_name']['sample'] = "sample";
		$this->data['sql_table_need_project']['sample'] = "true";

		$this->data['sql_new_index']['sample'] = array("exp_unit.unit_code", "exp_unit.trial_code");
		$this->data['sql_table_index_belong_to']['sample']['unit_code'] = 'exp_unit';
		$this->data['sql_table_index_belong_to']['sample']['trial_code'] = 'exp_unit';

		$this->data['sql']['sample'] = array();
		$this->data['sql']['sample']['select'] = array("SELECT * ");
		$this->data['sql']['sample']['from'] = array("FROM sample ",
			"NATURAL JOIN obs_unit o_u ",
			"NATURAL JOIN sample_stage s_s ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");
		$this->data['sql']['sample']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['sample']['where']['answer'] = array();
		$this->data['sql']['sample']['end'] = array(";");
		$this->data['sql']['sample']['request'] = "";
		//sample

		// --------------------
		// requete analysis
		// --------------------
		$this->data['sql_table_name']['analysis'] = "analysis";
		$this->data['sql_table_need_project']['analysis'] = "true";

		$this->data['sql_new_index']['analysis'] = array("sample.sample_code", "variable.method_code");
		$this->data['sql_table_index_belong_to']['analysis']['sample_code'] = 'sample';
		$this->data['sql_table_index_belong_to']['analysis']['method_code'] = 'variable';

		$this->data['sql']['analysis'] = array();
		$this->data['sql']['analysis']['select'] = array("SELECT analysis.* ");
		$this->data['sql']['analysis']['from'] = array("FROM analysis ",
			"NATURAL JOIN sample s ",
			"NATURAL JOIN obs_unit o_u ",
			"NATURAL JOIN sample_stage s_s ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");
		$this->data['sql']['analysis']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['analysis']['where']['answer'] = array();
		$this->data['sql']['analysis']['end'] = array(";");
		$this->data['sql']['analysis']['request'] = "";

		// --------------------
		// requete route
		// --------------------
		$this->data['sql_table_name']['route'] = "itk";
		$this->data['sql_table_need_project']['route'] = "true";

		$this->data['sql_new_index']['route'] = array("exp_unit.unit_code", "exp_unit.trial_code", "variable.method_code");
		$this->data['sql_table_index_belong_to']['route']['unit_code'] = 'exp_unit';
		$this->data['sql_table_index_belong_to']['route']['trial_code'] = 'exp_unit';
		$this->data['sql_table_index_belong_to']['route']['method_code'] = 'variable';

		$this->data['sql']['route'] = array();
		$this->data['sql']['route']['select'] = array("SELECT itk.* ");
		$this->data['sql']['route']['from'] = array("FROM itk ",
			"NATURAL JOIN exp_unit e_u ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");
		$this->data['sql']['route']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['route']['where']['answer'] = array();
		$this->data['sql']['route']['end'] = array(";");
		$this->data['sql']['route']['request'] = "";

		// --------------------
		// requete equipment
		// --------------------
		$this->data['sql_table_name']['equipment'] = "equipment";
		$this->data['sql_table_need_project']['equipment'] = "false";

		$this->data['sql_new_index']['equipment'] = array();
		$this->data['sql_table_index_belong_to']['equipment'] = array();

		$this->data['sql']['equipment'] = array();
		$this->data['sql']['equipment']['select'] = array("SELECT equipment.* ");
		/*
		$this->data['sql']['equipment']['from'] = array("FROM equipment ",
			"NATURAL JOIN itk_equipment i_e ",
			"NATURAL JOIN itk i ",
			"NATURAL JOIN exp_unit e_u ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");
		$this->data['sql']['equipment']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");
		*/
		$this->data['sql']['equipment']['from'] = array("FROM equipment ",
			"NATURAL LEFT JOIN itk_equipment i_e ",
			"NATURAL LEFT JOIN itk i ",
			"NATURAL LEFT JOIN exp_unit e_u ",
			"NATURAL LEFT JOIN trial_project t_p ",
			"NATURAL LEFT JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL LEFT JOIN public.users u ",);
		$this->data['sql']['equipment']['where']['question'] = array();
		$this->data['sql']['equipment']['where']['answer'] = array();
		$this->data['sql']['equipment']['end'] = array(";");
		$this->data['sql']['equipment']['request'] = "";

		// --------------------
		// requete product
		// --------------------
		$this->data['sql_table_name']['product'] = "product";
		$this->data['sql_table_need_project']['product'] = "true";

		$this->data['sql_new_index']['product'] = array();
		$this->data['sql_table_index_belong_to']['product'] = array();

		$this->data['sql']['product'] = array();
		$this->data['sql']['product']['select'] = array("SELECT product.* ");
		$this->data['sql']['product']['from'] = array("FROM product ",
			"NATURAL JOIN itk_product i_p ",
			"NATURAL JOIN itk i ",
			"NATURAL JOIN exp_unit e_u ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");
		$this->data['sql']['product']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['product']['where']['answer'] = array();
		$this->data['sql']['product']['end'] = array(";");
		$this->data['sql']['product']['request'] = "";

		// --------------------
		// requete weather
		// --------------------
		$this->data['sql_table_name']['weather'] = "weather_day";
		$this->data['sql_table_need_project']['weather'] = "true";

		$this->data['sql_new_index']['weather'] = array("ws.wsname","variable.method_code");
		$this->data['sql_table_index_belong_to']['weather']['wsname'] = 'ws';
		$this->data['sql_table_index_belong_to']['weather']['method_code'] = 'variable';

		$this->data['sql']['weather'] = array();
		$this->data['sql']['weather']['select'] = array("SELECT weather_day.* ");
		$this->data['sql']['weather']['from'] = array("FROM weather_day ",
			"NATURAL JOIN dataset_wd d_w ",
			"NATURAL JOIN dataset_obs d_o ",
			"NATURAL JOIN obs_unit o_u ",
			"NATURAL JOIN sample_stage s_s ",
			"NATURAL JOIN trial_project t_p ",
			"NATURAL JOIN project p ",
			"NATURAL JOIN dup_rules d_u ",
			"NATURAL JOIN public.users u ");
		$this->data['sql']['weather']['where']['question'] = array("WHERE u.username = ",
			"AND p.project_code = ");

		$this->data['sql']['weather']['where']['answer'] = array();
		$this->data['sql']['weather']['end'] = array(";");
		$this->data['sql']['weather']['request'] = "";

	}

	// initialisation des variables ou les données seront sauvegardé
	public function initialize_loading_data($table, $project, $clause){
		$this->data['dataProject'] = array();
		$this->aggresive_loading_data($table, $project, $clause);
	}

	public function aggresive_loading_data($table, $project, $clause){

		// on enleve la 1ere table de la variable table
		$actualTable = array_shift($table);
		$this->data['dataProject'][$actualTable] = array();
		// puis on charge la 1ere table, elle est ensuite sauvegardé dans des variables.

		$this->loading_dataRequest($actualTable, $project, $clause);

		// lancement recursif tant que la variable table possède des tables à charger.
		if (sizeof($table) > 0 ){
			$this->aggresive_loading_data($table, $project, $clause);
		}
	}
	// chargement des données et affectation aux variables prévu à cette effet
	// le chargement est en aggresive loading car la page ne propose pas de form submit pour plus de dynamisme
	// pour effectuer du lazy loading sur une certain table il faut envoyer une requete post en ajax depuis la page php view_data_project
	// quand un projet et selectionner par l'utilisateur lancer la requete post.

	public function loading_dataRequest($table, $project, $clause)
	{
		// pattern a faire
		if ((sizeof($this->data['sql'][$table]['select']) != 0) && (sizeof($this->data['sql'][$table]['from'])) ){
			// initialise query
			$query = '';
			$this->data['query']['body'] = array();

			// on requete la liste des colonnes disponible
			$this->data['resultColumn'] = $this->getColumnsFields($query, $table);

			//print_r($table);
			if (sizeof($this->data['sql_new_index'][$table]) > 0){
				$resultForeignKeys = $this->getColumnsForeignKey($query, $table);
			}else{
				$resultForeignKeys = '';
				$this->data['listForeignKeys'][$table] = '';
			}
			// la requete est créer dans cette fonction
			$this->createRequest($query, $table, $resultForeignKeys);
			if($this->data['sql_table_need_project'][$table] != "true"){
				$this->changeRequestClause($table, "noProjectNeeded");
				$this->data['dataProject'][$table]['project']["noProjectNeeded"] = array();
				$this->loading_data($query, $table, "noProjectNeeded");
			}else{
				while(sizeof($project) > 0 ){
					$actualProject = array_shift($project);
					// on affecte les données utilisateur à la requete déjà crée
					$this->changeRequestClause($table, $actualProject);

					$this->data['dataProject'][$table]['project'][$actualProject] = array();

					// on requete les valeurs
					$this->loading_data($query, $table, $actualProject);
				}
			}
			$this->data['tableLoaded'][$table] = "true";
		}else{
			$this->data['tableLoaded'][$table] = "false";
		}
	}
	public function getColumnsFields($query, $table){

		$this->data['resultColumn'] = '';
		$i = 0;

		$request = "SELECT *
					FROM ".$this->data['sql_table_name'][$table];


		// on lance la requete
		$query = $this->db->query($request, $this->data['query']['body']);

		$this->data['resultColumn'] = $query->list_fields();

		// on sauvegarde les colonnes disponible dans une variable de session
		if (!isset($_SESSION['indexProject'][$table])){
			$_SESSION['indexProject'][$table] = $this->data['resultColumn'];
		}
		$this->data['dataProject'][$table]['column']['size'] = sizeof($this->data['resultColumn']);

		//affectation de la variable sql_table_index_belong_to, dans cette variable est stocké la table de la colonne
		for ($i =0;$i<sizeof($this->data['resultColumn']);$i++){
			$this->data['sql_table_index_belong_to'][$table][$this->data['resultColumn'][$i]] = $table;
		}

		$query->free_result();

		return $this->data['resultColumn'];
	}

	public function getColumnsForeignKey($query, $table){

		$resultForeignKeys = '';
		$this->data['listForeignKeys'][$table] = array();

		$i = 0;
		$j = 0;
		$request = "SELECT
						conrelid::regclass AS table_id
					,	CASE WHEN pg_get_constraintdef(c.oid) LIKE 'FOREIGN KEY %'
						THEN substring(pg_get_constraintdef(c.oid), 14, position(')' in pg_get_constraintdef(c.oid))-14) END AS column_id
					,	CASE WHEN pg_get_constraintdef(c.oid) LIKE 'FOREIGN KEY %'
						THEN substring(pg_get_constraintdef(c.oid),
						position(' REFERENCES ' in pg_get_constraintdef(c.oid))+12,
						position('(' in substring(pg_get_constraintdef(c.oid), 14))-
						position(' REFERENCES ' in pg_get_constraintdef(c.oid))+1) END AS FK_table
					,	CASE WHEN pg_get_constraintdef(c.oid) LIKE 'FOREIGN KEY %'
						THEN substring(pg_get_constraintdef(c.oid), position('(' in substring(pg_get_constraintdef(c.oid), 14))+14,
						position(')' in substring(pg_get_constraintdef(c.oid),
						position('(' in substring(pg_get_constraintdef(c.oid), 14))+14))-1) END AS FK_id
					FROM
						pg_constraint c
						JOIN pg_namespace n ON n.oid = c.connamespace
					WHERE
						contype IN ('f', 'p ')
						AND pg_get_constraintdef(c.oid) LIKE 'FOREIGN KEY %'
						AND c.conrelid = 'public.".$this->data['sql_table_name'][$table]."'::regclass
						AND c.contype = 'f'
						ORDER  BY pg_get_constraintdef(c.oid), conrelid::regclass::text, contype DESC;";

		// on lance la requete
		$query = $this->db->query($request, $this->data['query']['body']);
		$fields = $query->list_fields();
		foreach($query->result() as $ligne){
			if ($ligne->$fields[0] != $ligne->$fields[2]){
				for($i=0;$i<sizeof($fields);$i++){
					$resultForeignKeys[$j][$fields[$i]] = $ligne->$fields[$i];
				}
				//fields[1] contient les l'identifiant des colonnes référent des clé étrangère (column_id)
				$this->data['listForeignKeys'][$table][$j] =  $ligne->$fields[1];
				$j = $j + 1;
			}
		}
		$query->free_result();
		return $resultForeignKeys;
	}

	public function getColumnsType($query, $primary, $primary_table, $foreign){
		$i = 0;
		$tmp_foreign = array();
		$tmp_foreign['foreign_to_split'] = array();
		$tmp_foreign['table'] = array();
		$tmp_foreign['column'] = array();

		$request = "SELECT column_name, data_type
					FROM information_schema.columns
					WHERE table_name = '".$this->data['sql_table_name'][$primary_table]."'
					AND column_name IN (
						SELECT column_name
						FROM information_schema.columns
						WHERE table_name = '".$this->data['sql_table_name'][$primary_table]."'
						AND column_name = '".$primary[0]."' ";


		for($i=1;$i<sizeof($primary)-1;$i++){
			$request = 	$request."OR table_name = '".$this->data['sql_table_name'][$primary_table]."'
					AND column_name = '".$primary[$i]."' ";
		}
		$request = 	$request."OR table_name = '".$this->data['sql_table_name'][$primary_table]."'
					AND column_name = '".$primary[sizeof($primary)-1]."');";

		for($i=0;$i<sizeof($foreign);$i++){
			$tmp_foreign['foreign_to_split'][$i] = explode('.',$foreign[$i]);
			$tmp_foreign['table'][$i] = $tmp_foreign['foreign_to_split'][$i][0];
			$tmp_foreign['column'][$i] = $tmp_foreign['foreign_to_split'][$i][1];
		}

		//	On lance la requête
		$query = $this->db->query($request, $this->data['query']['body']);
		$j = 0;
		$fields = $query->list_fields();

		foreach($query->result() as $ligne){
			$this->data['type_column'][$primary_table][$ligne->$fields[0]] = $ligne->$fields[1];
		}

		$query->free_result();
	}

	public function createRequest($query, $table, $resultForeignKeys){

		$i = 0;
		$tmp_str = array();
		if($resultForeignKeys != ''){
			$this->data['resultColumn'] = array_diff($this->data['resultColumn'],$this->data['listForeignKeys'][$table]);
			$this->data['resultColumn'] = array_values($this->data['resultColumn']);
		}
		$this->getColumnsType($query, $this->data['resultColumn'], $table, $this->data['sql_new_index'][$table]);

		// ajout des colonnes existantes dans la table principal en excluant chaques colonnes étrangère
		$this->data['sql'][$table]['select'][0] = "SELECT ";
		for($i=0;$i<sizeof($this->data['resultColumn'])-1;$i++){
			$this->data['sql'][$table]['select'][$i+1] = $this->data['sql_table_name'][$table].".".$this->data['resultColumn'][$i].", ";
		}

		if(sizeof($this->data['sql_new_index'][$table])== 0){
			$this->data['sql'][$table]['select'][sizeof($this->data['resultColumn'])] =
			$this->data['sql_table_name'][$table].".".$this->data['resultColumn'][sizeof($this->data['resultColumn'])-1]." ";
		}else{
			$this->data['sql'][$table]['select'][sizeof($this->data['resultColumn'])] =
			$this->data['sql_table_name'][$table].".".$this->data['resultColumn'][sizeof($this->data['resultColumn'])-1].", ";
			for($i=0;$i<sizeof($this->data['sql_new_index'][$table])-1;$i++){
				$this->data['sql'][$table]['select'][sizeof($this->data['sql'][$table]['select'])] =
				$this->data['sql_new_index'][$table][$i].", ";
			}
			$this->data['sql'][$table]['select'][sizeof($this->data['sql'][$table]['select'])] =
			$this->data['sql_new_index'][$table][sizeof($this->data['sql_new_index'][$table])-1]." ";
		}

		if($resultForeignKeys != ''){
			$this->data['resultColumn'] = array_merge($this->data['resultColumn'],$this->data['sql_new_index'][$table]);
			// start
			$startColumn = sizeof($this->data['resultColumn']) - sizeof($this->data['sql_new_index'][$table]);
			for($i=$startColumn;$i<sizeof($this->data['resultColumn']);$i++){
				// suppression du nom de la table de la colonne
				$this->data['resultColumn'][$i] = trim(trim(strstr($this->data['resultColumn'][$i], '.'), '.'));
			}
			// re-affecter la session de resultColumn
			$_SESSION['indexProject'][$table] = $this->data['resultColumn'];
			$this->data['dataProject'][$table]['column']['size'] = sizeof($this->data['resultColumn']);
		}

		// alter FROM
		if($resultForeignKeys != ''){
			for($i=0;$i<sizeof($resultForeignKeys);$i++){
				$tmp_str[0] = "JOIN ".$resultForeignKeys[$i]['fk_table'];
				$tmp_str[1] = " ON ".$resultForeignKeys[$i]['fk_table'].".".$resultForeignKeys[$i]['fk_id']." = ";
				$tmp_str[2] = $this->data['sql_table_name'][$table].".".$resultForeignKeys[$i]['column_id']." ";
				$this->data['sql'][$table]['from'][sizeof($this->data['sql'][$table]['from'])] = $tmp_str[0].$tmp_str[1].$tmp_str[2];
			}
		}
	}

	public function changeRequestClause($table, $project){

		$sqlRequestValue = "";
		$this->data['sql'][$table]['where']['answer'] = array();
		$this->data['sql'][$table]['where']['answer'][0] = "'".$_SESSION['pseudo']."' ";
		$this->data['sql'][$table]['where']['answer'][1] = "'".$project."' ";

		foreach($this->data['sql'][$table] as $key => $value){
			if(is_array($this->data['sql'][$table][$key])){
				foreach($this->data['sql'][$table][$key] as $key2 => $value2){
					if(!is_array($value2)){
						$sqlRequestValue = $sqlRequestValue.$value2;
					}

					if($key == "where" && $key2 == "question"){
						for ($i = 0; $i < sizeof($value2) ; $i++){
							$sqlRequestValue = $sqlRequestValue.$value2[$i].$this->data['sql'][$table]['where']['answer'][$i];
						}
					}
				}
			}
		}
		$this->data['sql'][$table]['request'] = $sqlRequestValue;
	}

	public function loading_data($query, $table, $actualProject){

		//	On lance la requête
		$query = $this->db->query($this->data['sql'][$table]['request'], $this->data['query']['body']);
		$j = 0;
		$field = '';

		foreach($query->result() as $ligne){
			for ($i =0;$i<sizeof($this->data['resultColumn']);$i++){
				$field = $this->data['resultColumn'][$i];
				$this->data['dataProject'][$table]['column'][$field]['value'][] = $ligne->$field;
				$this->data['dataProject'][$table]['project'][$actualProject][$j][$field] = $ligne->$field;
			}
			$j = $j+1;
		}
		$_SESSION['dataProject'][$table]['project'][$actualProject] = $this->data['dataProject'][$table]['project'][$actualProject];

		//	On libère la mémoire de la requête
		$query->free_result();
	}

	//ajax Request

	 public function save_query_tool()
    {
		if (!$this->input->is_ajax_request()) {
		   show_404();
		}
		else {
			$dao_query_tool = (array) $this->input->post('dao_query_tool');
			$tmp_dao = $this->data_viewer_query_tool_model->get_last_id();
			$dao_query_tool['query_tool_id'] = $tmp_dao[0]['query_tool_id']+1;
			$result = $this->data_viewer_query_tool_model->create($dao_query_tool);
			echo json_encode($result);
		}
    }

	public function load_query_tool(){
		if (!$this->input->is_ajax_request()) {
		   show_404();
		}
		else {
			$result = array();
			$result['global'] = $this->data_viewer_query_tool_model->get_all_query_tool();
			$result['user'] = $this->data_viewer_query_tool_model->get_all_query_tool_where_creator($_SESSION['pseudo']);

			$this->data['tab']['query_tool']['load']['globalJSON'] = $result['global'];
			$this->data['tab']['query_tool']['load']['userJSON'] = $result['user'];

			$this->load_query_tool_get_popularJSON();
			$result['popular'] = $this->data['tab']['query_tool']['load']['popularJSON'];

			echo json_encode($result);
		}
	}

	public function execute_query_tool(){
		if (!$this->input->is_ajax_request()) {
		   show_404();
		}
		else {
			$this->data['tab']['query_tool']['query'] = array();
			$this->data['query_tool']['query']['index'] = array();

			$query['body'] = array();
			$query_tool['query'] = array();
			$tab_query = array();

			$request = $this->input->post('request');
			$query = $this->db->query($request, $query['body']);
			$this->data['query_tool']['query']['index'] = array_keys((array)$query->result()[0]);
			foreach($query->result() as $line){
				$query_tool['query'] = array();
				foreach($line as $key => $value) {
					$query_tool['query'][$key] = $value;
				}
				array_push($this->data['tab']['query_tool']['query'], $query_tool['query']);
			}
			$query->free_result();

			$result['data'] = $this->data['tab']['query_tool']['query'];
			$result['index'] = $this->data['query_tool']['query']['index'];
			echo json_encode($result);
		}

	}

	public function load_query_tool_get_popularJSON(){

		$tab_query_user = $this->data_viewer_query_user_model->get_all_query_user();

		$popularJSON = array();
		$tab_popularJSON = array();
		$index = 0;

		foreach ($this->data['tab']['query_tool']['load']['globalJSON'] as $tab_JSON){
			$popularJSON = array();
			$popularJSON['query_tool_id'] = $tab_JSON['query_tool_id'];
			$popularJSON['user_count'] = 0;
			for($index = 0; $index < sizeof($tab_query_user); $index++){
				if ($tab_query_user[$index]['query_tool_id'] == $tab_JSON['query_tool_id']){
					$popularJSON['user_count'] = $popularJSON['user_count'] + $tab_query_user[$index]['user_count'];
				}
			}
			array_push($tab_popularJSON, $popularJSON);
		}

		// ordonne le tableau par ordre décroissant de user_count, pour classer le tableau par popularité
		foreach ($tab_popularJSON as $key => $row) {
			$user_count[$key]  = $row['user_count'];
			$query_tool_id[$key] = $row['query_tool_id'];
		}
		array_multisort($user_count, SORT_DESC, $query_tool_id, SORT_ASC, $tab_popularJSON);

		return $this->load_query_tool_popularJSON_range($tab_popularJSON, 10);

	}

	public function load_query_tool_popularJSON_range($tab_popularJSON, $range){
		$this->data['tab']['query_tool']['load']['popularJSON'] = array();
		$index = 0;
		$indexGlobal = 0;

		for ($index = 0; (($index < sizeof($tab_popularJSON)) && ($index < $range) ); $index ++){
			for ($indexGlobal = 0; $indexGlobal < sizeof($this->data['tab']['query_tool']['load']['globalJSON']); $indexGlobal ++){
				if ($this->data['tab']['query_tool']['load']['globalJSON'][$indexGlobal]['query_tool_id'] == $tab_popularJSON[$index]['query_tool_id']){
					array_push($this->data['tab']['query_tool']['load']['popularJSON'],$this->data['tab']['query_tool']['load']['globalJSON'][$indexGlobal]);
				}
			}
		}
	}

	public function increment_load_query_tool($id){

		if ($this->data_viewer_query_user_model->exist($id)){

			$request = "SELECT *
			FROM query_user
			WHERE query_tool_id = ".$id.";";
			$query = $this->db->query($request, $this->data['query']['body']);

			$dao_query_tool = (array) $query->result()[0];
			$dao_query_tool['user_count'] = $dao_query_tool['user_count'] + 1 ;
			$this->data_viewer_query_user_model->update($dao_query_tool, $id);
			$query->free_result();
		}else{

			$dao_query_tool = array();
			$dao_query_tool['query_tool_id'] = $id;
			$dao_query_tool['user_login'] = $_SESSION['pseudo'];
			$dao_query_tool['user_count'] = 1;

			$this->data_viewer_query_user_model->create($dao_query_tool);
		}
	}

}
