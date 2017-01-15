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
    <script Controller src="Controller.js"></script>
  </head>

  <body>
    <div id="banner">
      <table id="bannerTable">
        <tr>
          <td><img src="Pictures/UofTLogo.png" id="UofTLogo"></td>
          <td><h1> TA Application System </h1></td>
          <td>
            <button type="button" id="logout"> Logout </button>
            <button type="button" id="getInfo"> Get Info </button>
          </td>
        </tr>
      </table>
    </div>
    <br>
    <center>
      <div id="content">
      <!--Default-->
      </div>
    </center>
  </body>
</html>