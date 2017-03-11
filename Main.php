<!DOCTYPE html>

<?php
  require_once 'connect2db.php';
?>

<html lang='en'>
  <head>
    <title>CSC492</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <script JQUERY src="jquery-3.1.1.min.js"></script>
    <script Controller src="Controller.js"></script>
  </head>
  
  <body>
    <div id="banner">
      <table id="bannerTable">
        <tr>
          <td><img src="Pictures/UofTLogo2.png" id="UofTLogo"></td>
          <td><h1> TA Application System </h1></td>
          <td><button type="button" id="logout"> Logout </button></td>
        </tr>
      </table>
    </div>
    <br>
    <center>
      <div id="pageButtons">
        <!-- Display the page buttons here -->
      </div>

        <div id="pageInfo">
          <!-- Display the page content here -->
        </div>
     
      <div id="errorMessage">
        <!-- Display error messages here -->
      </div>
    </center>
  </body>
</html>