<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operations extends MY_Controller {
	
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
        $this->load->model(array('Operation_model'));
    }

    /**
     * Affiche la liste des samples disponibles pour l'utilisateur
     */
    public function index()
    {
        
    }

    public function create()
    {
        // Titre de la page
        $page['title'] = 'Opération';
        $page['subtitle'] = 'Effectuer une opération';

        $this->load->model(array('Sample_model', 'Lab_model'));
        


        // Récupération des données du formulaire
        $data['operation_type'] = $this->input->post('operation_type');
        $data['operation_date'] = $this->input->post('operation_date');
        $data['operation_loca'] = $this->input->post('operation_loca');
        $data['operation_info'] = $this->input->post('operation_info');

        // Règles de validation du formulaire
        $this->form_validation->set_rules('operation_type', 'Type d\'opération', 'required|trim|min_length[1]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('operation_date', 'Date de l\'opération', 'required|trim|min_length[1]|max_length[255]|xss_clean');
        $this->form_validation->set_rules('operation_loca', 'Lieu de l\'opération', 'required|trim|min_length[1]|max_length[255]|xss_clean');

        //verifier si tout est bon dans les input et output avant de créer une opération
        $array_sample_input = array();
        $array_sample_output = array();
        $form = FALSE;
        $is_good_input = TRUE;
        $is_good_output = TRUE;
        $is_one_sample_input = FALSE;
        $is_one_sample_output = FALSE;
        $nb_output = 0;                 //compteur utilisé pour la création des échantillons en sortie 
        foreach($_POST as $key => $value){
            $form=TRUE;
            $array_ex_key = explode("-", $key);

            //sample_operation_input
            if (strcmp($array_ex_key[0], "lab_select") ==0) {
                $is_one_sample_input = TRUE;
                $code = $array_ex_key[1];   
                $val_labo = $value;
                $val_nb_input = $this->input->post('nb_input-'.$code.'');
                $val_nb_remove = $this->input->post('nb_remove_input-'.$code.'');

                if ($val_labo == '' || $val_nb_remove == '' || $val_nb_remove < 0 || $val_nb_input < $val_nb_remove) {
                    $is_good_input = FALSE;
                }

                array_push($array_sample_input, [$code, $val_labo, $val_nb_input, $val_nb_remove]);
            }
            //sample_operation_output
            if (strcmp($array_ex_key[0], "sample_output_type") == 0) {
                $is_one_sample_output = TRUE;
                $nb_output++;
                $code = $array_ex_key[1];   //ici ce n'est pas le code c'est le nombre attribué par defaut dans le js
                $val_sample_type = $value;
                $val_sample_nb = $this->input->post('sample_output_nb-'.$code.'');
                $val_labo = $this->input->post('lab_select_output-nb_'.$code.'');
                if ($val_labo == '' || $val_sample_type == '' || $val_sample_nb == '' || $val_sample_nb < 0 ) {
                    $is_good_output = FALSE;
                }
                array_push($array_sample_output, [$nb_output, $val_sample_type, $val_sample_nb, $val_labo]);
            }
        }

        //verification des champs de l'opération
        $is_okay_operation = FALSE;
        if ($this->form_validation->run()) {
            $this->load->helper('postgre');
            $data = nullify_array($data); // Remplace les chaines de carctères vide par la valeur NULL

            //s'il y a au moin un sample input et un sample output et que tout les champs soient remplis
            if ($is_good_input == TRUE && $is_good_output == TRUE && $is_one_sample_input && $is_one_sample_output) {
                //création de l'opération
                $id_sample_operation = $this->Operation_model->create_sample_operation(
                    array(
                        'date' => $data['operation_date'],
                        'location' => $data['operation_loca'],
                        'information' => $data['operation_info'],
                        'type_id' => $data['operation_type']
                    )
                );
                //association de l'opération aux samples input
                foreach ($array_sample_input as $key => $sample) {                    
                    $sample_code = $sample[0] ;
                    $sample_val_labo = $sample[1] ;
                    $sample_val_nb_remove = $sample[3] ;
                    $sample_id = $this->Sample_model->return_sample_id($sample_code);

                    $this->Operation_model->create_sample_operation_input(
                            array(
                            'operation_id' => $id_sample_operation,
                            'donor_lab_id' => $sample_val_labo,
                            'sample_id' => $sample_id,
                            'nb_object_remove' => $sample_val_nb_remove
                        )
                    );
                }
                //samples output
                foreach ($array_sample_output as $key => $sample) {
                    $sample_type = $sample[1];
                    $sample_val_nb = $sample[2];
                    $sample_labo = $sample[3];

                    //creation du sample output
                    $lowest_id = $this->Sample_model->get_lowest_code_available();
                    $str_lowest = (string)$lowest_id; 
                    $num_length = strlen($str_lowest);
                    if ($num_length<12) {
                        for ($i=0; $i < 12-$num_length; $i++) { 
                            $str_lowest = '0'.$str_lowest;
                        }
                    }
                    $this->Sample_model->create(
                        array(
                            'sample_code' => $str_lowest,
                            'sample_type' => $sample_type,
                            'sample_nb_objects' => $sample_val_nb
                        )
                    );


                    //association de l'opération au sample output
                    $sample_id = $this->Sample_model->return_sample_id($str_lowest);
                    $this->Operation_model->create_sample_operation_output(
                            array(
                            'operation_id' => $id_sample_operation,
                            'receiver_lab_id' => $sample_labo,
                            'sample_id' => $sample_id
                        )
                    );
                }
                $is_okay_operation = TRUE;
                $this->session->set_flashdata('is_okay_operation', TRUE);

            }  
            
            //vider les tableaux
            if ($is_okay_operation){
                $array_sample_input = array();
                $array_sample_output = array();
                $form = FALSE;
            }
        }
        if ($form == TRUE) { // test pour savoir si le formulaire a été envoyé
            $error_input = (!$is_good_input || !$is_one_sample_input) ? TRUE : FALSE;
            $error_output = (!$is_good_output || !$is_one_sample_output) ? TRUE : FALSE;
            $this->session->set_flashdata('error_input', $error_input);
            $this->session->set_flashdata('error_output', $error_output);    
        }
        
        $data['list_sample_input_post'] = $array_sample_input;
        $data['list_sample_output_post'] = $array_sample_output;

        $this->load->library('table');

        //$data['list_sample_input'] = $this->Sample_model->get_all_sample();
        $data['list_sample_input'] = $this->Sample_model->get_samples_with_current_nb_and_nb_exist();
        $data['list_lab'] = $this->Lab_model->get_all_lab();
        $data['list_operation'] = $this->Operation_model->get_all_operation();


        // Affichage du formulaire de création d'un echantillon et retour des erreurs
        $scripts = array('jquery.dataTables', 'dataTables.bootstrap', 'bootstrap-select-ajax-plugin', 'new_operation');
        $this->view('operation/new_operation', $page['title'], $page['subtitle'], $data, $scripts);
    }

    public function create_operation_type()
    {
        if (!$this->input->is_ajax_request()) {
           show_404();
        }
        else {
            $operation_name = $this->input->post('operation_name');
            $id_operation = $this->Operation_model->create_operation_type(array(
                                'operation_name' => $operation_name
                            ));

            echo json_encode($id_operation);
        }
    }
    public function update_operation_type()
    {
        if (!$this->input->is_ajax_request()) {
           show_404();
        }
        else {
            $operation_id = $this->input->post('operation_id');
            $operation_name = $this->input->post('operation_name');
            $update = $this->Operation_model->update_operation_type(array(
                                'operation_name' => $operation_name
                            ), $operation_id);

            echo json_encode($update);
        }
    }
    public function delete_operation_type()
    {
        if (!$this->input->is_ajax_request()) {
           show_404();
        }
        else {
            $operation_id = $this->input->post('operation_id');
            $delete = $this->Operation_model->delete_operation_type($operation_id);

            echo json_encode($delete);
        }
    }
    
}