<?php
/**
 * Style switcher pour le frontend
 * @package Frontend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

@define('OW_SWITCHER_ACTIVATE', true);

// liste des styles disponibles
$OW_styles = array('original' => 'Normal',
  'fondnoir' => 'Fond noir',
  'fondnoir_medium' => 'Fond noir/gros caract&egrave;res',
  'minimale' => 'Minimal',
  'sanshabillage' => 'Sans habillage');

$OW_style_default = 'original';

// on teste s'il y a un style de demandé..

if(OW_SWITCHER_ACTIVATE)
{
  if(isset($_COOKIE['sitestyle']))
  {
    // on n'est jamais trop prudent ;-)
    if(isset($OW_styles[$_COOKIE['sitestyle']]))
      $OW_style_default=$_COOKIE['sitestyle'];
  }

  if(isset($_GET['set']) && isset($OW_styles[$_GET['set']]))
  {
    setcookie ('sitestyle', $_GET['set'], time()+31536000, '/');
    $OW_style_default=$_GET['set'];
  }
}

// affiche un formulaire pour choisir la feuille de style
function show_switcher()
{
  global $OW_styles, $OW_style_default;
  $text = '';

  if(OW_SWITCHER_ACTIVATE)
  {
    $text .= "<form action=\"".$_SERVER["REQUEST_URI"]."\" method=\"get\" id=\"switcher\">\n";
    $text .= "  <div id=\"habillage\">\n";
    $text .= "    <label for=\"set\">Choisir un habillage&nbsp;:</label>\n";
    $text .= "    <select id=\"set\" name=\"set\">\n";
    foreach($OW_styles as $value => $lib)
      if ($value == $OW_style_default)
        $text .= '      <option value="'.$value.'" selected="selected">'.$lib.'</option>'."\n";
      else
        $text .= '      <option value="'.$value.'">'.$lib.'</option>'."\n";
      $text .= "    </select>\n";
      $text .= "    <input type=\"submit\" value=\"Ok\" />\n";
      $text .= "  </div>\n";
      $text .= "</form>\n";
  }
  return $text;
}

/**
 * affiche la liste des balises link à afficher
 */
function stylesheet_list()
{
  global $OW_styles, $OW_style_default;
  $text = '';

  $text .= '<link rel="stylesheet" type="text/css" href="/style/'.$OW_style_default.'/screen.css" media="screen" title="'.$OW_styles[$OW_style_default].'" />'."\n";
  $text .= '    <link rel="stylesheet" type="text/css" href="/style/'.$OW_style_default.'/print.css" media="print" title="'.$OW_styles[$OW_style_default].'" />'."\n";

  foreach($OW_styles as $value => $lib)
  {
    if($value != $OW_style_default)
    {
      $text .= '    <link rel="alternate stylesheet" type="text/css" href="/style/'.$value.'/screen.css" media="screen" title="'.$lib.'" />'."\n";
      $text .= '    <link rel="alternate stylesheet" type="text/css" href="/style/'.$value.'/print.css" media="print" title="'.$lib.'" />'."\n";
    }
  }
  return $text;
}
?>
