<h1>Admin Statistics</h1>
<center>

<?php
$output = '<table class="statbox" width="100%">';
//create an array for the months to display names
$month_array = array(
'0' => '-',
'1' => 'Jan',
'2' => 'Feb',
'3' => 'Mar',
'4' => 'Apr',
'5' => 'May',
'6' => 'Jun',
'7' => 'Jul',
'8' => 'Aug',
'9' => 'Sep',
'10' => 'Oct',
'11' => 'Nov',
'12' => 'Dec',
);

$span_val = 12-$current_month;

$output .= '<tr>';
$output .= '<th>&nbsp;</th>';
if($span_val > 0){
	$output .= '<th colspan="'.$span_val.'">'.($current_year-1).'</th>';
	$output .= '<th colspan="'.(12-$span_val).'">'.$current_year.'</th>';
	$legend1 = ($current_year-1).'-'.$current_year;
	$legend2 = ($current_year-2).'-'.($current_year-1);
	$legend3 = ($current_year-3).'-'.($current_year-2);
}
else{
	$output .= '<th colspan="'.(12-$span_val).'">'.$current_year.'</th>';
	$legend1 = $current_year;
	$legend2 = ($current_year-1);
	$legend3 = ($current_year-2);
}
$output .= '</tr>';


$output .= '<tr>';
$output .= '<th>Month</th>';


$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}

$year_float = $current_year-1;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}
	
	
	$output .= '<td width="7%" '.$back.'>'.$month_array[$month_float].'</td>';
	
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
$i++;
}

$output .= '</tr>';


//output the active pilots per month
$output .= '<tr>';
$output .= '<th>Active Pilots</th>';
$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}
$year_float = $current_year-1;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}

	if($activity_stats[$year_float][$month_float]['pilots'] > 0){
		$output .= '<td width="7%" '.$back.'>'.number_format($activity_stats[$year_float][$month_float]['pilots'],0).'</td>';
		if($month_float != $current_month){
		$y2data1[($i-1)] = $activity_stats[$year_float][$month_float]['pilots'];
		}
	}
	else{
		$output .= '<td width="7%" '.$back.'>-</td>';
		if($month_float != $current_month){
		$y2data1[($i-1)] = 0;
		}
	}
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}	
	
	
$i++;
}


$output .= '</tr>';



//output the new pilots per month
$output .= '<tr>';
$output .= '<th>New Pilots</th>';
$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}
$year_float = $current_year-1;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}

	if($activity_stats[$year_float][$month_float]['signups'] > 0){
		$output .= '<td width="7%" '.$back.'>'.number_format($activity_stats[$year_float][$month_float]['signups'],0).'</td>';
	}
	else{
		$output .= '<td width="7%" '.$back.'>-</td>';
	}
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
	
	
$i++;
}


$output .= '</tr>';


$ydata = array();
// output the number of flights per month
$output .= '<tr>';
$output .= '<th>Total Flights</th>';
$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}
$year_float = $current_year-1;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}

	if($activity_stats[$year_float][$month_float]['pireps'] > 0){
		//if($i <= 12){
		$output .= '<td width="7%" '.$back.'>'.number_format($activity_stats[$year_float][$month_float]['pireps'],0).'</td>';
		//}
		
		
		$xdata[($i-1)] = $month_array[$month_float];
		if($month_float != $current_month){
		$ydata1[($i-1)] = $activity_stats[$year_float][$month_float]['pireps'];
		}
		
	}
	else{
		//if($i <= 12){
		$output .= '<td width="7%" '.$back.'>-</td>';
		//}
		$xdata[($i-1)] = $month_array[$month_float];
		if($month_float != $current_month){
		$ydata1[($i-1)] = 0;
		}
	}
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
	
$i++;
}


// SECOND TABLE COMPARATIVE YEAR ***************************************************************************************

$span_val = 12-$current_month;

$output .= '<tr>';
$output .= '<th>&nbsp;</th>';
$output .= '<th colspan="'.$span_val.'">'.($current_year-2).'</th>';
$output .= '<th colspan="'.(12-$span_val).'">'.($current_year-1).'</th>';
$output .= '</tr>';


$output .= '<tr>';
$output .= '<th>Month</th>';


$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}

$year_float = $current_year-2;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}
	
	
	$output .= '<td width="7%" '.$back.'>'.$month_array[$month_float].'</td>';
	
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
$i++;
}

$output .= '</tr>';


//output the active pilots per month
$output .= '<tr>';
$output .= '<th>Active Pilots</th>';
$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}
$year_float = $current_year-2;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}

	if($activity_stats[$year_float][$month_float]['pilots'] > 0){
		$output .= '<td width="7%" '.$back.'>'.number_format($activity_stats[$year_float][$month_float]['pilots'],0).'</td>';
		
		$y2data2[($i-1)] = $activity_stats[$year_float][$month_float]['pilots'];
	}
	else{
		$output .= '<td width="7%" '.$back.'>-</td>';
		$y2data2[($i-1)] = 0;
	}
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
	
	
$i++;
}


$output .= '</tr>';



//output the new pilots per month
$output .= '<tr>';
$output .= '<th>New Pilots</th>';
$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}
$year_float = $current_year-2;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}

	if($activity_stats[$year_float][$month_float]['signups'] > 0){
		$output .= '<td width="7%" '.$back.'>'.number_format($activity_stats[$year_float][$month_float]['signups'],0).'</td>';
	}
	else{
		$output .= '<td width="7%" '.$back.'>-</td>';
	}
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
	
	
$i++;
}


$output .= '</tr>';



