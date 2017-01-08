<!DOCTYPE html>

<?php
  require_once 'connect2db.php';
?>

<html lang='en'>
  <head>
    <title>CSC492</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="Test.css">
    <script JQUERY src="jquery-2.1.0.js"></script>
    <script Contoller src="Controller.js"></script>
  </head>

  <body>
    <form method="post" action="connect2db.php" id="login_form">
      <table id="login_table" style="border:2px solid black;">
        <tr>
          <th align="center" colspan="2">Login using a valid UTORID</th>
        </tr>
        <tr>
          <td allign="right"> UTORID:</td>
          <td> <input type="text" name="utorid" id="utorid" size="8"> </td>
        </tr>
        <tr>
          <td allign="right"> Password:</td>
          <td> <input type="password" name="password" id="password" size="15"> </td>
        </tr>
        <tr>
          <td align="right" colspan="2"> 
            <input type="submit" value="login">
          </td>
        </tr>
      </table>
    </form>
    <!--
  	<button type="button" name="MainButton" onclick="makeMessage('Main');">Main Page</button>
    <button type="button" name="ApplicationButton" onclick="makeMessage('Application');">Application</button>
    <button type="button" name="ProfileButton" onclick="makeMessage('Profile');">My Profile</button>
    -->
    <div id="content">
      <!--Default-->
    </div>
  </body>
</html>