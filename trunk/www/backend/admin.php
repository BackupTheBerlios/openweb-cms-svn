<?php
/**
 * Page d'accueil administration
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_ADMIN');
require_once('../../include/backend/init.inc.php');
echo html_liste_actions();

?>
