<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Taxos extends MY_Controller
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
        $this->load->model('Taxo_model');
    }

    /**
     * Affiche la liste des taxo
     */
    function index()
    {


    }
    /**
     * Page de création d'un site
     */
    public function create()
    {
        $page['title'] = 'Nouveau taxo';
        $page['subtitle'] = "Formulaire de création d'un nouveau taxo";

        //$this->load->model('Taxo_model');
        $data['taxos'] = $this->Taxo_model->find();


        //Regles de validation du formulaire
        $this->form_validation->set_rules('code', 'Code du taxon', 'trim|alpha_dash|required|max_length[255]|xss_clean|is_unique[taxo.taxo_code]');
        $this->form_validation->set_rules('name', 'Nom taxonomique', 'trim|required|min_length[5]|max_length[255]|xss_clean|is_unique[taxo.taxo_name]');
        $this->form_validation->set_rules('level_taxo', 'Rang taxonomique', 'trim|alpha_dash|max_length[255]|xss_clean');
        //$this->form_validation->set_rules('parent', 'Parent', 'trim|is_natural|xss_clean');

        if ($this->form_validation->run()) {
            $query_taxo= array(
            'taxo_code' =>  $this->input->post('code'),
            'taxo_name' =>  $this->input->post('name'),
            'level_taxo' =>  $this->input->post('level_taxo'),
            'id_parent' => $this->input->post('parent')
            );

            $this->Taxo_model->create($query_taxo);
            $name = $this->input->post('taxo_name');

            $data['msg'] = "Le taxo  <strong>".$name."</strong> a été ajouté avec succés!";
            $this->view('success', $page['title'], $page['subtitle'], $data);

        } else {
            $this->view('taxo/new_taxo', $page['title'], $page['subtitle'], $data);
        }
    }

    /**
     * Affiche le résumé d'un taxo
     */
    public function display()
    {
        # code...
    }


    /**
     * Affiche les taxo recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $results = $this->Taxo_model->like($searched_term, 'taxo_name');


            $taxos = array_map(function ($results) {
                return array(
                    'name' => $results['taxo_name'],
                    'value' => $results['taxo_id']
                );
            }, $results);


            echo json_encode($taxos);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

}