
/* ==================== Document.ready ======================== */

/* Gather Session info, display page info accordingly */
$(document).ready(function(){
	console.log("document.ready");
	getPages();
	displayPageInfo('Courses');
});

/* Having the page buttons identify which is currently active */
$(document).on("click",'#pageButtons button',function(e) {
    $('#pageButtons button').not(this).css("background-color", "#cacdd1");
    $(this).css("background-color", "#153363");
	$('#pageButtons button').not(this).css("color", "black");
    $(this).css("color", "white");
    e.preventDefault();
});

/* ================================================================================= */
/* ============================== Event Listeners ================================== */
/* ================================================================================= */

/* ================== ADMIN Course Page ====================== */

/* When selecting a row from the table of courses */
selectedRow = null;
$(document).on("click", "#courseTable tbody tr",function() {
	if (selectedRow != null){
		selectedRow.css("background-color", "#aeccfc");
		var i=0;
		selectedRow.find('td').each(function(){
			switch(i){
	        	case 2:
	        		$(this).text(rowTitle.replace('** '| ' **', ''));
	        		break;
			}
			i++;
		});
	} 
	$(this).css("background-color", "#ff8533");
	var i=0;
	$(this).find('td').each(function(){
		switch(i){
        	case 2:
        		rowTitle = $(this).text();
        		$(this).text('** ' + rowTitle + ' **');
        		break;
        	case 5:
        		rowInstructor = $(this).text();
        		break;
		}
		i++;
	});
	selectedRow = $(this);
	$("#changeInstructor").val(rowInstructor);
});

/* When adding a new course*/
$(document).on("submit", "#addCourseForm",function(page) {
	page.preventDefault();
	addItem('Course');
});

/* ====================== Users Page =========================== */

/* When selecting a row from the table of users */
userUtorid = '';
$(document).on("click", "#userTable tbody tr",function() {
	if (selectedRow != null){
		selectedRow.css("background-color", "#aeccfc");
		var i=0;
		selectedRow.find('td').each(function(){
			switch(i){
	        	case 0:
	        		$(this).text(userUtorid.replace('** '|' **', ''));
	        		break;
			}
			i++;
		});
	} 
	$(this).css("background-color", "#ff8533");
	selectedRow = $(this);
	var i=0;
	selectedRow.find('td').each(function(){
		switch(i){
        	case 0:
        		userUtorid = $(this).text()
        		$(this).text('** ' + userUtorid + ' **');
        		break;
		}
		i++;
	});
});

/* When adding a new User */
$(document).on("submit", "#addUserForm",function(page) {
	page.preventDefault();
	addItem('User');
});

/* ================= ADMIN Application Page ==================== */

/* When a sort value is selected */
$(document).on("change", "#appSort", function() {
	prevSort = $(this).val();
	sortString = 'Sort=True&SortValue=' + $(this).val() + 
				'&All_Applications';
	displayPageInfo(sortString);
});

/* Extract the information from the row before a tag change */
$(document).on("mousedown", "#allAppTable tbody tr",function() {
	selectedRow = $(this);
});

/* Collect old tag value */
$(document).on("focus", '#allAppTable select', function(){
	prevTag = $(this).val();
	console.log(prevTag);	
});

/* Collect new tag value */
$(document).on("change", "#allAppTable select", function(e){
	e.stopPropagation();
	var previousTag = prevTag;
	if(confirm("Are you sure you wish to change this " + 
		"application's status?")){
		changeTag($(this).val(), prevTag);
	}else{
		$(this).val(previousTag);
	}
});
/* ================= Student Application Page ================== */

/* When applicant wishes to apply to a course */
$(document).on("submit", "#editApplication",function(page) {
	page.preventDefault();
	updateProfile();
});

/* Gather old preference value in profile table */
$(document).on("focus", "#myProfileTable select",function() {
	currentVal = $(this).val();
});

/* Gather new preference value in profile table */
$(document).on("change", "#myProfileTable select",function() {
	checkValues(this.id,currentVal,$(this).val());
});


/* ================== Applicant Course Page ==================== */

/* When applicant applies to a course */
$(document).on("click","#apply",function(page){
	page.preventDefault();
	courseApply();
});

