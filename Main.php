<!DOCTYPE html>

<?php
  require_once 'Controller.php';
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
  	<button type="button" name="MainButton" onclick="makeMessage('Main');">Main Page</button>
    <button type="button" name="ApplicationButton" onclick="makeMessage('Application');">Application</button>
    <button type="button" name="ProfileButton" onclick="makeMessage('Profile');">My Profile</button>
    <div id="content">
      Default
    </div>
  </body>
</html>