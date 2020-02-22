<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('connected')) {
            set_status_header(401);
            show_error("Vous devez être connecté pour accéder à cette page.", 401);
            return;
        }

        $this->load->library('form_validation');
        $this->load->model('Users_model');
        $this->load->helper('date');
        $this->load->helper('html');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span>', '</div>');
    }

    /**
     * Affiche la liste des utilisateurs
     */
    public function index($offset = 0)
    {
        // Titre de la page
        $page['title'] = 'Listes des utilisateurs';
        $page['subtitle'] = 'Liste des utlisateurs inscrits dans DAPHNE';

        $data['limit'] = $this->input->get('limit'); // Nombre d'élements à afficher
        $data['offset'] = $offset; // Numero de l'élement à partir duquel l'affichage commence
        $data['order_field'] = $this->input->get('order_field'); // Champ par lequel ranger le tableau
        $data['order_direction'] = $this->input->get('order_direction'); // Direction par laquel ranger le tableau

        if (!$data['limit']) $data['limit'] = 10;
        $data['total_rows'] = $this->Users_model->count();//tous les essais

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('limit', 'limit', 'trim|is_natural_no_zero|less_than_equal_to[1000]|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|is_natural|less_than[' . $data['total_rows'] . ']|xss_clean');
        $this->form_validation->set_rules('order_field', 'order_field', 'trim|alpha_dash|in_list[' . implode(",", $this->Users_model->fields()) . ']|xss_clean');
        $this->form_validation->set_rules('order_direction', 'order_direction', 'trim|alpha|in_list[ASC,DESC,RANDOM]|xss_clean');

        if ($this->form_validation->run()) {
            $this->load->library('table');
            $this->load->library('pagination');

            $config['base_url'] = site_url('users/index');
            $config['total_rows'] = $data['total_rows'];
            $config['per_page'] = $data['limit'];
            $this->pagination->initialize($config);

            $data['users'] = $this->Users_model->find(array(), $data['limit'], $data['offset'], $data['order_field'], $data['order_direction']);
            $this->view('user/users', $page['title'], $page['subtitle'], $data);
        } else {
            show_error(validation_errors());
        }

    }

    public function display($login = null)
    {
        $this->form_validation->set_data(array('login' => $login));
        $this->form_validation->set_rules('login', 'login',
            array(
                'trim', 'required', 'alpha_dash', 'max_length[255]', 'xss_clean',
                array('login_exist_callable',
                    array($this->Users_model, 'exist'))
            )
        );
        $this->form_validation->set_message('login_exist_callable', 'Le {field} n\'éxiste pas.');

        if ($this->form_validation->run()) {
            $this->load->library('table');
            $data = $this->Users_model->find(array('login' => $login))[0];

            // Titre de la page
            $page['title'] = $data['login'];
            $page['subtitle'] = 'Les coordonnées du  ' . $data['login'];

            $this->view('user/display_user', $page['title'], $page['subtitle'], $data);

        } else {
            show_error(validation_errors());
        }
    }

    /**
     * Affiche la liste des notifications de l'utilisateur
     */
    public function notifications($offset = 0)
    {
        // Titre de la page
        $page['title'] = 'Mes notifications';
        $page['subtitle'] = "Liste de toutes les notifications de <strong>" . $this->session->userdata('username') . "</strong>";

        $data['limit'] = $this->input->get('limit'); // Nombre d'élements à afficher
        $data['offset'] = $offset; // Numero de l'élement à partir duquel l'affichage commence
        $data['order_field'] = $this->input->get('order_field'); // Champ par lequel ranger le tableau
        $data['order_direction'] = $this->input->get('order_direction'); // Direction par laquel ranger le tableau

        if (!$data['limit']) $data['limit'] = 10;
        $this->load->model('Notification_model');
        $data['total_rows'] = count($this->Notification_model->find(array('target_login' => $this->session->userdata('username'))));

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('limit', 'limit', 'trim|is_natural_no_zero|less_than_equal_to[1000]|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|is_natural|less_than_equal_to[' . $data['total_rows'] . ']|xss_clean');
        $this->form_validation->set_rules('order_field', 'order_field', 'trim|alpha_dash|in_list[' . implode(",", $this->Notification_model->fields()) . ']|xss_clean');
        $this->form_validation->set_rules('order_direction', 'order_direction', 'trim|alpha|in_list[ASC,DESC,RANDOM]|xss_clean');

        if ($this->form_validation->run()) {
            $this->load->library('table');
            $this->load->library('pagination');

            $config['base_url'] = site_url('users/notifications');
            $config['total_rows'] = $data['total_rows'];
            $config['per_page'] = $data['limit'];
            $this->pagination->initialize($config);

            $data['notifications'] = $this->Notification_model->find(array('target_login' => $this->session->userdata('username')), $data['limit'], $data['offset'], $data['order_field'], $data['order_direction']);
            $this->view('user/all_notifications', $page['title'], $page['subtitle'], $data);
        } else {
            show_error(validation_errors());
        }
    }
    /*public function remove_notifications($id)
    {
        if($id)
            return $this->Notification_model->remove_notifications($id);
        return false;
    }*/
    /**
     * Affiche les utilisateurs recherchés sous forme de liste d'options (Format JSON)
     */
    public function searched_options()
    {
        $this->form_validation->set_rules('term', 'Terme recherché', 'trim|required|max_length[255]|xss_clean');

        if ($this->form_validation->run()) {
            $searched_term = $this->input->post('term');
            $results = $this->Users_model->like($searched_term, 'login');

            $users = array_map(function ($result) {
                return array(
                    'name' => $result['login'],
                    'value' => $result['login']
                );
            }, $results);

            echo json_encode($users);
        } else {
            echo json_encode(array('type' => 'error', 'message' => form_error('term')));
        }
    }
}
