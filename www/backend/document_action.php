<?php
/**
 * Exécution des actions sur un document
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

$action = array_filter(array_keys($_POST),
            create_function('$var', 'return strpos($var, "act_") === 0;'));
if(count($action) != 1)
{
  echo 'J\'exécute *exactement* une action, pas ', count($action);
  exit;
}

$action = substr(array_pop($action), 4);

define('OW_BACKEND_ACTION', $action);

require_once("../../include/backend/init.inc.php");
require_once(PATH_INC_BACKEND_SERVICE.'Document.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'WorkflowManager.class.php');

$wk = new WorkflowManager($db, $_SESSION['utilisateur']['uti_id']);

if(!isset($_GET['id']))
{
  echo '<p>Aucun document à traiter</p>';
  exit;
}

/**
 * @todo vérifier que le chargement du document a réussi
 */
$doc = new Document($db, $_GET['id']);

$act = $wk->getActionInfos($action, $doc->id);

if(!$wk->canDoAction($doc->id, $act['act_name']))
{
  echo '<p>Vous ne pouvez pas exécuter cette action</p>';
  exit;
}

// Réalisation de l'action
switch($act['act_name'])
{
  case 'ACT_DOC_MAJ':
    $redirection = true;
    $url = 'document_upload.php?id='.$doc->id;
    break;

  case 'ACT_DOC_DEL':
    if(isset($_POST['confirm']))
    {
      $doc->changeEtat($act['doc_etat_out']);
      $url = 'documents.php';
      $redirection = true;
    }
    else
    {
      $redirection = false;
      $titre = 'Suppression d\'un document';
      $texte = 'Confirmez-vous la suppression du document <em>'.$doc->infos->titre.'</em> ?';
    }
    break;

  case 'ACT_DOC_OFFLINE':
  case 'ACT_DOC_ONLINE':
  case 'ACT_DOC_TEMP':
    $redirection = true;
    $url = 'document_details?id='.$doc->id;
    $doc->changeEtat($act['doc_etat_out']);
    break;

  default:
    echo '<p>Action inconnue</p>';
    exit;
    break;
}

if($redirection)
{
  header('Location: '.$url);
  exit;
}

echo '<h2>', $titre, '</h2>';
?>
<form action="<?php echo $_SERVER['PHP_SELF'], '?id=', $doc->id; ?>" method="post">
<fieldset>
<legend><?php echo $texte; ?></legend>
<input type="hidden" name="confirm" value="1" />
<input type="submit" name="act_<?php echo $act['act_name']; ?>" value="Continuer" />
<input type="button" value="Annuler" onclick="location.href='document_details?id=<?php echo $doc->id; ?>'" />
</fieldset>
</form>
