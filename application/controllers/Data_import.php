<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* (OBSOLETE|DEPRECATED) Contrôleur de l'importation/saisie de données.
*
* @author Medhi Boulnemour <boulnemour.medhi@live.fr>
*/
class Data_import extends MY_Controller{

  public function __construct()
  {
    parent::__construct();
    $this->load->model(array('users_model' /*'Group_model'*/));
    $this->load->library('form_validation');
    $this->load->helper('date');

    if($this->session->userdata('connected') != TRUE) {
      show_error("Vous n'êtes pas autorisé(e) à lire cette page!", 403);
      return;
    }
  }

  /**
  * Formulaire ajout de données
  */
  public function index()
  {
    /***********************************************************************
    *                         Formulaire de données
    **********************************************************************/

    $this->form_validation->set_rules('form', 'Formulaire de données', 'trim|required');

    if ($this->form_validation->run()) {
      $config['upload_path'] = './uploads/';
      $config['allowed_types'] = 'csv|txt';

      //load the upload library
      $this->load->library('upload', $config);
      $data['upload_data'] = '';

      // if not successful, set the error message
      if (!$this->upload->do_upload('userfile')) {
        $data = array('error' => $this->upload->display_errors());
      }
      else { // else, set the success message
        // echo ('Success: File upload completed.<br>');
        $data['upload_data'] = $this->upload->data();

        //traitement du fichier et importation dans la base
        $selected_form = $this->input->post('form');
        $file_path = $data['upload_data']["full_path"];

        switch ($selected_form) {
          case 'exp_unit':
          $this->import_exp_unit_csv($file_path);
          break;

          case 'accessions':
          $this->import_accessions($file_path);
          break;

          case 'accession_unit':
          $this->import_accession_unit($file_path);
          break;

          // case 'treatment_unit':
          // $this->import_treatment_unit($file_path);
          // break;

          case 'treatment':
          $this->import_treatment($file_path);
          break;

          case 'sample':
          $this->import_sample($file_path);
          break;

          case 'itk':
          $this->import_itk($file_path);
          break;

        }

        $this->session->unset_userdata('uploaded_file');
        //remove file
        unlink($file_path);
        return;
      }
    }

    $data['public_forms'] = array(
      'trial' => 'Essai agronomique',
      'exp_unit' => 'dispositif expérimental',
      'accessions' => 'Accessions',
      'accession_unit' => 'Associer des accessions à des unités expérimentales',
      'treatment' => 'Traitements',
      'sample' => 'Échantillons'
    );
    // 'treatment_unit' => 'Associer des traitements à des unités expérimentales',

    $data['private_forms'] = array(
      'itk' => 'Itinéraires techniques'
    );

    $this->load->model(array('Trial_model', 'Dataset_model'));
    $this->load->model(array('DatatsetType_model'));
    $data['trials'] = $this->Trial_model->find();
    $data['user_datasets'] = $this->Dataset_model->find(array('dataset_owner_login' => $this->session->userdata('username')));
    $data['dataset_types'] = $this->DatatsetType_model->find();

    /***********************************************************************
    *                         Création des tableaux
    **********************************************************************/

    $this->load->library('table');

    $template = array('table_open' => '<table class="table">');
    $this->table->set_template($template);
    $this->table->set_heading(array('Nom de l\'équipe', 'type d\'accés au jeu de données'));
    $data['access_table'] = $this->table->generate();

    /***********************************************************************
    *                         Appel de la vue
    **********************************************************************/

    $scripts = array('papaparse', 'dataset_mgr', 'bootstrap-datepicker.min', 'bootstrap-datepicker.fr.min', 'bootstrap-select-ajax-plugin');
    $this->view('member/add_dataset', 'Importation des données',
    'Formulaire d\'importation des données vers DAPHNE', $data, $scripts, TRUE);
  }
  /**
  * Créer un nouveau jeu de données dans DAPHNE
  */
  public function create_dataset()
  {
    $dataset_name = $this->input->post('datasetName');
    $dataset_description = $this->input->post('datasetDescription');
    $dataset_type = $this->input->post('datasetType');
    $visibility = $this->input->post('visibility');
    $login = $this->session->userdata('username');

    $this->form_validation->set_rules('datasetName', 'Nom', 'trim|alpha_dash|required|min_length[5]|max_length[80]|xss_clean');
    $this->form_validation->set_rules('datasetDescription', 'Description', 'trim|max_length[255]|xss_clean');
    $this->form_validation->set_rules('datasetType', 'Type du jeu de données', 'trim|alpha_dash|max_length[50]|xss_clean');
    $this->form_validation->set_rules('visibility', 'Visibilité', 'trim|required|is_natural|max_length[1]|xss_clean|in_list[0,1,2]');

    if ($this->form_validation->run()) {

      if (!$dataset_description)  $dataset_description = null;
      if (!$dataset_type)         $dataset_type = null;

      $this->load->model(array('Dataset_model', 'Group_Dataset_model'));
      $dataset_id = $this->Dataset_model->create(array(
        'dataset_name' => $dataset_name,
        'dataset_description' => $dataset_description,
        'dataset_type' => $dataset_type,
        'visibility' => $visibility,
        'dataset_owner_login' => $login
      ));

      $result = array(
        'type' => 'success',
        'dataset_id' => $dataset_id
      );
    }
    else {
      $result = array(
        'type' => 'error',
        'message' => validation_errors()
      );
    }
    echo json_encode($result);
  }

