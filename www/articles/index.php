<?php
require_once('../../include/frontend/init.inc.php');
require_once(PATH_INC_FRONTEND.'html_listedoc.inc.php');
require_once(PATH_INC_FRONTEND.'front_xhtml.inc.php');
?>
<!-- DÃ©but Texte -->
<div xmlns="http://www.w3.org/1999/xhtml" id="texteaccueil">
<?php
OW_liste_document(array('type' => 'A'), 200, "Tous les articles d'OpenWeb");
?>
</div>

<?php
front_xhtml_output();
?>
