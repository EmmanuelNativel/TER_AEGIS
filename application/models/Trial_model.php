<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Trial_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->table = 'trial';
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
   * Retourne vrai si le code de l'essai existe
   */
  public function exist($str)
  {
    if ($this->find(array('trial_code' => $str)) || $str == NULL) return TRUE;
    else                                                       return FALSE;
  }

  /**
   * Récupère les données des champs $fields de la table trial
   */
  public function get_all_trial_data($fields = '*')
  {
    return $this->db->select($fields)
      ->from($this->table)
      ->get()
      ->result_array();
  }

  /**
   * Récupère la "view" trials_visualization. ( contient toutes les données
   * liés aux essais qui seront affichées dans le menu à facettes de Données/Visualisation )
   */
  public function get_trials_visualization_view($fields = '*')
  {
    return $this->db->select($fields)
      ->from("trials_visualization")
      ->get()
      ->result_array();
  }

  /**
   * Récupère tous les facteurs qui sont utilisés actuellement dans la bdd
   */
  public function get_available_factors()
  {
    return $this->db->distinct()
      ->select('factor')
      ->from("trials_factors")
      ->get()
      ->result_array();
  }

  /**
   * Récupère tous les levels d'un facteur qui sont utilisés actuellement dans la bdd
   */
  public function get_available_levels($factor_name)
  {
    return $this->db->distinct()
      ->select('factor_level, factor_level_description')
      ->from("trials_factors")
      ->where('factor', $factor_name)
      ->get()
      ->result_array();
  }

  /**
   * Récupère la liste des trial_code qui possèdent les factor et factor_level
   * passés en paramètre.
   *
   * $factorsArray -> associative array du type [[ "factorName" => x ,  "selectedLevels" => [ level1, level2, ... , leveln ]]
   */
  public function get_factors_filtered_trials_code($factorsArray)
  {
    //log_message("error","factorArray = " . print_r($factorsArray,true));

    //======== Récupération des informations passés en paramètres ============

    //Boucle permettant de générer les éléments utiles à la requête tels que :
    // les tuples (factor, factor_level) voulu
    // et les noms de facteurs pour lesquels l'utilisateur n'a pas donné de levels (empty_factors)
    $tuples = array();
    $empty_factors = array();
    foreach ($factorsArray as $factor) {
      $factor_name = $factor['factorName'];
      $selected_levels = $factor['selectedLevels'];
      if (empty($selected_levels)) array_push($empty_factors, "'" . $factor_name . "'");
      else {
        foreach ($selected_levels as $level) {
          array_push($tuples, "['" . $factor_name  . "','" . $level . "']");
        }
      }
    }

    //=========== Construction de la requête au fur et à mesure : ============

    //idée : on groupe par essai et on utilise des tableaux pour filtrer.
    //       Ainsi, avec le premier having on selectionne tous les essais
    //       qui ont bien les facteurs sans levels données en paramètre (empty_factors)
    //       Puis, avec les deuxième having on refiltre les essais en séléctionnant
    //       ceux qui ont bien tous les tuples (factor, factor_level) donnés en paramètre.

    $query = $this->db->select('trial_code')
      ->from("trials_factors")
      ->group_by('trial_code');

    if (!empty($empty_factors))
      $query->having("array_agg(factor)::text[] @> ARRAY[" .
        implode(",", $empty_factors) .
        "]", null, false);
    if (!empty($tuples))
      $query->having("array_agg(ARRAY[factor,factor_level])::text[] @> ARRAY[" .
        implode(",", $tuples) .
        "]", null, false);

    //Finalisation de la requête et return
    return $query->get()
      ->result_array();
  }

  /**
   * Récupère toutes les informations liées au dispositif expérimental d'un essai
   */
  public function get_trial_experimental_data($trial_code, $selectedFields = null)
  {

    if ($selectedFields == null)
      $selectedFields = array(
        "e.exp_unit_id",
        "e.unit_code",
        "e.level_label",
        "e2.unit_code as parent_unit_code",
        "e2.level_label as parent_level_label",
        "f.factor_id",
        "f.factor",
        "fl.factor_level",
        "fl.factor_level_description"
      );

    return $this->db->distinct()
      ->select($selectedFields)
      ->from("factor_unit fu")
      ->join("exp_unit e", "e.exp_unit_id = fu.exp_unit_id")
      ->join("exp_unit e2", "e2.exp_unit_id = e.assigned_to")
      ->join("factor_level fl", "fl.factor_level_id = fu.factor_level_id")
      ->join("factor f", "f.factor_id = fl.factor_id")
      ->where('e.trial_code', $trial_code)
      ->order_by('e.unit_code')
      ->get()
      ->result_array();
  }

  /**
   * Retourne le nombre de ligne total contenu dans trial_get_observations
   * pour un essai donné ($trial_code)
   */
  public function count_trial_observations_data($trial_code)
  {
    return $this->db->count_all("trial_get_observations('" . $trial_code . "','')");
  }

  /**
   * Retourne les données d'observations d'un essai ($trial_code)
   * (avec les fonctionnalités de pagination, de recherche et de tri par colonne)
   * Utilise la fonction trial_get_observations(...) de la BDD qui retourne une table.
   *
   * $orderByColumns est un associative array du type : array( "columnName" => "ASC|DESC" , ... )
   */
  public function get_trial_observations_data($trial_code, $limit = null, $offset = null, $searched_term = '', $orderByColumns = array())
  {
    // On utilise le caching de codeigniter pour récupérer le nombre total de lignes filtrées
    // avant de faire la pagination. Sans caching la requête est réinitialisée après le count.
    $this->db->start_cache(); //Debut caching
    $query = $this->db->select()
      ->from("trial_get_observations('" . $trial_code . "','" . $searched_term . "')");


    foreach ($orderByColumns as $columnName => $direction) {
      $directionQuery = $direction == "DESC" ? "DESC NULLS LAST" : "ASC";
      $query->order_by($columnName . " " . $directionQuery, '', false);
    }
    $this->db->stop_cache(); //fin caching

    //Avant de limiter les résultats on enregistre le nombre total d'éléments filtrés
    $totalFiltered = $query->count_all_results();

    if ($limit) $query->limit($limit, $offset);
    $result = $query->get()->result_array();

    $this->db->flush_cache(); //vide le cache avant return

    return  array(
      "result" => $result,
      "totalFiltered" => $totalFiltered
    );
  }

  /**
   * Récupère toutes les informations d'observations des unités expérimentales passées en paramètres
   * pour les variables passées en paramètres
   */
  public function get_exp_unit_data($obs_variables = array(), $exp_unit_ids = array())
  {
    if ($obs_variables == null) $obs_variables = [""];
    if ($exp_unit_ids == null) $exp_unit_ids = [""];

    $selectedFields = array(
      "obs_variable",  // la variable observée
      "unit_id",       // id de l'expérience (exp_unit_id)
      "obs_date",      // date où la valeur a été relevée
      "obs_value",     // la valeur 
      "scale_code"     // l'unité de la valeur
    );
    $query = $this->db->select($selectedFields)
      ->from("obs_unit o")
      ->join("variable v", "v.variable_code = o.obs_variable")
      ->where_in('obs_variable', $obs_variables)
      ->where_in('unit_id', $exp_unit_ids);

    //On tri pour que l'ordre des données soit dans l'ordre des tableaux en paramètres
    foreach ($obs_variables as $obs_variable) {
      $query->order_by(" obs_variable = '" . $obs_variable . "'", "DESC", false);
    }
    foreach ($exp_unit_ids as $exp_unit_id) {
      $query->order_by(" unit_id = " . $exp_unit_id, "DESC", false);
    }
    $query->order_by("obs_date");
    return $query->get()->result_array();
  }

  /**
   * Récupère toutes les informations liées au dispositif expérimental d'un essai
   */
  public function get_trial_hierarchy_data($trial_code, $factors,  $selectedFields = null)
  {
    if ($selectedFields == null)
      $selectedFields = array(
        "e.exp_unit_id",
        "e.unit_code as name",
        "e.level_label",
        "e.num_level",
        "e2.unit_code as parent_unit_code",
        "e2.level_label as parent_level_label",
        "e2.num_level as parent_num_level",
        "f.factor",
        "fl.factor_level",
        "fl.factor_level_description"
      );

    return $this->db->distinct()
      ->select($selectedFields)
      ->from("factor_unit fu")
      ->join("exp_unit e", "e.exp_unit_id = fu.exp_unit_id")
      ->join("exp_unit e2", "e2.exp_unit_id = e.assigned_to")
      ->join("factor_level fl", "fl.factor_level_id = fu.factor_level_id")
      ->join("factor f", "f.factor_id = fl.factor_id")
      ->where('e.trial_code', $trial_code)
      // ->where_in("f.factor", $factors)
      ->where_in("fl.factor_level", $factors)
      ->order_by('e.unit_code')
      ->get()
      ->result_array();
  }

  /**
   * Récupération des valeurs des variables observée et des infos qui y sont liées
   * (exp_unit, level_label, value, date, variable)
   */
  public function get_exp_data_values($trial_code, $obs_variable)
  {
    $select = array(
      "e.exp_unit_id",
      "e.level_label",
      "e2.unit_code as parent_unit_code",
      "ob.obs_value as value",
      "ob.obs_date as date",
      "ob.obs_variable as variable"
    );

    return $this->db->select($select)
      ->from("exp_unit e")
      ->join("exp_unit e2", "e2.exp_unit_id = e.assigned_to")
      ->join("obs_unit ob", "e.exp_unit_id = ob.unit_id ")
      ->where("e.trial_code", $trial_code)
      ->where_in("e.level_label", array('plot', 'parcelle'))
      ->where("ob.obs_variable", $obs_variable)
      ->order_by("exp_unit_id", "ASC")
      ->order_by("obs_date", "ASC")
      ->get()
      ->result_array();
  }

  public function get_exp_data_values2($trial_code, $obs_variable, $parent_name)
  {
    $select = array(
      "e.exp_unit_id",
      "e.level_label",
      "e2.unit_code as parent_unit_code",
      "ob.obs_value as value",
      "ob.obs_date as date",
      "ob.obs_variable as variable",
      "v.scale_code as unite"
    );

    return $this->db->select($select)
      ->from("exp_unit e")
      ->join("exp_unit e2", "e2.exp_unit_id = e.assigned_to")
      ->join("obs_unit ob", "e.exp_unit_id = ob.unit_id")
      ->join("variable v", "v.variable_code = ob.obs_variable")
      ->where("e.trial_code", $trial_code)
      ->where("e2.unit_code", $parent_name)
      ->where_in("e.level_label", array('plot', 'parcelle'))
      ->where("ob.obs_variable", $obs_variable)
      ->order_by("exp_unit_id", "ASC")
      ->order_by("obs_date", "ASC")
      ->get()
      ->result_array();
  }

  function factors_lvl_list($trial_code)
  {
    $selectedFields = array(
      "f.factor",
      "fl.factor_level",
    );

    return $this->db->distinct()
      ->select($selectedFields)
      ->from("factor_unit fu")
      ->join("exp_unit e", "e.exp_unit_id = fu.exp_unit_id")
      ->join("factor_level fl", "fl.factor_level_id = fu.factor_level_id")
      ->join("factor f", "f.factor_id = fl.factor_id")
      ->where('e.trial_code', $trial_code)
      ->order_by('f.factor, fl.factor_level')
      ->get()
      ->result_array();
  }
}
