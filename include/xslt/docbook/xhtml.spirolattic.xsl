<?xml version="1.0" encoding="iso-8859-15"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:html="http://www.w3.org/1999/xhtml" version="1.0">

<xsl:include href="xhtml.xsl"/>

<xsl:template match="html:body">
  <xsl:apply-templates select="html:div[@class='wikitext']/html:h3"/>
</xsl:template>

<xsl:template match="html:h2">
  <title><xsl:apply-templates/></title>
</xsl:template>

<xsl:template match="html:title"></xsl:template>

<xsl:template match="html:head">
  <articleinfo>
    <xsl:apply-templates select="/html:html/html:body/html:div[@class='wikitext']/html:h2"/>
    <xsl:message terminate="no">Attention : l'en-tête du fichier est totalement incomplet !</xsl:message>
  </articleinfo>
</xsl:template>

<xsl:template match="html:a[@class='wiki']">
  <ulink url="{@href}"><xsl:apply-templates/></ulink>
  <xsl:message terminate="no">Lien vers le Wiki à corriger</xsl:message>
</xsl:template>

</xsl:stylesheet>
