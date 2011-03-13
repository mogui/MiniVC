<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>/** block title **/<?php echo $context->title; ?>/** endblock **/</title>
	
</head>
	
<body>
	<h1><a href="/">MiniVC</a></h1>
	<div style="background:yellow;padding:15px;">
	/** block content **/
		<h2>Hello World!</h2>
		<p><a href="/extends/bar/">Extends Bar</a></p>
		<p><a href="/extends/rock/">Extends Rock</a></p>
	/** endblock **/
	</div>
</body>
</html>
