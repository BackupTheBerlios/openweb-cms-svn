<?php
/**
 * Ensemble de fonctions pour extraire les informations d'un fichier au format DocBook
 * @package Backend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @see XML_Tree
 */

require_once(PATH_INC_BACKEND_SERVICE.'DocInfos.class.php');
require_once('PEAR/ErrorStack.php');
require_once("XML/Tree.php");

/**
 * Renvoie les enfants d'un élément d'après leur nom
 * @param object XML_Tree_Node $node élément parent à parcourir
 * @param string $name nom de l'élément enfant à sélectionner
 * @return array tableaux d'objects XML_Tree_Node
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
 * @param object XML_Tree_Node $element
 * @todo vérifier si la fonction ne retire pas trop d'espace, ou au contraire n'en laisse pas qui auraient dû être supprimées.
 * @todo remplacer les entités XML par les caractères correspondants (&amp;, &lt;, et &gt;)
 * @return string
 */
function tree2text($element)
{
  return preg_replace("/&#([0-9]+);/me", "chr('\\1')", _tree2text($element));
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

function _prendfiltres($element)
{
  $res = array();

  foreach(getElementsByTagName($element, "subjectterm") as $child)
    $res[] = tree2text($child);

  return $res;
}

function _toUtf8($srcenc, $str)
{
  if(!strcasecmp($srcenc, 'UTF-8'))
    return $src;

  if(function_exists('iconv'))
    return iconv($srcenc, 'UTF-8', $str);

  if(function_exists('utf8_encode') && !strcasecmp($srcenc, 'ISO-8859-1'))
    return utf8_encode($str);

  return null;
}

/**
 * Récupérer toutes les informations dans un fichier DocBook.
 * @param string $filename nom du fichier à analyser
 * @return object DocInfos objet contenant les infos de l'article, ou un tableau de chaînes contenant des messages d'erreurs
 * @todo Relire le code, il ne vérifie parfois pas assez les données d'entrées/les erreurs qui peuvent survenir pendant le traitement.
 */
function docbookGetArticleInfoFromFile($filename)
{
  $doc = new DocInfos();
  $errors = &PEAR_ErrorStack::singleton('OpenWeb_Backend_DocbookParse');

  /* Extrait du XML toutes les informations pour remplir la classe Article */

  /* Obtenir un arbre à partir du fichier XML */
  $xmltree = new XML_Tree($filename);
  /* lecture du fichier qui récupère la balise <article> */
  $article = $xmltree->getTreeFromFile();

  if($xmltree->isError($article))
  {
    $erreur = $article->getUserInfo();
    if($erreur == '')
      $erreur = 'probablement un document mal formé';
    return array('Impossible de parser le fichier XML ('.$erreur.')');
  }

  $charset = xml_parser_get_option($xmltree->parser, XML_OPTION_TARGET_ENCODING);

  $doc->repertoire = _toUtf8($charset, trim($article->getAttribute("id")));
  $doc->type = _toUtf8($charset, array_key_exists("role", $article->attributes) ?
                 $article->getAttribute("role") : "article");
  $doc->lang = _toUtf8($charset, array_key_exists("lang", $article->attributes) ?
                 $article->getAttribute("lang") : "fr");

  $artinfonode = getElementsByTagName($article, "articleinfo");
  if(count($artinfonode) == 0)
     return array('Impossible de continuer à parser le fichier XML : il manque la section articleinfo du docbook');
  /* Traitement particulier pour les balises <author> */
  $auteurs = array();

  foreach(getElementsByTagName($artinfonode[0], "author") as $author)
  {
    $firstnames = getElementsByTagName($author, "firstname");
    $surnames = getElementsByTagName($author, "surname");
    $auteurs[] = (count($firstnames) ? ucfirst(trim(tree2text($firstnames[0])))
                   : "").' '.
                 (count($surnames) ? ucfirst(trim(tree2text($surnames[0])))
                   : "");
  }
  $doc->auteurs = _toUtf8($charset, implode(', ', $auteurs));

  $doc->classement = array();

  foreach($artinfonode[0]->children as $child)
  {
    /* Récupération des balises <title>, <pubdate> et <date> */
    if(!strcmp($child->name, "title")) $doc->titre = _toUtf8($charset, tree2text($child));
    if(!strcmp($child->name, "titleabbrev")) $doc->titremini = _toUtf8($charset, tree2text($child));
    if(!strcmp($child->name, "pubdate")) $doc->pubdate = _toUtf8($charset, tree2text($child));
    if(!strcmp($child->name, "date")) $doc->update = _toUtf8($charset, tree2text($child));

    if(!strcmp($child->name, "subjectset"))
    {
      foreach(getElementsByTagName($child, "subject") as $subjnode)
      {
        $attribute = _toUtf8($charset, $subjnode->getAttribute("role"));
        $doc->classement[$attribute] = array();
        foreach(getElementsByTagName($subjnode, "subjectterm") as $entry)
          $doc->classement[$attribute][] = _toUtf8($charset, tree2text($entry));
      }
    }
    if(!strcmp($child->name, "abstract"))
      $doc->accroche = _toUtf8($charset, tree2text($child));
  }

  return $doc;
}

?>
