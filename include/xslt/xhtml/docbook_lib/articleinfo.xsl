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
  <meta name="DC.Language" content="{/article/@lang}" />
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
  <div class="auteur">
    <ul>
      <xsl:apply-templates select="subjectset"/>
      <xsl:apply-templates select="author"/>
      <xsl:apply-templates select="date"/>
    </ul>
  </div>
  <!-- Fin Auteur -->
</xsl:template>

<xsl:template match="articleinfo">
  <xsl:choose>
    <!-- Vrai lorsqu'il y a un abstract dans l'article -->
    <xsl:when test="abstract">
      <div class="abstract">
        <xsl:call-template name="articleinfo.contenu"/>
        <xsl:apply-templates select="abstract"/>
      </div>
    </xsl:when>
    <xsl:otherwise>
      <xsl:call-template name="articleinfo.contenu"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!--
===========================================================
Date du document
===========================================================
-->
<xsl:template match="articleinfo/date">
  <li class="dernier">
    <strong>Mise à jour&#160;:</strong><xsl:text> </xsl:text><xsl:value-of select="substring(normalize-space(.), 9,2)"/><xsl:text>/</xsl:text><xsl:value-of select="substring(normalize-space(.), 6,2)"/><xsl:text>/</xsl:text><xsl:value-of select="substring(normalize-space(.), 1,4)"/>
  </li>
</xsl:template>

<!--
===========================================================
Auteur du document
===========================================================
-->
<xsl:template match="articleinfo/author">
  <li>
    <strong>Auteur&#160;:</strong><xsl:text> </xsl:text><xsl:value-of select="concat(firstname, ' ', surname)"/>
  </li>
</xsl:template>

</xsl:stylesheet>
