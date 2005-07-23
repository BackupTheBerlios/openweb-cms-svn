<?php

require_once(PATH_INC_FRONTEND.'switcher.inc.php');

function front_xhtml_output()
{
  $buf = ob_get_contents();
  $xh = xslt_create();
  $args = array('/_xml' => '<'.'?xml version="1.0" encoding="utf-8"?'.'>'.$buf);
  ob_end_clean();
  $params = array('path_site_root' => PATH_SITE_ROOT);
  $result = xslt_process($xh, 'arg:/_xml', PATH_INC_FRONTEND.'front_xhtml.xsl',
      null, $args, $params);
  eval('?>'.$result);
}

ob_start();

?>
