<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="/conversation">
		<html>
			<head>
				<title>
					<xsl:apply-templates select="@title" />
				</title>
				<style type="text/css"><![CDATA[
body {
    margin: auto;
    max-width: 120ch;
}

* {
    font-size: 1em;
    font-family: ui-sans-serif, -apple-system, system-ui, Segoe UI,
        Helvetica, Apple Color Emoji, Arial, sans-serif, Segoe UI Emoji,
        Segoe UI Symbol;
}

article {
    margin: 2em 0;
}

article[data-speaker="assistant"] {
    padding-right: 20ch;
}

article[data-speaker="user"] {
    padding-left: 20ch;
}

article>div {
    border: 1px solid rgba(0, 0, 0, 0.5);
    padding: 0 1rem;
}

article[data-speaker="assistant"]>* {
    border-radius: 0 1rem 1rem 0;
    background-color: #efe;
}

article[data-speaker="user"]>* {
    border-radius: 0 1rem 0 1rem;
    background-color: #eef;
}

h2, h3, p, ul, ol {
    margin: 0.5rem 0;
}

p {
    white-space: pre-wrap;
    line-height: 1.5em;
}

body>h1 {
    font-size: 2rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.25);
}

article>h2 {
    font-size: 1.25rem;
    background-color: #ffe;
    border: 1px solid rgba(0, 0, 0, 0.5);
    border-bottom: none;
    border-radius: 1rem 1rem 0 0 !important;
    margin: 0;
    display: inline-block;
    padding: 0.5rem 1.5rem 0.25rem 1rem;
}

time {
    font-size: 1rem;
    padding-left: 1em;
    font-family: monospace;
}

pre, pre * {
    font-family: monospace;
}

pre {
    background: white;
    padding: 0.5rem;
    overflow: visible;
    width: fit-content;
    border: 1px solid rgba(0, 0, 0, 0.25);
}

h1, h2, h3, strong {
    font-weight: bold;
}
				]]></style>
			</head>
			<body>
				<h1>
					<span>
						<xsl:value-of select="@title" />
					</span>
					<time>
						<xsl:value-of select="@datetime" />
					</time>
				</h1>
				<xsl:apply-templates select="message" />
			</body>
		</html>
	</xsl:template>

	<xsl:template match="message">
		<article data-speaker="{@speaker-role}">
			<h2>
				<span>
					<xsl:value-of select="@speaker-name" />
				</span>
				<time>
					<xsl:value-of select="@datetime" />
				</time>
			</h2>
			<xsl:copy-of select="*" />
		</article>
	</xsl:template>
</xsl:stylesheet>
