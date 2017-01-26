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

		/* Deleting Courses */
		if(isset($_POST['Delete'])){
			$query = 'DELETE FROM Course WHERE CID="'.$_POST["ID"].'";';
			mysqli_query($dbconnect, $query);
		}

		/* Adding Courses */
		if(isset($_POST['Add'])){
			$query = 'SELECT * FROM COURSE ORDER BY CID DESC;';
			$result = mysqli_query($dbconnect, $query);
			$row =  mysqli_fetch_array($result, MYSQLI_NUM);
			mysqli_free_result($result);
			$query = 'INSERT INTO COURSE VALUES('.($row[0] + 1).',"'.$_POST['CourseCode'].'","'.$_POST['CourseTitle'].
					'","'.$_POST['CourseTerm'].'","'.$_POST['CourseInstructor'].'","'.$_POST['CourseCampus'].'");';
			mysqli_query($dbconnect, $query);
		}

		$query = 'SELECT * FROM Course;';
		/* could add sort feature */

		$result = mysqli_query($dbconnect, $query);
		$returnString = '<table id="courseTable"><thead><tr><th align="center" colspan="5">OPPORTUNITIES</th></tr>';
		$returnString .= '<tr><td>ID</td><td>Code</td><td>Title</td><td>Term</td><td>Instructor</td><td>Campus</td></thead><tbody>';
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i <=5; $i++){
				$returnString .= '<td>'.$row[$i].'</td>';
			}
			$returnString .= '</tr>';
		}
		/* Creates the html code for the Course Table and the form for adding a course underneath */
		$returnString .= '<tr><button id="deleteCourse" onclick="deleteCourse()">Delete Course</botton></tr>'.
						 '</tbody></table>'.
						 '<form id="addCourseForm"><table><tr><td allign="right"> Course Code:</td>'.
						 '<td><input type="text" name="courseCode" id="courseCode" size="7"></td>'. 
						 '<td allign="right"> Title:</td>'.
						 '<td><input type="text" name="courseTitle" id="courseTitle" size="40"></td></tr>'.
						 '<tr><td allign="right"> Term Offered:</td>'.
						 '<td><input type="text" name="courseTerm" id="courseTerm" size="1"></td>'.
						 '<td allign="right"> Instructor:</td>'.
						 '<td><input type="text" name="courseInstructor" id="courseInstructor" size="8"></td>'.
						 '<td allign="right"> Campus:</td>'.
						 '<td><input type="text" name="courseCampus" id="courseCampus" size="5"></td></tr></table>'.
						 '<input type="submit" id="addCourse" value="Add course"></form>';
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
