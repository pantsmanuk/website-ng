<?php
$this->load->view('global/profile/profile_topbar');
$limit = 5;

/*
foreach($timetable_flights as $key => $value){

echo $key.' : ';

foreach($value as $row){
	echo $row.' ';
}

echo'<hr />';

}
*/
?>

<table width="100%" border="0">
<tr>
<td>
<b><font size="+1">Favourite Aircraft (Flights)</font></b><br>
<table class="statbox" width="300">
  
      <tr>
          <th width="65%"><b>Aircraft</b></th>
          <th width="15%"><b>Flights</b></th>
          <th width="20%"><b>Time</b></th>    
      </tr>
      
		<?php
		$i = 0;
		foreach($fav_aircraft as $aircraft_id => $num_aircraft){
			echo '<tr align="center" valign="middle">';
			if(array_key_exists($aircraft_id, $aircraft)){
				echo '<td>'.$aircraft[$aircraft_id].'</td>';
			}
			else{
				echo '<td>'.$aircraft_id.'</td>';
			}			
			echo '<td>'.$num_aircraft.'</td>';
			echo '<td>'.$this->format_fns->format_seconds_hhmm($fav_aircraft_time[$aircraft_id]).'</td>';
			echo '</tr>';
		$i++;
			if($i >= $limit){
			break;
			}
		}
		?>
 
</table>       
</td>

<td>
<b><font size="+1">Favourite Aircraft (Time)</font></b><br>
<table class="statbox" width="300">
  
      <tr>
          <th width="65%"><b>Aircraft</b></th>
          <th width="15%"><b>Flights</b></th>
          <th width="20%"><b>Time</b></th>    
      </tr>
      
		<?php
		$i = 0;
		foreach($fav_aircraft_torder as $aircraft_id => $aircraft_time){
			echo '<tr align="center" valign="middle">';
			echo '<td>'.$aircraft[$aircraft_id].'</td>';
			echo '<td>'.$fav_aircraft_flights[$aircraft_id].'</td>';
			echo '<td>'.$this->format_fns->format_seconds_hhmm($aircraft_time).'</td>';
			echo '</tr>';
		$i++;
			if($i >= $limit){
			break;
			}
		}
		?>
 
</table>       
</td>

</tr>
<tr>
<td>
<b><font size="+1">Favourite Aircraft (Pax)</font></b><br>
<table class="statbox" width="300">
  
      <tr>
          <th width="65%"><b>Aircraft</b></th>
          <th width="15%"><b>Pax</b></th>
          <th width="20%"><b>Cargo</b></th>    
      </tr>
      
		<?php
		$i = 0;
		foreach($fav_aircraft_porder as $aircraft_id => $total_pax){
			echo '<tr align="center" valign="middle">';
			echo '<td>'.$aircraft[$aircraft_id].'</td>';
			echo '<td>'.$total_pax.'</td>';
			echo '<td>'.number_format($fav_aircraft_cargo[$aircraft_id]).'t</td>';
			echo '</tr>';
		$i++;
			if($i >= $limit){
			break;
			}
		}
		?>
 
</table>       
</td>

<td>
<b><font size="+1">Favourite Aircraft (Cargo)</font></b><br>
<table class="statbox" width="300">
  
      <tr>
          <th width="65%"><b>Aircraft</b></th>
          <th width="15%"><b>Pax</b></th>
          <th width="20%"><b>Cargo</b></th>    
      </tr>
      
		<?php
		$i = 0;
		foreach($fav_aircraft_cargo as $aircraft_id => $total_cargo){
			echo '<tr align="center" valign="middle">';
			echo '<td>'.$aircraft[$aircraft_id].'</td>';
			echo '<td>'.$fav_aircraft_porder[$aircraft_id].'</td>';
			echo '<td>'.number_format($total_cargo).'t</td>';
			echo '</tr>';
		$i++;
			if($i >= $limit){
			break;
			}
		}
		?>
 
</table>       
</td>

</tr>
<tr>

<td>
<b><font size="+1">Favourite Countries</font></b><br>
<table class="statbox" width="300">
  
      <tr>
          <th width="85%"><b>Country</b></th>
          <th width="15%"><b>Flights</b></th>   
      </tr>
		<?php
		$i = 0;
		foreach($fav_countries as $country => $num_flown){
			echo '<tr align="center" valign="middle">';
			if(array_key_exists($country, $countries)){
				echo '<td>'.$countries[$country].'</td>';
			}
			else{
				echo '<td>-Unknown'.$country.'-</td>';
			}
			echo '<td>'.$num_flown.'</td>';
			echo '</tr>';
		$i++;
			if($i >= $limit){
			break;
			}
		}
		?>
 
</table>       
</td>

<td>
</td>

</tr>
</table>