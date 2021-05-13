<!-- Register page lets the customer register their details and is redirected to
the bookings page on successful registration -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Register to CabsOnline" />
	<meta name="keywords" content="Booking, cabs" />
	<meta name="author" content="Sandeep Athiyarath" />
	<meta name="viewpoint" content="width=device-width, initial-scale=1" />
	<title> REGISTER </title>
	<link href="style.css" rel="stylesheet" />
</head>
	<body>
		<?php include "header.inc" ?>
		<?php include "nav.inc" ?>
		<section>
			<?php
				// start session
				session_start();

				$errMsg = "";
				$readyToSubmitForm = true;

				// function to sanitise the data
				function sanitise_input($data)
				{
					$data = trim($data);
					$data = stripslashes($data);
					$data = htmlspecialchars($data);
					return $data;
				}

				if ($_SERVER['REQUEST_METHOD'] == 'POST'){

					// check if email id is entered
					if (isset($_POST["email"]) && $_POST["email"] != "") {
						$email = sanitise_input($_POST["email"]);
					}
					else {
						$errMsg .= "<p>You must enter email</p>";
						$readyToSubmitForm = false;
					}

					// check if password is entered
					if (isset($_POST["userpassword"]) && $_POST["userpassword"] != "") {
						$userpassword = sanitise_input($_POST["userpassword"]);
					}
					else {
						$errMsg .= "<p>You must enter a password</p>";
						$readyToSubmitForm = false;
					}

					// check if password is reentered
					if (isset($_POST["confirmpassword"]) && $_POST["confirmpassword"] != "") {
						$confirmpassword = sanitise_input($_POST["confirmpassword"]);
					}
					else {
						$errMsg .= "<p>You must enter password again for confirmation</p>";
						$readyToSubmitForm = false;
					}

					// check if the two passwords entered matches
					if (strcmp($userpassword,$confirmpassword) != 0) {
						$errMsg .= "<p>The passwords do not match. Please recheck and try again</p>";
						$readyToSubmitForm = false;
					}

					// check if the customer name is entered
					if (isset($_POST["cname"]) && $_POST["cname"] != "") {
						$cname = sanitise_input($_POST["cname"]);
					}
					else {
						$errMsg .= "<p>You must enter customer name</p>";
						$readyToSubmitForm = false;
					}

					// check if the name is of a valid format
					if (!preg_match("/^[a-zA-Z ]*$/", $_POST["cname"])) {
						$errMsg .= "<p>Invalid name. Only Alphabets and spaces are allowed</p>";
						$readyToSubmitForm = false;
					}

					// check if phoneno is entered
					if (isset($_POST["phone"]) && $_POST["phone"] != "") {
						$phone = sanitise_input($_POST["phone"]);
					}
					else {
						$errMsg .= "<p>You must enter phone number</p>";
						$readyToSubmitForm = false;
					}

					// check for valid phone no
					if (!preg_match("/^[0-9]{8,12}$/", $_POST["phone"])) {
						$errMsg .= "<p>Invalid phone. Please use only numbers. 8-12 digits</p>";
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
					}
					else {
						if($readyToSubmitForm){
							$query = $listAllCustomers;
							$result = mysqli_query($conn, $query);

							// create table if not created
							if (empty($result)) {
								$query = $createCustomerTableQuery;
								$result = mysqli_query($conn, $query) or die('Error in creating table');
							}
							// query to check the uniqueness of the email id
							$query = "SELECT * FROM `customer` WHERE email='$email';";
							$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
							$count = mysqli_num_rows($result);

							// insert into table if no duplicate id is found
							if($count==0){
								$insertQuery = $insertIntoCustomerTableQuery."('$email','$userpassword','$cname','$phone');";
								$insertResult = mysqli_query($conn, $insertQuery)
								or die('Error in inserting');
								// pass the email to bookings page by creating a new session
								$_SESSION['email'] = $email;
							}
							else{
								$errMsg .= "<p>Email id already in use. Try with a different mail id</p>";
							}mysqli_close($conn);
						}

					}
				}

				// redirect to bookings page if registration is successful
				if (isset($_SESSION['email'])) {
					$email = $_SESSION['email'];
					header('Location: bookings.php');
				}
				else {
				?>
		</section>
		<section>
			<h2> Register to CabsOnline </h2>
			<form method="post" action="register.php">
				<fieldset>
					<legend>Registration Details</legend>
					<?php echo "<span style='color:red'> $errMsg </span>"; ?>
					<p>Please fill in the following details to complete the registration</p>

					<p><label for="cname"><strong>Name</strong></label>
					<input id="cname" type="text" name="cname" required="required"  placeholder="max 20 characters" maxlength="20" /></p>

					<p><label for="userPassword"><strong>Password</strong></label>
					<input type="password" name="userpassword" id="userpassword" placeholder="Password" required></p>

					<p><label for="confirmpassword"><strong>Password</strong></label>
					<input type="password" name="confirmpassword" id="confirmPassword" placeholder="Confirm password" required></p>

					<p><label><strong>e-mail   </strong><input type="email" name="email" size="20" placeholder="email" required="required" /></label></p>
					<p><label for="phone"><strong>Phone </strong></label>

					<input id="phone" type="text" name="phone" required="required" size="20" placeholder="8 to 12 digits(spaces are allowed)" /></p>

					<input class="btn" type="submit" value="Register" />
					<input class="btn" type="reset" value="Clear" />
				</fieldset>
				<p><strong> Already registered ?</strong> <a href="login.php"> Login Here</a></p>
			</form>
		<?php } ?>

		</section>
		<?php include "footer.inc" ?>
	</body>
</html>
