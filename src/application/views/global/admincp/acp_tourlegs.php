<div class="container">


<?php

echo '<h1>'.$tour_name.'</h1>';


//downloads
if($num_rows > 0){



//build and output menu
echo '<div class="menu_sub">';
$j = 0;
foreach($versions as $key => $row){
	
	
	if($j > 0){
		echo ' | ';
	}
	
echo '<a href="'.$base_url.'acp_tours/legs/'.$tour_id.'/'.$key.'">'.$row.'</a>';
$j++;
}
echo '</div>';

//output selected 
if(array_key_exists($selected_version, $versions)){
	echo '<h2>Legs: '.$versions[$selected_version].'</h2>';
}
else{
	echo '<h2>Legs: '.$versions[''].'</h2>';
}



?>
<center>
<table border="0" cellpadding="4" cellspacing="0" style="border-collapse: collapse;" bordercolor="#111111" width="100%">
  <tbody>
  <tr>
  	<th width="20">Leg</th>
    <th>Origin</th>
    <th>Destination</th>
    <th width="50">Radial</th>
    <th width="50">Distance</th>
    <th width="50">Altitude</th>
    <th>Award</th>
    <th>&nbsp;</th>
    <?php
	if($selected_version == ''){
		echo '<th>&nbsp;</th>';
	}
	?>
  </tr>
    
<?php

	$i = 0;
	foreach($flight_array as $row){
		
		if($i%2 == 0){
				
		
			//grey the background
			$bgstyle = 'style="background: #f2f2f2;"';
			
		}
		else{
			$bgstyle = '';
		}
		
			if(array_key_exists('sequence', $row) && $row['sequence'] != ''){
			
			$colour1 = '';
			$colour2 = '';
			if($row['alt'] == 1 && $row['selected_version'] != ''){
				$colour1 = ' <font color="red" >';
				$colour2 = ' </font>';
			}
			
			if($row['altitude'] != ''){
				$altitude = number_format($row['altitude'],0).' ft';
			}
			else{
				$altitude = '-';
			}
			
			if($row['gc_bearing'] == '-'){
				$bgstyle = 'style="background: #990000; color: #ffffff;"';
			}
		
			echo '<tr '.$bgstyle.'>';	
			
			echo '<td>'.$colour1.$row['sequence'].$colour2.'</td>';
			echo '<td>'.$colour1.$row['start_icao'].' '.$row['start_name'].$colour2.'</td>';
			echo '<td>'.$colour1.$row['end_icao'].' '.$row['end_name'].$colour2.'</td>';
			echo '<td align="center">'.$colour1.$row['gc_bearing'].' deg'.$colour2.'</td>';
			if(is_numeric($row['gcd_nm'])){
				$output_gcd_nm = number_format($row['gcd_nm'],0).' nm';
			}
			else{
				$output_gcd_nm = '-';
			}
			echo '<td align="center">'.$colour1.$output_gcd_nm.$colour2.'</td>';
			echo '<td align="center">'.$colour1.$altitude.$colour2.'</td>';
			
			if($row['award_id'] > 0){
				echo'<td align="center" width="15"><img src="'.$image_url.'icons/application/asterisk_yellow.png" alt="'.$row['award_name'].'" /></td>';
			}
			else{
				echo'<td align="center" width="15">-</td>';
			}
			
			
			if($selected_version == $row['selected_version']){
				echo'<td align="center" width="15"><a href="'.$base_url.'acp_tours/legs_edit/'.$tour_id.'/'.$row['leg_id'].'/'.$i.'/'.$selected_version.'">
				<img src="'.$image_url.'icons/application/database_edit.png" alt="Edit" /></a></td>';
			}
			else{
				echo'<td align="center" width="15"><a href="'.$base_url.'acp_tours/legs_edit/'.$tour_id.'/0/'.$i.'/'.$selected_version.'">
				<img src="'.$image_url.'icons/application/database_edit.png" alt="Edit" /></a></td>';
			}
			
			if($selected_version == ''){
			echo'<td align="center" width="15"><a href="'.$base_url.'acp_tours/legs_edit/'.$tour_id.'/'.$row['leg_id'].'/'.$i.'/'.'A">
			<img src="'.$image_url.'icons/application/database_link.png" alt="Alternate Sim" /></a></td>';
			}
			echo '</tr>';
			}
		
	$i++;
	}

}
?>

  </tbody>
</table>

</div>

<center>
<br />
<?php
if($selected_version == ''){
?>

<table border="0">
<tr>  
<td width="25" align="center"><a href="<?php echo $base_url.'acp_tours/legs_edit/'.$tour_id.'/0/'.$i.'/'.$selected_version; ?>"><img src="<?php echo $image_url; ?>icons/application/database_add.png" alt="Add" /></a></td>
<td align="left"><a href="<?php echo $base_url.'acp_tours/legs_edit/'.$tour_id.'/0/'.$i.'/'.$selected_version; ?>">Add new</a></td>
</tr>
</table>  

<?php
}
else{
	echo 'Legs can only be added to the generic tour.';
}
?>

</center>

Add all the legs for one simulator (Generic). If there are sim specific changes, select the first leg of change and hit the link icon. Insert this change. Select the new sim group and use the edit icon to change subsequent route legs. These will be highlighted in red where they differ from the generic. You must check that the legs are continuous (arrival of last = start of next) as no verification check is made.
<br /><br />
Ensure that there is an award set for the last leg. If this is customised, the award will need to be added to each customised leg. (Generic + reds). These are triggers, when the flight is complete, it will fire the award script. You can add award triggers on any leg (example: World tour has different awards at different stages of the tour completion).