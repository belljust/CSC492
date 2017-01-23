<?php
	
	/* ==================== Initial visit ======================== */

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


	/* ================================= $_POST variables ====================================== */


	/* ==================== Courses Request ======================== */

	/* References users table, loops though and places results into a table on page */
	if(isset($_POST['Courses'])){

		if(isset($_POST['Delete'])){
			$query = 'DELETE FROM Course WHERE CID="'.$_POST["ID"].'";';
			mysqli_query($dbconnect, $query);
		}

		$query = 'SELECT * FROM Course;';
		/* could add sort feature */

		$result = mysqli_query($dbconnect, $query);
		$returnString = '<table id="courseTable"><th align="center" colspan="5">OPPORTUNITIES</th>';
		$returnString .= '<tr><td>ID</td><td>Code</td><td>Title</td><td>Term</td><td>Instructor</td><td>Campus</td>';
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i <=5; $i++){
				$returnString .= '<td>'.$row[$i].'</td>';
			}
			$returnString .= '</tr>';
		}
		$returnString .= '</table><button id="deleteCourse" onclick="deleteCourse()">Delete Course</botton>';
		echo $returnString;
		mysqli_free_result($result);
	}

	/* ==================== Users Request ======================== */

	if(isset($_POST['Users'])){
		$query = 'SELECT * FROM users;';
		/* could add sort feature */

		$result = mysqli_query($dbconnect, $query);
		$returnString = '<table id="courseTable"><th align="center" colspan="4">CURRENT USERS</th>';
		$returnString .= '<tr><td>UTORID</td><td>First Name</td><td>Last Name</td><td>Role</td>';
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i <=3; $i++){
				$returnString .= '<td>'.$row[$i].'</td>';
			}
			$returnString .= '</tr>';
		}
		$returnString .= '</table>';
		echo $returnString;
		mysqli_free_result($result);
	}

?>
