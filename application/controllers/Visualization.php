<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Visualization extends MY_Controller {

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
        $this->load->model(array('Variable_model', 'Project_model', 'Trial_model'));
    }



    /**
     * Affiche la page principal du menu à facettes des essais
     */
    public function index()
    {
        // Titre de la page
        $page['title'] = 'Visualisation des données';
        $page['subtitle'] = '';

        //Récupération des données pour les filtres
        //--- Projet
        $data['projects'] = $this->Project_model->get_all_projects(array('project_code'));

        //-- Années
        // On récupère les starting_date des essais et on en extrait les années
        // afin d'en faire une liste unique d'années sélectionnable dans le filtre
        $all_trials_dates = $this->Trial_model->get_all_trial_data(array('starting_date'));
        $available_years = array();
        foreach ($all_trials_dates as $key => $trial_data) {
          $dateValue = strtotime($trial_data['starting_date']);
          $year = date("Y", $dateValue);
          if (!in_array($year, $available_years)) array_push($available_years, $year);
        }
        sort($available_years); //Tri par ordre croissant des années pour qu'ils apparaissent triés dans le filtre
        $data['years'] = $available_years;

        //Récupération de toutes les données sur les essais par le biais de notre vue PostgreSQL
        $data['trials'] = $this->Trial_model->get_trials_visualization_view();

        //Affichage de la page
        $scripts = array('bootstrap-select-ajax-plugin', 'trials_visualization');
        $stylesheets = array('trials_visualization');
	      $this->view('visualization/accueil', $page['title'], $page['subtitle'], $data, $scripts, $stylesheets);

    }

    /**
     * Fonction ajax appelée par Visualization afin de charger les facteurs dans le filtre
     */
    public function ajaxLoadFactors() {
      $ajaxData = array();
      $ajaxData['factors'] = $this->Trial_model->get_available_factors();
      echo json_encode($ajaxData);
    }

    /**
     * Fonction ajax appelée par Visualization afin de charger les levels pour
     * un facteur en particulier dans le filtre
     */
    public function ajaxLoadLevels($factor_name) {
      $ajaxData = array();
      $ajaxData['levels'] = $this->Trial_model->get_available_levels($factor_name);
      echo json_encode($ajaxData);
    }

    /**
     * Fonction ajax appelée par Visualization afin de récupérer la liste des trial_code
     * qui sont filtrés par le tableau selectedFactors passés en paramètre (POST).
     * Cela permet de déléguer le travail de filtrage à PostgreSQL et ainsi de pouvoir
     * passer à l'échelle.
     */
    public function ajaxLoadFactorsFilteredTrialsCode() {
      $selectedFactors = json_decode($this->input->post('factors'), true);
      $ajaxData = array();
      $ajaxData['trials_code'] = $this->Trial_model->get_factors_filtered_trials_code($selectedFactors);
      echo json_encode($ajaxData);
    }
}
