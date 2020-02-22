<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Variable_model extends MY_Model
{
	
	protected $trait_table = 'trait';
	protected $method_table = 'method';
    protected $scale_table = 'scale';
	protected $entity_table = 'entity';
    protected $variable_ontology_table = 'variable_ontology';
    protected $ontology_table = 'ontology';
	
    public function __construct()
    {
        parent::__construct();
        $this->table = 'variable';
    }

    /**
     * Retourne vrai si le code variable existe
     */
    public function exist($str)
    {
        if ($this->find(array('variable_code' => $str)) || $str == NULL)  return TRUE;
        else                                          return FALSE;
    }
	
	/**
     * Créé un nouvel enregistrement avec les valeurs passées en paramètre
     **/
    public function create($values)
    {
        if ($this->db->set($values)->insert($this->table)) return TRUE;
        return null;
    }
	
	/**
	* Importe les données de variable depuis un tableau de données bidimensionnel
	*/
	public function import($data)
	{
		$this->db->trans_start();
		foreach ($data as $row) {
			$this->create(array(
			'variable_code' => (isset($row['variable_code']))? $row['variable_code'] : NULL,
			'trait_code' => (isset($row['trait_code']))? $row['trait_code'] : NULL,
			'method_code' => (isset($row['method_code']))? $row['method_code'] : NULL,
			'scale_code' => (isset($row['scale_code']))? $row['scale_code'] : NULL,
			'author' => (isset($row['author']))? $row['author'] : NULL,
			'class' => (isset($row['class']))? $row['class'] : NULL,
			'subclass' => (isset($row['subclass']))? $row['subclass'] : NULL
			));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();

	}
    public function select_distinct_class_subclass_domain()
    {
        $query = $this->db->distinct()->select('class, subclass, domain')
            ->from($this->table)
            ->order_by("class", "asc")
            ->order_by("subclass", "asc")
            ->order_by("domain", "asc")
            ->get()
            ->result_array();
        return $query;
    }
    public function select_distinct_subclass_order_by_class()
    {
        $query = $this->db->distinct()->select('class, subclass')
            ->from($this->table)
            ->order_by("class", "asc")
            ->get()
            ->result_array();
        return $query;
    }
    public function select_distinct_domain_order_by_subclass()
    {
        $query = $this->db->distinct()->select('subclass, domain')
            ->from($this->table)
            ->order_by("subclass", "asc")
            ->get()
            ->result_array();
        return $query;
    }
	
    public function select_distinct_class()
    {        
        $query = $this->db->distinct()->select('class')
            ->from($this->table)
            ->order_by("class", "asc")
            ->get()
            ->result_array();
        return $query;
    }
    public function select_distinct_subclass()
    {        
        $query = $this->db->distinct()->select('subclass')
            ->from($this->table)
            ->order_by("subclass", "asc")
            ->get()
            ->result_array();
        return $query;
    }
    public function select_distinct_domain()
    {        
        $query = $this->db->distinct()->select('domain')
            ->from($this->table)
            ->order_by("domain", "asc")
            ->get()
            ->result_array();
        return $query;
    }

    public function get_all_from_varesult(){
        return $this->db->get('varesult')->result_array();
    }
	
    /*
     * 
     */
    public function return_var_from_varesult($var_code) {
        $variable = $this->db->get_where('varesult', array('variable_code' => $var_code), 1)
                            ->result_array();
        if (isset($variable)) return $variable;
        else return NULL;
    }

    /*
     * Pour une variable passé en param, retourne toutes les info sur la variable, trait, entité, methode, scale sans les ontologies
     */
    public function get_var_trait_entity_method_scale($var_code){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->join($this->method_table, 'method.method_code = variable.method_code', 'left');
        $this->db->join($this->trait_table, 'trait.trait_code = variable.trait_code', 'left');
        $this->db->join($this->scale_table, 'scale.scale_code = variable.scale_code', 'left');
        $this->db->join($this->entity_table, 'entity.entity_code = trait.trait_entity', 'left');
        $this->db->where('variable.variable_code',$var_code);
        $query = $this->db->get();
        return $query->result_array()[0];
    }

    /*
     * Retourne l'ontology associé a la variable
     */
    public function get_ontology($variable_code)
    {
        $this->db->select('*');
        $this->db->from($this->variable_ontology_table);
        $this->db->join($this->ontology_table, 'variable_ontology.ontology_id = ontology.ontology_id', 'left');
        $this->db->where('variable_ontology.variable_code',$variable_code);
        $result = $this->db->get()->result_array();
        if ($result != null) {
            return $result;
        }else{
            return null;
        }
    }
    


    /*
     * Fonction de Samia
     */

    public function selectClassesNamesForTree()
    {
        $query = $this->db->distinct()->select('class')
            ->from($this->table)
            ->order_by('class')
            ->get()
            ->result_array();

        //
        //$classes['classes'] = $query;
        for ($i = 0; $i < count($query); $i++) {
            $b = $i + 1;

            $nomClass = $query[$i]['class'];


            $sub = $this->selectSubClasses($nomClass);
            $classes[$b] = $query[$i];
            $classes[$b]["sub"] = $sub;
           /* echo '<pre>';
            print_r( $sub) ."<br>";
            echo '</pre>';
            die;*/
        }
       return $classes;
    }

    public function selectSubClasses($nomClasse)
    {
        $classe = $nomClasse;
        $query = $this->db->distinct()->select('subclass')
            ->from($this->table)
            ->where($this->table . '.class', $classe)
            ->order_by('subclass')
            ->get()
            ->result_array();

        for ($i = 0; $i < count($query); $i++) {
            $b = $i + 1;
            $nomsubclass = $query[$i]['subclass'];

            $trait = $this->selectRaitSubClasses($nomsubclass);
            $subs[$b] = $query[$i];
            $subs[$b]["Trait"] = $trait;

        }
        return $subs;

    }

    public function selectRaitSubClasses($nomsubclass)
    {
        $subclass = $nomsubclass;
        $query = $this->db->distinct()->select('trait_code')
            ->from($this->table)
            ->where($this->table . '.subclass', $subclass)
            ->order_by('trait_code')
            ->get()
            ->result_array();

        for ($i = 0; $i < count($query); $i++) {
            $b = $i + 1;
            $nomTrait = $query[$i]['trait_code'];

            $method = $this->selectMethodTrait($nomTrait);
            $trait[$b] = $query[$i];
            $trait[$b]["Method"] = $method;
        }

          return $trait;

    }

    public function selectMethodTrait($nomTrait)
    {
        $traits = $nomTrait;
        $query = $this->db->distinct()->select('method_code')
            ->from($this->table)
            ->where($this->table . '.trait_code', $traits)
            ->order_by('method_code')
            ->get()
            ->result_array();

        for ($i = 0; $i < count($query); $i++) {
            $b = $i + 1;
            $nomMethod = $query[$i]['method_code'];

            $scale = $this->selectScaleMethod($nomMethod);
            $method[$b] = $query[$i];
            $method[$b]["Scale"] = $scale;
        }
        return $method;
    }

    public function selectScaleMethod($nomMethod)
    {
        $methods = $nomMethod;
        $query = $this->db->distinct()->select('scale_code')
            ->from($this->table)
            ->where($this->table . '.method_code', $methods)
            ->order_by('scale_code')
            ->get()
            ->result_array();

        //echo "\n".$this->db->last_query()."\n";
        foreach ($query as $scale_code) {
            $scale[] = $scale_code;
        }
        return $scale;
    }
}

