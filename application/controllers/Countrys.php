<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Countrys extends MY_Controller
{
	/*
		Constructeur du controler Countries
	*/
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page.", 401);
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>', '</div>');
        $this->load->model('Country_model');
    }

    /**
     * Affiche la liste des pays
     */
    function index()
    {


    }

    /**
     * Affiche le résumé d'un pays
     */
    public function display()
    {
        # code...
    }


    /**
     * Affiche les pays recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[50]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $results = $this->Country_model->like($searched_term, 'country');


            $countrys = array_map(function ($results) {
                return array(
                    'name' => $results['country'],
                    'value' => $results['country_code']
                );
            }, $results);


            echo json_encode($countrys);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

}