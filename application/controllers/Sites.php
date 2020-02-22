<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sites extends MY_Controller{

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
    $this->load->model('Site_model');
  }

  /**
   * Affiche la liste des sites
   */
  function index($offset = 0)
  {
    // Titre de la page
    $page['title'] = 'Sites';
    $page['subtitle'] = 'Liste des sites répertoriés dans DAPHNE';

    $data['limit'] = $this->input->get('limit'); // Nombre d'élements à afficher
    $data['offset'] = $offset; // Numero de l'élement à partir duquel l'affichage commence
    $data['order_field'] = $this->input->get('order_field'); // Champ par lequel ranger le tableau
    $data['order_direction'] = $this->input->get('order_direction'); // Direction par laquel ranger le tableau

    if (!$data['limit']) $data['limit'] = 10;
    $data['total_rows'] = $this->Site_model->count();

    $this->form_validation->set_data($data);

    $this->form_validation->set_rules('limit', 'limit', 'trim|is_natural_no_zero|less_than_equal_to[1000]|xss_clean');
    $this->form_validation->set_rules('offset', 'offset', 'trim|is_natural|less_than['.$data['total_rows'].']|xss_clean');
    $this->form_validation->set_rules('order_field', 'order_field', 'trim|alpha_dash|in_list['.implode(",", $this->Site_model->fields()).']|xss_clean');
    $this->form_validation->set_rules('order_direction', 'order_direction', 'trim|alpha|in_list[ASC,DESC,RANDOM]|xss_clean');

    if ($this->form_validation->run()) {
      $this->load->library('table');
      $this->load->library('pagination');

      $config['base_url'] = site_url('sites/index');
  		$config['total_rows'] = $data['total_rows'];
  		$config['per_page'] = $data['limit'];
  		$this->pagination->initialize($config);

      $data['sites'] = $this->Site_model->find(array(), $data['limit'], $data['offset'], $data['order_field'], $data['order_direction']);
      $this->view('site/sites', $page['title'], $page['subtitle'], $data);
    }
    else {
      show_error(validation_errors());
    }
  }
  /**
   * Affiche le résumé d'un site
   */
  public function display($site_code)
  {
    # code...
  }
  /**
   * Page de création d'un site
   */
  public function create()
  {
      $page['title'] = 'Nouveau site';
      $page['subtitle'] = "Formulaire de création d'un nouveau site";

      $this->load->model('Country_model');
      $data['countrys'] = $this->Country_model->find();

      $countrys = array();
      foreach ($data['countrys'] as $country) {
          array_push($countrys, $country["country_code"]);
      }

      //Regles de validation du formulaire
      $this->form_validation->set_rules('site_code', 'Localisation','required|trim|min_length[2]|max_length[50]|alpha_dash|is_unique[site.site_code]|xss_clean');
      $this->form_validation->set_rules('site_name', 'Nom du site', 'alpha_dash|trim|required|min_length[2]|max_length[50]|xss_clean');
      $this->form_validation->set_rules('select_country', 'Pays', 'alpha|trim|required|min_length[2]|max_length[2]|xss_clean|in_list['.implode(',',$countrys).']');
      $this->form_validation->set_rules('site_lat', 'Latitude de site', 'numeric|trim|min_length[2]|max_length[20]|xss_clean');
      $this->form_validation->set_rules('site_long', 'Longitude de site', 'numeric|trim|max_length[20]|xss_clean');
      $this->form_validation->set_rules('site_alt', 'Altitude de site', 'numeric|trim|max_length[20]|xss_clean');

      if ($this->form_validation->run()) {
          $query_site= array(
              'site_code' => $this->input->post('site_code'),
              'site_name' => $this->input->post('site_name'),
              'country' => $this->input->post('select_country'),
              'site_lat' => $this->input->post('site_lat'),
              'site_long' => $this->input->post('site_long'),
              'site_alt' => $this->input->post('site_alt')
          );

          $this->Site_model->create($query_site);
          $name = $this->input->post('site_name');

          $data['msg'] = "Le site  <strong>".$name."</strong> a été ajouté avec succés!";
          $this->view('success', $page['title'], $page['subtitle'], $data);

      } else {
          $data['select_country'] = $this->input->post('select_country');
          $this->view('site/new_site', $page['title'], $page['subtitle'], $data);
      }
  }
  /**
   * Page de mise à jour d'un site
   */
  public function update($site_code)
  {
    # code...
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
       $results = $this->Site_model->like($searched_term, 'site_code');

       $sites = array_map(function($results) {
         return array(
           'name' => $results['site_code'],
           'value' => $results['site_code']
         );
       }, $results);

       echo json_encode($sites);
     }
     else {
       echo json_encode(array('type' => 'error', 'message' => form_error('term')));
     }
   }

}
