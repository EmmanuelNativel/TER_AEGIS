<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{

  public function __construct()
  {
    parent::__construct();
    date_default_timezone_set('Europe/Paris'); // On définit le fuseau horaire

    if($this->session->userdata('connected') == TRUE) { //Verifie si l'utilisateur est connecté
      //On enregistre les projets et les notifications associés à l'utilisateur
      $this->load->model(array('DupRules_model', 'Notification_model', 'ProjectMember_model'));
      $this->session->set_userdata('notifications', $this->Notification_model->get_notifications($this->session->userdata('username'), 10));
    }
  }
  /**
   * Affiche la vue d'une page
   */
  protected function view($page = 'welcome/index', $page_title='', $page_description='', $data=array(), $scripts=array(), $stylesheets=array(), $no_parallax=FALSE)
  {
    if ( ! file_exists(APPPATH.'views/'.$page.'.php'))
    {
      // La page n'existe pas
      show_404();
    }

    if ($page == 'welcome/index') {
      $data['page_title'] = $this->load->view('slide', '', TRUE);

    }
    elseif ($no_parallax == TRUE) {
      $page_title_data = array( 'page_title' => ucfirst($page_title),
                                'page_description' => ucfirst($page_description));
      $data['page_title'] = $this->load->view( 'page_title_no_parallax',
                                                $page_title_data,
                                                TRUE);
    }
    else {

      $page_title_data = array( 'page_title' => ucfirst($page_title),
                                'page_description' => ucfirst($page_description));
      $data['page_title'] = $this->load->view( 'page_title',
                                                $page_title_data,
                                                TRUE);
    }
    $data['page'] = $this->load->view($page, $data, TRUE);
    $data['scripts'] = $scripts;
    $data['stylesheets'] = $stylesheets;
    $this->load->view('template', $data);
  }
  /**
  * Test si un identifiant éxiste dans la table des utilisateurs de DAPHNE
  * Cette fonction peut être appelée de 2 manières:
  * Comme règle de validation de formulaire CodeIgniter -> mode = validation_rule (defaut)
  * Par une requête ajax dans un JavaScript -> mode = ajax
  *
  * @param String $str Identifiant à verifié
  * @param String $mode Mode d'utilisation de la fonction (mode -> validation_rule, ajax)
  *
  * @return Boolean Retourne TRUE : Si l'identifiant existe, FALSE : Sinon
  */
  public function pseudo_exist($str=NULL, $mode="validation_rule") {

    if($mode == "ajax") $str = $this->input->get('pseudo');

    if($this->ion_auth->username_check($str))
    //if($this->Users_model->count_members(array('login' => $str)) > 0)
    {
      if($mode == "ajax") echo 'TRUE';
      return TRUE;
    }
    else
    {
      if($mode == "ajax") echo 'FALSE';
      $this->form_validation->set_message('pseudo_exist', 'Cet {field} n\'éxiste pas');
      return FALSE;
    }
  }

  /**
  * Régle de validation de formulaire
  * Vérifie si le mot de passe est correct grâce à la librairie Ion Auth.
  */
  public function ionAuthLoginVerification($pwd,$username) {
    $loginOK = $this->ion_auth->login($username, $pwd, false);
    if ($loginOK) return TRUE;
    else {
      $this->form_validation->set_message('ionAuthLoginVerification', 'Identifiant ou mot de passe invalide');
      return FALSE;
    }
  }

  public function valid_password($pwd, $username) {
    $db_hash = $this->Users_model->get_user_hash($username);
    $passwordOK = $this->ion_auth->verify_password($pwd, $db_hash);

    if ($passwordOK) return TRUE;
    else {
      $this->form_validation->set_message('valid_password', 'Mot de passe invalide');
      return FALSE;
    }
  }

  /**
  * Régle de validation de formulaire
  * Vérifie si le mot de passe est correct!
  */
  public function OLD_valid_password($pwd, $username) {
    if ($this->pseudo_exist($username)) {
      //On verifie que le mot de passe est correct
      $db_hash = $this->Users_model->get_user_hash($username);
      if(password_verify($pwd, $db_hash)) {
        return TRUE;
      }
    }
    $this->form_validation->set_message('valid_password', 'Identifiant ou mot de passe invalide');
    return FALSE;
  }

}
