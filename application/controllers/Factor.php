<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Factors extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page. <a href=\"" . site_url('welcome/login') . "\">Connexion</a>", 401);
            return;
        }

        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->helper('date');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>', '</div>');
        $this->load->model('Factor_model');
    }
	
	/**
     * Affiche les factors recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $results = $this->Factor_model->like($searched_term, 'factor');


            $factors = array_map(function ($results) {
                return array(
                    'name' => $results['factor'],
                    'value' => $results['factor_id']
                );
            }, $results);


            echo json_encode($factors);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }
	
}