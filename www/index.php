<?php

require_once('../include/frontend/init.inc.php');
require_once(PATH_INC_FRONTEND.'FrontService.class.php');
require_once(PATH_INC_FRONTEND.'html_listedoc.inc.php');

$fs = new FrontService($db);

$rub = array();
$fs->nbLineParPage = 1;
$OW_presentation = $fs->getListPage(array('type' => 'openwebgroup', 'repertoire' => 'openwebgroup'), $rub);

setlocale(LC_TIME, 'fr_FR');
setlocale(LC_MESSAGES, 'fr_FR');

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
  </div>
  <!-- Fin Humeur -->

</div>
<!-- Fin Intro -->

<!-- Début Actualité -->
<div id="actualite">
  <h2>Actualité</h2>
  <dl>
<?php
require_once('actualite/inc/prepend.php');
$con = new connection($dbuser, $dbpass, $dbhost, $dbbase);
$blog = new blog($con, DB_PREFIX, NULL, dc_encoding);
$news = $blog->getLastNews(3);
while(!$news->EOF())
{
  echo '  <dt>', $news->f('post_titre'), ' par ', $news->getUserCN(),
       ', le ', strftime('%x', strtotime($news->f('post_dt'))), "</dt>\n";
  echo '  <dd>', $news->f('post_content'), "</dd>";
  $news->moveNext();
}
$con->close();
?>
  </dl>
</div>
<!-- Fin Actualité -->

<!-- Début Derniers Articles -->
<div id="articles">
<?php
OW_liste_document(array('type' => 'A'), 3, 'Derniers articles&#8230;');
?>
  <p><a href="/articles/">Tous les articles</a></p>
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
