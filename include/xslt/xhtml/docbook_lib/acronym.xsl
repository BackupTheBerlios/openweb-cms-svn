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
    <xsl:call-template name="output.attrs"/>
    <xsl:if test="$position = 1">
      <xsl:choose>
        <xsl:when test="string($find) != ''">
          <!-- si $find a une valeur on va l'utiliser en title -->
          <xsl:attribute name="title">
            <xsl:value-of select="$find"/>
          </xsl:attribute>
          <xsl:attribute name="lang">
            <xsl:value-of select="$dico/word[@acronym=$word]/@lang"/>
          </xsl:attribute>
        </xsl:when>
        <xsl:when test="//glossentry/acronym[text() = $word]">
          <xsl:attribute name="title">
            <xsl:value-of select="//glossentry/acronym[text() = $word]/../glossterm"/>
          </xsl:attribute>
          <xsl:if test="//glossentry/acronym[text() = $word]/../glossterm/@lang">
            <xsl:attribute name="lang">
              <xsl:value-of select="//glossentry/acronym[text() = $word]/../glossterm/@lang"/>
            </xsl:attribute>
          </xsl:if>
        </xsl:when>
      </xsl:choose>
    </xsl:if>
    <xsl:apply-templates/>
  </acronym>
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
