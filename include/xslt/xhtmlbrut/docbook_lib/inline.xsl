<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<!--
===========================================================
Éléments inline
===========================================================
-->

<xsl:template match="emphasis">
	<em>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</em>
</xsl:template>

<xsl:template match="emphasis[@role='strong']">
	<strong>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</strong>
</xsl:template>

<xsl:template match="para/programlisting">
	<code>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</code>
</xsl:template>

<xsl:template match="replaceable">
	<strong>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</strong>
</xsl:template>

<xsl:template match="type">
	<em>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</em>
</xsl:template>

<xsl:template match="constant">
	<code>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</code>
</xsl:template>

<xsl:template match="sgmltag">
	<code>
		<xsl:call-template name="output.attrs"/>
		<xsl:choose>
			<xsl:when test="@class='attribute'">"<xsl:apply-templates/>"</xsl:when>
			<xsl:when test="@class='starttag'">&lt;<xsl:apply-templates/>&gt;</xsl:when>
			<xsl:when test="@class='endtag'">&lt;/<xsl:apply-templates/>&gt;</xsl:when>
			<xsl:when test="@class='emptytag'">&lt;<xsl:apply-templates/>&#160;/&gt;</xsl:when>
			<xsl:when test="@class='xmlpi'">&lt;?<xsl:apply-templates/>?&gt;</xsl:when>
			<xsl:when test="@class='comment'">&lt;!--<xsl:apply-templates/>--&gt;</xsl:when>
			<xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
		</xsl:choose>
	</code>
</xsl:template>

<xsl:template match="filename">
	<code>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</code>
</xsl:template>

<xsl:template match="literal">
	<code>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</code>
</xsl:template>

<xsl:template match="token">
	<code>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</code>
</xsl:template>

<xsl:template match="userinput">
	<kbd>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</kbd>
</xsl:template>

<xsl:template match="quote">
	<q>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</q>
</xsl:template>

<xsl:template match="ulink">
	<a href="{@url}">
	    	<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	<!-- TODO: attribut @hreflang -->
	</a>
</xsl:template>

<xsl:template match="ulink/@role">
	<xsl:attribute name="title"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="xref">
	<a>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</a>
</xsl:template>

<xsl:template match="xref/@linkend">
	<xsl:attribute name="href">#<xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="personname">
	<xsl:value-of select="concat(firstname, ' ', surname)"/>
</xsl:template>

<xsl:template match="subscript">
	<sub>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</sub>
</xsl:template>

<xsl:template match="superscript">
	<sup>
		<xsl:call-template name="output.attrs"/>
		<xsl:apply-templates/>
	</sup>
</xsl:template>

</xsl:stylesheet>
