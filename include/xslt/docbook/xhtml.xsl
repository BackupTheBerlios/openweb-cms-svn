<?xml version="1.0" encoding="iso-8859-15"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:html="http://www.w3.org/1999/xhtml" version="1.0"
  exclude-result-prefixes="html">

<xsl:include href="dublincore.xsl"/>

<!--
Transformation d'un document XHTML Strict en Docbook XML
-->

<xsl:strip-space elements="*"/>
<xsl:preserve-space elements="html:code html:pre"/>

<xsl:output method="xml" version="1.0" encoding="UTF-8"
  omit-xml-declaration="no" indent="yes"
  doctype-public="-//OASIS//DTD DocBook XML V4.2//EN"
  doctype-system="http://openweb.eu.org/dtd/docbkx42/docbookx.dtd"/>

<!--
Templates mangeurs d'attributs
-->

<xsl:template name="default.attrs">
	<xsl:apply-templates select="@lang|@xml:lang|@id"/>
</xsl:template>

<xsl:template match="@lang">
	<xsl:attribute name="lang"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="@xml:lang">
	<xsl:attribute name="xml:lang"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="@dir">
	<xsl:message terminate="no">Attribut « dir » non géré</xsl:message>
</xsl:template>

<xsl:template match="@id">
	<xsl:attribute name="id"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="@onclick|@ondblclick|@onmousedown|@onmouseup|@onmouseover|@onmousemove|@onmouseout|@onkeypress|@onkeydown|@onkeyup|@style"/>

<xsl:template match="@class|@title"/>

<xsl:template match="/">
	<xsl:apply-templates/>
</xsl:template>


<!-- 7. The global structure of an HTML document -->

<xsl:template match="html:html">
	<article>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates select="/html:body/html:h1"/>
		<xsl:apply-templates/>
	</article>
</xsl:template>

<xsl:template match="html:head">
	<articleinfo>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</articleinfo>
</xsl:template>

<xsl:template match="html:title">
	<title><xsl:apply-templates/></title>
</xsl:template>

<xsl:template match="html:body">
	<xsl:apply-templates select="*[generate-id(following-sibling::html:h2[1]) = generate-id(current()/html:h2[1])]"/>
	<xsl:apply-templates select="html:h2"/>
</xsl:template>

<xsl:template match="html:h1">
	<xsl:if test="normalize-space(/html:html/html:head/html:title) != normalize-space(text())">
		<xsl:message terminate="no">Les titres dans les balises title et h1 devraient être identiques</xsl:message>
		<title>
			<xsl:call-template name="default.attrs"/>
			<xsl:apply-templates/>
		</title>
	</xsl:if>
</xsl:template>

<xsl:template match="html:h2">
	<section>
		<xsl:apply-templates select="@id"/>
		<title><xsl:apply-templates/></title>
<!-- Merci à Jeni Tennison et aux archives de ml pour le XPath suivant -->
<!-- Les explications sur :
 <http://www.biglist.com/lists/xsl-list/archives/200008/msg01102.html> -->
		<xsl:apply-templates select="following-sibling::*[generate-id(following-sibling::html:h2[1]) = generate-id(current()/following-sibling::html:h2[1])][generate-id(following-sibling::html:h3[1]) = generate-id(current()/following-sibling::html:h3[1])]"/>
		<xsl:apply-templates select="following-sibling::html:h3[generate-id(following-sibling::html:h2[1]) = generate-id(current()/following-sibling::html:h2[1])]"/>
	</section>
</xsl:template>

<xsl:template match="html:h3">
	<section>
		<xsl:apply-templates select="@id"/>
		<title><xsl:apply-templates/></title>
		<xsl:apply-templates select="following-sibling::*[generate-id(following-sibling::html:h2[1]) = generate-id(current()/following-sibling::html:h2[1])][generate-id(following-sibling::html:h3[1]) = generate-id(current()/following-sibling::html:h3[1])][generate-id(following-sibling::html:h4[1]) = generate-id(current()/following-sibling::html:h4[1])]"/>
		<xsl:apply-templates select="following-sibling::html:h4[generate-id(following-sibling::html:h3[1]) = generate-id(current()/following-sibling::html:h3[1])]"/>
	</section>
</xsl:template>