  /**
  * Ajoute un taxon
  */
  public function add_taxon()
  {
    $this->form_validation->set_rules('code', 'Code du taxon', 'trim|alpha_dash|required|max_length[15]|xss_clean|is_unique[bff_taxo.taxo_code]');
    $this->form_validation->set_rules('name', 'Nom taxonomique', 'trim|required|min_length[5]|max_length[255]|xss_clean|is_unique[bff_taxo.taxo_name]');
    $this->form_validation->set_rules('level_taxo', 'Rang taxonomique', 'trim|xss_clean');
    $this->form_validation->set_rules('parent', 'Parent', 'trim|is_natural|xss_clean');

    if ($this->form_validation->run()) {
      $taxo_code = $this->input->post('code');
      $taxo_name = $this->input->post('name');
      $level_taxo = $this->input->post('level_taxo');
      $id_parent = $this->input->post('parent');

      $this->load->model('Taxo_model');
      $this->Taxo_model->create($this->nullify_array(array('taxo_code' => $taxo_code, 'taxo_name' => $taxo_name, 'level_taxo' => $level_taxo, 'id_parent' => $id_parent)));
      echo 'success';
    }
    else {
      echo form_error('code');
      echo form_error('name');
      echo form_error('level_taxo');
      echo form_error('parent');
    }
  }
  /**
  * Régle de validation de formulaire:
  * Test si le couple "st_name"/"trial_code" est unique.
  */
  public function stage_is_unique($trial_code, $stage_name)
  {
    $this->load->model(array('Sample_stage_model'));
    if ($this->Sample_stage_model->find(array('st_name' => $stage_name, 'trial_code' => $trial_code))) {
      $this->form_validation->set_message('stage_is_unique', 'Le champ "Nom du stade" est déjà défini pour ce "Code essai".');
      return FALSE;
    }
    else {
      return TRUE;
    }
  }
  /**
  * Ajoute un stade de developpemnt
  */
  public function add_sample_stage()
  {
    $stage_name           = $this->input->post('stageName');
    $stage_starting_date  = $this->input->post('stageStartingDate');
    $stage_ending_date    = $this->input->post('stageEndingDate');
    $physio_stage         = $this->input->post('physioStage');
    $trial_code           = $this->input->post('trialCode');

    $this->form_validation->set_rules('stageName', 'Nom du stade', 'trim|alpha_dash|required|max_length[10]|xss_clean');
    $this->form_validation->set_rules('stageStartingDate', 'Date de début du stade', 'trim|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');
    $this->form_validation->set_rules('stageEndingDate', 'Date de fin du stade', 'trim|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');
    $this->form_validation->set_rules('physioStage', 'Stade physiologique', 'trim|max_length[255]|xss_clean');
    $this->form_validation->set_rules('trialCode', 'Code de l\'essai', 'trim|alpha_dash|required|max_length[30]|xss_clean|callback_trial_code_exist|callback_stage_is_unique['.$stage_name.']');

    if ($this->form_validation->run()) {
      $this->load->model('Sample_stage_model');
      $this->Sample_stage_model->create($this->nullify_array(array(
        'st_name' => $stage_name,
        'st_starting_date' => $stage_starting_date,
        'st_ending_date' => $stage_ending_date,
        'st_physio_stage' => $physio_stage,
        'trial_code' => $trial_code)));
        echo 'success';
      }
      else {
        echo form_error('stageName');
        echo form_error('stageStartingDate');
        echo form_error('stageEndingDate');
        echo form_error('physioStage');
        echo form_error('trialCode');
      }
    }
    /**
    * Retourne la liste des parents possible en fonction du rang taxonomique
    */
    public function possible_parents()
    {
      $levels = array(
        'order',
        'family',
        'subfamily',
        'genus',
        'species',
        'subspecies'
      );

      $this->form_validation->set_rules('taxoLevel', 'Rang taxonomique', 'trim|required|in_list['.implode(',', $levels).']|xss_clean');

      if ($this->form_validation->run()) {
        $this->load->model(array('Taxo_model'));
        $taxo_level = $this->input->post('taxoLevel');

        $level_key = array_search($taxo_level, $levels);
        if ( $level_key != 0) { // Si le rang taxonomique choisi est différent du 1er rang taxonomique décrit (Càd 'order')
          echo json_encode( $this->Taxo_model->find( array('level_taxo' => $levels[$level_key-1]) ) );
        }
      }
      else {
        echo json_encode(array('message' => form_error('taxoLevel'), 'type' => 'error'));
      }

    }


