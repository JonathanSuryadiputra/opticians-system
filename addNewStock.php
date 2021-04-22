<?php
/*
Student Name : Jonathan Suryadiputra 
Student ID : C00235450
Group : C2P-B
Course : CW_KCSOF_B
Date : 20/4/20
Purpose of Page : This page is invoked after add new stock item form has been submitted by the user, it acts as an intermediary between the user interface (the form UI) and the system database.
*/

// include database connection php file
include 'db.inc.php';

// check if submit button has been clicked, if not, redirect user back to the form page
if (!isset($_POST['submitButton'])) {
	// redirect page to form page if a form was never submitted
	header( "Location: https://c2popticians.candept.com/add-a-new-stock-item.php" );
	exit;
}

// handle supplier details stuff
$selectedSupplier = $_POST['suppliername'];
$recordResult = explode(",", $selectedSupplier);
$supplierId = $recordResult[0];

//AUTO INCREMENT CODE SEGMENT
	// get next stockNumber
	$stockNumQuery = mysqli_query($con, "SELECT MAX(stockNumber) AS maxStockNum FROM Stock");

	if (!stockNumQuery) {	
		die ("An Error in the SQL Query: " . mysqli_error());
	}

	// fetch each row(s) into an array
	$result = mysqli_fetch_array($stockNumQuery);

	// store row data into a php variable, in this case there is only one column and one row of data, so a loop is unnecessary
	$nextStockNum = $result['maxStockNum'];

	// get number of row(s) (records) and store them in a variable; this tells us how many records are fetched from the table in the db
	$affectedRows = mysqli_affected_rows($con);

	// if statement test to see if any records are present, if none are present, affectedRows above will contain a zero value
	if ($affectedRows > 0) {
		// if there are more than zero records i.e. if records are present, then add 1 to the maximum id fetched from the table
		$stocknumber = $nextStockNum + 1;
	}
	else {
		// else if no records are found beforehand, stocknumber will be initialized to 1
		$stocknumber = 1;
	}
//END AUTO INCREMENT CODE SEGMENT

// SQL insert statement into database
$sql = "INSERT INTO Stock (stockNumber, description, costPrice, retailPrice, reorderLevel, reorderQuantity, supplierID, supplierStockCode) VALUES ('$stocknumber', '$_POST[description]', '$_POST[costprice]', '$_POST[retailprice]', '$_POST[reorderlevel]', '$_POST[reorderquantity]', '$supplierId', '$_POST[supplierstockcode]')";

// if mysqli query has an error
if (!mysqli_query($con, $sql)) {
	die ("An Error in the SQL Query: " . mysqli_error($con));
}

// close sql connection
mysqli_close($con);

// redirect page back to form after submission to database
header( "Location: https://c2popticians.candept.com/add-a-new-stock-item.php" );
exit; // exit php file
?>