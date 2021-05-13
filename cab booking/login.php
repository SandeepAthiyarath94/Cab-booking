<!-- Login page lets the customer login to the bookings page -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Login to CabsOnline" />
	<meta name="keywords" content="Booking, cabs" />
	<meta name="author" content="Sandeep Athiyarath" />
	<meta name="viewpoint" content="width=device-width, initial-scale=1" />
	<title> LOGIN </title>
	<link href="style.css" rel="stylesheet" />
</head>
	<body>
		<?php include "header.inc" ?>
		<?php include "nav.inc" ?>
		<section>
			<?php
				// start the session
				session_start();

				$errMsg = "";
				// sanitising function
				function sanitise_input($data)
				{
					$data = trim($data);
					$data = stripslashes($data);
					$data = htmlspecialchars($data);
					return $data;
				}

				if ($_SERVER['REQUEST_METHOD'] == 'POST'){

					if (isset($_POST['email']) && isset($_POST['password'])) {

						$email = sanitise_input($_POST['email']);
						$password = sanitise_input($_POST['password']);

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
							// query oo check for matching details
							$query = "SELECT * FROM `customer` WHERE email='$email' and password='$password';";

							$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
							$count = mysqli_num_rows($result);
							if ($count == 1) {
								$_SESSION['email'] = $email;
							}
							else {
								$errMsg = "Invalid Login Credentials.";
							}
						}
					}
				}

				if (isset($_SESSION['email'])) {
					$email = $_SESSION['email'];
					// redirect to bookings page
					header('Location: bookings.php');
				}
				else {
			?>
		</section>
		<section>
			<h2> Login to CabsOnline </h2>
			<form method="post" action="login.php">
				<fieldset>
					<legend>Login Details</legend>
					<p><label><strong>e-mail   </strong><input type="email" name="email" size="20" placeholder="email" required="required" /></label></p>
					<p><label for="inputPassword"><strong>Password</strong></label>
					<input type="password" name="password" id="inputPassword" placeholder="Password" required></p>
					<input class="btn" type="submit" value="Login" />
					<input class="btn" type="reset" value="Clear" />
					<?php echo "<p><span style='color:red'> $errMsg </span></p>"; ?>
				</fieldset>
				<p><strong> New member ?</strong> <a href="register.php"> Register Now</a></p>
			</form>
			<?php } ?>
		</section>
		<?php include "footer.inc" ?>
	</body>
</html>
