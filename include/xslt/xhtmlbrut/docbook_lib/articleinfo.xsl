<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<!--
===========================================================
Template glouton pour texte indésirable
===========================================================
-->
<xsl:template match="articleinfo/*" mode="entete" priority="-10"/>

<!--
===========================================================
Articleinfo pour HEAD du document
===========================================================
-->
<xsl:template match="articleinfo" mode="entete">
  <meta name="DC.Language" scheme="RFC3066" content="{/article/@lang}" />
  <meta name="DC.Identifier" content="{/article/@id}" />
  <meta name="DC.Creator">
    <xsl:attribute name="content"><xsl:apply-templates mode="entete" select="author"/></xsl:attribute>
  </meta>
  <xsl:apply-templates select="*[local-name()!='author']" mode="entete"/>
</xsl:template>

<!--
===========================================================
Auteurs d'un article pour HEAD du document
===========================================================
-->
<xsl:template match="articleinfo/author" mode="entete">
  <xsl:if test="position()!=1">
    <xsl:text>, </xsl:text>
  </xsl:if>
  <xsl:value-of select="concat(firstname, ' ', surname)"/>
</xsl:template>

<!--
===========================================================
Date de création du document pour HEAD
===========================================================
-->
<xsl:template match="articleinfo/date" mode="entete">
  <meta name="DC.Date.modified" scheme="W3CDTF">
    <xsl:attribute name="content"><xsl:value-of select="substring(normalize-space(.), 1,4)"/><xsl:text>-</xsl:text><xsl:value-of select="substring(normalize-space(.), 6,2)"/><xsl:text>-</xsl:text><xsl:value-of select="substring(normalize-space(.), 9,2)"/></xsl:attribute>
  </meta>
</xsl:template>

<!--
===========================================================
Date de publication du document pour HEAD
===========================================================
-->
<xsl:template match="articleinfo/pubdate" mode="entete">
  <meta name="DC.Date.created" scheme="W3CDTF">
    <xsl:attribute name="content"><xsl:value-of select="substring(normalize-space(.), 1,4)"/><xsl:text>-</xsl:text><xsl:value-of select="substring(normalize-space(.), 6,2)"/><xsl:text>-</xsl:text><xsl:value-of select="substring(normalize-space(.), 9,2)"/></xsl:attribute>
  </meta>
</xsl:template>

<!--
===========================================================
License pour HEAD
===========================================================
-->
<xsl:template match="articleinfo/legalnotice" mode="entete">
  <meta name="DC.Rights">
    <xsl:attribute name="content"><xsl:value-of select="."/></xsl:attribute>
  </meta>
</xsl:template>

<!--
===========================================================
Articleinfo pour le corps du document
===========================================================
-->
<xsl:template name="articleinfo.contenu">
  <!-- Début Auteur -->
  <ul>
    <xsl:apply-templates select="subjectset"/>
    <xsl:apply-templates select="author"/>
    <xsl:apply-templates select="date"/>
  </ul>
  <!-- Fin Auteur -->
</xsl:template>

<xsl:template match="articleinfo">
  <xsl:call-template name="articleinfo.contenu"/>
  <xsl:apply-templates select="abstract"/>
</xsl:template>

<!--
===========================================================
Date du document
===========================================================
-->
<xsl:template match="articleinfo/date">
  <li>
    <strong>Mise à jour&#160;:</strong><xsl:text> </xsl:text><xsl:value-of select="substring(normalize-space(.), 9,2)"/><xsl:text>/</xsl:text><xsl:value-of select="substring(normalize-space(.), 6,2)"/><xsl:text>/</xsl:text><xsl:value-of select="substring(normalize-space(.), 1,4)"/>
  </li>
</xsl:template>

<!--
===========================================================
Auteur du document
===========================================================
-->
<xsl:template match="articleinfo/author">
  <xsl:variable name="nom" select="concat(firstname, ' ', surname)"/>
  <li>
    <strong>Auteur&#160;:</strong><xsl:text> </xsl:text>
    <xsl:choose>
      <xsl:when test="email">
        <xsl:apply-templates select="email">
	  <xsl:with-param name="texte" select="$nom"/>
	</xsl:apply-templates>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$nom"/>
      </xsl:otherwise>
    </xsl:choose>
  </li>
</xsl:template>

<!--
===========================================================
Email de l'auteur
===========================================================
-->
<xsl:template match="email">
	<xsl:param name="texte" select="text()"/>
	<a>
		<xsl:attribute name="href">mailto:<xsl:call-template name="email.antispam"/></xsl:attribute>
		<xsl:value-of select="$texte"/>
	</a>
</xsl:template>

<xsl:template name="email.antispam">
	<xsl:param name="email" select="text()"/>
	<xsl:choose>
		<xsl:when test="contains($email, '@')"><xsl:value-of select="substring-before($email, '@')"/>%40<xsl:call-template name="email.antispam"><xsl:with-param name="email" select="substring-after($email, '@')"/></xsl:call-template></xsl:when>
		<xsl:otherwise><xsl:value-of select="$email"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>