    /**
    * Régle de validation de formulaire: Vérifie si un panier contient au moins une entrée
    */
    public function valid_cart($str)
    {
      if ($str == '{}') {
        $this->form_validation->set_message('valid_cart', 'La liste des {field} doit contenir au moins une entrée');
        return FALSE;
      } else return TRUE;

    }
    /**
    * Régle de validation de formulaire: Verifie si tout les partenaires sont bien définit dans DAPHNE.
    */
    public function is_partner($array)
    {
      if ($array != '') {
        $this->load->model(array('Partner_model'));
        foreach ($array as $value) {
          if (!$this->Partner_model->find(array('partner_code' => $value))) {
            $this->form_validation->set_message('is_partner', 'Le champ {field} contient des partenaires non-définis dans DAPHNE');
            return FALSE;
          }
        }
        return TRUE;
      }
      else {
        return TRUE;
      }

    }
    /**
    * Régle de validation de formulaire: Verifie si un partenaire existe.
    */
    public function partner_name_exist($str)
    {
      $this->load->model(array('Partner_model'));
      if (empty($str) or $this->Partner_model->find(array('partner_name' => $str))) {
        return TRUE;
      }
      else {
        $this->form_validation->set_message('partner_name_exist', 'Le champ {field} n\'est pas défini dans DAPHNE');
        return FALSE;
      }

    }
    /**
    * Retourne TRUE si la chaine de caractère est une description valide
    */
    private function _valid_description($str)
    {
      return (bool) preg_match('/^[A-Z0-9 -_,:;.\(\)]+$/i', $str);
    }
    /**
    * Retourne TRUE si la chaine de caractère est un nom valide
    */
    private function _valid_name($str)
    {
      return (bool) preg_match('/^[A-Z0-9-_]+$/i', $str);
    }
    public function selection_required()
    {
      $selection = $this->input->post('select_users');

      if ($selection == NULL) {
        $this->form_validation->set_message('selection_required', 'Le champ {field} est requis.');
        return FALSE;
      }
      elseif (is_array($selection)) {
        if (in_array($this->session->userdata('username'), $selection) && count($selection) == 1) {
          $this->form_validation->set_message('selection_required', 'Le champ {field} doit contenir au moins un membre different de l\'utilisateur.');
          return FALSE;
        }
      }
      else {
        return TRUE;
      }
    }
    /**
    * Validation rule: Test si un "assigned_to" est valide.
    */
    public function valid_assignment($str, $num_lvl)
    {
      $this->load->model('exp_unit_model');
      if ($this->exp_unit_model->find(array('unit_code' => $str)))  return TRUE;
      elseif (empty($str) && $num_lvl == 1)                         return TRUE;
      else {
        $this->form_validation->set_message('valid_assignment', 'Le champ {field} n\'est pas valide.');
        return FALSE;
      }
    }
    /**
    * Validation rule:
    * Test si le couple "trial_code"/"unit_code" n'existe pas déjà.
    */
    public function trial_unit_valid($trial_code, $unit_code)
    {
      $this->load->model('exp_unit_model');
      if (!$this->exp_unit_model->find(array('unit_code' => $unit_code, 'trial_code' => $trial_code)))  return TRUE;
      else {
        $this->form_validation->set_message('trial_unit_valid', 'Il existe déja un "unit_code" du même nom pour cette essai.');
        return FALSE;
      }
    }
    /**
    * Validation rule:
    * Retourne vrai si le couple "trial_code"/"unit_code" existe.
    */
    public function trial_unit_exist($trial_code, $unit_code)
    {
      $this->load->model('exp_unit_model');
      if ($this->exp_unit_model->find(array('unit_code' => $unit_code, 'trial_code' => $trial_code)))  return TRUE;
      else {
        $this->form_validation->set_message('trial_unit_exist', 'Ce couple "trial_code"/"unit_code" n\'est pas défini dans daphne');
        return FALSE;
      }
    }
    /**
    * Validation rule:
    * Test si le site existe
    */
    public function site_exist($str)
    {
      $this->load->model('Site_model');
      if ($this->Site_model->find(array('site_code' => $str)) || empty($str))  return TRUE;
      else {
        $this->form_validation->set_message('site_exist', 'le champ {field} n\'est pas défini dans daphne');
        return FALSE;
      }
    }
    /**
    * Validation rule:
    * Test si un "trial_code" existe dans la base de données
    */
    public function trial_code_exist($str)
    {
      $this->load->model('trial_model');
      if ($this->trial_model->find(array('trial_code' => $str)))  return TRUE;
      else {
        $this->form_validation->set_message('trial_code_exist', 'Ce "trial_code" n\'est pas défini dans daphne.');
        return FALSE;
      }
    }
    public function crop_code_exist($str)
    {
      $this->load->model('Taxo_model');
      if ($this->Taxo_model->find(array('taxo_code' => $str)) || empty($str)) return TRUE;
      else {
        $this->form_validation->set_message('crop_code_exist', 'Le champ "{field}" n\'est pas défini dans daphne');
        return FALSE;
      }
    }
    public function country_exist($str)
    {
      $this->load->model('Country_model');
      if ($this->Country_model->find(array('country_code' => $str)) || empty($str)) return TRUE;
      else {
        $this->form_validation->set_message('country_exist', 'Le champ "{field}" n\'est pas défini dans daphne');
        return FALSE;
      }
    }
    public function accession_code_exist($str)
    {
      $this->load->model('Accession_model');
      if ($this->Accession_model->find(array('accession_code' => $str))) return TRUE;
      else {
        $this->form_validation->set_message('accession_code_exist', 'Le champ "{field}" n\'est pas défini dans daphne');
        return FALSE;
      }
    }
    public function exp_unit_code_exist($str)
    {
      $this->load->model('exp_unit_model');
      if ($this->exp_unit_model->find(array('unit_code' => $str)) || empty($str)) return TRUE;
      else {
        $this->form_validation->set_message('exp_unit_code_exist', 'Le champ "{field}" n\'est pas défini dans daphne');
        return FALSE;
      }
    }
    public function entity_exist($str)
    {
      $this->load->model('obs_entity_model');
      if ($this->obs_entity_model->find(array('entity_code' => $str)) || empty($str)) return TRUE;
      else {
        $this->form_validation->set_message('obs_entity_model', 'Le champ "{field}" n\'est pas défini dans daphne');
        return FALSE;
      }
    }
    public function st_trial_exist($stage, $trial_code)
    {
      $this->load->model('sample_stage_model');
      if ($this->sample_stage_model->find(array('st_name' => $stage, 'trial_code' => $trial_code)))  return TRUE;
      else {
        $this->form_validation->set_message('st_trial_exist', 'Ce couple "sample_st"/"trial_code" n\'est pas défini dans daphne');
        return FALSE;
      }
    }
    public function treatment_valid($factor_level, $json_factor_treatment)
    {
      $treatment = json_decode($json_factor_treatment, true);
      $trial_code = $treatment[0];
      $factor = $treatment[1];

      $this->load->model(array('Treatment_model'));
      if ($this->Treatment_model->find(array('trial_code' => $trial_code, 'factor' => $factor, 'factor_level' => $factor_level))) {
        $this->form_validation->set_message('treatment_valid', 'Ce niveau de facteur éxiste déja');
        return FALSE;
      } else {
        return TRUE;
      }
    }
    /**
    * Import de données sample à partir d'un fichier csv dans DAPHNE
    * @param $file_path Chemin d'accés au fichier csv
    */
    private function import_sample($file_path)
    {
      $data = array();
      $data['nb_success_lines'] = 0;
      $data['nb_error_lines'] = 0;
      $data['error_lines'] = array();

      $waitting_fields = array(
        'sample_code',
        'sample_type',
        'sample_nb_objects',
        'sample_plant_code',
        'sample_entity',
        'sample_entity_ref',
        'sample_entity_level',
        'sample_st',
        'unit_code',
        'trial_code'
      );

      $header_escaped = FALSE;
      $line_index = 1;

      $handle = fopen($file_path, "r");
      if ($handle) {
        while (($csv_line = fgetcsv($handle, 0, ';')) !== false) {

          //DEFINE THE HEADER
          if ($header_escaped == FALSE) {
            $header = $csv_line;
            $this->form_validation->set_data(array(
              'header' => json_encode($header)
            ));
            $this->form_validation->set_rules('header', 'Header', 'trim|required|xss_clean|callback_header_is_valid['.json_encode($waitting_fields).'])');
            if (!$this->form_validation->run()) {
              if(form_error('header') != NULL)  array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
              $data['nb_error_lines'] ++;
              break;
            }
            $header_escaped = TRUE;
            $line_index ++;
            continue;
          }

          //FORM VALIDATION
          $this->form_validation->reset_validation();
          $this->form_validation->set_data(array(
            'sample_code'         => $csv_line[array_search('sample_code', $header)],
            'sample_type'         => $csv_line[array_search('sample_type', $header)],
            'sample_nb_objects'   => $csv_line[array_search('sample_nb_objects', $header)],
            'sample_plant_code'   => $csv_line[array_search('sample_plant_code', $header)],
            'sample_entity'       => $csv_line[array_search('sample_entity', $header)],
            'sample_entity_ref'   => $csv_line[array_search('sample_entity_ref', $header)],
            'sample_entity_level' => $csv_line[array_search('sample_entity_level', $header)],
            'sample_st'           => $csv_line[array_search('sample_st', $header)],
            'unit_code'           => $csv_line[array_search('unit_code', $header)],
            'trial_code'          => $csv_line[array_search('trial_code', $header)]
          ));

          $this->form_validation->set_rules('sample_code', 'sample_code',
          'trim|is_natural|max_length[255]|xss_clean|is_unique[bff_sample.sample_code]');

          $this->form_validation->set_rules('sample_type', 'sample_type',
          'trim|alpha_dash|max_length[50]|xss_clean');

          $this->form_validation->set_rules('sample_nb_objects', 'sample_nb_objects',
          'trim|is_natural|max_length[50]|xss_clean');

          $this->form_validation->set_rules('sample_plant_code', 'sample_plant_code',
          'trim|alpha_dash|max_length[10]|xss_clean');

          $this->form_validation->set_rules('sample_entity', 'sample_entity',
          'trim|alpha_dash|max_length[50]|xss_clean|callback_entity_exist');

          $this->form_validation->set_rules('sample_entity_ref', 'sample_entity_ref',
          'trim|alpha_dash|max_length[50]|xss_clean|callback_entity_exist');

          $this->form_validation->set_rules('sample_entity_level', 'sample_entity_level',
          'trim|alpha_dash|max_length[50]|xss_clean');

          $this->form_validation->set_rules('sample_st', 'sample_st',
          'trim|alpha_dash|max_length[10]|xss_clean|callback_st_trial_exist['.$this->form_validation->validation_data['trial_code'].']');

          $this->form_validation->set_rules('unit_code', 'unit_code',
          'trim|alpha_dash|max_length[25]|xss_clean|callback_exp_unit_code_exist');

          $this->form_validation->set_rules('trial_code', 'trial_code',
          'trim|alpha_dash|max_length[30]|xss_clean'.
          '|callback_trial_unit_exist['.$this->form_validation->validation_data['unit_code'].']');

          if ($this->form_validation->run()) {
            $this->load->model(array('Sample_model', 'Sample_stage_model', 'Exp_unit_model'));

            $query_data = $this->nullify_array($this->form_validation->validation_data);
            $query_data['sample_st'] = $this->sample_stage_model->find(array('st_name' => $query_data['sample_st'], 'trial_code' => $query_data['trial_code']))[0]['code_st'];
            $query_data['unit_id'] = $this->Exp_unit_model->find(array('unit_code' => $query_data['unit_code'], 'trial_code' => $query_data['trial_code']))[0]['exp_unit_id'];

            $sample_code = $this->Sample_model->create($query_data);
            $data['nb_success_lines'] ++;
          }
          else {
            $data['nb_error_lines'] ++;
            array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
          }
          $line_index ++;
        }
        fclose($handle);
      }
      $this->diplay_import_result($data);
    }
    /**
    * Import de données accession_unit à partir d'un fichier csv dans DAPHNE
    * @param $file_path Chemin d'accés au fichier csv
    */
    private function import_accession_unit($file_path)
    {
      $data = array();
      $data['nb_success_lines'] = 0;
      $data['nb_error_lines'] = 0;
      $data['error_lines'] = array();

      $waitting_fields = array(
        'accession_code',
        'gen_starting_date',
        'gen_ending_date',
        'exp_unit_code',
        'trial_code'
      );

      $header_escaped = FALSE;
      $line_index = 1;

      $handle = fopen($file_path, "r");
      if ($handle) {
        while (($csv_line = fgetcsv($handle, 0, ';')) !== false) {

          //DEFINE THE HEADER
          if ($header_escaped == FALSE) {
            $header = $csv_line;
            $this->form_validation->set_data(array(
              'header' => json_encode($header)
            ));
            $this->form_validation->set_rules('header', 'Header', 'trim|required|xss_clean|callback_header_is_valid['.json_encode($waitting_fields).'])');
            if (!$this->form_validation->run()) {
              if(form_error('header') != NULL)  array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
              $data['nb_error_lines'] ++;
              break;
            }
            $header_escaped = TRUE;
            $line_index ++;
            continue;
          }

          //FORM VALIDATION
          $this->form_validation->reset_validation();
          $this->form_validation->set_data(array(
            'accession_code' => $csv_line[array_search('accession_code', $header)],
            'gen_starting_date' => $csv_line[array_search('gen_starting_date', $header)],
            'gen_ending_date' => $csv_line[array_search('gen_ending_date', $header)],
            'exp_unit_code' => $csv_line[array_search('exp_unit_code', $header)],
            'trial_code' => $csv_line[array_search('trial_code', $header)]
          ));

          $this->form_validation->set_rules('accession_code', 'accession_code',
          'trim|alpha_dash|max_length[10]|xss_clean|callback_accession_code_exist');

          $this->form_validation->set_rules('gen_starting_date', 'gen_starting_date',
          'trim|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');

          $this->form_validation->set_rules('gen_ending_date', 'gen_ending_date',
          'trim|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');

          $this->form_validation->set_rules('exp_unit_code', 'exp_unit_code',
          'trim|alpha_dash|max_length[25]|xss_clean|callback_exp_unit_code_exist');

          $this->form_validation->set_rules('trial_code', 'trial_code',
          'trim|alpha_dash|max_length[30]|xss_clean'.
          '|callback_trial_unit_exist['.$this->form_validation->validation_data['exp_unit_code'].']');

          if ($this->form_validation->run()) {
            $this->load->model(array('Accession_unit_model', 'Exp_unit_model', 'Accession_model'));

            //Remaniement des données pour la requete
            $query_data = $this->nullify_array($this->form_validation->validation_data);
            $query_data['exp_unit_id'] = $this->Exp_unit_model->find(array('unit_code' => $query_data['exp_unit_code'], 'trial_code' => $query_data['trial_code']))[0]['exp_unit_id'];
            $query_data['accession_id'] = $this->Accession_model->find(array('accession_code' => $query_data['accession_code']))[0]['accession_id'];
            unset($query_data['exp_unit_code']);
            unset($query_data['trial_code']);
            unset($query_data['accession_code']);

            $this->Accession_unit_model->create($query_data);
            $data['nb_success_lines'] ++;
          }
          else {
            $data['nb_error_lines'] ++;
            array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
          }

          $line_index ++;
        }
        fclose($handle);
      }
      $this->diplay_import_result($data);
    }
    /**
    * Import des traitements à partir d'un fichier csv dans DAPHNE
    * @param $file_path Chemin d'accés au fichier csv
    */
    private function import_treatment($file_path)
    {

      $data = array();
      $data['nb_success_lines'] = 0;
      $data['nb_error_lines'] = 0;
      $data['error_lines'] = array();

      $waitting_fields = array(
        'trial_code',
        'factor',
        'factor_level',
        'description',
      );

      $header_escaped = FALSE;
      $line_index = 1;

      $handle = fopen($file_path, "r");
      if ($handle) {
        while (($csv_line = fgetcsv($handle, 0, ';')) !== false) {

          //DEFINE THE HEADER
          if ($header_escaped == FALSE) {
            $header = $csv_line;
            $this->form_validation->set_data(array(
              'header' => json_encode($header)
            ));
            $this->form_validation->set_rules('header', 'Header', 'trim|required|xss_clean|callback_header_is_valid['.json_encode($waitting_fields).'])');
            if (!$this->form_validation->run()) {
              if(form_error('header') != NULL)  array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
              $data['nb_error_lines'] ++;
              break;
            }
            $header_escaped = TRUE;
            $line_index ++;
            continue;
          }

          //FORM VALIDATION
          $this->form_validation->reset_validation();

          $trial_code = $csv_line[array_search('trial_code', $header)];
          $factor = $csv_line[array_search('factor', $header)];
          $factor_level = $csv_line[array_search('factor_level', $header)];

          $this->form_validation->set_data(array(
            'trial_code' => $trial_code,
            'factor' => $factor,
            'factor_level' => $factor_level,
            'description' => $csv_line[array_search('description', $header)]
          ));

          // Regles de validations
          $this->form_validation->set_rules('trial_code', 'trial_code',
          'trim|alpha_dash|max_length[255]|xss_clean|callback_trial_code_exist');
          $this->form_validation->set_rules('factor', 'factor', 'trim|alpha_dash|required|max_length[255]|xss_clean');
          $this->form_validation->set_rules('factor_level', 'factor_level', 'trim|alpha_dash|required|max_length[255]|xss_clean|callback_treatment_valid['.json_encode(array($trial_code, $factor)).']');
          $this->form_validation->set_rules('description', 'description', 'trim|max_length[2000]|xss_clean');

          if ($this->form_validation->run()) {
            $this->load->model(array('Treatment_model'));
            // Remaniement des données pour la requete
            $query_data = $this->nullify_array($this->form_validation->validation_data);
            $this->Treatment_model->create($query_data);
            $data['nb_success_lines'] ++;
          }
          else {
            $data['nb_error_lines'] ++;
            array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
          }
          $line_index ++;
        }
        fclose($handle);
      }
      $this->diplay_import_result($data);
    }
    /**
    * Appel la vue des résultats d'importation
    */
    private function diplay_import_result($data)
    {
      $data['total_lines'] = $data['nb_error_lines'] + $data['nb_success_lines'];
      if ($data['total_lines'] > 0) {
        $data['tx_success'] = ($data['nb_success_lines'] / $data['total_lines']) * 100;
      }else {
        $data['tx_success'] = 0;
      }
      $this->view('member/import_result', 'Importation des données',
      'Résultats de l\'importation des données de traitements vers DAPHNE', $data, NULL, TRUE);
    }
    /**
    * Import de données itk à partir d'un fichier csv dans DAPHNE
    * @param $file_path Chemin d'accés au fichier csv
    */
    private function import_itk($file_path)
    {

      $data = array();
      $data['nb_success_lines'] = 0;
      $data['nb_error_lines'] = 0;
      $data['error_lines'] = array();

      //ajoute dataset

      $waitting_fields = array(
        'itk_date',
        'itk_duration',
        'itk_lab',
        'itk_val',
        'unit_code',
        'trial_code'
      );

      $header_escaped = FALSE;
      $line_index = 1;

      $handle = fopen($file_path, "r");
      if ($handle) {
        while (($csv_line = fgetcsv($handle, 0, ';')) !== false) {

          //DEFINE THE HEADER
          if ($header_escaped == FALSE) {
            $header = $csv_line;
            $this->form_validation->set_data(array(
              'header' => json_encode($header)
            ));
            $this->form_validation->set_rules('header', 'Header', 'trim|required|xss_clean|callback_header_is_valid['.json_encode($waitting_fields).'])');
            if (!$this->form_validation->run()) {
              if(form_error('header') != NULL)  array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
              $data['nb_error_lines'] ++;
              break;
            }
            $header_escaped = TRUE;
            $line_index ++;
            continue;
          }

          //FORM VALIDATION
          $this->form_validation->reset_validation();
          $this->form_validation->set_data(array(
            'itk_date' => $csv_line[array_search('itk_date', $header)],
            'itk_duration' => $csv_line[array_search('itk_duration', $header)],
            'itk_lab' => $csv_line[array_search('itk_lab', $header)],
            'itk_val' => $csv_line[array_search('itk_val', $header)],
            'unit_code' => $csv_line[array_search('unit_code', $header)],
            'trial_code' => $csv_line[array_search('trial_code', $header)]
          ));

          $this->form_validation->set_rules('itk_date', 'itk_date',
          'trim|required|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');

          $this->form_validation->set_rules('itk_duration', 'itk_duration',
          'trim|numeric|xss_clean');

          $this->form_validation->set_rules('itk_lab', 'itk_lab',
          'trim|alpha_dash|max_length[10]|xss_clean');

          $this->form_validation->set_rules('itk_val', 'itk_val',
          'trim|decimal|xss_clean');

          $this->form_validation->set_rules('unit_code', 'unit_code',
          'trim|alpha_dash|required|max_length[25]|xss_clean');

          $this->form_validation->set_rules('trial_code', 'trial_code',
          'trim|alpha_dash|max_length[30]|xss_clean'.
          '|callback_trial_unit_exist['.$this->form_validation->validation_data['unit_code'].']');

          if ($this->form_validation->run()) {
            $this->load->model(array('itk_model', 'Exp_unit_model'));

            //Remaniement des données pour la requete
            $query_data = $this->nullify_array($this->form_validation->validation_data);
            $query_data['unit_id'] = $this->Exp_unit_model->find(array('unit_code' => $query_data['unit_code'], 'trial_code' => $query_data['trial_code']))[0]['unit_id'];
            unset($query_data['unit_code']);
            unset($query_data['trial_code']);

            $this->itk_model->create($query_data);
            $data['nb_success_lines'] ++;
          }
          else {
            $data['nb_error_lines'] ++;
            array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
          }

          $line_index ++;
        }
        fclose($handle);
      }
      $this->diplay_import_result($data);
    }
    /**
    * Regle de validation de formulaire:
    * Test si le header d'un fichier d'importation est celui attentdu
    */
    public function header_is_valid($header, $waitting_fields)
    {
      $header = json_decode($header, TRUE);
      $waitting_fields = json_decode($waitting_fields, TRUE);
      if (count($header) != count($waitting_fields)) {
        $this->form_validation->set_message('header_is_valid', 'La taille du header est invalide');
        return FALSE;
      }
      else {
        foreach ($header as $field) {
          if (!in_array($field, $waitting_fields)) {
            $this->form_validation->set_message('header_is_valid', 'Champ "'.$field.'" n\'éxiste pas.');
            return FLASE;
          }
        }
      }
      return TRUE;
    }

