<?php
/**
 * index.php
 *
 * @package default
 */


// MdCms by Arghlex - Credits to John Gruber and Michel Fortin for their Markdown translators
// Lines of confusing HTML and PHP by Arghlex

// Configuration
// Open up md/settings.json, and edit that. Make sure your syntax is correct or the site WILL crash!
// Helpful JSON syntax hints: the last entry in an array will not have a comma after it. The others will.

// End configuration. Don't touch anything below this line unless you know what you're doing.

spl_autoload_register(function($class) {
		require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, "php-markdown/".ltrim($class, '\\')).'.php'; //Seriously, though. This is the dumbest way to load a class I've ever seen. And I've seen some shit.
	});

use \Michelf\Markdown;

$menu = json_decode(file_get_contents("md/settings.json"), true);


/**
 *
 * @param string $menu
 * @return string
 */
function generateMenu($menu) {
	//Generates the menu of pages.
	$htmlmenu=''; //Set an empty variable so we can add thigns to it.
	foreach ($menu as $menuentry) {  // iterate through each menu's entry
		if (is_array($menuentry) && $menuentry['type'] == "md" ) { //If it's an MD, link to it.
			$htmlmenu=$htmlmenu.'<li><a href="/?page='.$menuentry['id'].'">'.$menuentry['name'].'</a></li>';
		}else { // if it's not, use a link instead. Check for the site name though!
			if (is_array($menuentry) && $menuentry['type'] != $menu['sitename'][0]) {
				$htmlmenu=$htmlmenu.'<li><a href="'.$menuentry['type'].'">'.$menuentry['name'].'</a></li>';
			}
		}
	}
	return $htmlmenu;
}


/**
 *
 * @return string
 */
function pickPage() { //Checks for the 'page' GET variable, and if the apge it specifies exists, grab the file. If it doesnt, get a 404 page. If it's not set, show the index.md page.
	global $main_directory;
	global $menu;
	if (!isset($_GET['page'])) { //Varaible not set? show index page.
		$text=file_get_contents("md/index.md");
	}else { //variable IS set, attempt to show corresponding page.
		$request=filter_var($_GET['page'], FILTER_SANITIZE_STRING, FILTER_SANITIZE_SPECIAL_CHARS);
		$text=file_get_contents("md/".$request.".md");
		if ($text === FALSE) {
			$text="404 Not Found.
========";
		}
	}
	return $text; //returns the md's contents (or the index, or a 404 message), ready for parsing.
}


/**
 *
 * @return string
 */
function generateBody() {
	global $menu;
	global $main_directory;
	// Actually translate the .md to HTML.
	$text = pickPage();
	$parser=new Markdown;
	$html = $parser->defaultTransform($text);
	return $html;
}


/**
 *
 * @param string $htmlmenu
 * @param string $html
 * @return string
 */
function render($params) { //Actually makes the page, and bootstraps it.

	// Make variables available in 'template'.
	foreach ($params as $name => $param) {
		$varName = "tplvar_{$name}";
		$$varName = $param;
	}

	return <<<'EOT'
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{$tplvar_sitename}</title>
		<link href="/resources/bootstrap.css" rel="stylesheet">
		<style>
			body {
				padding-top: 50px;
				background-color: transparent;
			}
			.mainbody {
				padding: 40px 15px;
				text-align: center;
				background-color: transparent;
			}
		</style>
		<link href="/style.css" rel="stylesheet">
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
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
					<a class="navbar-brand" href="/">{$tplvar_sitename}</a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						{$tplvar_menu}
					</ul>
				</div>
				<!--/.nav-collapse -->
			</div>
		</div>
		<div class="container">
			<div class="mainbody">
				<div align="left">
					{$tplvar_pagecontent}
				</div>
			</div>
		</div>
		<script src="resources/jquery.js"></script>
		<script src="resources/bootstrap.js"></script>
	</body>
</html>
EOT;
}

$params = array();
$params['sitename'] = $menu['sitename'];
$params['menu'] = generateMenu($menu);
$params['pagecontent'] = generateBody();

die(render($params));
