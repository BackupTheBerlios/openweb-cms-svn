<?php
/**
 * Ajout/mise à jour d'un article
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_DOCAJOUT');

require_once('../../include/backend/init.inc.php');
require_once(PATH_INC_BACKEND_SERVICE."DocbookParse.lib.php");
require_once(PATH_INC_BACKEND_SERVICE.'WorkflowManager.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'Document.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'OutputFactory.lib.php');

echo html_liste_actions();

if(empty($_POST['action']))
{
?>

<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
<ul>
  <li><label><input type="radio" name="format" checked="checked" value="docbook" /> format Docbook </label></li>
  <li><label><input type="radio" name="format" value="xhtml" /> format Xhtml</label></li>
  <li><label><input type="radio" name="format" value="phpwiki" /> format XHTML de PhpWiki</label></li>
</ul>
<p><label>Document : <input type="file" name="xmlart" /></label><br />
<input type="submit" name="action" value="Envoyer" /></p>
</fieldset>
</form>
<?php
  return true;
}

$errors = array();
$maj = false;

$xmlart = $_FILES['xmlart']['tmp_name'];
if(!is_uploaded_file($xmlart))
{
  echo "<p>Enduit grossier d'horreur, méchant yuser</p>";
  return false;
}

if(empty($outputInfos[$_POST['format']]['docbook']) && $_POST['format'] != 'docbook')
  $errors[] = 'impossible de transformer '.$_POST['format'].' en Docbook';
else
{
  $docbookfile = tempnam(PATH_SITE_ROOT."temp/tmp/", "dbk");
  if($_POST['format'] == 'docbook')
  {
    move_uploaded_file($xmlart, $docbookfile);
  }
  else
  {
    $origfile = tempnam(PATH_SITE_ROOT."temp/tmp/", $_POST['format']);
    move_uploaded_file($xmlart, $origfile);
    if(!outputMake($origfile, $_POST['format'], 'docbook', $docbookfile))
      $errors[] = 'erreur lors de la transformation du format '.$_POST['format'];
  }

  /* TODO: si pas d'erreur l'appel suivant est au bout du
     compte réalisé deux fois : une idée pour ne pas gâcher autant
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
      /* On vérifie que l'on a le droit de mettre à jour */
      if(!$wk->canDoAction($doc->id, 'ACT_DOC_MAJ'))
        $errors[] = 'mise à jour du document interdite';
    }
    $errors = array_merge($errors, $doc->errors);

    if(count($errors) == 0)
      $doc->setContentFromDocbook($docbookfile);

    $errors = array_merge($errors, $doc->errors);
  }
}

?>
<h2><?php echo $maj ? "Mise à jour" : "Ajout"; ?> d'un document : résultat</h2>
<?php

if(count($errors) == 0)
{
  echo "<p>Aucune erreur ! Félicitations</p>\n";
  echo '<p>Voir les <a href="document_details.php?id=', $doc->id, '">détails</a> du document</p>', "\n";
}
else
{
  echo "<p>Des erreurs sont survenues durant le traitement du document :</p>\n<ul>\n";
  foreach($errors as $err)
    echo "<li><strong>", $err, "</strong></li>\n";
  echo "</ul>\n";
}
?>
