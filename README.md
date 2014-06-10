MdCms
=====

It's a Content Management System that uses Markdown to display the pages.
 It's easily extensible and doesn't require you to have a web designer, rocket scientist or heart surgeon to do it for you.

Configuration
-----------
First, edit settings.json:


	{
	"sitename":"My Awesome Markdown Site",  // This is the site's name.
	//These next entries are the three types of menu entries you can have.
	"example":{"name":"Example Page","id":"example","type":"md"},  // Loads and translates a markdown page from ./md/example.md
	"dynamicexample":{"name":"Example PHP-generated Page","id":"dynamicexample","type":"php"}, //runs a PHP script and returns its HTML contents
	"examplelink":{"name":"Some Site","id":"examplelink","type":"http:\/\/othersite.example.com\/"} // directly links someone to the link specified in 'type'
	}

Then finally, move your desired template's folder contents to the same directory as md/ and index.php.

Adding Content
----------------
Just put Markdown pages into md/. MdCms will do the rest. Add menu entries as described in the Configuration section
For PHP-generated stuff, make or adapt a script to return HTML when executed, and put it in md/php/. It cannot share a file name with anything in md/
(Example: md/test.md will be rendered instead of md/php/test.php)

Uses Michel Fortin's PHP Markdown parser.

Issues?
------------
Bring it to me on IRC.

irc.stormbit.net #stormbit, Ask for DJ_Arghlex
-----
