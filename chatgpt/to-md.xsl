<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="text" />

	<xsl:variable name="eol">
		<xsl:text>
</xsl:text>
	</xsl:variable>

	<xsl:template match="/conversation">
		<xsl:value-of select="concat('# ', @title, $eol, $eol)" />

		<xsl:apply-templates select="message" />
	</xsl:template>

	<xsl:template match="message">
		<xsl:value-of select="concat('## [', @datetime, '] ', @speaker-name, $eol)" />
		<xsl:value-of select="@text" />
		<xsl:value-of select="concat($eol, $eol, $eol)" />
	</xsl:template>
</xsl:stylesheet>
