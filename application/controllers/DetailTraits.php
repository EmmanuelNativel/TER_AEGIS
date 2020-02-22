<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DetailTraits extends MY_Controller
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

    public function displayTrait()
    {
        $trait_code = $this->input->post('trait_code');

        //$this->load->model('Trait_model');
        $this->load->library('table');

        $template = array('table_open' => '<table border="1" cellpadding="4" cellspacing="0">');

        $this->table->set_template($template);
        $this->table->set_heading(array('Trait name', 'Trait description'));


        $data['Details']=$this->table->generate($this->Trait_model->trait_all($trait_code));
        return $this->view('detailsTree/detailTrait', 'Dictionnaire des variables', 'Séléction des variables DAPHNE', $data);


    }
    public function displayMethod()
    {
        $method_code = $this->input->post('method_code');

        //$this->load->model('Trait_model');
        $this->load->library('table');

        $template = array('table_open' => '<table border="1" cellpadding="4" cellspacing="0">');

        $this->table->set_template($template);
        $this->table->set_heading(array('Method name', 'Method class ','Method description', 'Method formula'));


        $data['Details']=$this->table->generate($this->Trait_model->Methods_all($method_code));
        return $this->view('detailsTree/detailTrait', 'Dictionnaire des variables', 'Séléction des variables DAPHNE', $data);


    }

    public function displayScale()
    {
        $scale_code = $this->input->post('scale_code');

        //$this->load->model('Trait_model');
        $this->load->library('table');

        $template = array('table_open' => '<table border="1" cellpadding="4" cellspacing="0">');

        $this->table->set_template($template);
        $this->table->set_heading(array('Scale name', 'Scale type','Scale level'));


        $data['Details']=$this->table->generate($this->Trait_model->Scale_all($scale_code));
        return $this->view('detailsTree/detailTrait', 'Dictionnaire des variables', 'Séléction des variables DAPHNE', $data);


    }




}









