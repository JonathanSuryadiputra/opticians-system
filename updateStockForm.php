<!--
Student Name : Jonathan Suryadiputra 
Student ID : C00235450
Group : C2P-B
Course : CW_KCSOF_B
Date : 20/4/20
Purpose of Page : This page is to allow the user to update a stock record in the system's database.
-->
<html>
	<!--head start-->
	<head>
		<!--webpage title-->
		<title>Optician System > File Maintenance > Add a New Stock Item</title>
		<!--#include file="button_bar.php" -->
		<?php include("button_bar.php"); ?>
		<!--css file import-->
		<link rel = "stylesheet" type = "text/css" href = "css/updateStockForm.css">
	</head><!--head end-->
	
	<?php
	include "db.inc.php"; // include database connection php file

	// get selected stock number to be amended from the stock item table from the form page
	$selectedStockNum = $_POST[editstocknum];
	// make sql query using the retrieved stock number
	$sql = "SELECT * FROM Stock INNER JOIN Supplier ON Stock.supplierID = Supplier.supplierID WHERE stockNumber = " . $selectedStockNum . " ";

	// if sqli query came across an error
	if (!$result = mysqli_query($con, $sql)) {
		die ('Error in querying the database' . mysqli_error($con));
	}

	// fetch mysqli query result
	$row = mysqli_fetch_array($result);
	
	// set php variables to the stock details retrieved from the sql query
	$number = $row['stockNumber'];
	$desc = $row['description'];
	$costprice = $row['costPrice'];
	$retailprice = $row['retailPrice'];
	$qtyinstock = $row['quantityInStock'];
	$reorderqty = $row['reorderQuantity'];
	$reorderlvl = $row['reorderLevel'];
	$selectedsupplierid = $row['supplierID'];
	$selectedsuppliername = $row['supplierName'];
	// set supplier record comma-separated string for comparison for the supplier selection box in the form
	$supplierrecord = "$selectedsupplierid,$selectedsuppliername";
	
	// set all the php variables containing the stock details to a comma-separated string
	$selectedstock = "$number,$desc,$costprice,$retailprice,$reorderqty,$reorderlvl,$selectedsupplierid,$selectedsuppliername";
	?>
	<!--javascript start-->
	<script>
		// function to check retail price to be more than or equal to the cost price
		function retailPriceCheck(input) {
			// if retail price is less than cost price value
			if (parseFloat(input.value) < parseFloat(document.getElementById("costprice").value)) {
				input.setCustomValidity("The retail price value must be more than or equal to the cost price value");
			}
			// if retail price is below 0.01 i.e. if they're a minus value
			else if (input.validity.rangeUnderflow) {
				input.setCustomValidity("The value entered is invalid");
			}
			// else if they're valid and in range
			else {
				input.setCustomValidity("");
			}
		}
		
		// function to set validity messages for cost price value, reorder level, and reorder quantity input
		function numberValidCheck(input) {
			// if the number is below range i.e. they're invalid
			if (input.validity.rangeUnderflow) {
				input.setCustomValidity("The value entered is invalid");
			}
			else {
				input.setCustomValidity("");
			}
		}
		
		// javascript function to perform cancel action and redirect user back to main menu page
		function cancelAction() {
			var response;
			response = confirm("Are you sure you want to cancel amends?");
			
			if(response) {
				window.location.replace("https://c2popticians.candept.com/amend-view-a-stock-item.php");
				return true
			}
			else {
				return false;
			}	
		}
		
		// function to invoke message popup to confirm submission for the user
		function confirmCheck() {
			var response;
			// set confirm message
			response = confirm('Are you sure you want to save these changes?');
			
			// get selected supplier name for comparison
			var selected = document.getElementById('suppliername');
			
			if(response) {
				// compare elements, see if there are any changes, if there are none, prevent form submission
				if (document.getElementById('description').value == document.getElementById('description').defaultValue &&
				document.getElementById('costprice').value == document.getElementById('costprice').defaultValue &&
				document.getElementById('retailprice').value == document.getElementById('retailprice').defaultValue &&
				document.getElementById('reorderquantity').value == document.getElementById('reorderquantity').defaultValue &&
				selected.options[selected.selectedIndex].defaultSelected == true) {
					// alert out a message informing the user that no changes have been made
					alert("There are no changes made.");
					return false;
				}
				// else if there are changes made, submit form as usual with a finishing message
				else {
					document.getElementById('stocknumber').disabled = false;
					alert("The stock record has been updated in the database.");
					return true;
				}
			}
			// else if the user clicks 'cancel'
			else {
				return false;
			}	
		}
		
		// function to change fields of the record values they contain, in case user wants to move to another stock item's information details through the selection box provided at the very top of the form
		function changeRecord() {
			// get selection box element from the form
			var selected = document.getElementById("stockrecord");
			// get the selected value and set it as a result
			var result = selected.options[selected.selectedIndex].value;
			// split result string into individual details
			var stockDetails = result.split(',');
			// set element values to the new stock details
			document.getElementById("stocknumber").value = stockDetails[0];
			document.getElementById("description").value = stockDetails[1];
			document.getElementById("costprice").value = stockDetails[2];
			document.getElementById("retailprice").value = stockDetails[3];
			document.getElementById("reorderquantity").value = stockDetails[4];
			document.getElementById("reorderlevel").value = stockDetails[5];
			document.getElementById("suppliername").value = stockDetails[6] + "," + stockDetails[7];
		}
	</script> <!--end javascript code segment-->
	
	<!--body start-->
	<body>
		<div class = "container"><!--form container div-->
			<!--form start, form action derived from file = addNewStock.php-->
			<form name = 'updatestockform' action = "updateStock.php" method = "POST" onsubmit = "return confirmCheck()">
				<!--form title-->
				<div class = "formheadercontainer"><!--form header container div-->
					<p>Update Stock Item</p>
				</div><!--end form head container div-->
				
				<div class = "formfieldscontainer"><!--input fields & buttons container div-->
					<!--stock selection-->
					<p><label for = "selectedstock">Selected Stock</label><br>
						<?php
						// get list of stock records
						$stockQuery = mysqli_query($con, "SELECT * FROM Stock INNER JOIN Supplier ON Stock.supplierID = Supplier.supplierID WHERE Stock.DeleteFlag = false");
						
						// if stock query come across an error
						if (!$stockQuery) {
							die('Error in querying the database' . mysqli_error($con));
						}
						
						// echo out html select tage
						echo "<br><select name = 'stockrecord' id = 'stockrecord' onchange = 'changeRecord()'>";
						
						//fetch each row(s) into an array
						while ($queryResults = mysqli_fetch_array($stockQuery)) {
							// set every stock details into a php variable to be joined together in a comma-separated string
							$newstocknumber = $queryResults['stockNumber'];
							$newdescription = $queryResults['description'];
							$newcostprice = $queryResults['costPrice'];
							$newretailprice = $queryResults['retailPrice'];
							$newreorderquantity = $queryResults['reorderQuantity'];
							$newreorderlevel = $queryResults['reorderLevel'];
							$newsupplierid = $queryResults['supplierID'];
							$newsuppliername = $queryResults['supplierName'];

							// set all the stock details into a comma-separated string to be compared with the other comma-separated string that is set in a previous php line of code
							$recordString = "$newstocknumber,$newdescription,$newcostprice,$newretailprice,$newreorderquantity,$newreorderlevel,$newsupplierid,$newsuppliername";
							// echo out option value for selection box and set value to the record string
							echo "<option value = '$recordString' ";
							// if statement to compare the csv string from the selection box to the csv string set up from a previous php line of code
							if ($recordString == $selectedstock) {
								echo "selected";
							}
							// selection box text display
							echo ">Stock $newstocknumber : $newdescription</option>";
						}
						// echo out end select tag
						echo "</select>";
						// close sql connection
						mysqli_close($con);
						?>
					</p>
					
					<!--stock number field-->
					<p><label for = "stocknumber">Stock Number</label><br>
						<input class = "textfield" type = "text" name = "stocknumber" id = "stocknumber" value = <?php echo $number;?> disabled/>
					</p>
					
					<!--description field-->
					<p><label for = "description">Description</label><br>
						<textarea name = "description" id = "description" rows = "5" required><?php echo $desc;?></textarea>
					</p>

					<!--cost price field-->
					<div class = "inlinefieldcontainer">
						<p><label for = "costprice">Cost Price</label><br>
							<input class = "numberfield" type = "number" name = "costprice" min = "0.01" step = "0.01" id = "costprice" oninput = "numberValidCheck(this)" value = '<?php echo $costprice;?>' required/>
						</p>
					</div>
					
					<!--retail price field-->
					<div class = "inlinefieldcontainer">
						<p><label for = "retailprice">Retail Price</label><br>
							<input class = "numberfield" type = "number" name = "retailprice" min = "0.01" step = "0.01" id = "retailprice" oninput = "retailPriceCheck(this)" value = '<?php echo $retailprice;?>' required/>
						</p>
					</div>
					
					<!--reorder level input field-->
					<div class = "inlinefieldcontainer">
						<p><label for = "reorderlevel">Reorder Level</label><br>
							<input class = "numberfield" type = "number" name = "reorderlevel" min = "0" step = "1" id = "reorderlevel" oninput = "numberValidCheck(this)" value = '<?php echo $reorderlvl;?>' required/>
						</p>
					</div>
				  
					<!--reorder quantity field-->
					<div class = "inlinefieldcontainer">
						<p><label for = "reorderquantity">Reorder Quantity</label><br>
							<input class = "numberfield" type = "number" name = "reorderquantity" min = "0" step = "1" id = "reorderquantity" oninput = "numberValidCheck(this)" value = '<?php echo $reorderqty;?>' required/>
						</p>
					</div>
					
					<!--supplier selection box-->
					<p><label for = "suppliername">Supplier Name</label><br>
						<?php
						include 'db.inc.php'; //database connection

						// get list of suppliers
						$supplierQuery = mysqli_query($con, "SELECT supplierId, supplierName FROM Supplier");

						if (!$supplierQuery) {
							die('Error in querying the database' . mysqli_error($con));
						}

						echo "<br><select name = 'suppliername' id = 'suppliername'>";

						//fetch each row(s) into an array
						while ($queryResults = mysqli_fetch_array($supplierQuery)) {
							// set supplier details to variables to be joined to a csv string
							$supplierId = $queryResults['supplierId'];
							$supplierName = $queryResults['supplierName'];
							$recordString = "$supplierId,$supplierName";
							echo "<option value = '$recordString' ";
							// select the supplier previously set for the stock details
							if ($recordString == $supplierrecord) {
								echo "selected";
							}
							// echo out end supplier option tag
							echo ">$supplierName</option>";
						}
						
						// echo out end supplier selection box tag
						echo "</select>";
						// close sql connection
						mysqli_close($con);
						?>
					</p>
					<br>
					
					<!--submit and clear buttons-->
					<div class = "buttonscontainer"><!--button container-->
						<!-- submit form button -->
						<div class = "inlinefieldcontainer">
							<button type = "submit" name = "submitButton">Submit</button>
						</div>
						<!-- cancel/return to main amend-view-stock-item page -->
						<div class = "inlinefieldcontainer">
							<button type = "button" onclick = "return cancelAction()">Cancel</button>
						</div>
					</div><!--end button container div-->
				</div><!--end form input fields container div-->
				
			</form><!--form end-->
		</div><!--end form container div-->
		
	</body><!--body end-->
</html>
