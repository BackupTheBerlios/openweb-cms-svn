<?php
/**
 * Modifications des informations d'un utilisateur par l'administrateur
 *
 * Deux formulaires : le premier pour sélectionner l'utilisateur, le deuxième
 * pour modifier les informations de l'utilisateur sélectionné.
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_ADMIN_EDIT');
require_once('../../include/backend/init.inc.php');
echo html_liste_actions();

require_once(PATH_INC_BACKEND_SERVICE.'UserManager.class.php');
$um = new UserManager($db);

/* Modifier les infos si le submit « save_user » a été activé */
if(isset($_POST['save_user']))
{
  $newinfos = array();

  $newinfos['uti_login'] = $_POST['uti_login'];

  /* Si la case « uti_valide » n'est pas cochée, aucune valeur n'est envoyée
     par le navigateur pour le contrôle : on prend donc 0 par défaut */
  if(!isset($_POST['uti_valide']))
    $newinfos['uti_valide'] = 0;
  else
    $newinfos['uti_valide'] = 1;

  /* Le mot de passe n'est pas rempli par défaut : si le contrôle est vide,
     on ne change pas */
  if(!empty($_POST['uti_password']))
    $newinfos['uti_password'] = $_POST['uti_password'];

  foreach(array("uti_prenom", "uti_nom", "uti_type") as $controle)
    if(isset($_POST[$controle]))
      $newinfos[$controle] = stripslashes($_POST[$controle]);

  if($um->setUserDatas($newinfos))
    echo "<p>Informations enregistrées</p>\n";
  else
    echo "<p>N'a pas voulu des modifications</p>\n";
}

$users = $um->getUserList(true);

if(empty($_POST['uti_login']))
  $current_login = $users[0]['uti_login'];
    /* $users[0] doit bien exister puisque nous sommes loggés */
else
  $current_login = $_POST['uti_login'];
?>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
<legend>Utilisateur</legend>
<select name="uti_login" id="uti_login" size="">
<?php
/* Liste des utilisateurs, en sélectionnant l'utilisateur sur lequel
   on travaille */
foreach($users as $user)
  echo '<option value="', $user['uti_login'], '" ',
       $user['uti_login'] == $current_login ? 'selected="selected"': '',
       '">', $user['uti_login'], "</option>\n";
?>
</select>
<input type="submit" value="Sélectionner" id="select_user" name="select_user" />
</fieldset>
</form>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
<legend>Informations pour <?php echo $current_login; ?></legend>
<input name="uti_login" type="hidden" value="<?php echo $current_login; ?>" />
<?php
$user = $um->getUserDatas($current_login);
?>
<p><label>Prénom : <input type="text" id="uti_prenom" name="uti_prenom"
  value="<?php echo $user['uti_prenom'] ?>" /></label></p>
<p><label>Nom : <input type="text" id="uti_nom" name="uti_nom"
  value="<?php echo $user['uti_nom'] ?>" /></label></p>
<p><label>Mot de passe : <input type="password" id="uti_password"
  name="uti_password" /></label></p>
<p><label>Type : <input type="text" id="uti_type" name="uti_type"
  value="<?php echo $user['uti_type'] ?>" /></label></p>
<p><label>Valide : <input type="checkbox" id="uti_valide" name="uti_valide"
  <?php if($user['uti_valide']) echo 'checked="checked"' ?> value="1"/></label></p>

<p><input type="submit" value="Enregistrer" id="save_user" name="save_user" /></p>
</fieldset>
</form>
