MdCms
=====

It's a Content Management System that uses Markdown to display the pages.
 It's easily extensible and doesn't require you to have a web designer, rocket scientist or heart surgeon to do it for you.
 
Cloning
-----------
Believe it or not, you'll need to clone it.

 
Configuration
-----------
First, edit settings.json:


	{
	//These entries are the three types of menu entries you can have.
	"example":{"name":"Example Page","id":"example","type":"md"},
	// Loads and translates a markdown page from ./md/example.md to HTML and prints it out in the page body.
	"dynamicexample":{"name":"Example PHP-generated Page","id":"dynamicexample","type":"php"},
	// Runs a PHP script and returns its contents as the site body.
	"examplelink":{"name":"Some Site","id":"examplelink","type":"http:\/\/othersite.example.com\/"} 
	// Directly links someone to the link specified in 'type'
	}

Then finally, move your desired template's folder contents to the same directory as md/ and index.php.
Keep in mind, not all the pages need to be listed in the file to be accessible.

Second, you'll have to set a site name. That's as simple as creating a text file 'sitename.txt' in the same directory, with the site's name in it.

That's it! You're done with the setup! Now for content.

Adding Content
----------------
Just put Markdown pages into md/. MdCms will do the rest. Add menu entries as described in the Configuration section.
 For PHP-generated stuff, make or adapt a script to return HTML when executed, and put it in md/php/. It cannot share a file name with anything in md/ 
(Example: md/test.md will be rendered instead of md/php/test.php)

Uses Michel Fortin's PHP Markdown parser.

Issues?
------------
Bring it to me on IRC.

irc.stormbit.net #stormbit, Ask for DJ_Arghlex
-----
