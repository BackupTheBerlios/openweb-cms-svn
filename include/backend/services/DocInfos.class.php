<?php
/**
 * M�ta-donn�es d'un article
 * V�rifie et regroupe toutes les m�ta-donn�es � propos d'un article.
 * @package OpenWeb-CMS
 * @author Florian Hatat
 * @copyright 2003 OpenWeb.eu.org
 */

class DocInfos {

  /**
   * libell� du type de document
   */
  var $type = 'article';

  /**
   * Noms des auteurs s�par�s par des virgules
   * @var string
   */
  var $auteurs;

  /**
   * Titre
   * @var string
   */
  var $titre;

  /**
   * Titre abr�g�
   */
  var $titremini;

  /**
   * nom du r�pertoire de l'article
   * Celui-ci est unique et peut servir � identifier l'article dans la base
   * Il ne doit contenir que des caract�res alphanum�riques ou l'underscore.
   * @var string
   */
  var $repertoire;

  /**
   * Date de publication
   * Date de la premi�re publication sur le site, sous la forme d'un
   * tableau contenant les indices "year", "month" et "day".
   * @var array
   */
  var $pubdate;

  /**
   * Date de modification
   * Date de la derni�re modification majeure de l'article.
   * @var array
   * @see $pubdate
   */
  var $update;

  /**
   * Sujet
   * Triplet de classement de l'article par Profil (indice "profil"),
   * Technologie ("technologie") et theme ("theme"). La validit�
   * des entr�es est v�rifi�e par la partie SQL.
   * @var array
   */
  var $classement;

  /**
   * accroche de l'article (abstract)
   * @var string
   */
  var $accroche;

  /**
   * code langue de l'article : fr, en etc...
   * @var string
   */
  var $lang;

  /**
   * Constructeur DocInfos
   */
  function DocInfos()
  {
    $this->classement = array();
  }

  /**
   * V�rifie le format des informations
   * @return boolean
   */
  function verify()
  {
    return $this->verifyPubdate() && $this->verifyUpdate() &&
           $this->verifyAuteurs() && $this->verifyTitre() &&
           $this->verifyRepertoire();
  }

  /**
   * V�rifie la date
   * V�rifie que la date pass�e en param�tre est bien au format AAAA-MM-JJ
   * @return boolean
   * @access private
   */
  function _verifyDate($date)
  {
    if(ereg("([0-9]{4})-([0-1][0-9])-([0-3][0-9])", trim($date), $regs))
      if(checkdate($regs[2], $regs[3], $regs[1])) /* date valide ? */
        return true;
    return false;
  }

  /**
   * V�rifie la date de publication
   * @return boolean
   */
  function verifyPubdate()
  {
    $res = $this->_verifyDate($this->pubdate);
    if(!$res)
      $this->errors[] = "date de publication invalide";
    return $res;
  }

  /**
   * v�rifie la date de modification
   * @return boolean
   */
  function verifyUpdate()
  {
    $res = $this->_verifyDate($this->pubdate);
    if(!$res)
      $this->errors[] = "date de modification invalide";
    return $res;
  }

  /**
   * v�rification des auteurs
   * @return boolean  indique si ok ou pas
   */
  function verifyAuteurs()
  {
    if($this->auteurs == '' )
    {
      $this->errors[] = "pr�cisez au moins un auteur";
      return false;
    }
    return true;
  }

  /**
   * v�rification du titre
   * @return boolean
   */
  function verifyTitre()
  {
    if($this->titre == '')
    {
      $this->errors[] = "le titre ne doit pas �tre vide";
      return false;
    }
    return true;
  }

  /**
   * v�rification du r�pertoire
   * @return boolean
   */
  function verifyRepertoire()
  {
    $nbcar = strlen($this->repertoire);
    for($i = 0; $i < $nbcar; $i++)
      if(!(ctype_alnum($this->repertoire{$i}) || $this->repertoire{$i} == '_'))
      {
        $this->errors[] = 'nom de r�pertoire incorrect (il ne faut que des caract�res alphanum�riques)';
        return false;
      }
    return true;
  }
}
?>