<xsl:template match="html:h4">
	<section>
		<xsl:apply-templates select="@id"/>
		<title><xsl:apply-templates/></title>
		<xsl:apply-templates select="following-sibling::*[generate-id(following-sibling::html:h2[1]) = generate-id(current()/following-sibling::html:h2[1])][generate-id(following-sibling::html:h3[1]) = generate-id(current()/following-sibling::html:h3[1])][generate-id(following-sibling::html:h4[1]) = generate-id(current()/following-sibling::html:h4[1])][generate-id(following-sibling::html:h5[1]) = generate-id(current()/following-sibling::html:h5[1])]"/>
		<xsl:apply-templates select="following-sibling::html:h5[generate-id(following-sibling::html:h4[1]) = generate-id(current()/following-sibling::html:h4[1])]"/>
	</section>
</xsl:template>

<xsl:template match="html:h5">
	<section>
		<xsl:apply-templates select="@id"/>
		<title><xsl:apply-templates/></title>
		<xsl:apply-templates select="following-sibling::*[generate-id(following-sibling::html:h2[1]) = generate-id(current()/following-sibling::html:h2[1])][generate-id(following-sibling::html:h3[1]) = generate-id(current()/following-sibling::html:h3[1])][generate-id(following-sibling::html:h4[1]) = generate-id(current()/following-sibling::html:h4[1])][generate-id(following-sibling::html:h5[1]) = generate-id(current()/following-sibling::html:h5[1])][generate-id(following-sibling::html:h6[1]) = generate-id(current()/following-sibling::html:h6[1])]"/>
		<xsl:apply-templates select="following-sibling::html:h6[generate-id(following-sibling::html:h5[1]) = generate-id(current()/following-sibling::html:h5[1])]"/>
	</section>
</xsl:template>

<xsl:template match="html:h6">
	<section>
		<xsl:apply-templates select="@id"/>
		<title><xsl:apply-templates/></title>
		<xsl:apply-templates select="following-sibling::*[generate-id(following-sibling::html:h2[1]) = generate-id(current()/following-sibling::html:h2[1])][generate-id(following-sibling::html:h3[1]) = generate-id(current()/following-sibling::html:h3[1])][generate-id(following-sibling::html:h4[1]) = generate-id(current()/following-sibling::html:h4[1])][generate-id(following-sibling::html:h5[1]) = generate-id(current()/following-sibling::html:h5[1])][generate-id(following-sibling::html:h6[1]) = generate-id(current()/following-sibling::html:h6[1])]"/>
	</section>
</xsl:template>

<xsl:template match="html:address">
	<address><xsl:apply-templates/></address>
	<xsl:message terminate="no">Attention : vérifier la balise address qui est probablement incorrecte dans la sortie Docbook</xsl:message>
</xsl:template>

<xsl:template match="html:li">
	<listitem>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</listitem>
</xsl:template>

<xsl:template match="html:li/text()">
	<para><xsl:value-of select="."/></para>
</xsl:template>

<xsl:template match="html:ul">
	<itemizedlist>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</itemizedlist>
</xsl:template>

<xsl:template match="html:ol">
	<orderedlist>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</orderedlist>
</xsl:template>

<xsl:template match="html:i|html:em">
	<emphasis>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</emphasis>
</xsl:template>

<xsl:template match="html:b">
	<emphasis role="strong">
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</emphasis>
</xsl:template>

<xsl:template match="html:strong">
	<emphasis role="strong">
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</emphasis>
</xsl:template>

<xsl:template match="html:var">
	<varname>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</varname>
</xsl:template>

<xsl:template match="html:acronym">
	<acronym>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates select="@title"/>
		<xsl:apply-templates/>
	</acronym>
</xsl:template>

<!-- TODO: générer plutôt un glossaire en fin d'article -->
<xsl:template match="html:acronym/@title">
	<xsl:attribute name="role"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="html:cite">
	<citation>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</citation>
</xsl:template>

<xsl:template match="html:dfn">
	<firstterm>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</firstterm>
</xsl:template>

<xsl:template match="html:kbd">
	<userinput>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</userinput>
</xsl:template>

<!-- TODO: faire comme pour acronym ? -->
<xsl:template match="html:abbr">
	<abbrev>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</abbrev>
</xsl:template>

<xsl:template match="html:tt">
	<command>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</command>
</xsl:template>

<xsl:template match="html:p">
	<para>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</para>
</xsl:template>

<xsl:template match="html:pre">
	<programlisting>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</programlisting>
</xsl:template>

