
/* ==================== Document.ready ======================== */

/* Event listener that look for actions on page */
$(document).ready(function(){
	console.log("document.ready");

	/* When login form is submitted*/
	$("#login_form").submit(function(page){
		page.preventDefault();
		login();
	});

	/* When Logout button is pressed*/
	$("#logout").click(function(page){
		page.preventDefault();
		logout();
	});

	/* For testing purposes displayed Session info */
	$("#getInfo").click(function(page){
		page.preventDefault();
		getInfo();
	});
});

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
			$("#content").text(response);
		}
	})
	//getInfo();
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
		success: function(response){
			$("#content").text(response);
		}
	})
	//getInfo();
}

// function called to retrieve session variables
function getInfo(){
	var loginString = "LoggedIn=True&User=True&GetInfo=True";
	console.log(loginString);

	$.ajax({
		type: "POST",
		url: "connect2db.php",
		data: loginString,
		success: function(response){
			$("#content").text(response);
			console.log(response);
		}

	})
}

