<!--
Student Name : Jonathan Suryadiputra 
Student ID : C00235450
Group : C2P-B
Course : CW_KCSOF_B
Date : 20/4/20
Purpose of Page : This page is to allow the user to delete a stock item record from the list of stock items in the system.
-->
<html>
	<!-- html head segment -->
	<head>
		<!-- title tag -->
		<title>Optician System > File Maintenance > Delete a Stock Item</title>
		<!--#include file="button_bar.php" -->
		<?php include("button_bar.php"); ?>
		<link rel = "stylesheet" type = "text/css" href = "css/deletestock.css">
	</head> <!-- end html tag -->
	
	<!-- javascript code segment -->
	<script>
		// javascript function to invoke message popup to confirm submission for the user
		function confirmCheck(stocknumber) {
			var response;
			response = confirm('Are you sure you want to delete this stock record?');
			
			// if user confirms everything, proceed to submission
			if(response) {
				alert("The stock record selected has been deleted from the system.\nDeleted record : Stock No. " + stocknumber +  ", " + document.getElementById("querieddescription" + stocknumber).value);
				return true
			}
			else {
				return false;
			}	
		}
		
		// javascript function to check quantity (if there are still stock quantity left in inventory)
		function checkQuantity(stocknumber) {
			// if stock quantity is more than 0 i.e. there are still stock quantity left in inventory, prevent delete action
			if (document.getElementById("queriedquantity" + stocknumber).value > 0) {
				alert("The stock record you selected still has stock supply in inventory, therefore it cannot be deleted");
				return false;
			}
			// else if the stock item is on order (i.e. more stock quantity on the way), delete action will also be prevented
			else if (document.getElementById("onorder" + stocknumber).value == 1) {
				alert("The stock record you selected is currently on order, therefore it cannot be deleted");
				return false;
			}
			// else then pass control to confirmCheck() javascript function
			else {
				return confirmCheck(stocknumber);
			}
		}
		
		// function for search filter bar for the table
		function searchFilter() {
			// set up variables
			// get whatever is typed in to the search bar
			var search = document.getElementById("searchbar").value.toUpperCase(); // to avoid case sensitivity
			var tbody = document.getElementById("stocklistbody"); // get table body containing the table rows
			var rows = tbody.getElementsByTagName("tr"); // get the rows (the tr elements) from the table body
			var stocknumtd;
			var descriptiontd;
			var suppliernametd;
			var stocknumvalue;
			var descriptionvalue;
			var suppliernamevalue;

			//loop through all table rows, and only show what matches the search
			for (var i = 0; i < rows.length; i++) {
				stocknumtd = rows[i].getElementsByTagName("td")[0]; //get stock number column through the rows
				descriptiontd = rows[i].getElementsByTagName("td")[1]; //get description column through the rows
				suppliernametd = rows[i].getElementsByTagName("td")[3]; // get supplier name column through the rows
				if (stocknumtd || descriptiontd) {
					// set td contents to their respective variables to be compared in the if statement
					stocknumvalue = stocknumtd.textContent || stocknumtd.innerText;
					descriptionvalue = descriptiontd.textContent || descriptiontd.innerText;
					suppliernamevalue = suppliernametd.textContent || suppliernametd.innerText;
					// compare values from the search field and the variables
					if (stocknumvalue.toUpperCase().indexOf(search) > -1 || descriptionvalue.toUpperCase().indexOf(search) > -1 || suppliernamevalue.toUpperCase().indexOf(search) > -1) {
						rows[i].style.display = ""; // if search keyword matches whatever is in the row
					}
					else {
						rows[i].style.display = "none"; // if search keyword doesn't match, hide the row
					}
				}
			}
		}
		
		// function to clear search filter for the table
		function clearSearch() {
			// set variables
			var tbody = document.getElementById("stocklistbody"); // get table body containing the table rows
			var rows = tbody.getElementsByTagName("tr"); // get the rows (the tr elements) from the table body
			// clear/empty the search bar of keywords
			document.getElementById("searchbar").value = "";
			
			// loop through all the table rows
			for (var i = 0; i < rows.length; i++) {
				rows[i].style.display = ""; // unhide every table row that was hidden from the search filter action
			}
			document.getElementById("searchbar").focus(); // keep searchbar focused on screen
		}
	</script>
	
	<!-- body code segment -->
	<body>
		<?php
		// include database connection php file
	  	include 'db.inc.php';
		// set timezone to UTC
		date_default_timezone_set('UTC');
		?>
		
		<!-- header -->
		<div class = "headeroutercontainer">
			<div class = "headercontainer">
				<h1>Stock Items</h1>
			</div>
		</div> <!-- end header -->
		
		<!-- table -->
		<div class = "tableoutercontainer">
			<div class = "tablecontainer">
				<?php
				// sql query string
				$sql = "SELECT * FROM Stock INNER JOIN Supplier ON Stock.supplierID = Supplier.supplierID WHERE Stock.deleteFlag = false";
				// produceReport function call
				produceReport($con,$sql);
				// produceReport php function
				function produceReport($con,$sql)
				{
					// make sql connection and query in the function
					$result = mysqli_query($con,$sql);
					// afterwards, echo out the html table through a string
					// table head set up
					echo "<table>
					<thead>
					<tr><div style = 'display:flex; vertical-align:middle;'><input type = 'text' class = 'searchbar' id = 'searchbar' onkeyup = 'searchFilter()' placeholder = 'Search for stock number or description...'><button class = 'searchbutton' type = 'button' onclick = 'clearSearch()'>&times;</button></div></tr>
					<tr><th class = 'stocknum'>Stock No.</th><th class = 'description'>Description</th><th class = 'costprice'>Cost Price</th><th class = 'suppliername'>Supplier Name</th><th class = 'deletecol'></th></tr>
					</thead><tbody id = 'stocklistbody'>";
					// table body set up, rows are echoed out through a loop, because it depends on how many rows are retrieved from the sql query
					while ($row = mysqli_fetch_array($result)) {
						echo "	<tr><td class = 'stocknum'><div align = 'center'>" .$row['stockNumber']. "</div></td>
								<td class = 'description'>" . $row['description'] . "</td>
								<td class = 'costprice'>" . $row['costPrice'] . "</td>
								<td class = 'suppliername'>" . $row['supplierName'] . "</td>
								<td class = 'deletecol'>";
						// set up delete button positioned on the right side of the table, this button is encased in a form tag and actually acts as a 'delete' button instead of a 'submit' action. But the mechanics used are similar to that of a submit button. Attached to the onclick listener is a javascript function that checks that stock item's quantity in inventory (see javascript function checkQuantity() above on the script code tag.
						echo "	<div align = 'center'><form method = 'POST' action = 'deleteStock.php'><button class = 'delete' type = 'submit' value = '" .$row['stockNumber']. "' name = 'deletestocknum' onclick = 'return checkQuantity(this.value)'>Delete</button></form></div>";
						echo "</td></tr>";
					}
					echo "</tbody></table>"; // end of table body and table echo out setup
				}
				?>
			</div>
		</div> <!-- end table div -->
		
		<?php
		// php code to make query to sql for stock items' quantity status in inventory
		// make sql connection and query
		$result = mysqli_query($con,$sql);
		// loop for as many as the sql query brings back a number of rows
		while ($row = mysqli_fetch_array($result)) {
			// echo out an input tag for HTML, this is a hidden input field and is only there to be accessed and referenced by javascript, all the id for these are associated with the stock item's stock number
			echo "<input type = 'hidden' id = 'querieddescription" . $row['stockNumber'] . "' value = '" . $row['description'] . "'>";
			// echo out another hidden input containing that stock item's current stock quantity available in the shop's inventory, again this field's id is associated with the stock item's stock number for javascript reference, the value of this set of fields are checked by javascript to see if there are still stock quantity left in store, if the value is more than 0 then the system will prevent the user from deleting the stock item record, else if the value is zero (no stock quantity left available in store) then user may delete the stock record.
			echo "<input type = 'hidden' id = 'queriedquantity" . $row['stockNumber'] . "' value = '" . $row['quantityInStock'] . "'>";
			// echo out another hidden input containing that stock number's order status, again this field's id is associated with the stock item's stock number for javascript reference, the value of this set of fields are either true or false (boolean), or in this case '0' or '1', and their values will be checked by javascript to alert the user whether the said stock item is currently on order or not.
			echo "<input type = 'hidden' id = 'onorder" . $row['stockNumber'] . "' value = '";
			
			// make sqli query to check if the stock number in question is currently on order, we do this by querying the stock number AND check if the delivery date IS NULL, if NULL then it means the stock item is still currently on order and have not arrived/been delivered yet, even if there are currently no stock quantity left in inventory, if its on order then it means more stock quantity are on the way so the stock item record may not be deleted
			mysqli_query($con,"SELECT * FROM `Order` INNER JOIN `Order Item` ON `Order`.`Order Number` = `Order Item`.`Order Number` WHERE `Order Item`.`Stock Number` = " . $row['stockNumber'] . " AND `Order`.`Delivery Date` IS NULL");
			// check if the affected rows method yields a zero, if it does, it means the stock quantity is currently not on order, so the user may proceed to delete the stock (look up checkQuantity javascript function above in this document)
			if (mysqli_affected_rows($con) == 0) {
				echo "0"; // we set the onorder hidden input field value to '0' to indicate false that the stock item is currently on order, therefore stock item record may be deleted
			}
			else { // else if the affected rows are more than zero then the stock quantity is currently on order
				echo "1"; // therefore, we set the onorder hidden input field value to '1' to indicate true that the stock item is currently on order, therefore user may not delete the stock item record, even if there are no stock quantity left in inventory for the said stock item
			}
			// close the hidden onorder input field tag
			echo "'>";
		}
		// close sql connection
		mysqli_close($con);
		?>
	</body> <!-- end body tag -->
</html> <!-- end html tag -->
