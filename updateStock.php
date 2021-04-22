<?php
/*
Student Name : Jonathan Suryadiputra 
Student ID : C00235450
Group : C2P-B
Course : CW_KCSOF_B
Date : 20/4/20
Purpose of Page : This page is invoked after a user clicks 'submit' on the stock update form page.
*/

// include database connection php file
include 'db.inc.php';

// check if submit button has been clicked, if not, redirect user back to the form page
if (!isset($_POST['submitButton'])) {
	// redirect page to main amend-view page if a form was never submitted
	header( "Location: https://c2popticians.candept.com/amend-view-a-stock-item.php" );
	exit;
}

// handle supplier details stuff
$selectedSupplier = $_POST['suppliername'];
$recordResult = explode(",", $selectedSupplier);
$supplierId = $recordResult[0];

// fish out the stock number from the update stock details form
$stockNumber = $_POST['stocknumber'];

// SQL update statement string to update details in database
$sql = "UPDATE Stock SET description = '$_POST[description]', costPrice = '$_POST[costprice]', retailPrice = '$_POST[retailprice]', reorderQuantity = '$_POST[reorderquantity]', supplierID = '$supplierId' WHERE stockNumber = '$stockNumber'";

// if sql query come across error
if (!mysqli_query($con, $sql)) {
	die ("An Error in the SQL Query: " . mysqli_error($con));
}

// close sql connection
mysqli_close($con);

// redirect page back to form after submission to database
header( "Location: https://c2popticians.candept.com/amend-view-a-stock-item.php" );
exit;
?>