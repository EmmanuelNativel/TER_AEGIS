<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* Contrôleur de l'espace membre.
*
* @author Medhi Boulnemour <boulnemour.medhi@live.fr>
*/
class Member extends MY_Controller{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Users_model');
    //$this->load->model('Group_model');
    $this->load->library('form_validation');

    if($this->session->userdata('connected') != TRUE) {
      show_error("Vous n'êtes pas autorisé(e) à lire cette page!", 403);
      return;
    }

    $this->load->add_package_path(APPPATH.'third_party/ion_auth/');
    $this->load->library('ion_auth');
  }

  function index()
  {
    redirect("Member/profil", "location");
    return;
  }

  /**
  * Page profil de l'utilisateur
  */
  public function profil()
  {
    //Règles du formulaire
    $this->form_validation->set_rules('email', '"E-mail"', 'required|valid_email|xss_clean'); // TODO: verifier que l'adresse mail n'existe pas deja pour un autre compte
    $this->form_validation->set_rules('first_name', '"Prénom"', 'required|trim|min_length[2]'
    .'|max_length[50]|alpha_dash'
    .'|encode_php_tags|xss_clean');
    $this->form_validation->set_rules('last_name', '"Nom"', 'required|trim|min_length[2]|max_length[50]'
    .'|alpha_dash|encode_php_tags|xss_clean');

    $data['update_status'] = NULL;

    if($this->form_validation->run()){
      //On applique les changements
      $new_user_data = array(
        'first_name' => $this->input->post('first_name'),
        'last_name' => $this->input->post('last_name'),
        'email' => $this->input->post('email')
      );

      $data['update_status'] = $this->Users_model->set_user_data($this->session->userdata('username'), $new_user_data);

    }
    $data['user_infos'] = $this->Users_model->get_user_data($this->session->userdata('username'));

    $this->view('member/profil', 'Paramètres du profil', 'Informations à propos de votre profil DAPHNE', $data);
  }

  /**
  * Page changement de mot de passe de l'utilisateur
  */
  public function password()
  {
    //Règles du formulaire
    $this->form_validation->set_rules('new_mdp', '"Mot de passe"',	'required|min_length[5]'
    .'|max_length[52]|alpha_dash'
    .'|encode_php_tags'
    .'|matches[mdp_conf]|xss_clean');
    $this->form_validation->set_rules('mdp_conf', '"Confirmation du mot de passe"','required'
    .'|min_length[5]|max_length[52]'
    .'|alpha_dash|encode_php_tags|xss_clean');
    $this->form_validation->set_rules('mdp', '"Mot de passe"', 'required|encode_php_tags'
    .'|callback_valid_password['.$this->session->userdata('username').']|xss_clean');

    $data['update_status'] = NULL;

    if($this->form_validation->run()){
      //Après avoir tout vérifié dans le formulaire, on update le mot de passe dans la bdd grâce à Ion Auth
      $data['update_status'] = $this->ion_auth->reset_password($this->session->userdata('username'), $this->input->post('new_mdp'));
    }

    $data['user_infos'] = $this->Users_model->get_user_data($this->session->userdata('username'));
    $this->view('member/password', 'Mot de Passe', 'Vous pouvez changer votre mot de passe à partir de cette page.', $data);
  }

  /**
   * Indique à la base que le volet de notification a été ouvert.
   */
  public function notifications_read()
  {
    $this->load->model('Notification_model');
    $this->Notification_model->update(array('target_login' => $this->session->userdata('username')), array('was_read' => TRUE));
  }
}
