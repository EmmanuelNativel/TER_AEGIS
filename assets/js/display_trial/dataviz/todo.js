(function (d3) {
  // Sélection des Facteurs -> OnChange()
  // Sélection des valeurs -> OnChange() + drawSlider()
  /**
   * GLOBAL VAR :
   * - hierarchy
   * - obs_values
   * - selected_date
   * - current_values
   * - current_element
   * - dateMin
   * - dateMax
   */
  /**
   * OnChange() :
   * - Si aucun facteur n'est sélectionné -> Veuillez sélectionner au moins un facteur
   * - Sinon,
   *    - loadData(drawChildren)
   */
  /**
   * loadData(callback) :
   * - Chargement de la hiérarchie et des valeurs
   * - Préparation de la hiérarchie -> Stockage dans variable globale hierachie
   * - Stockage de l'élément root dans current_element
   * - Préparation des valeurs -> Stockage dans une variable globale obs_values
   * - callBack(root.children)
   */
  /**
   * prepareHierachy(data) :
   * - Récupération des blocs
   * - On ajoute les parcelles de chacun des blocs
   * - Transformation en objet hiérarchy -> root
   * - On retourne l'objet root
   */
  /**
   * prepareValues(data) :
   * - Grouper les valeurs par expérience (?)
   * - Parsing des dates
   * - Récupération de la date Min et de la date Max et stockage dans les variable globales dateMin et dateMax
   */
  /** TODO
   * drawChildren(elements) :
   * - Dessiner les carrés
   * - Afficher les valeurs dans chacun des carrés
   * - onClick sur obj:
   *   - Si l'objet a des enfants -> current_element = obj + drawChildren(current_element.children)
   */
  /**
   * drawSlider() :
   * - On efface le slider
   * - On récupère la date min et la date max dans la variable globale obs_values
   * - On dessine le slider (VOIR POUR LE FORMAT DES DATES SUR LE SLIDER)
   * - OnChange :
   *     - Stockage de la date sélectionnée dans la variable globale selected_date
   *     - updateValues(selected_date) -> Stockage dans current_values
   *     - drawChildren(current_element.children)
   */
  /**
   * updateValues(date) :
   * - Filtrer l'objet obs_values afin de garder la date la plus proche de 'date' pour chaque expérience.
   * - Retourner le nouvel objet
   */
})(d3);

/**
 * TODO :
 */
