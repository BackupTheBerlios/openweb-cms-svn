<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:tpl="http://openweb.eu.org/tpl"
xmlns="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="tpl">

<xsl:template match="subjectset" mode="entete">
	<meta name="DC.Subject"><xsl:attribute name="content"><xsl:apply-templates select=".//subjectterm" mode="entete"/></xsl:attribute></meta>
</xsl:template>

<xsl:template match="subjectterm[count(preceding-sibling::subjectterm)+count(../preceding-sibling::subject/subjectterm)=0]" mode="entete"><xsl:value-of select="normalize-space(text())"/></xsl:template>

<xsl:template match="subjectterm" mode="entete">, <xsl:value-of select="normalize-space(text())"/></xsl:template>

<xsl:template match="subject[@role][count(subjectterm[normalize-space(text()) != normalize-space(/article/@id)]) != 0]">
	<xsl:variable name="critere" select="$doc.criteres/criteres/critere[@name = current()/@role][@libelle]/@libelle"/>
	<xsl:if test="$critere">
		<li>
			<strong><xsl:value-of select="$critere"/>&#160;:</strong><xsl:text> </xsl:text><xsl:apply-templates select="subjectterm"/>
		</li>
	</xsl:if>
</xsl:template>

<xsl:template match="subject"/>

<xsl:template match="subjectterm[1]">
	<xsl:call-template name="jolisubjectterm"/>
</xsl:template>

<xsl:template match="subjectterm[position()!=1]">
	<xsl:text>, </xsl:text>
	<xsl:call-template name="jolisubjectterm"/>
</xsl:template>

<xsl:template name="jolisubjectterm">
  <xsl:if test="normalize-space(text()) != normalize-space(/article/@id)">
    <xsl:variable name="classement" select="$doc.criteres/criteres/critere/classements/entry[normalize-space(name/text()) = normalize-space(current()/text())]"/>
    <a>
      <xsl:if test="string($classement/location) != ''">
        <xsl:attribute name="href">
          <xsl:value-of select="$classement/location"/>
        </xsl:attribute>
      </xsl:if>
      <xsl:value-of select="$classement/libelle"/>
    </a>
  </xsl:if>
</xsl:template>

</xsl:stylesheet>
