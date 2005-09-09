<?php

require_once('../include/frontend/init.inc.php');
require_once(PATH_INC_FRONTEND.'FrontService.class.php');
require_once(PATH_INC_FRONTEND.'html_listedoc.inc.php');
require_once(PATH_INC_FRONTEND.'html_agreg_rss.inc.php');

$fs = new FrontService($db);

$rub = array();
$fs->nbLineParPage = 1;
$OW_presentation = $fs->getListPage(array('type' => 'openwebgroup', 'repertoire' => 'openwebgroup'), $rub);

ob_start();

?>
<!-- Début Texte -->
<div xmlns="http://www.w3.org/1999/xhtml" id="texteaccueil">

  <!-- Début Intro -->
  <div id="intro">

  <!-- Début Présentation -->
  <div id="presentation">
    <h2>Présentation</h2>
    <p><?php
if(count($OW_presentation) == 0)
{
  echo 'Pas de présentation pour le moment';
}
else
{
  $OW_presentation = $OW_presentation[0];
  echo $OW_presentation['accroche'];
  echo ' <a href="', $OW_presentation['repertoire'],'">Présentation complète</a>.';
}
?></p>
  </div>
  <!-- Fin Présentation -->

  <!-- Début Humeur -->
  <div id="humeur">
<?php
OW_liste_document(array('type' => 'H'), 1, 'Humeur&#8230;');
?>
    <p class="tous"><a href="/humeurs/">Toutes les humeurs</a></p>
  </div>
  <!-- Fin Humeur -->

  <!-- Début Blogs -->
  <div id="blogs">
    <h2>Blogs</h2>
<?php
OW_liste_rss();
?>
  </div>
  <!-- Fin Blogs -->




</div>
<!-- Fin Intro -->

<!-- Début Actualité -->
<div id="actualite">
  <h2>Actualité</h2>
<?php
require_once('dotclear/inc/prepend.php');
$con = new connection($dbuser, $dbpass, $dbhost, $dbbase);
$blog = new blog($con, DB_PREFIX, 1, dc_encoding);
$news = $blog->getLastNews(3, 'actualite');

if($news->isEmpty())
  echo "  <p>Aucune actualité</p>\n";
else
  while(!$news->EOF())
  {
    echo '  <h3>', $news->f('post_titre'), "</h3>\n";
    echo '  <h4>', strftime('%x', strtotime($news->f('post_dt'))), "</h4>\n";
    echo '  ', $news->f('post_content'), "\n\n";
    $news->moveNext();
  }

$con->close();
?>
  <p class="tous"><a href="/actualite/">Toutes les actualités</a></p>
</div>
<!-- Fin Actualité -->

<!-- Début Derniers Articles -->
<div id="articles">
<?php
OW_liste_document(array('type' => 'A'), 3, 'Derniers articles&#8230;');
?>
  <p class="tous"><a href="/articles/">Tous les articles</a></p>
</div>
<!-- Fin Derniers Articles -->

</div>
<?php
$buf = ob_get_contents();
$xh = xslt_create();
$args = array('/_xml' => '<'.'?xml version="1.0" encoding="utf-8"?'.'>'.$buf);
ob_end_clean();
$params = array('path_site_root' => PATH_SITE_ROOT);
$result = xslt_process($xh, 'arg:/_xml', PATH_INC_FRONTEND.'index.xsl',
    null, $args, $params);
eval('?>'.$result);
?>