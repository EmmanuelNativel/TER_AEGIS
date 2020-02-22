<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scales extends MY_Controller
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
        $this->load->model('Scale_model');
        $this->load->helper('html');


    }

	/**
     * Affiche les echelles/unites recherchées sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $results = $this->Scale_model->like($searched_term, 'scale_name');


            $scales = array_map(function ($results) {
                return array(
                    'name' => $results['scale_name'],
                    'value' => $results['scale_code']
                );
            }, $results);


            echo json_encode($scales);
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
            $scale_code = $this->input->post('scale_code');
            if ($this->Scale_model->exist($scale_code) == FALSE) {
                $scale_name = $this->input->post('scale_name');
                $scale_type = $this->input->post('scale_type');
                $scale_level = $this->input->post('scale_level');
                $request = $this->Scale_model->create(array(
                                    'scale_code' => $scale_code,
                                    'scale_name' => $scale_name,
                                    'scale_type' => $scale_type,
                                    'scale_level' => $scale_level
                                ));

                echo json_encode($request);
            }else{
                echo json_encode(-1);
            }
            
        }
    }

}