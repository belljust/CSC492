
/* ==================== Document.ready ======================== */

/* Event listener that look for actions on page */
$(document).ready(function(){
	console.log("document.ready");

	getInfo();

	/* When login form is submitted*/
	setInterval(function(){
		$("#login").click(function(page){
			console.log("login button clicked");
			login();
		});
	}, 1000);


	/* When Logout button is pressed*/
	$("#logout").click(function(page){
		logout();
		console.log('logout button just pressed');
	});

	/* For testing purposes displayed Session info */
	$("#getInfo").click(function(page){
		page.preventDefault();
		console.log("getInfo button just pressed");
		getInfo();
	});
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
	then calls displayInfo() with appropraite 
	page data based on role assigned */
function getInfo(){
	var loginString = "LoggedIn=True&User=True&GetInfo=True&Role=True";
	//console.log(loginString);

	$.ajax({
		type: "POST",
		url: "connect2db.php",
		data: loginString,
		success: function(response){
			console.log("response: ",response);
			Role = response.split('&')[2].substr(5,20);
			console.log("role: ", Role);
			displayInfo(Role);
		}
	})
}

/* Switches content of the page, based on role of the user; uses a 
	global variable from below to populate the 'content' div */
function displayInfo(role){
	switch(role){
		case "ADMIN":
			$("#content").replaceWith("ADMIN PAGE");
		case "INSTRUCTOR":
			$("#content").replaceWith("INSTRUCTOR PAGE");
		case "APPLICANT":
			$("#content").replaceWith("APPLICANT PAGE");
		case "NOTLOGGEDIN":
			$("#content").html(loginTable);
		case "":
			$("#content").html(loginTable);
	}
}

/* ==================== Page Content Global Variables ======================== */

/* HTML for login table. Displayed when $_SESSION[loggedIn] == False */
var loginTable = 
				'<table id="login_table" style="border:2px solid black;">' +
				'<tr><th align="center" colspan="2">Login using a valid UTORID</th></tr>' +
				'<tr><td allign="right"> UTORID:</td> <td>' +
				'<input type="text" name="utorid" id="utorid" size="8"> </td></tr>' +
				'<tr><td allign="right"> Password:</td><td>' +
				'<input type="password" name="password" id="password" size="15"> </td></tr>' +
				'<tr><td align="right" colspan="2"> <button type="button" id="login"> Login'  +
				'</button></td></tr></table>'



