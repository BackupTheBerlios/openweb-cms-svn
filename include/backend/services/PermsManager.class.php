<?php
/**
 * Gestion des permissions du backend
 * @package Backend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright � 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @uses Manager
 */

require_once (PATH_INC_BASECLASS.'Manager.class.php');

class PermsManager extends Manager
{
  /**
   * Groupe de l'utilisateur pour g�rer les droits d'acc�s
   * @var string
   */
  var $user_type;

  /**
   * Construit un nouvel objet PermsManager
   * @param object $db Connexion � la base de donn�es
   * @param string $user_type Groupe de l'utilisateur
   */
  function PermsManager(&$db, $user_type)
  {
    parent::Manager($db);
    $this->user_type = $user_type;
  }

  /**
   * Renvoie l'ID d'une action � partir de son nom.
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
   * Renvoie la liste des actions autoris�es � partir de l'action $action
   * @param mixed $action  ��action-m�re��, entier (champ act_id) ou cha�ne (act_name)
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
   * @param mixed $action l'action demand�e
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
   * V�rifie si un utilisateur peut accomplir une action
   * @param string $action l'action demand�e
   * @return boolean r�ponse
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
   * Renvoie l'arbre g�n�alogique d'une action, en commen�ant par les anc�tres
   * @param mixed $action action dont il faut trouver les parents (id ou nom)
   * @return array lign�e
   */
  function getActionAncestors($action)
  {
    if(!is_int($action))
      $id = $this->getActionId($action);

    $ancestors = array();
    $ancestors[] = $id;

    /* A cette �tape non seulement du programme, mais �galement de mon
       existence, ce n'est pas sans une certaine �motion que je vais accomplir
       cet acte hautement symbolique : pour la premi�re fois en sept ann�es
       pass�es � gratter du code source j'ai besoin de la boucle do...while.
       Ch�re boucle je verse ici une petite larme de joie pour toi. */
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
