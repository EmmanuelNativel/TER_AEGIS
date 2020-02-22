<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Traits extends MY_Controller
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
        $this->load->model('Trait_model');
        $this->load->helper('html');


    }

	/**
     * Affiche les traits recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $results = $this->Trait_model->like($searched_term, 'trait_name');


            $traits = array_map(function ($results) {
                return array(
                    'name' => $results['trait_name'],
                    'value' => $results['trait_code']
                );
            }, $results);


            echo json_encode($traits);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

    public function create()
    {
        if (!$this->input->is_ajax_request()) {
           show_404();
        }
        else {
            $trait_code = $this->input->post('trait_code');
            if ($this->Trait_model->exist($trait_code) == FALSE) {                
                $trait_name = $this->input->post('trait_name');
                $trait_description = $this->input->post('trait_description');
                $entity_code = $this->input->post('entity_code');
                $target_name = $this->input->post('target_name');
                $trait_author = $this->input->post('trait_author');
                $request = $this->Trait_model->create(array(
                                    'trait_code' => $trait_code,
                                    'trait_name' => $trait_name,
                                    'trait_description' => $trait_description,
                                    'trait_entity' => $entity_code,
                                    'trait_target' => $target_name,
                                    'trait_author' => $trait_author
                                ));

                echo json_encode($request);
            }else{
                echo json_encode(-1);
            }
            
        }
    }

}