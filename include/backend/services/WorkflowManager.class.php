<?php
/**
 * Gestion du workflow des documents
 * @package Backend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @uses Manager
 */

require_once (PATH_INC_BASECLASS.'Manager.class.php');

class WorkflowManager extends Manager
{
  /**
   * ID de l'utilisateur pour gérer les droits d'accès
   * @var     integer
   */
  var $user_id;

  /**
   * Construit un nouvel objet WorkflowManager
   * @param   object  $db connexion à la base de données
   * @param   integer $user_id ID de l'utilisateur
   */
  function WorkflowManager(&$db, $user_id)
  {
    parent::Manager($db);
    $this->user_id = $user_id;
  }

  /**
   * Renvoie la liste des actions possibles sur un document
   * @param   integer $docid id du document concerné
   * @param   boolean $all indique s'il faut également renvoyer toutes les actions pour l'utilisateur
   * @return  array   liste des actions possibles
   */
  function getListActions($docid, $all = false)
  {
    $liste = array();

    if($all)
      $sql = 'SELECT DISTINCT w.act_name as act_name,
              a.act_libelle as act_libelle
              FROM prm_permsbackend a, wkf_workflow w, uti_utilisateur u
              WHERE w.uti_type REGEXP CONCAT(".*[", u.uti_type, "].*")
              AND a.act_name = w.act_name
              AND u.uti_id = '.$this->user_id.'
              ORDER BY act_id';
    else
      $sql = 'SELECT DISTINCT w.act_name as act_name,
              a.act_libelle as act_libelle
              FROM prm_permsbackend a, wkf_workflow w, doc_document d,
              uti_utilisateur u
              WHERE w.uti_type REGEXP CONCAT(".*[", u.uti_type, "].*")
              AND (d.uti_id_soumis = u.uti_id OR w.only_author = 0)
              AND w.doc_etat_in = d.doc_etat
              AND a.act_name = w.act_name
              AND u.uti_id = '.$this->user_id.'
              AND d.doc_id = '.intval($docid).'
              ORDER BY act_id';

    $res = $this->_getList($sql);

    foreach($res as $act)
      $liste[$act['act_name']] = $act['act_libelle'];

    return $liste;
  }

  /**
   * Indique s'il est possible d'effectuer une action donnée sur un document donné
   * utilisé pour la vérification avant d'effectuer l'action proprement dite
   * @param   integer $docid  id du document concerné
   * @param   integer $action nom de l'action à tester
   * @return  boolean réponse
   */
  function canDoAction($docid, $action)
  {
    $sql = 'SELECT act_name
            FROM wkf_workflow a, doc_document d, uti_utilisateur u
            WHERE d.doc_etat = a.doc_etat_in
            AND a.uti_type REGEXP CONCAT(".*[", u.uti_type, "].*")
            AND (d.uti_id_soumis = u.uti_id OR a.only_author = 0)
            AND u.uti_id = '.$this->user_id.'
            AND d.doc_id = '.intval($docid).'
            AND a.act_name = '.$this->db->quote($action);

    $res = $this->_getRow($sql);

    if(isset($res['act_name']))
      return true;
    else
      return false;
  }

  /**
   * Renvoie les informations pour une action
   * @param string $action l'action demandée
   * @param integer $docid l'id du document concerné
   * @return array informations sur l'action
   */
  function getActionInfos($action, $docid)
  {
    $sql = 'SELECT act_name, w.uti_type as uti_type, doc_etat_in, doc_etat_out,
            only_author
            FROM wkf_workflow w, doc_document d,
            uti_utilisateur u
            WHERE w.uti_type REGEXP CONCAT(".*[", u.uti_type, "].*")
            AND (d.uti_id_soumis = u.uti_id OR w.only_author = 0)
            AND w.doc_etat_in = d.doc_etat
            AND u.uti_id = '.$this->user_id.'
            AND d.doc_id = '.intval($docid).'
            AND w.act_name = '.$this->db->quote($action);

    return $this->_getRow($sql);
  }

}
?>
