<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<!--
===========================================================
Template gourmand pour les gabarits
===========================================================
-->
<xsl:template match="*" priority="-10" mode="template">

  <xsl:element name="{name(.)}">
    <xsl:for-each select="attribute::*">
      <xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>
    </xsl:for-each>
     <xsl:apply-templates mode="template"/>
  </xsl:element>
</xsl:template>

<!--
===========================================================
Template de création de commentaire depuis le gabarit
===========================================================
-->
<xsl:template match="tpl:comment" mode="template">
  <xsl:comment><xsl:value-of select="."/></xsl:comment>
</xsl:template>

<!--
===========================================================
En tête du document
===========================================================
-->
<xsl:template match="tpl:head" mode="template">
  <head>
    <xsl:apply-templates mode="template"/>
    <xsl:apply-templates select="$doc.content/article/articleinfo" mode="entete"/>
  </head>
</xsl:template>

<!--
===========================================================
Titre du document
===========================================================
-->
<xsl:template match="tpl:title" mode="template">
  <xsl:value-of select="$doc.content/article/articleinfo/title"/>
</xsl:template>

<!--
===========================================================
Contenu du document
===========================================================
-->
<xsl:template match="tpl:content" mode="template">
  <div id="texte">
    <h2><xsl:value-of select="$doc.content/article/articleinfo/title"/></h2>
    <!-- Contenu -->
    <xsl:apply-templates select="$doc.content/article"/>
    <hr/>
    <xsl:comment>OW_LISTE_ART</xsl:comment>
    <xsl:variable name="email">
      <xsl:choose>
        <xsl:when test="$doc.content/article/articleinfo/author[1]/email"><xsl:apply-templates select="$doc.content/article/articleinfo/author[1]/email/text()"/></xsl:when>
      <xsl:otherwise>editorial@openweb.eu.org</xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <p class="reaction">Une question, une remarque&#160;? Écrivez à l'auteur à <a href="mailto:{$email}"><xsl:value-of select="$email"/></a>.</p>
  </div>
</xsl:template>

<xsl:template match="tpl:style-switcher-links" mode="template">
  <xsl:processing-instruction name="php">
echo stylesheet_list();
</xsl:processing-instruction>
</xsl:template>

<xsl:template match="tpl:style-switcher-form" mode="template">
  <xsl:processing-instruction name="php">
echo show_switcher();
</xsl:processing-instruction>
</xsl:template>

<xsl:template match="tpl:classements" mode="template">
  <xsl:variable name="crit" select="@critere"/>
  <xsl:apply-templates select="$doc.criteres/criteres/critere[@name=$crit]/classements" mode="classements"/>
</xsl:template>

<xsl:template match="tpl:critere" mode="template">
  <xsl:variable name="crit" select="@name"/>
  <xsl:apply-templates select="$doc.criteres/criteres/critere[@name=$crit] " mode="classements"/>
</xsl:template>

<xsl:template match="classements" mode="classements">
  <ul>
    <xsl:apply-templates mode="classements"/>
  </ul>
</xsl:template>

<xsl:template match="entry" mode="classements">
  <li><a><xsl:attribute name="href"><xsl:value-of select="location"/></xsl:attribute><xsl:value-of select="libelle"/></a></li>
</xsl:template>

<xsl:template match="critere" mode="classements">
  <div id="{@name}">
    <h2><xsl:value-of select="@libelle"/></h2>
    <xsl:apply-templates mode="classements"/>
  </div>
</xsl:template>

</xsl:stylesheet>
