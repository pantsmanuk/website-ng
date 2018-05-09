<div class="container">


<?php

$active_array = array('0' => 'Inactive',  '1' => 'Active');

echo '<h1>'.$tour_name.' ('.$active_array[$active].')</h1>';


//downloads
if($num_rows > 0){


//output selected
echo '<h2>Legs</h2>';

?>
<center>
<table border="0" cellpadding="4" cellspacing="0" style="border-collapse: collapse;" bordercolor="#111111" width="100%">
  <tbody>
  <tr>
  	<th>Leg</th>
    <th>Origin</th>
    <th>Destination</th>
    <th>Radial</th>
    <th>Distance</th>
    <th>Opens</th>
    <th>Closes</th>
    <th>Award</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
  </tr>

<?php

	$i = 1;
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


			echo '<tr '.$bgstyle.'>';

			echo '<td>'.$colour1.$row['sequence'].$colour2.'</td>';
			echo '<td>'.$colour1.$row['start_icao'].' '.$row['start_name'].$colour2.'</td>';
			echo '<td>'.$colour1.$row['end_icao'].' '.$row['end_name'].$colour2.'</td>';
			echo '<td align="center">'.$colour1.$row['gc_bearing'].' deg'.$colour2.'</td>';
			echo '<td align="center">'.$colour1.number_format($row['gcd_nm'],0).' nm'.$colour2.'</td>';
			echo '<td align="center">'.$colour1.gmdate('d/m/Y', strtotime($row['start_date'])).$colour2.'</td>';
			echo '<td align="center">'.$colour1.gmdate('d/m/Y', strtotime($row['end_date'])).$colour2.'</td>';
			if($row['award_id'] > 0){
				echo'<td align="center" width="15"><img src="'.$image_url.'icons/application/asterisk_yellow.png" alt="'.$row['award_name'].'" /></td>';
			}
			else{
				echo'<td align="center" width="15">-</td>';
			}



			echo'<td align="center" width="15"><a href="'.$base_url.'acp_propilot/event_legs_edit/'.$event_id.'/'.$row['leg_id'].'/'.$i.'/'.'">
				<img src="'.$image_url.'icons/application/database_edit.png" alt="Edit" /></a></td>';


			echo'<td align="center" width="15"><a href="'.$base_url.'acp_propilot/event_legs_edit/'.$event_id.'/'.$row['leg_id'].'/'.$i.'/">
			<img src="'.$image_url.'icons/application/database_link.png" alt="Alternate Sim" /></a></td>';

			echo '</tr>';

		$i++;
		}

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
<td width="25" align="center"><a href="<?php echo $base_url.'acp_propilot/event_legs_edit/'.$event_id.'/0/'.$i.'/'; ?>"><img src="<?php echo $image_url; ?>icons/application/database_add.png" alt="Add" /></a></td>
<td align="left"><a href="<?php echo $base_url.'acp_propilot/event_legs_edit/'.$event_id.'/0/'.$i.'/'; ?>">Add new</a></td>

<td width="25"></td>

<td width="25" align="center"><a href="<?php echo $base_url.'acp_propilot/event_awards_remark/'.$event_id.'/'; ?>"><img src="<?php echo $image_url; ?>icons/application/award_star_gold_1.png" alt="Add" /></a></td>
<td align="left"><a href="<?php echo $base_url.'acp_propilot/event_awards_remark/'.$event_id.'/'; ?>">Remark Awards</a></td>
</tr>
</table>

<?php
}
else{
	echo 'Legs can only be added to the generic tour.';
}
?>

</center>

Add all the legs and when done, remember to edit the event to make it active.
<br /><br />
Ensure that there is an award set for the last leg. When the flight with the award is complete, it will fire the award script. You can add award triggers on any leg (example: If you wish to have multiple awards for stage completion).