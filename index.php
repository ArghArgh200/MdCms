<?php
// MdCms by Arghlex - Credits to John Gruber and Michel Fortin for their Markdown translators
// Lines of confusing HTML and PHP by Arghlex

// Configuration
// Open up md/settings.json, and edit that. Make sure your syntax is correct or the site WILL crash!
// Helpful JSON syntax hints: the last entry in an array will not have a comma after it. The others will.

// End configuration. Don't touch anything below this line unless you know what you're doing.
spl_autoload_register(function($class){
        require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, "php-markdown/".ltrim($class, '\\')).'.php'; //Seriously, though. This is the dumbest way to load a class I've ever seen. And I've seen some shit.
});
use \Michelf\Markdown;

$menu = json_decode(file_get_contents("md/settings.json"),true);
function generateMenu($menu){ 
//Generates the menu of pages.
	$htmlmenu=''; //Set an empty variable so we can add thigns to it.
	foreach ($menu as $menuentry){  // iterate through each menu's entry
		if (is_array($menuentry) && $menuentry['type'] == "md" ){ //If it's an MD, link to it.
			$htmlmenu=$htmlmenu.'<li><a href="/?page='.$menuentry['id'].'">'.$menuentry['name'].'</a></li>';
		}else{ // if it's not, use a link instead. Check for the site name though!
			if (is_array($menuentry) && $menuentry['type'] != $menu['sitename'][0]){
				$htmlmenu=$htmlmenu.'<li><a href="'.$menuentry['type'].'">'.$menuentry['name'].'</a></li>';
			}
		}
	}
	return $htmlmenu;
}

function pickPage() { //Checks for the 'page' GET variable, and if the apge it specifies exists, grab the file. If it doesnt, get a 404 page. If it's not set, show the index.md page.
	global $main_directory;
	global $menu;
	if (!isset($_GET['page'])){ //Varaible not set? show index page.
		$text=file_get_contents("md/index.md");
	}else{ //variable IS set, attempt to show corresponding page.
		$request=filter_var($_GET['page'], FILTER_SANITIZE_STRING,FILTER_SANITIZE_SPECIAL_CHARS);
		$text=file_get_contents("md/".$request.".md");
		if ($text === FALSE) {
			$text="404 Not Found.
========";
		}
	}
	return $text; //returns the md's contents (or the index, or a 404 message), ready for parsing.
}

function generateBody() {
	global $menu;
	global $main_directory;
// Actually translate the .md to HTML.
	$text = pickPage();
	$parser=new Markdown;
	$html = $parser->defaultTransform($text);
	return $html;
}

function generatePage($htmlmenu,$html) { //Actually makes the page, and bootstraps it.
	global $menu;
	global $main_directory;
	// cue ugly lines of bad HTML embedding and whatever. Sorry, HTML devs. Best I could make it look.
	$htmldoc='<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' .$menu['sitename']. '</title>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
<style>body { padding-top: 50px; } .mainbody { padding: 40px 15px; text-align: center; }</style>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
<span class="icon-bar">
</span>
<span class="icon-bar">
</span>
<span class="icon-bar">
</span>
</button>
<a class="navbar-brand" href="/">'.$menu['sitename'].'</a>
</div>
<div class="collapse navbar-collapse">
<ul class="nav navbar-nav">
'.$htmlmenu.'
</div>
<!--/.nav-collapse -->
</div>
</div>
<div class="container">
<div class="mainbody">
<div align="left">
'.$html.'
</div>
</div>
<script src="//code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
</body>
</html>';
return $htmldoc;
}
//Hardcoded menu entries are OK. Look where it says '.$menu.' and after that, preferably on a new line, add <li><a href="http://example.com">Your Link</a></li>. Probably better to use the JSON or variables though.
//<a class="navbar-brand" href="/">Example Page</a>  This is the big button that sits on the left, and is basically your webpage's title.

$htmlmenu=generateMenu($menu); //Generate menu and store it in a variable
$htmlbody=generateBody(); // grab md and convert it to HTML, storing it in a variable.
$htmlpage=generatePage($htmlmenu,$htmlbody); // Take menu and body vars and stick them in the HTML
echo $htmlpage; //Print the page. Shiny.