/* When applicant submits their application */
$(document).on("submit", "#applyForm",function(page) {
	page.preventDefault();
	submitApplication();
});

/* ===================== ADMIN Other Page ====================== */

/* When Clear semester is pressed */
$(document).on("click","#clearSemester",function(page){
	if(confirm("Are you absolutely sure you would like to completely" +
		" erase ALL the current courses and applications?")){
		displayPageInfo('CLEARSEMESTER');
		displayPageInfo('Courses');
		setTimeout(function() {
    		$("#errorMessage").text('Your semester was successfully cleared.');
  		}, 200);
	}
});

/* When Upload CSV is pressed */
$(document).on("click","#uploadButton",function(page){
	page.preventDefault();
	if (window.File && window.FileReader && window.FileList && window.Blob) {
	  // Great success! All the File APIs are supported.
	} else {
	  console.log('The File APIs are not fully supported in this browser.');
	}
	var fileInput = document.getElementById('file-input');
	var file = fileInput.files[0];
	var reader = new FileReader();
	reader.onload = function(e) {
		var readText = reader.result;
		uploadCourses(readText);
	}
	reader.readAsText(file);
});

/* ================== Login/ Logout Features =================== */

/* When login form is submitted*/
$(document).on("submit", "#contentForm",function(page) {
	page.preventDefault();
	login();
	$('#utorid').val('');
	$('#password').val('');
});

/* When Logout button is pressed*/
$(document).on("click","#logout",function(){
	logout();
	console.log('logout button just pressed');
});




/* ================================================================================= */
/* ============================== User Functions =================================== */
/* ================================================================================= */

/* ==================== Login/ Logout Features ===================== */

/* Function creates a JSON word and sends it to connect2b.php to find 
   $_SESSION variables about current user logging in. */
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

/* Function creates a JSON word and sends it to connect2b.php to find 
$_SESSION variables about current user logging in. */
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

/* Function called to retrieve session variables then calls 
   displayButtons() with appropraite page data based on role assigned */
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


/* ======================= Page Display Functions ========================== */

/* The function that actually does the sending of the variables through 
	an Ajax call to Controller.php based on collected information stored
	in the 'page' paramter sent from other functions in Controller.js. */
function displayPageInfo(page){
	var postString = page+"=True";
	$.ajax({
		type: "POST",
		url: "Controller.php",
		data: postString,
		success: function(response){

			if(response.includes("placeValues()")){
				$("#pageInfo").html(response.replace('placeValues()',''));
				placeValues();
			}else if(response.includes(("getTags()"))){
				$("#pageInfo").html(response.replace('getTags()',''));
				$("#appSort").val(prevSort);
				updateTags();
			}else{
				$("#pageInfo").html(response);
			}
			$("#errorMessage").text("");
			//console.log(response);
		},
		error: function(){
			$("#pageInfo").html('<p>Error connecting to database</p>');
		}
	})
}

/* ======================= Course Page Functions ========================== */

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
				deleteString += $(this).text().replace('** ','').replace(' **','');
				console.log($(this).text().replace('** ','').replace(' **',''));
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

/* Function called to either add a course a user to the database Also 
   returns error messages when appropriate.*/
function addItem(item){
	if (item == 'Course'){
		var addString = 'CourseCode=' + $('#courseCode').val()
					+ '&CourseTitle=' + $('#courseTitle').val()
					+ '&CourseTerm=' + $('#courseTerm').val()
					+ '&CourseInstructor=' + $('#courseInstructor').val()
					+ '&CourseCampus=' + $('#courseCampus').val()
					+ '&TaPositions=' + $('#numPositions').val()
					+ '&CourseYear=' + $('#courseYear').val();
		if(!($('#question1').val().trim() == '')){
					addString += '&Question1=' + $('#question1').val();
		}else{
					addString += '&Question1=null';
		}
		if(!($('#question2').val().trim() == '')){
					addString += '&Question2=' + $('#question2').val();
		}else{
					addString += '&Question2=null';
		}
		if(!($('#question3').val().trim() == '')){
					addString += '&Question3=' + $('#question3').val();
		}else{
					addString += '&Question3=null';
		}
					
					addString += '&Add=True&Courses';
		displayPageInfo(addString);
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
						+ '&UserEmail=' + $('#email').val()
						+ '&Add=True&Users';
		}
		if(confirm("Are you sure you wish to add this entry?")){
			$("#errorMessage").text("");
			displayPageInfo(addString);
		}
	}
}

