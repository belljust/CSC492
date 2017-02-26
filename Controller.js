
/* ==================== Document.ready ======================== */

/* Event listener that look for actions on page */
$(document).ready(function(){
	console.log("document.ready");
	getPages();
	displayPageInfo('Courses');
});


/* ==================== Event Listeners ======================== */

/* When login form is submitted*/
$(document).on("submit", "#contentForm",function(page) {
	page.preventDefault();
	login();
	$('#utorid').val('');
	$('#password').val('');
});

/* When hover mouse over table rows*/
$(document).on("hover", "#pageInfo tbody tr",function() {
		$(this).css("background-color", "#ffff80");
});

/* When selecting a row from the table of courses */
selectedRow = null;
$(document).on("click", "#courseTable tbody tr",function() {
	if (selectedRow != null){
		selectedRow.css("background-color", "#aeccfc");
	} 
	$(this).css("background-color", "#ff8533");
	selectedRow = $(this);
});

$(document).on("click", "#allAppTable tbody tr",function() {
	if (selectedRow != null){
		selectedRow.css("background-color", "#aeccfc");
	} 
	$(this).css("background-color", "#ff8533");
	selectedRow = $(this);
});

/* When selecting a row from the table of users */
selectedRow = null;
$(document).on("click", "#userTable tbody tr",function() {
	if (selectedRow != null){
		selectedRow.css("background-color", "#aeccfc");
	} 
	$(this).css("background-color", "#ff8533");
	selectedRow = $(this);
});


/* When Logout button is pressed*/
$(document).on("click","#logout",function(){
	logout();
	console.log('logout button just pressed');
});


/* When adding a new course*/
$(document).on("submit", "#addCourseForm",function(page) {
	page.preventDefault();
	addItem('Course');
});

/* When adding a new User*/
$(document).on("submit", "#addUserForm",function(page) {
	page.preventDefault();
	addItem('User');
});

$(document).on("click","#apply",function(page){
	page.preventDefault();
	courseApply();
});

$(document).on("submit", "#applyForm",function(page) {
	page.preventDefault();
	submitApplication();
});

$(document).on("submit", "#editApplication",function(page) {
	page.preventDefault();
	updateProfile();
});

$(document).on("focus", "#myProfileTable select",function() {
	currentVal = $(this).val();
});
$(document).on("change", "#myProfileTable select",function() {
	checkValues(this.id,currentVal,$(this).val());

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
			if (!(response == "Incorrect Password" ||
				response == "User Not Found")){
				getPages();
				displayPageInfo('Courses');
			}
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
		success: function(){
			location.reload();
		}
	})
}

/* function called to retrieve session variables
	then calls displayButtons() with appropraite 
	page data based on role assigned */
function getPages(){
	var loginString = "GetPages=True";

	$.ajax({
		type: "POST",
		url: "connect2db.php",
		data: loginString,
		success: function(response){		
			$("#pageButtons").html(response);
		}
	})
}


/* ==================== Page Display Functions ======================== */


/* The function that actually does the sending of the variables through 
	an Ajax call to Controller.php based on collected information stored
	in the 'page' paramter sent from other functions in Controller.js.*/
function displayPageInfo(page){
	var postString = page+"=True";
	console.log(postString);
	$.ajax({
		type: "POST",
		url: "Controller.php",
		data: postString,
		success: function(response){
			//console.log(response);
			$("#pageInfo").html(response.replace('placeValues()',''));
			if(response.includes("placeValues()")){
				placeValues();
			}
		},
		error: function(){
			$("#pageInfo").html('<p>Error connecting to database</p>');
		}
	})
}

/* Function called to send values of the selected Course from the course
table to Controller.php to which is deleted from the database.*/
function deleteItem(item){
	if(selectedRow == null){
		alert("There's no row selected!");
	}
	else{
		var deleteString = "Delete=True&ID=";
		var i = 0;
		selectedRow.find('td').each(function(){
			if (i==0){
				deleteString += $(this).text();
				i ++;
			}
		});
		if (item == "User"){
			deleteString += '&Users'
		}else{
			deleteString += '&Courses'
		}
		if(confirm("Are you sure you wish to delete this entry?")){
			displayPageInfo(deleteString);
		}
	}
}

