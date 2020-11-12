<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:html="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="/report">
		<xsl:variable name="ids" select="test-id"/>
		<html>
			<head>
				<style type="text/css"><![CDATA[
pre {
	width: 80ch;
	white-space: pre-wrap;
	border-top: 1px solid gray;
	margin: 0 0 0 0;
	padding: 0.5em 0;
}
.summary {
	text-align: right;
	font-family: monospace;
	margin-right: 1em;
}
.success {
	background-color: rgba(0, 255, 0, 0.1);
}
.failure{
	background-color: rgba(255, 0, 0, 0.1);
}
				
				]]></style>
			</head>
			<body>
				<table border="1">
					<thead>
						<tr>
							<th>Name</th>
							<xsl:for-each select="$ids">
								<th><xsl:value-of select="."/></th>
							</xsl:for-each>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="repository">
							<xsl:variable name="suits" select=".//test-suite"/>
							<tr>
								<td><a href="{@href}"><xsl:value-of select="@name"/></a></td>
								<xsl:for-each select="$ids">
									<td>
									<xsl:for-each select="$suits[@name = current()]">
										<xsl:variable name="cases" select=".//test-case"/>
										<xsl:variable name="failures" select="self::*[@runstate='NotRunnable']/failure | $cases/failure"/>
										<xsl:variable name="passedCount" select="count($cases[@result='Passed'])"/>
										<xsl:variable name="totalCount" select="count($cases)"/>
										<xsl:variable name="success" select="$passedCount = $totalCount and not($failures)"/>
										<xsl:variable name="summary">
											<xsl:value-of select="count($cases[@result='Passed'])"/>
											<xsl:text>/</xsl:text>
											<xsl:value-of select="count($cases)"/>
										</xsl:variable>
										<xsl:choose>
											<xsl:when test="$success">
												<xsl:attribute name="class">success</xsl:attribute>
												<div class="summary"><xsl:value-of select="$summary"/></div>
											</xsl:when>
											<xsl:otherwise>
												<xsl:attribute name="class">failure</xsl:attribute>
												<details>
													<summary class="summary"><xsl:value-of select="$summary"/></summary>
													<xsl:for-each select="$failures">
														<pre><xsl:value-of select="message"/></pre>
													</xsl:for-each>
												</details>
											</xsl:otherwise>
										</xsl:choose>
											
									</xsl:for-each>
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
