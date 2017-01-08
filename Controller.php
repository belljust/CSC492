<?php
	session_save_path('Sessions');
	session_start();

	$servername = 'localhost';
	$username = 'root';
	$password = '';
	$database = 'CSC492';

	/* Create connection */
	$link = mysqli_connect($servername, $username, $password, $database);

	/* Display error if unable to connect to database*/
	if (!$link) {
    	echo "Error: Unable to connect to MySQL.";
    	exit;
	}

    $query = 'SELECT * FROM ASDFG;';
    /* execute multi query */
	if (mysqli_multi_query($link, $query)) {
    	do {
        	/* store first result set */
        	if ($result = mysqli_store_result($link)) {
        	    while ($row = mysqli_fetch_row($result)) {
        	        printf("%s\n", $row[0]);
        	    }
        	    mysqli_free_result($result);
        	}
        	/* print divider */
        	if (mysqli_more_results($link)) {
        	    printf("-----------------\n");
        	}
    	} while (mysqli_next_result($link));
	}
   
?>
