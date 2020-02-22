<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Treeview extends MY_Controller
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
        $this->load->model('Variable_model');
        $this->load->helper('html');
    }


    /**
     *page de trait dictionnaire
     */
    public function index()
    {
        $this->view('treeview/traits', 'Dictionnaire des variables', 'Séléction des variables DAPHNE');
    }

    public function classesNamesForTree()
    {

        $query = $this->Variable_model->selectClassesNamesForTree();
        //définir en-tête indiquant au navigateur qu'il puisse fonctionner avec du javascript
         header('Content-Type: application/json');
         echo json_encode($query);
         //print_r($query);


    }

    /**
     * Affiche les sites recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[50]|xss_clean');

        if( $this->form_validation->run() )
        {
            $searched_term = $this->input->post('term');
            $results = $this->Variable_model->like($searched_term, 'variable_code');

            $variables = array_map(function($results) {
                return array(
                    'name' => $results['variable_code'],
                    'value' => $results['variable_code']
                );
            }, $results);

            echo json_encode($variables);
        }
        else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }



}





