<!-- Data required to Establish connection to SQL database and some SQL queries
author : Sandeep Athiyarath 102005528
-->
<?php
	$host = "feenix-mariadb.swin.edu.au";
	$user = "s102005528";
	$pwd = "sandeep94";
	$sql_db = "s102005528_db";

	$sql_customer_table= "customer";
	$sql_booking_table= "cabBooking";

	$createCustomerTableQuery = "CREATE TABLE if not exists `$sql_db`.`$sql_customer_table`
															( `email` VARCHAR(30) NOT NULL ,
															  `password` VARCHAR(20) NOT NULL,
																`customername` VARCHAR(30) NOT NULL ,
																`phoneno` VARCHAR(15) NOT NULL ,
																PRIMARY KEY (`email`))";

$createBookingTableQuery = "CREATE TABLE if not exists `$sql_db`.`$sql_booking_table`
														( `refno` INT NOT NULL AUTO_INCREMENT,
														  `email` VARCHAR(30) NOT NULL ,
														  `passengername` VARCHAR(20) NOT NULL ,
															`contactno` VARCHAR(12) NOT NULL ,
															`unitno` VARCHAR(5) ,
															`streetno` INT NOT NULL,
															`streetname` VARCHAR(20) NOT NULL ,
															`suburb` VARCHAR(20) NOT NULL ,
															`destinationsuburb` VARCHAR(20) NOT NULL ,
															`pickupon` DATETIME NOT NULL,
															`bookedon` DATETIME NOT NULL,
															`status` VARCHAR(15) NOT NULL ,
															PRIMARY KEY (`refno`),
															FOREIGN KEY(`email`) REFERENCES customer(`email`))";

	$insertIntoCustomerTableQuery = "INSERT INTO `customer`(`email`, `password`, `customername`, `phoneno`) VALUES ";

	$insertIntoBookingTableQuery = "INSERT INTO `cabBooking`
																	(`refno`, `email`,`passengername`,`contactno`,
																		`unitno`,`streetno`,`streetname`,`suburb`,
																		`destinationsuburb`,`pickupon`,`bookedon`,
																		`status`) VALUES ";

	$listAllBookings = "SELECT b.refno FROM cabBooking b";
	$listAllCustomers = "SELECT * FROM customer c";
?>
