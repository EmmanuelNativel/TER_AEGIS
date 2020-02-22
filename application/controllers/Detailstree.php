<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detailstree extends MY_Controller
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
     * Controleur de la page Administrateur
     */
    public function index() {
        $this->view('treedetails/index');
    }

    public function traits() {

        $this->view('treedetails/trait');
    }

    public function methode() {

        $this->view('treedetails/methode');
    }
    public function scale() {

        $this->view('treedetails/scale');
    }







}