<?php
/**
 * Génération des différents formats de sorties d'un document
 * @package Backend
 * @subpackage Services
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

/**
 * Transformations disponibles.
 * Ce tableau associatif décrit toutes les transformations possibles. Il
 * s'organise sous la forme de trois tableaux associatifs imbriqués.
 * Chaque clef de ce tableau (1) définit le format d'entrée du document, et
 * la valeur est un tableau (2).
 * Les clefs du tableau (2) désignent le nom du format de sortie demandé
 * et les valeurs sont des tableaux (3).
 * Les tableaux (3) ont la structure suivante : la clef <code>file</code> indique le nom du fichier de sortie, <code>description</code> la description du format de sortie.
 * <code>$outputInfos['docbook']['xhtml']['file']</code> donne ainsi le nom du fichier obtenu en transformant du DocBook en XML.
 * @global array $outputInfos
 */
$outputInfos = array(
  'docbook' => array(
    'xhtml' => array('file' => 'index.php', 'description' => 'XHTML'),
    'xhtmlbrut' => array('file' => 'xhtml.xml', 'description' => 'XHTML brut')
  ),
  'xhtml' => array(
    'docbook' => array('file' => 'docbook.xml', 'description' => 'XML/Docbook')
  ),
  'phpwiki' => array(
    'docbook' => array('file' => 'docbook.xml', 'description' => 'XML/Docbook')
  )
);

/**
 * Génération de la sortie du document dans un format donné
 * @param string $src fichier source du document
 * @param string $informat format de la source
 * @param string $outformat format de sortie attendu
 * @param string $dest nom du fichier destination (null si par défaut)
 * @return boolean vrai si tout s'est bien passé
 * @uses $outputInfos
 */
function outputMake($src, $informat, $outformat, $dest = null)
{
  global $outputInfos;

  if(!isset($outputInfos[$informat]))
    return false;

  if(!isset($outputInfos[$informat][$outformat]))
    return false;

  /* Ne voir ici aucune allusion politique */
  $outfn = '_output_'.$informat.'_to_'.$outformat;
  if(!function_exists($outfn))
    return false;

  return $outfn($src, $dest);
}

/**
 * Transformation générique d'un document XML via XSLT
 * @param string $filename nom du fichier source
 * @param string $filename_source nom du fichier en sortie
 * @param string $stylesheet nom de la feuille XSLT
 * @param array $params paramètres à passer à la feuille XSLT
 * @access private
 * @return boolean vrai quand tout se passe bien
 */
function _output_xsl_generic_transform($filename, $filename_cible, $stylesheet, $params = array())
{
  /* Attention : code hautement toxique, il a fallu aller fouiller
     dans les sources de PHP pour découvrir une grande partie des engins
     nucléaires en jeu ici et rien ne garantit leur stabilité. Désolé pour
     le développement durable, c'est pas trop ça ici. */
  $xh = xslt_create();
  xslt_set_error_handler($xh, '_output_xsl_error_handler');
  $result = xslt_process($xh, $filename, PATH_INCLUDE."xslt/".$stylesheet,
               $filename_cible, array(), $params);
			   
  if(!$result)
  {
    $msgerr = 'Erreur XSLT '.xslt_errno($xh).' : '.xslt_error($xh);
    xslt_free($xh);
      return false;
  }
  else
    return true;
}

/**
 * Gestionnaire d'erreurs lors de la transformation XML
 *
 * Cette fonction intercepte les erreurs générées par le moteur XSLT et
 * les ajoute à la pile PEAR::ErrorStack, qui se charge de leur remontée
 * vers l'utilisateur.
 * @access private
 */
function _output_xsl_error_handler($xh, $error_level, $error_code, $messages)
{
  PEAR_ErrorStack::staticPush('OpenWeb::Backend::OutputFactory', OW_XSL, 
}

/**
 * @access private
 */
function _output_docbook_to_xhtml($src, $dest)
{
  global $outputInfos;
  return _output_xsl_generic_transform($src,
    $dest === null ? $outputInfos['docbook']['xhtml']['file'] : $dest,
    "xhtml/docbook.xsl", array('path_site_root' => PATH_SITE_ROOT));
}

/**
 * @access private
 */
function _output_docbook_to_xhtmlbrut($src, $dest)
{
  global $outputInfos;
  return _output_xsl_generic_transform($src,
    $dest === null ? $outputInfos['docbook']['xhtmlbrut']['file'] : $dest,
    "xhtmlbrut/docbook.xsl");
}

/**
 * @access private
 */
function _output_xhtml_to_docbook($src, $dest)
{
  global $outputInfos;
  return _output_xsl_generic_transform($src,
    $dest === null ? $outputInfos['xhtml']['docbook']['file'] : $dest,
    "docbook/xhtml.openweb.xsl");
}

/**
 * @access private
 */
function _output_phpwiki_to_docbook($src, $dest)
{
  global $outputInfos;
  return _output_xsl_generic_transform($src,
    $dest === null ? $outputInfos['xhtml']['docbook']['file'] : $dest,
    "docbook/xhtml.phpwiki.xsl");
}

?>
