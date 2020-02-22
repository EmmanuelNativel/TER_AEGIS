<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partners extends MY_Controller{

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
    $this->load->model('Partner_model');
  }

  /**
   * Affiche la liste des organisations
   */
  function index($offset = 0)
  {
    // Titre de la page
    $page['title'] = 'Entreprises';
    $page['subtitle'] = 'Liste des entreprises répertoriées dans DAPHNE';

    $data['limit'] = $this->input->get('limit'); // Nombre d'élements à afficher
    $data['offset'] = $offset; // Numero de l'élement à partir duquel l'affichage commence
    $data['order_field'] = $this->input->get('order_field'); // Champ par lequel ranger le tableau
    $data['order_direction'] = $this->input->get('order_direction'); // Direction par laquel ranger le tableau

    if (!$data['limit']) $data['limit'] = 10;
    $data['total_rows'] = $this->Partner_model->count();

    $this->form_validation->set_data($data);

    $this->form_validation->set_rules('limit', 'limit', 'trim|is_natural_no_zero|less_than_equal_to[1000]|xss_clean');
    $this->form_validation->set_rules('offset', 'offset', 'trim|is_natural|less_than['.$data['total_rows'].']|xss_clean');
    $this->form_validation->set_rules('order_field', 'order_field', 'trim|alpha_dash|in_list['.implode(",", $this->Partner_model->fields()).']|xss_clean');
    $this->form_validation->set_rules('order_direction', 'order_direction', 'trim|alpha|in_list[ASC,DESC,RANDOM]|xss_clean');

    if ($this->form_validation->run()) {
      $this->load->library('table');
      $this->load->library('pagination');

      $config['base_url'] = site_url('partners/index');
  		$config['total_rows'] = $data['total_rows'];
  		$config['per_page'] = $data['limit'];
  		$this->pagination->initialize($config);

      $partners = $this->Partner_model->find(array(), $data['limit'], $data['offset'], $data['order_field'], $data['order_direction']);

      // Convertit les codes pays à 2 lettres en noms de pays
      $this->load->model('Country_model');
      $n = count($partners);
      for ($i=0; $i < $n; $i++) {
        $query_result = $this->Country_model->find(array('country_code' => $partners[$i]['country']));
        if ($query_result) {
          $partners[$i]['country'] = $query_result[0]['country'];
        }
      }
      $data['partners'] = $partners;

      $this->view('partner/partners', $page['title'], $page['subtitle'], $data);
    }
    else {
      show_error(validation_errors());
    }
  }
  /**
   * Affiche le résumé d'un partenaire
   */
  public function display($partner_code)
  {
      $this->form_validation->set_data(array('partner_code' => $partner_code));
      $this->form_validation->set_rules('partner_code', 'Code partenaire',
          array(
              'trim', 'required','alpha_dash', 'max_length[255]', 'xss_clean',
              array('partner_exist_callable',
                  array($this->Partner_model, 'exist'))
          )
      );
      $this->form_validation->set_message('partner_exist_callable', 'Le {field} n\'éxiste pas.');

      if ($this->form_validation->run()) {
          $this->load->library('table');
          $data = $this->Partner_model->find(array('partner_code' => $partner_code))[0];


          // Titre de la page
          $page['title'] = 'Organisme ' . $data['partner_name'];
          $page['subtitle'] = $data['partner_name'];

          $this->view('partner/display_partner', $page['title'], $page['subtitle'], $data, array('bootstrap-treeview.min', 'bootstrap-select-ajax-plugin'));
      } else {
          show_error(validation_errors());
      }
  }
  /**
   * Page de création d'un partenaire
   */
  public function create()
  {
    $page['title'] = 'Nouvelle entreprise';
    $page['subtitle'] = "Formulaire de création d'une nouvelle entreprise";

    $this->load->model('Country_model');
    $data['countrys'] = $this->Country_model->find();

    $countrys = array();
    foreach ($data['countrys'] as $country) {
      array_push($countrys, $country["country_code"]);
    }

    //Regles de validation du formulaire
    $this->form_validation->set_rules('partner_name', 'Nom', 'alpha_dash|trim|required|min_length[2]|max_length[255]|xss_clean|is_unique[partner.partner_name]');
    $this->form_validation->set_rules('adress', 'Adresse', 'trim|min_length[2]|max_length[255]|xss_clean');
    $this->form_validation->set_rules('zip_code', 'Code Postal', 'numeric|trim|min_length[2]|max_length[20]|xss_clean');
    $this->form_validation->set_rules('city', 'Ville', 'alpha_dash|trim|min_length[2]|max_length[50]|xss_clean');
    $this->form_validation->set_rules('select_country', 'Pays', 'alpha|trim|required|min_length[2]|max_length[2]|xss_clean|in_list['.implode(',',$countrys).']'); // TODO: verifier si le pays est defini via une requete vers DAPHNE plutot qu'avec "in_list"

    if ($this->form_validation->run()) {
      $query_partner = array(
        'partner_name' => $this->input->post('partner_name'),
        'adress' => $this->input->post('adress'),
        'zip_code' => $this->input->post('zip_code'),
        'city' => $this->input->post('city'),
        'country' => $this->input->post('select_country')
      );

      $this->Partner_model->create($query_partner);
      $name = $this->input->post('partner_name');

      $data['msg'] = "L'organisme <strong>".$name."</strong> a été ajouté avec succés!";
      $this->view('success', $page['title'], $page['subtitle'], $data);

    } else {
      $data['select_country'] = $this->input->post('select_country');
      $this->view('partner/new_partner', $page['title'], $page['subtitle'], $data);
    }
  }
  /**
   * Page de mise à jour d'un partenaire
   */
    /**
     * Page de mise à jour d'un essai
     */
    public function update($partner_code=null)
    {
        $this->load->model('ProjectUser_model');
        try {
            $partner_code = xss_clean($partner_code); // Sécurise la valeur de la variable
            $query_result = $this->Partner_model->find(array('partner_code' => $partner_code));
            $data = $query_result[0];

        } catch (Exception $e) {
            show_error($e->getMessage());
        }
        //Regles de validation du formulaire
        $this->form_validation->set_rules('partner_name', 'Nom', 'required|min_length[2]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('adress', 'Adresse', 'trim|min_length[5]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('zip_code', 'Code Postal', 'numeric|trim|min_length[5]|max_length[20]|xss_clean');
        $this->form_validation->set_rules('city', 'Ville', 'alpha_dash|trim|min_length[2]|max_length[50]|xss_clean');



        if ($this->form_validation->run()) {

            $this->Partner_model->update(
                array('partner_code' => $partner_code),
                array(
                    'partner_name' => $this->input->post('partner_name'),
                    'adress' => $this->input->post('adress'),
                    'zip_code' => $this->input->post('zip_code'),
                    'city' => $this->input->post('city'),
                )
            );
            redirect('partners/display/'.$partner_code);

        } else {
            $page['title'] = ''.$data['partner_name'];
            $page['subtitle'] = 'Formulaire de modification d\'un organisme : ' .$data['partner_name'];;
            $this->view('partner/update_partner', $page['title'], $page['subtitle'], $data);
        }
    }
    /**
     * Page de suppression d'un organisme
     */
    public function delete()
    {
        $this->form_validation->set_rules('partner_code', 'Code partenaire', array('trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
            array('partner_exist_callable', array($this->Partner_model, 'exist'))));
        $this->form_validation->set_message('partner_exist_callable', 'Le {field} n\'éxiste pas.');


        if ($this->form_validation->run()) {

            $partner_code = $this->input->post('partner_code');
            $this->Partner_model->find(array('partner_code' => $partner_code))[0];
            //$this->load->model(array('Partner_model'));

            // Vérifie si l'utilisateur est admin
            if (!$this->session->userdata('admin')) {
                set_status_header(401);
                show_error("vous n'êtes pas autorisé à lire cette page.", 401);
                return;
            }

            $this->Partner_model->delete(array('partner_code' => $this->input->post('partner_code')));
            redirect('partners/index/');
        } else {
            show_error(validation_errors());
        }
    }

  /**
   * Affiche les partenaires recherchés sous forme de liste d'options (Format JSON)
   */
   public function searched_options()
   {
     $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

     if( $this->form_validation->run() )
     {
       $searched_term = $this->input->post('term');
       $results = $this->Partner_model->like($searched_term, 'partner_name', 20);

       $partners = array_map(function($results) {
         return array(
           'name' => $results['partner_name'],
           'value' => $results['partner_code']
         );
       }, $results);

       echo json_encode($partners);
     }
     else {
       echo json_encode(array('type' => 'error', 'message' => form_error('term')));
     }
   }

}
