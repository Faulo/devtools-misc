<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:html="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="/report">
		<html>
			<head>
			</head>
			<body>
				<table border="1">
					<tbody>
						<xsl:for-each select="repository">
							<xsl:variable name="suits" select=".//test-suite[@type='TestFixture'][contains(@name, 'Testat')]"/>
							<xsl:variable name="cases" select="$suits/test-case"/>
							<tr>
								<td><a href="{@href}"><xsl:value-of select="@name"/></a></td>
								<td>
									<xsl:value-of select="count($cases[@result='Passed'])"/>
									<xsl:text>/</xsl:text>
									<xsl:value-of select="count($cases)"/>
								</td>
								<td>
									<ul>
										<xsl:for-each select="$suits[@runstate='NotRunnable']/failure | $cases/failure">
											<li><pre><xsl:value-of select="message"/></pre></li>
										</xsl:for-each>
									</ul>
								</td>
							</tr>
						</xsl:for-each>
					</tbody>
				</table>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
