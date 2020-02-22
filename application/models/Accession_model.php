<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accession_model extends MY_Model
{

    protected $taxo_table = 'taxo';
    protected $partner_table = 'partner';
    protected $country_table = 'country';
	protected $site_table = 'site';
	protected $accesion_use_table = 'accession_use';
	protected $use_table = 'use';


    public function __construct()
    {

        parent::__construct();
        $this->table = 'accession';

    }

	/**
	* Retourne vrai si le code accession existe
	*/
    public function exist($str)
    {
        if ($this->find(array('accession_code' => $str)) || $str == NULL) return TRUE;
        else                                                            return FALSE;
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
     * liste des accessions par nom et taxo
     **/
  /* public function ListeAcession()
    {
        return $this->db->distinct()->select('*')
            ->from($this->table)
            ->join($this->accession_name_table, $this->table.'.accession_id = '.$this->accession_name_table.'.accession_id', 'left')
            ->join($this->taxo_table, $this->table.'.taxo_id = '.$this->taxo_table.'.taxo_id', 'left')
            ->get()
            ->result_array();
    }*/

    public function addAccessions($feuilleAccession, $nbreDeLignesAccession){
      $tableauFinalFeuilleAccession = array();
      $lineErrors=array();
      $lineSucess =array();
      $messageErrors = array();

      if (!empty($feuilleAccession)) {
          for ($i=1; $i<$nbreDeLignesAccession; $i++) {

              $taxo_ok = false;
              $accession_ok = false;

              $ligne = array_values($feuilleAccession[$i]);
              if ($i >0) { // boucle commence a 1 et seulement incrementation, interet du test ?
                  //stocker les éléments composant une accession
                  $accession_code= $ligne[0];
                  $taxo_code = $ligne[1];

                  // recuperer l'id de taxo
                  if (strlen($taxo_code)>0) {
                      $this->db->select('taxo_id');
                      $this->db->where('taxo_code', $taxo_code);
                      $query = $this->db->get($this->taxo_table);
                      $queryRes = $query->result_array();

                      if (count($queryRes) >0) {
                          $taxo_id = $queryRes[0]['taxo_id'];
                          $taxo_ok = true;
                      } else {
                          array_push($messageErrors, "Taxo_code, ligne ".$i." non présent dans la base");
                      }
                  } else {
                      array_push($messageErrors, "Taxo_code, ligne ".$i." absent");
                  }

                  if (strlen($accession_code)>0) {
                      // Vérifier si l'accession existe déja

                      if (!($this->exist($accession_code))) {
                          $accession_ok = true;
                      } else {
                          array_push($messageErrors, "Accession_code, ligne ".$i." déjà présent dans la base");
                      }
                  } else {
                      $accession = array();
                      array_push($messageErrors, "Accession_code, ligne ".$i." absent");
                  }

                  //Enregistrer dans un tableau les differentes informations
                  //if ($taxo_ok && $partenaire_ok && $origin_country_ok && $accession_ok && $site_ok && $formatDateSeed_production_date) {
                    if ($taxo_ok && $accession_ok) {
                      $data = array(
                          'accession_code' => $accession_code,
                          'taxo_id' => $taxo_id
                      );

                      $this->db->insert('accession', $data);
                      array_push($lineSucess, $i+1);
                  } else {
                      array_push($lineErrors, $i+1);
                  }
              }
          }
      } else {
          array_push($messageErrors, "Abscence de données dans la feuille Accession du fichier d'importation");
      }

      $tabResultSuccessAndErrorsLignes[0]= $lineSucess;
      $tabResultSuccessAndErrorsLignes[1]= $lineErrors;
      $tabResultSuccessAndErrorsLignes[2] = $messageErrors;

      return $tabResultSuccessAndErrorsLignes;

    }

	public function addAccession_Use($feuilleAccessionUse, $nbreDeLignesAccessionUse){
      $tableauFinalFeuilleAccessionUse = array();
      $lineErrors=array();
      $lineSucess =array();
      $messageErrors = array();

      if (!empty($feuilleAccessionUse)) {
          for ($i=1; $i<$nbreDeLignesAccessionUse; $i++) {
              $accession_ok = false;
              $use_ok = false;

              $ligne = array_values($feuilleAccessionUse[$i]);
              if ($i >0) { // boucle commence a 1 et seulement incrementation, interet du test ?
                  //stocker les éléments composants une accession
                  $accession_code= $ligne[0];
                  $accession_class = $ligne[1];
                  $accession_use = $ligne[2];


                  if (strlen($accession_code)>0) {
                      // Vérifier si l'accession existe déja
                      $this->db->select('accession_id');
                      $this->db->where('accession_code', $accession_code);
                      $q = $this->db->get($this->table);
                      $accession= $q->result_array();
                      if (count($accession) !=0) {
                          $accession_id = $accession[0]['accession_id'];
                          $accession_ok = true;
                      } else {
                          array_push($messageErrors, "Accession_code, ligne ".$i." non présent dans la base");
                      }
                  } else {
                      $accession = array();
                      array_push($messageErrors, "Accession_code, ligne ".$i." absent");
                  }

                  //stocker le use
                  if (strlen($accession_use)>0) {
                      //verif si use existe deja
                      $this->db->select('use_id');
                      $this->db->where('use', $accession_use);
                      $q = $this->db->get($this->use_table);
                      $use= $q->result_array();
                      if (count($use) !=0) {
                          $use_id = $use[0]['use_id'];
                          $use_ok = true;
                      } else {
                          $data = array(
                          'use' => $accession_use,
                          'class' => $accession_class
                          );

                          $this->db->insert($this->use_table, $data);

                          $this->db->select('use_id');
                          $this->db->where('use', $accession_use);
                          $q = $this->db->get($this->use_table);
                          $use= $q->result_array();
                          if (count($use) !=0) {
                              $use_id = $use[0]['use_id'];
                              $use_ok = true;
                          }
                      }
                  } else {
                      $use = array();
                      array_push($messageErrors, "Accession_use, ligne ".$i." absent");
                  }

                  //Enregistrer dans un tableau les differentes informations
                  if ($use_ok && $accession_ok) {
                      $data = array(
                          'accession_id' => $accession_id,
                          'use_id' => $use_id,
                      );

                      $this->db->select('use_id');
                      $this->db->where($data);
                      $q = $this->db->get($this->accesion_use_table);
                      $accessionuse= $q->result_array();

                      if (count($accessionuse)==0) {
                          $this->db->insert($this->accesion_use_table, $data);
                          array_push($lineSucess, $i+1);
                      } else {
                          array_push($lineErrors, $i+1);
                          array_push($messageErrors, "Accession_use, ligne ".$i." déjà présent dans la base");
                      }
                  }
              }
          }
      } else {
          array_push($messageErrors, "Abscence de données dans la feuille Accession_use du fichier d'importation");
      }

      $tabResultSuccessAndErrorsLignes[0]= $lineSucess;
      $tabResultSuccessAndErrorsLignes[1]= $lineErrors;
      $tabResultSuccessAndErrorsLignes[2] = $messageErrors;

      return $tabResultSuccessAndErrorsLignes;

    }

}