/* Simply sends which instructor needs to be updated */
function changeCourseIns(){
	if(selectedRow == null){
		$("#errorMessage").text("There's no course selected!");
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

/* Function asks user specific questions to the course, then submits an 
   application to this course. */ 
function courseApply(){
	if(selectedRow == null){
		$("errorMessage").text("There's no course selected!");
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

/* Takes filled out application form data and submits it */
function submitApplication(){
	if (parseInt($("#grade").val()) > 100 || parseInt($("#grade").val()) < 0 || isNaN(parseInt($("#grade").val()))){
		$("#errorMessage").text("Please enter a valid grade (0-100). ");
		return 0;
	}else{
		applyString = 'Grade="' + $("#grade").val() + '"&RowId="' + rowId + 
		'"&Late=0';
	}
	if(confirm('Are you sure you wish to submit this application?')){
		if(!($("#answer1").length == 0)){
			applyString += '&Answer1="'+ $("#answer1").val() + '"';
		}
		if(!($("#answer2").length == 0)){
			applyString += '&Answer2="'+ $("#answer2").val() + '"';
		}
		if(!($("#answer3").length == 0)){
			applyString += '&Answer3="'+ $("#answer3").val() + '"';
		}
		
		applyString += '&ApplySubmit=';
		displayPageInfo(applyString);
	}
}

/*  Retrieves all info of the selected row in the Oppourtunities  table */
function getCourseRowInfo(){
	rowId = '', rowCourse = '', rowTerm = '', rowInstructor = '';
	rowTitle = '';
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
        	case 5:
        		rowInstructor += $(this).text();
        		break;
		}
		i++;
	});
}

/* Reads the provided CSV file and adds the coruse to Course table */
function uploadCourses(file){
	//console.log(file.split("\n"));
	var rows = file.split("\n");
	for (i=0; i < rows.length; i++){
		var row = rows[i].split(",");
	
		var postString = 'Courses="True"&Add="True"&CourseCode=' + row[0] + '&CourseTitle=' +
						row[1] + '&CourseTerm=' + row[2] + '&CourseInstructor='
						+ row[3] + '&CourseCampus=' + row[4] + '&TaPositions='
						+ row[5] + '&CourseYear=' + row[6] +'';
		if(row.length > 7){
			postString += '&Question1=' + row[7] + '';
		}
		if(row.length > 8){
			postString += '&Question2=' + row[8] + '';
		}
		if(row.length > 9){
			postString += '&Question3=' + row[9] + '';
		}
		console.log(postString);
	    $.ajax({
	        type: "POST",
	        url: "Controller.php",
	        data: postString,
	        success: function(data){
	            displayPageInfo('Courses');
	        },
	        failure: function(){
	        	$("errorMessage").text('Upload Failed');
	        }
	     });
     };
     //console.log(postString);
}


/* ======================= Profile Page Functions ========================== */

/* Function displays a profile of a given person on the page, may be
   viewed by clicking a link (ADMIN) or clicking "My Profile" */
function getProfile(person){
	profileString = 'GetProfile=True&ProfileId='; 
	if(person == 'MyProfile'){
		profileString += $('#loggedInUser').text().substr(14,30);
	}
	else if(person == 'Instructor'){
		var i=0;
		selectedRow.find('td').each(function(){
			if(i==0){
				profileString += $(this).text();
			}
			i++;
		});
	}
	else{
		profileString += person;
	}
	profileString += '&Profiles';
	displayPageInfo(profileString);
}

/* Allows applicant only to edit the current information on their saved
   profile viewable only by ADMINS */
function editProfile(){
	year='', choice1='', choice2='', choice3='', choice4 = '', email='';
	choice5 = '', taExp = '', taVol = '', taWhy = '', status = '';
	var i=0;

	$("#myProfileTable").find('td').each(function(){
		switch(i){
        	case 3:
        		choice1 += $(this).text(); break;	
        	case 5:
        		choice2 += $(this).text(); break;
        	case 7:
        		choice3 += $(this).text(); break;
        	case 9:
        		choice4 += $(this).text(); break;
        	case 11:
        		choice5 += $(this).text(); break;
        	case 13:
        		status += $(this).text(); break;
        	case 14:
        		year += $(this).text().substr(27,50); break;
        	case 15:
        		taExp += $(this).text().substr(23,1000); break;
        	case 16:
        		taVol += $(this).text().substr(42,1000); break;
        	case 17:
        		taWhy += $(this).text().substr(29,1000); break;
        	case 19: 
        		email += $(this).text(); break;
		}
		i++;
	});
	console.log(email);
	profileString += $('#loggedInUser').text().substr(14,30) +
						'&EditProfile=True&Profiles';
	var getInfo = displayPageInfo(profileString);
	}

/* After empty edit profile page is returned, fill with applicant's saved info */
function placeValues(){
	$("#pYear").val(year), $("#course1").val(choice1), $("#course2").val(choice2);
	$("#course3").val(choice3), $("#course4").val(choice4), $("#course5").val(choice5);
	$("#pTAExp").val(taExp), $("#pTAVol").val(taVol), $("#pTAWhy").val(taWhy);
	$("#studentStatus").val(status), $("#userEmail").val(email);
}

/* Submits the profile information was applicant is finished editing and save it */
function updateProfile(){
	profileString = 'UpdateProfile=True' +  '&Year=' + $("#pYear").val() +
			'&TaExp=' + $("#pTAExp").val() + '&TaVol=' + $("#pTAVol").val() +
			'&TaWhy=' + $("#pTAWhy").val() + '&Choice1=' + $("#course1").val() + 
			'&Choice2=' + $("#course2").val() + '&Choice3=' + $("#course3").val() +
			'&Choice4=' + $("#course4").val() + '&Choice5=' + $("#course5").val() +
			'&Status=' + $("#studentStatus").val() + '&Email=' + $("#userEmail").val() +
			"&Profiles";
	displayPageInfo(profileString);
	console.log(profileString);
	setTimeout(function() {
    	getProfile('MyProfile');
  	}, 100);
}

/* Function switches tag values when selects are changed, to force applicant to
   distinguish between their 5 top choices and cannot be left blank */
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

/* ======================= Application Page Functions ========================== */

/* After applications page is displayed, this function updates the values of
   the statuses, also colour coordinating them */
function updateTags() {
	var i = 0;
	$("#allAppTable").find('.selectTd').each(function(){
		var tagId = 'tag' + i;
		var selectTag = '<select id="' + tagId +'">' +
						 '<option value="Pending">Pending</option>' +
						 '<option value="Yes">Granted</option>' +
						 '<option value="Maybe">Maybe</option>' +
						 '<option value="No">No</option></select>';
		tagValue = $(this).text();
		$(this).html(selectTag);
		var idid = '#' + tagId;
		$(idid).val(tagValue);
		switch($(idid).val()){
			case 'No':
				$(idid).css("background-color","red");
				break;
			case 'Yes':
				$(idid).css("background-color","green");
				break;
			case 'Maybe':
				$(idid).css("background-color","yellow");
				break;
			default:
				$(idid).css("background-color","#cec8c9");
				break;
		}
		i++;
	});
}

/* When the status of an application is changed, information is stored
   and sent to databse for storage, and update course table */
function changeTag(newTag, oldTag){
	tagUtorid = '', tagCourse = '';
	tagTerm = '', tagYear = '';
	var i=0;
	selectedRow.find('td').each(function(){
		switch(i){
    		case 0:
        		tagUtorid = $(this).text();
        		break;
    		case 1:
        		tagCourse = $(this).text();
        		break;
        	case 2:
        		tagTerm = $(this).text();
        		break;
        	case 3:
        		tagYear = $(this).text();
        		break;
		}
		i++;
	});
	var changeString = 'All_Applications=True&TagUtorid="' +
				tagUtorid + '"&TagCourse="' + tagCourse +
				'"&TagValue="' + newTag + '"&OldTag="' + oldTag +
				'"&TagTerm="' + tagTerm +'"&TagYear="' + tagYear +
				'"&ChangeTag';
	displayPageInfo(changeString);
}