    /**
    * Import les données d'accessions d'un fichier csv dans DAPHNE
    * @param $file_path Chemin d'accés au fichier csv
    */
    private function import_accessions($file_path)
    {
      $data = array();
      $data['nb_success_lines'] = 0;
      $data['nb_error_lines'] = 0;
      $data['error_lines'] = array();

      $waitting_fields = array(
        'accession_code',
        'taxo_code',
        'accession_name',
        'seed_production_site',
        'seed_production_date',
        'seed_institut_producer',
        'accession_type',
        'accession_mother',
        'accession_father',
        'genetic_pool',
        'seed_origin_country',
        'donor_code'
      );

      $header_escaped = FALSE;
      $line_index = 1;

      $handle = fopen($file_path, "r");
      if ($handle) {
        while (($csv_line = fgetcsv($handle, 0, ';')) !== false) {

          //DEFINE THE HEADER
          if ($header_escaped == FALSE) {
            $header = $csv_line;
            $this->form_validation->set_data(array(
              'header' => json_encode($header)
            ));
            $this->form_validation->set_rules('header', 'Header', 'trim|required|xss_clean|callback_header_is_valid['.json_encode($waitting_fields).'])');
            if (!$this->form_validation->run()) {
              if(form_error('header') != NULL)  array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
              $data['nb_error_lines'] ++;
              break;
            }
            $header_escaped = TRUE;
            $line_index ++;
            continue;
          }

          //FORM VALIDATION
          $this->form_validation->reset_validation();
          $this->form_validation->set_data(array(
            'accession_code' => $csv_line[array_search('accession_code', $header)],
            'taxo_code' => $csv_line[array_search('taxo_code', $header)],
            'accession_name' => $csv_line[array_search('accession_name', $header)],
            'seed_production_site' => $csv_line[array_search('seed_production_site', $header)],
            'seed_production_date' => $csv_line[array_search('seed_production_date', $header)],
            'seed_institut_producer' => $csv_line[array_search('seed_institut_producer', $header)],
            'accession_type' => $csv_line[array_search('accession_type', $header)],
            'accession_mother' => $csv_line[array_search('accession_mother', $header)],
            'accession_father' => $csv_line[array_search('accession_father', $header)],
            'genetic_pool' => $csv_line[array_search('genetic_pool', $header)],
            'seed_origin_country' => $csv_line[array_search('seed_origin_country', $header)],
            'donor_code' => $csv_line[array_search('donor_code', $header)]
          ));

          $this->form_validation->set_rules('accession_code', 'accession_code',
          'trim|alpha_dash|required|max_length[50]|is_unique[bff_accession.accession_code]|xss_clean');

          $this->form_validation->set_rules('taxo_code', 'taxo_code',
          'trim|alpha_dash|max_length[255]|xss_clean|callback_crop_code_exist');

          $this->form_validation->set_rules('accession_name', 'accession_name',
          'trim|alpha_dash|max_length[255]|xss_clean');

          $this->form_validation->set_rules('seed_production_site', 'seed_production_site',
          'trim|alpha_dash|max_length[20]|xss_clean|callback_site_exist');

          $this->form_validation->set_rules('seed_production_date', 'seed_production_date',
          'trim|regex_match[/^(\d{4}\-[0-1][0-9]\-[0-3][0-9])$/]|max_length[10]|xss_clean');

          $this->form_validation->set_rules('seed_institut_producer', 'seed_institut_producer',
          'trim|alpha_dash|max_length[255]|callback_partner_name_exist|xss_clean');

          $this->form_validation->set_rules('accession_type', 'accession_type',
          'trim|alpha_dash|max_length[255]|xss_clean');

          $this->form_validation->set_rules('accession_mother', 'accession_mother',
          'trim|alpha_dash|max_length[50]|xss_clean');

          $this->form_validation->set_rules('accession_father', 'accession_father',
          'trim|alpha_dash|max_length[50]|xss_clean');

          $this->form_validation->set_rules('genetic_pool', 'genetic_pool',
          'trim|alpha_dash|max_length[30]|xss_clean');

          $this->form_validation->set_rules('seed_origin_country', 'seed_origin_country',
          'trim|alpha_dash|max_length[2]|xss_clean|callback_country_exist');

          $this->form_validation->set_rules('donor_code', 'donor_code',
          'trim|alpha_dash|max_length[255]|xss_clean');

          if ($this->form_validation->run()) {
            $this->load->model(array('Accession_model', 'Taxo_model', 'Partner_model'));
            $accession_data = $this->nullify_array($this->form_validation->validation_data);
            if ($accession_data['taxo_code'] != NULL) {
              $accession_data['taxo_id'] = $this->Taxo_model->find(array('taxo_code' => $accession_data['taxo_code']))[0]['taxo_id'];
              unset($accession_data['taxo_code']);
            }
            if ($accession_data['seed_institut_producer'] != NULL) {
              $accession_data['seed_institut_producer'] = $this->Partner_model->find(array('partner_name' => $accession_data['seed_institut_producer']))[0]['partner_code'];
            }

            $this->Accession_model->create($accession_data);
            $data['nb_success_lines'] ++;
          }
          else {
            $data['nb_error_lines'] ++;
            array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
          }
          $line_index ++;
        }
        fclose($handle);
      }
      $this->diplay_import_result($data);
    }
    /**
    * Remplace les valeurs vide d'un tableau par la valeur NULL
    */
    private function nullify_array($array)
    {
      foreach ($array as $key => $value) {
        if (empty($value)) {
          $array[$key] = NULL;
        }
      }
      return $array;
    }

