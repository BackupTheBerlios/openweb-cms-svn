<?php
/**
 * Gestion de la sortie XHTML de la partie backend du site
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

ob_start('__ob_callback');
echo html_header();

function __ob_callback($buffer)
{
  return $buffer.html_footer();
}

function html_header()
{
  global $pm;
  $header = <<<FIN
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Laurent Jouanneau, Florian Hatat" />
FIN;
  $res = $pm->getActionInfos('ACT_ROOT');
  $rep = $pm->getActionInfos(OW_BACKEND_ACTION);
  $header .= "    <title>".$res['act_libelle']." - ".$rep['act_libelle']."</title>\n";
  $header .= <<<FIN
    <link rel="stylesheet" href="backend.css"  media="all" type="text/css" title="Theme classique" />
</head>
<body>
<h1>OpenWeb Backend</h1>
<ul id="menu">
FIN;
  $res = $pm->getActions('ACT_ROOT');
  $rep = $pm->getActionAncestors(OW_BACKEND_ACTION);

  foreach($res as $cur)
    $header .= '<li'.($cur['act_id'] == $rep[1]['act_id'] ? ' class="on"' : '').'><a href="'.$cur['act_param'].'">'.$cur['act_libelle']."</a></li>\n";

  $header .= <<<FIN
</ul>
<div id="contenu">
FIN;

  return $header;
}

function html_footer()
{
  $res = "</div>\n<div class=\"aide\"><p>Aide</p><div>";
  $aide = PATH_WWW_BACKEND.'aide/'.OW_BACKEND_ACTION;
  if(is_readable($aide))
    $res .= file_get_contents($aide);
  else
    $res .= "<p>Aucune aide pour <code>".OW_BACKEND_ACTION."</code>.</p>";
  return $res."</div>\n</div>\n</body>\n</html>\n";
}

function html_liste_actions()
{
  global $pm;
  $code = '';
  $res = $pm->getActions(OW_BACKEND_ACTION);
  if(count($res) > 0)
  {
    $code .= "<ul>\n";
    foreach($res as $cur)
      $code .= '<li><a href="'.$cur['act_param'].'">'.$cur['act_libelle']."</a></li>\n";
    $code .= "</ul>\n";
  }
  return $code;
}
?>
