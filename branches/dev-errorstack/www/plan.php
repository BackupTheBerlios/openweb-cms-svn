<?php
require_once('../../../include/frontend/init.inc.php');
require_once(PATH_INC_FRONTEND.'plan.inc.php');
require_once(PATH_INC_FRONTEND.'front_xhtml.inc.php');

function htmlDeListe($liste)
{
  if(empty($liste))
    return;
  echo "<ul>\n";
  foreach($liste as $url => $titre)
    echo '<li><a href="', $url, '">', $titre, '</a></li>';
  echo "</ul>\n";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Plan du site OpenWeb</title>
</head>
<body>
<!-- Début Texte -->
<?php
$plan = getOWPlan();
if(empty($plan))
  echo "<p>Accès à la base de données impossible</p>";
else
{
  foreach($plan as $type)
  {
    echo "<h3>", $type['libelle'], "</h3>\n";
    if(array_key_exists('docs', $type))
      htmlDeListe($type['docs']);
    if(array_key_exists('crit', $type))
     foreach($type['crit'] as $crit)
     {
       echo "<h4>", $crit['libelle'], "</h4>\n";
       htmlDeListe($crit['docs']);
     }
  }
}
?>
</body>
</html>

<?php
front_xhtml_output();
?>
