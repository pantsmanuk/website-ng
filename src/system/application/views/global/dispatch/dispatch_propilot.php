<div class="menu_sub">
<?php
//echo '<a href="'.$base_url.'dispatch/timetable_map/1/">Lock a flight</a> | ';
//echo '<a href="'.$base_url.'dispatch/propilot_lock">Lock a flight</a> | ';
echo '<a href="'.$base_url.'dispatch/deadhead">Set Deadhead</a> | ';
//echo '<a href="'.$base_url.'dispatch/propilot_aircraft/ALL/ALL/ALL/1">Locate Aircraft</a> | ';
echo '<a href="'.$base_url.'dispatch/propilot_aircraft/">Locate Aircraft</a> | ';
?>
</div>
<br />

<h1>Propilot scores for the last 90 days</h1>

<table width="100%" cellpadding="2">

<tr valign="top">

<td width="33.3333333333%">
<?php
if($average_count > 0){

echo '<table width="100%" class="boxed">';
echo '<h3>Top 10 Propilot Averages</h3>';
	echo '<tr>';
	echo '<th></th>';
	echo '<th>Pilot</th>';
	echo '<th align="center">Score</th>';
	echo '<th align="center">Time</th>';
	echo '</tr>';


$i = 0;
foreach($average_result as $row){

	if($i%2 == 0){
		$bgcol = 'bgcolor="#f2f2f2"';
	}
	else{
		$bgcol = '';
	}

	echo '<tr '.$bgcol.'>';
	echo '<td align="center" width="20">'.($i+1).'</td>';
	echo '<td>EHM-'.$row->username.' '.$row->fname.' '.$row->sname.'</td>';
	echo '<td align="center">'.number_format($row->pp_average).'</td>';
	echo '<td align="center">'.$this->format_fns->format_seconds_hhmm($row->pp_average_blocktime_mins, 1).'</td>';
	echo '</tr>';
$i++;
}
echo '</table>';

}


?>
</td>


<?php
if($count_count > 0){
echo '<td width="33.3333333333%">';
echo '<table width="100%" class="boxed">';
echo '<h3>Top 10 Propilot Frequency</h3>';
	echo '<tr>';
	echo '<th></th>';
	echo '<th>Pilot</th>';
	echo '<th align="center">Freq</th>';
	echo '</tr>';
	
$j = 0;
foreach($count_result as $row){

	if($j%2 == 0){
		$bgcol = 'bgcolor="#f2f2f2"';
	}
	else{
		$bgcol = '';
	}

	echo '<tr '.$bgcol.'>';
	echo '<td align="center" width="20">'.($j+1).'</td>';
	echo '<td>EHM-'.$row->username.' '.$row->fname.' '.$row->sname.'</td>';
	echo '<td align="center">'.number_format($row->pp_count).'</td>';
	echo '</tr>';
$j++;
}
echo '</table>';

echo '</td>';
}


?>



<td width="33.3333333333%">
<?php
if($sum_count > 0){

echo '<table width="100%" class="boxed">';
echo '<h3>Top 10 Propilot Totals</h3>';
	echo '<tr>';
	echo '<th></th>';
	echo '<th>Pilot</th>';
	echo '<th align="center">Score</th>';	
	echo '<th align="center">Time</th>';
	echo '</tr>';


$i = 0;
foreach($sum_result as $row){

	if($i%2 == 0){
		$bgcol = 'bgcolor="#f2f2f2"';
	}
	else{
		$bgcol = '';
	}

	echo '<tr '.$bgcol.'>';
	echo '<td align="center" width="20">'.($i+1).'</td>';
	echo '<td>EHM-'.$row->username.' '.$row->fname.' '.$row->sname.'</td>';
	echo '<td align="center">'.number_format($row->pp_sum).'</td>';
	echo '<td align="center">'.$this->format_fns->format_seconds_hhmm($row->pp_sum_blocktime_mins, 1).'</td>';
	echo '</tr>';
$i++;
}
echo '</table>';

}


?>
</td>

</tr>


</table>


<?php

//output any deadheaders

if($deadhead_num > 0){

	echo '<h3>Pilots requesting deadhead</h3>';

	//table
	echo '<table width="100%" class="boxed" cellpadding="2">';
	echo '<tr>';
	
		echo '<th width="22%" align="left">Pilot</th>';
		echo '<th width="5%" align="center">Loc</th>';
		echo '<th width="5%" align="center">Dest</th>';
		echo '<th width="5%" align="center">Direct</th>';
		echo '<th width="13%" align="center">Since</th>';
		
		echo '<th width="10" align="center">&nbsp;</th>';
		
		echo '<th width="22%" align="left">Pilot</th>';
		echo '<th width="5%" align="center">Loc</th>';
		echo '<th width="5%" align="center">Dest</th>';
		echo '<th width="5%" align="center">Direct</th>';
		echo '<th width="13%" align="center">Since</th>';
		
		
		
	echo '</tr>';

	$i = 0;
	foreach($deadhead_result as $row){
	
	if($i%4 == 0){
		$bgcol = 'bgcolor="#f2f2f2"';
	}
	else{
		$bgcol = '';
	}

	
		if($i%2 == 0){	
			echo '<tr '.$bgcol.'>'; 
		}
		else{ 
			echo '<td>&nbsp;</td>'; 
		}
		
		echo '<td align="left">EHM-'.$row['username'].' '.$row['fname'].' '.$row['sname'].'</td>';
		echo '<td align="center">'.$row['pp_location'].'</td>';
		if($row['deadhead_dest'] != ''){
			echo '<td align="center">'.$row['deadhead_dest'].'</td>';
		}
		else{
			echo '<td align="center">-</td>';
		}
		
		
		if($row['deadhead_direct'] == '1'){
			echo '<td align="center">Y</td>';
		}
		else{
			echo '<td align="center">-</td>';
		}
		
		if($row['deadhead_set'] == '' || $row['deadhead_set'] == '0000-00-00 00:00:00'){
			echo '<td align="center">-</td>';
		}
		else{
			echo '<td align="center">'.gmdate('d/m/Y H:i', strtotime($row['deadhead_set'])).'</td>';
		}
		
		//deadhead_direct
		
		if($i%2 != 0){	echo '</tr>'; }
	$i++;
	}
	
	if($deadhead_num%2 != 0){
		
		//close row
		echo '<td></td><td></td><td></td><td></td><td></td><td></td></tr>';
		
	}
	
	echo '</table>';

}
else{
	echo '<center><h3>No pilots currently require deadheading</h3></center>';
}
?>



