<?php
/**
 * Boîte d'identification du backend
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

/**
 * Affiche une boîte de login.
 * @see init.inc.php
 */
function ow_html_login_box()
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="author" content="Laurent Jouanneau" />
    <title>OpenWeb - Backend</title>
    <link rel="stylesheet" href="backend.css" media="all" type="text/css" />
</head>
<body>

<h1>OpenWeb Backend</h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<fieldset><legend>Identification</legend>
<label>Login : <input type="text" id="username" name="username" /></label>
<label>Mot de passe : <input type="password" id="password" name="password" /></label>
<input type="submit" value="Valider" />
</fieldset>
</form>

</body>
</html>
<?php
}
?>