    /**
    * Import les données exp_unit d'un fichier csv dans DAPHNE
    * @param $file_path Chemin d'accés au fichier csv
    */
    private function import_exp_unit_csv($file_path)
    {
      // BUG: S'il n'y a pas d'unité experimentale primaire (num_level = 1) alors l'importation ne s'effectue pas!

      $data = array();
      $data['nb_success_lines'] = 0;
      $data['nb_error_lines'] = 0;
      $data['error_lines'] = array();

      $waitting_fields = array(
        'num_level',
        'unit_code',
        'trial_code',
        'assigned_to',
        'level_label',
        'x_coord',
        'y_coord',
        'unit_lat',
        'unit_long',
        'rowspace',
        'unit_alt',
        'nb_plant',
        'surface'
      );

      $current_level = 1;
      $intresting_row_discovred = TRUE;
      $header_escaped = FALSE;

      while ($intresting_row_discovred != FALSE) {
        $line_index = 1;
        $intresting_row_discovred = FALSE;
        $handle = fopen($file_path, "r");
        if ($handle) {
          while (($csv_line = fgetcsv($handle, 0, ';')) !== false) {

            //DEFINE THE HEADER
            if ($header_escaped == FALSE) {
              $header = $csv_line;
              $this->form_validation->set_data(array(
                'header' => json_encode($header)
              ));
              $this->form_validation->set_rules('header', 'Header', 'trim|required|xss_clean|callback_header_is_valid['.json_encode($waitting_fields).'])');
              if (!$this->form_validation->run()) {
                if(form_error('header') != NULL)  array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
                $data['nb_error_lines'] ++;
                break;
              }
              $header_escaped = TRUE;
              $line_index ++;
              continue;
            }

            $column_index['num_level'] = array_search('num_level', $header);
            $column_index['unit_code'] = array_search('unit_code', $header);
            $column_index['trial_code'] = array_search('trial_code', $header);

            //SEARCH ROWS OF INTEREST
            if ($csv_line[$column_index['num_level']] == $current_level) {

              //VERIFY EVERY FILEDS OF THE ROW
              $this->form_validation->reset_validation();
              //Pass data to form_validation library
              $this->form_validation->set_data(array(
                'unit_code' => $csv_line[$column_index['unit_code']],
                'trial_code' => $csv_line[$column_index['trial_code']],
                'assigned_to' => $csv_line[array_search('assigned_to', $header)],
                'num_level' => $csv_line[$column_index['num_level']],
                'level_label' => $csv_line[array_search('level_label', $header)],
                'x_coord' => $csv_line[array_search('x_coord', $header)],
                'y_coord' => $csv_line[array_search('y_coord', $header)],
                'unit_lat' => $csv_line[array_search('unit_lat', $header)],
                'unit_long' => $csv_line[array_search('unit_long', $header)],
                'rowspace' => $csv_line[array_search('rowspace', $header)],
                'unit_alt' => $csv_line[array_search('unit_alt', $header)],
                'nb_plant' => $csv_line[array_search('nb_plant', $header)],
                'surface' => $csv_line[array_search('surface', $header)]
              ));

              //Validation rules
              $this->form_validation->set_rules('unit_code', 'unit_code',
              'trim|alpha_dash|required|max_length[25]|xss_clean');
              $this->form_validation->set_rules('trial_code', 'trial_code',
              'trim|alpha_dash|required|max_length[30]|xss_clean'.
              '|callback_trial_unit_valid['.$csv_line[$column_index['unit_code']].']'.
              '|callback_trial_code_exist');
              $this->form_validation->set_rules('assigned_to', 'assigned_to',
              'trim|alpha_dash|max_length[25]|xss_clean|'.
              'callback_valid_assignment['.$csv_line[$column_index['num_level']].']');
              $this->form_validation->set_rules('surface', 'surface',
              'trim|numeric|xss_clean');
              $this->form_validation->set_rules('nb_plant', 'nb_plant',
              'trim|is_natural|xss_clean');
              $this->form_validation->set_rules('rowspace', 'rowspace',
              'trim|numeric|xss_clean');
              $this->form_validation->set_rules('x_coord', 'x_coord',
              'trim|integer|xss_clean');
              $this->form_validation->set_rules('y_coord', 'y_coord',
              'trim|integer|xss_clean');
              $this->form_validation->set_rules('num_level', 'num_level',
              'trim|is_natural_no_zero|xss_clean');
              $this->form_validation->set_rules('level_label', 'level_label',
              'trim|alpha_dash|max_length[50]|xss_clean');
              $this->form_validation->set_rules('unit_lat', 'unit_lat',
              'trim|numeric|xss_clean');
              $this->form_validation->set_rules('unit_long', 'unit_long',
              'trim|numeric|xss_clean');
              $this->form_validation->set_rules('unit_alt', 'unit_alt',
              'trim|numeric|xss_clean');

              if ($this->form_validation->run()) {
                $this->load->model('exp_unit_model');
                //DATA FORMATING
                $exp_unit_data = $this->nullify_array($this->form_validation->validation_data);
                if (!empty($exp_unit_data['assigned_to'])) {
                  $searched_id = $this->exp_unit_model->find(array( 'unit_code' => $exp_unit_data['assigned_to'],
                  'trial_code' => $exp_unit_data['trial_code']))[0]['exp_unit_id'];
                  $exp_unit_data['assigned_to'] = $this->exp_unit_model->find(array('exp_unit_id' => $searched_id))[0]['exp_unit_id'];
                }
                foreach ($exp_unit_data as $key => $value) {
                  if (empty($value)) {
                    $exp_unit_data[$key] = NULL;
                  }
                }
                //SEND QUERY
                $this->exp_unit_model->create($exp_unit_data);
                $data['nb_success_lines'] ++;
                $intresting_row_discovred = TRUE;
              }
              else {
                $data['nb_error_lines'] ++;
                array_push( $data['error_lines'], array('csv_line_index' => $line_index, 'error_msg' => validation_errors()) );
                $intresting_row_discovred = FALSE;
                break;
              }
            }
            $line_index ++;

          }
          fclose($handle);
        }
        $current_level ++;
      }
      $this->diplay_import_result($data);
    }

