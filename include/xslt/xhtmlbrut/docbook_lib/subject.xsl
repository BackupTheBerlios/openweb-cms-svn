<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<xsl:template match="subject[@role='profil']" mode="entete">
  <meta name="DC.Audience"><xsl:attribute name="content"><xsl:apply-templates select="subjectterm" mode="entete"/></xsl:attribute></meta>
</xsl:template>

<xsl:template match="subject[@role='technologie']" mode="entete">
  <meta name="DC.ResourceType"><xsl:attribute name="content"><xsl:apply-templates select="subjectterm" mode="entete"/></xsl:attribute></meta>
</xsl:template>

<xsl:template match="subject[@role='theme']" mode="entete">
  <meta name="DC.Subject"><xsl:attribute name="content"><xsl:apply-templates select="subjectterm" mode="entete"/></xsl:attribute></meta>
</xsl:template>

<xsl:template match="subjectterm[1]" mode="entete"><xsl:value-of select="normalize-space(text())"/></xsl:template>

<xsl:template match="subjectterm[position()!=1]" mode="entete">, <xsl:value-of select="normalize-space(text())"/></xsl:template>

<xsl:template match="subject[@role='profil']">
  <li>
    <strong>Profil&#160;:</strong><xsl:text> </xsl:text><xsl:apply-templates select="subjectterm"/>
  </li>
</xsl:template>

<xsl:template match="subject[@role='technologie']">
  <li>
    <strong>Technologie&#160;:</strong><xsl:text> </xsl:text><xsl:apply-templates select="subjectterm"/>
  </li>
</xsl:template>

<xsl:template match="subject[@role='theme']">
  <li>
    <strong>Thème&#160;:</strong><xsl:text> </xsl:text><xsl:apply-templates select="subjectterm"/>
  </li>
</xsl:template>

<xsl:template match="subjectterm[1]">
  <xsl:call-template name="jolisubjectterm">
    <xsl:with-param name="term"><xsl:value-of select="normalize-space(text())"/></xsl:with-param>
  </xsl:call-template>
</xsl:template>

<xsl:template match="subjectterm[position()!=1]">,
  <xsl:call-template name="jolisubjectterm">
    <xsl:with-param name="term"><xsl:value-of select="normalize-space(text())"/></xsl:with-param>
  </xsl:call-template>
</xsl:template>

<xsl:template name="jolisubjectterm">
  <xsl:param name="term"/>
  <a href="/{translate($term, ' ', '_')}/">
  <xsl:choose>
    <xsl:when test="$term='debutant'">Débutant</xsl:when>
    <xsl:when test="$term='expert'">Expert</xsl:when>
    <xsl:when test="$term='gourou'">Gourou</xsl:when>
    <xsl:when test="$term='decideur'">Décideur</xsl:when>

    <xsl:when test="$term='navigateurs'">Navigateurs</xsl:when>
    <xsl:when test="$term='etudes de cas'">Étude de cas</xsl:when>
    <xsl:when test="$term='etude cas'">Étude de cas</xsl:when>
    <xsl:when test="$term='structure'">Structure</xsl:when>
    <xsl:when test="$term='pages dynamiques'">Pages dynamiques</xsl:when>
    <xsl:when test="$term='mise en page'">Mise en page</xsl:when>
    <xsl:when test="$term='accessibilite'">Accessibilité</xsl:when>

    <xsl:when test="$term='xhtml'"><acronym>XHTML</acronym></xsl:when>
    <xsl:when test="$term='css'"><acronym>CSS</acronym></xsl:when>
    <xsl:when test="$term='dom'"><acronym>DOM</acronym></xsl:when>
  </xsl:choose>
  </a>
</xsl:template>

</xsl:stylesheet>
