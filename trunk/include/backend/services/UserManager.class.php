<?php
/**
 * Gestion des utilisateurs
 * Fournit une API pour manipuler les informations des utilisateurs
 * @package Backend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License
 * @uses Manager
 */

require_once (PATH_INC_BASECLASS.'Manager.class.php');

class UserManager extends Manager
{
  /**
   * Renvoie les informations d'un utilisateur
   * @param   string  $login
   * @param   string  $password
   * @return  array   liste des infos de l'utilisateur
   */
  function getUserDatas($login)
  {
    $sql = 'SELECT * FROM uti_utilisateur
      WHERE uti_login = '.$this->db->quote($login);

    $res = $this->_getRow($sql);

    if(count($res) > 0)
      return $res;
    else
      return null;
  }

  /**
   * Change les informations de l'utilisateur
   * @param array $datas nouvelles informations
   * @return boolean true si les données ont été changées
   */
  function setUserDatas($datas)
  {
    if(!is_array($datas))
      return false;

    if(!array_key_exists('uti_login', $datas))
      return false;

    if(($user = $this->getUserDatas($datas['uti_login'])) == null)
      return false;

    $sql = 'UPDATE uti_utilisateur SET ';

    /* Cette fonction est idiote et ne vérifie rien du tout ! */
    $sqlpart = array();
    if(!empty($datas['uti_password']))
    {
      $sqlpart[] = 'uti_password = MD5('.$this->db->quote($datas['uti_password']).')';
      unset($datas['uti_password']);
    }

    foreach($datas as $champ => $valeur)
      $sqlpart[] = "$champ = ".$this->db->quote($valeur);

    reset($sqlpart);
    if(($part = current($sqlpart)) !== false)
      $sql .= $part;

    while($part = next($sqlpart))
      $sql .= ', '.$part;

    $sql .= ' WHERE uti_id = '.$user['uti_id'];

    $res = $this->db->query($sql);
    if(DB::isError($res))
      return false;

    return true;
  }

  /**
   * Renvoie la liste des utilisateurs du backend
   * @param   boolean $all    vrai s'il faut renvoyer la liste complète des utilisateurs (même ceux dont le compte est marqué invalide)
   * @return  array    la liste des utilisateurs
   */
  function getUserList($all = false)
  {
    $sql = 'SELECT uti_id, uti_login, uti_nom, uti_prenom, uti_type, uti_valide FROM uti_utilisateur';
    if(!$all)
      $sql .= ' WHERE uti_valide = 1';

    return $this->_getList($sql);
  }

  /**
   * Ajoute un nouvel utilisateur dans la base de données
   * @param string $login login de l'utilisateur à ajouter
   * @return boolean vrai si la mise au monde réussit
   */
  function addUser($login)
  {
    if(empty($login))
      return false;

    $sql = 'INSERT INTO uti_utilisateur (uti_login) VALUES ('.$this->db->quote($login).')';

    $res = $this->db->query($sql);
    if(DB::isError($res))
      return false;

    return true;
  }

  /**
   * Supprime un utilisateur de la base de données
   * @param string $login login de l'utilisateur à supprimer
   * @return boolean vrai si l'éradication réussit
   */
   function deleteUser($login)
   {
     $sql = 'DELETE FROM uti_utilisateur WHERE uti_login = '.$this->db->quote($login);
     $res = $this->db->query($sql);
     if(DB::isError($res))
       return false;

     return true;
   }
}
?>
