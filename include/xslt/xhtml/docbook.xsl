<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<xsl:strip-space elements="*"/>
<xsl:preserve-space elements="screen programlisting token"/>

<!--
===========================================================
  Recopie des attributs
===========================================================
-->
<xsl:template name="output.attrs">
  <xsl:apply-templates select="attribute::*"/>
</xsl:template>

<!-- Template glouton pour attributs à jeter -->
<xsl:template match="attribute::*" priority="-10"/>

<!-- Attributs généraux toujours recopiés -->
<xsl:template match="@lang">
  <xsl:attribute name="lang"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="@xml:lang">
  <xsl:attribute name="xml:lang"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="@id">
  <xsl:attribute name="id"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<!--
===========================================================
Structure générale du document
===========================================================
-->

<xsl:template match="article">
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="abstract" mode="entete"></xsl:template>

<xsl:template match="title" mode="entete"></xsl:template>

<xsl:template match="article/title|section/title">
	<xsl:variable name="niveau" select="count(ancestor-or-self::section)+2"/>
	<xsl:variable name="element">
		<xsl:choose>
			<xsl:when test="$niveau > 6">h6</xsl:when>
			<xsl:otherwise>h<xsl:value-of select="$niveau"/></xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:element name="{$element}">
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</xsl:element>
</xsl:template>

<xsl:template match="abstract/para">
	<h3>En bref</h3>
	<p>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</p>
	<hr />
</xsl:template>

<xsl:template match="article/subtitle">
	<h3>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</h3>
</xsl:template>

<xsl:include href="articleinfo.xsl"/>
<xsl:include href="subject.xsl"/>
<xsl:include href="bibliography.xsl"/>
<xsl:include href="table.xsl"/>
<xsl:include href="inline.xsl"/>
<xsl:include href="block.xsl"/>
<xsl:include href="listes.xsl"/>
<xsl:include href="mediaobject.xsl"/>

</xsl:stylesheet>
