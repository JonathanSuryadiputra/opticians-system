<?php
include 'db.inc.php'; //database connection

// get list of suppliers
$supplierQuery = mysqli_query($con, "SELECT supplierId, supplierName FROM Supplier");

if (!$supplierQuery) {
	die('Error in querying the database' . mysqli_error($con));
}

echo "<br><select name = 'suppliername' id = 'suppliername' required>
<option disabled selected value = '' style = 'display:none'> -- select a supplier -- </option>";

//fetch each row(s) into an array
while ($queryResults = mysqli_fetch_array($supplierQuery)) {
	$supplierId = $queryResults['supplierId'];
	$supplierName = $queryResults['supplierName'];
	$recordString = "$supplierId,$supplierName";
	echo "<option value = '$recordString'>$supplierName</option>";
}

echo "</select>";
mysqli_close($con);
?>