<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<!--
===========================================================
Objets multimédia externes
===========================================================
-->

<xsl:template match="inlinemediaobject">
	<xsl:apply-templates select="imageobject"/>
</xsl:template>

<xsl:template match="mediaobject">
	<div>
		<xsl:apply-templates select="imageobject"/>
	</div>
</xsl:template>

<xsl:template match="videoobject">
	<object>
	</object>
</xsl:template>

<xsl:template match="audioobject">
	<object>
	</object>
</xsl:template>

<xsl:template match="imageobject">
	<img src="{imagedata/@fileref}">
		<xsl:attribute name="alt"><xsl:apply-templates select="following-sibling::textobject//text()"/></xsl:attribute>
	</img>
</xsl:template>

</xsl:stylesheet>
