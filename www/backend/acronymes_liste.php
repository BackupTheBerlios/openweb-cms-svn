<?php
/**
 * Liste des acronymes
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_ACRO_LISTE');
require_once('../../include/backend/init.inc.php');
echo html_liste_actions();

// TODO : ajuster selon la langue

$xh = xslt_create();
xslt_set_encoding($xh, "iso-8859-1");
$res = xslt_process($xh, PATH_INCLUDE."xslt/inc/acronyms.xml", "acronymes_liste.xsl");
xslt_free($xh);
print $res;


?>
