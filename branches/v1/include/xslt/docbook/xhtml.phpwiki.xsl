<?xml version="1.0" encoding="iso-8859-15"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:html="http://www.w3.org/1999/xhtml" version="1.0"
  exclude-result-prefixes="html">

<xsl:include href="xhtml.xsl"/>

<xsl:template match="html:body">
  <xsl:apply-templates select="html:div[@class='wikitext']/html:h3"/>
</xsl:template>

<xsl:template match="div[@id='header' or @id='footer']"/>

<xsl:template match="div[@class='errors']">
  <xsl:message terminate="no">Le Wiki a indiqué des erreurs lors du
  traitement, espérons que la source est quand même correcte.</xsl:message>
</xsl:template>

<xsl:template match="html:h2">
  <title><xsl:apply-templates/></title>
</xsl:template>

<xsl:template match="html:title"/>

<xsl:template match="html:head">
  <articleinfo>
    <xsl:apply-templates select="html:meta|html:link"/>
    <xsl:apply-templates select="/html:html/html:body/html:div[@class='wikitext']/html:h2"/>
    <xsl:message terminate="no">Attention : l'en-tête du fichier est totalement incomplet !</xsl:message>
    <pubdate>1970-01-01</pubdate>
    <date>1970-01-01</date>
    <author><firstname>John</firstname><surname>Doe</surname></author>
  </articleinfo>
</xsl:template>

<xsl:template match="html:a[@class='wiki' or @class='named-wiki']">
  <ulink url="{@href}"><xsl:apply-templates/></ulink>
  <xsl:message terminate="no">Lien vers le Wiki à corriger</xsl:message>
</xsl:template>

<xsl:template match="html:link[@rel='copyright']">
  <legalnotice>
    <xsl:call-template name="default.attrs"/>
    <para><xsl:apply-templates select="@href"/></para>
  </legalnotice>
</xsl:template>

<xsl:template match="html:link/@href">
  <ulink>
    <xsl:attribute name="url"><xsl:value-of select="."/></xsl:attribute>
    <xsl:choose>
      <xsl:when test="../@title"><xsl:value-of select="../@title"/></xsl:when>
      <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
    </xsl:choose>
  </ulink>
</xsl:template>

<xsl:template match="html:meta[@content='description']">
  <para><xsl:value-of select="."/></para>
</xsl:template>

<xsl:template match="html:meta[@name='language']" mode="entete">
  <xsl:attribute name="lang"><xsl:value-of select="@content"/></xsl:attribute>
</xsl:template>

<xsl:template name="dernierbout">
  <xsl:param name="chaine"/>
  <xsl:choose>
    <xsl:when test="contains($chaine, '/')">
      <xsl:call-template name="dernierbout">
        <xsl:with-param name="chaine" select="substring-after($chaine, '/')"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$chaine"/></xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template match="html:h1" mode="entete">
  <xsl:attribute name="id">
    <xsl:call-template name="dernierbout">
      <xsl:with-param name="chaine" select="translate(string(.), 'AZERTYUIOPQSDFGHJKLMWXCVBN ', 'azertyuiopqsdfghjklmwxcvbn_')"/>
    </xsl:call-template>
  </xsl:attribute>
</xsl:template>

</xsl:stylesheet>
