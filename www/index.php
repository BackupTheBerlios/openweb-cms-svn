<?php

require_once('../include/frontend/init.inc.php');
require_once(PATH_INC_FRONTEND.'FrontService.class.php');
require_once(PATH_INC_FRONTEND.'html_listedoc.inc.php');

$fs = new FrontService($db);

$rub = array();
$fs->nbLineParPage = 1;
$OW_presentation = $fs->getListPage(array('type' => 'openwebgroup', 'repertoire' => 'openwebgroup'), $rub);

setlocale(LC_ALL, "fr_FR");

ob_start();

?>
<!-- D�but Texte -->
<div xmlns="http://www.w3.org/1999/xhtml" id="texteaccueil">

  <!-- D�but Intro -->
  <div id="intro">

  <!-- D�but Pr�sentation -->
  <div id="presentation">
    <h2>Pr�sentation</h2>
    <p><?php
if(count($OW_presentation) == 0)
{
  echo 'Pas de pr�sentation pour le moment';
}
else
{
  $OW_presentation = $OW_presentation[0];
  echo $OW_presentation['accroche'];
  echo ' <a href="', $OW_presentation['repertoire'],'">Pr�sentation compl�te</a>.';
}
?></p>
  </div>
  <!-- Fin Pr�sentation -->

  <!-- D�but Humeur -->
  <div id="humeur">
  <h2>Humeur&#8230;</h2>
<?php
OW_liste_document(array('type' => 'H'), 1);
?>
  </div>
  <!-- Fin Humeur -->

</div>
<!-- Fin Intro -->

<!-- D�but Actualit� -->
<div id="actualite">
  <h2>Actualit�</h2>
  <dl>
  </dl>
</div>
<!-- Fin Actualit� -->

<!-- D�but Derniers Articles -->
<div id="articles">
  <h2>Derniers articles&#8230;</h2>
<?php
OW_liste_document(array('type' => 'A'));
?>
  <p><a href="/articles/">Tous les articles</a></p>
</div>
<!-- Fin Derniers Articles -->

</div>
<?php
$buf = ob_get_contents();
$xh = xslt_create();
$args = array('/_xml' => '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'.$buf);
ob_end_clean();
$params = array('path_site_root' => PATH_SITE_ROOT);
$result = xslt_process($xh, 'arg:/_xml', PATH_INC_FRONTEND.'index.xsl',
    null, $args, $params);
header("Content-type: text/html; charset=utf-8");
eval('?>'.$result);
?>
