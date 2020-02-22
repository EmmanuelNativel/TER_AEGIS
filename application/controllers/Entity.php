<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entity extends MY_Controller {
	
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
        $this->load->model('Entity_model');
    }

    /**
     * Affiche la liste des entites disponibles pour l'utilisateur
     */
    public function index()
    {
        
    }
	
	public function create()
    {
        if (!$this->input->is_ajax_request()) {
           show_404();
        }
        else {
            $entity_code = $this->input->post('entity_code');
            if ($this->Entity_model->exist($entity_code) == FALSE) {
                $entity_name = $this->input->post('entity_name');
                $entity_definition = $this->input->post('entity_definition');
                $request = $this->Entity_model->create(array(
                                    'entity_code' => $entity_code,
                                    'entity_name' => $entity_name,
                                    'entity_definition' => $entity_definition
                                ));

                echo json_encode($request);
            }else{
                echo json_encode(-1);
            }
            
        }
    }
	
	/**
     * Affiche les entites recherchées sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if( $this->form_validation->run() )
        {
            $searched_term = $this->input->post('term');
            $results = $this->Entity_model->like($searched_term, 'entity_name');

            $entity = array_map(function($result) {            
                return array(
                    'name' => $result['entity_name'],
                    'value' => $result['entity_code']
                );
            }, $results);

            echo json_encode($entity);
        }
        else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

    /**
     * Affiche les entites recherchées sous forme de liste d'options (Format JSON)
     */
    public function searched_options_code_name()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if( $this->form_validation->run() )
        {
            $searched_term = $this->input->post('term');
            $results = $this->Entity_model->search_like_code_name($searched_term);

            $entity = array_map(function($result) {
                $name_str = $result['entity_code'].' : '.$result['entity_name'];
                return array(
                    'name' => $name_str,
                    'value' => $result['entity_code']
                );
            }, $results);

            echo json_encode($entity);
        }
        else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }
}