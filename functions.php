<?php

//$password = md5("Smith");
//echo $password."<br><br>";
//$code = md5(uniqid(rand(), TRUE));

//echo $code;



//Generate a unique code
function getUniqueCode($length = "") {
  $code = md5(uniqid(rand(), TRUE));
  if ($length != "") {
    return substr($code, 0, $length);
  }
  else {
    return $code;
  }
}



//$plainText = getUniqueCode(15);
//echo $plainText;


function generateHash($plainText, $salt = NULL) {
  if ($salt === NULL) {
    $salt = substr(md5(uniqid(rand(), TRUE)), 0, 25);
  }
  else {
    $salt = substr($salt, 0, 25);
  }

  return $salt . sha1($salt . $plainText);
}



//echo $newpassword;
//$compare = generateHash($_POST['password'], $newpassword);

//echo $compare;

function createNewUser($username, $firstname, $lastname, $email, $password)
{
  global $mysqli, $db_table_prefix;
  //Generate A random userid

  $character_array = array_merge(range(a, z), range(0, 9));
  $rand_string = "";
  for ($i = 0; $i < 6; $i++) {
    $rand_string .= $character_array[rand(
      0, (count($character_array) - 1)
    )];
  }
  //echo $rand_string;
  //echo $username;
  //echo $firstname;
  //echo $lastname;
  //echo $email;
  //echo $password;

  $newpassword = generateHash($password);

  echo $newpassword;


  $stmt = $mysqli->prepare(
    "INSERT INTO " . $db_table_prefix . "UserDetails (
		UserID,
		UserName,
		FirstName,
		LastName,
		Email,
		Password,
		MemberSince,
		Active
		)
		VALUES (
		'" . $rand_string . "',
		?,
		?,
		?,
		?,
		?,
        '" . time() . "',
        1
		)"
  );
  $stmt->bind_param("sssss", $username, $firstname, $lastname, $email, $newpassword);
  //print_r($stmt);
  $result = $stmt->execute();
  //print_r($result);
  $stmt->close();
  return $result;

}

