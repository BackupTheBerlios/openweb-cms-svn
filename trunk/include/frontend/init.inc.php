<?php
/**
 * Fichier d'initialisation frontend
 * @package Frontend
 * @subpackage Coordination
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

require_once("initmini.inc.php");
require_once(PATH_INCLUDE.'constantes.inc.php');

// Inclusion des bibliothèques de PEAR

$pear_path = realpath(PATH_INC_FRONTEND.'../../pear');
ini_set('include_path', ini_get('include_path').':'.$pear_path);
  // nécessaire pour PEAR sur le serveur d'OpenWeb.

require_once "DB.php";

// Paramètres pour la base de données
require_once(PATH_INCLUDE.'database.inc.php');
$DSN = "mysql://$dbuser:$dbpass@$dbhost/$dbbase";

// Connexion à la base de données
$db = DB::Connect($DSN);
$db->setFetchMode(DB_FETCHMODE_ASSOC);

?>
