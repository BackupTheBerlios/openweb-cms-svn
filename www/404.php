<?php
require ('../include/frontend/init.inc.php');
require (PATH_INC_FRONTEND.'front_xhtml.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
        <title>Page non trouvée</title>
</head>
<body>
<!-- Début Texte -->
  <p>Désolé, la page que vous cherchez n'existe pas ou plus.</p>
  <p>Si vous êtes arrivé sur cette page en suivant un lien, merci de
nous indiquer lequel en écrivant à
<a href="mailto:webmestre%40openweb.eu.org">webmestre@openweb.eu.org</a>.</p>
<!-- Fin Texte -->
</body>
</html>
<? front_xhtml_output(); ?>
