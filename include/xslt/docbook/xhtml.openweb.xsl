<?xml version="1.0" encoding="iso-8859-15"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:html="http://www.w3.org/1999/xhtml" version="1.0"
  exclude-result-prefixes="html">

<xsl:include href="xhtml.xsl"/>

<xsl:template match="html:head">
  <articleinfo>
    <xsl:apply-templates select="/html:html/html:body/html:h2"/>
    <xsl:apply-templates select="child::node()[local-name()!='meta' or (@name!='DC.Resource Type' and @name!='DC.Subject' and @name!='DC.Audience')]"/>
    <subjectset>
      <xsl:apply-templates select="html:meta[@name='DC.Resource Type' or @name='DC.Subject' or @name='DC.Audience']"/>
    </subjectset>
  </articleinfo>
</xsl:template>

<xsl:template match="html:dl[@class='interview']">
	<qandaset>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates select="html:dt"/>
	</qandaset>
</xsl:template>

<xsl:template match="html:dl[@class='interview']/html:dt">
	<qandaentry>
		<question>
			<xsl:call-template name="default.attrs"/>
			<para>
				<xsl:apply-templates/>
			</para>
		</question>
		<xsl:apply-templates select="following-sibling::html:dd[generate-id(following-sibling::html:dt[1]) = generate-id(current()/following-sibling::html:dt[1])]"/>
	</qandaentry>
</xsl:template>

<xsl:template match="html:dl[@class='interview']/html:dd">
	<answer>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</answer>
</xsl:template>

<xsl:template match="html:div[@class='note']">
	<note>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</note>
</xsl:template>

<xsl:template match="html:div[@class='important']">
	<important>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</important>
</xsl:template>

<xsl:template match="html:div[@class='attention']">
	<caution>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</caution>
</xsl:template>

<xsl:template match="html:div[@class='astuce']">
	<tip>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</tip>
</xsl:template>

<xsl:template match="html:div[@class='avertissement']">
	<warning>
		<xsl:call-template name="default.attrs"/>
		<xsl:apply-templates/>
	</warning>
</xsl:template>

<!-- On ignore le tout premier <strong> -->
<xsl:template match="html:div[@class='avertissement' or @class='astuce' or @class='attention' or @class='important' or @class='note']/strong[1]"></xsl:template>

</xsl:stylesheet>
