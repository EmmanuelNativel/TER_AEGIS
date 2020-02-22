<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dispositifs extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page. <a href=\"" . site_url('welcome/login') . "\">Connexion</a>", 401);
            return;
        }

        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->helper('date');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
        $this->load->model(array('Exp_unit_model'));
    }

    /**
     * Lien de téléchargement du formulaire d'importation de dispositif
     */

    public function download_form()
    {
        $this->load->helper('download');
        $file_path = 'download_forms/Import_design.xlsx';
        force_download($file_path, NULL);
    }

    /**
     * Appel de la vue import
     */
    public function mains_page(){
        $page['title'] = "Importation de dispositif";
        $page['subtitle'] = "Formulaire d'importation des dispositifs";
        $scripts = array('bootstrap-select-ajax-plugin');
        $this->view('dispositif/import', $page['title'], $page['subtitle'], $scripts);
    }



    public function import()
    {

        $config['upload_path'] = UPLOAD_PATH;//Le chemin d'accès au répertoire où le téléchargement doit être placé.
        $config['allowed_types'] = 'xls|xlsx'; // Autres extensions de la librairie phpexcel xml|ods|slk|gnumeric|csv|htm|html
        $config['encrypt_name'] = TRUE;


        $this->load->library('excel');
        $this->load->library('table');
        $this->load->library('upload', $config);


        // if not successful, set the error message
        if (!$this->upload->do_upload('dispo_file')) {
            $data['error'] = $this->upload->display_errors('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
            $scripts = array('bootstrap-select-ajax-plugin');
           $this->view('dispositif/import', $data, $scripts);
        } else {
            try {

                $upload_data = $this->upload->data();

                PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
                $objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']);// détecte automatiquement le lecteur correct à charger pour ce type de fichier

                //charger que certaines feuilles du fichier
                $loadSheets = array(
                    'Design',
                    'Accession',
                    'Accession_syn',
                    'Seedlot',
                    'Seedlot_Unit',
                    'Treatment',
                    'Treatment_Unit'
                );
                //$excelReader->setLoadSheetsOnly($loadSheets);

                $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);//charger les données Excel en PHP.

                $worksheetNames = $objPHPExcel->getSheetNames($upload_data['full_path']);
                $return = array();

                foreach ($worksheetNames as $key => $sheetName) {
                    //set the current active worksheet by name
                    $objPHPExcel->setActiveSheetIndexByName($sheetName);
                    // crée un tableau assoc avec le nom de la feuille en tant que clé et le tableau des contenus de la feuille comme valeur
                    $return[$sheetName] = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                }
                var_dump($return);


            } catch (Exception $e) {
                unlink($upload_data['full_path']); // remove file
                show_error($e->getmessage());
            }
        }
    }
	
	/**
     * Affiche les sites recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if( $this->form_validation->run() )
        {
            $searched_term = $this->input->post('term');
            $results = $this->Exp_unit_model->like($searched_term, 'unit_code');

            $unites = array_map(function($results) {
                return array(
                    'name' => $results['unit_code'],
                    'value' => $results['exp_unit_id']
                );
            }, $results);

            echo json_encode($unites);
        }
        else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }

	
}







