
/* ==================== Document.ready ======================== */

/* Event listener that look for actions on page */
$(document).ready(function(){
	console.log("document.ready");
	getInfo();

	/* When login form is submitted*/
	$("#contentForm").submit(function(page){
		page.preventDefault();
		login();
	});

	/* When Logout button is pressed*/
	$("#logout").click(function(){
		logout();
		console.log('logout button just pressed');
	});
});

selectedRow = null;
$(document).on("click", "tr",function() {
	if (selectedRow == null){} 
	else{
		selectedRow.css("background-color", "white");
	}
	$(this).css("background-color", "#ff8533");
	selectedRow = $(this);
});


/* Add event listeners here for different page buttons
	- call display info with each button press */

/* ==================== User Functions ======================== */

/* Function creates a JSON word and sends it to
	connect2b.php to find $_SESSION variables about
	current user loging in. */
function login(){
	var utorId = $("#utorid").val();
	var password = $("#password").val();
	var loginString = "Login=True&UTORID=";
	loginString += (utorId + '&password=' + password);
	$.ajax({
		type: "POST",
		url: "connect2db.php",
		data: loginString,
		success: function(response){
			getInfo();
		}
	})
}

/* Function creates a JSON word and sends it to
	connect2b.php to find $_SESSION variables about
	current user loging in. */
function logout(){
	var loginString = "Logout=True";
	$.ajax({
		type: "POST",
		url: "connect2db.php",
		data: loginString,
	})
	location.reload();
}

/* function called to retrieve session variables
	then calls displayButtons() with appropraite 
	page data based on role assigned */
function getInfo(){
	var loginString = "LoggedIn=True&User=True&GetInfo=True&Role=True";

	$.ajax({
		type: "POST",
		url: "connect2db.php",
		data: loginString,
		success: function(response){
			console.log("response: ",response);
			Role = response.split('&')[2].substr(5,20);
			console.log("role: ", Role);
			if (response.split('&')[0].substr(9,15) == 'false'){
				displayButtons("NOTLOGGEDIN");
			}else{
				displayButtons(Role);
			}
		}
	})
}


/* ==================== Page Display Functions ======================== */

/* Switches content of the page, based on role of the user; uses a 
	global variable from below to populate the 'content' div */
function displayButtons(role){
	console.log('displayInfo role:' + role);
	switch(role){
		case "ADMIN":
			$("#pageButtons").html(adminPages);
			$("#contentForm").hide();
			displayPageInfo("Courses");
			break;
		case "INSTRUCTOR":
			$("#pageButtons").html(instructorPages);
			$("#contentForm").hide();
			displayPageInfo("Courses");
			break;
		case "APPLICANT":
			$("#pageButtons").html(applicantPages);
			$("#contentForm").hide();
			displayPageInfo("Courses");
			break;
		case "NOTLOGGEDIN":
			$("#contentForm").html(loginTable);
			break;
	}
}

function displayPageInfo(page){
	console.log("displayCourses() called.");
	var postString = page+"=True";

	$.ajax({
		type: "POST",
		url: "Controller.php",
		data: postString,
		success: function(response){
			console.log("about to print: " + response);
			$("#pageInfo").html(response);
		},
		error: function(){
			$("#pageInfo").html('<p>Error connecting to database</p>');
		}
	})
}

function deleteCourse(){
	var deleteString = "ID=";
	var i = 0;
	selectedRow.find('td').each(function(){
		if (i==0){
			deleteString += $(this).text();
			i ++;
		}
	});
	deleteString += '&Delete=True&Courses'
	console.log(deleteString);
	displayPageInfo(deleteString);
}

/* ==================== Page Content Global Variables ======================== */

/* HTML for login table. Displayed when $_SESSION['loggedIn'] == False */
var loginTable ='<table id="login_table" style="border:2px solid black;">' +
				'<tr><th align="center" colspan="2">Login using a valid UTORID</th></tr>' +
				'<tr><td allign="right"> UTORID:</td> <td>' +
				'<input type="text" name="utorid" id="utorid" size="8"> </td></tr>' +
				'<tr><td allign="right"> Password:</td><td>' +
				'<input type="password" name="password" id="password" size="15"> </td></tr>' +
				'<tr><td align="right" colspan="2"> <input type="submit" id="login" value="Login"> '  +
				'</button></td></tr></table>';

/* HTML for page buttons for instructors. Displayed when $_SESSION['User'] == 'INSTRUCTOR' */
var instructorPages = '<center><table id="instButtons"><tr><td>' +
					  '<button id="coursePage" onclick="displayPageInfo(' + "'Courses'" +')">Courses</botton>' +
					  '</td><td></td><td><button id="appPage">Applicants</botton></td><td></td>' +
					  '<td><button id="userPage">Add User</botton></td></tr></table></center>';

/* HTML for page buttons for applicants. Displayed when $_SESSION['User'] == 'APPLICANT' */
var applicantPages = '<center><table id="appButtons"><tr><td><button id="coursePage">Courses</botton>' +
					  '</td><td></td><td><button id="profile">Profile</botton></td><td></td>' +
					  '<td><button id="contact">Contact</botton></td></tr></table></center>';

var adminPages = '<center><table id="admButtons"><tr><td>' +
				 '<button type="button" id="coursePage" onclick="displayPageInfo(' + "'Courses'" +')">Courses</botton>' +
				 '</td><td></td><td><button id="usersPage" onclick="displayPageInfo(' + "'Users'" +')">Users</botton></td><td></td>' +
				 '<td><button id="statsPage" onclick="displayStats()">Stats</botton></td></tr></table></center>';
