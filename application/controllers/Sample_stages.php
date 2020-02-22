<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sample_stages extends MY_Controller
{

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
        $this->load->model('Sample_stage_model');
    }

    /**
     * Affiche la liste des organisations
     */
    function index()
    {


    }

    /**
     * Affiche le résumé d'un partenaire
     */
    public function display()
    {
        # code...
    }


    /**
     * Affiche les stades recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[10]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $results = $this->Sample_stage_model->like($searched_term, 'st_name');


            $stages = array_map(function ($results) {
                return array(
                    'name' => $results['st_name'],
                    'value' => $results['code_st']
                );
            }, $results);


            echo json_encode($stages);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

}