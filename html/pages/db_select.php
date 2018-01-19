
<?php 

	// Get Specifications POSTED for the Database Search
	
	// problem_number = 1 or 2
	// year_select = year to look for
	// year_week_begin = week of the year start
	// year_week_end = week of the year end
	
	if (isset($_POST['problem_number'])!=""){
		$problem_number = $_POST['problem_number'];
	}
	if (isset($_POST['year_select'])!=""){
		$year_select = $_POST['year_select'];
	}
	if (isset($_POST['year_week_begin'])!=""){
		$year_week_begin = $_POST['year_week_begin'];
	}
	if (isset($_POST['year_week_end'])!=""){
		$year_week_end = $_POST['year_week_end'];
	}
	
	
	// Function used to count every stage, given the response arrays
	function count_deals($output_table, $triagem_response, $handle_response){
		// Count every stage transition -> (stage_to) from deals_updates
		foreach ($handle_response as $key => $value){ //for ($i = 0; $i < sizeof($handle_response); $i++) {
			// Increment stage count
			$output_table[$handle_response[$key][stage_to]]++;
		}
		
		// The number of rows at handle_response is the amount of newly added (Triagem)
		// Increment this number at the output table:
		$output_table[utf8_decode("Triagem")] = $output_table[utf8_decode("Triagem")] + sizeof($triagem_response);
		
		return $output_table;
	}
	
	// SQL query for deals table, looking for newly added deals on stage_id 44 (Triagem)
	function sql_query_triagem($problem_number, $year_select, $year_week_begin, $year_week_end, $triagem_ID){
		if ($problem_number == 1){
			$deleted_or_not = "";
		}
		else{
			$deleted_or_not = "AND deleted = 0 ";
		}
		
		$sql = "SELECT 
					*
				FROM 
					deals 
				WHERE 
					YEAR(add_date) = $year_select 
					AND
					YEARWEEK(update_date) - YEARWEEK('$year_select-01-01') >= $year_week_begin - 1
					AND
					YEARWEEK(update_date) - YEARWEEK('$year_select-01-01') <= $year_week_end - 1
					AND
					stage_id = $triagem_ID
					$deleted_or_not
				ORDER BY 
					add_date ASC";
		
		// Send query to DB, receive $handle_response variable with answer
		if ($sql != ""){ include 'db_connect.php'; }
		
		return $handle_response;
	}
	
	function separate_week($triagem_response, $handle_response){
		
		$update_date = array_merge( (array)array_column($triagem_response, 'update_date'), (array)array_column($handle_response, 'update_date'));
		$stage_name = array_merge(array_fill(0,sizeof($triagem_response),'Triagem'), (array)array_column($handle_response, 'stage_to'));
		
		for ($i = 0; $i < sizeof($stage_name); $i++){
			
			$stage_name[$i] = utf8_encode($stage_name[$i]);
			
			$u = new DateTime($update_date[$i]);
			$update_date[$i] = (int)$u -> format("W");
			
			$output_by_week_chart[$update_date[$i]][$stage_name[$i]]++;
		}
		
		return $output_by_week_chart;
	
	}
	
	// Clear variables
	$sql = "";
	$output_text = "";
	
	// Array with every possible stage (output answer)
	$output_table = array(
		utf8_decode("Triagem") => 0,
		utf8_decode("Qualificação") => 0,
		utf8_decode("Reconhecimento do Problema") => 0,
		utf8_decode("Construção da Solução") => 0,
		utf8_decode("Decisão de Compra") => 0); // utf8_decode needed because of charset=ISO-8859-1 used in Treasy DB
	
	// look for id triagem on stages table
	// Request Stages Table
	$sql = "SELECT 
				*
			FROM 
				stages
			WHERE
				stage_name = 'Triagem'";


	// Send query to DB, receive $handle_response variable with answer
	if ($sql != ""){ include 'db_connect.php'; }
	
	if (sizeof($handle_response) >= 1){
		$triagem_ID = $handle_response[0][stage_id];
	}
	else{
		$triagem_ID = "";
		echo "Error, Triagem stage ID not found!";
	}
	
	
	// SQL query according to requirements
	
	// SQL query for deals table, looking for newly added deals on stage_id 44 (Triagem) 
	// Ignore deleted triagem deals
	$triagem_response = sql_query_triagem($problem_number, $year_select, $year_week_begin, $year_week_end, $triagem_ID);
	
	$sql = "SELECT 
				*
			FROM 
				deals_updates
			WHERE 
				YEAR(update_date) = $year_select 
				AND
				YEARWEEK(update_date) - YEARWEEK('$year_select-01-01') >= $year_week_begin - 1
				AND
				YEARWEEK(update_date) - YEARWEEK('$year_select-01-01') <= $year_week_end - 1
			ORDER BY 
				update_id ASC";
	
	// Send query to DB, receive $handle_response variable with answer
	if ($sql != ""){ include 'db_connect.php'; }
	
	
	switch ($problem_number){
		case(1): // Problem 1:
				
			// Text Answer
			$output_text = "Total values from week $year_week_begin to $year_week_end of $year_select:";
			
			break;
			
		case(2): // Problem 2:
			
			// Getting Stage names in a single array 
			// (the order it was created matters, Triagem position 0 and Decisao de Compra position 4)
			$stage_names = array_keys($output_table);
			
			$loop_size = sizeof($handle_response);
			for ($i = 0; $i < $loop_size; $i++){
				
				// Find the index of the current stage name
				$index_current = array_search($handle_response[$i][stage_to], $stage_names);
				
				if ($index_current != ""){
					
					// Sweep all next deal updates
					for ($k = $i+1; $k < $loop_size; $k++){
						
						// Compare deal_id to remove duplicate deal_updates for the same deal
						if ($handle_response[$i][deal_id] == $handle_response[$k][deal_id]){
							
							// Find the stage index of the deal being swept
							$index_following = array_search($handle_response[$k][stage_to], $stage_names);
							
							if ($index_current > $index_following){
								// if the following deal index is lower, remove the following deal
								unset($handle_response[$k]);
							}
							else{
								// otherwise remove the current deal, and break the loop
								unset($handle_response[$i]);
								break;
							}
						}
					}
				}
			}
			break;			
	}
	
	// Send the database query results to increment the output table
	$output_table = count_deals($output_table, $triagem_response, $handle_response);
	
	$output_by_week_chart = separate_week($triagem_response, $handle_response, $output_table);
	
	// Remove the special characters, to allow a json encode, so that ajax receives the given response
	foreach ($output_table as $key => $value) 
    {
		$output_table_enc[utf8_encode($key)] = $output_table[$key];
	}
	$output_table = $output_table_enc;
	
	// Echo variables back
	echo json_encode([$output_text, $output_table, $problem_number, $output_by_week_chart]);
	
	
	
?>