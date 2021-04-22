<!--
Student Name : Jonathan Suryadiputra 
Student ID : C00235450
Group : C2P-B
Course : CW_KCSOF_B
Date : 20/4/20
Purpose of Page : This page is to present the user with the HTML form to add a new stock item into the system DB and to submit the form.
-->
<html>
	<!--head start-->
	<head>
		<!--webpage title-->
		<title>Optician System > File Maintenance > Add a New Stock Item</title>
		<!--#include file="button_bar.php" -->
		<?php include("button_bar.php"); ?>
		<!--css file import-->
		<link rel = "stylesheet" type = "text/css" href = "css/addnewstock.css">
	</head><!--head end-->
	
	<!--javascript start-->
	<script>
		// javascript function to invoke message popup to confirm submission for the user
		function confirmCheck() {
			var response;
			response = confirm("Are you sure you want to submit this form?\nPlease check and confirm that the details entered are correct.");
			
			// if user confirms everything, proceed to submission
			if(response) {
				alert("The new stock record has been added into database.\nAssigned stock number : " + document.getElementById("stocknumber").value);
				return true;
			}
			else {
				return false;
			}	
		}
		
		// javascript function to perform cancel action and redirect user back to main menu page
		function cancelAction() {
			var response;
			response = confirm("Are you sure you want to cancel this form and go back to the home page?");
			
			if(response) {
				window.location.replace("https://c2popticians.candept.com/menu.php");
				return true
			}
			else {
				return false;
			}	
		}
		
		// function to check retail price to be more than or equal to the cost price
		function retailPriceCheck(input) {
			// if retail price is less than cost price value
			if (parseFloat(input.value) < parseFloat(document.getElementById("costprice").value) && parseFloat(input.value) >= input.min) {
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
		
	</script><!--end javascript code segment-->
	
	<!--body start-->
	<body>
		<div class = "container"><!--form container div-->
			<!--form start, form action derived from file = addNewStock.php-->
			<form name = 'addstockform' action = "addNewStock.php" method = "POST" onsubmit = "return confirmCheck()">
				<!--form title-->
				<div class = "formheadercontainer"><!--form header container div-->
					<p>New Stock Item</p>
				</div><!--end form head container div-->
				
				<div class = "formfieldscontainer"><!--input fields & buttons container div-->
					<!--description input field-->
					<p><label for = "description">Description</label><br>
						<textarea name = "description" id = "description" rows = "5" required></textarea>
					</p>

					<!--cost price input field-->
					<div class = "inlinefieldcontainer">
						<p><label for = "costprice">Cost Price</label><br>
							<input class = "numberfield" type = "number" name = "costprice" min = "0.01" step = "0.01" id = "costprice" oninput = "numberValidCheck(this)" required/>
						</p>
					</div>
					
					<!--retail price input field-->
					<div class = "inlinefieldcontainer">
						<p><label for = "retailprice">Retail Price</label><br>
							<input class = "numberfield" type = "number" name = "retailprice" min = "0.01" step = "0.01" id = "retailprice" oninput = "retailPriceCheck(this)" required/>
						</p>
					</div>
				  
				  
					<!--reorder level input field-->
					<div class = "inlinefieldcontainer">
						<p><label for = "reorderlevel">Reorder Level</label><br>
							<input class = "numberfield" type = "number" name = "reorderlevel" min = "0" step = "1" id = "reorderlevel" oninput = "numberValidCheck(this)" required/>
						</p>
					</div>
					
				  
					<!--reorder quantity input field-->
					<div class = "inlinefieldcontainer">
						<p><label for = "reorderquantity">Reorder Quantity</label><br>
							<input class = "numberfield" type = "number" name = "reorderquantity" min = "0" step = "1" id = "reorderquantity" oninput = "numberValidCheck(this)" required/>
						</p>
					</div>
					
					<!--supplier selection box-->
					<p><label for = "suppliername">Supplier Name</label><br>
						<?php include 'listsuppliers.php'; ?>
					</p>
				  
					<!--supplier stock code input field-->
					<p><label for = "supplierstockcode">Supplier Stock Code</label><br>
						<input class = "textfield" type = "text" name = "supplierstockcode" id = "supplierstockcode" autocomplete = off required/>
					</p>
					<br>
					
					<!--submit and reset buttons-->
					<div class = "buttonscontainer"><!--button container-->
						<!-- submit form button -->
						<div class = "inlinefieldcontainer">
							<button type = "submit" name = "submitButton">Submit</button>
						</div>
						<!-- clear button -->
						<div class = "inlinefieldcontainer">
							<button type = "reset">Clear</button>
						</div>
						<!-- cancel/return to menu button -->
						<div class = "inlinefieldcontainer">
							<button type = "button" onclick = "return cancelAction()">Cancel</button>
						</div>
					</div><!--end button container div-->
				</div><!--end form input fields container div-->
				
			</form><!--form end-->
		</div><!--end form container div-->
		
		<?php
		// include database connection php file
		include 'db.inc.php';
		// get next stockNumber
		$stockNumQuery = mysqli_query($con, "SELECT MAX(stockNumber) AS maxStockNum FROM Stock");
		// if mysqli query has an error
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
		// close mysqli connection
		mysqli_close($con);
		?>
		
		<!-- stock number hidden for javascript reference for submit message popup -->
		<input type = "hidden" id = "stocknumber" value = <?php echo $stocknumber; ?> disabled/>
		
	</body> <!-- body end -->
</html> <!-- html end tag -->
