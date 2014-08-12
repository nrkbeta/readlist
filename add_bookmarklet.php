<?php
require_once("./readlist.php");
?><!DOCTYPE html>
<html>
<head>
	<title>Readlist bookmarklet generator</title>
	<style type="text/css">
		body {
			font-family: helvetica, arial;
		}
		a {
			text-decoration: none;
			padding: 10px;
			border-radius: 10px;
			background: #eee;
			font-weight: bold;
			color: #333;
			text-transform: uppercase;
			border: 1px solid #ddd;
		}
	</style>
</head>
<body>
<h1>Readlist bookmarklet generator</h1>
<p style="font-weight: bold;">NOTE: This page will be removed if you refresh the window. Be sure to save the bookmarklet now.</p>
<p>To be able to post links to your site from your browser, drag this button to your bookmarks bar:</p>

<p style="margin-top: 30px;"><a href="BOOKURL">Add to Readlist</a></p>

<p style="margin-top: 30px;">If you are on a phone:</p>
<ol>
	<li>Bookmark this page</li>
	<li>Kopier this text:<br><code>BOOKURL</code></li>
	<li>Edit the bookmark you just created, and change the URL to the text you just copied.</li>
</ol>
</body>
</html>

