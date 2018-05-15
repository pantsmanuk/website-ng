<?php

echo '<h1>'.$tour_name.' ('.$tour_enabled.')</h1>';

echo '<h3>Allowed Aircraft</h3>';

?>

<center>
<table border="0">
<tr>
<td width="25" align="center"><img src="<?php echo $image_url; ?>icons/application/database_delete.png" alt="Delete" /></td>
<td align="left">Delete</td>
</tr>
</table>

<?php

$aircraft_output = '';

$i = 0;
foreach($aircraft as $row){
	if($row['tour_aircraft_id'] != ''){
	
		//determine row colour
		
		$aircraft_output .= '<tr>';
		
		$aircraft_output .= '<td align="center">'.$row['aircraft_name'].'</td>';
		$aircraft_output .= '<td align="center">'.$row['aircraft_clss'].'</td>';
		$aircraft_output .= '<td align="center">'.$row['aircraft_division'].'</td>';
		if($row['in_fleet'] == '1'){
			$aircraft_output .= '<td align="center">Current</td>';
		}
		else{
			$aircraft_output .= '<td align="center">Historical</td>';
		}
		
		if($row['aircraft_enabled'] == '1'){
			$aircraft_output .= '<td align="center">Enabled</td>';
		}
		else{
			$aircraft_output .= '<td align="center">Disabled</td>';
		}
		$aircraft_output .= '<td align="center" width="20"><a href="'.$base_url.'acp_tours/aircraft/'.$row['id'].'/1/'.$row['tour_aircraft_id'].'">
			<img src="'.$image_url.'icons/application/database_delete.png" alt="Delete" /></a></td>';
		
		$aircraft_output .= '</tr>';
	
		$i++;
	}
}

echo '<br />';

if($i < 1){
	echo 'No aircraft currently allowed';
}
else{

	echo '<table width="100%">';
	echo '<tr>';
	echo '<th>Aircraft</th>';
	echo '<th>Class</th>';
	echo '<th>Division</th>';
	echo '<th>Current</th>';
	echo '<th>Enabled</th>';
	echo '<th></th>';
	echo '</tr>';
	echo $aircraft_output;
	echo '</table>';
	
}

//output the form to add a new aircraft

echo '<br /><br />';



$hidden = array('valid' => 'true');
echo form_open('acp_tours/aircraft/'.$tour_id,'',$hidden);

echo '<fieldset style="border: 1px dotted rgb(40, 45, 78); padding: 0.6em; margin-top: 0.4em; margin-bottom: 0.4em;">';
echo '<legend>Allow aircraft</legend>'; 
//output dropdown
echo '<label for="enabled">Aircraft</label>'.form_dropdown('aircraft_id', $aircraft_array, $aircraft_id).'<br />';
echo '</fieldset>';
echo '<br />';
echo '<center>'.form_submit('submit', 'Allow aircraft').'</center>';
echo form_close();

?>