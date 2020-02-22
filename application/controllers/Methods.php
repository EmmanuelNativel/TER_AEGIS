<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Methods extends MY_Controller {

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
        $this->load->model('Method_model');
    }
	
	/**
     * Affiche les methodes recherchées sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $results = $this->Method_model->like($searched_term, 'method_name');


            $methods = array_map(function ($results) {
                return array(
                    'name' => $results['method_name'],
                    'value' => $results['method_code']
                );
            }, $results);


            echo json_encode($methods);
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
            $method_code = $this->input->post('method_code');
            if ($this->Method_model->exist($method_code) == FALSE) {
                $method_name = $this->input->post('method_name');
                $method_class = $this->input->post('method_class');
                $method_subclass = $this->input->post('method_subclass');
                $method_description = $this->input->post('method_description');
                $method_formula = $this->input->post('method_formula');
                $method_reference = $this->input->post('method_reference');
                $method_type = $this->input->post('method_type');
                $method_content_type = $this->input->post('method_content_type');
                $method_author = $this->input->post('method_author');
                $request = $this->Method_model->create(array(
                                    'method_code' => $method_code,
                                    'method_name' => $method_name,
                                    'method_class' => $method_class,
                                    'method_subclass' => $method_subclass,
                                    'method_description' => $method_description,
                                    'method_formula' => $method_formula,
                                    'method_reference' => $method_reference,
                                    'method_type' => $method_type,
                                    'content_type' => $method_content_type,
                                    'author' => $method_author
                                ));

                echo json_encode($request);
            }else{
                echo json_encode(-1);
            }
            
        }
    }
	
}