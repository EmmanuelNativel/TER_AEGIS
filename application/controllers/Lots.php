<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lots extends MY_Controller
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
        $this->load->model('Lot_model');
    }
	
	/**
     * Appel de la vue import
     */
    // public function mains_page(){
        // $page['title'] = "Importation de lots de graines";
        // $page['subtitle'] = "Formulaire d'importation des lots de graine";
        // $scripts = array('bootstrap-select-ajax-plugin');
        // $this->view('exp_unit/import', $page['title'], $page['subtitle'], $scripts);
    // }
	
	/**
     * Affiche les lots recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if( $this->form_validation->run() )
        {
            $searched_term = $this->input->post('term');
            $results = $this->Lot_model->like($searched_term, 'lot_code');

            $lots = array_map(function($results) {
                return array(
                    'name' => $results['lot_code'],
                    'value' => $results['lot_id']
                );
            }, $results);

            echo json_encode($lots);
        }
        else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }
	
}