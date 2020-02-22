<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Targets extends MY_Controller {
	
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
        $this->load->model('Target_model');
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
            $target_name = $this->input->post('target_name');
            if ($this->Target_model->exist($target_name) == FALSE) {
                $request = $this->Target_model->create(array(
                                    'target_name' => $target_name
                                ));

                echo json_encode($request);
            }else{
                echo json_encode(-1);
            }
            
        }
    }

    /**
     * Affiche les target recherchées sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if( $this->form_validation->run() )
        {
            $searched_term = $this->input->post('term');
            $results = $this->Target_model->like($searched_term, 'target_name');

            $target = array_map(function($result) {            
                return array(
                    'name' => $result['target_name'],
                    'value' => $result['target_name']
                );
            }, $results);

            echo json_encode($target);
        }
        else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

}