    public function download_csv_form($form)
    {
      $this->load->helper('download');

      switch ($form) {
        case 'exp_unit':
        $header = array(
          'num_level',
          'unit_code',
          'trial_code',
          'assigned_to',
          'level_label',
          'x_coord',
          'y_coord',
          'unit_lat',
          'unit_long',
          'rowspace',
          'unit_alt',
          'nb_plant',
          'surface'
        );
        break;

        case 'accessions':
        $header = array(
          'accession_code',
          'taxo_code',
          'accession_name',
          'seed_production_site',
          'seed_production_date',
          'seed_institut_producer',
          'accession_type',
          'accession_mother',
          'accession_father',
          'genetic_pool',
          'seed_origin_country',
          'donor_code'
        );
        break;

        case 'accession_unit':
        $header = array(
          'accession_code',
          'gen_starting_date',
          'gen_ending_date',
          'exp_unit_code',
          'trial_code'
        );
        break;

        case 'sample':
        $header = array(
          'sample_code',
          'sample_type',
          'sample_nb_objects',
          'sample_plant_code',
          'sample_entity',
          'sample_entity_ref',
          'sample_entity_level',
          'sample_st',
          'unit_code',
          'trial_code'
        );
        break;

        case 'itk':
        $header = array(
          'itk_date',
          'itk_duration',
          'itk_lab',
          'itk_val',
          'unit_code',
          'trial_code'
        );
        break;

        case 'treatment':
        $header = array(
          'trial_code',
          'factor',
          'factor_level',
          'description'
        );
        break;

        default:
        # code...
        return;
      }

      $data = implode(";", $header);

      $name = $form.'.csv';

      force_download($name, $data);
    }
    /**
    * formulaire demande de création d'un Nouveau projet
    */
    public function new_project()
    {

          $data['select_partners'] = $this->input->post('select_partners');

          //On définit les règles de validation du formulaire
          $this->form_validation->set_rules('project_code', '"Nom de code"', 'required|trim|min_length[2]|max_length[255]'
          .'|alpha_dash|encode_php_tags|is_unique[bff_project.project_code]|xss_clean'
          .'|is_unique[bff_project.project_code]');
          $this->form_validation->set_rules('project_name', '"Nom du projet"', 'required|trim|min_length[2]|max_length[255]'
          .'|encode_php_tags|xss_clean');
          $this->form_validation->set_rules('project_resume', '"Description du projet"', 'required|trim|min_length[3]'
          .'|encode_php_tags|xss_clean');
          $this->form_validation->set_rules('coordinator', '"Responsable"', 'required|trim|min_length[2]|max_length[255]'
          .'|encode_php_tags|xss_clean');
          $this->form_validation->set_rules('coord_company', '"Société"', 'required|trim|min_length[2]|max_length[255]'
          .'|encode_php_tags|xss_clean');
          $this->form_validation->set_rules('select_partners', 'Partenaires', 'callback_is_partner|xss_clean');

          $data['project_request_status'] = NULL;

          $this->load->model('Partner_model');
          $data['list_partner'] = $this->Partner_model->find();

          $page_title = 'Nouveau projet';
          $page_description = 'Demande de création d\'un nouveau projet.';

          if ($this->form_validation->run()) { // Que faire si le formulaire est valide? => Executer le code suivant
            $data['project_code'] = $this->input->post('project_code');
            $data['project_name'] = $this->input->post('project_name');
            $data['project_resume'] = $this->input->post('project_resume');
            $data['coordinator'] = $this->input->post('coordinator');
            $data['coord_company'] = $this->input->post('coord_company');

            $username = $this->session->userdata('username');
            $project_reqst = array(
              "code" => $this->input->post('project_code'),
              "name" => $this->input->post('project_name'),
              "resume" => $this->input->post('project_resume'),
              "coordinator" => $this->input->post('coordinator'),
              "company" => $this->input->post('coord_company')
            );
            $partners_reqst = $this->input->post('select_partners');

            //Ajout de la demande de création de projet à la base de données
            $this->load->model('Project_model');
            $this->Project_model->add_request($username, $project_reqst, $partners_reqst);

            $data['msg'] = 'Votre demande de projet a été ajoutée avec succé! Cette demande est en attente de validation par un Administrateur';
            $this->view('success', $page_title, $page_description, $data);
          }
          else { // Formulaire invalide ou non soumis
            $scripts = array('selectize', 'init_partners_select');
            $this->view('member/new_project', $page_title, $page_description, $data, $scripts);
          }

        }

