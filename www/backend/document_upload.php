<?php
/**
 * Ajout/mise � jour d'un article
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright � 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_DOCAJOUT');

require_once('../../include/backend/init.inc.php');
require_once(PATH_INC_BACKEND_SERVICE."DocbookParse.lib.php");
require_once(PATH_INC_BACKEND_SERVICE.'WorkflowManager.class.php');
require_once(PATH_INC_BACKEND_SERVICE."Document.class.php");

echo html_liste_actions();

if(empty($_POST['action']))
{
?>

<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
<ul>
  <li><label><input type="radio" name="format" checked="checked" value="docbook" /> format Docbook </label></li>
  <li><label><input type="radio" name="format" value="xhtml" /> format Xhtml</label></li>
</ul>
<p><label>Document�: <input type="file" name="xmlart" /></label><br />
<input type="submit" name="action" value="Envoyer" /></p>
</fieldset>
</form>
<?php
  return true;
}

$errors = array();

$xmlart = $_FILES['xmlart']['tmp_name'];
if(!is_uploaded_file($xmlart))
{
  echo "<p>Enduit grossier d'horreur, m�chant yuser</p>";
  return false;
}

/* TODO: transformer si l'on n'a pas de Docbook */

$docbookfile = tempnam(PATH_SITE_ROOT."temp/tmp/", "dbk");
move_uploaded_file($xmlart, $docbookfile);

/* TODO: si pas d'erreur l'appel suivant est au bout du
   compte r�alis� deux fois : une id�e pour ne pas g�cher autant
   de ressources ? */
$infos = docbookGetArticleInfoFromFile($docbookfile);
if(is_array($infos)) /* erreur ! */
  $errors = array_merge($errors, $infos);
else
{
  $doc = new Document($db, $infos->repertoire);

  if(!is_numeric($doc->id))
  {
    $maj = false; /* Le document n'existe pas encore */
    $doc->errors = array();
  }
  else
  {
    $maj = true;
    $wk = new WorkflowManager($db, $_SESSION['utilisateur']['uti_id']);
    /* On v�rifie que l'on a le droit de mettre � jour */
    if(!$wk->canDoAction($doc->id, 'ACT_DOC_MAJ'))
      $errors[] = 'mise � jour du document interdite';
  }

  $errors = array_merge($errors, $doc->errors);

  if(count($errors) == 0)
    $doc->setContentFromDocbook($docbookfile);

  $errors = array_merge($errors, $doc->errors);
}

?>
<h2><?php echo $maj ? "Mise � jour" : "Ajout"; ?> d'un document�: r�sultat</h2>
<?php

if(count($errors) == 0)
{
  echo "<p>Aucune erreur�! F�licitations</p>\n";
  echo '<p>Voir les <a href="document_details.php?id=', $doc->id, '">d�tails</a> du document</p>', "\n";
}
else
{
  echo "<p>Des erreurs sont survenues durant le traitement du document�:</p>\n<ul>\n";
  foreach($errors as $err)
    echo "<li><strong>", $err, "</strong></li>\n";
  echo "</ul>\n";
}
?>
