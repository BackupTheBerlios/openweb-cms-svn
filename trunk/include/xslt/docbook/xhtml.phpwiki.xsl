<?xml version="1.0" encoding="iso-8859-15"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:html="http://www.w3.org/1999/xhtml" version="1.0"
  exclude-result-prefixes="html">

<!-- La syntaxe du Wiki est molle, notamment les auteurs n'utilisent pas
toujours une hiérarchisation rigoureuse des titres (le niveau le plus haut
sera peut-être un <h3>, le <h2> étant omis). La variable highest-heading
va permettre de repérer le premier (le plus haut) niveau des sections. -->
<xsl:variable name="highest-heading">
  <xsl:variable name="h2count" select="html:div[@class='wikitext']/html:h2"/>
  <xsl:variable name="h3count" select="html:div[@class='wikitext']/html:h3"/>
  <xsl:variable name="h4count" select="html:div[@class='wikitext']/html:h4"/>
  <xsl:variable name="h5count" select="html:div[@class='wikitext']/html:h5"/>
  <xsl:variable name="h6count" select="html:div[@class='wikitext']/html:h6"/>
  <xsl:choose>
    <xsl:when test="$h2count > 1">h2</xsl:when>
  </xsl:choose>
</xsl:variable>

<xsl:include href="xhtml.xsl"/>

<!-- Bibliothèque de petites fonctions utiles pour manipuler les chaînes
  du document source -->
<xsl:template name="explose.wikiword">
  <xsl:param name="separateur"><xsl:text> </xsl:text></xsl:param>
  <xsl:param name="chaine"/>
  <xsl:param name="debut" select="true()"/>

  <xsl:choose>
    <xsl:when test="string-length($chaine) = 0"/>

    <xsl:otherwise>
      <xsl:variable name="premiere" select="substring($chaine,1,1)"/>
      <xsl:if test="contains('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $premiere) and not($debut)">
        <xsl:value-of select="$separateur"/>
      </xsl:if>
      <xsl:value-of select="$premiere"/>
      <xsl:call-template name="explose.wikiword">
        <xsl:with-param name="separateur" select="$separateur"/>
        <xsl:with-param name="chaine" select="substring($chaine,2)"/>
	<xsl:with-param name="debut" select="false()"/>
      </xsl:call-template>
    </xsl:otherwise>
  </xsl:choose>
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

<xsl:template name="en.minuscules">
  <xsl:param name="chaine"/>
  <xsl:value-of select="translate($chaine, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')"/>
</xsl:template>


<xsl:template match="html:body">
  <xsl:apply-templates select="html:div[@class='wikitext']/html:h3"/>
</xsl:template>

<xsl:template match="html:div[@id='header' or @id='footer' or @class='toolbar'
  or @id='navbuttons' or @id='logo']"/>

<xsl:template match="html:div[@class='errors']">
  <xsl:message terminate="no">Le Wiki a indiqué des erreurs lors du
  traitement, espérons que la source est quand même correcte.</xsl:message>
</xsl:template>

<xsl:template match="html:h1">
  <title>
    <xsl:call-template name="dernierbout">
      <xsl:with-param name="chaine" select="string(.)"/>
    </xsl:call-template>
  </title>
</xsl:template>

<xsl:template match="html:h2">
  <title><xsl:apply-templates/></title>
</xsl:template>

<xsl:template match="html:title"/>

<xsl:template name="trouve.titre">
  <xsl:choose>
    <xsl:when test="//html:div[@class='wikitext']/html:h2[1]">
      <xsl:apply-templates select="/html:html/html:body/html:div[@class='wikitext']/html:h2[1]"/>
    </xsl:when>
    <xsl:otherwise>
      <xsl:apply-templates select="//html:h1"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template match="html:li[html:a[@class='wiki' or @class='named-wiki']]" mode="metadata">
  <xsl:variable name="valeur">
    <xsl:call-template name="en.minuscules">
      <xsl:with-param name="chaine" select="string(.)"/>
    </xsl:call-template>
  </xsl:variable>
  <xsl:variable name="criteres" select="document('criteres.wiki.xml')"/>

  <xsl:choose>
    <xsl:when test="contains($valeur, 'auteur')">
      <xsl:for-each select=".//html:a[@class='wiki' or @class='named-wiki']">
        <xsl:variable name="nom.auteur">
          <xsl:call-template name="explose.wikiword">
            <xsl:with-param name="chaine" select="@href"/>
          </xsl:call-template>
        </xsl:variable>
        <author>
          <firstname><xsl:value-of select="substring-before($nom.auteur, ' ')"/></firstname>
          <surname><xsl:value-of select="substring-after($nom.auteur, ' ')"/></surname>
        </author>
      </xsl:for-each>
    </xsl:when>
  </xsl:choose>
</xsl:template>

<xsl:template match="html:head">
  <articleinfo>
    <xsl:apply-templates select="html:meta|html:link"/>
    <xsl:call-template name="trouve.titre"/>
    <xsl:apply-templates select="/html:html/html:body/html:div[@class='wikitext']/html:ul[1]" mode="metadata"/>
<!--    <xsl:message terminate="no">Attention : l'en-tête du fichier est totalement incomplet !</xsl:message> -->
<!--    <pubdate>1970-01-01</pubdate> -->
<!--    <date>1970-01-01</date> -->
<!--    <author><firstname>John</firstname><surname>Doe</surname></author> -->
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

<xsl:template match="html:h1" mode="entete">
  <xsl:attribute name="id">
    <xsl:call-template name="dernierbout">
      <xsl:with-param name="chaine" select="translate(string(.), 'AZERTYUIOPQSDFGHJKLMWXCVBN ', 'azertyuiopqsdfghjklmwxcvbn_')"/>
    </xsl:call-template>
  </xsl:attribute>
</xsl:template>

</xsl:stylesheet>
