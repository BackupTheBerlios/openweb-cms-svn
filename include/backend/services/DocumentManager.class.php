<?php
/**
 * Gestion des documents
 * @package Backend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @uses Manager
 * @uses ReferenceManager
 * @uses Document
 */

require_once (PATH_INC_BASECLASS.'Manager.class.php');
require_once (PATH_INC_BACKEND_SERVICE.'Document.class.php');
require_once (PATH_INC_BACKEND_SERVICE.'ReferenceManager.class.php');

class DocumentManager extends Manager
{
  /**
   * Nombre d'éléments à traiter par page
   * @var integer $nbParPage
   */
  var $nbParPage = 20;

  /**
   * Nombre de liens à afficher dans la liste des pages
   * @var integer $nbLiensMax
   */
  var $nbLiensMax = 10;

  /**
   * Récupérer une liste d'articles en fonction de critères
   * @param   array   $params         tableau associatif contenant les critères de sélection. clé : nom du paramètre, valeur : valeur du paramètre
   * @param   array   $infosPages     tableau associatif contenant des indications sur le nombre de pages, la liste des numéros de pages disponibles, etc.
   * @return  array   liste des articles
   * @see Manager::getIndexPages
   * @see Manager::_getListPage
   * @see Manager::_getCount
   */
  function getListPage($params, &$infosPages)
  {
    // Construction de la requête en fonction des paramètres
    $sql = 'SELECT a.doc_id as id, doc_auteurs,
              doc_titre as titre, doc_etat as etat, doc_date_publication,
              doc_date_enregistrement, doc_date_modification,
              u.uti_nom as nom, u.uti_prenom as prenom ';
    $from = ' FROM uti_utilisateur u, doc_document a';
    $where = ' WHERE u.uti_id = a.uti_id_soumis ';

    if(isset($params['uti']) && intval($params['uti']) > 0)
    {
      $where .= ' AND uti_id_soumis = '.intval($params['uti']);
    }
    if(isset($params['status']) && intval($params['status']) > 0)
    {
      $where .= ' AND doc_etat = '.trim($params['status']);
    }

    if(isset($params['type']) && strlen($params['type']) == 1)
    {
      $where .= ' AND typ_id = \''.trim($params['type']).'\'';
    }

    $ref = new ReferenceManager($this->db);
    $i = 0;
    foreach($params as $clef => $val)
      if(strpos($clef, 'cri_') === 0 && $val != 'none')
      {
        $crit = $ref->getCriterionInfos(substr($clef, 4));
	$crit = $crit['id'];
	$tmpsql = 'SELECT doc_id FROM doc_document WHERE doc_repertoire = '.$this->db->quote($val);
	$res = $this->_getRow($tmpsql);
	$intro = $res['doc_id'];
	
        $from .= ", document_criteres c$i";
        $where .= " AND a.doc_id = c$i.doc_id AND c$i.cri_id = ".intval($crit)." AND c$i.intro_id = ".intval($intro);
        $i++;
      }

    if(isset($params['pg']))
      $page = intval($params['pg']);
    else
      $page = 0;

    $nombreLigneTotal = $this->_getCount($from.$where);

    $infosPages = $this->getIndexPages($page, $this->nbParPage, $nombreLigneTotal, $this->nbLiensMax );

    $sql = $sql.$from.$where." ORDER BY doc_date_enregistrement DESC";

    return $this->_getListPage($sql, $page, $this->nbParPage);
  }

  /**
   * Récupérer une liste d'article en fonction d'un seul classement
   * @param   string  $classmt   nom du classement
   * @return  array   liste des articles
   * @see Manager::_getList
   */
  function getListBy1Critere($classmt)
  {
    $sql = 'SELECT a.doc_id as id, a.doc_titre_mini as titre, c.ordre as ordre
            FROM doc_document a, doc_document b, document_criteres c
            WHERE a.doc_id = c.doc_id AND b.doc_id = c.intro_id
            AND c.doc_id <> c.intro_id
            AND b.doc_repertoire = '.$this->db->quote($classmt).'
            ORDER BY c.ordre ASC';

    return $this->_getList($sql);
  }


  /**
   * Modifier l'ordre des documents en base pour un seul classement
   * @param   string  $classmt   nom du classement
   * @param   array   $ordres    tableau associatif : doc id => ordre
   * @return  boolean vrai si réussite
   */
  function setDocumentOrder($classmt, $ordres)
  {

    $sql = 'SELECT doc_id FROM doc_document
            WHERE doc_repertoire = '.$this->db->quote($classmt);
    $res = $this->_getRow($sql);

    if(count($res) < 1)
      return false;

    $catid = intval($res['doc_id']);
    $ret = true;

    foreach($ordres as $doc_id => $ordre)
    {
      $sql = 'UPDATE document_criteres SET ordre = '.intval($ordre). ' WHERE intro_id = '.$catid.' AND doc_id = '.intval($doc_id);
      if(DB::isError($this->db->query($sql)))
        $ret = false;
    }

    return $ret;
  }

  /**
   * Regénère une partie de la liste de documents
   * une partie seulement, pour éviter les timeout lors de l'exécution du script
   * @param  integer   $page     indice renvoyé par l'appel précédent de cette méthode. donner 0 au premier appel
   * @return integer   indice pour le prochain appel à la méthode, ou 0 si il n'y a plus de document à traiter
   */
  function toutRegenerer($page)
  {
    $nbrtotal = $this->nbrDocs();

    $page = intval($page);
    $nombre = intval($this->nbParPage);

    if($nbrtotal > 0 && $page < $nbrtotal && $this->nbParPage > 1)
    {
      $sql = 'SELECT doc_id FROM doc_document ORDER BY doc_id LIMIT '.$page.','.$this->nbParPage;
      $liste = $this->_getList($sql);

      foreach($liste as $docdata)
      {
        $doc = new Document($this->db, $docdata['doc_id']);
        $doc->generation();
      }

      return ($page + $this->nbParPage <= $nbrtotal ? $page + $this->nbParPage : 0);
    }
    return 0;
  }

  /**
   * Nombre de documents enregistrés dans la base de données
   * @return integer nombre de documents
   */
  function nbrDocs()
  {
    return $this->_getCount('FROM doc_document');
  }
}
?>
