<?php

require_once("config.php");
require_once("header.php");

//Prevent the user visiting the logged in page if he/she is already logged in
//if(isUserLoggedIn()) { header("Location: myaccount.php"); die(); }

$allblogs = fetchAllBlogs();

echo "
<body>
<div id='wrapper'>

<div id='content'>

<h2>Blogs, Blogs and More Blogs!!!</h2>

<div id='left-nav'>";
  include("left-nav.php");

echo "
</div>
<div id='main'>
<div id='content'>";
foreach($allblogs as $bloginfo) {

  $summary = truncate_chars($bloginfo['blogcontent'], 150, '...');
  //$summary  = $bloginfo['blogcontent'];
  $publisheddate = date("M d, Y", $bloginfo['datecreated']);

  echo "
<div class='post'>
	<h2><a href='viewblog.php?blogid=".$bloginfo['blogid']."'>".$bloginfo['title']."</a></h2>
	<br>
	<p>".$summary."</p>
	<div class='post-info'>
		<ul>
		    <li>BY : ".$bloginfo['firstname']." ".$bloginfo['lastname']."</li>
			<li class='date'>$publisheddate</li>
			<li class='read-more'><a href='viewblog.php?blogid=".$bloginfo['blogid']."'>Read more</a></li>
		</ul>
	</div>
</div>";

}


echo "</div>";
