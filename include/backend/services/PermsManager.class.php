<?php
/**
 * Gestion des permissions du backend
 * @package Backend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @uses Manager
 */

require_once (PATH_INC_BASECLASS.'Manager.class.php');

class PermsManager extends Manager
{
  /**
   * Groupe de l'utilisateur pour gérer les droits d'accès
   * @var string
   */
  var $user_type;

  /**
   * Construit un nouvel objet PermsManager
   * @param object $db Connexion à la base de données
   * @param string $user_type Groupe de l'utilisateur
   */
  function PermsManager(&$db, $user_type)
  {
    parent::Manager($db);
    $this->user_type = $user_type;
  }

  /**
   * Renvoie l'ID d'une action à partir de son nom.
   * @param string $nom nom symbolique de l'action
   * @return integer ID de l'action
   */
  function getActionId($action)
  {
    $sql = 'SELECT act_id FROM prm_permsbackend WHERE act_name = '.$this->db->quote($action).' AND uti_type LIKE '.$this->db->quote('%'.$this->user_type.'%');
    $res = $this->_getRow($sql);
    return $res['act_id'];
  }

  /**
   * Renvoie la liste des actions autorisées à partir de l'action $action
   * @param mixed $action  « action-mère », entier (champ act_id) ou chaîne (act_name)
   * @return array liste des actions possibles
   */
  function getActions($action)
  {
    if(!is_int($action))
      $action = $this->getActionId($action);

    $sql = 'SELECT act_id, act_libelle, act_param, act_name FROM prm_permsbackend WHERE act_parent = '.intval($action).' AND uti_type LIKE '.$this->db->quote('%'.$this->user_type.'%').' ORDER BY act_id';

    return $this->_getList($sql);
  }

  /**
   * Renvoie les informations pour une action
   * @param mixed $action l'action demandée
   * @return array informations sur l'action
   */
  function getActionInfos($action)
  {
    if(!is_int($action))
      $action = $this->getActionId($action);

    $sql = 'SELECT act_id, act_parent, uti_type, act_libelle, act_param, act_name FROM prm_permsbackend WHERE act_id = '.$action;
    return $this->_getRow($sql);
  }

  /**
   * Vérifie si un utilisateur peut accomplir une action
   * @param string $action l'action demandée
   * @return boolean réponse
   */
  function canDoAction($action)
  {
    $sql = 'SELECT act_libelle FROM prm_permsbackend WHERE act_name = '.$this->db->quote($action).' AND uti_type LIKE '.$this->db->quote('%'.$this->user_type.'%');
    $res = $this->_getRow($sql);
    if(isset($res['act_libelle']))
      return true;
    else
      return false;
    return false;
  }

  /**
   * Renvoie l'arbre généalogique d'une action, en commençant par les ancêtres
   * @param mixed $action action dont il faut trouver les parents (id ou nom)
   * @return array lignée
   */
  function getActionAncestors($action)
  {
    if(!is_int($action))
      $id = $this->getActionId($action);

    $ancestors = array();
    $ancestors[] = $id;

    /* A cette étape non seulement du programme, mais également de mon
       existence, ce n'est pas sans une certaine émotion que je vais accomplir
       cet acte hautement symbolique : pour la première fois en sept années
       passées à gratter du code source j'ai besoin de la boucle do...while.
       Chère boucle je verse ici une petite larme de joie pour toi. */
    do
    {
      $sql = 'SELECT act_parent FROM prm_permsbackend WHERE act_id = '.intval($id);
      $res = $this->_getRow($sql);
      $ancestors[] = $id = $res['act_parent'];
    } while($id > 1);

    return array_reverse($ancestors);
  }

}
?>
