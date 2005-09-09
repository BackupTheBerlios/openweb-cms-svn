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

@define('PATH_SITE_ROOT',dirname(realpath(__FILE__.'/../../')).'/');
@define('PATH_INCLUDE',PATH_SITE_ROOT .'include/');
@define('PATH_INC_FRONTEND', dirname(__FILE__).'/');
@define('PATH_INC_BASECLASS', PATH_INCLUDE.'baseclass/');

setlocale(LC_TIME, 'fr_FR');
setlocale(LC_MESSAGES, 'fr_FR');


function fctErrorHandler($errno, $errmsg, $filename, $linenum, $errcontext){

    $codeString = array(
        E_ERROR         => 'ERROR',
        E_WARNING       => 'WARNING',
        E_NOTICE        => 'NOTICE',
        E_USER_ERROR    => 'USER_ERROR',
        E_USER_WARNING  => 'USER_WARNING',
        E_USER_NOTICE   => 'USER_NOTICE',
        E_STRICT        => 'STRICT'
        );

    if (error_reporting() == 0)
        return;

    $msg = date("Y-m-d H:i:s") .' ['.$codeString[$errno].'] file:'.$filename.' line:'
         .$linenum."\n\t".$errmsg."\n";

    error_log($msg,3, PATH_SITE_ROOT.'temp/error.log');


    if($errno == E_ERROR || $errno == E_USER_ERROR ){
        echo 'Erreur fatale sur la page.';
        exit;
    }
}

set_error_handler('fctErrorHandler');
// error_reporting(E_ALL); seulement en dev, et seulement si tu désactive le handler fctErrorHandler !
?>