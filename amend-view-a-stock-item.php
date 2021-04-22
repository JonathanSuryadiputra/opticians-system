<!--
Student Name : Jonathan Suryadiputra 
Student ID : C00235450
Group : C2P-B
Course : CW_KCSOF_B
Date : 20/4/20
Purpose of Page : This page is to allow the user to go through a list of stock records presented in a table to update one of them in the system's database.
-->
<html>
	<!-- html head segment -->
	<head>
		<!-- title tag -->
		<title>Optician System > File Maintenance > Amend / View a Stock Item</title>
		<!--#include file="button_bar.php" -->
		<?php include("button_bar.php"); ?>	  
		<link rel = "stylesheet" type = "text/css" href = "css/updatestock.css">
	</head> <!-- end html tag -->
	
	<!-- javascript code segment -->
	<script>
		// javascript function for search filter bar for the table
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
				suppliernametd = rows[i].getElementsByTagName("td")[5]; // get supplier name column through the rows
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
		
		// javascript function to clear search filter for the table
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
				function produceReport($con,$sql) {
					// make sql connection query
					$result = mysqli_query($con,$sql);
					// afterwards, echo out the html table through a string
					// table head set up
					echo "	<table>
							<thead>
					  		<tr><div style = 'display:flex; vertical-align:middle;'><input type = 'text' class = 'searchbar' id = 'searchbar' onkeyup = 'searchFilter()' placeholder = 'Search for stock number or description...'><button class = 'searchbutton' type = 'button' onclick = 'clearSearch()'>&times;</button></div></tr>
							<tr><th class = 'stocknum'>Stock No.</th><th class = 'description'>Description</th><th class = 'costprice'>Cost Price</th><th class = 'retailprice'>Retail Price</th><th class = 'reorderquantity'>Reorder Qty</th><th class = 'suppliername'>Supplier Name</th><th class = 'editcol'></th></tr>
							</thead>
							
							<tbody id = 'stocklistbody'>";
					// table body set up, rows are echoed out through a loop, because it depends on how many rows are retrieved from the sql query
					while ($row = mysqli_fetch_array($result)) {
						echo "	<tr><td class = 'stocknum'><div align = 'center'>" .$row['stockNumber']. "</div></td>
								<td class = 'description'>" . $row['description'] . "</td>
								<td class = 'costprice'>" . $row['costPrice'] . "</td>
								<td class = 'retailprice'>" . $row['retailPrice'] . "</td>
								<td class = 'reorderquantity'>" . $row['reorderQuantity'] . "</td>
								<td class = 'suppliername'>" . $row['supplierName'] . "</td>
								<td class = 'editcol'>";
						// set up 'amend' button positioned on the right side of the table. Like the delete button on the delete stock item screen, this button is also designated as a 'submit' button, just labelled as an 'update' or 'amend' button. This button also contains the value of the stock number that is to be posted to the php sql query file updateStockForm.php, and is encased in a form tag to have a POST method, in the form page the stock number will be referenced to fill the form page with the selected stock's details. This button acts as an unlock button that was covered in the labs, but instead of a locked form filled with details, this consists of a table format with rows of stock records containing details in it and clicking this button brings up the editable form instead of just unlocking disabled form fields.
						echo "	<div align = 'center'><form method = 'POST' action = 'updateStockForm.php'><button class = 'edit' type = 'submit' value = '" .$row['stockNumber']. "' name = 'editstocknum'>Amend</button></form></div>";
						echo "	</td></tr>";
					}
					
					echo "</tbody></table>"; // end of table body and table echo out setup
				}
				mysqli_close($con); // close sql connection
				?>
			</div>
		</div> <!-- end table div -->
	</body> <!-- end body tag -->
</html> <!-- end html tag -->
