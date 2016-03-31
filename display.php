<?php



include_once("config.php");

// call to function fetchAllUsers() from functions.php
$allusers = fetchAllUsers();

?>

<div id='left-nav'>";

<?php include("left-nav.php"); ?>

</div>

<pre><?php //print_r($allusers); ?></pre>

<html>
  <head>
    <title>Display All Users</title>
  </head>

  <body>
    <table>
      <tr><td>UserName</td>
      <td>FirstName</td>
      <td>LastName</td>

        <?php //NOTICE THE USE OF PHP IN BETWEEN HTML
            foreach($allusers as $userdetails){
              ?>
    
              <tr>
            <td><a href="updateThisUser.php?userid=<?php print $userdetails['UserID']; ?>"><?php print $userdetails['UserID']; ?></a></td>
            <td><?php print $userdetails['FirstName']; ?></td>
            <td><?php print $userdetails['LastName']; ?></td>

          </tr>
    
              <?php } ?>

    </table>
  </body>
</html>
