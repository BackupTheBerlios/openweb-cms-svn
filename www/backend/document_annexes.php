<?php
/**
 * Gestion des annexes d'un document
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_DOC_ANNEXES');

require_once('../../include/backend/init.inc.php');
require_once(PATH_INC_BACKEND_SERVICE.'Document.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'WorkflowManager.class.php');

$wk = new WorkflowManager($db, $_SESSION['utilisateur']['uti_id']);

if(!isset($_GET['id']))
{
  echo "<p>Aucun document sur lequel travailler</p>\n";
  exit;
}

if(!$wk->canDoAction($_GET['id'], OW_BACKEND_ACTION))
{
  echo "<p>Vous n'avez pas la permission de modifier les annexes</p>\n";
  exit;
}

/* TODO: vérifier que le chargement a réussi */
$doc = new Document($db, $_GET['id']);

$errors = array();
$listeannx = $doc->listeAnnexe();

if(isset($_POST['ficdel']))
  foreach($_POST['ficdel'] as $todel)
    if(in_array($todel, $listeannx))
      $doc->supprimerAnnexe($todel);
    else
      $errors[] = "Aucune annexe ne s'appelle $todel";

if(isset($_FILES['ficadd']))
  if(is_uploaded_file($_FILES['ficadd']['tmp_name']))
    $doc->ajoutAnnexe($_FILES['ficadd']['tmp_name'], $_FILES['ficadd']['name']);
  else
    $errors[] = $_FILES['ficadd']['name']." n'est pas un fichier uploadé";

if(count($errors) > 0)
{
  echo "<p>Des erreurs se sont produites :</p>\n<ul>\n";
  foreach($errors as $err)
    echo "<li>$err</li>\n";
  echo "</ul>\n";
  echo '<p>Retour aux <a href="document_details.php?id=', $_GET['id'], '">détails du document</a>.</p>';
}
else
{
  header('Location: document_details.php?id='.$_GET['id']);
  exit;
}
?>
