<?php
/**
 * Liste des articles
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @todo permettre des sélections multiples (oui ça va être compliqué mais il faudra y passer)
 * @todo la requête par défaut ne doit porter que sur les articles non en-ligne (lié au précédent todo)
 */

define('OW_BACKEND_ACTION', 'ACT_DOCUMENTS');

require_once('../../include/backend/init.inc.php');
require_once(PATH_INC_BACKEND_JPACK.'JHtml.lib.php');
require_once(PATH_INC_BACKEND_JPACK.'JUrl.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'ReferenceManager.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'UserManager.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'DocumentManager.class.php');

echo html_liste_actions();

$ref = new ReferenceManager($db);
$um = new UserManager($db);

$statusLib = $ref->getStatusList();
$critereLib = $ref->getCriterionList();

// On récupère d'éventuels critères à la page
$contraintes = array();
$contraintes['status'] = isset($_GET['status']) ? $_GET['status'] : 'none';
$contraintes['type'] = isset($_GET['type']) ? $_GET['type'] : 'none';
$contraintes['uti'] = isset($_GET['uti']) ? $_GET['uti'] : $_SESSION['utilisateur']['uti_id'];

foreach($critereLib as $nom => $critere)
  $contraintes["cri_$nom"] = isset($_GET["cri_$nom"]) ? $_GET["cri_$nom"]
                               : 'none';
?>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">

<fieldset>
<legend>Critères de tris de la liste</legend>

<label>Type : <?php htmlSelect('type', $ref->getTypeList(), $contraintes['type'], '', false, 'none', '-- tous --'); ?></label>
<label>Utilisateur : <?php htmlSelectDB('uti', $um->getUserList(), $contraintes['uti'], 'uti_id', array('uti_prenom', 'uti_nom'), 1, false, 0, '-- tous --'); ?></label>
<label>État : <?php htmlSelect('status', $statusLib, $contraintes['status'], 1, false, 'none', '-- tous --'); ?></label>
<?php
  foreach($critereLib as $nom => $critere)
  {
    echo '<label>', $critere, " : ";
    htmlSelect("cri_$nom", $ref->getEntriesList($nom), $contraintes["cri_$nom"], '', false, 'none', '-- tous --');
    echo "</label>\n";
  }
?>

<input type="submit" value="Filtrer" name="filtrer" id="filtrer" />
</fieldset>
</form>

<?php

/* On affiche la liste des articles */
$am = new DocumentManager($db);
$infosPages = array();
$listeArticles = $am->getListPage($contraintes, $infosPages);

if(count($listeArticles) == 0)
{
  echo '<p>Aucun document ne correspond à ces critères.</p>', "\n";
  exit;
}

// affichage des articles
echo '<table class="tableliste">', "\n";
echo '<caption>Liste des documents &#8212; page ', $infosPages['pagecour'],
     '/', $infosPages['totalpage'], '</caption>', "\n";
echo "<thead>\n  <tr><th>Numéro</th>\n  <th>Document</th>\n  <th>Auteur</th>\n  <th>Utilisateur</th>\n <th>État</th></tr>\n</thead>\n<tbody>\n";

$i = 0;
foreach($listeArticles as $art)
{
  echo '<tr><td>', ++$i, '</td><td><a href="document_details.php?id=',
       $art['id'], '">', $art['titre'], '</a></td>';
  echo '<td>', $art['doc_auteurs'], '</td>';
  echo '<td>', $art['prenom'], ' ', $art['nom'] ,'</td>';
  echo '<td>', $statusLib[intval($art['etat'])] ,'</td>';
  echo '</tr>';
}
echo '</tbody>';

if(count($infosPages['pages']) > 1)
{
  echo '<tfoot><tr><td colspan="4">';
  // barre de navigation entre les pages
  $url = new JUrl($_SERVER["PHP_SELF"], $contraintes, array_keys($contraintes));

  if($infosPages['pageprec'])
  {
    $url->set('pg', $infosPages['pageprec']);
    echo '<a href="', $url->getUrl(), '">Page précédente</a>';
  }

  if($infosPages['fenprec'])
  {
    $url->set('pg', $infosPages['fenprec']);
    echo ' <a href="', $url->getUrl(), '">...</a>';
  }

  foreach($infosPages['pages'] as $page)
  {
    $url->set('pg',$page);
    if($page == $infosPages['pagecour'])
      echo ' <a href="', $url->getUrl(), '"><em>', $page, '</em></a>';
    else
      echo ' <a href="', $url->getUrl(), '">', $page, '</a>';
  }

  if($infosPages['fensuiv'])
  {
    $url->set('pg', $infosPages['fensuiv']);
    echo ' <a href="', $url->getUrl(), '">...</a>';
  }

  if($infosPages['pagesuiv'])
  {
    $url->set('pg',$infosPages['pagesuiv']);
    echo ' <a href="', $url->getUrl(), '">Page suivante</a>';
  }
  echo "</td>\n</tr>\n</tfoot>\n";
}
echo "</table>\n";

?>
