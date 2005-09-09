<?php
/**
 * Suppression d'un utilisateur
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_ADMIN_DELETE');
require_once('../../include/backend/init.inc.php');
echo html_liste_actions();

require_once(PATH_INC_BACKEND_SERVICE.'UserManager.class.php');
$um =  new UserManager($db);

if(!(empty($_POST['uti_login']) || $_POST['uti_login'] == "none"))
{
  if($um->deleteUser($_POST['uti_login']))
    echo "<p>Utilisateur ", $_POST['uti_login'], " fusillé</p>";
  else
    echo "<p>Utilisateur ", $_POST['uti_login'], " gracié</p>";

  exit;
}

$users = $um->getUserList(true);
?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
<legend>Supprimer un utilisateur</legend>
<select name="uti_login" id="uti_login" size="">
<option value="none" selected="selected">--- utilisateur ---</option>
<?php
foreach($users as $user)
  echo '<option value="', $user['uti_login'], '">', $user['uti_login'], "</option>\n";
?>
</select>
<input type="submit" value="Supprimer" />
</fieldset>
</form>
