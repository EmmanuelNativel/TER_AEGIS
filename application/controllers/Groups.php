<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends MY_Controller{

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
    $this->load->model(array('Group_model','Group_user_model'));
  }

  /**
   * Liste des groupes de partage des données de l'utilisateur
   */
  function index($offset = 0)
  {
    // Titre de la page
    $page['title'] = 'Mes Groupes de partage';
    $page['subtitle'] = 'Liste de mes groupes de partage de données';

    $data['limit'] = $this->input->get('limit'); // Nombre d'élements à afficher
    $data['offset'] = $offset; // Numero de l'élement à partir duquel l'affichage commence
    $data['order_field'] = $this->input->get('order_field'); // Champ par lequel ranger le tableau
    $data['order_direction'] = $this->input->get('order_direction'); // Direction par laquel ranger le tableau

    if (!$data['limit']) $data['limit'] = 10;
    $data['total_rows'] = $this->Group_model->count(array('owner_login' => $this->session->userdata('username')));

    $this->form_validation->set_data($data);

    $this->form_validation->set_rules('limit', 'limit', 'trim|is_natural_no_zero|less_than_equal_to[1000]|xss_clean');
    $this->form_validation->set_rules('offset', 'offset', 'trim|is_natural|less_than_equal_to['.$data['total_rows'].']|xss_clean');
    $this->form_validation->set_rules('order_field', 'order_field', 'trim|alpha_dash|in_list['.implode(",", $this->Group_model->fields()).']|xss_clean');
    $this->form_validation->set_rules('order_direction', 'order_direction', 'trim|alpha|in_list[ASC,DESC,RANDOM]|xss_clean');

    if ($this->form_validation->run()) {
      $this->load->library('table');
      $this->load->library('pagination');

      $config['base_url'] = site_url('groups/index');
      $config['total_rows'] = $data['total_rows'];
      $config['per_page'] = $data['limit'];
      $this->pagination->initialize($config);

      $data['groups'] = $this->Group_model->find(array('owner_login' => $this->session->userdata('username')), $data['limit'], $data['offset'], $data['order_field'], $data['order_direction']);
      $this->view('group/groups', $page['title'], $page['subtitle'], $data);
    }
    else {
      show_error(validation_errors());
    }
  }

  /**
   * Affiche un groupe et ses membres
   */
  public function display($group_name=null)
  {
    $group_name = xss_clean($group_name);

    try {
      $query_result = $this->Group_model->find(array('group_name' => $group_name, 'owner_login' => $this->session->userdata('username')));
      if (!$query_result) throw new Exception("Ce groupe n'éxiste pas!", 1);
    } catch (Exception $e) {
      show_error($e->getmessage());
    }

    $this->load->library('table');

    $data = $query_result[0];
    $data['members'] = $this->Group_user_model->get_members($data['group_id']);

    // Titre de la page
    $page['title'] = 'Mon groupe '.$group_name;
    $page['subtitle'] = 'Mon groupe de partage de données';

    $scripts = array('bootstrap-select-ajax-plugin');
    $this->view('group/display_group', $page['title'], $page['subtitle'], $data, $scripts);
  }

  /**
   * Page de création d'un groupe de partage de données
   */
  public function create()
  {
    // Titre de la page
    $page['title'] = 'Nouveau Groupe';
    $page['subtitle'] = 'Ajout d\'un groupe de partage de données';

    $this->form_validation->set_rules('name', 'Nom', 'trim|required|min_length[3]|alpha_dash|max_length[255]|xss_clean|is_unique[group.group_name]');
    $this->form_validation->set_rules('description', 'Description', 'trim|max_length[255]|xss_clean');

    if (!$this->form_validation->run()) {
      $this->view('group/new_group', $page['title'], $page['subtitle']);
    }
    else {
      $data = array(
        'group_name' => $this->input->post('name'),
        'group_description' => $this->input->post('description'),
        'owner_login' => $this->session->userdata('username')
      );
      $this->Group_model->create($data);
      redirect('groups/display/'.$data['group_name'], 'location');
    }
  }

  /**
   * Affiche les utilisateurs recherchés qui ne sont pas encore dans le groupe
   * concerné sous forme de liste d'options (Format JSON)
   */
   public function searched_users($group_id=null)
   {
     $group_id = xss_clean($group_id);

     try {
       $query_result = $this->Group_model->find(array('group_id' => $group_id, 'owner_login' => $this->session->userdata('username')));
       if (!$query_result) throw new Exception("Ce groupe n'éxiste pas!", 1);
     } catch (Exception $e) {
       show_error($e->getmessage());
     }

     $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

     if( $this->form_validation->run() )
     {
       $searched_term = xss_clean($this->input->post('term'));
       $results = $this->Group_user_model->get_not_members($searched_term, $group_id);

       $users = array_map(function($result) {
           return array(
             'name' => $result['login'],
             'value' => $result['login'],
             'subtext' => $result['first_name'].' '.$result['last_name']
           );
       }, $results);

       echo json_encode($users);
     }
     else {
       echo json_encode(array('type' => 'error', 'message' => form_error('term')));
     }
   }

   /**
    * Ajoute un membre au groupe de partage de données
    */
   public function add_member($group_id=null)
   {
     $this->load->model('Users_model');
     $group_id = xss_clean($group_id);

     try {
       $query_result = $this->Group_model->find(array('group_id' => $group_id, 'owner_login' => $this->session->userdata('username')));
       if (!$query_result) throw new Exception("Ce groupe n'éxiste pas!", 1);
     } catch (Exception $e) {
       show_error($e->getmessage());
     }

     $group_name = $query_result[0]['group_name'];

     $this->form_validation->set_data(array('login' => $this->input->post('user')));

     $this->form_validation->set_rules('login', 'identifiant',
     array(
       'trim', 'required', 'max_length[255]', 'xss_clean',
       array('user_exist_callable', array($this->Users_model, 'exist'))
     )
    );
    $this->form_validation->set_message('user_exist_callable', 'L\'{field} n\'éxiste pas.');

     if (!$this->form_validation->run()) {
       show_error(validation_errors());
     }
     else {
       $login = xss_clean(trim($this->input->post('user')));
       $this->Group_user_model->create(array('group_id' => $group_id, 'login' => $login));
       redirect('groups/display/'.$group_name, 'location');
     }


   }

}
