
<?php $pageTitle = 'Search'; include '../header.html'; ?>



<!--  
	Input Form Section
	Contains Year and week range selection
 -->
<div>
<p class="green"> Filter deals by: </p>
<label for="uname">Year</label>
<input type="number" id="year_select" value="2017">
<label for="uname"> sarting at week </label>
<input type="number" id="starting_week" value="1">
<label for="uname"> Ending at week </label>
<input type="number" id="ending_week" value="1"> 
<a id="search_button" class="button" onclick="update_results();">Search</a>
</div>

<div class="clear"></div>

<p id="print_me"></p>
<p id="main_text"></p>




<!-- Javascript used to access Data -->
<script type="text/javascript">

// Function used to print result to DIV box, the result is the generated table from the database response
function print_results(output_table, problem_number){
	
	if (problem_number == 1){
		// Echo Output Main Text
		document.getElementById("main_text").innerHTML = output_text;
	}
	
	// Generating HTML Table According to results
	var data_print = "<br></br><table>";
	
	if (problem_number == 1){
		data_print = data_print + "<tr><th width='70%' > Deal Update </th><th width='30%' > Occurrences </th></tr>";
	}
	
	if (problem_number == 2){
		data_print = data_print + "<tr><th width='70%' > Current Deal Stage </th><th width='30%' > Number </th></tr>";
	}
	
	for(key in output_table){
		data_print = data_print + "<tr><td width='70%' >" + key + "</td><td width='30%' >" + output_table[key] + "</td></tr>";
	}
	// End table HTML tag
	data_print = data_print + "</table>";

	
	// Print table into HTML div
	document.getElementById("the_return" + problem_number).innerHTML = data_print;
	
	// Update class to display the div
	document.getElementById("the_return" + problem_number).className = "result_box";
	
	// Call the Generate chart function
	drawChart(problem_number, output_by_week_chart, output_table);
}

// Function used to convert ISO-8859-1 to UTF8
function decode_utf8(s) {
	return decodeURIComponent(escape(s));
}

// Function used to draw every chart after the response has returned from the DB request
function drawChart(chart_number, output_by_week_chart, output_table) {
	
	// Define stage name keys and the number of weeks
	var stage_names = Object.keys(output_table);
	var week_number = Object.keys(output_by_week_chart);
	
	var week = [];
	
	// Concatenated loop used to cycle through result "output_by_week_chart"
	// and add every deal_update to its respective stage inside the range of weeks
	stage_names.forEach(function(Stage_entry) {
		week[Stage_entry] = [];
		week_number.forEach(function(week_entry) {
			value = output_by_week_chart[week_entry][Stage_entry];
			if (value == undefined){
				// if no deals are found, remove the undefined tag, and set it to zero only
				value = 0;
			}
			week[Stage_entry].push(value);
		});
	});
	
	// Prefix displayed before every week number at the bottom of the charts
	var prefix = "Semana ";
	for(var i=0; i < week_number.length; i++){
		week_number[i]= prefix + week_number[i];
	}

	// Get canvas box to output the chart to
	var ctx = document.getElementById("myChart" + chart_number).getContext("2d");
	
	// Define data series to be plot
	var data = {
		labels: week_number,
		datasets: [
			{
				label: stage_names[0], // Triagem
				backgroundColor: "#3366cd",
				data: week[stage_names[0]]
			},
			{
				label: stage_names[1], // Qualificação
				backgroundColor: "#dc3a11",
				data: week[stage_names[1]]
			},
			{
				label: stage_names[2], // Reconhecimento
				backgroundColor: "#ff9902",
				data: week[stage_names[2]]
			},
			{
				label: stage_names[3], // Construção
				backgroundColor: "#0f9618",
				data: week[stage_names[3]]
			},
			{
				label: stage_names[4], // Decisão
				backgroundColor: "#990099",
				data: week[stage_names[4]]
			}
		]
	};
	
	// Default chart options for a bar plot
	var options = {
		type: 'bar',
		data: data,
		options: {
			responsive: false,
			barValueSpacing: 20,
			scales: {
				yAxes: [{
					ticks: {
						min: 0,
					}
				}]
			}
		}
	};
	
	
	if (chart_number == 1){
		// If variable is defined, means that a previous plot has been done
		if (typeof myBarChart1 !== 'undefined') {
			// If it happens, destroy that chart before ploting the new one
			myBarChart1.destroy();
		}
		myBarChart1 = new Chart(ctx, options);
		
	}
	else{ // do it for chart_number 2, too:
		console.log(typeof myBarChart2);
		if (typeof myBarChart2 !== 'undefined') {
			
			myBarChart2.destroy();
		}
		myBarChart2 = new Chart(ctx, options);
	}
}

// Function used to gather information input in the input boxes
// It also validates the numbers input, changing if necessary
// At last, it calls the php page used to send mysql querys to the DB
function update_results() {
	
	// Read Values Input by user
	var week_begin = document.getElementById("starting_week").value;
	var week_end = document.getElementById("ending_week").value;
	var year_select = document.getElementById("year_select").value;
	
	// Data Validation
	if (week_begin < 1) {
		week_begin = 1;
		document.getElementById("starting_week").value = week_begin;
	}
	
	// Data Validation
	if (week_end > 53) {
		week_end = 53;
		document.getElementById("ending_week").value = week_end;
	}
	
	// Alert on invalid week range
	if (week_end < week_begin){
		alert("Invalid week range, try again.");
		return
	}
	
	// Animation to inform the user about the search submit
	var text_display = document.getElementById("the_return2").innerHTML + '<br><p> Searching... </p>';
	document.getElementById("the_return2").innerHTML = text_display;
	
	// AJAX used to load a php page in order to search the database
	// Run both problems 1 and 2, separately, but both run at the same time
	for (problem_number = 1; problem_number <= 2; problem_number++){
		$.ajax({
		   type: "POST",
		   url: 'db_select.php',
		   data:{'problem_number':problem_number,
				'year_select':year_select,
				'year_week_begin':week_begin,
				'year_week_end':week_end},
		   dataType: 'json',
		   success:function(data){
			   
				output_text = data[0];
				output_table = data[1];
				problem_number = data[2];
				output_by_week_chart = data[3];
				
				print_results(output_table, problem_number, output_by_week_chart);
			}
		});
	}
} 

</script>


<!--  Divs Used to print output response from Database  -->

<div id="the_return1"></div>
<canvas id="myChart1" width = "600px" height = "250px"></canvas>

<div class="clear"></div> <br>
<div id="the_return2">
  Select week number and press Search
</div>
<canvas id="myChart2" width = "600px" height = "250px"></canvas>
<div class="clear"></div>

<br>


<?php 
	
	// Function to Print Objects in a readable way
	function print_r2($val){
		echo '<pre>';
		print_r($val);
		echo  '</pre>';
	}
	
?>

	
	


<?php include '../footer.html'; ?>