/* Function called to send values of the add Course form to Controller.php
	to which is added to the database.*/
function addItem(item){
	if (item == 'Course'){
		var addString = 'CourseCode=' + $('#courseCode').val()
					+ '&CourseTitle=' + $('#courseTitle').val()
					+ '&CourseTerm=' + $('#courseTerm').val()
					+ '&CourseInstructor=' + $('#courseInstructor').val()
					+ '&CourseCampus=' + $('#courseCampus').val()
					+ '&TaPositions=' + $('#numPositions').val()
					+ '&CourseYear=' + $('#courseYear').val()
					+ '&Add=True&Courses';
	}else{
		if(!($("#userPassword").val() == $("#retypePassword").val())){
			$("#errorMessage").text('Your passwords do not match!');
			$("#userPassword").css("background-color", "#ff3333");
			$("#retypePassword").css("background-color", "#ff3333");
			$("#userUtorid").css("background-color", "#ffffff");
			return;

		}else if($("#userUtorid").val().trim() == ''){
			$("#errorMessage").text('Utorid cannot be empty!');
			$("#userUtorid").css("background-color", "#ff3333");
			$("#userPassword").css("background-color", "#ffffff");
			$("#retypePassword").css("background-color", "#ffffff");
			return;

		}else if($("#userFname").val().trim() == ''){
			$("#errorMessage").text('Your first name cannot be empty!');
			$("#userUtorid").css("background-color", "#ff3333");
			$("#userPassword").css("background-color", "#ffffff");
			$("#retypePassword").css("background-color", "#ffffff");
			return;
			
		}else if($("#userLname").val().trim() == ''){
			$("#errorMessage").text('Your last name cannot be empty!');
			$("#userUtorid").css("background-color", "#ff3333");
			$("#userPassword").css("background-color", "#ffffff");
			$("#retypePassword").css("background-color", "#ffffff");
			return;
			
		}else{
			var addString = 'UserUtorid=' + $('#userUtorid').val()
						+ '&UserRole=' + $('#userRole').val()
						+ '&UserFname=' + $('#userFname').val()
						+ '&UserLname=' + $('#userLname').val()
						+ '&UserPassword=' + $('#userPassword').val()
						+ '&Add=True&Users';
		}
	}
	if(confirm("Are you sure you wish to add this entry?")){
		$("#errorMessage").text("");
		displayPageInfo(addString);
	}
}

/* Simply sends which instructor needs to be updated */
function changeCourseIns(){
	if(selectedRow == null){
		alert("There's no course selected!");
	}
	else{
		getCourseRowInfo();
		var changeString = 'Instructor=';
		changeString += $('#changeInstructor').val() + '&RowId='
				+ rowId + '&ChangeInstructor=True&Courses';

		if(confirm('Are you sure you wish change this ' + rowCourse +
				"'s instructor to " + $('#changeInstructor').val() + '?')){
			displayPageInfo(changeString);
		}
	}
}

/* Function as of right now just asks if user is she they wish to 
   submit an application for the selected course */ 
function courseApply(){
	if(selectedRow == null){
		alert("There's no course selected!");
	}
	else{
		getCourseRowInfo();
		if(confirm('Are you sure you wish to apply to ' + rowCourse + ' ('
			+ rowTerm + ') with ' + rowInstructor + '?')){
			applyString = 'RowCourse="'+ rowCourse +'"&RowTerm="' + rowTerm 
			+ '"&RowInstructor="' + rowInstructor + '"&Late=0&ApplyRequest=';
			displayPageInfo(applyString);
		}	
	} 
}

/* Takes Apllication form data and submits it */
function submitApplication(){
	if(confirm('Are you sure you wish to submit this application?')){
		applyString = 'NumCourses="'+ $("#numCourses").val() +'"&TaBefore="' 
		+ $("#taBefore").val() + '"&WorkBefore="' + $("#workBefore").val() 
		+ '"&Grade="' + $("#grade").val() + '"&RowId="' + rowId + 
		'"&Late=0&ApplySubmit=';
		displayPageInfo(applyString);
	}
}


