<?php
/**
 * Ajout d'un utilisateur
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_ADMIN_ADD');
require_once('../../include/backend/init.inc.php');
echo html_liste_actions();

if(isset($_POST['uti_login']))
{
  require_once(PATH_INC_BACKEND_SERVICE.'UserManager.class.php');
  $um = new UserManager($db);
  if($um->addUser($_POST['uti_login']))
    echo "<p>Utilisateur ", $_POST['uti_login'], " ajouté</p>";
  else
    echo "<p>Mise en orbite de ", $_POST['uti_login'], " ratée</p>";
  exit;
}

?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
<legend>Ajout d'un utilisateur</legend>
<label>Login : <input type="text" id="uti_login" name="uti_login" /></label>
<input type="submit" value="Ajouter" />
</fieldset>
</form>
