<?xml version="1.0" encoding="iso-8859-15"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:html="http://www.w3.org/1999/xhtml" version="1.0"
  exclude-result-prefixes="html">

<xsl:variable name="doc.criteres" select="document('../inc/criteres.xml')"/>

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
	<xsl:apply-templates select="$doc.criteres"/>
</xsl:template>

<xsl:template match="criteres">
	<subjectset>
		<xsl:apply-templates/>
	</subjectset>
</xsl:template>

<xsl:template match="critere">
	<subject>
		<xsl:apply-templates select="@name"/>
		<xsl:apply-templates/>
	</subject>
</xsl:template>

<xsl:template match="critere/@name">
	<xsl:attribute name="role"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<!-- TODO l'appel à contains() peut retourner un faux vrai si le nom d'un
     classement possible est contenu dans le nom d'un autre classement -->
<xsl:template match="entry[contains($doc.content//html:meta[@name='DC.Subject']/@content, normalize-space(name/text()))]">
	<subjectterm>
		<xsl:choose>
			<xsl:when test="location != ''">
				<a href="{location/text()}">
					<xsl:apply-templates select="name"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="name"/>
			</xsl:otherwise>
		</xsl:choose>
	</subjectterm>
</xsl:template>

<!-- On ignore les entrées qui ne nous conviennent pas -->
<xsl:template match="entry"/>

</xsl:stylesheet>
