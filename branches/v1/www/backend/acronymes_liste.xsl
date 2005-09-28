<?xml version="1.0" encoding="iso-8859-15"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml" version="1.0">

<xsl:param name="langue">fr</xsl:param>

<xsl:strip-space elements="*"/>

<xsl:output method="xml" version="1.0" encoding="utf-8"
  omit-xml-declaration="yes" indent="yes"/>

<xsl:template match="/">
	<dl>
		<xsl:apply-templates/>
	</dl>
</xsl:template>

<xsl:template match="word">
	<dt><a href="acronymes_edit.php?acronym_id={@acronym}"><xsl:apply-templates select="@acronym"/><xsl:apply-templates select="@lang"/></a></dt>
	<dd lang="{@lang}"><xsl:apply-templates/></dd>
</xsl:template>

<xsl:template match="@lang">
	<xsl:if test=". != $langue"> (<xsl:value-of select="."/>)</xsl:if>
</xsl:template>

</xsl:stylesheet>
