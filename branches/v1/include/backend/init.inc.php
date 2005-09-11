<?php
/**
 * Fichier d'initialisation du backend
 *
 * Ce fichier, inclus dans toutes les pages du backend, se charge de toute
 * l'initialisation de l'environnement du backend :
 *  - définition des chemins d'accès aux différents fichiers ;
 *  - connexion à la base de données ;
 *  - identification et gestion des droits ;
 *  - gestion du gabarit de sortie XHTML.
 *
 * @package Backend
 * @subpackage Coordination
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('PATH_SITE_ROOT', dirname(realpath(__FILE__.'/../../')).'/');
define('PATH_INC_BACKEND', dirname(__FILE__).'/');
define('PATH_INCLUDE', realpath(PATH_INC_BACKEND.'../').'/');
define('PATH_INC_BASECLASS', PATH_INCLUDE.'baseclass/');
define('PATH_INC_FRONTEND', PATH_INCLUDE.'frontend/');
define('PATH_INC_BACKEND_SERVICE', PATH_INC_BACKEND.'services/');
define('PATH_INC_BACKEND_JPACK', realpath(PATH_INC_BACKEND.'../jpack/').'/');
define('PATH_WWW_BACKEND', PATH_SITE_ROOT.'/www/backend/');

include(PATH_INCLUDE.'constantes.inc.php');

error_reporting(E_ALL);

// Inclusion des bibliothèques de PEAR
$pear_path = realpath(PATH_INC_BACKEND.'../../pear');
ini_set('include_path', ini_get('include_path').':'.$pear_path);
  // nécessaire pour PEAR sur le serveur d'OpenWeb

require_once "Auth/Auth.php";
require_once "DB.php";

// Paramètres pour la base de données
require_once(PATH_INCLUDE.'database.inc.php');
$DSN = "mysql://$dbuser:$dbpass@$dbhost/$dbbase";

// Identification de l'utilisateur
$options = array('table' => 'uti_utilisateur',
                 'usernamecol' => 'uti_login',
                 'passwordcol' => 'uti_password',
                 'dsn' => $DSN);

require_once(PATH_INC_BACKEND.'logon.php');

$openwebAuth = new Auth("DB", $options, 'ow_html_login_box', true);
$openwebAuth->start();

if(isset($_GET['logon']) && $openwebAuth->getAuth())
{
  $openwebAuth->logout();
  unset($_SESSION['utilisateur']);
  $openwebAuth->start();
}

if(!$openwebAuth->getAuth())
{
  exit;
}

// Connexion à la base de données
$db = DB::Connect($DSN);
$db->setFetchMode(DB_FETCHMODE_ASSOC);

// Récuperation des données de l'utilisateur
if(!isset($_SESSION['utilisateur']))
{
  require_once(PATH_INC_BACKEND_SERVICE.'UserManager.class.php');
  $um = new UserManager($db);
  if(($user = $um->getUserDatas($openwebAuth->username)) === null)
  {
    session_destroy();
    echo 'Problème de lecture de vos données personnelles';
    exit;
  }

  // Vérifie si l'utilisateur est autorisé à se connecter
  if(intval($user['uti_valide']) == 0)
  {
    echo 'Votre compte est désactivé';
    exit;
  }

  $_SESSION['utilisateur'] = $user;
  unset($um);
}

require_once(PATH_INC_BACKEND_SERVICE.'PermsManager.class.php');

/**
 * Rôle de l'utilisateur dans le backend, définit les actions qu'il peut effectuer
 * @global object $pm
 */

$pm = new PermsManager($db, $_SESSION['utilisateur']['uti_type']);

/* On ne se pose la question des droits d'accès que si l'appelant l'a demandé,
   en définissant OW_BACKEND_ACTION */
if(defined('OW_BACKEND_ACTION'))
{
  if(!$pm->canDoAction(OW_BACKEND_ACTION))
  {
    echo 'Action interdite';
    exit;
  }
}

if(!defined('OW_BACKEND_NOHTML'))
  include(PATH_INC_BACKEND.'html_output.inc.php');

?>
