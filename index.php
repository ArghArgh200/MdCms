<?php
/**
 * index.php
 *
 * @package default
 */


// MdCms by Arghlex - Credits to John Gruber and Michel Fortin for their Markdown translators
//  and the Bootstrap and jQuery project teams for also being awesome at their jobs/free-time development.

// Lines of confusing HTML and PHP by Arghlex M.

// Configuration
// See README.md, it'll make more sense

spl_autoload_register(function($class) {
		require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, "php-markdown/".ltrim($class, '\\')).'.php'; // who does this
	});

use \Michelf\Markdown;



/**
 * Generate the menu
 *
 * @param unknown $menu
 * @return unknown
 */
function generateMenu() {
	$menu = json_decode(file_get_contents("md/settings.json"), true);
	$htmlmenu=''; //Set an empty variable so we can add things to it.
	foreach ($menu as $menuentry) {  // iterate through each menu's entry
		if (is_array($menuentry)){
			if ($menuentry['type'] == "php" || $menuentry['type'] == "md" ) { //If it's an MD or PHP script, link to it.
				global $request;
				if($request==$menuentry['id']){
					$htmlmenu=$htmlmenu.'<li class=active><a href="/?page='.$menuentry['id'].'">'.$menuentry['name'].'</a></li>';
				}else{
					$htmlmenu=$htmlmenu.'<li><a href="/?page='.$menuentry['id'].'">'.$menuentry['name'].'</a></li>';
				}
			} else { // Must be a link to something. Embed it.
				$htmlmenu=$htmlmenu.'<li><a href="'.$menuentry['type'].'">'.$menuentry['name'].'</a></li>';
			}
		}
	}
	return $htmlmenu;
}


/**
 * grabs and HTML-ifies the specified (or default) MD, or runs a PHP file, or displays a 404 error message.
 *
 * @return unknown
 */
function generateBody() {
	global $main_directory;
	global $request; //we'll be using this in the menu function
	if (!isset($_GET['page'])) { //Is the user even requesting a specific page?
		$parser=new Markdown;
		$text=file_get_contents("md/index.md");
		$text=$parser->defaultTransform($text);
	}else { //User is requesting a page.
		$request=filter_var($_GET['page'], FILTER_SANITIZE_STRING, FILTER_SANITIZE_SPECIAL_CHARS);
		// Sanitization of things to prevent people stealing files they shouldn't or things like that
		$text=@file_get_contents("md/".$request.".md");
		if ($text === FALSE) {
			// No .md found, look for a PHP script with the same name.
			$text=include_once "md/php/".$request.".php";
			// Still nothing? Show a 404.
			if ($text === FALSE) {$text="<h1 color='red'>404 - Page Not Found.";}
		}else { // We found a Markdown page. Time to translate it.
			$parser=new Markdown;
			$text = $parser->defaultTransform($text);
		}
	}
	return $text; //returns the md's HTML-ified contents, a 404, or the index page's markdown, or a PHP script's HTML output, ready for embedding with the template.
}


/**
 * Actually renders the page
 *
 * @param unknown $menu
 * @param unknown $menuhtml
 * @param unknown $bodyhtml
 * @return unknown
 */
function render($menuhtml, $bodyhtml) {
	$template = file_get_contents("template.html"); //load our template
	//make sure we HAVE a template
	if ($template === FALSE ) {die("<h1 color=red>ERROR: Site administrator has not set a page template. Consult MdCms's documentation for more.</h1>");}
	$template=str_replace("MDCMS_SITENAME", file_get_contents("sitename.txt"), $template);  //replace the parts of the site that need it
	$template=str_replace("MDCMS_MENU", $menuhtml, $template);
	$template=str_replace("MDCMS_CONTENT", $bodyhtml, $template);
	return $template;
}


$bodyhtml=generateBody();
$menuhtml=generateMenu(); //needs to happen in this order so don't change it
die(render($menuhtml, $bodyhtml));
?>
