<?php
// MdCms Blog Module - part two of two
// This is where the blog writer will post to the blog in markdown and edit settings. Protected using a very simple HTTP authorization system.

//the first rendition of this is to get it working. Then I'll do all the splitting of everything into functions and making it all display after it's processed everything and done whatever.

if ($requireSSL && $_SERVER['SERVER_PORT'] != 443){ //make sure we're using SSL so people can't sniff our passwords from our packets or something
	header('Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']',true,301);
	exit();
}else{
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="Blog Admin Module"');
		header('HTTP/1.0 401 Unauthorized');
		echo("Text to send if user hits Cancel button");
		return false;
	} else {
		//user authorized, let's do stuff
		$user=hash("sha256",$_SERVER['PHP_AUTH_USER']."/".$_SERVER['PHP_AUTH_PW']);
		$blogsettings=json_decode(file_get_contents("md/settings.json"), true);
		if (!$blogsettings){ //defaults! yay!
			$blogsettings["user"]=$user;
			$blogsettings["name"]="MDCMS_SITENAME Blog";
			$blogsettings["comment"]="Example blog system using MdCms";
			file_put_contents("md/blog/settings.json",json_encode($blogsettings));
		}
		if ($user != $blogsettings["user"]){
			header('HTTP/1.0 401 Unauthorized');
			print("Unauthorized user! Get your own blog and stop messing with mine!"); //you tell em, server!
		}else{ //"welcome back, dave"
			if (!$_REQUEST["posttime"]){
				$postpage='<h3>Add a new post!</h3><hr>
<form action="?" method="POST"><input type="textbox" id="postname" placeholder="Enter your post\'s title here!"><br>
<input type="textarea" id="postcontents" rows="15" cols="60" placeholder="Enter your post\'s contents here!"><br>
<input type="hidden" id="posttime" value="'.time().'"><br>
<input type="submit" value="Post!">
<input type="text" name="blogname" value="'.$blogsettings["name"].'"><br>
<input type="text" name="blogcomment" value="'.$blogsettings["comment"].'"><br>
</form>';
			}else{
				$blogsettings["name"]=$_REQUEST["blogname"];
				$blogsettings["comment"]=$_REQUEST["blogcomment"];
				file_put_contents("md/blog/settings.json",json_encode($blogsettings));
				file_put_contents("md/blog/".$_REQUEST["posttime"].".md","####".$_REQUEST["postname"]."\n\nPosted ".date("r",$_REQUEST["posttime"])."\n\n".$_REQUEST["postcontents"]."\n\n");
				print("Posted!");
		}
	}
}
