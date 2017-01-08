// sets up Jquery mesages when changing pages

$(document).ready(function(){
	console.log("document.ready");
	$("#login_form").submit(function(page){
		page.preventDefault();
		login();
		//console.log("in login_form submit");
	});
});



function login(){
	var utorId = $("#utorid").val();
	var password = $("#password").val();
	var loginString = "Login=true&UTORID=";
	loginString += (utorId + '&password=' + password);
	console.log(loginString);
	$.ajax({
		type: "POST",
		url: "connect2db.php",
		data: loginString,
		success: function(response){
			$("#content").text(response);
		}
	})
}

function makeMessage(page){
	//getInfo();
	document.getElementById('content').innerHTML = page;
}

// function called to retrieve session variables
function getInfo(){

}
// Main Page


// Application Page


// My Profile Page