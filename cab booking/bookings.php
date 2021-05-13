<!-- Bookings page stores the details of a customer on Successfully
entering the data into a table cabBooking. The user is notified
upon successful booking through an email.If tried to access the page without
logging in, it is redirected to login page -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Booking on CabsOnline" />
	<meta name="keywords" content="Booking, cabs" />
	<meta name="author" content="Sandeep Athiyarath" />
	<meta name="viewpoint" content="width=device-width, initial-scale=1" />
	<title> BOOKINGS </title>
	<link href="style.css" rel="stylesheet" />
</head>
	<body>
		<?php include "header.inc" ?>
		<?php include "nav.inc" ?>
		<section>
			<?php
					  // start a new session
            session_start();

            if (isset($_SESSION['email'])) {
						  	$email = $_SESSION['email'];
								// display the user mail id
								echo "<div><p id=\"user\">Hi $email</p><a class=\"btn special\" href=\"logout.php\">Logout</a></div>";

								$readyToSubmitForm = true;
								$errMsg = "";

								// function to sanitise input data from form
								function sanitise_input($data)
								{
									$data = trim($data);
									$data = stripslashes($data);
									$data = htmlspecialchars($data);
									return $data;
								}

								if ($_SERVER['REQUEST_METHOD'] == 'POST'){
									/*    DATA VALIDATION    */

									//check for passenger name
									if (isset($_POST["passengername"]) && $_POST["passengername"] != "") {
										$passengername = sanitise_input($_POST["passengername"]);
									}
									else {
										$errMsg .= "<p>You must enter a passenger name</p>";
										$readyToSubmitForm = false;
									}
									// pattern check for passenger name
									if (!preg_match("/^[a-zA-Z ]*$/", $_POST["passengername"])) {
										$errMsg .= "<p>Invalid name. Please use alphabets and space only </p>";
										$readyToSubmitForm = false;
									}

									// check if phone number is entered
									if (isset($_POST["passengerphone"]) && $_POST["passengerphone"] != "") {
										$passengerphone = sanitise_input($_POST["passengerphone"]);
									}
									else {
										$errMsg .= "<p>You must enter phone number</p>";
										$readyToSubmitForm = false;
									}

									// check for phoneno pattern
									if (!preg_match("/^[0-9]{8,12}$/", $_POST["passengerphone"])) {
										$errMsg .= "<p>Invalid phone. Use only digits and space. 8-12 digits</p>";
										$readyToSubmitForm = false;
									}

									// store unit number
									if (isset($_POST["unitnumber"])) {
										$unitnumber = sanitise_input($_POST["unitnumber"]);
									}

									// check for street number is empty or not
									if (isset($_POST["streetnumber"]) && $_POST["streetnumber"] != "") {
										$streetnumber = sanitise_input($_POST["streetnumber"]);
									}
									else {
										$errMsg .= "<p>You must enter a street number</p>";
										$readyToSubmitForm = false;
									}

									// validating the pattern for street number
									if (!preg_match("/^[0-9]*$/", $_POST["streetnumber"])) {
										$errMsg .= "<p>Invalid street number. Use only numbers</p>";
										$readyToSubmitForm = false;
									}

									// street name checked for nil
									if (isset($_POST["streetname"]) && $_POST["streetname"] != "") {
										$streetname = sanitise_input($_POST["streetname"]);
									}
									else {
										$errMsg .= "<p>You must enter a street name</p>";
										$readyToSubmitForm = false;
									}

									// checking pattern for valid street name
									if (!preg_match("/^[a-zA-Z ]*$/", $_POST["streetname"])) {
										$errMsg .= "<p>Invalid street name. Use alphabets and spaces</p>";
										$readyToSubmitForm = false;
									}

									// check if value is persent for suburb
									if (isset($_POST["suburb"]) && $_POST["suburb"] != "") {
										$suburb = sanitise_input($_POST["suburb"]);
									}
									else {
										$errMsg .= "<p>You must enter a suburb</p>";
										$readyToSubmitForm = false;
									}

									// checking for valid pattern for Suburb
									if (!preg_match("/^[a-zA-Z ]*$/", $_POST["suburb"])) {
										$errMsg .= "<p>Invalid suburb. Use alphabets and spaces</p>";
										$readyToSubmitForm = false;
									}

									// check if value is entered for destinationsuburb
									if (isset($_POST["destinationsuburb"]) && $_POST["destinationsuburb"] != "") {
										$destinationsuburb = sanitise_input($_POST["destinationsuburb"]);
									}
									else {
										$errMsg .= "<p>You must enter a suburb</p>";
										$readyToSubmitForm = false;
									}

									// pattern check for destinationsuburb
									if (!preg_match("/^[a-zA-Z ]*$/", $_POST["destinationsuburb"])) {
										$errMsg .= "<p>Invalid destination suburb format. Use alphabets and spaces</p>";
										$readyToSubmitForm = false;
									}

									// check if pick up date and time are entered
									if (isset($_POST["pickupdetails"]) && $_POST["pickupdetails"] != "") {
										$pickupdetails = sanitise_input($_POST["pickupdetails"]);
									}
									else {
										$errMsg .= "<p>You must enter a pick up date and time</p>";
										$readyToSubmitForm = false;
									}

									require_once("settings.php");
									$conn = @mysqli_connect(
										$host,
										$user,
										$pwd,
										$sql_db
									);

									if (!$conn) {
										echo "<p>Database connection failure</p>";
									} else {
										$query = $listAllBookings;
										$result = mysqli_query($conn, $query);

										// create cabBookin table if not created
										if (empty($result)) {
											$query = $createBookingTableQuery;
											$result = mysqli_query($conn, $query) or die('Error in creating table');
										}

										date_default_timezone_set ( "Australia/Victoria" );
										$bookedon = date("Y-m-d H:i:s");
										$start_time=strtotime($bookedon);
										$end_time=strtotime($pickupdetails);
										// difference between booking and pickup time is calculated and stored in minutes
										$diff=($end_time-$start_time)/60;

										if($readyToSubmitForm){
											if($diff>40){

												$status = "UNALLOCATED";
												// query to insert into the table to make a booking
												$insertQuery = $insertIntoBookingTableQuery."('NULL','$email','$passengername','$passengerphone',
																			'$unitnumber','$streetnumber','$streetname','$suburb',
																			'$destinationsuburb', '$pickupdetails',
																			'$bookedon','$status');";
												$insertResult = mysqli_query($conn, $insertQuery)
												or die('Error in inserting');

												// query to identify rh customername and reference number for the booking made
												$searchquery = "SELECT b.refno,c.customername
																				FROM customer c
																				INNER JOIN cabBooking b
																				ON c.email = b.email
																				WHERE b.bookedon = '$bookedon'
																				AND b.email = '$email';";
												$result = mysqli_query($conn, $searchquery) or die('could not find the data');

												if($result){
													while ($row = mysqli_fetch_assoc($result)) {
														$refno = $row["refno"];
														$customer = $row["customername"];
														$splitpickupdetails= explode("T",$pickupdetails);
														$date = $splitpickupdetails[0];
														$time = $splitpickupdetails[1];
														// alert message on successful booking of a cab
														echo "<script type='text/javascript'>alert('Thank you for booking with CabsOnline! Your booking reference number is $refno. We will pick up the passengers in front of your provided address at $time on $date ');
														</script>";

														$to = $email;
														$subject = "Your booking request confirmation mail ";
														$txt = "Dear $customer , \nThank you for booking with CabsOnline! \nYour booking reference number is $refno. \nWe will pick up the passenger in front of your provided address on $date at $time";
														$headers = "From: booking@cabsonline.com.au" . "\r\n";
														// send confirmation mail to the customer with details
														mail($to,$subject,$txt,$headers,"-r 102005528@student.swin.edu.au");
													}
													mysqli_free_result($result);
												}

											}
											else{
													echo "<script type='text/javascript'>alert('Pickip time should be atleast 40 minutes from the booking time. Please recheck and change the pickup time');</script>";
											}
										}

							  }
							}
			?>
		</section>
		<section>
			<h2> Booking a cab on CabsOnline </h2>
			<form method="post">
				<fieldset>
					<?php echo "<span style='color:red'> $errMsg </span>"; ?>
					<p>Please fill in the fields to book a taxi</p>

					<p><label for="pname"><strong>Passenger Name</strong></label>
					<input id="pname" type="text" name="passengername" required="required"  placeholder="max 20 characters" maxlength="20" /></p>

					<p><label for="ph1"><strong>Contact phone of the passenger</strong></label>
					<input id="ph1" type="text" name="passengerphone" required="required" size="20" placeholder="8 to 12 digits(spaces are allowed)" /></p>
					<fieldset>
						<legend>Pick up Address</legend>
						<p><label for="unitnumber"><strong>Unit Number</strong></label>
						<input id="unitnumber" type="text" name="unitnumber" /></p>

						<p><label for="streetnumber"><strong>Street Number</strong></label>
						<input id="streetnumber" type="text" name="streetnumber" required="required" /></p>

						<p><label for="streetname"><strong>Street Name</strong></label>
				        <input id="streetname" type="text" name="streetname" required="required" maxlength="20" /></p>

						<p><label for="suburb"><strong>Suburb</strong></label>
						<input id="suburb" type="text" name="suburb" required="required" maxlength="20" /></p>
					</fieldset>

					<p><label for="destinationsuburb"><strong>Destination Suburb</strong></label>
					<input id="destinationsuburb" type="text" name="destinationsuburb" required="required" maxlength="20" /></p>

					<p><label for="pickupdetails"><strong>Pick up date</strong></label>
					<input type="datetime-local" name="pickupdetails" id="pickupdetails" required></p>

					<input class="btn" type="submit" value="Register" />
					<input class="btn" type="reset" value="Clear" />
				</fieldset>
			</form>
			<?php } else {
                header('Location: login.php');
            }?>

		</section>
		<?php include "footer.inc" ?>
	</body>
</html>
