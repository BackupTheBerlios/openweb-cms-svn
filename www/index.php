<?php

require_once('../include/frontend/init.inc.php');
require_once(PATH_INC_FRONTEND.'FrontService.class.php');

$fs = new FrontService($db);

$rub = array();
$fs->nbLineParPage = 3;
$OW_liste_article = $fs->getListPage(array('type' => 'A'), $rub);

$rub = array();
$fs->nbLineParPage = 1;
$OW_presentation = $fs->getListPage(array('type' => 'openwebgroup', 'repertoire' => 'openwebgroup'), $rub);
$rub = array();
$OW_humeur = $fs->getListPage(array('type' => 'humeur'), $rub);

$db->disconnect();
$db = null;

//---- special B2, du code qui suxe >:-(
chdir(dirname(__FILE__).'/actualite/');
ini_set('include_path',
        ini_get('include_path').':'.dirname(__FILE__).'/actualite/');
$blog = 1;
$posts = 3; // nombre maximum de posts sur la page
include ("actualite/blog.header.php");
setlocale(LC_ALL, "fr_FR");

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
  echo ' <a href="/openwebgroup/', $OW_presentation['repertoire'],'/">Présentation complète</a>.';
}
?></p>
  </div>
  <!-- Fin Présentation -->

  <!-- Début Humeur -->
  <div id="humeur">
  <h2>Humeur&#8230;</h2>
<?php
if(count($OW_humeur) == 0)
  echo 'Pas d\'humeur';
else
{
  $OW_humeur = $OW_humeur[0];
  echo '  <dl class="listedocs">
    <dt><cite><a href="/humeurs/',
    $OW_humeur['repertoire'], '/">', $OW_humeur['titre'], '</a></cite></dt>
    <dd>
      <dl>
        <dt>par</dt><dd>', $OW_humeur['auteurs'], '</dd>
        <dt>le</dt><dd>', $OW_humeur['date'], '</dd>
      </dl>
      <p class="accroche">', $OW_humeur['accroche'],'</p>
    </dd>
  </dl>
  <p><a href="/humeurs/">Toutes les humeurs</a></p>', "\n";
}
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
while($row = mysql_fetch_object($result))
{
  start_b2();
?>
    <dt><?php the_title(); ?></dt>
    <dd>
      <dl>
        <dt>le</dt>
        <dd><?php the_date("l j F Y","",""); ?></dd>
      </dl>
      <p><?php the_content(); ?></p>
    </dd>
<?php
}
?>
  </dl>
</div>
<!-- Fin Actualité -->

<!-- Début Derniers Articles -->
<div id="articles">
  <h2>Derniers articles&#8230;</h2>
  <dl class="listedocs">
<?php
/**
 * @todo le lien est beurk car pas dit qu'on commence par "articles/"
 */
foreach($OW_liste_article as $art)
{
  echo '    <dt><cite><a href="/articles/', $art['repertoire'], '/">',
       $art['titre'], "</a></cite></dt>\n";
  echo "    <dd>\n      <dl>\n";
  echo "        <dt>par</dt><dd>", $art['auteurs'], "</dd>\n";
  echo "        <dt>le</dt><dd>",  $art['date'], "</dd>\n";

  if(count($art['classement']) > 0)
  {
    $pro = '';
    foreach($art['classement'] as $crit)
      foreach($crit['entries'] as $clas)
      $pro .= ', '.$clas;
    $pro = substr($pro, 2);
    
    echo "        <dt>pour</dt><dd>$pro</dd>\n";
  }
  echo '      </dl>
      <p>', $art['accroche'], '</p>
    </dd>
';
}
?>
  </dl>
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
