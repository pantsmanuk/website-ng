<div class="error" style="float:right;"><?php echo $error; ?></div>

<?php echo '<b>Results: '.$num_flights.'</b><br />'; ?>
<br />

<?php
echo form_open('dispatch/timetable/'.$division.'/'.$hub_icao.'/'.$class.'/'.$origin.'/'.$destination);
echo form_dropdown('hub_icao', $hub_array, $hub_icao);
echo form_dropdown('division', $divisions_array, $division);
echo form_dropdown('class', $class_array, $class);


echo 'Origin: ';
echo form_input($origin_input);
echo 'Dest: ';
echo form_input($destination_input);
echo form_submit('Submit', 'Select');
echo form_close();

$hidden['valid'] = 'true';
echo form_open('dispatch/timetable/'.$division.'/'.$hub_icao.'/'.$class.'/'.$origin.'/'.$destination.'/'.$offset, '', $hidden);
?>


<table class="boxed" width="100%">
<tr>
<td colspan="10"><span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span></td>

</tr>
<tr>
<th align="center">Division</th>
<th align="center">Class</th>
<th align="center">Flight #</th>
<th align="center">Departure</th>
<th align="center">Take-Off </th>
<th align="center">Arrival</th>
<th align="center">Landing</th>
<th align="center">Days</th>
<th align="center">Distance</th>
<th align="center"></th>
<th align="center"></th>
 	   	  	  	  	 
<?php
$i = 0;
$j = 0;
$flights = '';
//echo 'offset: '.$offset;
foreach($timetable_flights as $row){

if(is_numeric($offset) 
	&& $i >= $offset 
	&& $i < ($offset+$limit)
	){
	
	
	if($j%2 != 0){
		$bgcol = 'bgcolor="#f2f2f2"';
	}
	else{
		$bgcol = '';
	}
	$j++;
	
	$link_open = '';
	$link_close = '';
	//only allow selection if the flight matches the current division and class
	if($row->clss != $class || $row->division_id != $division){
		$link_open = '<a href="'.$base_url.'dispatch/timetable/'.$row->division_id.'/'.$hub_icao.'/'.$row->clss.'/'.$row->dep_airport.'/'.$row->arr_airport.'/X">';
		$link_close = '</a>';
	}
	
	
	//caluclate distabnce between airports
	$gcd_km = $this->geocalc_fns->GCDistance($row->dep_lat, $row->dep_long, $row->arr_lat, $row->arr_long);
	$gcd_nm = $this->geocalc_fns->ConvKilometersToMiles($gcd_km);

	$flights .= '<tr '.$bgcol.'>';
	$flights .= '<td align="center">'.$link_open.$row->division.$link_close.'</td>';
	$flights .= '<td align="center">'.$link_open.$row->clss.$link_close.'</td>';
	$flights .= '<td align="center">'.$link_open.'EH'.$row->prefix.'-'.$row->flightnumber.$link_close.'</td>';
	$flights .= '<td align="left">'.$link_open.$row->dep_airport.' - '.$row->dep_name.$link_close.'</td>';
	$flights .= '<td align="center">'.$link_open.substr($row->dep_time, 0, 5).'z'.$link_close.'</td>';
	$flights .= '<td align="left">'.$link_open.$row->arr_airport.' - '.$row->arr_name.$link_close.'</td>';
	$flights .= '<td align="center">'.$link_open.substr($row->arr_time, 0, 5).'z'.$link_close.'</td>';
	//days
	$flights .= '<td align="center">'.$link_open;
	if($row->sun == '1'){ $flights .= 'S'; } else{ $flights .= '-'; }
	if($row->mon == '1'){ $flights .= 'M'; } else{ $flights .= '-'; }
	if($row->tue == '1'){ $flights .= 'T'; } else{ $flights .= '-'; }
	if($row->wed == '1'){ $flights .= 'W'; } else{ $flights .= '-'; }
	if($row->thu == '1'){ $flights .= 'T'; } else{ $flights .= '-'; }
	if($row->fri == '1'){ $flights .= 'F'; } else{ $flights .= '-'; }
	if($row->sat == '1'){ $flights .= 'S'; } else{ $flights .= '-'; }
	$flights .= $link_close.'</td>';
	$flights .= '<td align="center">'.$link_open.number_format($gcd_nm,0).'nm'.$link_close.'</td>';
	//$flights .= '<td align="center">'.$row->division.'</td>';
	$flights .= '<td align="center"><a href="http://www.gcmap.com/map?P='.strtolower($row->dep_airport).'-'.strtolower($row->arr_airport).'&MS=bm&PM=*" 
	rel="lightbox" title="Map of the route '.strtoupper($row->dep_airport).'-'.strtoupper($row->arr_airport).'">
	<img src="'.$image_url.'icons/application/map.png" alt="Map" /></a></td>';
	
	if($row->flightnumber == $post_flight_num){
		$checked = TRUE;
	}
	else{
		$checked = FALSE;
	}
	
	


	
	//only allow selection if the flight matches the current division and class
	if($row->clss == $class && $row->division_id == $division){
	
		$check_data = array(
		'name'        => 'flight_num',
		'id'          => 'flight_num',
		'value'       => $row->flightnumber,
		'checked'     => $checked,
		);
	
		$flights .= '<td align="center">'.form_radio($check_data).'</td>';
		
	}
	else{
		/*
		$row->clss
		$row->division
		$row->dep_airport
		$row->arr_airport
		'dispatch/timetable/'.$division.'/'.$hub_icao.'/'.$class.'/'.$origin.'/'.$destination.'/'.$offset
		*/
		$flights .= '<td align="center">'.$link_open.'<img src="'.$image_url.'icons/application/page_go.png" alt="Select" />'.$link_close.'</td>';
	}	
		
	$flights .= '</tr>';
}
	
$i++;
}
echo $flights;

?>
<tr>
<td colspan="10"><span style="float: right;"><p><?php echo $this->pagination->create_links(); ?></p></span></td>
</tr>
</table>
<div align="right">
<?php
echo form_dropdown('aircraft', $aircraft_array, $aircraft_id);
echo form_submit('Submit', 'Assign Flight');
echo form_close();

echo '<br /><br /><div align="center">When searching on origin or destination, hub is ignored and flights from all divisions and classes will be shown. Flights from a different division and class to that selected will not be selectable until you click on the row or the <img src="'.$image_url.'icons/application/page_go.png" alt="Select" /> icon to switch to that flight\'s class and division and allow it to be booked.</div>';

echo '<br /><br /><div class="error">'.$error.'</div>';
?>
</div>


<div id="gcmattrib">Maps generated by the <a href="http://www.gcmap.com/">Great Circle Mapper</a>&nbsp;- copyright &#169; <a href="http://www.kls2.com/~karl/">Karl L. Swartz</a>.</div>