        /**
        * formulaire de création d'un partenaire (organisation)
        */
        public function add_partner()
        {
          $this->load->model(array('Country_model'));
          $data['countrys'] = $this->Country_model->find();

          $countrys = array();
          foreach ($data['countrys'] as $country) {
            array_push($countrys, $country["country_code"]);
          }

          //Regles de validation du formulaire
          $this->form_validation->set_rules('partner_name', 'Nom', 'alpha_dash|trim|required|min_length[2]|max_length[255]|xss_clean');
          $this->form_validation->set_rules('adress', 'Adresse', 'trim|required|min_length[5]|max_length[255]|xss_clean');
          $this->form_validation->set_rules('zip_code', 'Code Postal', 'numeric|trim|required|min_length[5]|max_length[5]|xss_clean');
          $this->form_validation->set_rules('city', 'Ville', 'alpha_dash|trim|required|min_length[2]|max_length[50]|xss_clean');
          $this->form_validation->set_rules('select_country', 'Pays', 'alpha|trim|required|min_length[2]|max_length[2]||xss_clean|in_list['.implode(',',$countrys).']'); // TODO: verifier si le pays est defini via une requete vers DAPHNE plutot qu'avec "in_list"

          if ($this->form_validation->run()) {
            $this->load->model(array('Partner_model'));
            $query_partner = array(
              'partner_name' => $this->input->post('partner_name'),
              'adress' => $this->input->post('adress'),
              'zip_code' => $this->input->post('zip_code'),
              'city' => $this->input->post('city'),
              'country' => $this->input->post('select_country')
            );

            if ($this->Partner_model->create($query_partner) != NULL) {
              $name = $this->input->post('partner_name');
              echo "L'organisme ".$name." a été ajouté avec succés!";
              return;
            }
          }

          $scripts = array('selectize', 'init_country_select');
          $this->view('member/add_partner', 'Enregistrer un Organisme', 'Création d\'un nouvelle organisme', $data, $scripts);

        }

