<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ontologies extends MY_Controller {

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
        $this->load->model('Ontology_model');
    }

    /**
     * Affiche la liste des ontologies disponibles pour l'utilisateur
     */
    public function index()
    {
        
    }

	/**
     * Affiche les ontologies recherchées sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[20]|xss_clean');

        if( $this->form_validation->run() )
        {
            $searched_term = $this->input->post('term');
            $results = $this->Ontology_model->like($searched_term, 'ontology_code');

            $ontologies = array_map(function($results) {
                return array(
                    'name' => $results['ontology_code'],
                    'value' => $results['ontology_id']
                );
            }, $results);

            echo json_encode($ontologies);
        }
        else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

}