<?php
//
//  OpenWebGroup CMS
//  Version 1.0 - 03/2003
//  Released under the GNU General Public License
//
/**
 * affiche une liste de documents
 * Utilisé par exemple pour les intros
 * @param   string  $titre  titre de la liste
 * @param   array   $criteres   liste de critere de selection des documents (voir FrontService)
 * @param   boolean $show_extra_infos   indique si il faut afficher des infos detailles sur chaque article
 * @param   boolean $show_categories    indique si il faut afficher toutes les categories de l'article
 * @param   boolean $usedivtexte      influence la présentation de la liste. A utiliser en fonction du type de page.
 * @return  array   la liste
 * @see FrontService::getListPage
 */
function OW_liste_document ($titre, $criteres, $repertoire,
			    $show_extra_infos = true,
			    $show_categories = true, $usedivtexte = true)
{
  require_once (PATH_INC_FRONTEND.'FrontService.class.php');

  $infosPages = '';
  $fs = new FrontService ($GLOBALS['db']);
  $fs->nbLigneParPage = 90;
  $OW_liste_article =
    $fs->getListPage ($criteres, $infosPages, $show_categories);

  if ($usedivtexte)
    echo '<!-- Début Texte -->
      <div id="texte">';

  if (count ($OW_liste_article) > 0)
  {
    echo "<h2>$titre</h2>\n";
    echo "<div class=\"listeintro\">\n";
    foreach ($OW_liste_article as $art)
    {
      $pro = '';
      foreach ($art['profil'] as $profil)
        $pro .= ', '.$profil;
      $pro = substr ($pro, 1);
      echo "  <h3>".$art['titre']."</h3>\n";
      if ($show_extra_infos)
      {
        echo '  <p>par ', $art['auteurs'], ', le ', $art['date'], '</p>';
        if ($show_categories)
        {
	  echo '  <ul>';
          foreach ($art['profil'] as $profil)
          {
            echo '    <li><a href="/', $profil['repertoire'],
              '/" title="Profil">', $profil['libelle'], '</a></li>';
          }
          foreach ($art['theme'] as $profil)
          {
            echo '    <li><a href="/', $profil['repertoire'],
              '/" title="Thème">', $profil['libelle'], '</a></li>';
          }
          foreach ($art['techno'] as $profil)
          {
            echo '    <li><a href="/', $profil['repertoire'],
              '/" title="Technologie">', $profil['libelle'], '</a></li>';
          }
	  echo '  </ul>';
        }
      }
      echo "<p>", $art['accroche'], ' <a href="', $repertoire,
        $art['repertoire'], "/\">Lire <cite>".$art['titre']."</cite></a>.</p></dd>\n";
    }
    echo "</div>";
  }

  if ($usedivtexte)
    echo ' </div>
      <!-- fin Texte -->';
}

?>
