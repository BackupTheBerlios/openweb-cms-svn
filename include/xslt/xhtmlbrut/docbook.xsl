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
Variable gabarit et contenu
===========================================================
-->
<xsl:variable name="doc.template" select="document('../gabarits/xhtmlbrut.xml')"/>
<xsl:variable name="doc.content" select="/"/>

<!--
===========================================================
Racine du document
===========================================================
-->
<xsl:template match="/">
  <xsl:apply-templates select="$doc.template/*" mode="template"/>
</xsl:template>

<!--
===========================================================
En tête du document
===========================================================
-->
<xsl:template match="tpl:head" mode="template">
  <head>
    <xsl:apply-templates mode="template"/>
    <xsl:apply-templates select="$doc.content/article/articleinfo" mode="entete"/>
  </head>
</xsl:template>

<!--
===========================================================
Titre du document
===========================================================
-->
<xsl:template match="tpl:title" mode="template">
  <xsl:value-of select="$doc.content/article/articleinfo/title"/>
</xsl:template>

<!--
===========================================================
Contenu du document
===========================================================
-->
<xsl:template match="tpl:content" mode="template">
  <h1><xsl:value-of select="$doc.content/article/articleinfo/title"/></h1>
  <!-- Contenu -->
  <xsl:apply-templates select="$doc.content/article"/>
</xsl:template>

</xsl:stylesheet>
