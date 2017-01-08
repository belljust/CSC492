<?php

	/* ================== Initial visit ======================== */

	session_save_path('Sessions');
	session_start();
	
	/* Paramaters for database connection */
	$servername = 'localhost';
	$username = 'root';
	$password = '';
	$database = 'CSC492';

	/* Create connection */
	$dbconnect = mysqli_connect($servername, $username, $password, $database);

	/* Display error if unable to connect to database*/
	if (!$dbconnect) {
    	echo "Error: Unable to connect to MySQL.";
    	exit;
	}


	/* ================ $_SESSION variables ====================== */

	/* Initalize sessions variables for vistors 
		who aren't logged in */
	if(!isset($_SESSION['loggedIn'])){
		$_SESSION["loggedIn"] = "false";
		$_SESSION['user'] = "";
		echo 'You are NOT logged in!';
	}


	/* ================= $_POST variables ======================== */

	/* Check if given UTORID is in the table users,
		if so, log in user and display message,
		otherwise echo appropriate message */
	if(isset($_POST['Login'])){
		$query = 'SELECT * FROM USERS WHERE UTORID='."'".$_POST['UTORID']."';";
		$result = mysqli_query($dbconnect, $query);
		$row =  mysqli_fetch_array($result, MYSQLI_ASSOC);
		if(mysqli_num_rows($result) == 1){
			if(md5($_POST['password']) == $row['Password']){
				echo "User Found";
			}
			else{
				echo "Incorrect Password";
			}
		}
		else{
			echo "User Not Found";
		}

		mysqli_free_result($result);
	}

	/* Terminate session and database connection
		when user logs out */
	if(isset($_POST['Logout'])){
		$_SESSION["loggedIn"] = "false";
		session_destroy();
		echo 'Just logged you out!';
		mysqli_close($dbconnect);
	}

?>
