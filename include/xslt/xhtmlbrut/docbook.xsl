<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">
<xsl:output method="xhtml" version="1.0" encoding="UTF-8"
  omit-xml-declaration="yes" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
  doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
  indent="yes" media-type="text/html"/>

<!--
===========================================================
Inclusions
===========================================================
-->
<xsl:include href="docbook_lib/gabarits.xsl"/>
<xsl:include href="docbook_lib/docbook.xsl"/>
<xsl:include href="docbook_lib/acronym.xsl"/>

<!--
===========================================================
Variables gabarit, contenu et critères
===========================================================
-->
<xsl:variable name="doc.template" select="document('../gabarits/xhtmlbrut.xml')"/>
<xsl:variable name="doc.content" select="/"/>
<xsl:variable name="doc.criteres" select="document('../inc/criteres.xml')"/>

<!--
===========================================================
Racine du document
===========================================================
-->
<xsl:template match="/">
  <xsl:apply-templates select="$doc.template/*" mode="template"/>
</xsl:template>

</xsl:stylesheet>