<xsl:template match="html:blockquote">
	<blockquote>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</blockquote>
</xsl:template>

<xsl:template match="html:q">
	<quote>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</quote>
</xsl:template>

<xsl:template match="html:span">
	<phrase>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</phrase>
</xsl:template>

<xsl:template match="html:a[@href]">
	<ulink url="{@href}">
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates select="@name"/>
		<xsl:apply-templates select="@title"/>
		<xsl:apply-templates/>
	</ulink>
</xsl:template>

<xsl:template match="html:a[@href][starts-with(@href, '#')]">
	<xref>
		<xsl:call-template name="default.attrs"/>
		<xsl:attribute name="linkend"><xsl:value-of select="substring-after(@href, '#')"/></xsl:attribute>
		<xsl:apply-templates select="@title"/>
	</xref>
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="html:a[not(@href)][@name]">
	<anchor>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates select="@name"/>
		<xsl:apply-templates select="@title"/>
	</anchor>
	<xsl:apply-templates/>
</xsl:template>

<!-- il n'y a pas d'attribut lang sur anchor en Docbook -->
<xsl:template match="html:a[not(@href)][@name]/@lang">
  <xsl:message terminate="no">il n'y a pas d'attribut lang sur anchor en Docbook</xsl:message>
</xsl:template>

<xsl:template match="html:a/@title">
	<xsl:attribute name="role"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="html:a/@name">
	<xsl:attribute name="id"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template match="html:script|html:style|html:base"/>

<xsl:template match="html:noscript"><xsl:apply-templates/></xsl:template>

<xsl:template match="html:big">
	<emphasis><xsl:apply-templates/></emphasis>
</xsl:template>

<xsl:template match="html:small">
	<xsl:apply-templates/>
	<xsl:message terminate="no">Balise small non prise en compte</xsl:message>
</xsl:template>

<xsl:template match="html:code">
	<token>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</token>
</xsl:template>

<xsl:template match="html:samp">
	<replaceable>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</replaceable>
</xsl:template>

<xsl:template match="html:p//html:img">
	<inlinemediaobject>
		<imageobject><imagedata fileref="{@src}"/></imageobject>
		<textobject><phrase><xsl:value-of select="@alt"/></phrase></textobject>
	</inlinemediaobject>
</xsl:template>

<xsl:template match="html:img">
	<mediaobject>
		<xsl:call-template name="default.attrs"/>
		<imageobject><imagedata fileref="{@src}"/></imageobject>
		<textobject><phrase><xsl:value-of select="@alt"/></phrase></textobject>
	</mediaobject>
</xsl:template>

<xsl:template match="html:object">
	<inlinemediaobject>
		<xsl:apply-templates/>
	</inlinemediaobject>
</xsl:template>

<xsl:template match="html:param"></xsl:template>
<xsl:template match="html:br"></xsl:template>
<xsl:template match="html:map"></xsl:template>
<xsl:template match="html:area"></xsl:template>

<xsl:template match="html:sub">
	<subscript>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</subscript>
</xsl:template>

<xsl:template match="html:sup">
	<superscript>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</superscript>
</xsl:template>

<xsl:template match="html:bdo"/>
<xsl:template match="html:form"/>
<xsl:template match="html:input"/>
<xsl:template match="html:select"/>
<xsl:template match="html:optgroup"/>
<xsl:template match="html:option"/>
<xsl:template match="html:textarea"/>
<xsl:template match="html:label"/>
<xsl:template match="html:button"/>
<xsl:template match="html:fieldset"/>
<xsl:template match="html:legend"/>
<xsl:template match="html:hr"/>

<xsl:template match="html:dl">
	<variablelist>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates select="html:dt"/>
	</variablelist>
</xsl:template>

<xsl:template match="html:dt">
	<varlistentry>
		<term>
			<xsl:call-template name="default.attrs"/>
			<xsl:apply-templates/>
		</term>
		<xsl:apply-templates select="following-sibling::html:dd[generate-id(following-sibling::html:dt[1]) = generate-id(current()/following-sibling::html:dt[1])]"/>
	</varlistentry>
</xsl:template>

<xsl:template match="html:dd">
	<listitem>
		<xsl:apply-templates/>
	</listitem>
</xsl:template>

<!-- TODO: vérifier -->
<xsl:template match="html:dd/*[@local-name != 'p']">
	<para><xsl:apply-templates/></para>
</xsl:template>

