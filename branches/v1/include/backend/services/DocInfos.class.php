<?php
/**
 * Méta-données d'un article
 * Vérifie et regroupe toutes les méta-données à propos d'un article.
 * @package OpenWeb-CMS
 * @author Florian Hatat
 * @copyright 2003 OpenWeb.eu.org
 */

require_once('PEAR/ErrorStack.php');

class DocInfos {

  /**
   * libellé du type de document
   * @var string
   */
  var $type = 'article';

  /**
   * Noms des auteurs séparés par des virgules
   * @var string
   */
  var $auteurs;

  /**
   * Titre
   * @var string
   */
  var $titre;

  /**
   * Titre abrégé
   * @var string
   */
  var $titremini;

  /**
   * nom du répertoire de l'article
   * Celui-ci est unique et peut servir à identifier l'article dans la base
   * Il ne doit contenir que des caractères alphanumériques ou l'underscore.
   * @var string
   */
  var $repertoire;

  /**
   * Date de publication
   * Date de la première publication sur le site, sous la forme d'un
   * tableau contenant les indices "year", "month" et "day".
   * @var array
   */
  var $pubdate;

  /**
   * Date de modification
   * Date de la dernière modification majeure de l'article.
   * @var array
   * @see $pubdate
   */
  var $update;

  /**
   * Sujet
   * Triplet de classement de l'article par Profil (indice "profil"),
   * Technologie ("technologie") et theme ("theme"). La validité
   * des entrées est vérifiée par la partie SQL.
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
   * Erreurs générées par les méthodes de la classe
   */
  var $errors

  /**
   * Constructeur DocInfos
   */
  function DocInfos()
  {
    $this->classement = array();
    $this->errors = &PEAR_ErrorStack::singleton('OpenWeb_Backend_DocInfos');
    $this->errors->setErrorMessageTemplate(
      array(
        1 => "Date incorrecte",
        2 => "Précisez au moins un auteur",
        3 => "Le titre ne doit pas être vide",
        4 => "Nom de répertoire invalide"
    ));
  }

  /**
   * Vérifie le format des informations
   * @return boolean
   */
  function verify()
  {
    $this->verifyPubdate();
    $this->verifyUpdate();
    $this->verifyAuteurs();
    $this->verifyTitre();
    $this->verifyRepertoire();
    return !$this->errors->hasErrors();
  }

  /**
   * Vérifie la date
   * Vérifie que la date passée en paramètre est bien au format AAAA-MM-JJ
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
   * Vérifie la date de publication
   * @return boolean
   */
  function verifyPubdate()
  {
    $res = $this->_verifyDate($this->pubdate);
    if(!$res)
      $this->errors->push(1);
    return $res;
  }

  /**
   * vérifie la date de modification
   * @return boolean
   */
  function verifyUpdate()
  {
    $res = $this->_verifyDate($this->pubdate);
    if(!$res)
      $this->errors->push(1);
    return $res;
  }

  /**
   * vérification des auteurs
   * @return boolean  indique si ok ou pas
   */
  function verifyAuteurs()
  {
    if($this->auteurs == '' )
    {
      $this->errors->push(2);
      return false;
    }
    return true;
  }

  /**
   * vérification du titre
   * @return boolean
   */
  function verifyTitre()
  {
    if($this->titre == '')
    {
      $this->errors->push(3);
      return false;
    }
    return true;
  }

  /**
   * vérification du répertoire
   * @return boolean
   */
  function verifyRepertoire()
  {
    $nbcar = strlen($this->repertoire);

    for($i = 0; $i < $nbcar; $i++)
      if(!(ctype_alnum($this->repertoire{$i}) || $this->repertoire{$i} == '_'))
      {
        $this->errors->push(4);
        return false;
      }
    return true;
  }
}
?>
