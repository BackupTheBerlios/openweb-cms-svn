<?php
/**
 * Index général du backend
 * @package Backend
 * @subpackage Presentation
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

define('OW_BACKEND_ACTION', 'ACT_ACCUEIL');
require_once('../../include/backend/init.inc.php');
require_once(PATH_INC_BACKEND_SERVICE.'DocumentManager.class.php');
require_once(PATH_INC_BACKEND_SERVICE.'ReferenceManager.class.php');

echo html_liste_actions();

$user = $_SESSION['utilisateur'];

echo "<p>Bienvenue ", $user['uti_prenom'], " ", $user['uti_nom'], "</p>\n";
?>

<h2>Tableau de bord</h2>

<?php
$am = new DocumentManager($db);
$ref = new ReferenceManager($db);
$statusLib = $ref->getStatusList();
$am->nbLigneParPage = -1;
$infosPages = array();
$listeArticles = $am->getListPage(array('uti' => $user['uti_id']), $infosPages);
if(count($listeArticles) > 0)
{
?>
<table class="tableliste">
<caption>Liste de vos documents</caption>
<thead>
  <tr><th>Document</th><th>État</th></tr>
</thead>
<tbody>
<?php
foreach($listeArticles as $art)
{
  echo '<tr><td><a href="document_details.php?id=',$art['id'], '">', $art['titre'], '</a></td><td>', $statusLib[intval($art['etat'])] ,'</td>';
}
?>
</tbody>
</table>
<?php
}
?>

<h2>Quelques liens</h2>
<ul>
<li><a href="http://stats.apinc.org/openweb.eu.org/">Les stats du site</a></li>
<li><a href="template/template_xhtml.html">Template XHTML</a> pour la rédaction de documents. (clic droit, save link target as..)</li>
</ul>
