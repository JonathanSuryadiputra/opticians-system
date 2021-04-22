<?php
/*
Student Name : Jonathan Suryadiputra 
Student ID : C00235450
Group : C2P-B
Course : CW_KCSOF_B
Date : 20/4/20
Purpose of Page : This page is invoked after a user clicks 'delete' on a stock item record row on the delete-a-stock-item.php page, it acts as an intermediary between the user interface (the form UI) and the system database.
*/

// include database connection php file
include 'db.inc.php';

// check if a 'submit' button was clicked, if not, redirect user back to the form page
if (!isset($_POST['deletestocknum'])) {
	// redirect page to form page if a form was never submitted
	header( "Location: https://c2popticians.candept.com/delete-a-stock-item.php" );
	exit;
}

// get stock number up for deletion from the 'form' page, found on the 'submit' deletion button's value
$stockNumber = $_POST['deletestocknum'];

// SQL delete statement string to delete stock details from database, or in this case just set the delete flag to 'true'
$sql = "UPDATE Stock SET DeleteFlag = true WHERE stockNumber = '$stockNumber'";

// if SQL query has an error
if (!mysqli_query($con, $sql)) {
	die ("An Error in the SQL Query: " . mysqli_error($con));
}

// close sql connection
mysqli_close($con);

// redirect page back to form after submission to database
header( "Location: https://c2popticians.candept.com/delete-a-stock-item.php" );
exit;
?>