//Retrieve complete user information by username
function fetchUserDetails($username)
{

  global $mysqli,$db_table_prefix;
  $stmt = $mysqli->prepare("SELECT
		UserID,
		UserName,
		FirstName,
		LastName,
		Email,
		Password,
		MemberSince,
		Active
		FROM ".$db_table_prefix."UserDetails
		WHERE
		UserName = ?
		LIMIT 1");
  $stmt->bind_param("s", $username);

  $stmt->execute();
  $stmt->bind_result($UserID, $UserName, $FirstName, $LastName, $Email, $Password, $MemberSince, $Active);
  while ($stmt->fetch()){
    $row = array('UserID' => $UserID,
                 'UserName' => $UserName,
                 'FirstName' => $FirstName,
                 'LastName' => $LastName,
                 'Email' => $Email,
                 'Password' => $Password,
                 'MemberSince' => $MemberSince,
                 'Active' => $Active);
  }
  $stmt->close();
  return ($row);
}



//Check if a user is logged in
function isUserLoggedIn()
{
  global $loggedInUser,$mysqli,$db_table_prefix;
  $stmt = $mysqli->prepare("SELECT
		UserID,
		Password
		FROM ".$db_table_prefix."UserDetails
		WHERE
		UserID = ?
		AND
		Password = ?
		AND
		active = 1
		LIMIT 1");
  $stmt->bind_param("is", $loggedInUser->user_id, $loggedInUser->hash_pw);
  $stmt->execute();
  $stmt->store_result();
  $num_returns = $stmt->num_rows;
  $stmt->close();

  if($loggedInUser == NULL)
  {
    return false;
  }
  else
  {
    if ($num_returns > 0)
    {
      return true;
    }
    else
    {
      destroySession("ThisUser");
      return false;
    }
  }
}



//Destroys a session as part of logout
function destroySession($name)
{
  if(isset($_SESSION[$name]))
  {
    $_SESSION[$name] = NULL;
    unset($_SESSION[$name]);
  }
}


//Retrieve complete user information of all users
function fetchAllUsers()
{

  global $mysqli,$db_table_prefix;
  $stmt = $mysqli->prepare("SELECT
		UserID,
		UserName,
		FirstName,
		LastName,
		Email,
		Password,
		MemberSince,
		Active
		FROM ".$db_table_prefix."UserDetails
		");

  $stmt->execute();
  $stmt->bind_result($UserID, $UserName, $FirstName, $LastName, $Email, $Password, $MemberSince, $Active);
  while ($stmt->fetch()){
    $row[] = array('UserID' => $UserID,
                 'UserName' => $UserName,
                 'FirstName' => $FirstName,
                 'LastName' => $LastName,
                 'Email' => $Email,
                 'Password' => $Password,
                 'MemberSince' => $MemberSince,
                 'Active' => $Active);
  }
  $stmt->close();
  return ($row);
}


function fetchAllBlogs(){
  global $mysqli,$db_table_prefix;
  $stmt = $mysqli->prepare("SELECT
		bloglisting.blogid,
		bloglisting.title,
	    bloglisting.datecreated,
	    bloglisting.deleteflag,
	    bloglisting.active,
	    whomadewho.userid,
	    blogcontent.blogcontent,
	    UserDetails.UserName,
	    UserDetails.FirstName,
	    UserDetails.LastName,
	    UserDetails.Email

        FROM whomadewho INNER JOIN bloglisting ON whomadewho.blogid = bloglisting.blogid
	 INNER JOIN UserDetails ON whomadewho.userid = UserDetails.UserID
	 INNER JOIN blogcontent ON blogcontent.blogid = bloglisting.blogid
		");

  $stmt->execute();
  $stmt->bind_result($blogid, $title, $datecreated, $deleteflag, $active, $userid, $blogcontent, $username, $firstname, $lastname, $email);
  while ($stmt->fetch()){
    $row[] = array('blogid' => $blogid,
                   'title' => $title,
                   'datecreated' => $datecreated,
                   'deleteflag' => $deleteflag,
                   'active' => $active,
                   'userid' => $userid,
                   'blogcontent' => $blogcontent,
                   'username' => $username,
                   'firstname' => $firstname,
                   'lastname' => $lastname,
                   'email'  => $email
                   );
  }
  $stmt->close();
  return ($row);
}





function fetchMyBlogs(){
  global $loggedInUser, $mysqli,$db_table_prefix;
  $stmt = $mysqli->prepare("SELECT
		bloglisting.blogid,
		bloglisting.title,
	    bloglisting.datecreated,
	    bloglisting.deleteflag,
	    bloglisting.active,
	    whomadewho.userid,
	    blogcontent.blogcontent

        FROM whomadewho INNER JOIN bloglisting ON whomadewho.blogid = bloglisting.blogid
	                    INNER JOIN blogcontent ON blogcontent.blogid = bloglisting.blogid
		WHERE whomadewho.userid = ?");
  $stmt->bind_param("s", $loggedInUser->user_id);
  $stmt->execute();
  $stmt->bind_result($blogid, $title, $datecreated, $deleteflag, $active, $userid, $blogcontent);
  while ($stmt->fetch()){
    $row[] = array('blogid'       => $blogid,
                   'title'        => $title,
                   'datecreated'  => $datecreated,
                   'deleteflag'   => $deleteflag,
                   'active'       => $active,
                   'userid'       => $userid,
                   'blogcontent'  => $blogcontent
                   );
  }
  $stmt->close();
  return ($row);
}





function fetchThisBlog($blogid){
global $loggedInUser, $mysqli,$db_table_prefix;
  $stmt = $mysqli->prepare("SELECT
		bloglisting.blogid,
		bloglisting.title,
	    bloglisting.datecreated,
	    bloglisting.deleteflag,
	    bloglisting.active,
	    whomadewho.userid,
	    blogcontent.blogcontent,
	    UserDetails.UserName,
	    UserDetails.FirstName,
	    UserDetails.LastName,
	    UserDetails.Email

        FROM whomadewho INNER JOIN bloglisting ON whomadewho.blogid = bloglisting.blogid
	 INNER JOIN UserDetails ON whomadewho.userid = UserDetails.UserID
	 INNER JOIN blogcontent ON blogcontent.blogid = bloglisting.blogid
		WHERE bloglisting.blogid = ?");
  $stmt->bind_param("s", $blogid);
  $stmt->execute();
  $stmt->bind_result($blogid, $title, $datecreated, $deleteflag, $active, $userid, $blogcontent, $username, $firstname, $lastname, $email);
  while ($stmt->fetch()){
  $row = array('blogid'       => $blogid,
                 'title'        => $title,
                 'datecreated'  => $datecreated,
                 'deleteflag'   => $deleteflag,
                 'active'       => $active,
                 'userid'       => $userid,
                 'blogcontent'  => $blogcontent,
                  'username' => $username,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email'  => $email
  );
}
$stmt->close();
return ($row);
}







function createBlog($title, $blog){
  global $loggedInUser, $mysqli,$db_table_prefix;


  $character_array = array_merge(range(a, z), range(0, 9));
  $rand_string = "";
  for ($i = 0; $i < 6; $i++) {
    $rand_string .= $character_array[rand(
      0, (count($character_array) - 1)
    )];
  }
  $inserted_blogid = $rand_string;

  $stmt = $mysqli->prepare(
    "INSERT INTO bloglisting (
		blogid,
		title,
		datecreated,
		deleteflag,
		active
		)
		VALUES (
		?,
		?,
		'" . time() . "',
		0,
		1
		)"
  );
  $stmt->bind_param("ss",$inserted_blogid,$title);
  $result = $stmt->execute();
  $stmt->close();
  //return $result;

  $stmt = $mysqli->prepare(
    "INSERT INTO blogcontent (
		blogid,
		blogcontent
		)
		VALUES (
		?,
		?
		)"
  );
  $stmt->bind_param("ss",$inserted_blogid, $blog);
  $result = $stmt->execute();
  $stmt->close();
  //return $result;


  $stmt = $mysqli->prepare(
    "INSERT INTO whomadewho (
		blogid,
		userid
		)
		VALUES (
        ?,
        ?
		)"
  );
  $stmt->bind_param("ss",$inserted_blogid, $loggedInUser->user_id);
  $result = $stmt->execute();
  $stmt->close();
  return $result;

}

function truncate_chars($text, $limit, $ellipsis = '...') {
  if( strlen($text) > $limit )
    $text = trim(substr($text, 0, $limit)) . $ellipsis;
  return $text;
}