        /**
        * Formulaire de création d'une équipe(groupe)
        */
       /* public function create_group()
        {
          $data['name'] = $this->input->post('name');
          $data['description'] = $this->input->post('description');
          $data['team_members'] = $this->input->post('select_users');

          $data['list_users'] = $this->users_model->find();

          $this->form_validation->set_rules('name', 'Nom', 'trim|required|min_length[5]|max_length[30]|alpha_dash|is_unique[bff_group.group_name]|xss_clean');
          $this->form_validation->set_rules('description', 'Description', 'trim|max_length[255]|xss_clean');
          $this->form_validation->set_rules('select_users', 'Membres de l\'équipe', 'callback_selection_required|xss_clean');

          $scripts = array('selectize', 'init_users_select');
          $page_title	= 'Créer une équipe';
          $page_description = "Formulaire de création d'équipe";

          if ($this->form_validation->run()) {
            //Créer le groupe
            $this->load->model(array('Group_model', 'Notification_model'));
            $group_id = $this->Group_model->create(array(
              'group_name' => $data['name'],
              'group_description' => $data['description']
            ));

            //Ajoute l'utilisateur comme leader du groupe
            $this->Group_model->add_group_user($group_id, $this->session->userdata('username'), 'TRUE', 'TRUE');

            //Ajoute les membres
            foreach ($data['team_members'] as $member) {
              //Si l'utilisateur est dans la liste on pass au suivant
              if ($member == $this->session->userdata('username')) {
                continue;
              }
              $this->Group_model->add_group_user($group_id, $member);
              $notification = array(
                'target_login' => $member,
                'notification_type' => GROUP_INVIT,
                'sender_login' => $this->session->userdata('username'),
                'created_time' => 'NOW()',
                'url' => site_url('member/group/'.$data['name']),
                'message' => '<b>'.$this->session->userdata('username').'</b> vous a invité à rejoindre l\'équipe <b>'.$data['name'].'</b>.'
              );
              $this->Notification_model->create($notification);
            }

            $data['msg'] = 'Équipe <b>'.$data['name'].'</b> ajouté avec succés!
            <a href="'.site_url('member/group/'.$data['name']).'">Voir l\'équipe</a>';
            $this->view('success', $page_title, $page_description, $data, $scripts);
          }
          else {
            $this->view('member/create_group', $page_title, $page_description, $data, $scripts);
          }
        }
        // Retourne la liste des groupes existants au format json
        public function existing_groups()
        {
          $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

          if($this->form_validation->run()){
            $searched_term = $this->input->post('term');
            $results = $this->Group_model->groups_like($searched_term);

            $groups = array_map(function($results) {
              return array(
                'name' => $results['group_name'],
                'value' => $results['group_id']
              );
            }, $results);

            echo json_encode($groups);
          }
          else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
          }
        }*/
        public function get_dataset()
        {
          $dataset_id = $this->input->post('dataset_id');
          // TODO: VERIFIER SI L'UTILISATEUR EST AUTORISER A VOIR LE JEU DE DONNEES!
          $this->form_validation->set_rules('dataset_id', 'dataset_id', 'trim|numeric|required|xss_clean');

          if ($this->form_validation->run()) {
            $dataset_info = $this->Dataset_model->find(array('dataset_id' => $dataset_id))[0];
            echo json_encode($dataset_info);
          }
          else {
            echo json_encode(array('type' => 'error', 'message' => form_error('dataset_id')));
          }
        }

        public function select_form()
        {
          $this->view('member/select_data_form', 'Saisie et importation des données', 'interface de sélection des formulaires de saisie et d\'importation des données', array(), array());
        }
      }
