<?php
/**
 * Ensemble de fonctions pour manipuler la liste des acronymes
 * @package Backend
 * @subpackage Services
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @see XML_Unserializer
 * @see XML_Serializer
 */

require_once('XML/Serializer.php');
require_once('XML/Unserializer.php');

/**
 * Renvoie les informations concernant un acronyme
 * @param string $acronym l'acronyme à rechercher dans la liste
 * @return array tableau contenant l'acronyme, sa signification et sa langue
 */
function getAcronym($acronym)
{
  $dat = _loadAcronymList();

  $res = array('acronym' => $acronym);

  foreach($dat as $entry)
    if($entry['_attrs']['acronym'] == $acronym)
    {
      $res['lang'] = $entry['_attrs']['lang'];
      $res['content'] = $entry['_content'];
    }

  return $res;
}

function setAcronym($acronym, $contents)
{
  $dat = _loadAcronymList();

  if(!empty($contents))
    $new = array(
        '_attrs' => array(
          'acronym' => $contents['acronym'],
          'lang' => $contents['lang']),
        '_content' => $contents['content']);

  $found = false;

  foreach($dat as $key => $entry)
    if($entry['_attrs']['acronym'] == $acronym)
    {
      $found = true;

      if(empty($contents))
        unset($dat[$key]);
      else
        $dat[$key] = $new;

      break;
    }

  if(!$found)
    $dat[] = $new;

  return _saveAcronymList($dat);
}

function _loadAcronymList()
{
  $unser = new XML_Unserializer(array(
    'parseAttributes' => TRUE,
    'attributesArray' => '_attrs'));

  $unser->unserialize(PATH_INCLUDE."xslt/inc/acronyms.xml", true);
  $dat = $unser->getUnserializedData();

  return $dat['word'];
}

function _saveAcronymList($dat)
{
  $ser = new XML_Serializer(array(
    'addDecl' => true,
    'encoding' => 'UTF-8',
    'defaultTagName' => 'word',
    'rootName' => 'acronyms',
    'attributesArray' => '_attrs',
    'contentName' => '_content',
    'indent' => '  '));

  $ser->serialize($dat);

  $res = false;
  ignore_user_abort(true);

  if(($fp = fopen(PATH_INCLUDE."xslt/inc/acronyms.xml", "w")) !== false)
  { 
    fwrite($fp, $ser->getSerializedData()."\n");
    fclose($fp);
    $res = true;
  }

  ignore_user_abort(false);

  return $res;
}

?>
