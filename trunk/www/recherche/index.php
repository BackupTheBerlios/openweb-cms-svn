<?php
require_once('../../include/frontend/init.inc.php');
require_once(PATH_INC_FRONTEND.'front_xhtml.inc.php');
require_once(PATH_INC_FRONTEND.'class.swish.php');

if(!empty($_GET['q']))
{
  $rechaine = utf8_decode($_GET['q']);
  $moteur = new swish(PATH_INC_FRONTEND.'swish.index',
    PATH_SITE_ROOT."bin/swish-e");
  $moteur->set_params($rechaine, array("DESC" => "swishdescription"));

  $res = $moteur->get_result();
  $numres = count($res);
  $rechaine = utf8_encode(htmlspecialchars($rechaine));
}
else
  $numres = -1;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Recherche</title>
</head>
<body>
<?php

if ($numres > 0)
{
  echo "<h2>", $numres, " résultat", $numres == 1 ? '' : 's',
       " pour <q>", $rechaine, "</q></h2>\n";
  echo "<dl class=\"listedocs\">\n";

  foreach ($res as $art)
  {
    echo '  <dt><cite><a href="'.utf8_encode($art['URL']).'">'.utf8_encode($art['TITRE']).'</a></cite></dt>', "\n";
    echo '  <dd>'.utf8_encode($art['DESC'])."</dd>\n";
  }
  echo "</dl>\n";
}
elseif($numres == 0)
{
  echo "<h2>Aucun résultat</h2>\n<p>Désolé, il n'y a aucun résultat dans OpenWeb pour <q>", $rechaine, "</q>.</p>\n";
}
else
{
?>
<form method="get" action="/recherche/">
  <div>
    <label for="q">Nouvelle recherche</label>
    <input name="q" id="q" accesskey="4" value="" class="champs" />
    <input type="submit" name="Submit" value="Ok" class="valid" />
  </div>
</form>
<?php
}
?>

<h2 id="conseils">Pour affiner votre recherche</h2>
<dl>
<dt>Pour chercher une expression exacte :</dt>
<dd><p>Utilisez des guillemets : <samp>"feuilles de style en cascade"</samp></p></dd>
<dt>Pour chercher l'un des mots d'une série :</dt>
<dd><p>Utilisez l'opérateur <em>or</em> : <samp>"accessibilité or image or attribut or alt"</samp></p></dd>
<dt>Pour exclure un mot d'une recherche :</dt>
<dd><p>Utilisez l'opérateur <em>not</em> : <samp>"navigateurs not explorer"</samp></p></dd>

</dl>
<p>La présence ou l'absence des majuscules ou des accents est indifférente.</p>

</body>
</html>
<?php
front_xhtml_output();
?>
