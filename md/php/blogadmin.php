<?php
// MdCms Blog Module - part two of two
// This is where the blog writer will post to the blog in markdown and edit settings. Protected using a very simple HTTP authorization system.

//the first rendition of this is to get it working. Then I'll do all the splitting of everything into functions and making it all display after it's processed everything and done whatever.

//main backbone thing
function blogMain() {
	$blogsettings=json_decode(@file_get_contents("md/blog/settings.json"), true);
	if ($blogsettings===FALSE){
		$blogsettings["name"]="MDCMS_SITENAME Blog";
		$blogsettings["comment"]="An Example Blog powered by MdCms";
		file_put_contents("md/blog/settings.json",json_encode($blogsettings));
	}
	if (isset($_REQUEST["blogaction"])){
		if ($_REQUEST["blogaction"]=="login"){ //someone's feeding us login information
			$attempt=hash("sha256",$_REQUEST["username"].$_REQUEST["password"]);
			if (isset($blogsettings["user"])) {
				if ($blogsettings["user"] == $attempt) {
					header("Location: ?page=blogadmin&blogaction=addpost&user=".$blogsettings["user"],true,301);
					$output='<script type="text/javascript">window.location="?page=blogadmin&blogaction=addpost&user='.$blogsettings["user"].'";</script><a href="?page=blogadmin&blogaction=addpost&user='.$blogsettings["user"].'">Continue.</a></script>';
				}else{
					$output='<h4>Login incorrect</h4>';
				}
			}else{
				$blogsettings["user"]=$attempt;
				$blogsettings["name"]="MDCMS_SITENAME Blog";
				$blogsettings["comment"]="An Example Blog powered by MdCms";
				file_put_contents("md/blog/settings.json",json_encode($blogsettings));
				$output='<script type="text/javascript">window.location="?page=blogadmin&blogaction=addpost&user='.$blogsettings["user"].'";</script><a href="?page=blogadmin&blogaction=addpost&user='.$blogsettings["user"].'">Continue.</a></script>';
			}
		} elseif ($_REQUEST["blogaction"]=="addpost" && $_REQUEST["user"] == $blogsettings["user"]) { //admin wants to post a post
			$output='<h3>Add a new post!</h3><hr>
			Post Title:<br><form action="?page=blogadmin" method="POST"><input type="textbox" name="postname" placeholder="Enter your post\'s title here!"><br>
			Post Contents:<br><textarea name="postcontents" rows="15" cols="60">Enter your post\'s contents here!</textarea><br>
			<input type="hidden" name="posttime" value="'. time() .'"><br>
			<input type="submit" value="Post!"><hr>
			Blog Name:<br><input type="text" name="blogname" value="'.$blogsettings["name"].'"><br>
			Blog Splash:<br><input type="text" name="blogcomment" value="'.$blogsettings["comment"].'"><br>
			<input type="hidden" name="blogaction" value="dopost">
			<input type="hidden" name="user" value="'.$_REQUEST["user"].'">
			</form>';
		} elseif ($_REQUEST["blogaction"]=="dopost" && $_REQUEST["user"] == $blogsettings["user"]) { //admin is posting a post
			$blogsettings["name"]=htmlspecialchars($_REQUEST["blogname"]);
			$blogsettings["comment"]=htmlspecialchars($_REQUEST["blogcomment"]);
			file_put_contents("md/blog/settings.json",json_encode($blogsettings));
			file_put_contents("md/blog/". number_format(floor(9223372036854772205-time()),0,null,'') .".md","####".$_REQUEST["postname"]."\n\nPosted ".@date("r",$_REQUEST["posttime"])."\n\n".$_REQUEST["postcontents"]."\n\n");
				$output='<script type="text/javascript">window.location="?page=blog";</script><a href="?page=blog">Posted!</a></script>';
		} else { $output="<h4>You didn't specify a task for the blog lackey to complete!</h4>"; }
	} else {
		$output='<h3>Login</h3><hr>
		<form action="?page=blogadmin" method="POST">
		Username: <input type="textbox" name="username"><br>
		Password: <input type="password" name="password"><br>
		<input type="submit" value="Login!">
		<input type="hidden" name="blogaction" value="login">
		</form>';
	}
	return $output;
}
$output=blogMain();
return $output;
