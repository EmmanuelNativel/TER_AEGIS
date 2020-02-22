<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ressources extends MY_Controller{

  public function __construct()
  {
    parent::__construct();
    //Codeigniter : Write Less Do More
  }

  function index()
  {
    $page['title'] = "Ressources";
    $page['subtitle'] = "Accès aux ressources disponibles de la base de données";

    $this->view('ressource/ressources', $page['title'], $page['subtitle']);
  }
  public function import()
  {
    $page['title'] = "Saisie et importation des données";
    $page['subtitle'] = "Sélection des formulaires de saisie et d'importation des données";

    $this->view('ressource/import_forms', $page['title'], $page['subtitle']);
  }

}
