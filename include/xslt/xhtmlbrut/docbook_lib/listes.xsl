<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<!--
===========================================================
Listes
===========================================================
-->

<!--
Listes ordonnées et non-ordonnées
-->
<xsl:template match="orderedlist">
  <ol>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </ol>
</xsl:template>

<xsl:template match="itemizedlist">
  <ul>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </ul>
</xsl:template>

<xsl:template match="listitem">
  <li>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </li>
</xsl:template>

<!--
Liste de définitions
-->

<xsl:template match="variablelist">
   <dl>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </dl>
</xsl:template>

<xsl:template match="varlistentry">
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="varlistentry/term">
  <dt>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </dt>
</xsl:template>

<xsl:template match="varlistentry/listitem">
  <dd>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </dd>
</xsl:template>

<!--
Question-réponses
-->

<xsl:template match="qandaset">
  <dl>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates select="qandaentry"/>
  </dl>
</xsl:template>

<xsl:template match="question">
  <dt>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </dt>
</xsl:template>

<xsl:template match="question/para">
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="answer">
  <dd>
    <xsl:call-template name="output.attrs"/>
    <xsl:apply-templates/>
  </dd>
</xsl:template>

</xsl:stylesheet>
