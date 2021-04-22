<!--
Student Name : Jonathan Suryadiputra 
Student ID : C00235450
Group : C2P-B
Course : CW_KCSOF_B
Date : 20/4/20
Purpose of Page : This page allows the user add transaction records, acting as a 'cash register' UI.
-->
<html> <!-- html tag -->
	<!-- head tag and segment -->
	<head>
		<!-- title tag -->
		<title>Optician System > Counter Sales</title>
		<!--#include file="button_bar.php" -->
		<?php include("button_bar.php"); ?>
		<!--css file import-->
		<link rel = "stylesheet" type = "text/css" href = "css/countersales.css">
	</head>
	
	<!-- body tag and segment -->
	<body>
		<?php
		//database connection inclusion
		include 'db.inc.php';
		//set timezone to UTC
		date_default_timezone_set('UTC');
		?>
		
		<!--'form' segment; to push whatever item information purchased to the addSale.php file to make updates in the DB-->
		<form action = "addSale.php" method = "POST" onsubmit = "return confirmCheck()">
			<!-- flex outer container to house the two inline table containers in it -->
			<div class = "outercontainer">
				<!-- stock list table inline div segment -->
				<div class = "inlinecontainer">
					<!-- header container -->
					<div class = "headercontainer">
						<h1>&emsp;&ensp;Stock Items</h1>
					</div>
					<!-- stock table container -->
					<div class = "stocktablecontainer">
						<?php
						//sql query string to get stock items data from database
						$sql = "SELECT * FROM Stock WHERE deleteFlag = false";
						//php function call to build stock list table
						produceReport($con,$sql);

						//php function to build stock list table
						function produceReport($con,$sql) {
							//make sql query and set it to result variable
							$result = mysqli_query($con,$sql);
							//set up table header and column names and start table body with the id 'stocklistbody'
							echo "	<table>
									<thead>
									<tr><div style = 'display:flex; vertical-align:middle;'><input type = 'text' class = 'searchbar' id = 'searchbar' onkeyup = 'searchFilter()' placeholder = 'Search for stock number or description...'><button class = 'searchbutton' type = 'button' onclick = 'clearSearch()'>&times;</button></div></tr>
									<tr><th class = 'stocknum'>Stock No.</th><th class = 'description'>Description</th><th class = 'unitprice'>Unit Price</th><th class = 'qtyinstock'>Qty In Stock</th><th class = 'addtocart'></th></tr>
									</thead>
									<tbody id = 'stocklistbody'>";
							// loop for as many row as the sql query brings back
							while ($row = mysqli_fetch_array($result)) {	
								// Set up table rows for display in the loop
								echo "<tr";
								// if the quantity in stock is zero
								if ($row['quantityInStock'] == 0) {
									echo " class = 'outofstock'";
								}
								echo "	>";
								// display the stock information on the table row cells
								echo "	<td class = 'stocknum'>" .$row['stockNumber']. "</td>
										<td class = 'description'>" . $row['description'] . "</td>
										<td class = 'unitprice'>" . $row['retailPrice'] . "</td>
										<td class = 'qtyinstock'>" . $row['quantityInStock'] . "</td>
										<td class = 'addtocart'><div align = 'center'>";
								// set up 'addtocart' button on each row, set the button's value to a '|' separated value to avoid confusion with commas or dots
								echo "	<button class = 'tablebutton' type = 'button' value = '" . $row['stockNumber'] . "|" . $row['description'] . "|" . $row['retailPrice'] . "' onclick = 'addToCart(this.value)'";
								// if the quantity in stock is zero, add to cart button is disabled and shows 'out of stock!' message instead
								if ($row['quantityInStock'] == 0) {
									echo "disabled>Out of Stock!</button>";
								}
								// else the button remains enabled and shows 'add to cart' message
								else {
									echo ">Add to Cart</button>";
								}
								echo "</div></td></tr>";
							}
							// end of table body and the whole table
							echo "</tbody></table>";
						}
						?>
					</div> <!-- end of stock table container-->
				</div> <!-- end of inline container for stock list table -->
				
				<!-- shopping cart table div segment -->
				<div class = "inlinecontainer">
					<!-- header container -->
					<div class = "headercontainer">
						<h1>&emsp;&ensp;Shopping Cart</h1>
					</div>
					<div class = "shoppingcartcontainer">
						<!-- set up empty table and table header columns -->
						<table id = "shoppingcart" name = "shoppingcart">
							<thead>
							<tr><th class = "cartstocknum">Stock No.</th><th class = "cartdescription">Description</th><th class = "cartbuyquantity">Quantity</th><th class = "carttotalprice">Total Price</th><th class = "remove">Remove</th><th class = "hidden"></th></tr>
							</thead>
							<!-- set up empty table body, contents are added once items from the stock list are added to cart through the add to cart button -->
							<tbody class = "shoppingcarttbody" id = "shoppingcarttbody"></tbody>
						</table>
						<!-- this input field acts as a counter, for the number of rows present in the 'shopping cart' table, it is hidden and is only there to be accessed in the sql insert php file -->
						<input type = "hidden" name = "numberofrows" id = "numberofrows" value = 0>
					</div>
					<!-- set up footer and its container displaying the grand total and 'checkout' button-->
					<div class = "footercontainer">
						<label class = "grandtotallabel">Grand Total</label>
						<input class = "grandtotal" type = "text" name = "grandtotal" id = "grandtotal" value = 0.00 disabled>
						<!-- checkout button -->
						<!-- This button is disabled at first but will be enabled once user starts adding items to cart from the stock items table list -->
						<button type = submit class = "checkoutbutton" id = "checkout" name = "checkout" disabled>Checkout</button>
						<!-- clear cart button -->
						<!-- Again this button is also disabled at first but will be enabled once user starts adding items to cart from the stock items table list -->
						<button type = button class = "clearcartbutton" id = "clearcart" name = "clearcart" onclick = "clearCart()" disabled>Clear Cart</button>
					</div> <!-- end of footer container -->
				</div> <!-- end of inline container for shopping cart list -->
			</div> <!-- end outer layout container -->
		</form> <!-- end of 'form' segment -->
		
		<?php
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
		
		// sql query for quantity in stock for each stock item, this is to compare with the quantity of an item added to the cart, if it's larger than the quantity in stock
		$result = mysqli_query($con,$sql);
		// set up some sort of an array comprising of hidden inputs with values containing the queried stock quantity for each stock item with their respective stock number as an index embedded in the id of each hidden textfield input
		while ($row = mysqli_fetch_array($result)) {
			echo "<input type = 'hidden' id = 'queriedquantity" . $row['stockNumber'] . "' value = '" . $row['quantityInStock'] . "'>";
		}
		// close sql connection
		mysqli_close($con);
		?>
		
		<!-- sale ID hidden for javascript reference for submit message popup -->
		<input type = "hidden" id = "saleid" value = <?php echo $countersaleid; ?> disabled/>
		
		<!-- javascript code segment -->
		<script>
			// javascript function to confirm check everything after user is done
			function confirmCheck() {
				var response;
				response = confirm('Please check and confirm that the items have been correctly added.');
				
				if(response) {
					// enable grand total number field to allow the value in it to be submitted to the php action page and to be passed and inserted into the system's database for record keepsake
					document.getElementById("grandtotal").disabled = false;
					// alert the user and display the assigned sale ID as well
					alert("A sale record for this transaction has been added into database.\nAssigned sale ID : " + document.getElementById("saleid").value);
					return true;
				}
				else {
					return false;
				}
			}

			// input validation for the shopping cart quantity field just in case user types into it
			function checkQuantity(input) {
				// if the user types a value larger than the quantity in stock
				if (input.validity.rangeOverflow) {
					input.setCustomValidity("The quantity entered is more than the current stock quantity!");
				}
				// else if the quantity is '0' or a minus value
				else if (input.validity.rangeUnderflow) {
					input.setCustomValidity("The quantity entered is invalid!");
				}
				else {
					input.setCustomValidity("");
				}
			}

			// function to add items to shopping cart, this function has two conditions
			function addToCart(record) {
				// split the record string into individual data fields
				var stockDetails = record.split('|');
				var stocknumber = stockDetails[0];
				var description = stockDetails[1];
				var retailprice = stockDetails[2];

				// get number of rows in the table
				var rows = document.getElementById("shoppingcarttbody").getElementsByTagName("tr");
				var rowCount = rows.length;

				// find <table> element with the id "shoppingcart"
				var tablebody = document.getElementById("shoppingcarttbody");	

				// check if item has been previously added to cart, if it has, there is no need for a duplicate row in the shopping cart
				var tOrF = false;
				for (var i = 0; i < rowCount; i ++) {
					if (tablebody.rows[i].cells[0].innerHTML == stocknumber){
						tOrF = true;
					}
				}

				// the if statement for the two conditions
				// if the item has been added to the shopping cart previously, so there won't be any duplicate rows of the same stock item type, therefore add to cart button only acts as a 'plus button' adding one to the item's quantity to be purchased in the shopping cart table
				if (tOrF) {
					// get grandtotal before another item unit is added
					var grandtotal = parseFloat(document.getElementById("grandtotal").value);
					// get item total price before another item unit is added
					var oldTotal = parseFloat(document.getElementById("totalprice" + stocknumber).value);
					// set up a temp variable that contains the value from the take away of total price before another item unit is added from the grandtotal
					var tempgrandtotal = grandtotal - oldTotal;
					// increase shopping cart quantity of the item by one
					document.getElementById("buyquantity" + stocknumber).stepUp(1);
					// calculate new item total price with the new quantity added one multiply by the unit price of the stock item
					var newTotal = retailprice * document.getElementById("buyquantity" + stocknumber).value;
					// set the new total price in the total price field on the table for display with 2 decimal places
					document.getElementById("totalprice" + stocknumber).value = newTotal.toFixed(2);
					// add new total price item with the grandtotal, previously rid of the old total price so the grandtotal is accurate
					var newgrandtotal = tempgrandtotal + newTotal;
					// set the new grand total to the grand total field on the table with 2 decimal places
					document.getElementById("grandtotal").value = newgrandtotal.toFixed(2);
				}
				// else if the item is added to cart for the first time, then a new row will be added with the item's details and quantity
				else {
					// get number of rows before a new row for the new stock item is added to cart
					var numberofrows = parseInt(document.getElementById("numberofrows").value);
					// insert new row for shopping cart table body
					var row = tablebody.insertRow(rowCount);
					// insert new cells for new row
					var cell1 = row.insertCell(0);
					var cell2 = row.insertCell(1);
					var cell3 = row.insertCell(2);
					var cell4 = row.insertCell(3);
					var cell5 = row.insertCell(4);
					var cell6 = row.insertCell(5);
					
					// hide the last cell, as it contains stocknumber inside a hidden input only to be accessed by the insert to sql php file
					cell6.style.display = "none";
					
					// insert class names for css references
					cell1.classList.add("cartstocknum");
					cell2.classList.add("cartdescription");
					cell3.classList.add("cartbuyquantity");
					cell4.classList.add("carttotalprice");
					cell5.classList.add("remove");

					// set cell contents. The element identifiers in the shopping cart table (whether it be the name, id, etc) are all associated with the stock number of that row's stock item for php and javascript references, except for the stock number field itself which is hidden and is associated with the table's row number instead as it is only there for php reference, hidden from the user's view.
					// set cell 1 for stock number
					cell1.innerHTML = stocknumber;
					// set cell 2 for description
					cell2.innerHTML = description;
					// set cell 3 for quantity field. This quantity field is a number field but with the default spinners removed, in place of it is a plus and minus buttons to it's left and right, both attached to their respective javascript functions. The quantity field itself has a min value of 1, and a max value that is the number of stock quantity available in inventory. The javascript function calculateTP() is attached to it, to the 'onchange' action listener, so whenever the user changes it either by typing it in, using 'add to cart' button on the stock list table or using the plus and minus buttons the total price will dynamically change through the function called.
					cell3.innerHTML = "<div class = 'quantity'><button class = 'plusbutton' type = 'button' value = '" + stocknumber + "' onclick = 'stepUp(this.value," + retailprice + ")'>+</button><input class = 'buyquantity' type = 'number' name = 'buyquantity" + stocknumber + "' id = 'buyquantity" + stocknumber + "' value = 1 min = 1 max = " + parseInt(document.getElementById("queriedquantity" + stocknumber).value) + " onchange = 'calculateTP(" + retailprice + ", " + stocknumber + ")' oninput = 'checkQuantity(this)' required><button class = 'minusbutton' type = 'button' value = '" + stocknumber + "' onclick = 'stepDown(this.value," + retailprice + ")'>-</button></div>";
					// set cell 4 for total price field. This total price field is disabled and is only changed through javascript function calls from other elements in the table body, e.g. whenever an item is added to cart or an item's quantity is changed, this field will also change dynamically in accordance to whatever changes the user made to the shopping cart table.
					cell4.innerHTML = "<input class = 'totalpricefield' type = 'text' name = 'totalprice" + stocknumber + "' id = 'totalprice" + stocknumber + "' value = '" + retailprice + "' disabled>";
					// set cell 5 for remove 'x' button that allows the user to remove a specific row of stock item type in the shopping cart. This button is also attached to a javascript function.
					cell5.innerHTML = "<div align = 'center'><button class = 'xbutton' type = 'button' onclick = 'removeItem(" + stocknumber + ")'>&times;</button></div>";
					// set cell 6 for a special hidden field containing the stock number of each row. Unlike other elements in the cart table, this field is associated with the row number in the table, this will be used for php reference, in turn the value of this element will be used to reference the other elements in this table as the other elements are associated with the stock number of the stock item details they contain.
					cell6.innerHTML = "<input type = 'hidden' name = 'stocknumber" + numberofrows + "' id = 'stocknumber" + numberofrows + "' value = '" + stocknumber + "'>";
					// do calculations on the grand total, unit total price, etc.
					// get grand total from the grand total field and parseFloat() it
					var grandtotal = parseFloat(document.getElementById("grandtotal").value);
					// parse retail price extracted from the stock item details string into float type
					var parsedretailprice = parseFloat(retailprice);
					// add the newly added stock item's retail price to the grand total
					var newgrandtotal = grandtotal + parsedretailprice;
					// set the grand total field's value with the new grand total with 2 decimal places
					document.getElementById("grandtotal").value = newgrandtotal.toFixed(2);
					// add 1 to the number of rows retrieved in a previous line of code
					var newnumberofrows = numberofrows + 1;
					// set the number of rows field with the new number of rows
					document.getElementById("numberofrows").value = newnumberofrows.toString();
					// enable the check out and clear cart buttons
					document.getElementById("checkout").disabled = false;
					document.getElementById("clearcart").disabled = false;
				}
			}
			
			// function to clear cart
			function clearCart() {
				// get table body element from the shopping cart table
				var tbody = document.getElementById("shoppingcarttbody");
				// loop through the table body, while table body still has 'children' (in this case its tr elements), this loop will stop once all rows have been removed
				while (tbody.hasChildNodes()) {
					// remove row each time the loop goes through
					tbody.removeChild(tbody.firstChild);
				}
				// set grand total value to zero
				document.getElementById("grandtotal").value = "0.00";
			}

			// function to calculate total price on each stock item row of the shopping cart, the stock number parameter is used for javascript element ID reference
			function calculateTP(retailprice, stocknumber) {
				// check if the stock number is valid and not a minus value
				if (document.getElementById("buyquantity" + stocknumber).value < 0 == false) {
					// get grand total from the form field and parseFloat() it to update it
					var grandTotal = parseFloat(document.getElementById("grandtotal").value);
					// get current, old total price from the form field and parseFloat() it
					var currentTP = parseFloat(document.getElementById("totalprice" + stocknumber).value);
					// set a temp variable do calculations to the grand total, take away the old total price from the grand total
					var grandTotalDecreased = grandTotal - currentTP;
					// get the quantity from the form field using the stock number as reference
					var quantity = document.getElementById("buyquantity" + stocknumber).value;
					// calculate new total price with the quantity and retail price parameter
					var newTotalPrice = quantity * retailprice;
					// set totalprice field value of the stock item in the cart to the new total price calculated from the previous line of code with two decimal places
					document.getElementById("totalprice" + stocknumber).value = newTotalPrice.toFixed(2);
					// calculate new grand total by adding the temp variable with the new total price value
					var newGrandTotal = grandTotalDecreased + newTotalPrice;
					// set grand total form field value to the new grand total value calculated with 2 decimal places
					document.getElementById("grandtotal").value = newGrandTotal.toFixed(2);
				}
			}
			
			// function to remove item row individually, this function is attached to the x remove buttons to the right side of shopping cart table, the index parameter is to be used for the stock number as stock numbers are used as indexes to the id of shopping cart elements
			function removeItem(index) {
				// get balance difference from the total price field
				var balanceDiff = parseFloat(document.getElementById("totalprice" + index).value);
				// get the grand total value from the form
				var grandtotal = parseFloat(document.getElementById("grandtotal").value);
				// take away total price from the grandtotal to get rid of whatever price the item to be removed has added to the grand total
				var newgrandtotal = grandtotal - balanceDiff;
				// set grand total field value to the new grand total with 2 decimal places
				document.getElementById("grandtotal").value = newgrandtotal.toFixed(2);
				
				// get number of rows that are in the shopping cart table
				var numOfRecords = parseInt(document.getElementById("numberofrows").value);
				// decrement or take 1 away from the number of rows
				var newnumofrows = numOfRecords - 1;
				// if condition to check if the number of rows is equal to zero
				if (newnumofrows == 0) {
					// if upon removing the item the shopping cart becomes empty i.e. the user removes the last item in the shopping cart, then the two buttons check out and clear cart should be disabled
					document.getElementById("checkout").disabled = true;
					document.getElementById("clearcart").disabled = true;
				}
				
				// set the new number of rows to the number of rows field in the shopping cart table
				document.getElementById("numberofrows").value = newnumofrows.toString();

				// piece of code that actually removes the row
				var button = event.target.parentNode; // event.target will be the X button element.
				var td = button.parentNode; // the cell containing the button is the parent of the button
				var tr = td.parentNode; // get the row containing the cell
				tr.parentNode.removeChild(tr); // get the tbody containing the row and do removeChild function which will then remove the row
			}
			
			// function to increase quantity of stock item on the shopping cart; attached to the plus button
			function stepUp(stocknumber,retailprice) {
				// get the grand total from the form and parseFloat() it
				var grandtotal = parseFloat(document.getElementById("grandtotal").value);
				// get the total price before quantity of stock item is increased and parseFloat() it
				var oldTotal = parseFloat(document.getElementById("totalprice" + stocknumber).value);
				// take away the old total price from the grand total and put it in a temp variable
				var tempgrandtotal = grandtotal - oldTotal;
				// step up the buy quantity number field on the shopping cart 'form' by one
				document.getElementById("buyquantity" + stocknumber).stepUp(1);
				// calculate the new total price by invoking the retail price and multiply it by the new quantity that has been stepped up
				var newTotal = retailprice * document.getElementById("buyquantity" + stocknumber).value;
				// set the new total price to the total price field value on the table 'form'
				document.getElementById("totalprice" + stocknumber).value = newTotal.toFixed(2);
				// add the new total price to the temp variable containing the solved grand total
				var newgrandtotal = tempgrandtotal + newTotal;
				// set the value of the grandtotal field with the new grand total value with two decimal places
				document.getElementById("grandtotal").value = newgrandtotal.toFixed(2);

			}
			
			// function to decrease quantity of stock item on the shopping cart; attached to the minus button
			function stepDown(stocknumber,retailprice) {
				// step down/decrement the buy quantity number field on the shopping cart 'form' by one
				document.getElementById("buyquantity" + stocknumber).stepDown(1);
				// get the grand total from the form and parseFloat() it
				var grandtotal = parseFloat(document.getElementById("grandtotal").value);
				// get the total price before quantity of stock item is decreased and parseFloat() it
				var oldtotal = parseFloat(document.getElementById("totalprice" + stocknumber).value);
				// take away the old total price from the grand total
				var grandtotal = grandtotal - oldtotal;
				// calculate the new total price by invoking the retail price and multiply it by the new quantity that has been decremented
				var newTotal = retailprice * document.getElementById("buyquantity" + stocknumber).value;
				// set the new total price to the total price field value on the table 'form'
				document.getElementById("totalprice" + stocknumber).value = newTotal.toFixed(2);
				// add the new total price to the solved grand total
				var newgrandtotal = grandtotal + newTotal;
				// set the value of the grandtotal field with the new grand total value with two decimal places
				document.getElementById("grandtotal").value = newgrandtotal.toFixed(2);
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
				var stocknumvalue;
				var descriptionvalue;

				// loop through all table rows, and only show what matches the search
				for (var i = 0; i < rows.length; i++) {
					stocknumtd = rows[i].getElementsByTagName("td")[0]; // get stock number column through the rows
					descriptiontd = rows[i].getElementsByTagName("td")[1]; // get description column through the rows
					if (stocknumtd || descriptiontd) {
						// set td contents to their respective variables to be compared in the if statement
						stocknumvalue = stocknumtd.textContent || stocknumtd.innerText;
						descriptionvalue = descriptiontd.textContent || descriptiontd.innerText;
						// compare values from the search field and the variables
						if (stocknumvalue.toUpperCase().indexOf(search) > -1 || descriptionvalue.toUpperCase().indexOf(search) > -1) {
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
		</script> <!-- end javascript -->
	</body> <!-- end body code segment -->
</html> <!-- html end -->