<xsl:template match="html:div">
	<xsl:call-template name="default.attrs"/>
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="html:hr"/>

<xsl:template match="html:table">
  <table>
    <xsl:apply-templates select="html:caption"/>
    <xsl:apply-templates select="@summary"/>
    <tgroup>
<!-- La suite calcule le nombre de colonnes dans le tableau, comme précisé
     sur http://www.w3.org/TR/html4/struct/tables.html#h-11.2.4.3 -->
      <xsl:attribute name="cols">
        <xsl:choose>
          <xsl:when test="html:col or html:colgroup">
            <xsl:value-of select="sum(html:col[@span]/@span)+count(html:col[not(@span)])+sum(html:colgroup[not(html:col)]/@span)+sum(html:colgroup/html:col[@span]/@span)+sum(html:colgroup/html:col[not(@span)])"/>
          </xsl:when>
          <xsl:otherwise>
<!-- Repris d'Alain Ketterlin sur fr.comp.text.xml
     <wywutprdkb.fsf@polya.u-strasbg.fr>. Magistral :-) -->
            <xsl:for-each select="html:tbody/html:tr">
<!--<xsl:sort select="count(html:td[not(@colspan) or @colspan = 0])+sum(html:td[@colspan]/@colspan)+sum(preceding-sibling::html:tr/html:td[@rowspan][@colspan][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0]/@colspan)+count(preceding-sibling::html:tr/html:td[@rowspan][not(@colspan) or @colspan = 0][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0])" data-type="number" order="descending"/>--><xsl:value-of select="count(html:td[not(@colspan) or @colspan = 0])+sum(html:td[@colspan]/@colspan)+sum(preceding-sibling::html:tr/html:td[@rowspan][@colspan][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0]/@colspan)+count(preceding-sibling::html:tr/html:td[@rowspan][not(@colspan) or @colspan = 0][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0])"/>-<!--
              <xsl:sort select="count(html:td[not(@colspan) or @colspan = 0])+sum(html:td[@colspan]/@colspan)+sum(preceding-sibling::html:tr/html:td[@rowspan][@colspan][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0]/@colspan)+count(preceding-sibling::html:tr/html:td[@rowspan][not(@colspan) or @colspan = 0][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0])" data-type="number" order="descending"/><xsl:value-of select="count(html:td)"/>/<xsl:value-of select="count(html:td[not(@colspan) or @colspan = 0])+sum(html:td[@colspan]/@colspan)+sum(preceding-sibling::html:tr/html:td[@rowspan][@colspan][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0]/@colspan)+count(preceding-sibling::html:tr/html:td[@rowspan][not(@colspan) or @colspan = 0][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0])"/>-<xsl:if test="position()=1">(<xsl:value-of select="count(html:td[not(@colspan) or @colspan = 0])+sum(html:td[@colspan]/@colspan)+sum(preceding-sibling::html:tr/html:td[@rowspan][@colspan][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0]/@colspan)+count(preceding-sibling::html:tr/html:td[@rowspan][not(@colspan) or @colspan = 0][count(current()/preceding-sibling::*)-count(preceding-sibling::*)+1 &lt;= @rowspan or @rowspan = 0])"/>)</xsl:if>-->
            </xsl:for-each>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:attribute>
      <xsl:apply-templates select="child::node()[local-name()!='caption']"/>
    </tgroup>
  </table>
</xsl:template>

<xsl:template match="html:caption">
	<title>
		<xsl:call-template name="default.attrs"/>
		<xsl:value-of select="text()"/>
	</title>
</xsl:template>

<xsl:template match="html:table/@summary">
	<textobject><phrase><xsl:value-of select="."/></phrase></textobject>
</xsl:template>

<xsl:template match="html:tr">
	<row>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</row>
</xsl:template>

<xsl:template match="html:th|html:td">
	<entry>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</entry>
</xsl:template>

<xsl:template match="html:thead">
	<thead>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</thead>
</xsl:template>

<xsl:template match="html:tbody">
	<tbody>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</tbody>
</xsl:template>

<xsl:template match="html:tfoot">
	<tfoot>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</tfoot>
</xsl:template>

<xsl:template match="html:colgroup"/>

<xsl:template match="html:col">
	<colspec/>
</xsl:template>

<xsl:template match="html:link"/>
<xsl:template match="html:ins"/>
<xsl:template match="html:del"/>

</xsl:stylesheet>
