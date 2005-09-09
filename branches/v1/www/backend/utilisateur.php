<?php
/**
 * Modification des informations de l'utilisateur
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_UTI');
require_once('../../include/backend/init.inc.php');
echo html_liste_actions();

require_once(PATH_INC_BACKEND_SERVICE.'UserManager.class.php');
$um = new UserManager($db);

if(isset($_POST['setinfos']))
{
  $infos = array();
  $infos['uti_login'] = $_SESSION['utilisateur']['uti_login'];
  if(isset($_POST['uti_nom']))
    $infos['uti_nom'] = $_POST['uti_nom'];
  if(isset($_POST['uti_prenom']))
    $infos['uti_prenom'] = $_POST['uti_prenom'];
  if(isset($_POST['uti_lang']))
    $infos['uti_lang'] = $_POST['uti_lang'];
  if(isset($_POST['uti_charset']))
    $infos['uti_charset'] = $_POST['uti_charset'];
  if(isset($_POST['uti_rss']))
    $infos['uti_rss'] = trim($_POST['uti_rss']);

  if($_POST['mdp_new'] !=''){
    if($_POST['mdp_new'] == $_POST['mdp_cfm']){
      $infos['uti_password'] = $_POST['mdp_new'];
      echo '<p>Nouveau mot de passe enregistré</p>';
    }else{
      echo "<p>Mots de passe différents</p>\n";
    }
  }
  $um->setUserDatas($infos);
  $_SESSION['utilisateur'] = $um->getUserDatas($infos['uti_login']);
}

$user = $_SESSION['utilisateur'];

?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
<legend>Informations pour <?php echo $user['uti_login'] ?></legend>
<p><label>Prénom : <input type="text" id="uti_prenom" name="uti_prenom" value="<?php echo $user['uti_prenom'] ?>" /></label></p>
<p><label>Nom : <input type="text" id="uti_nom" name="uti_nom" value="<?php echo $user['uti_nom'] ?>" /></label></p>
<p><label>Langue : <input type="text" id="uti_lang" name="uti_lang" value="<?php echo $user['uti_lang'] ?>" /></label></p>
<p><label>Jeu de caractère : <input type="text" id="uti_charset" name="uti_charset" value="<?php echo $user['uti_charset'] ?>" /></label></p>
</fieldset>
<fieldset>
<legend>L'inscription de votre blog sur l'aggrégateur d'openweb</legend>
<p>Si vous voulez que des billets de votre blog apparaissent sur la page d'accueil d'openweb,
indiquez ici l'url du fils rss. <strong>Attention !</strong> Le fils RSS que vous indiquez
ne doit contenir que des billets sur les standards. Ce fils doit donc correspondre par exemple
aux billets d'une catégorie "openweb" sur votre blog. Pour un blog sous Dotclear, ça sera par exemple :
http://votresite.com/dotclear/rss.php?cat=Standards .</p>
<p>Si vous ne voulez pas que vos billets apparaissent, laissez le champs vide.</p>
<p><label>URL du fils RSS : <input type="text" size="40" id="uti_rss" name="uti_rss" value="<?php echo $user['uti_rss'] ?>" /></label></p>
</fieldset>

<fieldset>
<legend>Mot de passe</legend>
<p><label>Nouveau mot de passe : <input type="text" id="mdp_new" name="mdp_new" /></label></p>
<p><label>Confirmation du mot de passe : <input type="text" id="mdp_cfm" name="mdp_cfm" /></label></p>
</fieldset>
<p><input type="hidden" id="setinfos" name="setinfos" /><input type="submit" value="Sauvegarder" />
</form>
