<?php
//MdCms Blog Module - part one of two.
//this script grabs all the blog posts from MdCms's md/blog/*.md pages and displays them with their data. filenames will be in unixtime so that the program displays them in chronological order.

//this again
spl_autoload_register(function($class) {
		require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, "php-markdown/".ltrim($class, '\\')).'.php';
	});
use \Michelf\Markdown;
$blogsettings=json_decode(file_get_contents("md/settings.json"), true);
if (!$blogsettings){die("Blog's not ready for users!");}else{
	$text="###".$blogsettings["name"]."\n\n####".$blogsettings["comment"]."\n\n";
	//blog header in markdown
	foreach (glob("md/blog/*.md") as $file) {
		$text=$text."\n\n<pre>\n\n"; //separate them so it doesn't look like a giant post
		$text=$text.@file_get_contents($file); //read ALL the posts!
		$text=$text."\n\n</pre>\n\n"; //"what is this, a post for giants?"
	}

	//then HTML-ify them
	$parser=new Markdown;
	$text = $parser->defaultTransform($text);
	print($text);
}
?>