<?php
/**
 * Modifications de la liste des acronymes
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_ACRO_EDIT');

require_once('../../include/backend/init.inc.php');
require_once(PATH_INC_BACKEND_SERVICE.'Acronyms.lib.php');

function sanitizeInput($in)
{
  return htmlspecialchars(stripslashes($in), ENT_NOQUOTES);
}

$acrolist = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])
  .'/acronymes_liste.php';

if(isset($_REQUEST['set']) && !empty($_REQUEST['acronym']))
{
  setAcronym(empty($_REQUEST['acronym_id']) ? null : $_REQUEST['acronym_id'],
    array('acronym' => sanitizeInput($_REQUEST['acronym']),
      'lang' => sanitizeInput($_REQUEST['lang']),
      'content' => sanitizeInput($_REQUEST['content'])));

  header('Location: '.$acrolist);
}

if(!empty($_REQUEST['acronym_id']))
{
  if(isset($_REQUEST['del']))
  {
    setAcronym($_REQUEST['acronym_id'], array());

    header('Location: '.$acrolist);
  }

  $acronym_id = empty($_REQUEST['acronym']) ? $_REQUEST['acronym_id'] :
      $_REQUEST['acronym'];

  $acroinfos = getAcronym($acronym_id);
}

echo html_liste_actions();

?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
<legend><?php echo empty($acronym_id) ? 'Nouvel acronyme' :
  "Modification de l'acronyme <acronym>$acronym_id</acronym>"; ?></legend>
<p><label>Acronyme : <input type="text" id="acronym" name="acronym"<?php
  if(!empty($acroinfos['acronym']))
    echo ' value="', $acroinfos['acronym'], '"';
?> /></label></p>

<p><label>Signification : <input type="text" id="content" name="content"<?php
  if(!empty($acroinfos['content']))
    echo ' value="', sanitizeInput($acroinfos['content']), '"';
?>/></label></p>

<p><label>Langue : <input type="text" id="lang" name="lang"<?php
  if(!empty($acroinfos['lang']))
    echo ' value="', $acroinfos['lang'], '"';
?>/></label></p>

<p><?php
  if(!empty($acronym_id))
    echo '<input type="hidden" name="acronym_id" id="acronym_id" value="',
      $acronym_id, '" />';
?>
<input type="submit" name="set" id="set" value="Enregistrer" />
<input type="submit" name="del" id="del" value="Supprimer" /></p>
</fieldset>
</form>
