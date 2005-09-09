<?php
require_once('../../include/frontend/init.inc.php');
require_once(PATH_INC_FRONTEND.'html_listedoc.inc.php');
require_once(PATH_INC_FRONTEND.'front_xhtml.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Humeurs</title>
</head>
<body>
<!-- Début Texte -->
<?php
OW_liste_document(array('type' => 'H'), 200, "Toutes les humeurs");
?>
</body>
</html>

<?php
front_xhtml_output();
?>
