<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">
<xsl:output method="xhtml" version="1.0" encoding="UTF-8"
  omit-xml-declaration="yes" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
  doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
  indent="yes" media-type="text/html"/>

<xsl:param name="path_site_root" select="'../'"/>

<!--
===========================================================
Variable gabarit et contenu
===========================================================
-->
<xsl:variable name="doc.content" select="/"/>
<xsl:variable name="doc.template" select="document('../xslt/gabarits/xhtml.xml')"/>
<xsl:variable name="doc.criteres" select="document('../xslt/inc/criteres.xml')"/>

<!--
===========================================================
Racine du document
===========================================================
-->
<xsl:template match="/">
  <xsl:processing-instruction name="php">
  include('<xsl:value-of select="$path_site_root"/>include/frontend/switcher.inc.php');
  </xsl:processing-instruction>
  <xsl:apply-templates select="$doc.template/*" mode="template"/>
</xsl:template>

<!--
===========================================================
En t�te du document
===========================================================
-->
<xsl:template match="tpl:head" mode="template">
  <head>
    <xsl:apply-templates mode="template"/>
  </head>
</xsl:template>
<!--
===========================================================
Titre du document
===========================================================
-->
<xsl:template match="tpl:title" mode="template">Accueil</xsl:template>

<!--
===========================================================
Contenu du document
===========================================================
-->
<xsl:template match="tpl:content" mode="template">
<xsl:copy-of select="$doc.content"/>
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
Template pour virer le div#accueil non pr�vu sur la
maquette pour la page d'accueil
===========================================================
-->
<xsl:template match="*[@id='accueil']" mode="template"/>

<!--
===========================================================
Template de cr�ation de commentaire depuis le gabarit
===========================================================
-->
<xsl:template match="tpl:comment" mode="template">
  <xsl:comment><xsl:value-of select="."/></xsl:comment>
</xsl:template>

</xsl:stylesheet>

