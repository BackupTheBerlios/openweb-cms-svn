<?php
/**
 * Crée une liste de documents en XHTML
 * @package Frontend
 * @subpackage Présentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

require_once('init.inc.php');

function OW_liste_document ($criteres, $max = 90, $show_extra_infos = true,
			    $show_categories = true)
{
  global $db;
  require_once (PATH_INC_FRONTEND.'FrontService.class.php');

  $infosPages = '';
  $fs = new FrontService ($db);

  $fs->nbLigneParPage = $max;

  $OW_liste_article =
    $fs->getListPage ($criteres, $infosPages, $show_categories);

  if (count ($OW_liste_article) > 0)
  {
    echo "<dl class=\"listedocs\">\n";
    foreach ($OW_liste_article as $art)
    {
      echo '  <dt><cite><a href="'.$art['repertoire'].'">'.$art['titre'].'</a></cite>';
      if ($show_extra_infos)
      {
        echo ' par '.$art['auteurs'].', le '.$art['date'];

        if ($show_categories && !empty($art['classement']))
        {
          echo ' pour ';
          $classmt = '';
	  foreach($art['classement'] as $cri)
            foreach($cri as $rep => $cls)
              $classmt .= ', <a href="'.$fs->getDocumentPath($rep).'">'.$cls.'</a>';
          echo substr($classmt, 1);
        }
      }
      echo "</dt>\n";
      echo '  <dd>'.$art['accroche']."</dd>\n"; 
    }
    echo "</dl>";
  }
}

?>
