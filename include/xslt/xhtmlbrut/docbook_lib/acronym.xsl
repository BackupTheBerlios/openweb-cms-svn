<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<!--
===========================================================
Acronym qui n'est pas dans un title
===========================================================
-->
<xsl:template match="acronym[not(ancestor::title)]">
  <xsl:param name="word" select="text()"/>
  
  <!-- Position de l'acronyme -->
  <xsl:variable name="position">
    <xsl:number level="any" count="acronym[text() = $word and not (ancestor::title)]"/>
  </xsl:variable>
  
  <!-- Vérification de la présence de l'acronym dans le fichier -->
  <xsl:variable name="dico" select="document('../../inc/acronyms.xml')/acronyms"/>
  <xsl:variable name="find" select="$dico/word[@acronym=$word]"/>
  
  <acronym>
    <xsl:if test="$position = 1">
    <xsl:call-template name="output.attrs"/>
    <!-- l'appel précédent peut avoir créé des attributs @title et @lang,
         ils seront remplacés si nécessaire -->
      <xsl:if test="string($find) != ''">
        <!-- si $find a une valeur on va l'utiliser en title -->
        <xsl:attribute name="title">
          <xsl:value-of select="$find"/>
        </xsl:attribute>
        <xsl:attribute name="lang">
          <xsl:value-of select="$dico/word[@acronym=$word]/@lang"/>
        </xsl:attribute>
      </xsl:if>
    </xsl:if>

    <xsl:apply-templates/>
  </acronym>
</xsl:template>

<!--
Attribut role d'un acronyme qui n'est pas dans le dico
-->
<xsl:template match="acronym/@role">
  <xsl:attribute name="title">
    <xsl:value-of select="."/>
  </xsl:attribute>
</xsl:template>

<!--
===========================================================
Acronym d'un title
===========================================================
-->
<xsl:template match="acronym[(ancestor::title)]">
  <acronym><xsl:apply-templates/></acronym>
</xsl:template>

</xsl:stylesheet>
