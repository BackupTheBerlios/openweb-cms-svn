<?xml version="1.0" encoding="iso-8859-15"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:html="http://www.w3.org/1999/xhtml" version="1.0"
  exclude-result-prefixes="html">

<xsl:template match="html:meta[@name='DC.Relation.isPartOf']" mode="entete">
	<xsl:attribute name="role"><xsl:value-of select="@content"/></xsl:attribute>
</xsl:template>

<xsl:template match="html:meta[@name='DC.Identifier']" mode="entete">
	<xsl:attribute name="id"><xsl:value-of select="@content"/></xsl:attribute>
</xsl:template>

<xsl:template match="html:meta[@name='DC.Creator']">
	<xsl:call-template name="separeauteur">
		<xsl:with-param name="chaine" select="@content"/>
	</xsl:call-template>
</xsl:template>

<xsl:template name="separeauteur">
	<xsl:param name="chaine"></xsl:param>
	<xsl:variable name="auteur">
		<xsl:choose>
			<xsl:when test="contains($chaine, ',')">
				<xsl:value-of select="normalize-space(substring-before($chaine, ','))"/>
			</xsl:when>
			<xsl:otherwise><xsl:value-of select="normalize-space($chaine)"/></xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:if test="$auteur!=''">
		<author>
			<firstname><xsl:value-of select="substring-before($auteur, ' ')"/></firstname>
			<surname><xsl:value-of select="substring-after($auteur, ' ')"/></surname>
		</author>
		<xsl:call-template name="separeauteur">
			<xsl:with-param name="chaine" select="substring-after($chaine, ',')"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<xsl:template match="html:meta[@name='DC.Date.created']">
	<pubdate><xsl:value-of select="@content"/></pubdate>
</xsl:template>

<xsl:template match="html:meta[@name='DC.Date.modified']">
	<date><xsl:value-of select="@content"/></date>
</xsl:template>

<xsl:template match="html:meta[@name='DC.Description.abstract']">
	<abstract>
		<para>
			<xsl:value-of select="@content"/>
		</para>
	</abstract>
</xsl:template>

<xsl:template match="html:meta[@name='DC.Subject']">
	<subject role="theme">
		<xsl:call-template name="separesujet">
			<xsl:with-param name="chaine" select="@content"/>
		</xsl:call-template>
	</subject>
</xsl:template>

<xsl:template match="html:meta[@name='DC.Audience']">
	<subject role="profil">
		<xsl:call-template name="separesujet">
			<xsl:with-param name="chaine" select="@content"/>
		</xsl:call-template>
	</subject>
</xsl:template>

<xsl:template match="html:meta[@name='DC.Type']">
	<subject role="technologie">
		<xsl:call-template name="separesujet">
			<xsl:with-param name="chaine" select="@content"/>
		</xsl:call-template>
	</subject>
</xsl:template>

<xsl:template name="separesujet">
	<xsl:param name="chaine"></xsl:param>
	<xsl:variable name="sujet">
		<xsl:choose>
			<xsl:when test="contains($chaine, ',')">
				<xsl:value-of select="normalize-space(substring-before($chaine, ','))"/>
			</xsl:when>
			<xsl:otherwise><xsl:value-of select="normalize-space($chaine)"/></xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:if test="$sujet!=''">
		<subjectterm><xsl:value-of select="$sujet"/></subjectterm>
		<xsl:call-template name="separesujet">
			<xsl:with-param name="chaine" select="substring-after($chaine, ',')"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