/* Function displays a profile of a given person on the page */
function getProfile(person){
	profileString = 'GetProfile=True&ProfileId='; 
	if(person == 'MyProfile'){
		profileString += $('#loggedInUser').text().substr(14,30);
	}
	if(person == 'Instructor'){
		var i=0;
		selectedRow.find('td').each(function(){
			if(i==0){
				profileString += $(this).text();
				console.log(profileString);
			}
			i++;
		});
	}
	profileString += '&Profiles';
	displayPageInfo(profileString);
}

function editProfile(){
	year='', choice1='', choice2='', choice3='', choice4 = '';
	choice5 = '', taExp = '', taVol = '', taWhy = '';
	var i=0;

	$("#myProfileTable").find('td').each(function(){
		switch(i){
        	case 3:
        		choice1 += $(this).text();
        		break;	
        	case 5:
        		choice2 += $(this).text();
        		break;
        	case 7:
        		choice3 += $(this).text();
        		break;
        	case 9:
        		choice4 += $(this).text();
        		break;
        	case 11:
        		choice5 += $(this).text();
        		break;
        	case 12:
        		year += $(this).text().substr(27,50);
        		break;
        	case 13:
        		taExp += $(this).text().substr(23,1000);
        		break;
        	case 14:
        		taVol += $(this).text().substr(42,1000);
        		break;
        	case 15:
        		taWhy += $(this).text().substr(29,1000);
        		break;
		}
		i++;
	});
	
	profileString += $('#loggedInUser').text().substr(14,30) +
						'&EditProfile=True&Profiles';

	var getInfo = displayPageInfo(profileString);
	}

function placeValues(){
	$("#pYear").val(year), $("#course1").val(choice1), $("#course2").val(choice2);
	$("#course3").val(choice3), $("#course4").val(choice4), $("#course5").val(choice5);
	$("#pTAExp").val(taExp), $("#pTAVol").val(taVol), $("#pTAWhy").val(taWhy);
	
}

function updateProfile(){
	profileString = 'UpdateProfile=True' +  '&Year=' + $("#pYear").val() +
			'&TaExp=' + $("#pTAExp").val() + '&TaVol=' + $("#pTAVol").val() +
			'&TaWhy=' + $("#pTAWhy").val() + '&Choice1=' + $("#course1").val() + 
			'&Choice2=' + $("#course2").val() + '&Choice3=' + $("#course3").val() +
			'&Choice4=' + $("#course4").val() + '&Choice5=' + $("#course5").val() +
			"&Profiles";
	displayPageInfo(profileString);
	
	setTimeout(function() {
    	getProfile('MyProfile');
  	}, 100);
	
}

function checkValues(id,prevValue,newValue){
	
	var idid= '#' + id;
	var courseList = ['#course1','#course2','#course3','#course4','#course5'];

	if(id=='course1'||id=='course2'||id=='course3'||id=='course4'||id=='course5'){
		for(courses in courseList){
			if(($(courseList[courses]).val() == newValue) && !(idid == courseList[courses])){
				$(idid).val(newValue);
				$(courseList[courses]).val(prevValue);
			}
		}
	}
	
}
/*  Retrieves all info of the selected row in the Oppourtunities  table */
function getCourseRowInfo(){
	rowId = '', rowCourse = '', rowTitle = '', rowTerm = '', rowYear = '';
	rowInstructor = '', rowCampus = '', rowPos = '', rowAvail = '';
	var i=0;
	selectedRow.find('td').each(function(){
		switch(i){
    		case 0:
        		rowId += $(this).text();
        		break;
    		case 1:
        		rowCourse += $(this).text();
        		break;
        	case 2:
        		rowTitle += $(this).text();
        		break;	
        	case 3:
        		rowTerm += $(this).text();
        		break;
        	case 4:
        		rowYear += $(this).text();
        		break;
        	case 5:
        		rowInstructor += $(this).text();
        		break;
        	case 6:
        		rowCampus += $(this).text();
        		break;
        	case 7:
        		rowPos += $(this).text();
        		break;
        	case 8:
        		rowAvail += $(this).text();
        		break;
		}
		i++;
	});
}



