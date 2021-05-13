<!-- Admin page displays the unallocated bookings present in the database for
the next 3 hours. the listed bookings can be assigned a cab by refering it by the
	reference number. The allocated booking is removed from the list of unallocated bookings -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Admin page for CabsOnline" />
	<meta name="keywords" content="Booking, cabs" />
	<meta name="author" content="Sandeep Athiyarath" />
	<meta name="viewpoint" content="width=device-width, initial-scale=1" />
	<title> ADMIN </title>
	<link href="style.css" rel="stylesheet" />
</head>
	<body>
		<?php include "header.inc" ?>
		<?php include "nav.inc" ?>
		<section>
			<h2> Admin page of CabsOnline </h2>

            <form method="post">
				<p><strong>1. Click below button to search for all unassigned booking requests with a pick-up time within 3 hours</strong></p>
                <input type="submit" name="listAll"class="btn" value="List All" />
            </form>
						<?php

							// function to display all the unallocated cab bookings for the next hours
							function viewAllbookingsForNext3Hours($conn){
								// sets default time zone to Melbourne Victoria
								date_default_timezone_set ( "Australia/Victoria" );
								// current time is stored
								$currentTime = date("Y-m-d H:i:s");
								// to store the time after 3 hours from current time
								$uppetTimeLimit = date("Y-m-d H:i:s",strtotime("+3 hours"));
								// query to display all the unallocated bookings for the next 3 hours
								$listQuery = "SELECT c.customername, b.refno, b.passengername, b.contactno, b.unitno,
															b.streetno, b.streetname, b.suburb, b.destinationsuburb, b.pickupon
															FROM customer c
															INNER JOIN cabBooking b
															ON c.email = b.email
															WHERE b.status = 'UNALLOCATED'
															AND b.pickupon BETWEEN '$currentTime' AND '$uppetTimeLimit';";
								$result = mysqli_query($conn, $listQuery);

								// printing out the table
								if ($result) {
										echo "<h1>List of Bookings for the next 3 hours</h1>";
										echo "<table>";
										echo "<thead>";
										echo "<tr>";
										echo "<th>Reference No</th>";
										echo "<th>Customer Name</th>";
										echo "<th>Passenger Name</th>";
										echo "<th>Passenger Contact Phone</th>";
										echo "<th>Pick up Address</th>";
										echo "<th>Destination suburb</th>";
										echo "<th>Pick up time</th>";
										echo "</tr>";
										echo "</thead>";
										echo "<tbody>";

										// reading row by row and displaying in a table format
										while ($row = mysqli_fetch_assoc($result)) {
												echo "<tr>";
												echo "<td>", $row["refno"], "</td>";
												echo "<td>", $row["customername"], "</td>";
												echo "<td>", $row["passengername"], "</td>";
												echo "<td>", $row["contactno"], "</td>";

												// formating the address based on whether the unit number is present or not
												if(is_null($row["unitno"]) || $row["unitno"]==""){
													$pickUpAddress = $row["streetno"]." ".$row["streetname"].", ".$row["suburb"];
												}else{
													$pickUpAddress = $row["unitno"]."/".$row["streetno"]." ".$row["streetname"].", ".$row["suburb"];
												}

												echo "<td>", $pickUpAddress, "</td>";
												echo "<td>", $row["destinationsuburb"], "</td>";
												echo "<td>", $row["pickupon"], "</td>";
												echo "</tr>";
										}

										echo "</tbody>";
										echo "</table>";
										mysqli_free_result($result);
								}
							}

							// function to allocate a driver to the bookings
							function updateTable($conn,$refno){

									// setting tie zone to local Australian time
								  date_default_timezone_set ( "Australia/Victoria" );
								  $currentTime = date("Y-m-d H:i:s");
								  $uppetTimeLimit = date("Y-m-d H:i:s",strtotime("+3 hours"));

									// query to search for unallocated bookings between current time and 3 hours from current time
									$searchquery = "SELECT * FROM cabBooking
																	WHERE refno=$refno
																	AND status='UNALLOCATED'
																	AND pickupon BETWEEN '$currentTime' AND '$uppetTimeLimit';";

									// query to update the status of the booking
									$updateRowQuery = "UPDATE cabBooking
																		 SET status='ALLOCATED'
																		 WHERE refno=$refno;";

									// check for any match for the entered reference number
									$checkresult = mysqli_query($conn, $searchquery);
									$count = mysqli_num_rows($checkresult);

									// display message when the refernce number entered by the user is not found in the table displayed
									if($count==0){
										$notifyUser = true;
										$notifymsg = "<p>The reference number you entered not present in the above table Possible reasons are refno not found or is already assigned a driver</p>";
									}else{
										$updateResult = mysqli_query($conn, $updateRowQuery);
										// update the row
										if ($updateResult) {
												$notifymsg = "Successfully assigned a cab driver to ref no $refno";
										}

									}
									return $notifymsg;
							}

							$errMsg = "";
							$notifymsg = "";

							// function to sanitise the input dtaa
							function sanitise_input($data)
							{
								$data = trim($data);
								$data = stripslashes($data);
								$data = htmlspecialchars($data);
								return $data;
							}

							require_once("settings.php");
							$conn = @mysqli_connect(
									$host,
									$user,
									$pwd,
									$sql_db
							);

							$refnoFound = true;

							if ($_SERVER['REQUEST_METHOD'] === 'POST') {
								 // if listall button is clicked, display the table
						     if (isset($_POST['listAll'])) {
									  viewAllbookingsForNext3Hours($conn);
						     } else {

									 // check if the reference number is provided by the user
									 if (isset($_POST["updaterefno"]) && $_POST["updaterefno"] != "") {
										 $updaterefno = sanitise_input($_POST["updaterefno"]);
									 }
									 else {
										 $errMsg .= "<p>You must enter a reference number</p>";
										 $refnoFound = false;
									 }

									 // check if the entered reference number is of valid format
									 if (!preg_match("/^[0-9]*$/", $_POST["updaterefno"])) {
										 $errMsg .= "<p>Invalid value for a reference number.</p>";
										 $refnoFound = false;
									 }

									 if($refnoFound){
										 // function call to update the table row
										 $notifymsg = updateTable($conn, $updaterefno);
									 }
									 // display updated table
									 viewAllbookingsForNext3Hours($conn);

						     }
						  }
							mysqli_close($conn);
						?>
            <form method="post">
								<p><strong>2. Input reference number and click "update" button to assign a taxi to that requester</strong></p>
                <p><label>Reference Number<input type="text" name="updaterefno" /></label></p>
                <input type="submit" name="update" class="btn" value="Update" />
								<?php
									if(!$refnoFound){echo $errMsg; }
									echo $notifymsg;
								?>
            </form>
		</section>
		<?php include "footer.inc" ?>
	</body>
</html>
