<?php
/**
 * Détails d'un document
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_DOC_DETAILS');

require_once('../../include/backend/init.inc.php');
require_once(PATH_INC_BACKEND_SERVICE.'Document.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'WorkflowManager.class.php');

$wk = new WorkflowManager($db, $_SESSION['utilisateur']['uti_id']);

/**
 * @todo permettre de choisir un document si aucun n'a été précisé
 */
if(!isset($_GET['id']))
{
  echo '<p>Aucun document à afficher</p>';
  exit;
}

/**
 * @todo vérifier que le chargement du document a réussi
 */
$doc = new Document($db, $_GET['id']);

$actionsAll = $wk->getListActions($doc->id, true);
$actionsAllow = $wk->getListActions($doc->id);
$actionsForbid = array_diff($actionsAll, $actionsAllow);

?>
<h2>Informations</h2>
<dl class="details">
  <dt>Type</dt><dd><?php echo $doc->type->libelle; ?></dd>
  <dt>Titre</dt><dd><?php echo $doc->infos->titre; ?></dd>
<?php if(!empty($doc->infos->accroche)) { ?>
  <dt>Accroche</dt><dd><?php echo $doc->infos->accroche; ?></dd>
<?php } ?>
  <dt>Auteurs</dt><dd><?php echo $doc->infos->auteurs; ?></dd>
  <dt>Date de publication</dt><dd><?php echo $doc->infos->pubdate; ?></dd>
  <dt>Date de modification</dt><dd><?php echo $doc->infos->update; ?></dd>
  <dt>Langue</dt><dd><?php echo $doc->infos->lang; ?></dd>
  <dt>Répertoire</dt><dd><?php echo $doc->infos->repertoire; ?></dd>
  <dt>État</dt><dd><?php $eta = $doc->ref->getStatusInfos($doc->etat); echo $eta["libelle"]; unset($eta); ?></dd>
<?php
foreach($doc->infos->classement as $crit => $classement)
  if(!empty($classement))
  {
    $critinfos = $doc->ref->getCriterionInfos($crit);
    echo "  <dt>", $critinfos['libelle'], "</dt><dd>";
    $res = '';
    foreach($classement as $entry)
      $res .= $entry.', ';
    echo trim($res, ', '), "</dd>\n";
    unset($res);
  }
?>
</dl>

<?php
if(array_key_exists("ACT_DOC_VIEW", $actionsAllow)):
?>
<h2>Formats disponibles</h2>
<ul>
<?php
  foreach($doc->getDocumentFormats() as $dsc => $format)
    echo '<li><a href="document_view.php/', $doc->infos->repertoire, '/',
         $format,'">', $dsc, '</a></li>', "\n";
?>
</ul>
<?php
if(($url = $doc->getDocumentUrl()) != '')
  echo '<p>Voir le document <a href="'.$url.'">sur le site</a></p>', "\n";
?>

<?php endif;
unset($actionsAllow["ACT_DOC_VIEW"], $actionsAll["ACT_DOC_VIEW"], $actionsForbid["ACT_DOC_VIEW"]);
?>

<h2>Fichiers annexes</h2>
<?php
$annexes = $doc->listeAnnexe();

$modifann = array_key_exists('ACT_DOC_ANNEXES', $actionsAllow);

unset($actionsAllow['ACT_DOC_ANNEXES'], $actionsAll['ACT_DOC_ANNEXES'], $actionsForbid['ACT_DOC_ANNEXES']);

if(count($annexes) > 0)
{
  if($modifann)
  {
?>
<form enctype="multipart/form-data" action="<?php $annx = $pm->getActionInfos('ACT_DOC_ANNEXES'); echo $annx['act_param'], '?id=', $doc->id; unset($annx); ?>" method="post">
<fieldset>
<legend>Supprimer des annexes</legend>
<?php
  }
  echo "<ul>\n";

  foreach($annexes as $annx)
    echo "  <li>", $modifann ?
      '<label><input type="checkbox" name="ficdel[]" value="'.$annx.'" />': "",
      '<a href="document_view.php/', $doc->infos->repertoire,
      '/annexes/', $annx, '">', $annx, '</a>', $modifann ? "</label>": "",
      "</li>\n";

  echo "</ul>\n";

  if($modifann)
  {
?>
  <p><input type="submit" name="act_ACT_DOC_ANNEXES" value="Supprimer" /></p>
</fieldset>
</form>
<?php
  }
}
?>

<?php
if($modifann)
{
?>
<form enctype="multipart/form-data" action="<?php $annx = $pm->getActionInfos('ACT_DOC_ANNEXES'); echo $annx['act_param'], '?id=', $doc->id; unset($annx); ?>" method="post">
  <fieldset>
    <legend>Ajouter/remplacer une annexe</legend>
    <label>Fichier : <input type="file" name="ficadd" id="ficadd" /></label><br />
    <input type="submit" name="act_ACT_DOC_ANNEXES" value="Enregistrer" />
  </fieldset>
</form>
<?php
}
?>

<h2>Actions</h2>
<?php
if(count($actionsAll) > 0)
{
?>
<form enctype="multipart/form-data" action="<?php echo 'document_action.php?id=', $doc->id; ?>" method="post">
  <fieldset>
<?php
  foreach($actionsAll as $act => $lib)
    echo '    <input type="submit" name="act_', $act,
         '" value="', $lib, '" ',
         array_key_exists($act, $actionsForbid) ? 'disabled="disabled" ' :
           '', '/>', "\n";
?>
  </fieldset>
</form>
<?php
}
else
 echo "<p>Aucune action possible.</p>\n";
?>
