<?php
/**
 * Méta-données d'un article
 * Vérifie et regroupe toutes les méta-données à propos d'un article.
 * @package OpenWeb-CMS
 * @author Florian Hatat
 * @copyright 2003 OpenWeb.eu.org
 */

require_once('PEAR/ErrorStack.php');
require_once("XML/Tree.php");

class DocInfos {

  /**
   * libellé du type de document
   * @var string
   */
  var $type;

  /**
   * Noms des auteurs séparés par des virgules
   * @var string
   */
  var $auteurs;

  /**
   * Titre
   * @var string
   */
  var $title;

  /**
   * Titre abrégé
   * @var string
   */
  var $titleabbrev;

  /**
   * nom du répertoire de l'article
   * Celui-ci est unique et peut servir à identifier l'article dans la base
   * Il ne doit contenir que des caractères alphanumériques ou l'underscore.
   * @var string
   */
  var $repertoire;

  /**
   * Date de publication.
   * Date de la première publication sur le site, au format AAAA-MM-JJ.
   * @var string
   */
  var $pubdate;

  /**
   * Date de modification.
   * Date de la dernière modification majeure de l'article.
   * @var array
   * @see $pubdate
   */
  var $date;

  /**
   * Sujet
   * Triplet de classement de l'article par Profil (indice "profil"),
   * Technologie ("technologie") et theme ("theme"). La validité
   * des entrées est vérifiée par la partie SQL.
   * @var array
   */
  var $classement;

  /**
   * accroche de l'article
   * @var string
   */
  var $abstract;

  /**
   * code langue de l'article : fr, en etc...
   * @var string
   */
  var $lang;

  /**
   * Jeu de caractères du document source.
   * @var string
   */
  var $charset;

  /** 
   * connexion à la base de données
   * @var object PEAR::DB
   */
  var $db;

  /**
   * Erreurs générées par les méthodes de la classe
   * @var object PEAR_ErrorStack
   */
  var $errors;

  /**
   * Constructeur DocInfos
   */
  function DocInfos(&$db)
  {
    $this->db = &$db;
    $this->errors = &PEAR_ErrorStack::singleton('OpenWeb::Backend::DocInfos');
  }

  /**
   * Récupérer toutes les informations dans un fichier DocBook.
   * @param string $filename nom du fichier à analyser
   * @return boolean booléen indiquant si le document XML a pu être lu
   * @throws PEAR_ErrorStack
   */
  function setFromDocbook($filename)
  {
    $xmltree = new XML_Tree($filename);
    $xmltree->setErrorHandling(PEAR_ERROR_CALLBACK,
      array(&$this, '_XMLTreeErrorHandler'));

    $article = $xmltree->getTreeFromFile();
    if($this->errors->hasErrors('error'))
     return false;

    $this->charset = xml_parser_get_option($xmltree->parser,
      XML_OPTION_TARGET_ENCODING);

    $this->repertoire = $this->stringValue(@$article->getAttribute("id"));

    $this->type = $this->stringValue(@$article->getAttribute("role"));

    $this->lang = $this->stringValue(@$article->getAttribute("lang"));

    $artinfonode = $this->getElementsByTagName($article, "articleinfo");
    if(count($artinfonode) == 0)
    {
      $this->errors->push(OW_MISSING_NODE, 'error',
        array('file' => $filename, 'node' => 'articleinfo'));
      return false;
    }

    /* Traitement particulier pour les balises <author> */
    $auteurs = array();

    foreach($this->getElementsByTagName($artinfonode[0], "author") as $author)
    {
      $firstnames = $this->getElementsByTagName($author, "firstname");
      $surnames = $this->getElementsByTagName($author, "surname");
      $nom = $this->stringValue(ucfirst(implode(' ',$firstnames)).' '.
        ucfirst(implode(' ',$surnames)));

      if(preg_match('/^\s*$/', $nom) != 0)
        $auteurs[] = $nom;
    }

    $this->auteurs = implode(', ', $auteurs);

    /* Traitement des autres méta-données */
    $this->classement = array();

    /**
     * Tableau des balises que l'on récupère sans traitement particulier
     */
    $autotags = array('title', 'titleabbrev', 'pubdate', 'date', 'abstract');

    foreach($artinfonode[0]->children as $child)
    {
      $name = $child->name;

      if(in_array($name, $autotags))
        $this->$name = $this->stringValue($child);

      if($name == 'subjectset')
        foreach($this->getElementsByTagName($child, 'subject') as $subjnode)
        {
          $attribute = $this->stringValue($subjnode->getAttribute('role'));

          if(empty($attribute))
            continue;

          $this->classement[$attribute] = array();

          foreach($this->getElementsByTagName($subjnode, 'subjectterm')
	      as $entry)
            $this->classement[$attribute][] = $this->stringValue($entry);
        }
    }
    return true;
  }

  /**
   * Récupérer toutes les informations depuis la base de données.
   * @param integer|string ID ou nom de répertoire du document à charger
   * @return boolean booléen indiquant si les informations ont pu être lues
   * @throws PEAR_ErrorStack
   */
  function setFromDB($id)
  {
  }

  /**
   * Vérifie le format des informations
   * @throws PEAR_ErrorStack
   * @return boolean
   */
  function check()
  {
    $res = true;

    if(!$this->_checkDate($this->pubdate))
    {
      $this->errors->push(OW_WRONG_DATE, 'error',
        array('node' => 'pubdate', 'value' => $this->pubdate));
      $res = false;
    }

    if(!$this->_checkDate($this->date))
    {
      $this->errors->push(OW_WRONG_DATE, 'error',
        array('node' => 'date', 'value' => $this->date));
      $res = false;
    }

    if(empty($this->auteurs))
    {
      $this->errors->push(OW_NO_AUTHOR, 'error');
      $res = false;
    }

    if(empty($this->title))
    {
      $this->errors->push(OW_NO_TITLE, 'error');
      $res = false;
    }

    $res = $res && $this->checkDir();

    return $res;
  }

