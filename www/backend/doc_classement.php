<?php
/**
 * Classement des documents par critère
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_DOCCLASSE');

require_once('../../include/backend/init.inc.php');
require_once(PATH_INC_BACKEND_JPACK.'JHtml.lib.php');
require_once(PATH_INC_BACKEND_JPACK.'JUrl.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'ReferenceManager.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'UserManager.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'DocumentManager.class.php');

echo html_liste_actions();

$ref = new ReferenceManager($db);
$listeCrit = $ref->getCriterionList();
$listeCat = array();

foreach($listeCrit as $crit => $name)
{
  $listeCat[$name] = $ref->getEntriesList($crit);
  if(empty($listeCat[$name])) /* on enlève lorsqu'aucun classement n'existe */
    unset($listeCat[$name]);
}

$listeArticles = array();

if(isset($_GET['catid']))
{
  $catid = $_GET['catid'];
  $am = new DocumentManager($db);
  $am->nbParPage = $am->nbrDocs();
  
  if(isset($_POST['liste']))
    $am->setDocumentOrder($catid, array_flip($liste));
  
  $listeArticles = $am->getListBy1Critere($catid);
}
else
{
  $catid = null;
  $listeArticles = array();
}

?>
<script type="text/javascript" src="deversoir.js"></script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="frm">

<fieldset>
<legend>Sélection pour la liste</legend>
<label>Catégorie
  <select name="catid" id="catid" size="1">
<?php
foreach($listeCat as $cri => $classmt)
{
  echo '    <optgroup label="', $cri, '">', "\n";

  foreach($classmt as $id => $name)
    echo '      <option value="', $id, '"',$id === $catid ? ' selected="selected"' : '', '>', $name, "</option>\n";

  echo "    </optgroup>\n";
}
?>
  </select>
</label>
<input type="submit" value="Choisir" />
</fieldset>
</form>

<?php
if(count($listeArticles) > 0)
{
?>
<form action="<?php echo $_SERVER['PHP_SELF'], '?catid=', $catid; ?>" method="post" name="frmclass" onsubmit="AllSelect(this['liste[]']);">
<fieldset>
<legend>Liste des articles à classer</legend>

<div style="float: left;">
<select id="liste[]" name="liste[]" size="20" style="min-width:30em;" multiple="multiple">
<?php
foreach($listeArticles as $art)
  echo '  <option value="'.$art['id'].'">'.$art['titre'].'</option>';
?>
</select>
</div>

<div style="float: left; width: 40%">
<p>Cliquez sur un élément de la liste, et faites-le monter ou descendre avec les boutons ci-dessous.</p>
<p>
<input type="button" value="Monter" onclick="dvrUp(document.frmclass['liste[]'])" />
<input type="button" value="Descendre" onclick="dvrDwn(document.frmclass['liste[]'])" />
</p>
<p>
<input type="submit" name="act" id="act" value="Valider le classement" />
</p>
</div>

</fieldset>
</form>
<?php
}
?>

