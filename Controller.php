<?php
	
	/* ==================== Initial visit ======================== */
	require_once 'connect2db.php';

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
	if((isset($_POST['Courses'])) && ($_SESSION['loggedIn'] == "true")){

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
			$query = 'INSERT INTO COURSE VALUES('.($row[0] + 1).',"'.htmlspecialchars($_POST['CourseCode']).
					'","'.htmlspecialchars($_POST['CourseTitle']).'","'.$_POST['CourseTerm'].'","'.
					$_POST['CourseYear'].'","'.$_POST['CourseInstructor'].'","'.$_POST['CourseCampus'].'","'.
					$_POST['TaPositions'].'","'.$_POST['TaPositions'].'");';
			mysqli_query($dbconnect, $query);
			
		}

		/* Changing Instructor of a course */
		if(isset($_POST['ChangeInstructor'])){
			$query = 'UPDATE COURSE SET INSTRUCTOR="'.htmlspecialchars($_POST['Instructor']).
					'" WHERE CID='.$_POST['RowId'];
			$result = mysqli_query($dbconnect, $query);
		}
		
		$query = 'SELECT * FROM Course;';
		/* could add sort feature */

		/* Populate the Course table, row by row */
		$result = mysqli_query($dbconnect, $query);
		$returnString = '<div id="tableWrap"><table id="courseTable"><thead><tr>'.
						'<th align="center" colspan="9">OPPORTUNITIES</th></tr><tr><td>ID</td>'.
						'<td>Code</td><td>Title</td><td>Term</td><td>Year</td><td>Instructor</td>'.
						'<td>Campus</td><td>Num Positions</td><td>Available</td></tr></thead><tbody>';
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i <=8; $i++){
				$returnString .= '<td>'.$row[$i].'</td>';
			}
			$returnString .= '</tr>';
		}
		$returnString.= '</tbody></table></div><br>';

		/* Only contstruct/ send forms at bottom of table if role is an ADMIN or INSTRUCTOR */
		if ($_SESSION['role'] == 'ADMIN' or $_SESSION['role'] == 'INSTRUCTOR'){

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

				/* Num TA Positions select */
				$Positions = '<select id="numPositions">';
				foreach (range(1, 20) as $number) {
				    $Positions.= '<option value="'.$number.'">'.$number.'</option>';
				}
				$Positions.= '</select>';
				
				/* Course Years */ 
				$years = '<select id="courseYear">';
				foreach (range(2017, 2020) as $number) {
				    $years.= '<option value="'.$number.'">'.$number.'</option>';
				}
				$years.= '</select>';
			/* ===================================================================================== */
	

			/* Creates the html code for the Course Table and the form for adding a course underneath */
			$returnString .= '<table><tr><td>Remove a course: </td><td>'.
							 '<button id="deleteCourse" onclick="deleteItem('."'".'Course'."'".')">'.
							 'Delete Selected Course</button></td></tr><tr><td>Change Course'."'".'s Instructor to: </td>'.
							 '<td><select id="changeInstructor">'.$instStringBody.'</td><td>'.
							 '<button id="changeCourseIns" onclick="changeCourseIns()">Update Teacher</button>'.
							 '</td></tr></table><p>Add a course with form below: </p>'.
							 '<form id="addCourseForm"><table><tr><td> Course Code:</td>'.
							 '<td><input type="text" name="courseCode" id="courseCode" size="7"></td>'. 
							 '<td> Title:</td>'.
							 '<td colspan="3"><input type="text" name="courseTitle" id="courseTitle" size="40">'.
							 '</td></tr>'.'<tr><td> Term Offered:</td><td><select id="courseTerm">'.
							 '<option value="W">Winter</option><option value="F">Fall</option>'.
							 '<option value="S">Summer</option></select></td>'.
							 '<td> Instructor:</td><td>'.$instStringHead.'</td><td> Campus:</td><td>'.
							 $Campuses.'</td></tr><tr><td>TA Positions: </td><td>'.$Positions.'</td>'.
							 '<td>Year: </td><td>'.$years.'</td></tr></table>'.
							 '<input type="submit" id="addCourse" value="Add course"></form>';
		}
		elseif($_SESSION['role'] == 'APPLICANT'){
			$returnString.= '<center><button id="apply">Apply For Selected Course</botton></center>';
		}
		echo $returnString;
		mysqli_free_result($result);
	}

	/* The HTML for the application form to be filled out. */
	if(isset($_POST['ApplyRequest'])){
		$returnString = '<center><form id="applyForm"><table id="applyTable"><tr><th align="center" colspan="2">'.
						'Application for '.trim($_POST["RowCourse"],'"').' ('.trim($_POST["RowTerm"],'"').') with Prof.'.
						'</th></tr><tr><td> Number of courses taken with this Instructor?: </td>'.
						'<td><select id="numCourses"><option value=0>0</option><option value=1>1</option>'.
						'<option value=2>2</option><option value=3>3</option><option value=4>4</option>'.
						'<option value="5+">5 or more</option></td></tr>'.
						'<tr><td> Have you TA'."'".'d this course before?: </td>'.
						'<td><select id="taBefore"><option value="yes">YES</option><option value="no">NO</option>'.
						'<tr><td> Have you worked for this Prof before?: </td>'.
						'<td><select id="workBefore"><option value="yes">YES</option><option value="no">NO</option>'.
						'<tr><td> Grade you received in this course? (0-100): </td>'.
						'<td><input type="text" name="grade" id="grade" maxlength="3" size="4" placeholder="65">'.
						'</td></tr><tr><td><input type="submit" id="applySubmit" value="Submit Application">'.
						'</td></tr></table></form></center>';
		echo $returnString;
	}

	/* Adding information from application into appropriate tables. */
	if(isset($_POST['ApplySubmit'])){

		/* Incrementing primary key in answers table. */
		$query = 'SELECT * FROM ANSWERS ORDER BY AID DESC;';
		$result = mysqli_query($dbconnect, $query);
		$row =  mysqli_fetch_array($result, MYSQLI_NUM);
		mysqli_free_result($result);

		/* Adding the answers from post into Answers table. */
		$query = 'INSERT INTO ANSWERS VALUES('.($row[0] + 1).','.$_POST['NumCourses'].','.$_POST['WorkBefore'].
			   	 ','.$_POST['TaBefore'].','.$_POST['Grade'].');';
		mysqli_query($dbconnect, $query);

		/* Adding the Application info into applications table. */
		$query = 'INSERT INTO APPLICATIONS VALUES ("'.$_SESSION['user'].'",'.$_POST['RowId'].',"'.
				 $_POST['Late'].'","'.($row[0] + 1).'","UNSET");';
		mysqli_query($dbconnect, $query);
		echo "<br><p>Thanks, Your Application has been submitted!</p>";
	}

	if(isset($_POST['All_Applications'])) {
		if (isset($_POST['Sort'])){
			$query = 'SELECT UTORID,CODE,SEMESTER,YEAR,INSTRUCTOR,CAMPUS '.
				 'FROM (COURSE NATURAL JOIN APPLICATIONS) ORDER BY ' . $_POST['SortValue'] . ';';
		
		}else{		
			$query = 'SELECT UTORID,CODE,SEMESTER,YEAR,INSTRUCTOR,CAMPUS '.
				 'FROM (COURSE NATURAL JOIN APPLICATIONS);';
		}

		$returnString = '<div id="tableWrap"><table id="allAppTable"><thead><th align="center" colspan="6">'.
						'All Applications</th><tr><td>UTORID</td><td>Course Code</td><td>Term</td>'.
						'<td>Year</td><td>Instructor</td><td>Campus</td></tr></thead><tbody>';	 
				 
		$result = mysqli_query($dbconnect, $query);
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i<=5; $i++){
				$returnString .= '<td>'.$row[$i].'</td>';
			}
			$returnString .= '</tr>';
		}
		$returnString.= '</tbody></table></div><br><button id="viewProfile"'.
						'onclick="getProfile('."'Instructor')".'">View Profile</button><br>';
		
		$returnString.= 'Sort by: <label for="Sort"></label><select id="appSort">
						<option value="UTORID">	UTORID</option>
						<option value="Code"> Course Code</option>
						<option value="Semester"> Term</option>
						<option value="Year"> Year</option>
						<option value="Instructor"> Instructor</option>
						<option value="Campus">	Campus</option></select>';
		
		echo $returnString;
	}
	
	if(isset($_POST['My_Applications'])) {
		$returnString = '<table id="myAppTable"><thead><th align="center" colspan="5">'.
						'My Applications</th><tr><td>Course Code</td><td>Term</td>'.
						'<td>Year</td><td>Instructor</td><td>Campus</td></tr></thead><tbody>';

		$query = 'SELECT CODE,SEMESTER,YEAR,INSTRUCTOR,CAMPUS '.
				 'FROM (COURSE NATURAL JOIN APPLICATIONS) NATURAL JOIN ANSWERS '.
				 'WHERE UTORID="'.$_SESSION['user'].'";';
		$result = mysqli_query($dbconnect, $query);
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i <=4; $i++){
				$returnString .= '<td>'.$row[$i].'</td>';
			}
			$returnString .= '</tr>';
		}
		$returnString.= '</tbody></table><br>';
		echo $returnString;
	}


	/* ==================== Users Request ======================== */

	if(isset($_POST['Users'])){


		if (isset($_POST['Delete'])){
			$query = 'DELETE FROM Users WHERE UTORID="'.$_POST["ID"].'";';
			mysqli_query($dbconnect, $query);
		}

		if (isset($_POST['Add'])){
			$query = 'INSERT INTO Users (UTORID,FNAME,LNAME,ROLE,Password) VALUES("'.
					$_POST["UserUtorid"].'","'.$_POST["UserFname"].'","'.$_POST["UserLname"].
					'","'.$_POST["UserRole"].'","'.md5($_POST["UserPassword"]).'");';
			mysqli_query($dbconnect, $query);
		}

		$query = 'SELECT * FROM Users;';
		/* could add sort feature */

		$result = mysqli_query($dbconnect, $query);
		$returnString = '<div id="tableWrap"><table id="userTable">'.
						'<thead><th align="center" colspan="6">CURRENT USERS</th>'.
						'<tr><td>UTORID</td><td>First Name</td><td>Last Name</td><td>Role</td>'.
						'</thead><tbody>';
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i <=3; $i++){
				$returnString .= '<td>'.$row[$i].'</td>';
			}
			$returnString .= '</tr>';
		}
		$returnString .= '<tr></tbody></table></div><br>'.
						 '<button id="deleteUser" onclick="deleteItem('."'".'User'."'".')">'.
		                 'Remove Selected User</button><br>'.
						 '<form id="addUserForm"><table><tr><td> Utorid:</td>'.
						 '<td><input type="text" name="userUtorid" id="userUtorid" size="10"'.
						 'maxlength="8"></td><td> Role:</td>'.
						 '<td><select id="userRole"><option value="APPLICANT"> Applicant</option>'.
						 '<option value="INSTRUCTOR"> Instructor </option></select></td></tr>'.
						 '<tr><td> First Name:</td>'.
						 '<td colspan="3"><input type="text" name="userFname" id="userFname" size="40">'.
						 '</td>'.'</tr><tr><td> Last Name:</td><td colspan="3">'.
						 '<input type="text" name="userLname" id="userLname" size="40">'.
						 '</td></tr>'.'<tr><td> Password:</td><td colspan="3">'.
						 '<input type="password" name="userPassword" id="userPassword" size="40">'.
						 '</td></tr>'.'<tr><td> Retype Pswd:</td><td colspan="3">'.
						 '<input type="password" name="retypePassword" id="retypePassword" size="40">'.
						 '</td></tr>'.
						 '</table><input type="submit" id="addUser" value="Add User"></form><br>';
		echo $returnString;
		mysqli_free_result($result);
	}


	/* ==================== Profiles Request ======================== */

	if(isset($_POST['Profiles'])){
		if(isset($_POST['GetProfile'])){

			$query = 'SELECT UTORID,FNAME,LNAME,YEAR_STUDY,CHOICE1,CHOICE2,CHOICE3,CHOICE4,
				 CHOICE5,TA_EXP,VOLUNTEER,BLURB FROM (USERS NATURAL JOIN PROFILES) WHERE UTORID="'.
				 $_POST['ProfileId'].'";';
			$result = mysqli_query($dbconnect, $query);
			$returnString = '<form id="editApplication"><table id="myProfileTable"><tr>'.
							'<th align="center" colspan="12">';

			if(isset($_POST['EditProfile'])){
				/* Create selects for preference choices */
				$query2 = 'SELECT CODE FROM APPLICATIONS NATURAL JOIN COURSE WHERE UTORID="'.
						   $_SESSION['user'].'";';
				$result2 = mysqli_query($dbconnect, $query2);
				$course1 = '<select id="course1">';
				$course2 = '<select id="course2">';
				$course3 = '<select id="course3">';
				$course4 = '<select id="course4">';
				$course5 = '<select id="course5">';

				while($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)){
					$course1 .= '<option value="'.$row2[0].'">'.$row2[0].'</option>';
					$course2 .= '<option value="'.$row2[0].'">'.$row2[0].'</option>';
					$course3 .= '<option value="'.$row2[0].'">'.$row2[0].'</option>';
					$course4 .= '<option value="'.$row2[0].'">'.$row2[0].'</option>';
					$course5 .= '<option value="'.$row2[0].'">'.$row2[0].'</option>';
				}
				$course1 .= '</select>'; 
				$course2 .= '</select>';
				$course3 .= '</select>';
				$course4 .= '</select>';
				$course5 .= '</select>';

				$returnString.= $_SESSION['user']."'s Profile</th></tr>".
							'<tr><td><b>Year of Study:</b></td><td><select id="pYear"'.
							'style="width: 75px;"><option value="1">1</option>'.
							'<option value="2">2</option><option value="3">3</option>'.
							'<option value="4">4</option><option value="5">5</option>'.
							'<option value="Grad">Graduate</option><option value="non">Non-Student</option>'.
							'<td><b>1st Choice:</b></td> <td>'.$course1.'</td>'.
							'<td><b>2nd Choice:</b></td><td>'.$course2.'</td></tr>'.
							'<tr><td><b>3rd Choice:</b></td><td>'.$course3.'</td>'.
							'<td><b>4th Choice:</b></td><td>'.$course4.'</td>'.
							'<td><b>5th Choice:</b></td><td>'.$course5.'</td></tr>'.
							'<tr><center><th colspan="6">My Past TA Experience</center><th></tr>'.
							'<tr><td colspan="6"><textarea id="pTAExp" style="width: 100%;" '.
							'cols="25" rows="4"></textarea></td></tr>'.
							'<tr><center><th colspan="6">My Extracurricular/Volunteer Activites</center><th></tr>'.
							'<tr><td colspan="6"><textarea id="pTAVol" style="width: 100%;" '.
							'cols="25" rows="4"></textarea></td></tr>'.
							'<tr><center><th colspan="6">Why I'."'".'d Make a Good TA?</center><th></tr>'.
							'<tr><td colspan="6"><textarea id="pTAWhy" style="width: 100%;" '.
							'cols="25" rows="4"></textarea></td></tr><tr><td>'.
							'<input type="submit" id="submitPro" value="Update Profile"></td></tr></table></form>'.
							'placeValues()';
			}else{
				/* Return message when profile is empty */
				if(mysqli_num_rows($result) == 0){
					$returnString.= 'THIS PROFILE IS EMPTY!</th></tr></table></form>';
				}else{ 
					while($row = mysqli_fetch_array($result, MYSQLI_NUM)){
						$returnString.= $row[0]. "'s Profile</th></tr>".'<tr><td id="fName"><b>First Name:</b>'.
								$row[1].'</td><td id="lName"><b>Last Name:</b> '.$row[2].'</td></tr>'.
								'<tr><td><b>First Choice: </b></td><td id="choice1">'.$row[4].'</td>'.
								'<td><b>Second Choice: </b></td><td id="choice2">'.$row[5].'</td>'.
								'<td><b>Third Choice:</b></td><td id="choice3">'.$row[6].'</td>'.
								'<td><b>Fourth Choice: </b></td><td id="choice4">'.$row[7].'</td>'.
								'<td><b>Fifth Choice: </b></td><td id="choice5">'.$row[8].'</td></tr>'.
								'<tr><td colspan="6"  id="studyYear"><b>My Current Year of Study: </b> '.
								$row[3].'</td></tr>'.
								'<tr><td colspan="6"  id="taExp"><b>My Past TA Experience: </b><p>'.
								$row[9].'</p></td></tr>'.
					 			'<tr><td colspan="6"  id="taVol"><b>My Extracurricular/ Volunteer Activities:'.
					 			' </b><p>'.$row[10].'</p></td></tr>'.
								'<tr><td colspan="6" id="taWhy"><b>Why Would I Make a Good TA?: </b><p>'.
								$row[11].'</p></td></tr></table></form>';
					}
				}
			}
			
			/* Only display edit button for the owner of the profile */
			if(($_SESSION['role'] == 'APPLICANT') && !(isset($_POST['EditProfile']))){
				$returnString.= '<button id="editProfile" onclick="editProfile()">'.
								'Edit Profile</button>';
			}
			echo $returnString;	
		}

		if(isset($_POST['UpdateProfile'])){
			$query = 'UPDATE PROFILES SET YEAR_STUDY="'.$_POST['Year'].
					'", CHOICE1="'.$_POST['Choice1']. '", CHOICE2="'.$_POST['Choice2'].
					'", CHOICE3="'.$_POST['Choice3']. '", CHOICE4="'.$_POST['Choice4'].
					'", CHOICE5="'.$_POST['Choice5'].
					'", TA_EXP="'.htmlspecialchars($_POST['TaExp']).
					'", VOLUNTEER="'.htmlspecialchars($_POST['TaVol']).
					'", BLURB="'.htmlspecialchars($_POST['TaWhy']).'" WHERE UTORID="'.
					$_SESSION['user'].'";';
			mysqli_query($dbconnect, $query);
			
		}

	}

?>
