<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<xsl:template match="bibliography">
  <h2>Bibliographie</h2>
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="biblioentry">
  <p><xsl:apply-templates select="abbrev"/>
  <xsl:apply-templates select="author"/>
  <xsl:apply-templates select="title"/>
  <xsl:text>,</xsl:text>
  <xsl:apply-templates select="pubdate"/>
  <xsl:text>.</xsl:text>
  </p>
</xsl:template>

<xsl:template match="biblioentry/abbrev">
  <strong>[<xsl:apply-templates/>]</strong>
</xsl:template>

<xsl:template match="biblioentry/title">
  <em><xsl:apply-templates/></em>
</xsl:template>

<xsl:template match="biblioentry/author">
  <xsl:apply-templates select="surname"/>, <xsl:apply-templates select="firstname"/>.
</xsl:template>

</xsl:stylesheet>
