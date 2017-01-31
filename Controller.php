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

		/* Changing Instructor of a course */
		if(isset($_POST['ChangeInstructor'])){
			$query = 'UPDATE COURSE SET INSTRUCTOR="'.$_POST['Instructor'].'" WHERE CID='.$_POST['RowId'];
			$result = mysqli_query($dbconnect, $query);
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
			/* ========= Constructing selection options for available teachers and campuses ============ */
				mysqli_free_result($result);
 				$query = 'SELECT * FROM Users WHERE ROLE="INSTRUCTOR"';
				$result = mysqli_query($dbconnect, $query);

				/* Generate select options for Instructors to choose from */
				$instStringHead = '<select id="courseInstructor">';
				$instStringBody = '';
				while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
					$instStringBody .= '<option value="'.$row[0].'">'.$row[1].' '.$row[2].'</option>';
				}
				$instStringBody .= '</select>';
				$instStringHead .= $instStringBody;

				/* Campus Select box */
				$Campuses = '<select id="courseCampus"><option value="UTSTG"> UTSTG</option>'.
							'<option value="UTM"> UTM </option></select>';
			/* ===================================================================================== */


		/* Creates the html code for the Course Table and the form for adding a course underneath */
		$returnString .= '</tbody></table>'.'<p>Remove a course: </p>'.
						 '<button id="deleteCourse" onclick="deleteItem('."'".'Course'."'".')">'.
						 'Delete Selected Course</button><p>Change Course'."'".'s Instructor to: </p>'.
						 '<select id="changeInstructor">'.$instStringBody.
						 '<button id="changeCourseIns" onclick="changeCourseIns()">Update Teacher</button>'.
						 '<p>Add a course with form below: </p>'.
						 '<form id="addCourseForm"><table><tr><td allign="right"> Course Code:</td>'.
						 '<td><input type="text" name="courseCode" id="courseCode" size="7"></td>'. 
						 '<td allign="right"> Title:</td>'.
						 '<td><input type="text" name="courseTitle" id="courseTitle" size="40"></td></tr>'.
						 '<tr><td allign="right"> Term Offered:</td>'.
						 '<td><input type="text" name="courseTerm" id="courseTerm" size="2"></td>'.
						 '<td allign="right"> Instructor:</td><td>'.$instStringHead.'</td>'.
						 '<td allign="right"> Campus:</td><td>'.$Campuses.'</tr></table>'.
						 '<input type="submit" id="addCourse" value="Add course"></form>';
		echo $returnString;
		mysqli_free_result($result);
	}


	/* ==================== Users Request ======================== */

	if(isset($_POST['Users'])){


		if (isset($_POST['Delete'])){
			$query = 'DELETE FROM Users WHERE UTORID="'.$_POST["ID"].'";';
			mysqli_query($dbconnect, $query);
		}

		if (isset($_POST['Add'])){
			$query = 'INSERT INTO Users VALUES("'.$_POST["UserUtorid"].'","'.$_POST["UserFname"].
					'","'.$_POST["UserLname"].'","'.$_POST["UserRole"].'","'.md5($_POST["UserPassword"]).'");';
			mysqli_query($dbconnect, $query);
			echo md5($_POST["UserPassword"]);
		}

		$query = 'SELECT * FROM Users;';
		/* could add sort feature */

		$result = mysqli_query($dbconnect, $query);
		$returnString = '<table id="courseTable"><thead><th align="center" colspan="4">CURRENT USERS</th>'.
						'<tr><td>UTORID</td><td>First Name</td><td>Last Name</td><td>Role</td></thead><tbody>';
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i <=3; $i++){
				$returnString .= '<td>'.$row[$i].'</td>';
			}
			$returnString .= '</tr>';
		}
		$returnString .= '<tr></tbody></table><br>'.
						 '<button id="deleteUser" onclick="deleteItem('."'".'User'."'".')">'.
		                 'Remove Selected User</button><br>'.
						 '<form id="addUserForm"><table><tr><td allign="right"> Utorid:</td>'.
						 '<td><input type="text" name="userUtorid" id="userUtorid" size="10"></td>'.
						 '</tr><tr><td allign="right"> Role:</td>'.
						 '<td><input type="text" name="userRole" id="userRole" size="10"></td></tr>'.
						 '<tr><td allign="right"> First Name:</td>'.
						 '<td><input type="text" name="userFname" id="userFname" size="40"></td>'. 
						 '</tr><tr><td allign="right"> Last Name:</td><td>'.
						 '<input type="text" name="userLname" id="userLname" size="40"></td></tr>'.
						 '<tr><td allign="right"> Password:</td><td>'.
						 '<input type="password" name="userPassword" id="userPassword" size="40"></td></tr>'.
						 '</table><input type="submit" id="addUser" value="Add User"></form>';
		echo $returnString;
		mysqli_free_result($result);
	}

?>
