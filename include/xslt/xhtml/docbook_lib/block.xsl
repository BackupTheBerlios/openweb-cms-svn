<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<!--
===========================================================
Éléments block
===========================================================
-->

<!-- Paragraphes -->
<xsl:template match="para">
	<p>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</p>
</xsl:template>

<!-- Indications détachées du texte -->
<xsl:template match="note">
	<div class="note">
		<xsl:call-template name="output.attrs"/>
		<strong>Note&#160;:&#160;&#8212;</strong>
		<xsl:apply-templates/>
	</div>
</xsl:template>

<xsl:template match="important">
	<div class="important">
		<xsl:call-template name="output.attrs"/>
		<strong>Important&#160;:&#160;&#8212;</strong>
		<xsl:apply-templates/>
	</div>
</xsl:template>

<xsl:template match="caution">
	<div class="attention">
		<xsl:call-template name="output.attrs"/>
		<strong>Attention&#160;:&#160;&#8212;</strong>
		<xsl:apply-templates/>
	</div>
</xsl:template>

<xsl:template match="tip">
	<div class="astuce">
		<xsl:call-template name="output.attrs"/>
		<strong>Astuce&#160;:&#160;&#8212;</strong>
		<xsl:apply-templates/>
	</div>
</xsl:template>

<xsl:template match="warning">
	<div class="avertissement">
		<xsl:call-template name="output.attrs"/>
		<strong>Avertissement&#160;:&#160;&#8212;</strong>
		<xsl:apply-templates/>
	</div>
</xsl:template>

<!-- Copies d'écran, listings, etc. -->
<xsl:template match="screen">
	<pre><xsl:call-template name="output.attrs"/></pre>
</xsl:template>

<xsl:template match="programlisting">
	<pre><xsl:call-template name="output.attrs"/><xsl:apply-templates/></pre>
</xsl:template>

<!-- Citations -->
<xsl:template match="blockquote">
	<blockquote><xsl:call-template name="output.attrs"/><xsl:apply-templates/></blockquote>
</xsl:template>

</xsl:stylesheet>
