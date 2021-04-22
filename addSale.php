<?php
/*
Student Name : Jonathan Suryadiputra 
Student ID : C00235450
Group : C2P-B
Course : CW_KCSOF_B
Date : 20/4/20
Purpose of Page : This page is invoked after a user clicks 'checkout' on the counter-sales 'cash register' page.
*/

// include database connection php file
include 'db.inc.php';
// set timezone to UTC
date_default_timezone_set('UTC');

// check if the user has clicked submit button, in this case the checkout button, if user has not clicked it then redirect them back to the form page
if (!isset($_POST['checkout'])) {
	// redirect page to form page if a form was never submitted
	header( "Location: https://c2popticians.candept.com/counter-sales.php" );
	exit;
}

//AUTO INCREMENT CODE SEGMENT
	//get next counter sale ID
	$salesIdQuery = mysqli_query($con, "SELECT MAX(counterSaleID) AS maxCounterSaleID FROM CounterSales");

	if (!$salesIdQuery) {
		die ("An Error in the SQL Query: " . mysqli_error($con));
	}

	// fetch each row(s) into an array
	$result = mysqli_fetch_array($salesIdQuery);

	// store row data into a php variable, in this case there is only one column and one row of data, so a loop is unnecessary
	$nextSaleId = $result['maxCounterSaleID'];

	// get number of row(s) (records) and store them in a variable; this tells us how many records are fetched from the table in the db
	$affectedRows = mysqli_affected_rows($con);

	// if statement test to see if any records are present, if none are present, affectedRows above will contain a zero value
	if ($affectedRows > 0) {
		// if there are more than zero records i.e. if records are present, then add 1 to the maximum id fetched from the table
		$countersaleid = $nextSaleId + 1;
	}
	else {
		// else if no records are found beforehand, countersaleid will be initialized to 1
		$countersaleid = 1;
	}
//END AUTO INCREMENT CODE SEGMENT

//SQL insert statement into Counter Sales table on database
$dateofsale = date('Y-m-d');
$totalcost = $_POST['grandtotal'];
$sql = "INSERT INTO CounterSales (counterSaleID, dateOfSale, totalCost) VALUES ('$countersaleid', '$dateofsale', '$totalcost')";

if (!mysqli_query($con, $sql)) {
	die ("An Error in the SQL Query: " . mysqli_error($con));
}

//SQL insert statement into Sales Item table on database
$numOfItems = $_POST["numberofrows"];

//retrieve stock number from the form page
$a = 0;
for ($i = 0; $i < $numOfItems; $i++) {
	$stocknumber[$i] = $_POST['stocknumber' . $a];
	$a++;
}

//retrieve quantity information from the form page using the stock number as reference for each field
$quantity = array();
for ($i = 0; $i < $numOfItems; $i++) {
	$quantity[$i] = $_POST['buyquantity' . $stocknumber[$i]];
}

//looped insert statement to SalesItem second level table
for ($i = 0; $i < $numOfItems; $i++) {
	$sql = "INSERT INTO SalesItem (counterSaleID, stockNumber, quantity) VALUES ('$countersaleid', '$stocknumber[$i]', '$quantity[$i]')";
	if (!mysqli_query($con, $sql)) {
		die ("An Error in the SQL Query: " . mysqli_error($con));
	}
}

//looped statement for Stock table to take away buying quantity from quantity in stock
for ($i = 0; $i < $numOfItems; $i++) {
	$sql = "UPDATE Stock SET quantityInStock = quantityInStock - '" . $quantity[$i] . "' WHERE stockNumber = '" . $stocknumber[$i] . "' ";
	if (!mysqli_query($con, $sql)) {
		die ("An Error in the SQL Query: " . mysqli_error($con));
	}
}

// close sql connection
mysqli_close($con);

// redirect page to home page after sales record submission to database
header( "Location: https://c2popticians.candept.com/counter-sales.php" );
exit;
?>