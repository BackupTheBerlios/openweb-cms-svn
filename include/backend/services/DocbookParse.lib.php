<?php
/**
 * Ensemble de fonctions pour extraire les informations d'un fichier au format DocBook
 * @package Backend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright � 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @see XML_Tree
 */

require_once(PATH_INC_BACKEND_SERVICE.'DocInfos.class.php');
require_once("XML/Tree.php");

/**
 * Renvoie les enfants d'un �l�ment d'apr�s leur nom
 * @param object XML_Tree_Node $node �l�ment parent � parcourir
 * @param string $name nom de l'�l�ment enfant � s�lectionner
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
 * Transforme un �l�ment en texte.
 * Cette fonction parcourt r�cursivement les descendants d'un �l�ment et
 * renvoie la cha�ne form�e par la concat�nation de toutes les valeurs
 * des enfants.
 * @param object XML_Tree_Node $element
 * @todo v�rifier si la fonction ne retire pas trop d'espace, ou au contraire n'en laisse pas qui auraient d� �tre supprim�es.
 * @todo remplacer les entit�s XML par les caract�res correspondants (&amp;, &lt;, et &gt;)
 * @return string
 */
function _tree2text($element)
{
  $text = $element->content;
  if(!strcmp($element->name, '')) /* pas d'enfants pour ce noeud */
    return $text;
  foreach($element->children as $child)
    $text .= _tree2text($child); /* Vive la r�cursivit� */
  return preg_replace("/&#([0-9]+);/me", "chr('\\1')", trim($text));
}

function _prendfiltres($element)
{
  $res = array();

  foreach(getElementsByTagName($element, "subjectterm") as $child)
    $res[] = _tree2text($child);

  return $res;
}

/**
 * R�cup�rer toutes les informations dans un fichier DocBook.
 * @param string $filename nom du fichier � analyser
 * @return object DocInfos objet contenant les infos de l'article, ou un tableau de cha�nes contenant des messages d'erreurs
 * @todo Mauvaise gestion des erreurs, � refaire.
 * @todo Relire le code, il ne v�rifie parfois pas assez les donn�es d'entr�es/les erreurs qui peuvent survenir pendant le traitement.
 */
function docbookGetArticleInfoFromFile($filename)
{
  $doc = new DocInfos();

  /* Extrait du XML toutes les informations pour remplir la classe Article */

  /* Obtenir un arbre � partir du fichier XML */
  $xmltree = new XML_Tree($filename);
  /* lecture du fichier qui r�cup�re la balise <article> */
  $article = $xmltree->getTreeFromFile();

  if($xmltree->isError($article))
  {
    $erreur = $article->getUserInfo();
    if($erreur == '')
      $erreur = 'probablement un document mal form�';
    return array('Impossible de parser le fichier XML ('.$erreur.')');
  }

  $doc->repertoire = trim($article->getAttribute("id"));
  $doc->type = array_key_exists("role", $article->attributes) ?
                 $article->getAttribute("role") : "article";
  $doc->lang = array_key_exists("lang", $article->attributes) ?
                 $article->getAttribute("lang") : "fr";

  $artinfonode = getElementsByTagName($article, "articleinfo");
  if(count($artinfonode) == 0)
     return array('Impossible de continuer � parser le fichier XML : il manque la section articleinfo du docbook');
  /* Traitement particulier pour les balises <author> */
  $auteurs = array();

  foreach(getElementsByTagName($artinfonode[0], "author") as $author)
  {
    $firstnames = getElementsByTagName($author, "firstname");
    $surnames = getElementsByTagName($author, "surname");
    $auteurs[] = (count($firstnames) ? ucfirst(trim(_tree2text($firstnames[0])))
                   : "").' '.
                 (count($surnames) ? ucfirst(trim(_tree2text($surnames[0])))
                   : "");
  }
  $doc->auteurs = implode(', ', $auteurs);

  $doc->classement = array();

  foreach($artinfonode[0]->children as $child)
  {
    /* R�cup�ration des balises <title>, <pubdate> et <date> */
    if(!strcmp($child->name, "title")) $doc->titre = _tree2text($child);
    if(!strcmp($child->name, "titleabbrev")) $doc->titremini = _tree2text($child);
    if(!strcmp($child->name, "pubdate")) $doc->pubdate = _tree2text($child);
    if(!strcmp($child->name, "date")) $doc->update = _tree2text($child);

    if(!strcmp($child->name, "subjectset"))
    {
      foreach(getElementsByTagName($child, "subject") as $subjnode)
      {
        $attribute = $subjnode->getAttribute("role");
        $doc->classement[$attribute] = array();
        foreach(getElementsByTagName($subjnode, "subjectterm") as $entry)
          $doc->classement[$attribute][] = _tree2text($entry);
      }
    }
    if(!strcmp($child->name, "abstract"))
      $doc->accroche = _tree2text($child);
  }

  return $doc;
}

?>