  /**
   * Vérification d'une date.
   * Vérifie que la date passée en paramètre est bien au format AAAA-MM-JJ.
   * @param string $date la date à vérifier
   * @return boolean
   * @access private
   */
  function _checkDate($date)
  {
    if(preg_match("/([0-9]{4})-([0-1][0-9])-([0-3][0-9])/", $date, $regs))
      if(checkdate($regs[2], $regs[3], $regs[1])) /* date valide ? */
        return true;
    return false;
  }

  /**
   * vérification du répertoire
   * @return boolean
   */
  function checkDir()
  {
    $nbcar = strlen($this->repertoire);

    for($i = 0; $i < $nbcar; $i++)
      if(!(ctype_alnum($this->repertoire{$i}) || $this->repertoire{$i} == '_'))
      {
        $this->errors->push(OW_WRONG_DIR, 'error',
          array('illegal' => $this->repertoire{$i}));
        return false;
      }

    if($nbcar == 0)
    {
      $this->errors->push(OW_NO_DIR, 'error');
      return false;
    }

    return true;
  }

  /**
   * récupère et traite les erreurs renvoyées par le paquet XML_Tree
   */
  function _XMLTreeErrorHandler($error)
  {
    $params = array('pearerror' => $error);
    $msg = $error->getMessage();

    if(preg_match('/^XML_Parser: *(.*) at XML input line ([0-9]*)/',
      $msg, $infos) > 0)
    {
      $params['underlying'] = $infos[0][1];
      $params['line'] = $infos[0][2];
      $msg = false;
    }
    
    $this->errors->push($error->getCode(), 'error', $params, $msg);
  }

  /**
   * Renvoie les enfants d'un élément d'après leur nom
   * @param object XML_Tree_Node $node élément parent à parcourir
   * @param string $name nom de l'élément enfant à sélectionner
   * @return array tableaux d'objects XML_Tree_Node
   * @access private
   */
  function getElementsByTagName($node, $name)
  {
    $res = array();
    if(strcmp($node->name, ''))
      foreach($node->children as $child)
        if(!strcmp($name, $child->name))
          $res[] = $child;
    return $res;
  }

  /**
   * Transforme un élément en texte.
   * Cette fonction parcourt récursivement les descendants d'un élément et
   * renvoie la chaîne formée par la concaténation de toutes les valeurs
   * des enfants.
   * @param string|object XML_Tree_Node $element
   * @return string
   */
  function stringValue($element)
  {
    $res = '';
    if(is_object($element))
      $res = $this->_tree2text($element);
    else
      $res = strval($element);

    $patterns = array("/&#([0-9]+);/e", "/&#x([0-9a-f]+);/e",
      "/&lt;/", "/&gt;/", "/&amp;/", "/&apos;/", "/&quot;/");
    $replace = array("utf8Chr(intval(\\1))", "utf8Chr(intval(0x\\1))",
      "<", ">", "&", "'", '"');

    return preg_replace($patterns, $replace, toUtf8($res));
  }

  /**
   * @access private
   * @see tree2text
   */
  function _tree2text($element)
  {
    $text = $element->content;
    if(!strcmp($element->name, '')) /* pas d'enfants pour ce noeud */
      return $text;
    foreach($element->children as $child)
      $text .= _tree2text($child);
    return trim($text);
  }

  /**
   * Convertit une chaîne en UTF-8
   * Cette méthode convertit la chaîne $str passée en argument en UTF-8,
   * en utilisant les fonctions de conversion disponibles dans la version
   * de PHP.
   * @param string $str la chaîne à convertir
   * @return string|null le représentant de la chaîne en UTF-8, null en cas
   * d'erreur
   * @access private
   */
  function toUtf8($str)
  {
    $srcenc = $this->charset;

    if(!strcasecmp($srcenc, 'UTF-8'))
      return $src;

    if(function_exists('iconv'))
      if(($res = iconv($srcenc, 'UTF-8', $str)) !== false)
       return $res;

    if(function_exists('utf8_encode') && !strcasecmp($srcenc, 'ISO-8859-1'))
      return utf8_encode($str);

    $this->errors->push(OW_NO_CHARSET_CONVERSION, 'error',
      array('srcenc' => $srcenc, 'destenc' => 'UTF-8'));
    return null;
  }
}

/**
 * Donne la représentation UTF-8 d'un caractère Unicode
 * @param integer $num indice du caractère dans l'Unicode
 * @todo Vérifier que ce code est vraiment portable. Problèmes possibles : big/little endian, type integer qui n'est pas sur 32 bits.
 * @return string
 */
function utf8Chr($num)
{
  if($num < 127)
    return chr($num);

  if($num < 2048)
    return chr(192 + ($num >> 6)).chr(128 + ($num & 63));

  if($num < 65536)
    return chr(224 + ($num >> 12)).chr((128 + ($num >> 6) & 63)).
      chr(128 + ($num & 63));

  if($num < 2097152)
    return chr(240 + ($num >> 18)).chr(128 + (($num >> 12) & 63)).
      chr(128 + (($num >> 6) & 63)).chr(128 + ($num & 63));

  PEAR_ErrorStack::staticPush('OpenWeb::Backend::DocInfos', OW_WRONG_ENTITY,
    'warning', array('entity' => '&#'.strval($num).';'));

  return '';
}

?>
