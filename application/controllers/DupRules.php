<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DupRules extends MY_Controller
{
	//Constructeur du controler
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page.", 401);
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
        //$this->load->model('DupRules_model', 'Project_model', 'Dataset_model');
		$this->load->model('DupRules_model');
    }
<<<<<<< HEAD



=======
>>>>>>> 7933d5d90e5e286305eec6c90962a3c238777b70
}