<?php
/**
 * Visualisation d'un article
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_DOC_VIEW');
define('OW_BACKEND_NOHTML', true);

require_once("../../include/backend/init.inc.php");
require_once(PATH_INC_BACKEND_SERVICE."Document.class.php");
require_once(PATH_INC_BACKEND_SERVICE.'WorkflowManager.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'mime.inc.php');

if(empty($_SERVER['PATH_INFO']))
{
  echo "Je ne ferai rien sans paramètres.\n";
  exit(1);
}

@list($id, $fic) = explode('/', trim(ereg_replace('/+', '/', $_SERVER['PATH_INFO']), '/'), 2);

if(empty($id))
{
  echo "ID du document manquant\n";
  exit(1);
}

$doc = new Document($db, $id);
if(count($doc->errors) != 0)
{
  echo "Impossible d'ouvrir le document\n";
  exit(1);
}

$wk = new WorkflowManager($db, $_SESSION['utilisateur']['uti_id']);
if(!$wk->canDoAction($doc->id, OW_BACKEND_ACTION))
{
  echo "Vous n'avez pas la permission de consulter le document\n";
  exit(1);
}

if(empty($fic))
{
  echo "Rien à afficher\n";
  exit(1);
}

$filenames = $doc->getDocumentFormats();
foreach($doc->listeAnnexe() as $format)
  $filenames[] = "annexes/".$format;

if(!in_array($fic, $filenames))
{
  echo "Format demandé inconnu\n";
  exit(1);
}

$fichier = PATH_SITE_ROOT.$doc->getDocumentPath().'/'.$fic;

$mime = mime($fichier);
if($mime == 'application/x-httpd-php')
{
  header('Content-disposition: inline; filename="'.$id.'.html"');
  include($fichier);
}
else
{
  header('Content-type: '.mime($fichier));
  header('Content-disposition: inline; filename="'.
    $id.'.'.substr(strrchr($fic, "."), 1).'"');

  openfic($fichier);
}

function openfic($filename)
{
  if(!($fp = @fopen($filename, "r")))
    return false;

  $res = fread($fp, filesize($filename));
  fclose($fp);

  print $res;
  return true;
}

?>
