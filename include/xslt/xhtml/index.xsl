<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">
<xsl:output method="xml" version="1.0" encoding="UTF-8"
  omit-xml-declaration="yes" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
  doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
  indent="yes" media-type="text/html"/>

<!--
===========================================================
Inclusions
===========================================================
-->
<xsl:include href="docbook_lib/params.xsl"/>
<xsl:include href="docbook_lib/gabarits.xsl"/>
<xsl:include href="docbook_lib/docbook.xsl"/>
<xsl:include href="docbook_lib/acronym.xsl"/>

<!--
===========================================================
Variables gabarit, contenu et critères
===========================================================
-->
<xsl:variable name="doc.template" select="document('../gabarits/xhtml.xml')"/>
<xsl:variable name="doc.content" select="/"/>
<xsl:variable name="doc.criteres" select="document('../inc/criteres.xml')"/>

<!--
===========================================================
Racine du document
===========================================================
-->
<xsl:template match="/">
  <xsl:processing-instruction name="php">
  include('<xsl:value-of select="$path_site_root"/>include/frontend/switcher.inc.php');
  </xsl:processing-instruction>
  <xsl:apply-templates select="$doc.template/*" mode="template"/>
</xsl:template>

</xsl:stylesheet>
