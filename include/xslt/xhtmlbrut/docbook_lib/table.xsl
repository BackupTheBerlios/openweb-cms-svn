<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<xsl:template match="table">
  <table width="100%" border="1" rules="all">
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates select="textobject"/>
    <xsl:apply-templates select="title|tgroup"/>
  </table>
</xsl:template>

<xsl:template match="table/textobject/phrase">
  <xsl:attribute name="summary"><xsl:value-of select="normalize-space(text())"/></xsl:attribute>
</xsl:template>

<xsl:template match="table/title">
  <caption>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </caption>
</xsl:template>

<xsl:template match="thead">
  <thead>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </thead>
</xsl:template>

<xsl:template match="tbody">
  <tbody>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </tbody>
</xsl:template>

<xsl:template match="row">
  <tr>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </tr>
</xsl:template>

<xsl:template match="thead/row/entry">
  <th>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </th>
</xsl:template>

<xsl:template match="entry">
  <td>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </td>
</xsl:template>

</xsl:stylesheet>