// output the number of flights per month
$output .= '<tr>';
$output .= '<th>Total Flights</th>';
$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}
$year_float = $current_year-2;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}

	if($activity_stats[$year_float][$month_float]['pireps'] > 0){
		$output .= '<td width="7%" '.$back.'>'.number_format($activity_stats[$year_float][$month_float]['pireps'],0).'</td>';
		$ydata2[($i-1)] = $activity_stats[$year_float][$month_float]['pireps'];
	}
	else{
		$output .= '<td width="7%" '.$back.'>-</td>';
		$ydata2[($i-1)] = 0;
	}
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
	
$i++;
}

$output .= '</tr>';






// THIRD TABLE COMPARATIVE YEAR ***************************************************************************************

$span_val = 12-$current_month;

$output .= '<tr>';
$output .= '<th>&nbsp;</th>';
$output .= '<th colspan="'.$span_val.'">'.($current_year-3).'</th>';
$output .= '<th colspan="'.(12-$span_val).'">'.($current_year-2).'</th>';
$output .= '</tr>';


$output .= '<tr>';
$output .= '<th>Month</th>';


$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}

$year_float = $current_year-3;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}
	
	
	$output .= '<td width="7%" '.$back.'>'.$month_array[$month_float].'</td>';
	
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
$i++;
}

$output .= '</tr>';


//output the active pilots per month
$output .= '<tr>';
$output .= '<th>Active Pilots</th>';
$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}
$year_float = $current_year-3;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}

	if($activity_stats[$year_float][$month_float]['pilots'] > 0){
		$output .= '<td width="7%" '.$back.'>'.number_format($activity_stats[$year_float][$month_float]['pilots'],0).'</td>';
		$y2data3[($i-1)] = $activity_stats[$year_float][$month_float]['pilots'];
	}
	else{
		$output .= '<td width="7%" '.$back.'>-</td>';
		$y2data3[($i-1)] = 0;
	}
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
	
	
$i++;
}


$output .= '</tr>';



//output the new pilots per month
$output .= '<tr>';
$output .= '<th>New Pilots</th>';
$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}
$year_float = $current_year-3;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}

	if($activity_stats[$year_float][$month_float]['signups'] > 0){
		$output .= '<td width="7%" '.$back.'>'.number_format($activity_stats[$year_float][$month_float]['signups'],0).'</td>';
	}
	else{
		$output .= '<td width="7%" '.$back.'>-</td>';
	}
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
	
	
$i++;
}


$output .= '</tr>';



// output the number of flights per month
$output .= '<tr>';
$output .= '<th>Total Flights</th>';
$month_float = $current_month+1;

if($month_float > 12){
$month_float = 1;	
}
$year_float = $current_year-3;
$i = 1;
while($i <= 12){

	$back = '';
	if($month_float == $current_month && $year_float == $current_year){
		$back = 'bgcolor="#d1d9e5"';
	}

	if($activity_stats[$year_float][$month_float]['pireps'] > 0){
		$output .= '<td width="7%" '.$back.'>'.number_format($activity_stats[$year_float][$month_float]['pireps'],0).'</td>';
		$ydata3[($i-1)] = $activity_stats[$year_float][$month_float]['pireps'];
	}
	else{
		$output .= '<td width="7%" '.$back.'>-</td>';
		$ydata3[($i-1)] = 0;
	}
	
	if($month_float == 12){
		$month_float = 1;
		$year_float++;
	}						
	else{
		$month_float++;
	}
	
	
$i++;
}

$output .= '</tr>';



$output .= '</tr>';


$output .= '</table>';


//jpgrah of latest year data
// Setup Chart
//$ydata = array(11,3,8,12,5,1,9,13,5,7); // this should come from the model        
$graph = acpstatschart(	$xdata, 'Three year comparative of total flights', 320, 250, 
					$legend1, $ydata1, 
					$legend2, $ydata2, 
					$legend3, $ydata3 );
					

// File locations
// Could possibly add to config file if necessary
$graph_temp_directory = $base_path.'assets/uploads/tmp';
$graph_file_name = 'acp_flights.png';  
$graph_file_path = $graph_temp_directory.'/'.$graph_file_name;
$graph_file_location = $assets_url.'uploads/tmp/'.$graph_file_name;
$graph->Stroke($graph_file_path);  // create the graph and write to file


$graph2 = acpstatschart(	$xdata, 'Three year comparative of active pilots', 320, 250, 
					$legend1, $y2data1, 
					$legend2, $y2data2, 
					$legend3, $y2data3 );

// File locations
// Could possibly add to config file if necessary
$graph_temp_directory2 = $base_path.'assets/uploads/tmp';
$graph_file_name2 = 'acp_actives.png';  
$graph_file_path2 = $graph_temp_directory2.'/'.$graph_file_name2;
$graph_file_location2 = $assets_url.'uploads/tmp/'.$graph_file_name2;
$graph2->Stroke($graph_file_path2);  // create the graph and write to file

//echo '<tr>';
//echo '<td colspan="13" align="center">';
//echo $graph_file_path.'<br />';
//echo $graph_file_location.'<br />';
echo '<table border="0" cellpadding="0" cellspacing="0"><tr><td>';
echo '<img src="'.$graph_file_location2.'" />';
echo '</td><td>';
echo '<img src="'.$graph_file_location.'" />';
echo '</td>';
echo '</tr>';
echo '</table>';

echo $output;

?>

</center>
<br /><br />
Active Pilots are the number of unique pilots who flew during that month. Total flights are the total number of flights made by pilots during the month.
<br /><br />
The current month is highlighted and shows the stats for the month so far.
