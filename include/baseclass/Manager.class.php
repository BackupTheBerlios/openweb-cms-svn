<?php
/**
 * Classe de base de gestion
 * @abstract
 * @package OpenWeb-CMS
 * @author Laurent Jouanneau
 * @copyright 2003 OpenWeb.eu.org
 * @version 0.0.1
 * @todo
 * @see
 */


class Manager
{
  var $db;

  /*
   * constructeur
   * @param   DB  a pear DB connection to database
  */
  function Manager($db)
  {
    $this->db = &$db;
  }

  /**
   * récupère une liste
   * @access private
   * @param   string  la requête à effectuer
   * @return array
   */
  function _getList($query)
  {
    $res = $this->db->getAll($query);
    if(DB::isError($res))
      trigger_error($res->userinfo.' ('.get_class($this).'::_getList)', E_USER_ERROR);
    return $res;
  }


  /**
   * récupère une liste pour faire du page par page (pas de group by dans la requête : résultats imprévus...)
   * @access private
   * @param   string  $query       requête
   * @param   integer $numpage     numéro de page
   * @param   integer $maxparpage  nombre de lignes par page
   * @return  array   la liste
   */
  function _getListPage($query, $numpage, $maxparpage)
  {
    if($numpage == '')
      $numpage = 1;

    $numreview = ($numpage - 1) * $maxparpage;

    if($maxparpage > 0)
      $query.=" limit " .intval($numreview).','.intval($maxparpage);

    $res = $this->db->getAll($query);
    if(DB::isError($res))
      trigger_error($res->userinfo.' ('.get_class($this).'::_getListPage)', E_USER_ERROR);
    return $res;
  }


  /**
   * récupère le nombre total de lignes pour une requête donnée
   * pour faire du page par page (pas de group by dans la requête : résultats imprevus...)
   * @access private
   * @param   string  $query_fromwhere   partie FROM et WHERE de la requête
   * @return  integer   le nombre total de lignes
   */
  function _getCount($query_fromwhere)
  {
    $res = $this->db->getRow('SELECT count(*) as cnt '.$query_fromwhere);
    if(DB::isError($res))
      trigger_error($res->userinfo.' ('.get_class($this).'::_getCount)', E_USER_ERROR);
    return intval($res['cnt']);
  }

   /**
   * récupère un enregistrement
   * @access private
   * @param   string  la requête à effectuer
   * @return array
   */
  function _getRow($query)
  {
    $res = $this->db->getRow($query);
    if(DB::isError($res))
      trigger_error($res->userinfo.' ('.get_class($this).'::_getRow)', E_USER_ERROR);
    return $res;
  }


  /**
   * calcule différents index de pages (précédent, suivant, fenêtre suivante, etc.)
   * @param integer $pageCourante    numéro de la page courante (de 1 à ...)
   * @param integer $nbLigneParPage  nombre de ligne par page
   * @param integer $totalLigne      nombre total de ligne
   * @param integer $nbLiensMax      nombre de liens maximum à afficher
   * @return array  tableau associatif contenant les différents index. index à 0 = index inexistant
   */
  function getIndexPages($pageCourante, $nbLigneParPage, $totalLigne, $nbLiensMax)
  {
    $pages = array();
    $index = array();
    $pageCourante = intval($pageCourante);
    if($pageCourante < 1)
      $pageCourante = 1;

    // calcul du nombre de pages au total
    $nombrePages = intval($totalLigne / $nbLigneParPage);
    if ($totalLigne % $nbLigneParPage) $nombrePages++;

    // calcul du nombre de fenêtres (fenêtre = un ensemble de pages)
    $nombreFenetre = intval($nombrePages / $nbLiensMax);
    if ($nombrePages % $nbLiensMax) $nombreFenetre++;

    // calcul index fenêtre courante
    $fenetreCourante = intval($pageCourante / $nbLiensMax);
    if ($pageCourante % $nbLiensMax) $fenetreCourante++;

    // calcul index page précédente
    $pagePrecedente = ($pageCourante > 1) ? $pageCourante - 1 : 0;

    // calcul index page suivante
    $pageSuivante = ($pageCourante < $nombrePages && $nombrePages > 1) ? $pageCourante + 1 : 0;

    // calcul index fenêtre précédente
    $fenetrePrecedente = ($fenetreCourante > 1) ? ($fenetreCourante - 1) * $nbLiensMax : 0;

    // calcul index fenêtre suivante
    $fenetreSuivante = ($fenetreCourante < $nombreFenetre) ? $fenetreCourante * $nbLiensMax + 1 : 0;


    // calcul des index de page de la fenêtre courante
    for ($jump_to_page = 1 + (($fenetreCourante - 1) * $nbLiensMax); ($jump_to_page <= ($fenetreCourante * $nbLiensMax)) && ($jump_to_page <= $nombrePages); $jump_to_page++)
    {
      $pages[] = $jump_to_page;
    }

    // calcul de l'intervalle du nombre de ligne inclus dans la page
    // (pour affichage du genre : produit 5 à 15)
    $lignesMinPage = ($nbLigneParPage * ($pageCourante - 1)) + 1;
    $lignesMaxPage = ($nbLigneParPage * $pageCourante);
    if ($lignesMaxPage > $totalLigne)
      $lignesMaxPage = $totalLigne;


    $index = array();
    $index['pagecour'] = $pageCourante;
    $index['pageprec'] = $pagePrecedente;
    $index['pagesuiv'] = $pageSuivante;
    $index['fenprec'] = $fenetrePrecedente;
    $index['fensuiv'] = $fenetreSuivante;
    $index['pages'] = $pages;
    $index['lignemin'] = $lignesMinPage;
    $index['lignemax'] = $lignesMaxPage;
    $index['liensmax'] = $nbLiensMax;
    $index['totalligne'] = $totalLigne;
    $index['totalpage'] = $nombrePages;
    return $index;
  }
}
?>
