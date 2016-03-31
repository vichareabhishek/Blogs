<?php

//Links for logged in user
if(isUserLoggedIn()) {
	echo "
	<ul>
		<li><a href='myaccount.php'>Account Home</a></li>
		<li><a href='viewmyblogs.php'>View My Blogs</a> </li>
		<li><a href='createblog.php'>Create A New Blog</a></li>
		<li><a href='logout.php'>Logout</a></li>
	</ul>";

}
//Links for users not logged in
else {
	echo "
	<ul>
		<li><a href='index.php'>Home</a></li>
		<li><a href='login.php'>Login</a></li>
		<li><a href='register.php'>Register</a></li>
	</ul>";
}

?>
