<?php
/**
 * Interface avec le moteur de recherche Swish-e
 * @package Backend
 * @subpackage Services
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

function rechercheRegenIndex()
{
  $swish = PATH_SITE_ROOT.'bin/swish-e';
  $tempindex = tempnam(PATH_INC_FRONTEND, 'swish.tmp');
  $cmd = $swish.' -v 1'.
    ' -f '.$tempindex.
    ' -c '.PATH_INC_BACKEND.'swish.conf';
  $cwd = getcwd();

  chdir(PATH_SITE_ROOT);

  $res = array();
  $ret = -1;
  exec($cmd, $res, $ret);

  chdir($cwd);

  if($ret == 0)
  {
    rename($tempindex, PATH_INC_FRONTEND.'swish.index');
    rename($tempindex.'.prop', PATH_INC_FRONTEND.'swish.index.prop');
    return true;
  }
  else
  {
    /* TODO: renvoyer une erreur avec un mécanisme propre de gestion d'erreur
    via les messages donnés par $res */
    unlink($tempindex);
    unlink($tempindex.'.prop');
    return false;
  }
}

?>
