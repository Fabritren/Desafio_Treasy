<?php
	// Tabelas disponíveis
	// Negócios (deals), Movimentações dos Negócios (deals_updates) e Estágios (stages).
	
	// Connect to DB given a $sql query
	
	// Information
	$servername = "35.193.118.123";
	$username_db = "challenge";
	$password_db = "challenge@123";
	$dbname = "ops_challenge";
	$db_port = 3306;
	
	// Clear conn and Handle_response variables
	unset($conn, $handle_response);
	
	// Create connection
	$conn = new mysqli($servername, $username_db, $password_db, $dbname, $db_port);
	
	// Check connection
	if ($conn->connect_error) {
		echo "Failed to connect<br/>";
		die("Connection to database failed: " . $conn->connect_error);
	}
	//echo "Successfully connected to database<br/>";
	
	//$start = microtime(true);
	
	//$sql = "SELECT * FROM deals";
	// Send the database query request
	$db_response = $conn->query($sql);
	
	// Close Database connection
	$conn->close(); 
	
	// Fetch response into an array
	while ($row = mysqli_fetch_assoc($db_response)) { 
		$handle_response[] = $row;
	}
	
	//$time_elapsed_secs = microtime(true) - $start;
	//echo "Time Spent: " . $time_elapsed_secs;
	
	
	// $handle_response is the desired response
?>