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
// See README.md, it'll make more sense in its own document.

spl_autoload_register(function($class) {
		require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, "php-markdown/".ltrim($class, '\\')).'.php'; // who does this
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
function generateBody() { // grabs and HTML-ifies the specified (or default) MD, or runs a PHP file, or displays a 404 error message.
	global $main_directory;
	global $menu;
	if (!isset($_GET['page'])) { //Varaible not set? show index page.
		$parser=new Markdown;
		$text=file_get_contents("md/index.md");
		$text=$parser->defaultTransform($text);
	}else { //variable IS set, look for a static markdown file to render.
		$request=filter_var($_GET['page'], FILTER_SANITIZE_STRING, FILTER_SANITIZE_SPECIAL_CHARS);
		$text=file_get_contents("md/".$request.".md");
		if ($text === FALSE) {
			//try looking for a PHP page.
			$text=include_once("md/php/".$request.".php");
			if ($text === FALSE) {$text="<h1 color='red'>404 - Page Not Found.";}
		}else{ //we have a markdown page, translate it.
			$parser=new Markdown;
			$text = $parser->defaultTransform($text);
		}
	}
	return $text; //returns the md's HTML-ified contents, a 404, or the index markdown, or a PHP script's HTML output, ready for embedding with the template.
}


/**
 *
 * @param string $htmlmenu
 * @param string $html
 * @return string
 */
function render($params) { //Actually makes the page, and adds the template.

	// Make variables available in 'template'.
	foreach ($params as $name => $param) {
		$varName = "tplvar_{$name}";
		$$varName = $param;
	}
	
	$template = file_get_contents("template.html");
	if ($template === FALSE ){die("<h1 color=red>ERROR: Site administrator has not set a page template. Consult MdCms's documentation for more.</h1>");}
	$template=str_replace("MDCMS_SITENAME",$tplvar_sitename,$template);
	$template=str_replace("MDCMS_MENU",$tplvar_menu,$template);
	$template=str_replace("MDCMS_CONTENT",$tplvar_content,$template);
	return $template;
}

$params = array();
$params['sitename'] = $menu['sitename'];
$params['menu'] = generateMenu($menu);
$params['content'] = generateBody();

die(render($params));
