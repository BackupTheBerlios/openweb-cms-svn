<?php
/**
 * Ensemble de services pour les pages du frontend
 * @package Frontend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @uses Manager
 */

require_once (PATH_INC_BASECLASS.'Manager.class.php');

class FrontService extends Manager
{
  var $nbLigneParPage = 15;
  var $nbLiensMax = 10;

  /**
   * Récupérer une liste d'article en fonction de critères
   * @param   array   $params         tableau associatif contenant les critères de sélection, (nom du paramètre => valeur du paramètre)
   * @param   array   $infosPages     tableau associatif contenant des indications sur le nombre de pages, la liste des numéros de pages dispo, etc.
   * @return  array   liste des articles
   */
  function getListPage($params, &$infosPages)
  {
    $select = 'SELECT d.doc_id as id, d.doc_titre as titre,
            d.doc_auteurs as auteurs,
            CONCAT(typ_repertoire, "/", d.doc_repertoire) as repertoire,
            d.doc_accroche as accroche, d.doc_date_modification';
    $from = 'FROM doc_document d NATURAL JOIN typ_typedocument';
    $where = 'WHERE d.doc_etat > 0';

    if(isset($params['type']) && strlen($params['type']) == 1)
      $where .= ' AND d.typ_id = \''.trim($params['type']).'\'';
    if(isset($params['repertoire']))
      $where .= ' AND doc_repertoire = '.$this->db->quote($params['repertoire']);

    $i = 0;
    if(isset($params['classement']))
      foreach($params['classement'] as $intro => $strict)
      {
	$from .= ", document_criteres c$i, doc_document d$i";
	$where .= " AND d.doc_id = c$i.doc_id AND c$i.intro_id = d$i.doc_id AND d$i.doc_repertoire = ".$this->db->quote($intro);
        if($strict === true)
          $where .= " AND c$i.doc_id != d$i.doc_id";
	$i++;
      }

    /**
     * @todo être plus précis sur le tri : trier selon l'ordre des critères, et pour chaque critère selon l'ordre des intros
     */
    if($i > 0)
      $order = 'ORDER BY c0.ordre ASC';
    else
      $order = 'ORDER BY d.doc_date_modification DESC';

    if(isset($params['pg']))
      $page = intval($params['pg']);
    else
      $page = 0;
    
    $nombreLigneTotal = $this->_getCount($from.' '.$where);

    $infosPages = $this->getIndexPages($page, $this->nbLigneParPage, $nombreLigneTotal, $this->nbLiensMax);

    $sql = $select.' '.$from.' '.$where.' '.$order;

    $liste = $this->_getListPage($sql, $page, $this->nbLigneParPage);

    foreach($liste as $k => $art)
    {
      $liste[$k]['repertoire'] = preg_replace('/\/+/', '/', '/'.$art['repertoire'].'/');
      $liste[$k]['classement'] = $this->getClassements($art['id']);
      $liste[$k]['date'] = strtotime($art['doc_date_modification']);
    }
    return $liste;
  }

  /**
   * Récupère le classement d'un document
   * @param integer $docid id du document cherché
   * @return array les classements
   */
  function getClassements($doc_id)
  {
    $classements = array();
    $sql = "SELECT cri_name, doc_repertoire, doc_titre_mini
            FROM document_criteres c
            NATURAL JOIN cri_criteres, doc_document d
            WHERE intro_id = d.doc_id AND c.doc_id <> d.doc_id
            AND c.doc_id = ".intval($doc_id);
    $res = $this->_getList($sql);

    foreach($res as $val)
      $classements[$val['cri_name']][$val['doc_repertoire']] = $val['doc_titre_mini'];

    return $classements;
  }

  /**
   * Récupère le chemin de stockage d'un document
   * @param integer $doc_id id du document concerné
   * @return string chemin, relatif à la racine du site
   * @see Document::getDocumentPath
   */
  function getDocumentPath($doc_rep)
  {
    $sql = "SELECT CONCAT(typ_repertoire, '/', doc_repertoire) as rep
            FROM doc_document NATURAL JOIN typ_typedocument
            WHERE doc_repertoire = ".$this->db->quote($doc_rep);
    $res = $this->_getRow($sql);
    return preg_replace('/\/+/', '/', '/'.$res['rep'].'/');
  }
}

?>
