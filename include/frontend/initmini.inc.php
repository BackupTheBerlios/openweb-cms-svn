<?php
/**
 * Fichier d'initialisation minimal Frontend
 * @package Frontend
 * @subpackage Coordination
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

header("Content-type: text/html; charset=utf-8");
define('PATH_SITE_ROOT',dirname(realpath(__FILE__.'/../../')).'/');
define('PATH_INCLUDE',PATH_SITE_ROOT .'include/');
define('PATH_INC_FRONTEND', dirname(__FILE__).'/');
define('PATH_INC_BASECLASS', PATH_INCLUDE.'baseclass/');
error_reporting(E_ALL);

setlocale(LC_TIME, 'fr_FR');
setlocale(LC_MESSAGES, 'fr_FR');

?>
