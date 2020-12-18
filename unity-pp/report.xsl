<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:html="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:variable name="ids" select="//test-id" />

	<xsl:template match="/report">
		<html>
			<head>
				<style type="text/css"><![CDATA[
dl {
	white-space: pre-wrap;
	font-family: monospace;
	margin: 0 0 0 0;
}
dt {
	font-weight: bold;
	margin: 0 0 0 0;
}
dd {
	width: 80ch;
	margin: 0 0 0 0;
	border-top: 1px solid gray;
	padding: 0.5em 0;
}

.summary {
	text-align: right;
	font-family: monospace;
	padding-right: 1em;
}
.success {
	background-color: rgba(0, 255, 0, 0.1);
}
.failure{
	background-color: rgba(255, 0, 0, 0.1);
}
table {
	margin: 1em auto;
}
th:nth-child(1) {
    width: 10em;
}
th:nth-child(2) {
    width: 15em;
}
th {
    width: 5em;
}
				
				]]></style>
			</head>
			<body>
				<xsl:call-template name="table">
					<xsl:with-param name="repositories" select="repository[@master]" />
				</xsl:call-template>
				<xsl:call-template name="table">
					<xsl:with-param name="repositories" select="repository[not(@master)]" />
				</xsl:call-template>
				<hr/>
				<xsl:for-each select="duplicate[not(file/@author = 'Faulo-Master')]">
					<details>
						<summary><xsl:value-of select="count(file)"/> duplicates: <xsl:value-of select="file/@location"/></summary>
						<dl>
							<xsl:for-each select="file">
								<dt><xsl:value-of select="@author"/></dt>
								<dd><xsl:value-of select="@location"/></dd>
							</xsl:for-each>
						</dl>
						<pre><xsl:value-of select="@content"/></pre>
					</details>
				</xsl:for-each>
			</body>
		</html>
	</xsl:template>

	<xsl:template name="table">
		<xsl:param name="repositories" />

		<table border="1">
			<thead>
				<tr>
					<th>Repository</th>
					<th>Author</th>
					<xsl:for-each select="$ids">
						<th>
							<xsl:value-of select="." />
						</th>
					</xsl:for-each>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th/>
					<th>Total</th>
					<xsl:for-each select="$ids">
						<xsl:variable name="suits" select="$repositories//test-suite[@name = current()]" />
						<xsl:variable name="summary">
							<xsl:value-of select="count($suits[not(.//test-case/@result != 'Passed')])" />
							<xsl:text>/</xsl:text>
							<xsl:value-of select="count($suits)" />
						</xsl:variable>
						<td>
							<div class="summary">
								<xsl:value-of select="$summary" />
							</div>
						</td>
					</xsl:for-each>
				</tr>
			</tfoot>
			<tbody>
				<xsl:apply-templates select="$repositories">
					<xsl:sort select="@company"/>
				</xsl:apply-templates>
			</tbody>
		</table>
	</xsl:template>

	<xsl:template match="repository">
		<xsl:variable name="suits" select=".//test-suite" />
		<tr>
			<td>
				<a href="{@href}">
					<xsl:value-of select="@name" />
				</a>
			</td>
			<td>
				<xsl:value-of select="@company" />
			</td>
			<xsl:for-each select="$ids">
				<td>
					<xsl:for-each select="$suits[@name = current()]">
						<xsl:variable name="cases" select=".//test-case" />
						<xsl:variable name="failures" select="self::*[@runstate='NotRunnable']/failure | $cases/failure" />
						<xsl:variable name="passedCount" select="count($cases[@result='Passed'])" />
						<xsl:variable name="totalCount" select="count($cases)" />
						<xsl:variable name="success" select="$passedCount = $totalCount and not($failures)" />
						<xsl:variable name="summary">
							<xsl:value-of select="count($cases[@result='Passed'])" />
							<xsl:text>/</xsl:text>
							<xsl:value-of select="count($cases)" />
						</xsl:variable>
						<xsl:choose>
							<xsl:when test="$success">
								<xsl:attribute name="class">success</xsl:attribute>
								<div class="summary">
									<xsl:value-of select="$summary" />
								</div>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="class">failure</xsl:attribute>
								<details>
									<summary class="summary">
										<xsl:value-of select="$summary" />
									</summary>
									<dl>
										<xsl:for-each select="$failures">
											<dt>
												<xsl:value-of select="ancestor::test-case/@name"/>
											</dt>
											<dd>
												<xsl:value-of select="message" />
											</dd>
										</xsl:for-each>
									</dl>
								</details>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</td>
			</xsl:for-each>
		</tr>
	</xsl:template>
</xsl:stylesheet>
