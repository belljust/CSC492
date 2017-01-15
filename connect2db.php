<?php

	/* ==================== Initial visit ======================== */

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

	/* ======================= REQUESTS ========================= */
	
	/* Creates a string of user info to return to controller.js
		when it makes a getinfo() call via ajax */
	
	//should wrap all of this in GETINFO isset!!!
	if (isset($_POST['GetInfo'])){	
		$returnString = '';
		if (isset($_POST['LoggedIn'])){
			$returnString .= 'loggedIn='.$_SESSION['loggedIn'].'&';
		}
		if (isset($_POST['User'])){
			$returnString .= 'User='.$_SESSION['user'].'&';
		}
		if (isset($_POST['Role'])){
			$returnString .= 'Role='.$_SESSION['role'];
		}
		echo $returnString;
	}


	/* =================== $_POST variables ======================== */

	/* Check if given UTORID is in the table users, if so, log in user 
	and display message, otherwise echo appropriate message */
	if(isset($_POST['Login'])){
		$query = 'SELECT * FROM USERS WHERE UTORID='."'".$_POST['UTORID']."';";
		$result = mysqli_query($dbconnect, $query);
		$row =  mysqli_fetch_array($result, MYSQLI_ASSOC);

		/*If a row is returned (result is nonempty) */
		if(mysqli_num_rows($result) == 1){
			if(md5($_POST['password']) == $row['Password']){
				$_SESSION['loggedIn'] = "true";
				$_SESSION['user'] = $_POST['UTORID'];
				$_SESSION['role'] = $row['ROLE'];
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

	/* Terminate session and database connection when user logs out */
	if(isset($_POST['Logout'])){
		$_SESSION["loggedIn"] = "false";
		session_destroy();
		echo 'You are now logged out';
		mysqli_close($dbconnect);
	}


	/* ================== $_SESSION variables ====================== */

	/* Initalize sessions variables for vistors 
		who aren't logged in */
	if(!isset($_SESSION['loggedIn'])){
		$_SESSION["loggedIn"] = "false";
		$_SESSION['user'] = "";
		$_SESSION['role'] = "";
	}
?>
