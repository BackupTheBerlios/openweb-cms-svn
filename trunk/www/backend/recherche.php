<?php
/**
 * Moteur de recherche
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_RECHERCHE');
require_once('../../include/backend/init.inc.php');
require_once(PATH_INC_BACKEND_SERVICE.'Recherche.lib.php');
echo html_liste_actions();

$res = rechercheRegenIndex();

if($res)
{
  echo "<p>L'index a été mis à jour</p>";
}
else
{
  echo "<p>Pépin lors de la mise à jour de l'index</p>";
}

?>
