<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<!--
===========================================================
Listes
===========================================================
-->

<!--
Listes ordonnée et non-ordonnée
-->
<xsl:template match="orderedlist">
	<ol>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</ol>
</xsl:template>

<xsl:template match="itemizedlist">
	<ul>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</ul>
</xsl:template>

<xsl:template match="listitem[count(*)=1]/para">
	<xsl:call-template name="output.attrs"/>
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="listitem">
	<li>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</li>
</xsl:template>

<!--
Liste de définitions
-->

<xsl:template match="variablelist">
 	<dl>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</dl>
</xsl:template>

<xsl:template match="varlistentry">
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="varlistentry/term">
	<dt>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</dt>
</xsl:template>

<xsl:template match="varlistentry/listitem">
	<dd>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</dd>
</xsl:template>

<!--
Question-réponses
-->

<xsl:template match="qandaset">
	<dl>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates select="qandaentry"/>
	</dl>
</xsl:template>

<xsl:template match="question">
	<dt>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</dt>
</xsl:template>

<xsl:template match="question/para">
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="answer">
	<dd>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates select="*[local-name() != 'label']"/>
	</dd>
</xsl:template>

<xsl:template match="answer/para[1]">
	<p>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates select="../label"/>
		<xsl:apply-templates/>
	</p>
</xsl:template>

<xsl:template match="label">
	<strong>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
		<xsl:text> &#8212;</xsl:text>
	</strong>
	<xsl:text> </xsl:text>
</xsl:template>

</xsl:stylesheet>
