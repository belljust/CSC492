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
	if((isset($_POST['Courses'])) && ($_SESSION['loggedIn'] == "true") && (!(isset($_POST['Questions'])))){

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
					$_POST['TaPositions'].'","'.$_POST['TaPositions'].'","'.htmlspecialchars($_POST['Question1']).'"
					,"'.htmlspecialchars($_POST['Question2']).'","'.htmlspecialchars($_POST['Question3']).'");';
			mysqli_query($dbconnect, $query);
		}

		/* Changing Instructor of a course */
		if(isset($_POST['ChangeInstructor'])){
			$query = 'UPDATE COURSE SET INSTRUCTOR="'.htmlspecialchars($_POST['Instructor']).
					'" WHERE CID='.$_POST['RowId'];
			$result = mysqli_query($dbconnect, $query);
		}

		/* Update questions for given course. */
		if(isset($_POST['UpdateQuestions'])){
			$query = 'UPDATE COURSE SET QUESTION_1="'.htmlspecialchars($_POST['Question1']).'", QUESTION_2="'.
					htmlspecialchars($_POST['Question2']).'", QUESTION_3="'.htmlspecialchars($_POST['Question3']).
					'" WHERE CID='.$_POST['RowId'].';';
			$result = mysqli_query($dbconnect, $query);
		}


		$query = 'SELECT * FROM Course ORDER BY CODE;';

		/* Populate the Course table, row by row */
		$result = mysqli_query($dbconnect, $query);
		$returnString = '<div id="tableWrap"><table id="courseTable" ><thead><tr>'.
						'<th align="center" colspan="9">OPPORTUNITIES</th></tr>'.
						'<td id="cCode">Code</td><td id="cTitle">Title</td><td id="cTerm">Term</td>'.
						'<td id="cYear">Year</td><td id="cInstructor">Instructor</td>'.
						'<td id="cCampus">Campus</td><td id="cPos">Num Positions</td><td id="cAvail">Available</td>'.
						'</td></thead><tbody>';

		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr id="body">';
			for($i=0; $i <=8; $i++){
				if($i==0){
					$returnString .= '<td style="display: none;" id="courseId">'.$row[$i].'</td>';
				}
				elseif($i == 2){
					$returnString .= '<td id="rowTitle">'.$row[$i].'</td>';
				}
				elseif($i == 7){
					$returnString .= '<td id="cPos">'.$row[$i].'</td>';
				}
				else{
					$returnString .= '<td id="cOther">'.$row[$i].'</td>';
				}
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
				$Campuses = '<select id="courseCampus"><option value="UTSG"> UTSG</option>'.
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
			$returnString.= '<table><tbody><tr><td><table id="courseOptionsTable">'.
							'<thead><tr><th colspan="6" align="center">Selected Course: '.
							'</th></tr></thead><tbody>'.
							'<tr><td colspan="3"> <label>Question 1:</label>'.
							'<input type="text" id="Q1text"></td></tr>'.
							'<tr><td colspan="3"> <label>Question 2:</label>'.
							'<input type="text" id="Q2text"></td></tr>'.
							'<tr><td colspan="3"> <label>Question 3:</label>'.
							'<input type="text" id="Q3text"></td></tr>'.
							'<tr><td colspan="5"><center><button id="updateQuestions">Update Course Questions'.
							'</button></center></td></tr>'.'<tr><td>Remove a course: </td>'.
							'<td><button id="deleteCourse" onclick="deleteItem('."'".'Course'."'".')">'.
							'Delete Selected Course</button></td></tr>'.
							'<tr><td>Change Course'."'".'s Instructor to: </td>'.
							'<td><select id="changeInstructor">'.$instStringBody.'</td><td>'.
							'<button id="changeCourseIns" onclick="changeCourseIns()">Update Teacher</button>'.
							'</tr></tbody></table></td><td>'.
							'<form id="addCourseForm"><table>'.
							'<thead><tr><th colspan="6" align="center">Add a Course: '.
							'</td></th></thead></tr><tr><td> Course Code:</td>'.
							'<td><input type="text" name="courseCode" id="courseCode" size="7"></td>'. 
							'<td> Title:</td>'.
							'<td colspan="3"><input type="text" name="courseTitle" id="courseTitle" size="40">'.
							'</td></tr>'.'<tr><td> Term Offered:</td><td><select id="courseTerm">'.
							'<option value="W">Winter</option><option value="F">Fall</option>'.
							'<option value="S">Summer</option></select></td>'.
							'<td> Instructor:</td><td>'.$instStringHead.'</td><td> Campus:</td><td>'.
							$Campuses.'</td></tr><tr><td>TA Positions: </td><td>'.$Positions.'</td>'.
							'<td>Year: </td><td>'.$years.'</td></tr>'.
							'<tr><td> Question 1:</td>'.
							'<td colspan="5"><input type="text" name="Question1" id="question1" size="63">'.
							'</td></tr><tr><td> Question 2:</td>'.
							'<td colspan="5"><input type="text" name="Question2" id="question2" size="63">'.
							'</td></tr><tr><td> Question 3:</td>'.
							'<td colspan="5"><input type="text" name="Question3" id="question3" size="63">'.
							'</td></tr></table><center><input type="submit" id="addCourse" value="Add course"></center></form></td></tr></table>';
		}
		elseif($_SESSION['role'] == 'APPLICANT'){
			$returnString.= '<center><button id="apply">Apply For Selected Course</botton></center><br>';
		}
		echo $returnString;
		mysqli_free_result($result);
	}

	/* Retieving just the questions for the given course */
	if(isset($_POST['Courses']) && (isset($_POST['Questions']))){
		$returnString = array('test2','','');
		$query = 'SELECT Question_1,Question_2,Question_3 FROM Course 
			      WHERE CID='.$_POST['Questions'].';';
		$result = mysqli_query($dbconnect, $query);
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString[0] = htmlspecialchars_decode($row[0]);
			$returnString[1] = htmlspecialchars_decode($row[1]);
			$returnString[2] = htmlspecialchars_decode($row[2]);
		}
		echo json_encode($returnString);
	}

	/* The HTML for the application form to be filled out. */
	if(isset($_POST['ApplyRequest'])){
		$query = 'SELECT Question_1,Question_2,Question_3 FROM COURSE WHERE CODE='.$_POST['RowCourse'].';';
		$result = mysqli_query($dbconnect, $query);
		$row =  mysqli_fetch_array($result, MYSQLI_NUM);
		$returnString = '<center><form id="applyForm"><table id="applyTable"><tr><th align="center" colspan="2">'.
				'Application for '.trim($_POST["RowCourse"],'"').' ('.trim($_POST["RowTerm"],'"').')'.
				'</th></tr>';
				if(!(strlen($row[0])==1) && !($row[0]=='null')){
					$returnString .= '<tr><td>'.$row[0].'</td></tr>'.
					'<tr><td><textarea id="answer1" cols="40" rows="4"></textarea></td></tr>';
				}
				if(!(strlen($row[1])==1) && !($row[1]=='null')){
					$returnString .= '<tr><td>'.$row[1].'</td></tr>'.
				'<tr><td><textarea id="answer2" cols="40" rows="4"></textarea></td></tr>';
				}
				if(!(strlen($row[2])==1) && !($row[2]=='null')){
					$returnString .= '<tr><td>'.$row[2].'</td></tr>'.
					'<tr><td><textarea id="answer3" cols="40" rows="4"></textarea></td></tr>';
				}
		$returnString .= '<tr><td> Grade you received in this course? (0-100): </td>'.
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

		if(isset($_POST['Answer1'])){
			$Answer1 =  mysqli_real_escape_string($dbconnect,$_POST['Answer1']);
		}else{
			$Answer1 = 'null';
		}
		if(isset($_POST['Answer2'])){
			$Answer2 = mysqli_real_escape_string($dbconnect,$_POST['Answer2']);
		}else{
			$Answer2 = 'null';
		}
		if(isset($_POST['Answer3'])){
			$Answer3 = mysqli_real_escape_string($dbconnect,$_POST['Answer3']);
		}else{
			$Answer3 = 'null';
		}

		if(isset($_POST['Overwrite'])){
			$query = 'SELECT AID FROM APPLICATIONS NATURAL JOIN ANSWERS WHERE CID='.
				      $_POST['RowId'].';';
			$result = mysqli_query($dbconnect, $query);
			$row =  mysqli_fetch_array($result, MYSQLI_NUM);

			$query2 = 'UPDATE ANSWERS SET ANSWER_1="'.htmlspecialchars($Answer1).'",ANSWER_2="'.
					  htmlspecialchars($Answer2).'",ANSWER_3="'.htmlspecialchars($Answer3).
					  '",MARK_RECEIVED='.$_POST['Grade'].' WHERE AID='.$row[0].';';
			mysqli_query($dbconnect, $query2);
			$query3 = 'UPDATE APPLICATIONS SET APP_DATE='.$_POST['AppDate'].' WHERE CID='.$_POST['RowId'].
					' AND UTORID="'.$_SESSION['user'].'";';
			mysqli_query($dbconnect, $query3);
			echo $query3;
		}else{
			/* Adding the answers from post into Answers table. */
			$query = 'INSERT INTO ANSWERS VALUES('.($row[0] + 1).',"'.$Answer1.'","'.$Answer2.
				   	 '","'.$Answer3.'",'.$_POST['Grade'].');';
			mysqli_query($dbconnect, $query);
			
			/* Adding the Application info into applications table. */
			$query = 'INSERT INTO APPLICATIONS VALUES ("'.$_SESSION['user'].'",'.$_POST['RowId']. ',"'.
					($row[0] + 1).'","Pending",'.$_POST['AppDate'].');';
			

			if(!(mysqli_query($dbconnect, $query))){
				$query = 'DELETE FROM ANSWERS WHERE AID='.($row[0] + 1).';';
				mysqli_query($dbconnect, $query);
				echo "alreadySubmitted()";
			}else{
				echo "<br><p>Thanks, Your Application has been submitted!</p>";
			}
		}		
	}

	if(isset($_POST['All_Applications'])) {
		$returnString = 'getTags()<div id="tableWrap"><table id="allAppTable"><thead>'.
						'<th align="center" colspan="8">'.'ALL APPLICATIONS</th><tr><td id="appUtorid">UTORID</td>'.
						'<td id="appCode">Course Code</td><td id="appTerm">Term</td>'.
						'<td id="appYear">Year</td><td id="appInstructor">Instructor</td><td id="appCampus">Campus</td>'.
						'<td id="appStatus">Status</td><td id="appDate">Date</td></tr></thead><tbody>';	
	
		if(isset($_POST['ChangeTag'])){
			
			if($_POST['TagValue'] == '"Yes"'){
				$query = 'SELECT CID,Total_Positions,POSITIONS_AVAILABLE FROM'.
						 ' (SELECT * FROM APPLICATIONS NATURAL JOIN COURSE) AS TEST WHERE CODE='.
						 $_POST['TagCourse'].' AND UTORID='.$_POST['TagUtorid'].
					  	' AND SEMESTER='.$_POST['TagTerm'].' AND YEAR='.$_POST['TagYear'].';';
				
				$result = mysqli_query($dbconnect, $query);
				$row =  mysqli_fetch_array($result, MYSQLI_NUM);

				if($row[2] > 0){
					
					$query =  'UPDATE APPLICATIONS SET TAG='.$_POST['TagValue'].' WHERE CID='.
					  	'(SELECT CID FROM (SELECT * FROM APPLICATIONS NATURAL JOIN COURSE) AS TEST WHERE CODE='.
					  	$_POST['TagCourse'].' AND UTORID='.$_POST['TagUtorid'].
					  	' AND SEMESTER='.$_POST['TagTerm'].' AND YEAR='.$_POST['TagYear'].
					  	') AND UTORID='.$_POST['TagUtorid'].';';
					mysqli_query($dbconnect, $query); 
					
					$query2 = 'UPDATE COURSE SET POSITIONS_AVAILABLE='. ($row[2]-1).
							  ' WHERE CID='.$row[0].';';
					mysqli_query($dbconnect, $query2);
				}
			}
			else{
				$query =  'UPDATE APPLICATIONS SET TAG='.$_POST['TagValue'].' WHERE CID='.
					  	'(SELECT CID FROM (SELECT * FROM APPLICATIONS NATURAL JOIN COURSE) AS TEST WHERE CODE='.
					  	$_POST['TagCourse'].' AND UTORID='.$_POST['TagUtorid'].
					  	' AND SEMESTER='.$_POST['TagTerm'].' AND YEAR='.$_POST['TagYear'].
					  	') AND UTORID='.$_POST['TagUtorid'].';';
					mysqli_query($dbconnect, $query);

				
				if(!($_POST['TagValue'] == '"Yes"') && ($_POST['OldTag'] == '"Yes"')){ 
					$query = 'SELECT CID,Total_Positions,POSITIONS_AVAILABLE FROM'.
						 ' (SELECT * FROM APPLICATIONS NATURAL JOIN COURSE) AS TEST WHERE CODE='.
						 $_POST['TagCourse'].' AND UTORID='.$_POST['TagUtorid'].
					  	' AND SEMESTER='.$_POST['TagTerm'].' AND YEAR='.$_POST['TagYear'].';';
					$result = mysqli_query($dbconnect, $query);
					$row =  mysqli_fetch_array($result, MYSQLI_NUM);

					$query2 = 'UPDATE COURSE SET POSITIONS_AVAILABLE='. ($row[2]+1).
							  ' WHERE CID='.$row[0].';';
					mysqli_query($dbconnect, $query2);
				}
			}
		}

		if (isset($_POST['Sort'])){
			$query = 'SELECT UTORID,CODE,SEMESTER,YEAR,INSTRUCTOR,CAMPUS,TAG,APP_DATE '.
				 'FROM (COURSE NATURAL JOIN APPLICATIONS) ORDER BY ' . $_POST['SortValue'] . ';';
		}else{		
			$query = 'SELECT UTORID,CODE,SEMESTER,YEAR,INSTRUCTOR,CAMPUS,TAG,APP_DATE '.
				 'FROM (COURSE NATURAL JOIN APPLICATIONS);';
		}

		/* Create select option of Tag values */
		$tags = '<select class="tagSelect"><option value="Pending">Pending</option>
										 <option value="Granted">Granted</option>
										 <option value="Maybe">Maybe</option>
										 <option value="No">No</option></select>';
		$tagValues = array();
		$result = mysqli_query($dbconnect, $query);
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i<=7; $i++){
				if($i==0){
					$returnString .= '<td onclick="getProfile('."'".$row[$i]."')".'"'.
								' id="student">'.
									 $row[$i].'</td>';
				}
				elseif($i==6){
					$returnString .= '<td class="selectTd">'.$row[$i].'</td>';
				}elseif($i==7){
					$returnString .= '<td>'.substr($row[$i],0,15).'</td>';
				}else{	
					$returnString .= '<td>'.$row[$i].'</td>';
				}
			}
			$returnString .= '</tr>';
		}
		$returnString.= '</tbody></table></div><table id="viewProfileTable">'.
					'<thead><th align="center" colspan="5">'.
						'Selected Application: </th></thead><br><tr><td><center>'. 
						'<br><button id="viewProfile"onclick="getProfile('."'Instructor')".'">'.
						'View Profile</button></center><br></td></tr>';

		$returnString.= '<tr><td colspan="3"> <label>Question 1: </label>'.
						'</td></tr>'.
						'<tr><td colspan="3"> <label>Question 2: </label>'.
						'</td></tr>'.
						'<tr><td colspan="3"> <label>Question 3: </label>'.
						'</td></tr>'.
						'<td><br></td>'.
						'<tr><td><center>Sort Applications by: <label for="Sort"></label><select id="appSort">
						<option value="UTORID">	UTORID</option>
						<option value="Code"> Course Code</option>
						<option value="Semester"> Term</option>
						<option value="Year"> Year</option>
						<option value="Instructor"> Instructor</option>
						<option value="Tag">Status</option></select></center></td></tr></table>';
		
		
		echo $returnString;
	}
	
	if(isset($_POST['My_Applications'])) {

		$returnString = '<table id="myAppTable"><thead><th align="center" colspan="6">'.
						'MY APPLICATIONS</th><tr><td>Course Code</td><td>Term</td>'.
						'<td>Year</td><td>Instructor</td><td>Campus</td>'.
						'<td>Date</td></tr></thead><tbody>';

		$query = 'SELECT AID,CID,CODE,SEMESTER,YEAR,INSTRUCTOR,CAMPUS,APP_DATE '.
				 'FROM (COURSE NATURAL JOIN APPLICATIONS) NATURAL JOIN ANSWERS '.
				 'WHERE UTORID="'.$_SESSION['user'].'" ORDER BY CODE;';
		$result = mysqli_query($dbconnect, $query);
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i <=7; $i++){
				if($i == 0){
					$returnString .= '<td style="display: none" id="aid";>'.$row[$i].'</td>';
				}elseif($i == 1){
					$returnString .= '<td style="display: none" id="cid";>'.$row[$i].'</td>';
				}elseif($i == 7){
					$returnString .= '<td>'.substr($row[$i],0,15).'</td>';
				}
				else{
					$returnString .= '<td>'.$row[$i].'</td>';
				}
			}
			$returnString .= '</tr>';
		}
		$returnString.= '</tbody></table><br>'.
						'<table id="editAppTable"><thead><th align="center" colspan="5">'.
						'Selected Application </th>'. 
						'<tr><td><center><button id="editApp"onclick="">'.
						'Edit Application</button></td></tr></center>'.
						'<tr><td><center><button id="deleteApp"onclick="">'.
						'<center>Delete Application</button></td></tr></center></table><br></thead>';
		echo $returnString;
	}


	/* ==================== Users Request ======================== */

	if(isset($_POST['Users'])){


		if (isset($_POST['Delete'])){
			$query = 'DELETE FROM Users WHERE UTORID="'.$_POST["ID"].'";';
			mysqli_query($dbconnect, $query);
			$query = 'DELETE FROM Profiles WHERE UTORID="'.$_POST["ID"].'";';
			mysqli_query($dbconnect, $query);
			$query = 'DELETE FROM Applications WHERE UTORID="'.$_POST["ID"].'";';
			mysqli_query($dbconnect, $query);
		}

		if (isset($_POST['Add'])){
			$query = 'INSERT INTO Users (UTORID,FNAME,LNAME,ROLE,EMAIL,Password) VALUES("'.
					$_POST["UserUtorid"].'","'.$_POST["UserFname"].'","'.$_POST["UserLname"].
					'","'.$_POST["UserRole"].'","'.$_POST["UserEmail"].'","'.
					md5($_POST["UserPassword"]).'");';
			mysqli_query($dbconnect, $query);
		}

		$query = 'SELECT * FROM Users;';
		/* could add sort feature */

		$result = mysqli_query($dbconnect, $query);
		$returnString = '<div id="tableWrap"><table id="userTable">'.
						'<thead><th align="center" colspan="5">CURRENT USERS</th>'.
						'<tr><td id="userUtorid">UTORID</td><td id="userOther">First Name</td>'.
						'<td id="userOther">Last Name</td><td id="userOther">Role</td>'.
						'<td id="userEmail">Email Address</td></thead><tbody>';
		while ($row =  mysqli_fetch_array($result, MYSQLI_NUM)){
			$returnString .= '<tr>';
			for($i=0; $i <=4; $i++){
				if($i==0 && $row[3]=='APPLICANT'){
					$returnString .= '<td onclick="getProfile('."'".$row[$i]."')".'"'.
									' id="applicant">'.
									 $row[$i].'</td>';
				}elseif($i==4){
					$returnString .= '<td id="userEmail">'.$row[$i].'</td>';
				}
				else{
					$returnString .= '<td id="userOther">'.$row[$i].'</td>';
				}
			}
			$returnString .= '</tr>';
		}
		$returnString .= '<tr></tbody></table></div><br><table><tr><td><table id="removeUserTable">'.
						'<thead><tr><th colspan="6" align="center">Selected User:</tr></th></thead>'.
						'<td><center><br><button id="deleteUser" onclick="deleteItem('."'".'User'."'".')">'.
		                'Remove Selected User</button></center><br></td></table></td><td>'.
						'<form id="addUserForm"><table id="addUserTable">'.
						'<thead><tr><th colspan="6" align="center">Add a User:</tr></th></thead>'.
						'<td> Utorid:</td>'.
						'<td><input type="text" name="userUtoridText" id="userUtoridText" size="10"'.
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
						'</td></tr>'.'<tr><td>Email Address:</td>'.
						'<td colspan="3"><input type="text" name="email" id="email" size="40"'.
						' placeholder="UTOR Email Preferably"></td></tr>'.
						'<tr><th colspan="6" align="center" style="border-bottom: 0;">'.
						'<input type="submit" id="addUser" value="Add User">'.
						'</th></tr></table></form></td></tr></table';
		echo $returnString;
		mysqli_free_result($result);
	}


	/* ==================== Profiles Request ======================== */

	if(isset($_POST['Profiles'])){
		if(isset($_POST['GetProfile'])){

			$query = 'SELECT UTORID,FNAME,LNAME,STATUS,YEAR_STUDY,CHOICE1,CHOICE2,CHOICE3,CHOICE4,
				 CHOICE5,TA_EXP,VOLUNTEER,BLURB,EMAIL FROM (USERS NATURAL JOIN PROFILES) WHERE UTORID="'.
				 $_POST['ProfileId'].'";';
			$result = mysqli_query($dbconnect, $query);
			$returnString = '<div id="editMyProfile"><form id="editApplication"><table id="myProfileTable"><tr>'.
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
					'<tr><td><b>Student Status:</b><td><select id="studentStatus">'.
					'<option value="Undergrad">Undergrad</option>'.
					'<option value="Graduate">Graduate</option>'.
					'<option value="Non-Student">Non-Student</option></select></td>'.
					'<td><b>Year of Study:</b></td><td><select id="pYear"'.
					'style="width: 75px;"><option value="1">1</option>'.
					'<option value="2">2</option><option value="3">3</option>'.
					'<option value="4">4</option><option value="5">5</option>'.
					'<option value="6">6</option><option value="7">7</option></select></tr>'.
					'<tr><td><b>1st Choice:</b></td> <td>'.$course1.'</td>'.
					'<td><b>2nd Choice:</b></td><td>'.$course2.'</td>'.
					'<td><b>3rd Choice:</b></td><td>'.$course3.'</td></tr>'.
					'<tr><td><b>4th Choice:</b></td><td>'.$course4.'</td>'.
					'<td><b>5th Choice:</b></td><td>'.$course5.'</td></tr>'.
					'<tr><center><th colspan="6">My Past TA Experience</center><th></tr>'.
					'<tr><td colspan="6"><textarea id="pTAExp" style="width: 100%;" maxlength="995";'.
					'cols="25" rows="4"></textarea></td></tr>'.
					'<tr><center><th colspan="6">My Extracurricular/Volunteer Activites</center><th></tr>'.
					'<tr><td colspan="6"><textarea id="pTAVol" style="width: 100%;" maxlength="1995";'.
					'cols="25" rows="4"></textarea></td></tr>'.
					'<tr><center><th colspan="6">Why I'."'".'d Make a Good TA?</center><th></tr>'.
					'<tr><td colspan="6"><textarea id="pTAWhy" style="width: 100%;" maxlength="3995";'.
					'cols="25" rows="4"></textarea></td></tr><tr><td>My email address: </td>'.
					'<td colspan="5"><input type="text" id="userEmail" style="width: 100%;"></td></tr><tr><td>'.
					'<input type="submit" id="submitPro" value="Update Profile">'.'</td></tr></table></form></table>'.
					'placeValues()';
			}else{
				/* Return message when profile is empty */
				if(mysqli_num_rows($result) == 0){
					$returnString.= 'THIS PROFILE IS EMPTY!</th></tr></table></form>';
				}else{ 
					while($row = mysqli_fetch_array($result, MYSQLI_NUM)){
						$returnString.= $row[0]. "'s Profile</th></tr>".'<tr><td id="fName"><b>First Name: </b>'.
								$row[1].'</td></tr><tr><td id="lName"><b>Last Name:</b> '.$row[2].'</td></tr>'.
								'<tr><td><b>First Choice: </b></td><td id="choice1">'.$row[5].'</td></tr>'.
								'<tr><td><b>Second Choice: </b></td><td id="choice2">'.$row[6].'</td></tr>'.
								'<tr><td><b>Third Choice: </b></td><td id="choice3">'.$row[7].'</td></tr>'.
								'<tr><td><b>Fourth Choice: </b></td><td id="choice4">'.$row[8].'</td></tr>'.
								'<tr><td><b>Fifth Choice: </b></td><td id="choice5">'.$row[9].'</td></tr>'.
								'<tr><td id="pStatus"><b>Student Status:</b></td><td>'.$row[3].'</td>'.
								'<td colspan="6" id="studyYear"><b>My Current Year of Study: </b> '.
								$row[4].'</td></tr>'.
								'<td><br></td><br>'.
								'<tr><td colspan="6"  id="taExp"><b>My Past TA Experience: </b><p>'.
								$row[10].'</p></td></tr>'.
					 			'<tr><td colspan="6"  id="taVol"><b>My Extracurricular / Volunteer Activities: '.
					 			' </b><p>'.$row[11].'</p></td></tr>'.
								'<tr><td colspan="6" id="taWhy"><b>Why Would I Make a Good TA?: </b><p>'.
								$row[12].'</p></td></tr>'.'<tr>';
						if(!($_SESSION['role'] == "APPLICANT")){
							$returnString.= '<td>Send Email to '.$row[1].'?</td><td><a href="mailto:'.$row[13].
										'?Subject=TA%20Application" taget="_blank">'.$row[13].'</a>';
						}else{
							$returnString.= '<td><b>My email address: </b></td><td>'.$row[13];
						}
						$returnString.= '</td></tr></table></form>';
					}
				}
			}
			
			/* Only display edit button for the owner of the profile */
			if(($_SESSION['role'] == 'APPLICANT') && !(isset($_POST['EditProfile']))){
				$returnString.= '<button id="editProfile" onclick="editProfile()">'.
								'Edit Profile</button><td><br></td><br>';
			}
			echo $returnString;	
		}

		if(isset($_POST['UpdateProfile'])){
			$Answer1 =  mysqli_real_escape_string($dbconnect,$_POST['TaExp']);
			$Answer2 =  mysqli_real_escape_string($dbconnect,$_POST['TaVol']);
			$Answer3 =  mysqli_real_escape_string($dbconnect,$_POST['TaWhy']);
			$Answer4 =  mysqli_real_escape_string($dbconnect,$_POST['Email']);

			$query = 'SELECT COUNT(*) FROM PROFILES WHERE UTORID="'.$_SESSION['user'].'";';
			$result = mysqli_query($dbconnect, $query);
			$row = mysqli_fetch_array($result, MYSQLI_NUM);
			if($row[0] == 0){
				$query = 'INSERT INTO PROFILES VALUES(NULL,"'.$_SESSION['user'].'","'.$_POST['Status'].'","'.
				$_POST['Year'].'","'.$_POST['Choice1'].'","'.$_POST['Choice2'].'","'.$_POST['Choice3'].
				'","'.$_POST['Choice4'].'","'.$_POST['Choice5'].'","'.htmlspecialchars($Answer1).
				'","'.htmlspecialchars($Answer2).'","'.htmlspecialchars($Answer3).'");';
				mysqli_query($dbconnect, $query);
			}
			else{
				$query = 'UPDATE PROFILES SET STATUS="'
						.$_POST['Status'].'", YEAR_STUDY="'.$_POST['Year'].
						'", CHOICE1="'.$_POST['Choice1']. '", CHOICE2="'.$_POST['Choice2'].
						'", CHOICE3="'.$_POST['Choice3']. '", CHOICE4="'.$_POST['Choice4'].
						'", CHOICE5="'.$_POST['Choice5']. 
						'", TA_EXP="'.htmlspecialchars($Answer1).
						'", VOLUNTEER="'.htmlspecialchars($Answer2).
						'", BLURB="'.htmlspecialchars($Answer3).'" WHERE UTORID="'.
						$_SESSION['user'].'";';
				mysqli_query($dbconnect, $query);
			}
			$query2 = 'UPDATE USERS SET EMAIL="'.htmlspecialchars($Answer4).'" WHERE UTORID="'.
					$_SESSION['user'].'";';
			mysqli_query($dbconnect, $query2);
		}
	}
	if(isset($_POST['OtherPage'])){
		$returnString = '<table id="csvTable"><form id="csvForm"><th>Upload a course CSV file</th>'.
						'<tr><td><input id="file-input" type="file" name="name"/></td</tr>'.
						'<tr><td><button id="uploadButton">Upload Courses</button></td></tr></form></table>'.'<br><table id="clearSemTable"><td>Clear Semester: <br></td><td><button id="clearSemester">Clear Semester</button></td></table>';
		echo $returnString;
	}
	if(isset($_POST['CLEARSEMESTER'])){
		$query = 'DELETE FROM ANSWERS;';
		mysqli_query($dbconnect, $query);
		$query = 'DELETE FROM APPLICATIONS;';
		mysqli_query($dbconnect, $query);
		$query = 'DELETE FROM COURSE;';
		mysqli_query($dbconnect, $query);
	}
	if(isset($_POST['CSV'])){
		$query = 'DELETE FROM ANSWERS;';
	}
?>
