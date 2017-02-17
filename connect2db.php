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
	else{
		
	}

	/* ================== $_SESSION variables ====================== */

	/* Initalize sessions variables for vistors 
		who aren't logged in */
	if(!isset($_SESSION['loggedIn'])){
		$_SESSION["loggedIn"] = "false";
		$_SESSION['user'] = "";
		$_SESSION['role'] = "";
	}

	/* ======================= REQUESTS ========================= */
	
	/* Creates a string of user info to return to controller.js
		when it makes a getinfo() call via ajax */
	
	//should wrap all of this in GETINFO isset!!!
	if (isset($_POST['GetPages'])){	
		if ($_SESSION['loggedIn'] == "false"){
			/* HTML for login table. Displayed when $_SESSION['loggedIn'] == False */
			$loginTable = '<form id ="contentForm" method="post" action="">'.
      			'<table id="login_table" style="border:2px solid black;">'.
				'<tr><th align="center" colspan="2">Login using a valid UTORID</th></tr>'.
				'<tr><td allign="right"> UTORID:</td><td>'.
				'<input type="text" name="utorid" id="utorid" size="8"> </td></tr>'.
				'<tr><td allign="right"> Password:</td><td>'.
				'<input type="password" name="password" id="password" size="15"> </td></tr>'.
				'<tr><td align="right" colspan="2"> <input type="submit" id="login" value="Login">'.
				'</button></td></tr></table></form>';
			echo $loginTable;
		}else{
			if ($_SESSION['role'] == "ADMIN"){
				/* HTML for page buttons for admins. Displayed when $_SESSION['User'] == 'ADMIN' */
				$adminPages = '<center><table id="admButtons"><tr><td>'.
					'<button type="button" id="coursePage" onclick="displayPageInfo(' . "'Courses'" .')">Courses</button>'.
					'</td><td><button id="usersPage" onclick="displayPageInfo(' . "'Users'" .
					')">Users</button></td>'.
					'</td><td><button id="appPage" onclick="displayPageInfo(' . "'All_Applications'" .')">All Applications</button></td>'.
					'<td><button id="statsPage" onclick="displayStats()">Stats</button></td>
					</tr></table></center><br>';
				echo $adminPages;
			}
			elseif ($_SESSION['role'] == "INSTRUCTOR"){
				/* HTML for page buttons for instructors. Displayed when $_SESSION['User'] == 'INSTRUCTOR' */
				$instructorPages = '<center><table id="instButtons"><tr><td>'.
					'<button type="button" id="coursePage" onclick="displayPageInfo(' . "'Courses'" .')">Courses</button>'.
					'<td><button id="usersPage" onclick="displayPageInfo(' . "'Users'" .')">Users</button></td>'.
					'</td><td><button id="appPage" onclick="displayPageInfo(' . "'All_Applications'" .')">All Applications</button></td>
					</tr></table></center><br>';
				echo $instructorPages;
			}
			elseif ($_SESSION['role'] == "APPLICANT"){
				/* HTML for page buttons for applicants. Displayed when $_SESSION['User'] == 'APPLICANT' */
				$applicantPages = '<center><table id="appButtons"><tr><td><button id="coursePage"'.
					  'onclick="displayPageInfo(' . "'Courses'" .')">Courses</button>'.
					  '</td><td><button id="profile">Profile</button></td><td>'.
					  '<button id="applications" onclick="displayPageInfo('."'My_Applications'".')">'.
					  'My Applications</button></td>'.
					  '<td><button id="contact">Contact</button></td></tr></table></center><br>';
				echo $applicantPages;
			}
		}
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
		session_destroy();
		mysqli_close($dbconnect);
	}
	
?>
