<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:html="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="/report">
		<html>
			<head>
			</head>
			<body>
				<table border="1">
					<thead>
						<tr>
							<th>Name</th>
							<th>Testat 01</th>
							<th>Testat 02</th>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="repository">
							<xsl:variable name="suits" select=".//test-suite[@type='TestFixture'][contains(@name, 'Testat')]"/>
							<tr>
								<td><a href="{@href}"><xsl:value-of select="@name"/></a></td>
								<xsl:for-each select="$suits">
									<xsl:variable name="cases" select=".//test-case"/>
									<td>
										<xsl:value-of select="count($cases[@result='Passed'])"/>
										<xsl:text>/</xsl:text>
										<xsl:value-of select="count($cases)"/>
										<ul>
											<xsl:for-each select="self::*[@runstate='NotRunnable']/failure | $cases/failure">
												<li><pre><xsl:value-of select="message"/></pre></li>
											</xsl:for-each>
										</ul>
									</td>
								</xsl:for-each>
							</tr>
						</xsl:for-each>
					</tbody>
				</table>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
