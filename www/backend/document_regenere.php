<?php
/**
 * Regénération de tous les documents
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_DOCREGEN');
require_once("../../include/backend/init.inc.php");
require_once(PATH_INC_BACKEND_SERVICE.'DocumentManager.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'ReferenceManager.class.php');

// instanciation des services
$am = new DocumentManager($db);

$nbrtotal = $am->nbrDocs();

if(isset($_GET['act']) && $_GET['act'] == 'do')
{
  $page = isset($_GET['pg']) ? $_GET['pg'] : 0;

  if($page == 0)
  {
    $ref = new ReferenceManager($db);
    $ref->dumpClassements();
  }

  $nextpage = $am->toutRegenerer($page);
  if($nextpage > 0)
  {
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<fieldset>
<legend>Regénération</legend>
<p><?php echo $nextpage; ?> documents regénérés sur <?php echo $nbrtotal; ?>.</p>
<input type="hidden" name="pg" value="<?php echo $nextpage ?>" />
<input type="hidden" name="act" value="do" />
<input type="submit" value="Suite" />
</fieldset>
</form>
<?php
  }
  else
  {
    echo '<p>'.$nbrtotal.' documents regénérés.</p>';
    echo '<p>La regénération est terminée.</p>';
  }
}
else
{
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<fieldset>
<legend>Regénération</legend>
<p>Lancer la regénération de tous les documents ?</p>
<input type="hidden" name="pg" value="0" />
<input type="hidden" name="act" value="do" />
<input type="submit" value="Valider"  />
</fieldset>
</form>
<?php
}
?>
