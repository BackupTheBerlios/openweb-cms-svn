<?php
/**
 * Ajout d'un acronyme
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_ACRO_ADD');
require_once('../../include/backend/init.inc.php');
echo html_liste_actions();

if(isset($_POST['set_acronyme']))
{
  /**
   * @todo manque le code pour enregistrer l'acronyme dans le XML... :-(
   */
}

?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
<legend>Nouvel acronyme</legend>
<p><label>Acronyme : <input type="text" id="acronyme" name="acronyme" /></label></p>
<p><label>Signification : <input type="text" id="signification" name="signification" /></label></p>
<p><label>Langue : <input type="text" id="langue" name="langue" value="fr" /></label></p>
<p><input type="submit" name="set_acronyme" id="set_acronyme" /></p>
</fieldset>
</form>
